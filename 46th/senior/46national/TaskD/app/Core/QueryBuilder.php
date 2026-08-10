<?php

declare(strict_types=1);

namespace App\Core;

/**
 * 查詢建構器。
 *
 * 負責把條件組合成 SQL，所有使用者提供的值都以「?」佔位符綁定，
 * 呼叫端不需要（也不應該）自行把值串進 SQL 字串裡。
 */
class QueryBuilder
{
    /** @var class-string<Model> 這個查詢對應的模型類別 */
    private string $modelClass;

    private string $table;

    /** @var array<int, string> WHERE 子句片段 */
    private array $conditions = [];

    /** @var array<int, mixed> 對應 WHERE 子句的綁定值 */
    private array $bindings = [];

    /** @var array<int, string> ORDER BY 子句片段 */
    private array $orders = [];

    private ?int $limit = null;

    private ?int $offset = null;

    /**
     * @param class-string<Model> $modelClass
     */
    public function __construct(string $modelClass)
    {
        $this->modelClass = $modelClass;
        $this->table      = $modelClass::table();
    }

    /**
     * 加入一般比較條件，例如 where('status', 'BOOKED') 或 where('id', '>', 10)。
     */
    public function where(string $column, mixed $operatorOrValue, mixed $value = null): static
    {
        // 只給兩個參數時視為等號比較
        if ($value === null && func_num_args() === 2) {
            $operator = '=';
            $value    = $operatorOrValue;
        } else {
            $operator = (string) $operatorOrValue;
        }

        $this->conditions[] = sprintf('%s %s ?', $this->quote($column), $this->safeOperator($operator));
        $this->bindings[]   = $value;

        return $this;
    }

    /**
     * 加入 IN 條件；給空陣列時會讓查詢必定沒有結果。
     *
     * @param array<int, mixed> $values
     */
    public function whereIn(string $column, array $values): static
    {
        if ($values === []) {
            $this->conditions[] = '1 = 0';

            return $this;
        }

        $placeholders       = implode(', ', array_fill(0, count($values), '?'));
        $this->conditions[] = sprintf('%s IN (%s)', $this->quote($column), $placeholders);

        foreach ($values as $value) {
            $this->bindings[] = $value;
        }

        return $this;
    }

    /**
     * 加入 IS NULL 條件。
     */
    public function whereNull(string $column): static
    {
        $this->conditions[] = sprintf('%s IS NULL', $this->quote($column));

        return $this;
    }

    /**
     * 加入 IS NOT NULL 條件。
     */
    public function whereNotNull(string $column): static
    {
        $this->conditions[] = sprintf('%s IS NOT NULL', $this->quote($column));

        return $this;
    }

    /**
     * 加入模糊比對條件；使用者輸入中的萬用字元會被跳脫，避免查詢語意被改寫。
     */
    public function whereLike(string $column, string $keyword): static
    {
        $escaped            = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $keyword);
        $this->conditions[] = sprintf('%s LIKE ?', $this->quote($column));
        $this->bindings[]   = '%' . $escaped . '%';

        return $this;
    }

    /**
     * 排序。方向只接受 ASC / DESC，避免被注入額外語法。
     */
    public function orderBy(string $column, string $direction = 'ASC'): static
    {
        $direction     = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $this->orders[] = sprintf('%s %s', $this->quote($column), $direction);

        return $this;
    }

    /**
     * 限制回傳筆數。
     */
    public function limit(int $limit): static
    {
        $this->limit = max(0, $limit);

        return $this;
    }

    /**
     * 略過前面幾筆。
     */
    public function offset(int $offset): static
    {
        $this->offset = max(0, $offset);

        return $this;
    }

    /**
     * 取出符合條件的所有模型物件。
     *
     * @return array<int, Model>
     */
    public function get(): array
    {
        $sql  = sprintf('SELECT * FROM %s%s%s%s', $this->quote($this->table),
            $this->buildWhere(), $this->buildOrder(), $this->buildLimit());
        $rows       = Database::instance()->select($sql, $this->bindings);
        $modelClass = $this->modelClass;

        return array_map(
            static fn (array $row): Model => $modelClass::hydrate($row),
            $rows
        );
    }

    /**
     * 取出第一筆結果，沒有資料時回傳 null。
     */
    public function first(): ?Model
    {
        $rows = $this->limit(1)->get();

        return $rows[0] ?? null;
    }

    /**
     * 計算符合條件的筆數。
     */
    public function count(): int
    {
        $sql = sprintf('SELECT COUNT(*) AS aggregate FROM %s%s', $this->quote($this->table), $this->buildWhere());
        $row = Database::instance()->selectOne($sql, $this->bindings);

        return (int) ($row['aggregate'] ?? 0);
    }

    /**
     * 加總指定欄位。
     */
    public function sum(string $column): int
    {
        $sql = sprintf(
            'SELECT COALESCE(SUM(%s), 0) AS aggregate FROM %s%s',
            $this->quote($column),
            $this->quote($this->table),
            $this->buildWhere()
        );
        $row = Database::instance()->selectOne($sql, $this->bindings);

        return (int) ($row['aggregate'] ?? 0);
    }

    /**
     * 依條件刪除資料，回傳受影響筆數。
     */
    public function delete(): int
    {
        $sql = sprintf('DELETE FROM %s%s', $this->quote($this->table), $this->buildWhere());

        return Database::instance()->execute($sql, $this->bindings)->rowCount();
    }

    /**
     * 依條件更新資料，回傳受影響筆數。
     *
     * @param array<string, mixed> $values
     */
    public function update(array $values): int
    {
        if ($values === []) {
            return 0;
        }

        $assignments = [];
        $bindings    = [];

        foreach ($values as $column => $value) {
            $assignments[] = sprintf('%s = ?', $this->quote($column));
            $bindings[]    = $value;
        }

        $sql = sprintf(
            'UPDATE %s SET %s%s',
            $this->quote($this->table),
            implode(', ', $assignments),
            $this->buildWhere()
        );

        return Database::instance()->execute($sql, array_merge($bindings, $this->bindings))->rowCount();
    }

    /**
     * 以反引號包住識別字，並移除反引號本身，確保欄位名稱不會夾帶語法。
     */
    private function quote(string $identifier): string
    {
        return '`' . str_replace('`', '', $identifier) . '`';
    }

    /**
     * 只允許白名單內的比較運算子。
     */
    private function safeOperator(string $operator): string
    {
        $allowed = ['=', '!=', '<>', '<', '<=', '>', '>=', 'LIKE'];

        return in_array(strtoupper($operator), $allowed, true) ? strtoupper($operator) : '=';
    }

    private function buildWhere(): string
    {
        return $this->conditions === [] ? '' : ' WHERE ' . implode(' AND ', $this->conditions);
    }

    private function buildOrder(): string
    {
        return $this->orders === [] ? '' : ' ORDER BY ' . implode(', ', $this->orders);
    }

    private function buildLimit(): string
    {
        if ($this->limit === null) {
            return '';
        }

        // LIMIT / OFFSET 皆已轉為整數，不會夾帶其他語法
        return $this->offset === null
            ? sprintf(' LIMIT %d', $this->limit)
            : sprintf(' LIMIT %d OFFSET %d', $this->limit, $this->offset);
    }
}

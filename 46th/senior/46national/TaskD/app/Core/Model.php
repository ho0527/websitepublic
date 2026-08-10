<?php

declare(strict_types=1);

namespace App\Core;

/**
 * ORM 模型基底類別（Active Record）。
 *
 * 每一張資料表都對應一個繼承本類別的模型，資料表的讀寫一律透過模型完成，
 * 呼叫端不需要自己撰寫 SQL，也因此不會有把使用者輸入串進 SQL 的機會。
 */
abstract class Model
{
    /** 對應的資料表名稱，由子類別指定 */
    protected static string $table = '';

    /** 主鍵欄位名稱 */
    protected static string $primaryKey = 'id';

    /** @var array<int, string> 允許被大量指派的欄位，避免使用者塞入未預期的欄位 */
    protected static array $fillable = [];

    /** @var array<string, mixed> 本筆資料的欄位內容 */
    protected array $attributes = [];

    /** 這筆資料是否已存在於資料庫（決定 save() 要 INSERT 還是 UPDATE） */
    protected bool $exists = false;

    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    /**
     * 取得資料表名稱。
     */
    public static function table(): string
    {
        return static::$table;
    }

    /**
     * 取得主鍵欄位名稱。
     */
    public static function primaryKey(): string
    {
        return static::$primaryKey;
    }

    /**
     * 由資料庫回傳的原始資料列建立模型物件。
     *
     * @param array<string, mixed> $row
     */
    public static function hydrate(array $row): static
    {
        $model             = new static();
        $model->attributes = $row;
        $model->exists     = true;

        return $model;
    }

    /**
     * 開始一段查詢。
     */
    public static function query(): QueryBuilder
    {
        return new QueryBuilder(static::class);
    }

    /**
     * 以條件開始查詢的捷徑。
     */
    public static function where(string $column, mixed $operatorOrValue, mixed $value = null): QueryBuilder
    {
        return func_num_args() === 2
            ? static::query()->where($column, $operatorOrValue)
            : static::query()->where($column, $operatorOrValue, $value);
    }

    /**
     * 取得全部資料。
     *
     * @return array<int, static>
     */
    public static function all(): array
    {
        return static::query()->get();
    }

    /**
     * 以主鍵取得單筆資料。
     */
    public static function find(int|string $id): ?static
    {
        return static::query()->where(static::$primaryKey, $id)->first();
    }

    /**
     * 建立並立即寫入一筆資料。
     *
     * @param array<string, mixed> $attributes
     */
    public static function create(array $attributes): static
    {
        $model = new static($attributes);
        $model->save();

        return $model;
    }

    /**
     * 以允許欄位填入資料。
     *
     * @param array<string, mixed> $attributes
     */
    public function fill(array $attributes): static
    {
        foreach ($attributes as $column => $value) {
            if (static::$fillable === [] || in_array($column, static::$fillable, true)) {
                $this->attributes[$column] = $value;
            }
        }

        return $this;
    }

    /**
     * 寫回資料庫：新資料 INSERT，既有資料 UPDATE。
     */
    public function save(): bool
    {
        $database = Database::instance();
        $values   = $this->attributes;

        if ($this->exists) {
            $key = $this->attributes[static::$primaryKey] ?? null;
            unset($values[static::$primaryKey]);

            static::query()->where(static::$primaryKey, $key)->update($values);

            return true;
        }

        unset($values[static::$primaryKey]);

        $columns      = array_keys($values);
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        $columnList   = implode(', ', array_map(
            static fn (string $column): string => '`' . str_replace('`', '', $column) . '`',
            $columns
        ));

        $database->execute(
            sprintf('INSERT INTO `%s` (%s) VALUES (%s)', static::$table, $columnList, $placeholders),
            array_values($values)
        );

        $this->attributes[static::$primaryKey] = $database->lastInsertId();
        $this->exists                          = true;

        return true;
    }

    /**
     * 刪除本筆資料。
     */
    public function delete(): bool
    {
        if (!$this->exists) {
            return false;
        }

        static::query()->where(static::$primaryKey, $this->attributes[static::$primaryKey])->delete();
        $this->exists = false;

        return true;
    }

    /**
     * 取得主鍵值。
     */
    public function id(): int
    {
        return (int) ($this->attributes[static::$primaryKey] ?? 0);
    }

    /**
     * 取得所有欄位內容。
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    public function __get(string $name): mixed
    {
        // 允許子類別以 getXxxAttribute() 定義衍生屬性
        $accessor = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $name))) . 'Attribute';

        if (method_exists($this, $accessor)) {
            return $this->{$accessor}();
        }

        return $this->attributes[$name] ?? null;
    }

    public function __set(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }
}

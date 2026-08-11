<?php
/**
 * 極簡 GraphQL 執行器（自行實作，不依賴外部套件）
 *
 * 依照 Schema 定義逐層解析欄位，並依照使用者查詢的欄位輸出對應資料，
 * 未被查詢的欄位不會出現在回應中（GraphQL 的核心特性）。
 */

class GraphQLExecutor
{
    public function __construct(
        /** @var array Schema 定義，見 Schema.php */
        private array $schema
    ) {
    }

    /**
     * 執行一份查詢文件
     *
     * @param  array  $operations    解析後的操作定義
     * @param  array  $variables     使用者傳入的變數
     * @param  ?string $operationName 指定要執行的操作名稱
     * @return array  欄位名稱 => 值
     */
    public function execute(array $operations, array $variables = [], ?string $operationName = null): array
    {
        $operation = $this->pickOperation($operations, $operationName);
        $variables = $this->resolveVariableDefaults($operation['variables'], $variables);

        $rootType = $operation['operation'] === 'mutation' ? 'Mutation' : 'Query';
        if (!isset($this->schema['types'][$rootType])) {
            throw new GraphQLError('unsupported operation type');
        }

        return $this->resolveSelectionSet($rootType, null, $operation['selectionSet'], $variables);
    }

    /** 從文件中挑出要執行的操作 */
    private function pickOperation(array $operations, ?string $operationName): array
    {
        if ($operationName !== null && $operationName !== '') {
            foreach ($operations as $operation) {
                if ($operation['name'] === $operationName) {
                    return $operation;
                }
            }
            throw new GraphQLError('operation "' . $operationName . '" not found');
        }

        return $operations[0];
    }

    /** 套用變數預設值 */
    private function resolveVariableDefaults(array $definitions, array $variables): array
    {
        foreach ($definitions as $name => $definition) {
            if (!array_key_exists($name, $variables)) {
                $variables[$name] = $definition['default'];
            }
        }

        return $variables;
    }

    /**
     * 解析一組選取集合
     *
     * @param string $typeName 目前所在的型別名稱
     * @param mixed  $source   上層解析出來的資料
     */
    private function resolveSelectionSet(string $typeName, mixed $source, array $selections, array $variables): array
    {
        $fields = $this->schema['types'][$typeName] ?? [];
        $result = [];

        foreach ($selections as $selection) {
            $fieldName = $selection['name'];

            // 內省欄位：回傳目前型別名稱
            if ($fieldName === '__typename') {
                $result[$selection['alias']] = $typeName;
                continue;
            }

            if (!isset($fields[$fieldName])) {
                throw new GraphQLError('Cannot query field "' . $fieldName . '" on type "' . $typeName . '"');
            }

            $field     = $fields[$fieldName];
            $arguments = $this->resolveArguments($selection['arguments'], $variables);
            $value     = $this->resolveFieldValue($field, $source, $arguments, $fieldName);

            $result[$selection['alias']] = $this->completeValue(
                $field['type'],
                $value,
                $selection['selectionSet'],
                $variables
            );
        }

        return $result;
    }

    /** 將參數中的變數代換成實際值 */
    private function resolveArguments(array $arguments, array $variables): array
    {
        $resolved = [];

        foreach ($arguments as $name => $value) {
            $resolved[$name] = $this->resolveArgumentValue($value, $variables);
        }

        return $resolved;
    }

    private function resolveArgumentValue(mixed $value, array $variables): mixed
    {
        if (is_array($value) && array_key_exists('__variable', $value) && count($value) === 1) {
            return $variables[$value['__variable']] ?? null;
        }

        if (is_array($value)) {
            return array_map(fn ($item) => $this->resolveArgumentValue($item, $variables), $value);
        }

        return $value;
    }

    /** 取得欄位的原始值：優先使用 resolve 函式，否則直接取上層資料的同名鍵 */
    private function resolveFieldValue(array $field, mixed $source, array $arguments, string $fieldName): mixed
    {
        if (isset($field['resolve'])) {
            return ($field['resolve'])($source, $arguments);
        }

        if (is_array($source) && array_key_exists($fieldName, $source)) {
            return $source[$fieldName];
        }

        return null;
    }

    /**
     * 依照欄位型別把原始值轉成輸出值
     */
    private function completeValue(string $type, mixed $value, ?array $selectionSet, array $variables): mixed
    {
        $type = rtrim($type, '!');

        // 串列型別
        if (str_starts_with($type, '[')) {
            if ($value === null) {
                return null;
            }
            $innerType = rtrim(substr($type, 1, -1), '!');

            return array_map(
                fn ($item) => $this->completeValue($innerType, $item, $selectionSet, $variables),
                array_values((array) $value)
            );
        }

        // 純量型別
        if (in_array($type, ['Int', 'Float', 'String', 'Boolean', 'ID'], true)) {
            if ($value === null) {
                return null;
            }

            return match ($type) {
                'Int'     => (int) $value,
                'Float'   => (float) $value,
                'Boolean' => (bool) $value,
                default   => (string) $value,
            };
        }

        // 物件型別：必須有選取集合
        if ($selectionSet === null) {
            throw new GraphQLError('Field of type "' . $type . '" must have a selection of subfields');
        }

        if ($value === null) {
            return null;
        }

        return $this->resolveSelectionSet($type, $value, $selectionSet, $variables);
    }
}

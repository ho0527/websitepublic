<?php
/**
 * 欄位驗證工具
 * 依題目規範拋出 MSG_MISSING_FIELD（400）與 MSG_WRONG_DATA_TYPE（400）
 */
class Validator
{
    /**
     * 檢查必填欄位是否存在且非空
     *
     * @param array    $data   來源資料
     * @param string[] $fields 必填欄位名稱
     * @throws ApiException
     */
    public static function required(array $data, array $fields): void
    {
        $missing = [];

        foreach ($fields as $field) {
            if (!array_key_exists($field, $data)) {
                $missing[] = $field;
                continue;
            }

            $value = $data[$field];
            if ($value === null || (is_string($value) && trim($value) === '') || (is_array($value) && $value === [])) {
                $missing[] = $field;
            }
        }

        if ($missing !== []) {
            throw new ApiException('MSG_MISSING_FIELD', 400, ['fields' => $missing]);
        }
    }

    /**
     * 檢查欄位為字串型別
     *
     * @param string[] $fields 欄位名稱
     */
    public static function strings(array $data, array $fields): void
    {
        $wrong = [];

        foreach ($fields as $field) {
            if (!array_key_exists($field, $data)) {
                continue;
            }
            if (!is_string($data[$field]) && !is_numeric($data[$field])) {
                $wrong[] = $field;
            }
        }

        self::throwWrongType($wrong);
    }

    /**
     * 檢查欄位為整數（允許數字字串）
     *
     * @param string[] $fields 欄位名稱
     */
    public static function integers(array $data, array $fields): void
    {
        $wrong = [];

        foreach ($fields as $field) {
            if (!array_key_exists($field, $data) || $data[$field] === null || $data[$field] === '') {
                continue;
            }

            $value = $data[$field];
            if (is_bool($value) || is_array($value)) {
                $wrong[] = $field;
                continue;
            }
            // 只接受整數字面值，例如 "12"、12；"12abc"、"1.5" 均視為格式錯誤
            if (!preg_match('/^-?\d+$/', (string) $value)) {
                $wrong[] = $field;
            }
        }

        self::throwWrongType($wrong);
    }

    /**
     * 檢查欄位為數值（允許小數）
     *
     * @param string[] $fields 欄位名稱
     */
    public static function numerics(array $data, array $fields): void
    {
        $wrong = [];

        foreach ($fields as $field) {
            if (!array_key_exists($field, $data) || $data[$field] === null || $data[$field] === '') {
                continue;
            }
            if (!is_numeric($data[$field])) {
                $wrong[] = $field;
            }
        }

        self::throwWrongType($wrong);
    }

    /** 檢查 email 格式 */
    public static function email(array $data, string $field): void
    {
        if (!array_key_exists($field, $data)) {
            return;
        }

        $value = $data[$field];
        // 系統內建帳號為 admin@localhost 這類沒有頂級網域的位址，
        // 因此不使用 FILTER_VALIDATE_EMAIL，改以「本地端@主機」的基本格式檢查
        if (!is_string($value) || preg_match('/^[^@\s]+@[^@\s]+$/', $value) !== 1) {
            self::throwWrongType([$field]);
        }
    }

    /**
     * 檢查布林欄位（接受 true/false、"true"/"false"、1/0）
     *
     * @return bool 轉換後的布林值
     */
    public static function boolean(array $data, string $field): bool
    {
        $value = $data[$field] ?? null;

        if (is_bool($value)) {
            return $value;
        }
        if (is_string($value)) {
            $lower = strtolower(trim($value));
            if (in_array($lower, ['true', '1'], true)) {
                return true;
            }
            if (in_array($lower, ['false', '0'], true)) {
                return false;
            }
        }
        if (is_int($value) && ($value === 0 || $value === 1)) {
            return $value === 1;
        }

        self::throwWrongType([$field]);
        return false; // 不會執行到，僅為讓靜態分析滿意
    }

    /** 統一拋出資料格式錯誤 */
    private static function throwWrongType(array $wrong): void
    {
        if ($wrong !== []) {
            throw new ApiException('MSG_WRONG_DATA_TYPE', 400, ['fields' => array_values(array_unique($wrong))]);
        }
    }
}

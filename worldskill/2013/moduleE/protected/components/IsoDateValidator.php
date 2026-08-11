<?php
/**
 * ISO 日期（YYYY-MM-DD）驗證器。
 *
 * 註：Yii 1.1.13 內建的 CDateValidator 依賴 CDateTimeParser，
 * 該檔案使用 PHP 8 已移除的字串大括號索引語法（$str{$i}），
 * 因此改以本驗證器取代，不需要修改題目提供的框架原始碼。
 */
class IsoDateValidator extends CValidator
{
    /** @var bool 允許空值 */
    public $allowEmpty = true;

    protected function validateAttribute($object, $attribute)
    {
        $value = $object->$attribute;

        if ($this->allowEmpty && ($value === null || $value === '')) {
            return;
        }

        if (!is_string($value) || !preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value, $matches)) {
            $this->addError($object, $attribute,
                '{attribute} must be a valid date in YYYY-MM-DD format.');
            return;
        }

        list(, $year, $month, $day) = $matches;
        if (!checkdate((int)$month, (int)$day, (int)$year)) {
            $this->addError($object, $attribute, '{attribute} is not an existing date.');
        }
    }
}

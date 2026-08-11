<?php
/* @var $this DriverController */
/* @var $driver Driver */
/* @var $vehicles array 依車種分組的車輛清單 */
/* @var $isNew bool */
// 表單欄位一律使用 Yii 的 CHtml active* 方法產生，而非純 HTML。
?>
<div class="form">

    <?php echo CHtml::beginForm('', 'post', array(
        'id'      => 'driver-form',
        'enctype' => 'multipart/form-data',
    )); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo CHtml::errorSummary($driver); ?>

    <div class="row">
        <?php echo CHtml::activeLabelEx($driver, 'name'); ?>
        <?php echo CHtml::activeTextField($driver, 'name', array('size' => 45, 'maxlength' => 45)); ?>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabelEx($driver, 'birth_date'); ?>
        <?php echo CHtml::activeDateField($driver, 'birth_date'); ?>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabelEx($driver, 'email'); ?>
        <?php echo CHtml::activeEmailField($driver, 'email', array('size' => 50, 'maxlength' => 50)); ?>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabelEx($driver, 'phone'); ?>
        <?php echo CHtml::activeTextField($driver, 'phone', array('size' => 40, 'maxlength' => 40)); ?>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabelEx($driver, 'avatarFile'); ?>
        <?php echo CHtml::activeFileField($driver, 'avatarFile'); ?>
        <?php if (!$isNew): ?>
            <p class="hint">Current avatar:
                <img class="avatar" src="<?php echo $driver->getAvatarUrl(); ?>" alt=""></p>
        <?php endif; ?>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabelEx($driver, 'type'); ?>
        <?php echo CHtml::activeDropDownList($driver, 'type', Yii::app()->params['vehicleTypes'],
                                             array('empty' => '-- select --')); ?>
        <p class="hint">A driver can only drive vehicles of this type.</p>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabel($driver, 'vehicle_id'); ?>
        <?php echo CHtml::activeDropDownList($driver, 'vehicle_id', $vehicles,
                                             array('empty' => '-- not assigned --')); ?>
        <p class="hint">A driver can be assigned to one vehicle only.</p>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($isNew ? 'Create' : 'Save'); ?>
    </div>

    <?php echo CHtml::endForm(); ?>
</div><!-- form -->

<script>
// 依所選車種即時過濾「Vehicle」下拉選單，避免指派到不相符的車輛。
(function () {
    var typeSelect    = document.getElementById('Driver_type');
    var vehicleSelect = document.getElementById('Driver_vehicle_id');
    if (!typeSelect || !vehicleSelect) {
        return;
    }

    function syncVehicleOptions() {
        var selectedType = typeSelect.value;
        var groups = vehicleSelect.getElementsByTagName('optgroup');
        var matched = false;

        for (var i = 0; i < groups.length; i++) {
            var visible = (selectedType === '' || groups[i].label === selectedType);
            groups[i].hidden   = !visible;
            groups[i].disabled = !visible;
            if (visible) {
                matched = true;
            }
        }
        // 目前選取的車輛若不屬於所選車種，改回「未指派」
        var chosen = vehicleSelect.options[vehicleSelect.selectedIndex];
        if (chosen && chosen.parentNode.tagName === 'OPTGROUP' && chosen.parentNode.disabled) {
            vehicleSelect.value = '';
        }
        return matched;
    }

    typeSelect.addEventListener('change', syncVehicleOptions);
    syncVehicleOptions();
})();
</script>

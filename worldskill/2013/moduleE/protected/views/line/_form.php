<?php
/* @var $this LineController */
/* @var $line Line */
/* @var $isNew bool */
// 表單欄位一律使用 Yii 的 CHtml active* 方法產生，而非純 HTML。
?>
<div class="form">

    <?php echo CHtml::beginForm('', 'post', array(
        'id'      => 'line-form',
        'enctype' => 'multipart/form-data',
    )); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo CHtml::errorSummary($line); ?>

    <div class="row">
        <?php echo CHtml::activeLabelEx($line, 'code'); ?>
        <?php echo CHtml::activeTextField($line, 'code', array('size' => 50, 'maxlength' => 50)); ?>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabelEx($line, 'start_time_operation'); ?>
        <?php echo CHtml::activeDropDownList($line, 'start_time_operation', Line::hourOptions(),
                                             array('empty' => '-- select --')); ?>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabelEx($line, 'end_time_operation'); ?>
        <?php echo CHtml::activeDropDownList($line, 'end_time_operation', Line::hourOptions(),
                                             array('empty' => '-- select --')); ?>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabelEx($line, 'type'); ?>
        <?php echo CHtml::activeDropDownList($line, 'type', Yii::app()->params['vehicleTypes'],
                                             array('empty' => '-- select --')); ?>
        <p class="hint">Only vehicles and drivers of this type can be assigned to the line.</p>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabelEx($line, 'mapFile'); ?>
        <?php echo CHtml::activeFileField($line, 'mapFile'); ?>
        <?php if (!$isNew && $line->map !== ''): ?>
            <p class="hint">Current map: <?php echo CHtml::encode($line->map); ?>
                (leave empty to keep it)</p>
        <?php endif; ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($isNew ? 'Create' : 'Save'); ?>
    </div>

    <?php echo CHtml::endForm(); ?>
</div><!-- form -->

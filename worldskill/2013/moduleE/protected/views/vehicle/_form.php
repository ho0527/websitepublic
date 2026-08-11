<?php
/* @var $this VehicleController */
/* @var $vehicle Vehicle */
/* @var $isNew bool */
?>
<div class="form">

    <?php echo CHtml::beginForm('', 'post', array('id' => 'vehicle-form')); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo CHtml::errorSummary($vehicle); ?>

    <div class="row">
        <?php echo CHtml::activeLabelEx($vehicle, 'name'); ?>
        <?php echo CHtml::activeTextField($vehicle, 'name', array('size' => 30, 'maxlength' => 30)); ?>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabelEx($vehicle, 'capacity'); ?>
        <?php echo CHtml::activeNumberField($vehicle, 'capacity', array('min' => 1, 'max' => 1000)); ?>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabelEx($vehicle, 'type'); ?>
        <?php echo CHtml::activeDropDownList($vehicle, 'type', Yii::app()->params['vehicleTypes'],
                                             array('empty' => '-- select --')); ?>
        <p class="hint">Changing the type releases the line and the drivers that no longer match.</p>
    </div>

    <?php if (!$isNew): ?>
        <div class="row">
            <label>Line</label>
            <p class="hint"><?php echo CHtml::encode($vehicle->getLineLabel()); ?>
                &mdash; vehicles are assigned from the line's
                <?php echo CHtml::link('vehicles form', array('line/index')); ?></p>
        </div>
    <?php endif; ?>

    <div class="row buttons">
        <?php echo CHtml::submitButton($isNew ? 'Create' : 'Save'); ?>
    </div>

    <?php echo CHtml::endForm(); ?>
</div><!-- form -->

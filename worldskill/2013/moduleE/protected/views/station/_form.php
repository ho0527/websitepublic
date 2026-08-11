<?php
/* @var $this StationController */
/* @var $station Station */
/* @var $isNew bool */
?>
<div class="form">

    <?php echo CHtml::beginForm('', 'post', array('id' => 'station-form')); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo CHtml::errorSummary($station); ?>

    <div class="row">
        <?php echo CHtml::activeLabelEx($station, 'name'); ?>
        <?php echo CHtml::activeTextField($station, 'name', array('size' => 60, 'maxlength' => 80)); ?>
    </div>

    <?php if (!$isNew): ?>
        <div class="row">
            <label>Line</label>
            <p class="hint"><?php echo CHtml::encode($station->getLineLabel()); ?>
                <?php if ((int)$station->line_id !== 0): ?>
                    (<?php echo CHtml::encode($station->position_station); ?>) &mdash;
                    stations are assigned from the
                    <?php echo CHtml::link('line stations form', array('line/stations', 'id' => $station->line_id)); ?>
                <?php endif; ?>
            </p>
        </div>
    <?php endif; ?>

    <div class="row buttons">
        <?php echo CHtml::submitButton($isNew ? 'Create' : 'Save'); ?>
    </div>

    <?php echo CHtml::endForm(); ?>
</div><!-- form -->

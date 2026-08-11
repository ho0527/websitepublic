<?php
/* @var $this LineController */
/* @var $line Line */
/* @var $vehicles Vehicle[] */
/* @var $selected int[] */
/* @var $errors string[] */
$maxVehicles = Yii::app()->params['maxVehiclesPerLine'];
?>
<div id="content" class="wide">

    <h1>Vehicles of <?php echo CHtml::encode($line->code); ?></h1>

    <p class="note">Only <strong><?php echo CHtml::encode($line->getTypeLabel()); ?></strong> vehicles can run on this
        line, and a maximum of <?php echo $maxVehicles; ?> vehicles can be assigned.
        A vehicle can belong to one Intermediate Line only.</p>

    <?php if (!empty($errors)): ?>
        <div class="errorSummary" style="border:2px solid #C00;padding:7px;margin:0 0 20px;background:#FEE;">
            <p>Please fix the following errors:</p>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo CHtml::encode($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="form">
        <?php echo CHtml::beginForm('', 'post', array('id' => 'line-vehicles-form')); ?>

        <?php if (empty($vehicles)): ?>
            <p class="hint-text">There is no available <?php echo CHtml::encode($line->getTypeLabel()); ?> vehicle.
                <?php echo CHtml::link('Create one', array('vehicle/create')); ?>.</p>
        <?php else: ?>
            <ul class="vehicle-picker">
                <?php foreach ($vehicles as $vehicle): ?>
                    <li>
                        <label>
                            <?php echo CHtml::checkBox('VehicleIds[]', in_array((int)$vehicle->id, $selected, true),
                                                       array('value' => $vehicle->id, 'id' => 'vehicle_' . $vehicle->id)); ?>
                            <?php echo CHtml::encode($vehicle->name); ?>
                            <span class="quiet">(<?php echo (int)$vehicle->capacity; ?> seats)</span>
                        </label>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class="row buttons">
            <?php echo CHtml::hiddenField('submitVehicles', '1'); ?>
            <?php echo CHtml::submitButton('Assign vehicles'); ?>
            <?php echo CHtml::link('Cancel', array('line/view', 'id' => $line->id), array('class' => 'button secondary')); ?>
        </div>

        <?php echo CHtml::endForm(); ?>
    </div><!-- form -->
</div><!-- content -->

<?php
/* @var $this VehicleController */
/* @var $vehicle Vehicle */
?>
<div id="content">
    <h1>Update Vehicle <?php echo CHtml::encode($vehicle->name); ?></h1>
    <?php $this->renderPartial('_form', array('vehicle' => $vehicle, 'isNew' => false)); ?>
</div><!-- content -->

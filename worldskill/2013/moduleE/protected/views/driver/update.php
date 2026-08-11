<?php
/* @var $this DriverController */
/* @var $driver Driver */
/* @var $vehicles array */
?>
<div id="content">
    <h1>Update Driver <?php echo CHtml::encode($driver->name); ?></h1>
    <?php $this->renderPartial('_form', array('driver' => $driver, 'vehicles' => $vehicles, 'isNew' => false)); ?>
</div><!-- content -->

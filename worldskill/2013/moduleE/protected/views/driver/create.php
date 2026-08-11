<?php
/* @var $this DriverController */
/* @var $driver Driver */
/* @var $vehicles array */
?>
<div id="content">
    <h1>Create Driver</h1>
    <?php $this->renderPartial('_form', array('driver' => $driver, 'vehicles' => $vehicles, 'isNew' => true)); ?>
</div><!-- content -->

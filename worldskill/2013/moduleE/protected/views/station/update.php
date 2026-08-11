<?php
/* @var $this StationController */
/* @var $station Station */
?>
<div id="content">
    <h1>Update Station <?php echo CHtml::encode($station->name); ?></h1>
    <?php $this->renderPartial('_form', array('station' => $station, 'isNew' => false)); ?>
</div><!-- content -->

<?php
/* @var $this LineController */
/* @var $line Line */
?>
<div id="content">
    <h1>Update <?php echo CHtml::encode($line->code); ?></h1>
    <?php $this->renderPartial('_form', array('line' => $line, 'isNew' => false)); ?>
</div><!-- content -->

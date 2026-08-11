<?php
/* @var $this SiteController */
/* @var $error array */
?>
<div id="content">
    <h1>Error <?php echo (int)$error['code']; ?></h1>
    <div class="flash-error"><?php echo CHtml::encode($error['message']); ?></div>
    <p><?php echo CHtml::link('Back to home page', array('site/index')); ?></p>
</div><!-- content -->

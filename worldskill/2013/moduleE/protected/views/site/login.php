<?php
/* @var $this SiteController */
/* @var $model LoginForm */
?>
<div id="content">

    <h1>Login</h1>

    <div class="form">
        <?php echo CHtml::beginForm(array('site/login'), 'post', array('id' => 'login-form')); ?>

        <p class="note">Fields with <span class="required">*</span> are required.</p>

        <?php echo CHtml::errorSummary($model); ?>

        <div class="row">
            <?php echo CHtml::activeLabelEx($model, 'username'); ?>
            <?php echo CHtml::activeTextField($model, 'username', array('size' => 40, 'maxlength' => 40)); ?>
        </div>

        <div class="row">
            <?php echo CHtml::activeLabelEx($model, 'password'); ?>
            <?php echo CHtml::activePasswordField($model, 'password', array('size' => 40, 'maxlength' => 40)); ?>
        </div>

        <div class="row buttons">
            <?php echo CHtml::submitButton('Login'); ?>
        </div>

        <?php echo CHtml::endForm(); ?>
    </div><!-- form -->
</div><!-- content -->

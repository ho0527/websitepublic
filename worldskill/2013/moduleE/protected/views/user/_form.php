<?php
/* @var $this UserController */
/* @var $user User */
/* @var $isNew bool */
?>
<div class="form">

    <?php echo CHtml::beginForm('', 'post', array('id' => 'user-form')); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo CHtml::errorSummary($user); ?>

    <div class="row">
        <?php echo CHtml::activeLabelEx($user, 'name'); ?>
        <?php echo CHtml::activeTextField($user, 'name', array('size' => 50, 'maxlength' => 50)); ?>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabelEx($user, 'gender'); ?>
        <?php echo CHtml::activeDropDownList($user, 'gender', User::genderOptions(), array('empty' => '-- select --')); ?>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabelEx($user, 'birth_date'); ?>
        <?php echo CHtml::activeDateField($user, 'birth_date'); ?>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabelEx($user, 'email'); ?>
        <?php echo CHtml::activeEmailField($user, 'email', array('size' => 50, 'maxlength' => 50)); ?>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabelEx($user, 'login'); ?>
        <?php echo CHtml::activeTextField($user, 'login', array('size' => 40, 'maxlength' => 40)); ?>
    </div>

    <div class="row">
        <?php echo CHtml::activeLabelEx($user, 'newPassword'); ?>
        <?php echo CHtml::activePasswordField($user, 'newPassword', array('size' => 50, 'maxlength' => 40)); ?>
        <?php if (!$isNew): ?>
            <p class="hint">Leave empty to keep the current password.</p>
        <?php endif; ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($isNew ? 'Create' : 'Save'); ?>
    </div>

    <?php echo CHtml::endForm(); ?>
</div><!-- form -->

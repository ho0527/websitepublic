<?php
/* @var $this UserController */
/* @var $user User */
?>
<div id="content">
    <h1>Update User <?php echo CHtml::encode($user->login); ?></h1>
    <?php $this->renderPartial('_form', array('user' => $user, 'isNew' => false)); ?>
</div><!-- content -->

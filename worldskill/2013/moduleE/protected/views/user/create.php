<?php
/* @var $this UserController */
/* @var $user User */
?>
<div id="content">
    <h1>Create User</h1>
    <?php $this->renderPartial('_form', array('user' => $user, 'isNew' => true)); ?>
</div><!-- content -->

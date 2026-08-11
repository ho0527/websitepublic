<?php
/* @var $this UserController */
/* @var $users User[] */
?>
<div id="content" class="wide">

    <h1>Administrators</h1>
    <p class="hint-text">Every administrator can create new administrator users.</p>

    <table class="items">
        <caption>Administrator accounts</caption>
        <thead>
        <tr><th>Login</th><th>Name</th><th>Gender</th><th>Birth Date</th><th>Email</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><strong><?php echo CHtml::encode($user->login); ?></strong></td>
                <td><?php echo CHtml::encode($user->name); ?></td>
                <td><?php echo CHtml::encode($user->getGenderLabel()); ?></td>
                <td><?php echo CHtml::encode($user->birth_date); ?></td>
                <td><?php echo CHtml::encode($user->email); ?></td>
                <td class="actions">
                    <?php echo CHtml::link('Update', array('user/update', 'id' => $user->id)); ?> |
                    <?php echo CHtml::link('Delete', array('user/delete', 'id' => $user->id), array(
                        'onclick' => "return confirm('Delete this administrator?');")); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <p><?php echo CHtml::link('Create User', array('user/create'), array('class' => 'button')); ?></p>
</div><!-- content -->

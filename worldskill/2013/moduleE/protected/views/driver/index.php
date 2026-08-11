<?php
/* @var $this DriverController */
/* @var $drivers Driver[] */
?>
<div id="content" class="wide">

    <h1>Drivers</h1>
    <p class="hint-text">A driver is exclusive to one type of vehicle and can be assigned to one vehicle only.</p>

    <table class="items">
        <caption>Drivers report</caption>
        <thead>
        <tr><th>Avatar</th><th>Name</th><th>Birth Date</th><th>Email</th><th>Phone</th><th>Type</th><th>Vehicle</th><th>Line</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php if (empty($drivers)): ?>
            <tr><td class="empty" colspan="9">No driver has been created yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($drivers as $driver): ?>
            <tr>
                <td><img class="avatar" src="<?php echo $driver->getAvatarUrl(); ?>"
                         alt="Avatar of <?php echo CHtml::encode($driver->name); ?>"></td>
                <td><?php echo CHtml::link(CHtml::encode($driver->name), array('driver/view', 'id' => $driver->id)); ?></td>
                <td><?php echo CHtml::encode($driver->birth_date); ?></td>
                <td><?php echo CHtml::encode($driver->email); ?></td>
                <td><?php echo CHtml::encode($driver->phone); ?></td>
                <td><span class="tag"><?php echo CHtml::encode($driver->getTypeLabel()); ?></span></td>
                <td><?php echo CHtml::encode($driver->getVehicleLabel()); ?></td>
                <td><?php echo $driver->vehicle === null ? '-' : CHtml::encode($driver->vehicle->getLineLabel()); ?></td>
                <td class="actions">
                    <?php echo CHtml::link('Update', array('driver/update', 'id' => $driver->id)); ?> |
                    <?php echo CHtml::link('Delete', array('driver/delete', 'id' => $driver->id), array(
                        'onclick' => "return confirm('Delete this driver?');")); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <p><?php echo CHtml::link('Create Driver', array('driver/create'), array('class' => 'button')); ?></p>
</div><!-- content -->

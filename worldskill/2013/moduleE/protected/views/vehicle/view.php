<?php
/* @var $this VehicleController */
/* @var $vehicle Vehicle */
?>
<div id="content" class="wide">
    <h1>Vehicle <?php echo CHtml::encode($vehicle->name); ?></h1>

    <table class="detail">
        <tr><th>Name</th><td><?php echo CHtml::encode($vehicle->name); ?></td></tr>
        <tr><th>Type</th><td><?php echo CHtml::encode($vehicle->getTypeLabel()); ?></td></tr>
        <tr><th>Capacity</th><td><?php echo (int)$vehicle->capacity; ?></td></tr>
        <tr><th>Line</th><td><?php
            echo $vehicle->line === null
                ? '-'
                : CHtml::link(CHtml::encode($vehicle->line->code), array('line/view', 'id' => $vehicle->line_id));
        ?></td></tr>
    </table>

    <h3>Drivers</h3>
    <table class="items">
        <thead><tr><th>Avatar</th><th>Name</th><th>Email</th><th>Phone</th></tr></thead>
        <tbody>
        <?php if (empty($vehicle->drivers)): ?>
            <tr><td class="empty" colspan="4">No driver is assigned to this vehicle.</td></tr>
        <?php endif; ?>
        <?php foreach ($vehicle->drivers as $driver): ?>
            <tr>
                <td><img class="avatar" src="<?php echo $driver->getAvatarUrl(); ?>" alt=""></td>
                <td><?php echo CHtml::link(CHtml::encode($driver->name), array('driver/view', 'id' => $driver->id)); ?></td>
                <td><?php echo CHtml::encode($driver->email); ?></td>
                <td><?php echo CHtml::encode($driver->phone); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <p>
        <?php echo CHtml::link('Update', array('vehicle/update', 'id' => $vehicle->id), array('class' => 'button')); ?>
        <?php echo CHtml::link('Delete', array('vehicle/delete', 'id' => $vehicle->id), array(
            'class' => 'button danger', 'onclick' => "return confirm('Delete this vehicle?');")); ?>
    </p>
</div><!-- content -->

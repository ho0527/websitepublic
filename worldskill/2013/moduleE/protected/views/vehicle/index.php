<?php
/* @var $this VehicleController */
/* @var $vehicles Vehicle[] */
?>
<div id="content" class="wide">

    <h1>Vehicles</h1>
    <p class="hint-text">A vehicle can belong to one Intermediate Line only, and only to a line of the same type.</p>

    <table class="items">
        <caption>Vehicles report</caption>
        <thead>
        <tr><th>Name</th><th>Type</th><th>Capacity</th><th>Line</th><th>Drivers</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php if (empty($vehicles)): ?>
            <tr><td class="empty" colspan="6">No vehicle has been created yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($vehicles as $vehicle): ?>
            <tr>
                <td><?php echo CHtml::link(CHtml::encode($vehicle->name), array('vehicle/view', 'id' => $vehicle->id)); ?></td>
                <td><span class="tag"><?php echo CHtml::encode($vehicle->getTypeLabel()); ?></span></td>
                <td><?php echo (int)$vehicle->capacity; ?></td>
                <td><?php echo CHtml::encode($vehicle->getLineLabel()); ?></td>
                <td><?php echo count($vehicle->drivers); ?></td>
                <td class="actions">
                    <?php echo CHtml::link('Update', array('vehicle/update', 'id' => $vehicle->id)); ?> |
                    <?php if ((int)$vehicle->line_id !== 0): ?>
                        <?php echo CHtml::link('Remove from line', array('vehicle/detach', 'id' => $vehicle->id), array(
                            'onclick' => "return confirm('Remove this vehicle from its line?');")); ?> |
                    <?php endif; ?>
                    <?php echo CHtml::link('Delete', array('vehicle/delete', 'id' => $vehicle->id), array(
                        'onclick' => "return confirm('Delete this vehicle?');")); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <p><?php echo CHtml::link('Create Vehicle', array('vehicle/create'), array('class' => 'button')); ?></p>
</div><!-- content -->

<?php
/* @var $this DriverController */
/* @var $driver Driver */
?>
<div id="content" class="wide">
    <h1>Driver <?php echo CHtml::encode($driver->name); ?></h1>

    <img class="avatar-large" src="<?php echo $driver->getAvatarUrl(); ?>"
         alt="Avatar of <?php echo CHtml::encode($driver->name); ?>">

    <table class="detail">
        <tr><th>Name</th><td><?php echo CHtml::encode($driver->name); ?></td></tr>
        <tr><th>Birth Date</th><td><?php echo CHtml::encode($driver->birth_date); ?></td></tr>
        <tr><th>Email</th><td><?php echo CHtml::encode($driver->email); ?></td></tr>
        <tr><th>Phone</th><td><?php echo CHtml::encode($driver->phone); ?></td></tr>
        <tr><th>Type Vehicle</th><td><?php echo CHtml::encode($driver->getTypeLabel()); ?></td></tr>
        <tr><th>Vehicle</th><td><?php
            echo $driver->vehicle === null
                ? 'not assigned'
                : CHtml::link(CHtml::encode($driver->vehicle->name), array('vehicle/view', 'id' => $driver->vehicle_id));
        ?></td></tr>
        <tr><th>Line</th><td><?php
            echo $driver->vehicle === null ? '-' : CHtml::encode($driver->vehicle->getLineLabel());
        ?></td></tr>
    </table>

    <p>
        <?php echo CHtml::link('Update', array('driver/update', 'id' => $driver->id), array('class' => 'button')); ?>
        <?php echo CHtml::link('Delete', array('driver/delete', 'id' => $driver->id), array(
            'class' => 'button danger', 'onclick' => "return confirm('Delete this driver?');")); ?>
    </p>
</div><!-- content -->

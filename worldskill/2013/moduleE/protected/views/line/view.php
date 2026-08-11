<?php
/* @var $this LineController */
/* @var $line Line */
$maxStations = Yii::app()->params['maxStationsPerLine'];
$maxVehicles = Yii::app()->params['maxVehiclesPerLine'];
?>
<div id="content" class="wide">

    <h1><?php echo CHtml::encode($line->code); ?></h1>

    <table class="detail">
        <tr><th>Name</th><td><?php echo CHtml::encode($line->code); ?></td></tr>
        <tr><th>Type</th><td><?php echo CHtml::encode($line->getTypeLabel()); ?></td></tr>
        <tr><th>Start Time Operation</th><td><?php echo CHtml::encode($line->start_time_operation); ?></td></tr>
        <tr><th>End Time Operation</th><td><?php echo CHtml::encode($line->end_time_operation); ?></td></tr>
        <tr><th>Stations</th><td><?php echo count($line->stations) . ' / ' . $maxStations; ?></td></tr>
        <tr><th>Vehicles</th><td><?php echo count($line->vehicles) . ' / ' . $maxVehicles; ?></td></tr>
    </table>

    <h3>Route map</h3>
    <?php if ($line->getMapUrl() !== null): ?>
        <img class="map-preview" src="<?php echo $line->getMapUrl(); ?>"
             alt="Route map of line <?php echo CHtml::encode($line->code); ?>">
    <?php else: ?>
        <p class="hint-text">No route map has been uploaded for this line.
            <?php echo CHtml::link('Upload one', array('line/update', 'id' => $line->id)); ?>.</p>
    <?php endif; ?>

    <h3>Stations</h3>
    <table class="items">
        <thead>
        <tr><th>#</th><th>Position</th><th>Station</th></tr>
        </thead>
        <tbody>
        <?php if (empty($line->stations)): ?>
            <tr><td class="empty" colspan="3">No stations assigned yet -
                <?php echo CHtml::link('assign the 7 stations', array('line/stations', 'id' => $line->id)); ?>.</td></tr>
        <?php endif; ?>
        <?php foreach ($line->stations as $index => $station): ?>
            <tr>
                <td><?php echo $index + 1; ?></td>
                <td><span class="tag <?php echo CHtml::encode($station->position_station); ?>">
                        <?php echo CHtml::encode($station->position_station); ?></span></td>
                <td><?php echo CHtml::link(CHtml::encode($station->name), array('station/view', 'id' => $station->id)); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Vehicles &amp; drivers</h3>
    <table class="items">
        <thead>
        <tr><th>Vehicle</th><th>Type</th><th>Capacity</th><th>Drivers</th></tr>
        </thead>
        <tbody>
        <?php if (empty($line->vehicles)): ?>
            <tr><td class="empty" colspan="4">No vehicles assigned yet -
                <?php echo CHtml::link('assign vehicles', array('line/vehicles', 'id' => $line->id)); ?>.</td></tr>
        <?php endif; ?>
        <?php foreach ($line->vehicles as $vehicle): ?>
            <tr>
                <td><?php echo CHtml::link(CHtml::encode($vehicle->name), array('vehicle/view', 'id' => $vehicle->id)); ?></td>
                <td><?php echo CHtml::encode($vehicle->getTypeLabel()); ?></td>
                <td><?php echo (int)$vehicle->capacity; ?></td>
                <td>
                    <?php if (empty($vehicle->drivers)): ?>
                        <span class="hint-text">no driver assigned</span>
                    <?php else: ?>
                        <?php
                        $names = array();
                        foreach ($vehicle->drivers as $driver) {
                            $names[] = CHtml::link(CHtml::encode($driver->name), array('driver/view', 'id' => $driver->id));
                        }
                        echo implode(', ', $names);
                        ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <p>
        <?php echo CHtml::link('Update', array('line/update', 'id' => $line->id), array('class' => 'button')); ?>
        <?php echo CHtml::link('Manage stations', array('line/stations', 'id' => $line->id), array('class' => 'button secondary')); ?>
        <?php echo CHtml::link('Manage vehicles', array('line/vehicles', 'id' => $line->id), array('class' => 'button secondary')); ?>
        <?php echo CHtml::link('Delete', array('line/delete', 'id' => $line->id), array(
            'class'   => 'button danger',
            'onclick' => "return confirm('Delete this line? All its stations and vehicles will be released.');",
        )); ?>
    </p>
</div><!-- content -->

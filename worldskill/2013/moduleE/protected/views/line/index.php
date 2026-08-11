<?php
/* @var $this LineController */
/* @var $lines Line[] */
$maxStations = Yii::app()->params['maxStationsPerLine'];
$maxVehicles = Yii::app()->params['maxVehiclesPerLine'];
?>
<div id="content" class="wide">

    <h1>Intermediate Lines</h1>
    <p class="hint-text">Report of all Intermediate Lines with their type, operating hours, stations and vehicles.</p>

    <table class="items">
        <caption>Lines report</caption>
        <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Operation</th>
            <th>Stations</th>
            <th>Vehicles</th>
            <th>Map</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($lines)): ?>
            <tr><td class="empty" colspan="7">No Intermediate Line has been created yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($lines as $line): ?>
            <tr>
                <td><?php echo CHtml::link(CHtml::encode($line->code), array('line/view', 'id' => $line->id)); ?></td>
                <td><span class="tag"><?php echo CHtml::encode($line->getTypeLabel()); ?></span></td>
                <td><?php echo CHtml::encode(substr($line->start_time_operation, 0, 5) . ' - ' . substr($line->end_time_operation, 0, 5)); ?></td>
                <td><?php echo count($line->stations) . ' / ' . $maxStations; ?></td>
                <td><?php echo count($line->vehicles) . ' / ' . $maxVehicles; ?></td>
                <td><?php echo $line->map === '' ? '-' : CHtml::encode($line->map); ?></td>
                <td class="actions">
                    <?php echo CHtml::link('View', array('line/view', 'id' => $line->id)); ?> |
                    <?php echo CHtml::link('Update', array('line/update', 'id' => $line->id)); ?> |
                    <?php echo CHtml::link('Stations', array('line/stations', 'id' => $line->id)); ?> |
                    <?php echo CHtml::link('Vehicles', array('line/vehicles', 'id' => $line->id)); ?> |
                    <?php echo CHtml::link('Delete', array('line/delete', 'id' => $line->id), array(
                        'onclick' => "return confirm('Delete line " . CHtml::encode($line->code)
                                   . "? All its stations and vehicles will be released.');",
                    )); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <p><?php echo CHtml::link('Create Line', array('line/create'), array('class' => 'button')); ?></p>
</div><!-- content -->

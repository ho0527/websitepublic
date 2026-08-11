<?php
/* @var $this StationController */
/* @var $stations Station[] */
?>
<div id="content" class="wide">

    <h1>Stations</h1>
    <p class="hint-text">A station can belong to one Intermediate Line only.</p>

    <table class="items">
        <caption>Stations report</caption>
        <thead>
        <tr><th>ID</th><th>Name</th><th>Line</th><th>Position</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php if (empty($stations)): ?>
            <tr><td class="empty" colspan="5">No station has been created yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($stations as $station): ?>
            <tr>
                <td><?php echo (int)$station->id; ?></td>
                <td><?php echo CHtml::link(CHtml::encode($station->name), array('station/view', 'id' => $station->id)); ?></td>
                <td><?php echo CHtml::encode($station->getLineLabel()); ?></td>
                <td>
                    <?php if ($station->position_station !== ''): ?>
                        <span class="tag <?php echo CHtml::encode($station->position_station); ?>">
                            <?php echo CHtml::encode($station->position_station); ?></span>
                    <?php else: ?>
                        <span class="quiet">free</span>
                    <?php endif; ?>
                </td>
                <td class="actions">
                    <?php echo CHtml::link('Update', array('station/update', 'id' => $station->id)); ?> |
                    <?php if ((int)$station->line_id !== 0): ?>
                        <?php echo CHtml::link('Remove from line', array('station/detach', 'id' => $station->id), array(
                            'onclick' => "return confirm('Remove this station from its line?');")); ?> |
                    <?php endif; ?>
                    <?php echo CHtml::link('Delete', array('station/delete', 'id' => $station->id), array(
                        'onclick' => "return confirm('Delete this station?');")); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <p><?php echo CHtml::link('Create Station', array('station/create'), array('class' => 'button')); ?></p>
</div><!-- content -->

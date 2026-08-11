<?php
/* @var $this StationController */
/* @var $station Station */
?>
<div id="content" class="wide">
    <h1>Station <?php echo CHtml::encode($station->name); ?></h1>
    <table class="detail">
        <tr><th>ID</th><td><?php echo (int)$station->id; ?></td></tr>
        <tr><th>Name</th><td><?php echo CHtml::encode($station->name); ?></td></tr>
        <tr><th>Line</th><td><?php
            echo $station->line === null
                ? '-'
                : CHtml::link(CHtml::encode($station->line->code), array('line/view', 'id' => $station->line_id));
        ?></td></tr>
        <tr><th>Position</th><td><?php echo $station->position_station === ''
            ? 'free' : CHtml::encode($station->position_station); ?></td></tr>
    </table>
    <p>
        <?php echo CHtml::link('Update', array('station/update', 'id' => $station->id), array('class' => 'button')); ?>
        <?php echo CHtml::link('Delete', array('station/delete', 'id' => $station->id), array(
            'class' => 'button danger', 'onclick' => "return confirm('Delete this station?');")); ?>
    </p>
</div><!-- content -->

<?php
/* @var $this LineController */
/* @var $line Line */
/* @var $slots array */
/* @var $selected array */
/* @var $stations Station[] */
/* @var $errors string[] */

// 下拉選單資料：只列出尚未指派、或已屬於本路線的站點
$options = CHtml::listData($stations, 'id', 'name');
?>
<div id="content" class="wide">

    <h1>Stations of <?php echo CHtml::encode($line->code); ?></h1>

    <p class="note">All <?php echo count($slots); ?> stations must be selected at the same time.
        A station can belong to one Intermediate Line only.</p>

    <?php if (!empty($errors)): ?>
        <div class="errorSummary" style="border:2px solid #C00;padding:7px;margin:0 0 20px;background:#FEE;">
            <p>Please fix the following errors:</p>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo CHtml::encode($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="form">
        <?php echo CHtml::beginForm('', 'post', array('id' => 'line-stations-form')); ?>

        <ul class="station-slots">
            <?php foreach ($slots as $i => $slot): ?>
                <li>
                    <label for="StationSlots_<?php echo $i; ?>">
                        <?php echo CHtml::encode($slot['label']); ?>
                        <span class="required">*</span>
                    </label>
                    <?php echo CHtml::dropDownList(
                        'StationSlots[' . $i . ']',
                        isset($selected[$i]) ? $selected[$i] : '',
                        $options,
                        array('id' => 'StationSlots_' . $i, 'empty' => '-- select station --')
                    ); ?>
                    <span class="tag <?php echo $slot['position']; ?>"><?php echo $slot['position']; ?></span>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="row buttons">
            <?php echo CHtml::submitButton('Assign stations'); ?>
            <?php echo CHtml::link('Cancel', array('line/view', 'id' => $line->id), array('class' => 'button secondary')); ?>
        </div>

        <?php echo CHtml::endForm(); ?>
    </div><!-- form -->

    <p class="hint-text"><?php echo count($stations); ?> station(s) currently available for this line.
        <?php echo CHtml::link('Create a new station', array('station/create')); ?>.</p>
</div><!-- content -->

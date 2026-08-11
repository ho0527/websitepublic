<?php
/* @var $this SiteController */
/* @var $summary array */
$assets = Yii::app()->request->baseUrl . '/assets';
?>
<div id="content" class="full">

    <h1>Welcome to <i>LVB Leipzig</i></h1>

    <p>The largest transport company in Leipzig, LVB (Leipziger VerkehrsBetriebe) translated into English as the
        &ldquo;Leipzig Transport Company&rdquo;, operates the tramway and bus transport services in Leipzig.</p>
    <p>The LVB route network is a part of the regional public transport association and was formed by merger, from
        January 1917. Public transport in Leipzig is characterized by a dense light-rail system.</p>
    <p>13 tram lines serve a transport area of about 152 kilometers, complemented by more than 30 bus lines in large
        part being en-route in the suburban area.</p>

    <?php if (Yii::app()->user->isGuest): ?>
        <p class="hint-text">Please <?php echo CHtml::link('log in', array('site/login')); ?> to manage the
            Intermediate Lines, Stations, Vehicles and Drivers.</p>
    <?php else: ?>
        <ul class="summary">
            <?php foreach ($summary as $label => $count): ?>
                <li><strong><?php echo (int)$count; ?></strong><?php echo CHtml::encode($label); ?></li>
            <?php endforeach; ?>
        </ul>
        <div class="clear"></div>
    <?php endif; ?>

    <img width="670" src="<?php echo $assets; ?>/images/Routes.svg" alt="LVB route network">
</div><!-- content -->

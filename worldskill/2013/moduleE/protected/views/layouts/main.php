<?php
/* @var $this Controller */
/* @var $content string */
$baseUrl = Yii::app()->request->baseUrl;
$assets  = $baseUrl . '/assets';

// 主選單項目：圖示檔名 => 標題 + 路由
$menuItems = array(
    array('icon' => 'line.png',    'title' => 'Line',    'route' => array('line/index')),
    array('icon' => 'station.png', 'title' => 'Station', 'route' => array('station/index')),
    array('icon' => 'vehicle.png', 'title' => 'Vehicle', 'route' => array('vehicle/index')),
    array('icon' => 'driver.png',  'title' => 'Driver',  'route' => array('driver/index')),
    array('icon' => 'xml.png',     'title' => 'XML-XSD', 'route' => array('xml/index')),
    array('icon' => 'user.png',    'title' => 'User',    'route' => array('user/index')),
);
$isGuest = Yii::app()->user->isGuest;
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="language" content="en">
    <link rel="stylesheet" type="text/css" href="<?php echo $assets; ?>/css/core.css" media="screen, projection">
    <link rel="stylesheet" type="text/css" href="<?php echo $assets; ?>/css/principal.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $assets; ?>/css/nav.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $assets; ?>/css/content.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $assets; ?>/css/form.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $assets; ?>/css/footer.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $assets; ?>/css/module_e.css">
    <title>WorldSkills Leipzig / Leipziger Verkehrsbetriebe</title>
</head>
<body>

<div class="container" id="page">

    <a href="<?php echo CHtml::normalizeUrl(array('site/index')); ?>">
        <div id="header"><div id="logo"><!--WorldSkills Leipzig / Leipziger Verkehrsbetriebe--></div></div>
    </a>

    <div id="mainmenu">
        <ul>
            <?php if (!$isGuest): // 未登入時不顯示任何功能 ?>
                <?php foreach ($menuItems as $item): ?>
                    <li>
                        <a href="<?php echo CHtml::normalizeUrl($item['route']); ?>" title="<?php echo $item['title']; ?>">
                            <span style="background-image: url(<?php echo $assets; ?>/images/<?php echo $item['icon']; ?>)"></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>

        <!-- 登入 / 登出 -->
        <div id="access">
            <?php if ($isGuest): ?>
                <div><?php echo CHtml::link('Login', array('site/login')); ?></div>
            <?php else: ?>
                <div><?php echo CHtml::encode(Yii::app()->user->getState('displayName')); ?>
                    (<?php echo CHtml::link('Logout', array('site/logout')); ?>)</div>
            <?php endif; ?>
        </div>
    </div><!-- mainmenu -->

    <?php if (!empty($this->breadcrumbs)): ?>
        <div class="breadcrumbs">
            <?php echo CHtml::link('Home', array('site/index')); ?>
            <?php foreach ($this->breadcrumbs as $label => $url): ?>
                &raquo;
                <?php if (is_int($label)): ?>
                    <span><?php echo CHtml::encode($url); ?></span>
                <?php else: ?>
                    <?php echo CHtml::link(CHtml::encode($label), $url); ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div><!-- breadcrumbs -->
    <?php endif; ?>

    <?php
    // 一次性訊息（成功／錯誤）
    $flashes = '';
    foreach (array('success', 'notice', 'error') as $flashType) {
        if (Yii::app()->user->hasFlash($flashType)) {
            $flashes .= '<div class="flash-' . $flashType . '">'
                     . CHtml::encode(Yii::app()->user->getFlash($flashType)) . '</div>';
        }
    }
    ?>

    <?php if (empty($this->operations)): ?>
        <?php echo $flashes . $content; ?>
    <?php else: ?>
        <div class="span-19">
            <?php echo $flashes . $content; ?>
        </div>
        <div class="span-5 last">
            <div id="sidebar">
                <div class="portlet">
                    <div class="portlet-decoration">
                        <div class="portlet-title">Operations</div>
                    </div>
                    <div class="portlet-content">
                        <ul class="operations">
                            <?php foreach ($this->operations as $label => $url): ?>
                                <li><?php echo CHtml::link(CHtml::encode($label), $url); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div><!-- sidebar -->
        </div>
    <?php endif; ?>

    <div class="clear"></div>

    <div id="footer">
        <ul>
            <li class="sitemap"><a href="<?php echo CHtml::normalizeUrl(array('site/index')); ?>">Site Map</a></li>
            <li class="copyr"><a href="#">Copyright &copy; 2013 by LVB</a></li>
            <li class="allright"><a href="#">All Rights Reserved. <br>By WorldSkills International</a></li>
        </ul>
    </div><!-- footer -->

</div><!-- page -->

</body>
</html>

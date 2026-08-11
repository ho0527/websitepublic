<?php
/* @var $this XmlController */
/* @var $xml string */
/* @var $result array */
?>
<div id="content" class="wide">

    <h1>XML Schema</h1>

    <p class="hint-text">The XML file is generated from the database every time it is requested and validated
        against the supplied <code>lvb_system.xsd</code>.</p>

    <p>
        <?php echo CHtml::link('Download XML', array('xml/download'), array('class' => 'button')); ?>
        <?php echo CHtml::link('Display XML', array('xml/display'), array('class' => 'button secondary', 'target' => '_blank')); ?>
    </p>

    <?php if ($result['valid']): ?>
        <div class="flash-success">The generated XML is valid against the XML Schema <code>lvb_system.xsd</code>.</div>
    <?php else: ?>
        <div class="flash-error">
            The generated XML does not validate against <code>lvb_system.xsd</code>:
            <ul>
                <?php foreach ($result['errors'] as $error): ?>
                    <li><?php echo CHtml::encode($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <h3>Preview</h3>
    <pre class="xml-preview"><?php echo CHtml::encode($xml); ?></pre>
</div><!-- content -->

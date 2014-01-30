<?php
/**
 * One item layout (not expanded)
 */
/* @var $this CdGridPreview */
/* @var $data (CActiveRecord|CdGridItemBehavior) - making code competition work better */

$previewImage = CHtml::image(
    $data->getGridItemPreviewSrc(), 
    $data->getGridItemPreviewAlt(), 
    $data->getGridItemPreviewOptions()
);
$previewLink = CHtml::link($previewImage, $data->getGridItemNoJsUrl(), $data->getGridItemLinkOptions());


echo CHtml::openTag('li', array(
    'data-toggle' => 'tooltip',
    'data-title'  => $data->getGridItemPreviewAlt(),
));
echo $previewLink;
echo CHtml::closeTag('li');
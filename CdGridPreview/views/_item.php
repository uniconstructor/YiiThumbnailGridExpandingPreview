<?php
/**
 * One item layout (not expanded)
 */
/* @var $this CdGridPreview */
/* @var $data CdGridItemBehavior */

$imageOptions = $data->getGridItemPreviewOptions();
$imageOptions = CMap::mergeArray($this->previewHtmlOptions, $imageOptions);

$previewImage = CHtml::image(
    $data->getGridItemPreviewSrc(), 
    $data->getGridItemPreviewAlt(), 
    $imageOptions
);
$previewLink = CHtml::link($previewImage, $data->getGridItemNoJsUrl(), $data->getGridItemLinkOptions());


echo CHtml::openTag('li', $data->getGridListItemOptions());
echo $previewLink;
echo CHtml::closeTag('li');
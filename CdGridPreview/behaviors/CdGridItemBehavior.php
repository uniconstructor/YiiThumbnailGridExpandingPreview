<?php

/**
 * Base class for grid item behaviors
 * Before using your models in grid you shoud create a new behavior for displayed model:
 * 1) extend this class
 * 2) override all needed methods
 * 3) attach created behavior to your model
 */
class CdGridItemBehavior extends CActiveRecordBehavior
{
    /**
     * @var bool - create unique id for every preview image
     */
    public $uniquePreviewId = false;
    /**
     * @var array - options for preview img tag
     */
    public $previewHtmlOptions = array();
    /**
     * @var bool - load item description via AJAX if true
     */
    public $useAjax = false;
    /**
     * @var array - options for CHtml::ajax() to create a request with item description
     *              (used only if ajaxDescription=true)
     *              Note: do not specify 'success' parameter: in this case the item content will not be updated,
     *              because CHtml::ajax() ignores 'update' parameter if 'success' is specified
     */
    public $ajaxOptions = array();
    /**
     * @var string - name of the url parameter which contains item primary key during content AJAX request
     */
    public $ajaxPk = 'id';
    
    /**
     * Get link to small preview image
     * @return string
     */
    public function getGridItemPreviewSrc()
    {
        throw new CException('This method must be overriden');
    }
    
    /**
     * Alternative text if image not loaded
     * Not required but strongly recommended
     * @return string
     */
    public function getGridItemPreviewAlt()
    {
        return '';
    }
    
    /**
     * Get link to large model image
     * @return string
     */
    public function getGridItemLargeSrc()
    {
        throw new CException('This method must be overriden');
    }
    
    /**
     * Image/model title: in expanded description displays as header. Optional.
     * @return string
     */
    public function getGridItemTitle()
    {
        return '';
    }
    
    /**
     * Image/model description. 
     * HTML is allowed here, but please try to use only one type of quotes in all code.
     * Yii 1.1.13 and below can incorrect handle this situation sometimes.
     * @return string
     */
    public function getGridItemDescription()
    {
        $content = $this->getGridItemDescriptionContent();
        $ajax    = $this->createGridItemDescriptionAjax();
        
        return $content.$ajax;
    }
    
    /**
     * Create the item description: override this function if you not plan to load description via AJAX
     * You can also use this function to output some content before AJAX script call
     * HTML is allowed here, but please try to use only one type of quotes in all code.
     * Yii 1.1.13 and below can incorrect handle this situation sometimes.
     * @return string
     */
    protected function getGridItemDescriptionContent()
    {
        return '';
    }
    
    /**
     * Generate AJAX script to load item description (used only if $this->ajaxDescription=true)
     * @return string - JS returning item description via AJAX
     */
    protected function createGridItemDescriptionAjax()
    {
        $descriptionId = $this->getCdGridElementId('content');
        
        // AJAX settings
        $ajaxDefaults = array(
            'data'   => array($this->ajaxPk => $this->owner->primaryKey),
            'update' => '#'.$descriptionId,
            'type'   => 'post',
            'cache'  => false,
        );
        if ( Yii::app()->request->enableCsrfValidation )
        {// add CSRF token if nesessary
            $ajaxDefaults['data'][Yii::app()->request->csrfTokenName] = Yii::app()->request->csrfToken;
        }
        $this->ajaxOptions = CMap::mergeArray($ajaxDefaults, $this->ajaxOptions);
        
        $js  = '<div id="'.$descriptionId.'"></div>';
        $js .= '<script>'.CHtml::ajax($this->ajaxOptions).'</script>';
        
        return $js;
    }
    
    /**
     * Url to model description page (used as fallback, if JS is not supported)
     * @return string
     */
    public function getGridItemNoJsUrl()
    {
        throw new CException('This method must be overriden');
    }
    
    /**
     * Get all needed options for preview &lt;img&gt; tag 
     * @return array
     */
    public function getGridItemPreviewOptions()
    {
        if ( $this->uniquePreviewId )
        {
            $this->previewHtmlOptions['id'] = $this->getCdGridElementId('preview');
        }
        return $this->previewHtmlOptions;
    }
    
    /**
     * Collects all special options for link
     * @return void
     */
    public function getGridItemLinkOptions()
    {
        return array(
            'data-largesrc'    => $this->getGridItemLargeSrc(),
            'data-title'       => $this->getGridItemTitle(),
            'data-description' => $this->getGridItemDescription(),
        );
    }
    
    /**
     * Get unique id for grid element
     * 
     * @param string $type - element type (for example 'preview' or 'content')
     * @return string
     */
    protected function getCdGridElementId($type)
    {
        $class = get_class($this->owner);
        return 'cd'.$class.'_'.$type.'_'.$this->owner->getPrimaryKey();
    }
}
<?php

/**
 * Thumbnail Grid with Expanding Preview
 * @see http://tympanus.net/codrops/2013/03/19/thumbnail-grid-with-expanding-preview/
 * 
 * @todo separate "system styles" (elemets alignment) and "content styles" 
 *      (header and text size, background color, etc.)
 *       Put them into 2 different files. 
 */
class CdGridPreview extends CWidget
{
    /**
     * @var string - selector for grid element
     */
    public $selector;
    /**
     * @var (int|string)[] - expand action settings. Contains keys:
     *          ['minHeight'] - minimum preview height (default: 500)
     *          ['speed']     - animation speed        (default: 350)
     *          ['easing']    - easing type            (default: 'ease')
     */
    public $expandSettings;
    /**
     * @var string - path to widget root (override if nesesary)
     */
    public $widgetLocation    = 'ext.CdGridPreview';
    /**
     * @var string - default ListView widget (override it if you use want to use Bootstrap or any custom ListView)
     */
    public $listViewLocation  = 'zii.widgets.CListView';
    /**
     * @var array - custom options for ListView widget
     */
    public $listViewOptions = array();
    /**
     * @var CActiveDataProvider - data for grid: each elemrnt shoud have
     */
    public $dataProvider;
    /**
     * @var array - HTML options for view container tag
     */
    public $htmlOptions = array();
    /**
     * @var array - HTML options for preview image
     */
    public $previewHtmlOptions = array();
    /**
     * @var string - if true, hide large image, display description with 100% width instread
     */
    public $descriptionOnly  = false;
    /**
     * @var array - JS plugin options
     */
    public $options          = array();
    /**
     * @var bool - include modernizr library?
     *             If you already use this library in your application then you don't need to register it twice
     * @see http://modernizr.com official site for more info
     */
    public $includeModernizr = true;
    
    /**
     * @var string
     */
    protected $assetUrl;
    
    /**
     * @see CWidget::init()
     */
    public function init()
    {
        $this->setupOptions();
        
        // register widget styles and scripts
        $this->assetUrl = Yii::app()->assetManager->publish(Yii::getPathOfAlias($this->widgetLocation.'.assets'));
        // native component styles: keep in mind that you may need to override some of them
        Yii::app()->clientScript->registerCssFile($this->assetUrl.'/css/component.css');
        
        if ( $this->includeModernizr )
        {// include modernizr in head section to avoid "undefined function" JS error
            Yii::app()->clientScript->registerScriptFile($this->assetUrl.'/js/modernizr.custom.js', CClientScript::POS_HEAD);
        }
        // "grid.js" is main widget library. It can be included in the bottom to speed up page load 
        Yii::app()->clientScript->registerScriptFile($this->assetUrl.'/js/grid.js', CClientScript::POS_END);
        // finally, add grid init script. It runs only when page is ready and must be included after "grid.js"
        $js = $this->createGridInitJs();
        Yii::app()->clientScript->registerScript($this->getId().'_init', $js, CClientScript::POS_READY);
        
        parent::init();
    }
    
    /**
     * @see CWidget::run()
     */
    public function run()
    {
        $this->widget($this->listViewLocation, $this->listViewOptions);
    }
    
    /**
     * Creates thumbnail grid init script
     * @return string
     */
    protected function createGridInitJs()
    {
        return 'Grid.init('.CJSON::encode($this->options).');';
    }
    
    /**
     * @return void
     */
    protected function setupOptions()
    {
        // Yii widget options
        if ( ! ($this->dataProvider instanceof CDataProvider) )
        {
            throw new CException('Grid data provider not set');
        }
        
        if ( ! isset($this->htmlOptions['id']) )
        {
            $this->htmlOptions['id'] = $this->getContainerId();
        }
        if ( ! $this->selector )
        {
            $this->selector = '.og-grid';
        }
        
        $listViewDefaults = array(
            'template'      => '{summary}{items}{pager}',
            'itemsTagName'  => 'ul',
            'itemsCssClass' => 'og-grid',
            'itemView'      => '_item',
            'dataProvider'  => $this->dataProvider,
            'htmlOptions'   => $this->htmlOptions,
            'id'            => $this->htmlOptions['id'],
        );
        $this->listViewOptions = CMap::mergeArray($listViewDefaults, $this->listViewOptions);
        
        // JS plugin options
        $this->options['descriptionOnly'] = $this->descriptionOnly;
        if ( $this->selector )
        {// @todo raize an error if selector is not set
            $this->options['selector'] = $this->selector;
        }
        if ( $this->expandSettings )
        {
            $this->options['settings'] = $this->expandSettings;
        }
    }
    
    /**
     * @return string
     */
    protected function getContainerId()
    {
        return $this->getId().'_gridContainer';
    }
}
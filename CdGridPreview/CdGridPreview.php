<?php

/**
 * Thumbnail Grid with Expanding Preview
 * @see http://tympanus.net/codrops/2013/03/19/thumbnail-grid-with-expanding-preview/
 * 
 * @todo is default.css required for this plugin?
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
     * @var array - the HTML options for the view container tag
     */
    public $htmlOptions = array();
    
    /**
     * @var array - JS plugin options
     */
    protected $options = array();
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
        // publishing original plugin assets
        $this->assetUrl = Yii::app()->assetManager->publish(Yii::getPathOfAlias($this->widgetLocation.'.assets'));
        
        // registring custom widget libraries
        Yii::app()->clientScript->registerCssFile($this->assetUrl.'/css/component.css');
        Yii::app()->clientScript->registerScriptFile($this->assetUrl.'/js/modernizr.custom.js', CClientScript::POS_HEAD);
        Yii::app()->clientScript->registerScriptFile($this->assetUrl.'/js/grid.js', CClientScript::POS_END);
        
        // initilazing a grid
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
        if ( ! ($this->dataProvider instanceof CActiveDataProvider) )
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
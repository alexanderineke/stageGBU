<?php
/**
 *
 * Yii extension for Jquery Tag-It
 *
 * @author Johan Post (jpost@gbu.nl)
 *
 */
class ETagIt extends CWidget {
    /**
     * @var string The id of the tag ul
     */
    public $id = false;
    /**
     * @var string The classes of the tag ul
     */
    public $class = false;
    /**
     * @var array An array of options that are supported by Tag-It
     */
    public $options = array();
    /**
     * @var string The URL that handles the autocomplete search
     */
    public $url = false;
    /**
     * @var array An array of pre-existing items
     */
    public $values = array();
    /**
     * Create a div and the appropriate Javascript to make the tagging work
     */
    public function run() {
        if (!$this->url || $this->url == '')
            $this->url = Yii::app()->createUrl('tag/get');

        if (!$this->id || $this->id == '')
            $this->id = 'tags';

        if (!$this->class || $this->class == '')
            $this->class = 'tagit';
        else
            $this->class = $this->class.' tagit';

        $required = CHtml::openTag('span', array('class' => 'required')) . '&#42;' . CHtml::closeTag('span');
        echo CHtml::label('Steekwoorden '.$required, $this->id, array('class' => 'required'));
        echo CHtml::openTag('ul', array('class' => $this->class, 'id' => $this->id));
        foreach ($this->values as $value)
            echo CHtml::openTag('li', array('data-tagid' => $value['id'])) . $value['tag'] . CHtml::closeTag('span');   
        echo CHtml::closeTag('ul');

        $options = CMap::mergeArray(array(), $this->options);

        $options = CJavaScript::encode($options);

        $script = '$(\'#'.$this->id.'\').tagit({
                        autocomplete: {
                            delay: 0, 
                            minLength: 2, 
                            source: function(request, response){
                                $.ajax({
                                  url: "'.$this->url.'",
                                  dataType: "json",
                                  data: {
                                    term: request.term
                                  },
                                  success: function(data){
                                    response(data);
                                  }, 
                                });
                            }
                        }
                    });';
        $this->registerAssets();
        Yii::app()->getClientScript()->registerScript(__CLASS__ . '#' . $this->getId(), $script, CClientScript::POS_END);
    }

    private function registerAssets() {
        $basePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR;
        $baseUrl = Yii::app()->getAssetManager()->publish($basePath);
        Yii::app()->getClientScript()->registerCoreScript('jquery');
        Yii::app()->clientScript->registerCoreScript('jquery.ui');
        Yii::app()->getClientScript()->registerScriptFile("{$baseUrl}/js/tag-it.js", CClientScript::POS_END);
        Yii::app()->getClientScript()->registerCssFile("{$baseUrl}/css/jquery.tagit.css");
        Yii::app()->getClientScript()->registerCssFile("{$baseUrl}/css/jquery-ui.css");
    }

}
?>

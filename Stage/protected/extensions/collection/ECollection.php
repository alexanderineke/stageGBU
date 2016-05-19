<?php 

class ECollection extends CWidget 
{
    /**
     * @var string Type of file. Example: image or documentent
     */	
	public $file_type = false;

    /**
     * @var int The id of the file being added to the collection
     */
	public $file_id = false;


	public function run()
	{
		if(!Yii::app()->user->isGuest){
			$this->widget('bootstrap.widgets.TbButton', array(
			    'label'=>'Voeg toe aan collectie',
			    'type'=>'primary',
			    'htmlOptions'=>array(
			        'data-toggle'=>'modal',
			        'data-target'=>'#collection_modal',
			    ),
			));

			$list = CHtml::listData(
			    Collection::model()->findAll(
			        array(
			            'order' => 'title', 
			            'condition' => 'user_id=:id AND published=1 OR id=17',
			            'params' => array(':id'=>Yii::app()->user->getId())
			        )
			    ), 
			    'id',
			    'title'
			);

			$this->render('_modal', array(
				'type'=>$this->file_type,
				'id'=>$this->file_id,
				'list'=>$list,
			)); 
		}
	}

}

?>
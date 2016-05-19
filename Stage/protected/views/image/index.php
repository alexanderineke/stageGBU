<?php
$this->breadcrumbs=array(
	'Afbeeldingen',
);

$this->menu=array(
	array('label'=>'Acties','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Maak afbeeldingen aan','icon'=>'file','url'=>array('create'),'visible'=>Yii::app()->user->checkAccess('user')),
	array('label'=>'Beheer afbeeldingen','url'=>array('admin'),'icon'=>'list-alt','visible'=>Yii::app()->user->checkAccess('admin')),
);

?>

<h1>Afbeeldingen</h1>

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'dataProvider'=>$model->search(),
    'columns'=>array(
        array('name'=>'images', 'type' => 'html', 'htmlOptions' => array(
		        'style' => 'width: 100px; text-align: center;',
		    ), 'header'=>'Voorbeeld', 'value'=>'CHtml::image("uploads/afbeeldingen/".$data->images[0]->location."/thumb/".$data->images[0]->file.$data->images[0]->format)'),    	
        array('name'=>'title', 'header'=>'Titel', 'type'=>'html', 'value'=>'CHtml::link($data->title, Yii::app()->createUrl("image/view",array("id"=>$data->id)))'),
    ),
    'enableHistory'=>true,
	'pager' => array('class' => 'bootstrap.widgets.TbPager', 'prevPageLabel' => '&laquo;', 'nextPageLabel' => '&raquo;'),
)); ?>


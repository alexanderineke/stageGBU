<?php
$this->breadcrumbs=array(
	'Collecties',
);

$this->menu=array(
	array('label'=>'Acties','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Maak collectie aan','url'=>array('create'),'icon'=>'file','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Beheer collecties','url'=>array('admin'),'icon'=>'list-alt','visible'=>Yii::app()->user->checkAccess('admin')),
);
?>

<h1>Collecties</h1>

<?php $this->widget('bootstrap.widgets.TbListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>

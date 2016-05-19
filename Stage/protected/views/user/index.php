<?php
$this->breadcrumbs=array(
	'Gebruikers',
);

$this->menu=array(
	array('label'=>'Acties','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Maak gebruiker aan','url'=>array('create'),'icon'=>'file','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Beheer gebruikers','url'=>array('admin'),'icon'=>'list-alt','visible'=>Yii::app()->user->checkAccess('admin')),
);

?>

<h1>Gebruikers</h1>

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'user-grid',
	'dataProvider'=>$dataProvider,
	'columns'=>array(
		'id',
		array(
			'name'=>'username',
			'value' => 'CHtml::link($data->username, Yii::app()->createUrl("user/view",array("id"=>$data->primaryKey)))',
			'type' => 'raw',
		),
		'email',
		'roles',
	),
)); ?>

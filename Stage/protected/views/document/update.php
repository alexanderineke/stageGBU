<?php
$this->breadcrumbs=array(
	'Documenten'=>array('index'),
	$model->title=>array('view','id'=>$model->id),
	'Bewerk',
);

$this->menu=array(
	array('label'=>'Acties','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Lijst van documenten','url'=>array('index'),'icon'=>'list','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Maak documenten aan','icon'=>'file','url'=>array('create'),'visible'=>Yii::app()->user->checkAccess('user')),
	array('label'=>'Bekijk document','url'=>array('view','id'=>$model->id),'icon'=>'eye-open','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Beheer document','url'=>array('admin'),'icon'=>'list-alt','visible'=>Yii::app()->user->checkAccess('admin')),
);
?>

<h1>Bewerk document <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_update',array('model'=>$model)); ?>
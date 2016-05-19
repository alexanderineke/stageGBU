<?php
$this->breadcrumbs=array(
	'Audio'=>array('index'),
	$model->title=>array('view','id'=>$model->id),
	'Bewerk',
);

$this->menu=array(
	array('label'=>'Acties','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Lijst van audio bestanden','url'=>array('index'),'icon'=>'list','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Maak audio bestanden aan','icon'=>'file','url'=>array('create'),'visible'=>Yii::app()->user->checkAccess('user')),
	array('label'=>'Bekijk audio bestand','url'=>array('view','id'=>$model->id),'icon'=>'eye-open','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Beheer audio bestand','url'=>array('admin'),'icon'=>'list-alt','visible'=>Yii::app()->user->checkAccess('admin')),
);
?>

<h1>Bewerk audio bestanden <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_update',array('model'=>$model)); ?>
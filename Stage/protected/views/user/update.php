<?php
$this->breadcrumbs=array(
	'Gebruikers'=>array('index'),
	$model->username=>array('view','id'=>$model->id),
	'Bewerk',
);

$this->menu=array(
	array('label'=>'Acties','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Lijst van gebruikers','url'=>array('index'),'icon'=>'list','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Maak gebruiker aan','url'=>array('create'),'icon'=>'file','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Bekijk gebruiker','url'=>array('view','id'=>$model->id),'icon'=>'eye-open','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Beheer gebruiker','url'=>array('admin'),'icon'=>'list-alt','visible'=>Yii::app()->user->checkAccess('admin')),
);
?>

<h1>Bewerk gebruiker "<?php echo $model->username; ?>"</h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>
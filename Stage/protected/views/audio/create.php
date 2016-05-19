<?php
$this->breadcrumbs=array(
	'Documenten'=>array('index'),
	'Aanmaken',
);

$this->menu=array(
	array('label'=>'Acties','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Lijst van documenten','url'=>array('index'),'icon'=>'list','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Beheer documenten','url'=>array('admin'),'icon'=>'list-alt','visible'=>Yii::app()->user->checkAccess('admin')),
);
?>

<h1>Maak audio bestand aan</h1>

<?php echo $this->renderPartial('_create', array('model'=>$model)); ?>


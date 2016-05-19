<?php
$this->breadcrumbs=array(
	'Afbeeldingen'=>array('index'),
	'Aanmaken',
);

$this->menu=array(
	array('label'=>'Acties','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Lijst van afbeeldingen','url'=>array('index'),'icon'=>'list','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Beheer afbeeldingen','url'=>array('admin'),'icon'=>'list-alt','visible'=>Yii::app()->user->checkAccess('admin')),
);

?>

<h1>Maak afbeelding aan</h1>

<?php echo $this->renderPartial('_create', array('model'=>$model)); ?>




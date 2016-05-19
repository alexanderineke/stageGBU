<?php
$this->breadcrumbs=array(
	'Gebruikers'=>array('index'),
	'Aanmaken',
);

$this->menu=array(
	array('label'=>'Acties','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Lijst van gebruikers','url'=>array('index'),'icon'=>'list','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Beheer gebruikers','url'=>array('admin'),'icon'=>'list-alt','visible'=>Yii::app()->user->checkAccess('admin')),
);
?>

<h1>Maak gebruiker aan</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
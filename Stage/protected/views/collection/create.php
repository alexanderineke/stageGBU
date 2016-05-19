<?php
$this->breadcrumbs=array(
	'Collecties'=>array('index'),
	'Maak aan',
);

$this->menu=array(
	array('label'=>'Acties','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Lijst van collecties','icon'=>'list','url'=>array('index'),'visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Beheer collecties','url'=>array('admin'),'icon'=>'list-alt','visible'=>Yii::app()->user->checkAccess('admin')),
);
?>

<h1>Maak een collectie</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
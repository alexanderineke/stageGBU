<?php
$this->breadcrumbs=array(
	'Afbeeldingen'=>array('index'),
	'Meerdere aanmaken'=>array(),
	'Verwerken',
);

$this->menu=array(
	array('label'=>'Acties','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Lijst van afbeeldingen','url'=>array('index'),'icon'=>'list','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Beheer afbeeldingen','url'=>array('admin'),'icon'=>'list-alt','visible'=>Yii::app()->user->checkAccess('admin')),
);
?>

<h1>Verwerk document <?php echo $file['file']; ?></h1>

<?php echo $this->renderPartial('_process', array('model'=>$model,'file'=>$file,'collection_list'=>$collection_list)); ?>


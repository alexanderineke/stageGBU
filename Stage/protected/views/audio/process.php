<?php
$this->breadcrumbs=array(
	'Audio'=>array('index'),
	'Meerdere aanmaken'=>array(),
	'Verwerken',
);

$this->menu=array(
	array('label'=>'Acties','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Lijst van audio bestanden','url'=>array('index'),'icon'=>'list','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Beheer audio bestanden','url'=>array('admin'),'icon'=>'list-alt','visible'=>Yii::app()->user->checkAccess('admin')),
);
?>

<h1>Verwerk audio <?php echo $file['file']; ?></h1>
<?php echo $this->renderPartial('_process', array('model'=>$model,'file'=>$file,'collection_list'=>$collection_list)); ?>


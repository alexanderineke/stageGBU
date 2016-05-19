<?php
$this->breadcrumbs=array(
	'Gebruikers'=>array('index'),
	$model->username,
);

$this->menu=array(
	array('label'=>'Acties','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Lijst van gebruikers','url'=>array('index'),'icon'=>'list','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Maak gebruiker aan','url'=>array('create'),'icon'=>'file','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Bewerk gebruiker','url'=>array('update','id'=>$model->id),'icon'=>'pencil','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Verwijder gebruiker','url'=>'#','icon'=>'trash','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Weet je zeker dat je deze gebruiker wilt verwijderen?'),'visible'=>Yii::app()->user->checkAccess('admin')),
	array('label'=>'Beheer gebruiker','url'=>array('admin'),'icon'=>'list-alt','visible'=>Yii::app()->user->checkAccess('admin')),
);
?>

<h1>Bekijk gebruiker "<?php echo $model->username; ?>"</h1>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'username',
		'email',
		'roles',
	),
)); 

?>

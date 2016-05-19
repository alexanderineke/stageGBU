<?php
$this->breadcrumbs=array(
	'Afbeeldingen'=>array('index'),
	$model->title,
);

$this->menu=array(
	array('label'=>'Acties','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Lijst van afbeeldingen','url'=>array('index'),'icon'=>'list','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Maak afbeeldingen aan','icon'=>'file','url'=>array('create'),'visible'=>Yii::app()->user->checkAccess('user')),
	array('label'=>'Bewerk afbeelding','url'=>array('update','id'=>$model->id),'icon'=>'pencil','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Verwijder afbeelding','url'=>'#','icon'=>'trash','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Weet je zeker dat je deze afbeelding wilt verwijderen?'),'visible'=>Yii::app()->user->checkAccess('admin')),
	array('label'=>'Beheer afbeelding','url'=>array('admin'),'icon'=>'list-alt','visible'=>Yii::app()->user->checkAccess('admin')),
);
?>

<h1><?php echo $model->title; ?></h1>

<?php echo CHtml::image('uploads/afbeeldingen/'.$model->images[0]->location.'/'.$model->images[0]->file.$model->images[0]->format, $model->title); ?>

<?php
	$tags = '';
	foreach($model->tags as $i => $tag)
			$tags .= $tag->name.', ';
	$tags = substr($tags, 0, -2);
?>

<?php $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'title',
		array('label'=>'Uploader', 'value'=>$model->user->username),
		'description:html',
		array('label'=>'Steekwoorden', 'value'=>$tags),
		'year',
		array('name'=>'owner', 'value'=>!empty($model->owner) ? $model->owner : "Niet opgegeven"),
		array('name'=>'created_on', 'value'=>($model->created_on !== "0000-00-00 00:00:00" ? $model->created_on : "Niet beschikbaar")),
		array('name'=>'modified_on', 'value'=>($model->created_on !== "0000-00-00 00:00:00" ? $model->created_on : "Niet beschikbaar")),		
		array('name'=>'published', 'label'=>'Gepubliceerd', 'value'=>$model->published? "Ja":"Nee"),
	),
));

$this->widget('ext.collection.ECollection',
	array(
		'file_id'=>$model->id,
		'file_type'=>'image',
	)
);


?>

<hr />

<?php
$this->breadcrumbs=array(
	'Documenten'=>array('index'),
	$model->title,
);

$this->menu=array(
	array('label'=>'Acties','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Lijst van documenten','url'=>array('index'),'icon'=>'list','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Maak documenten aan','icon'=>'file','url'=>array('create'),'visible'=>Yii::app()->user->checkAccess('user')),
	array('label'=>'Bewerk document','url'=>array('update','id'=>$model->id),'icon'=>'pencil','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Verwijder document','url'=>'#','icon'=>'trash','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Weet je zeker dat je deze document wilt verwijderen?'),'visible'=>Yii::app()->user->checkAccess('admin')),
	array('label'=>'Beheer document','url'=>array('admin'),'icon'=>'list-alt','visible'=>Yii::app()->user->checkAccess('admin')),
);
?>

<h1><?php echo $model->title; ?></h1>
<?php echo CHtml::image('uploads/documenten/'.$model->documents[0]->location.'/'.$model->documents[0]->file.'_b.jpg', $model->title); ?>
<?php echo CHtml::image('uploads/documenten/'.$model->documents[0]->location.'/'.$model->documents[0]->file.'_c.jpg', $model->title); ?>
<?php echo CHtml::image('uploads/documenten/'.$model->documents[0]->location.'/'.$model->documents[0]->file.'_z.jpg', $model->title); ?>
<?php echo CHtml::image('uploads/documenten/'.$model->documents[0]->location.'/'.$model->documents[0]->file.'.jpg', $model->title); ?>
<?php echo CHtml::image('uploads/documenten/'.$model->documents[0]->location.'/'.$model->documents[0]->file.'_n.jpg', $model->title); ?>
<?php echo CHtml::image('uploads/documenten/'.$model->documents[0]->location.'/'.$model->documents[0]->file.'_m.jpg', $model->title); ?>
<?php echo CHtml::image('uploads/documenten/'.$model->documents[0]->location.'/'.$model->documents[0]->file.'_t.jpg', $model->title); ?>

<?php
	$tags = '';
	foreach($model->tags as $i => $tag)
			$tags .= $tag->name.', ';
	$tags = substr($tags, 0, -2);
?>

<?php
	if(isset($model->documents[0]->location) && isset($model->documents[0]->file) && isset($model->documents[0]->format)){
		$button = $this->widget('bootstrap.widgets.TbButton', array(
		    'type'=>'primary',
		    'label'=>'Geef document weer',
		    'size'=>'mini',
		    'url'=>'uploads/documenten/'.$model->documents[0]->location.'/'.$model->documents[0]->file.$model->documents[0]->format,
		    'htmlOptions'=>array('target'=>'_blank'),
		), true);
	}else{
		$button = '<span class="null">Niet opgegeven</span>';
	}
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
		array('label'=>'Bestand', 'value'=>$button, 'type'=>'raw'),
		array('name'=>'published', 'label'=>'Gepubliceerd', 'value'=>$model->published? "Ja":"Nee"),
	),
));
$this->widget('ext.collection.ECollection',
	array(
		'file_id'=>$model->id,
		'file_type'=>'document',
	)
);

?>

<hr />


<?php
$this->breadcrumbs=array(
	'Audioen'=>array('index'),
	$model->title,
);

$this->menu=array(
	array('label'=>'Acties','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Lijst van audio bestanden','url'=>array('index'),'icon'=>'list','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Maak audio bestand aan','icon'=>'file','url'=>array('create'),'visible'=>Yii::app()->user->checkAccess('user')),
	array('label'=>'Bewerk audio bestand','url'=>array('update','id'=>$model->id),'icon'=>'pencil','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Verwijder audio bestand','url'=>'#','icon'=>'trash','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Weet je zeker dat je dit audio bestand wilt verwijderen?'),'visible'=>Yii::app()->user->checkAccess('admin')),
	array('label'=>'Beheer audio bestand','url'=>array('admin'),'icon'=>'list-alt','visible'=>Yii::app()->user->checkAccess('admin')),
);
?>

<h1><?php echo $model->title; ?></h1>

<?php
	$tags = '';
	foreach($model->tags as $i => $tag)
			$tags .= $tag->name.', ';
	$tags = substr($tags, 0, -2);
?>

<?php
	if(isset($model->audios[0]->location) && isset($model->audios[0]->file) && isset($model->audios[0]->format)){
		$button = $this->widget('bootstrap.widgets.TbButton', array(
		    'type'=>'primary',
		    'label'=>'Speel audio bestand af',
		    'size'=>'mini',
		    'url'=>'uploads/audio/'.$model->audios[0]->location.'/'.$model->audios[0]->file.$model->audios[0]->format,
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
		'file_type'=>'audio',
	)
);

?>

<hr />

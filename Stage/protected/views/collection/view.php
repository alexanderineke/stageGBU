<?php
$this->breadcrumbs=array(
	'Collections'=>array('index'),
	$model->title,
);

$this->menu=array(
	array('label'=>'Acties','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Lijst van collecties','url'=>array('index'),'icon'=>'list','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Maak collectie aan','url'=>array('create'),'icon'=>'file','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Bewerk collectie','url'=>array('update','id'=>$model->id),'icon'=>'pencil','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Verwijder collectie','url'=>'#','icon'=>'trash','linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Weet je zeker dat je deze collectie wilt verwijderen?'),'visible'=>Yii::app()->user->checkAccess('admin')),
	array('label'=>'Beheer collectie','url'=>array('admin'),'icon'=>'list-alt','visible'=>Yii::app()->user->checkAccess('admin')),
);
?>

<div class="row">
	<?php if($model->thumb): ?>
	<div class="span3">
		<?php echo CHtml::image('uploads/afbeeldingen/'.$model->thumb->location.'/'.$model->thumb->file.$model->thumb->format, $model->title, array('class'=>'img-polaroid')); ?>
	</div>
	<?php endif; ?>
	<div class="span9">
		<h1><?php echo $model->title; ?></h1>
		<?php echo $model->description; ?>
		<small class="collection-thumb-items"><?php echo (count($model->documents)+count($model->images)+count($model->collections)); ?> items</small>
	</div>
</div>

<?php if($model->collections): ?>
	<h2>Subcollecties</h2>
	<div class="row">
	<?php foreach ($model->collections as $collection): ?>
		<article class="span4 collection-thumb">
			<div class="row">
				<?php if($collection->thumb || !Yii::app()->user->isGuest): ?>
				<div class="span1">
					<?php if($collection->thumb): echo CHtml::image('uploads/afbeeldingen/'.$collection->thumb->location.'/thumb/'.$collection->thumb->file.$collection->thumb->format, $collection->title, array('class'=>'img-polaroid collection-thumb-img')); endif; ?>
	 				<?php if(!Yii::app()->user->isGuest): ?>
						<?php echo CHtml::link("<i class=\"icon-trash icon-white\"></i>", Yii::app()->createUrl("collection/deletecollection",array("id"=>$_GET["id"], "collection"=>$collection->id)), array("class"=>"btn btn-primary")); ?>
 				<?php endif; ?>
	 			</div>
				<?php endif; ?>
				<a href="<?php echo Yii::app()->createUrl("collection/view",array("id"=>$collection->id)); ?>"  class="span3">
					<h3 class="collection-thumb-title"><?php echo $collection->title; ?></h3>
					<p><?php echo substr(strip_tags($collection->description), 0, 70); ?>...</p>
					<small class="collection-thumb-items"><?php echo (count($collection->documents)+count($collection->images)+count($collection->collections)); ?> items</small>
				</a>
			</div>
		</article>
	<?php endforeach; ?>
	</div>
<?php endif; ?>

<h3>Documenten</h3>
<?php 
$arr = array();
foreach ($model->documents as $key => $value) {
	$arr[] = $value;
}

$dataProv = new CArrayDataProvider($arr);
$this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
    'dataProvider'=>$dataProv,
	'pager' => array('class' => 'bootstrap.widgets.TbPager', 'prevPageLabel' => '&laquo;', 'nextPageLabel' => '&raquo;'),
    'columns'=>
        (!Yii::app()->user->isGuest ? 
        	array( //Ingelogd
        		array(
        			'name'=>'title', 
        			'header'=>'Titel',
        			'type' => 'raw',
        			'value' => 'CHtml::link( $data->title, Yii::app()->createUrl("document/view",array("id"=>$data->id)))',
        		),
        		array(
				    'name' => 'Acties',
				    'type' => 'raw',
				    'htmlOptions' => array(
				        'style' => 'width: 100px; text-align: center;',
				    ),
				    'value' => 'CHtml::link( "<i class=\"icon-trash icon-white\"></i>", Yii::app()->createUrl("collection/deletedocument",array("id"=>$_GET["id"], "document"=>$data->id)))',
				)
			) : 
			array( //Niet ingelogd
				array(
					'name'=>'title', 
					'header'=>'Titel',
        			'type' => 'raw',
        			'value' => 'CHtml::link( $data->title, Yii::app()->createUrl("document/view",array("id"=>$data->id)))',					
				)
			)
    	),
	)
);
?>

<h3>Afbeeldingen</h3>
<?php 
$arr = array();
foreach ($model->images as $key => $value) {
	$arr[] = $value;
}

$dataProv = new CArrayDataProvider($arr);
$this->widget('bootstrap.widgets.TbGridView', array(
    'type'=>'striped bordered condensed',
    'dataProvider'=>$dataProv,
	'pager' => array('class' => 'bootstrap.widgets.TbPager', 'prevPageLabel' => '&laquo;', 'nextPageLabel' => '&raquo;'),
    'columns'=>
        (!Yii::app()->user->isGuest ? 
        	array( //Ingelogd
        		array(
        			'name'=>'title', 
        			'header'=>'Titel',
        			'type' => 'raw',
        			'value' => 'CHtml::link( $data->title, Yii::app()->createUrl("image/view",array("id"=>$data->id)))',        			
        		),
        		array(
				    'name' => 'Acties',
				    'type' => 'raw',
				    'htmlOptions' => array(
				        'style' => 'width: 100px; text-align: center;',
				    ),
		    		'value' => 'CHtml::link( "<i class=\"icon-trash icon-white\"></i>", Yii::app()->createUrl("collection/deleteimage",array("id"=>$_GET["id"], "image"=>$data->id)))',
				)
			) : 
			array( //Niet ingelogd
				array(
					'name'=>'title', 
					'header'=>'Titel',
        			'type' => 'raw',
        			'value' => 'CHtml::link( $data->title, Yii::app()->createUrl("image/view",array("id"=>$data->id)))',					
				)
			)
    	),
	)
);
?>

<?php
$this->widget('ext.collection.ECollection',
	array(
		'file_id'=>$model->id,
		'file_type'=>'collection',
	)
);

?>




<?php
$this->breadcrumbs=array(
	'Documenten'=>array('index'),
	'Beheer',
);

$this->menu=array(
	array('label'=>'Acties','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Lijst van document','icon'=>'list','url'=>array('index'),'visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Maak documenten aan','icon'=>'file','url'=>array('create'),'visible'=>Yii::app()->user->checkAccess('user')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('document-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Beheer documenten</h1>

<p>
U kan optioneel een vergelijks symbool (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
of <b>=</b>) gebruiken in uw zoekopdracht.
</p>

<?php echo CHtml::link('Geavanceerd zoeken','#',array('class'=>'search-button btn')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model, true
)); ?>
</div><!-- search-form -->

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'document-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array( 	'name'=>'title',
				'filter'=>CHtml::activeTextField($model, 'title', 
                 array('placeholder'=>'Zoek op titel..'))
		),
		array( 	'name'=>'created_on' ),		
		array( 	'name'=>'modified_on' ),		
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
	),
)); ?>
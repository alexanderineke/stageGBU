<?php
$this->breadcrumbs=array(
	'Collecties'=>array('index'),
	'Beheer',
);

$this->menu=array(
	array('label'=>'Acties','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Maak collectie aan','url'=>array('create'),'icon'=>'file','visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Lijst van collecties','icon'=>'list','url'=>array('index'),'visible'=>Yii::app()->user->checkAccess('moderator')),
	array('label'=>'Uitgelichte collecties','icon'=>'eye-open','url'=>array('view&id=17'),'visible'=>Yii::app()->user->checkAccess('moderator')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('collection-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>


<h1>Beheer collecties</h1>

<p>
U kan optioneel een vergelijks symbool (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
of <b>=</b>) gebruiken in uw zoekopdracht.
</p>

<?php echo CHtml::link('Geavanceerd zoeken','#',array('class'=>'search-button btn')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'collection-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'title',
		'created_on',
		'modified_on',				
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
	),
)); ?>

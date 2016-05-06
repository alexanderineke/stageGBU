<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'images-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

	<p class="help-block">Velden met een <span class="required">*</span> zijn verplicht.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model,'title',array('class'=>'span5','maxlength'=>64)); ?>

	 <?php echo $form->labelEx($model,'description'); ?>		
	 <?php 
		 $this->widget('ext.redactor.ImperaviRedactorWidget', array(
		 	'name' => 'Image[description]',
		 	'value' => $model->description,
		 	'options' => array(
		 	'minHeight'=>150,
		 	'class'=>'span8',
		 	'lang'=>'nl',
		 	'buttons'=>array('formatting', '|', 'bold', 'italic', 'deleted', '|', 'unorderedlist', 'orderedlist', 'outdent', 'indent', '|', 'table', 'link', '|', 'horizontalrule')
		 ))); 
	?>

	<?=
$tags = '';
$values = [];
foreach ($model->tags as $i => $tag) {
    $tags .= $tag->name . ', ';
    $values[$i]['id'] = $tag->id;
    $values[$i]['tag'] = $tag->name;
    $tags = substr($tags, 0, -1);
}
?>
<?= $form->field($model, 'tags_previous')->hiddenInput(['value' => $tags]) ?>

<?= $form->field($model, 'year')->textInput(['class' => 'span5']) ?>

<?= $form->field($model, 'owner')->textInput(['class' => 'span5', 'maxlength' => 45]) ?>

<?= $form->field($model, 'file')->label(['Label Of file', 'minHeight' => 150, 'class' => 'span8', 'lang' => 'nl']); ?>	

<?= $form->field($model, 'published')->dropDownList($items) ?>


	<?php 
		$this->widget('ext.dropzone.EDropzone',[
		    'model' => $model,
		    'attribute' => 'file',
		    'url' => $this->createUrl('image/upload'),
		    'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
		    'options' => [	    
		    	'dictDefaultMessage' => 'Plaats hier het bestand dat u wilt uploaden',
		    	'dictFallbackMessage' => 'Uw browser wordt niet ondersteund',
		    	'dictInvalidFileType' => 'Dit bestands formaat wordt niet ondersteund. Converteer het a.u.b. naar PDF.',
		    	'dictFileTooBig' => 'Het bestand dat u probeert te uploaden is te groot.',
		    ],
		]);
	?>
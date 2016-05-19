<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'collection-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="help-block">Velden met een <span class="required">*</span> zijn verplicht.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model,'title',array('class'=>'span5','maxlength'=>64)); ?>

	 <?php echo $form->labelEx($model,'description'); ?>		
	 <?php 
		 $this->widget('ext.redactor.ImperaviRedactorWidget', array(
		 	'name' => 'Collection[description]',
		 	'value' => $model->description,
		 	'options' => array(
		 	'minHeight'=>150,
		 	'class'=>'span8',
		 	'lang'=>'nl',
		 	'buttons'=>array('formatting', '|', 'bold', 'italic', 'deleted', '|', 'unorderedlist', 'orderedlist', 'outdent', 'indent', '|', 'table', 'link', '|', 'horizontalrule')
		 ))); 
	?>

	<?php echo $form->dropDownListRow($model, 'published', array('1'=>'Ja', '0'=>'Nee')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Maak aan' : 'Bewerk',
		)); ?>
	</div>

<?php $this->endWidget(); ?>

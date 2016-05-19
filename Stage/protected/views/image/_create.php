<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'image-form',
	'action'=>$this->createUrl('image/process'),
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

	<p class="help-block">Velden met een <span class="required">*</span> zijn verplicht.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model,'title',array('class'=>'span5','maxlength'=>64)); ?>

	<?php 
		$this->widget('ext.dropzone.EDropzone', array(
		    'model' => $model,
		    'attribute' => 'file',
		    'url' => $this->createUrl('image/batchupload'),
		    'mimeTypes' => array('image/jpeg', 'image/png', 'image/gif'),
		    'options' => array(		    
		    	'dictDefaultMessage' => 'Plaats hier het bestand dat u wilt uploaden',
		    	'dictFallbackMessage' => 'Uw browser wordt niet ondersteund',
		    	'dictInvalidFileType' => 'Dit bestands formaat wordt niet ondersteund. Converteer het a.u.b. naar PDF.',
		    	'dictFileTooBig' => 'Het bestand dat u probeert te uploaden is te groot.',
		    ),
		));
	?>
	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>'Maak aan',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
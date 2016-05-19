<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'audio-form',
	'action'=>$this->createUrl('audio/process'),
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
		    'url' => $this->createUrl('audio/batchupload'),
		    'mimeTypes' => array('audio/mpeg3', 'audio/x-mpeg-3', 'audio/mpeg', 'audio/mp3'),
		    'options' => array(		    
		    	'dictDefaultMessage' => 'Plaats hier het bestand dat u wilt uploaden',
		    	'dictFallbackMessage' => 'Uw browser wordt niet ondersteund',
		    	'dictInvalidFileType' => 'Dit bestands formaat wordt niet ondersteund. Converteer het a.u.b. naar MP3.',
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
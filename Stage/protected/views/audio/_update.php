<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'audio-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

	<p class="help-block">Velden met een <span class="required">*</span> zijn verplicht.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model,'title',array('class'=>'span5','maxlength'=>64)); ?>

	 <?php echo $form->labelEx($model,'description'); ?>		
	 <?php 
		 $this->widget('ext.redactor.ImperaviRedactorWidget', array(
		 	'name' => 'Audio[description]',
		 	'value' => $model->description,
		 	'options' => array(
		 	'minHeight'=>150,
		 	'class'=>'span8',
		 	'lang'=>'nl',
		 	'buttons'=>array('formatting', '|', 'bold', 'italic', 'deleted', '|', 'unorderedlist', 'orderedlist', 'outdent', 'indent', '|', 'table', 'link', '|', 'horizontalrule')
		 ))); 
	?>

	<?php 
		$tags = '';
		$values = array();
		foreach($model->tags as $i => $tag){
			$tags .= $tag->id.',';
			$values[$i]['id'] = $tag->id;
			$values[$i]['tag'] = $tag->name;
		}
		$tags = substr($tags, 0, -1);
	?>

	<?php 
		$this->widget('ext.tagIt.ETagIt', array(
			'id' => 'Audio_tags',
		    'url' => $this->createUrl('tag/search'),
		    'options' => array(),
		    'values' => $values,
		));
	?>

	<?php echo $form->hiddenField($model,'tags_previous',array('value'=>$tags)); ?>

	<?php echo $form->textFieldRow($model,'year',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'owner',array('class'=>'span5','maxlength'=>45)); ?>

	<?php echo $form->dropDownListRow($model, 'published', array('1'=>'Ja', '0'=>'Nee')); ?>
	
	<?php 
		$this->widget('ext.dropzone.EDropzone', array(
		    'model' => $model,
		    'attribute' => 'file',
		    'url' => $this->createUrl('audio/upload'),
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
			'label'=>$model->isNewRecord ? 'Maak aan' : 'Bewaar',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
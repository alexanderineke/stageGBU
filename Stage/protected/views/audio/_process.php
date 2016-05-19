<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'audio-form',
	'action'=>$this->createUrl('audio/process'),	
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

	<?php echo $form->hiddenField($model,'included_file',array('value'=>$file['location'].'/'.$file['file'])); ?>
          
    <?php echo $form->labelEx($model,'collection'); ?>	
          
    <?php echo CHtml::dropDownList(
            'Audio[collection]',
            null,
           	$collection_list,
            array('empty'=>Yii::t('none','Geen collectie'))
        );
    ?>

	<?php echo $form->textFieldRow($model,'year',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'owner',array('class'=>'span5','maxlength'=>45)); ?>

	<?php 
	if(isset($file)){
		$button = $this->widget('bootstrap.widgets.TbButton', array(
		    'type'=>'primary',
		    'label'=>'Speel audio bestand '.$file['file'].' af',
		    'size'=>'mini',
		    'url'=>'uploads/'.$file['location'].'/'.$file['file'],
		    'htmlOptions'=>array('target'=>'_blank'),
		), true); 
	}else{
		$button = '<span class="null">Niet opgegeven</span>';
	}
	?>

	<?php echo $form->labelEx($model,'file'); ?>	
	<?php echo $button; ?>

	<?php echo $form->dropDownListRow($model, 'published', array('1'=>'Ja', '0'=>'Nee')); ?>
	
	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			//'label'=>'Volgende',
			'label'=>(sizeof(Yii::app()->user->getState('filesToProcess')) > 1 ? 'Volgende' : 'Bewaar'),
		)); ?>
	</div>

<?php $this->endWidget(); ?>
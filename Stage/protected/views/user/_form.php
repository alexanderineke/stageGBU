<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
	'id'=>'user-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="help-block">De velden met <span class="required">*</span> zijn verplicht.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model,'username',array('class'=>'span5','maxlength'=>128)); ?>

	<?php echo $form->passwordFieldRow($model,'password',array('class'=>'span5','maxlength'=>128)); ?>
	<?php echo $form->passwordFieldRow($model,'repeat_password',array('class'=>'span5','maxlength'=>128)); ?>

	<?php echo $form->textFieldRow($model,'email',array('class'=>'span5','maxlength'=>128)); ?>

	<?php echo $form->dropDownListRow($model, 'roles', array('user'=>'Gebruiker', 'moderator'=>'Moderator', 'admin'=>'Beheerder')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Maak aan' : 'Update',
		)); ?>
	</div>

<?php $this->endWidget(); ?>

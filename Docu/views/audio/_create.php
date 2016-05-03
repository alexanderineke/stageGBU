<?php
$form = ActiveForm::begin([
            'id' => 'audio-form',
            'action' => ['audio/process'],
            'enableAjaxValidation' => false,
            'method' => 'get',
            //?↓
            'options' => 'form',
                //
        ]);
?>

<p class="help-block">Velden met een <span class="required">*</span> zijn verplicht</p>

<?php echo $form->errorSummary($model); ?>

<?php echo $form->field($model, 'title')->textInput(['class' => 'span5', 'maxlength' => 64]); ?>

<?php
//Hier moet de dropzone komen
?>

<div class='form-actions'>
    <?= Html::submitButton('Maak aan', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
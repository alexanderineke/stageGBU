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

echo $form->field($model, 'picture')->widget(DropZone::className, [
    'options' => [
        'url'=>'upload',
        'maxFilesize' => '200',
        'addRemoveLinks'=>true,
    ],
    'clientEvents' => [
        'complete' => "function(file) { console.log(file) }",
        'removedfile' => "function(file) {alert(file.name + ' is removed') }",
    ],
]);

?>

<div class='form-actions'>
    <?= Html::submitButton('Maak aan', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
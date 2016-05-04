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

echo \kato\DropZone::widget([
    'options' => [
        'maxFilesize' => '200',
        'dictDefaultMessage' => 'Plaats hier het bestand dat u wilt uploaden',
        'dictFallbackMessage' => 'Uw browser wordt niet ondersteund',
        'dictInvalidFileType' => 'Dit bestands formaat wordt niet ondersteund. Converteer het a.u.b. naar MP3.',
        'dictFileTooBig' => 'Het bestand dat u probeert te uploaden is te groot.',
        'clickable' => true,
        'accept' => ['audio/mpeg3', 'audio/x-mpeg-3', 'audio/mpeg', 'audio/mp3'],
        'url' => $this->createUrl('audio/batchupload'),
    ],
    'clientEvents' => [
        'complete' => "function(file){console.log(file)}",
        'removedfile' => "function(file){alert(file.name + ' is removed')}"
    ],
]);
?>

<div class='form-actions'>
    <?= Html::submitButton('Maak aan', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
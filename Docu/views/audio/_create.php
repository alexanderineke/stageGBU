<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\dropzone\DropZone;
use yii\helpers\Url;

$form = ActiveForm::begin([
            'id' => 'audio-form',
            'action' => ['audio/process'],
            'enableAjaxValidation' => false,
            'method' => 'get',
            'options' => ['enctype' => 'multipart/form-data'],
        ]);
?>

<p class="help-block">Velden met een <span class="required">*</span> zijn verplicht</p>

<?= $form->errorSummary($model); ?>

<?= $form->field($model, 'title')->textInput(['class' => 'span5', 'maxlength' => 64]); ?>

<?php
echo DropZone::widget([
  'options' => [
  'maxFilesize' => '200',
  //'dictDefaultMessage' => 'Plaats hier het bestand dat u wilt uploaden',
  //'dictFallbackMessage' => 'Uw browser wordt niet ondersteund',
 //'dictInvalidFileType' => 'Dit bestands formaat wordt niet ondersteund. Converteer het a.u.b. naar PDF.',
 // 'dictFileTooBig' => 'Het bestand dat u probeert te uploaden is te groot.',
  //'clickable' => true,
 // 'accept' => ['image/jpeg', 'image/png', 'image/gif'],
 'url' => Url::to('audio/batchupload'),
    ],
]);
?>

<div class='form-actions'>
<?= Html::submitButton('Maak aan', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
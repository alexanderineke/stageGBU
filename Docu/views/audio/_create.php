<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use devgroup\dropzone\DropZone;

$form = ActiveForm::begin([
            'id' => 'audio-form',
            'action' => ['audio/process'],
            'enableAjaxValidation' => false,
            'method' => 'post',
            'options' => ['enctype' => 'multipart/form-data'],
        ]);
?>

<p class="help-block">Velden met een <span class="required">*</span> zijn verplicht.</p>

<?= $form->field($model, 'title')->textInput(['class' => 'span5', 'maxlength' => 64]) ?>

<?php
echo DropZone::widget([
    'name' => 'filename',
    'storedFiles' => [],
    'url' => 'index.php?r=audio/batchupload',
    'options' => [
        'maxFilesize' => '2000',
        'dictDefaultMessage' => 'Plaats hier het bestand dat u wilt uploaden',
        'dictFallbackMessage' => 'Uw browser wordt niet ondersteund',
        'dictInvalidFileType' => 'Dit bestands formaat wordt niet ondersteund. Converteer het a.u.b. naar PDF.',
        'dictFileTooBig' => 'Het bestand dat u probeert te uploaden is te groot.',
    ]
]);
?>
<div class="form-actions">
    <?= Html::submitButton('Maak aan', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
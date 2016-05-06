<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;


$form = ActiveForm::begin([
            'id' => 'document-form',
            'action' => ['document/process'],
            'enableAjaxValidation' => false,
            'method' => 'get',
            'options' => ['enctype' => 'multipart/form-data'],
        ]);
?>

<p class="help-block">Velden met een <span class="required">*</span> zijn verplicht.</p>

<?= $form->errorSummary($model); ?>

<?= $form->field($model, 'title')->textInput(['class' => 'span5', 'maxlength' => 64]); ?>

<?php
echo \kato\DropZone::widget([
    'model' => $model,
    'attribute' => 'file',
    'url' => $this->createUrl('document/upload'),
    'options' => [
        'maxFilesize' => '200',
        'dictDefaultMessage' => 'Plaats hier het bestand dat u wilt uploaden',
        'dictFallbackMessage' => 'Uw browser wordt niet ondersteund',
        'dictInvalidFileType' => 'Dit bestands formaat wordt niet ondersteund. Converteer het a.u.b. naar MP3.',
        'dictFileTooBig' => 'Het bestand dat u probeert te uploaden is te groot.',
        'clickable' => true,
        'accept' => ['application/pdf', 'application/x-pdf'],
    ],
    'clientEvents' => [
        'complete' => "function(file){console.log(file)}",
        'removedfile' => "function(file){alert(file.name + ' is removed')}"
    ],
]);
?>
<div class="form-actions">
    <?= Html::submitButton('Maak aan', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
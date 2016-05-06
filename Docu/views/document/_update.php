<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;


$form = ActiveForm::begin([
            'id' => 'documenten-form',
            'enableAjaxValidation' => false,
            'options' => ['enctype' => 'multipart/form-data'],
        ]);
?>

<p class="help-block">Velden met een <span class="required">*</span> zijn verplicht.</p>

<?= $form->errorSummary($model); ?>

<?= $form->field($model, 'title')->textInput(['class' => 'span5', 'maxlength' => 64]); ?>

<?= $form->field($model, 'description')->label('Description'); ?>

<?php
//Hier moet een ext. widget komen: ImperaviRedactorWidget.
?>

<?php
$tags = '';
$values = [];
foreach ($model->tags as $i => $tag) {
    $tags .=$tag->id . ',';
    $values[$i]['id'] = $tag->id;
    $values[$i]['tag'] = $tag->name;
}
$tags = substr($tags, 0, -1);
?>

<?php
//Hier moet een ext. widget komen: ETagIt.
?>

<?= $form->field($model, 'tags_previous')->hiddenInput(['value' => $tags]); ?>

<?= $form->field($model, 'year')->textInput(['class' => 'span5']); ?>

<?= $form->field($model, 'owner')->textInput(['class' => 'span5', 'maxlength' => 45]); ?>

<?= $form->field($model, 'published')->dropDownList(['1' => 'Ja', '0' => 'Nee']); ?>

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

<?= Html::submitButton($model->isNewRecord ? 'Maak aan' : 'Bewaar', ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end(); ?>
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use vova07\imperavi\Widget;
use devgroup\dropzone\DropZone;
use xj\tagit\Tagit;
use yii\helpers\Url;

$form = ActiveForm::begin([
            'id' => 'documenten-form',
            'enableAjaxValidation' => false,
            'options' => ['enctype' => 'multipart/form-data'],
        ]);
?>

<p class="help-block">Velden met een <span class="required">*</span> zijn verplicht.</p>

<?= $form->errorSummary($model); ?>

<?= $form->field($model, 'title')->textInput(['class' => 'span5', 'maxlength' => 64]); ?>

<?= $form->field($model, 'description')->hiddenInput(); ?>
<?php


echo Widget::widget([
    'name' => 'Document[description]',
    'value' => $model->description,
    'settings' => [
        'lang' => 'nl',
        'minHeight' => 150,
    ]
]);

$tags = '';
$values = [];
foreach ($model->tags as $i => $tag) {
    $tags .= $tag->id . ',';
  //  $values[$i] = $tag->id;
    $values[$i] = $tag->name;
}
$tags = substr($tags, 0, -1);
?>
<?= $form->field($model, 'tags_previous')->hiddenInput(['value' => $tags]); ?>
<?php

echo Tagit::widget([
    'id' => 'Document_tags',
    'name' => 'tags',
    'value' => $values,
]);
?>
<?= $form->field($model, 'year')->textInput(['class' => 'span5']); ?>

<?= $form->field($model, 'owner')->textInput(['class' => 'span5', 'maxlength' => 45]); ?>

<?= $form->field($model, 'published')->dropDownList(['1' => 'Ja', '0' => 'Nee']); ?>

<?php

echo DropZone::widget([
    'name' => 'filename',
    'storedFiles' => [],
    'url' => 'index.php?r=document/upload',
    'options' => [
        //'acceptedFiles' => ['application/pdf', 'application/x-pdf'],
        'maxFilesize' => '2000',
        'dictDefaultMessage' => 'Plaats hier het bestand dat u wilt uploaden',
        'dictFallbackMessage' => 'Uw browser wordt niet ondersteund',
        'dictInvalidFileType' => 'Dit bestands formaat wordt niet ondersteund. Converteer het a.u.b. naar PDF.',
        'dictFileTooBig' => 'Het bestand dat u probeert te uploaden is te groot.',
    ]
]);
?>

<?= Html::submitButton($model->isNewRecord ? 'Maak aan' : 'Bewaar', ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end(); ?>
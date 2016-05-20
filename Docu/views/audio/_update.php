<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin([
            'id' => 'audio-form',
            'action' => ['audio/process'],
            'enableAjaxValidation' => false,
            'method' => 'get',
            'options' => ['enctype' => 'multipart/form-data'],
        ]);
?>

<p class="help-block">Velden met een <span class="required">*</span> zijn verplicht.</p>

<?= $form->errorSummary($model); ?>

<?= $form->field($model, 'title')->textInput(['class' => 'span5', 'maxlength' => 64]); ?>

<?= $form->field($model, 'description')->textarea(); ?>


<?php
echo \vova07\imperavi\Widget::widget([
    'name' => 'redactor',
    'settings' => [
        'lang' => 'ru',
        'minHeight' => 200,
        'plugins' => [
            'clips',
            'fullscreen'
        ]
    ]
]);
//Hier moet een variant komen van ImperaviRedactorWidget
?>

<?php

$tags = "";
$values = [];
foreach ($model->tags as $i => $tag) {
    $tags .= $tag->id . ',';
    $values[$i]['id'] = $tag->id;
    $values[$i]['tag'] = $tag->name;
}
$tags = substr($tags, 0, -1);
?>

<?php

//Hier moet een variant komen van ETagIt
?>

<?= $form->field($model, 'tags_previous')->hiddenInput(['value' => $tags]); ?>

<?= $form->field($model, 'year')->textInput(['class' => 'span5']); ?>

<?= $form->field($model, 'owner')->textInput(['class' => 'span5', 'maxlength' => 45]); ?>

<?= $form->field($model, 'published')->dropDownList(['1' => 'Ja', '0' => 'Nee']); ?>

<?php

//Hier moet de dropzone widget komen
?>

<?= Html::submitButton($model->isNewRecord ? 'Maak aan' : 'Bewaar', ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end(); ?>
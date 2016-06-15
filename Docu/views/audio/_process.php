<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\imperavi\src\Widget;

$form = ActiveForm::begin([
            'id' => 'audio-form',
            'action' => ['audio/process'],
            'enableAjaxValidation' => false,
            'method' => 'post',
            'options' => ['enctype' => 'multipart/form-data'],
        ]);
?>

<p class="help-block">Velden met een <span class="required">*</span> zijn verplicht.</p>

<?= $form->errorSummary($model); ?>

<?= $form->field($model, 'title')->textInput(['class' => 'span5', 'maxlength' => 64]); ?>

<?= $form->field($model, 'description')->hiddenInput(); ?>

<?php
echo Widget::widget([
    'name' => 'Audio[description]',
    'value' => $model->description,
    'settings' => [
        'lang' => 'nl',
        'minHeight' => 150,
    ]
]);

$tags = '';
$values = array();
foreach ($model->tags as $i => $tag) {
    $tags .= $tag->id . ',';
    $values[$i]['id'] = $tag->id;
    $values[$i]['tag'] = $tag->name;
}
$tags = substr($tags, 0, -1);

// Hier moet een externe widget komen
?>

<?php // $form->field($model, 'tags_previous')->hiddenInput(['value' => $tags]) ?>

<?= $form->field($model, 'included_file')->hiddenInput(['value' => $file['location'] . '/' . $file['file']]) ?>

<?= $form->field($model, 'collection')->hiddenInput(); ?>

<?= $form->field($model, 'Audio[collection]')->dropDownList($collection_list, ['empty' => Yii::t('none', 'Geen collectie')]); ?>

<?= $form->field($model, 'year')->textInput(['class' => 'span5']) ?>

<?= $form->field($model, 'owner')->textInput(['class' => 'span5', 'maxlength' => 45]) ?>
<?php
if (isset($file)) {
    $button = Html::submitButton('Speel audio bestand ' . $file['file'] . ' af', ['@app/uploads/' . $file['location'] . '/' . $file['file']], ['class' => 'btn btn-primary btn-xs']);
} else {
    $button = '<span class="null">Niet opgegeven</span>';
}
?>

<?= $form->field($model, 'file')->hiddenInput(); ?>

<?= $button ?>

<?= $form->field($model, 'published')->dropDownList(['1' => 'Ja', '0' => 'Nee']); ?>

<div class="form-actions">
<?= Html::submitButton(sizeof(Yii::$app->session->get('filesToProcess')) > 1 ? 'Volgende' : 'Bewaar', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use vova07\imperavi\Widget;
use yii\helpers\Url;
use xj\tagit\Tagit;
use yii\web\JsExpression;

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
$values = [];
foreach ($model->tags as $i => $tag) {
    $tags .= $tag->id . ',';
     $values[$i] = $tag->name;
}
$tags = substr($tags, 0, -1);
?>
<?= $form->field($model, 'tags_previous')->hiddenInput(['value' => $tags]); ?>
<?php
echo Tagit::widget([
    'id' => 'Audio_tags',
    'name' => 'tags',
    'value' => $values,
]);
?>
<?php // $form->field($model, 'collection')->label() ?>

<?php 
//Html::dropDownList('Audio[collection]', null, $collection_list, array('empty' => Yii::t('none', 'Geen collectie')));
?>

<?= $form->field($model, 'year')->textInput(['class' => 'span5']) ?>

<?= $form->field($model, 'owner')->textInput(['class' => 'span5', 'maxlength' => 45]) ?>

<?= $form->field($model, 'included_file')->hiddenInput(['value' => $file['location'] . '/' . $file['file']]) ?>
<?php
if (isset($file)) {
    echo $button = Html::a('Speel audio bestand ' . $file['file'] . ' af', Url::to('@web') . '/uploads/' . $file['location'] . '/' . $file['file'], ['class' => 'btn btn-primary btn-xs', 'target' => '_blank']);
} else {
    echo $button = '<span class="null">Niet opgegeven</span>';
}
?>

<?= $form->field($model, 'published')->dropDownList(['1' => 'Ja', '0' => 'Nee']); ?>

<div class="form-actions">
    <?= Html::submitButton(sizeof(Yii::$app->session->get('filesToProcess')) > 1 ? 'Volgende' : 'Bewaar', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>

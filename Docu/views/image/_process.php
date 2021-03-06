<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use vova07\imperavi\Widget;
use xj\tagit\Tagit;

$form = ActiveForm::begin([
            'id' => 'images-form',
            'action' => ['image/process'],
            'enableAjaxValidation' => false,
            'method' => 'post',
            //?↓
            'options' => ['enctype' => 'multipart/form-data'],
                //
        ]);
?>

<p class="help-block">Velden met een <span class="required">*</span> zijn verplicht.</p>

<?= $form->errorSummary($model); ?>

<?= $form->field($model, 'title')->textInput(['maxlength' => 64]); ?>

<?= $form->field($model, 'description')->hiddenInput(); ?>	

<?php
echo Widget::widget([
    'name' => 'Image[description]',
    'value' => $model->description,
    'settings' => [
        'lang' => 'nl',
        'minHeight' => 150,
    ]
]);
?>

<?php
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
    'id' => 'Image_tags',
    'name' => 'tags',
    'value' => $values,
]);
?>

<?php //$form->field($model, 'collection')->label(['Collection']);  ?>	

<?php // $form->field($model, 'Image[collection]')->dropDownList($collection_list, ['empty' => Yii::t('none', 'Geen collectie')]);  ?>

<?= $form->field($model, 'year')->textInput() ?>

<?= $form->field($model, 'owner')->textInput(['maxlength' => 45]) ?>

<?= $form->field($model, 'included_file')->hiddenInput(['value' => $file['location'] . '/' . $file['file']]) ?>

<?php
if (isset($file)) {
    echo $button = Html::a('Geef afbeelding ' . $file['file'] . ' weer', Url::to('@web') . '/uploads/' . $file['location'] . '/' . $file['file'], ['class' => 'btn btn-primary btn-xs', 'target' => '_blank']);
} else {
    echo $button = '<span class="null">Niet opgegeven</span>';
}
?>	

<?= $form->field($model, 'published')->dropDownList(['1' => 'Ja', '0' => 'Nee']); ?>

<div class="form-actions">
    <?= Html::submitButton(sizeof(Yii::$app->session->get('filesToProcess')) > 1 ? 'Volgende' : 'Bewaar', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
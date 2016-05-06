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

<?= $form->field($model, 'included_file')->hiddenInput(['value' => $file['location'] . '/' . $file['file']]); ?>

<?= $form->field($model, 'collection')->label('Collection'); ?>

<?= $form->field($model, 'Document[collection]')->dropDownList($collection_list, ['empty' => Yii::t('none', 'Geen collectie')]); ?>

<?= $form->field($model, 'year')->textInput(['class' => 'span5']); ?>

<?= $form->field($model, 'owner')->textInput(['class' => 'span5', 'maxlength' => 45]); ?>

<?php
if (isset($file)) {
    $button = Html::a('Geef document ' . $file['file'] . ' weer', ['uploads/' . $file['location'] . '/' . $file['file']], ['class' => 'btn btn-primary btn-xs', 'target' => '_blank']);
} else {
    $button = '<span class="null">Niet opgegeven</span>';
}
?>

<?= $form->field($model, 'file')->label('File'); ?>

<?= $button; ?>

<?= $form->field($model, 'published')->dropDownList(['1' => 'Ja', '0' => 'Nee']); ?>

<div class="form-actions">
    <?= Html::submitButton(sizeof(Yii::app()->user->getState('filesToProcess')) > 1 ? 'Volgende' : 'Bewaar', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
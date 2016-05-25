  
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\dropzone\DropZone;
use yii\widgets\imperavi\src\Widget;

$form = ActiveForm::begin([
            'id' => 'image-form',
            'action' => ['image/process'],
            'enableAjaxValidation' => false,
            'method' => 'get',
            'options' => ['enctype' => 'multipart/form-data'],
        ]);
?>

<p class="help-block">Velden met een <span class="required">*</span> zijn verplicht.</p>

<?php echo $form->errorSummary($model); ?>

<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'description')->hiddenInput(); ?>		
<?php
// Hier moet een ImperaviRedactorWidget komen

echo Widget::widget([
    'name' => 'Image[description]',
    'value' => $model->description,
    'settings' => [
        'lang' => 'nl',
        'minHeight' => 150,
    ]
]);
?>

<?=
$tags = '';
$values = [];
foreach ($model->tags as $i => $tag) {
    $tags .= $tag->name . ', ';
    $values[$i]['id'] = $tag->id;
    $values[$i]['tag'] = $tag->name;
    $tags = substr($tags, 0, -1);
}
?>

<?php
//Hier moet een ETagIt komen
?>

<?= $form->field($model, 'tags_previous')->hiddenInput(['value' => $tags]) ?>

<?= $form->field($model, 'year')->textInput(['class' => 'span5']) ?>

<?= $form->field($model, 'owner')->textInput(['class' => 'span5', 'maxlength' => 45]) ?>

<?= $form->field($model, 'published')->dropDownList(['1' => 'Ja', '0' => 'Nee']); ?>

<?php
echo DropZone::widget([
    'options' => [
        'maxFilesize' => '2',
    ],
    'clientEvents' => [
        'complete' => "function(file){console.log(file)}",
        'removedfile' => "function(file){alert(file.name + ' is removed')}"
    ],
]);
?>


<div class="form-actions">
<?= Html::submitButton($model->isNewRecord ? 'Maak aan' : 'Bewaar', ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>
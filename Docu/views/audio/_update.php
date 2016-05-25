<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\imperavi\src\Widget;
use yii\widgets\dropzone\DropZone;

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

echo DropZone::widget([
    'options' => [
        'maxFilesize' => '2',
    ],
    'clientEvents' => [
        'complete' => "function(file){console.log(file)}",
        'removedfile' => "function(file){alert(file.name + ' is removed')}"
    ],
]);
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
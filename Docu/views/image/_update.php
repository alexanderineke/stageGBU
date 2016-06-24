  
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use devgroup\dropzone\DropZone;
use vova07\imperavi\Widget;
use xj\tagit\Tagit;

$form = ActiveForm::begin([
            'id' => 'image-form',
          //  'action' => ['image/process'],
            'enableAjaxValidation' => false,
            'method' => 'post',
            'options' => ['enctype' => 'multipart/form-data'],
        ]);
?>

<p class="help-block">Velden met een <span class="required">*</span> zijn verplicht.</p>

<?php echo $form->errorSummary($model); ?>

<?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

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
    'id' => 'Audio_tags',
    'name' => 'tags',
    'value' => $values,
]);
?>

<?= $form->field($model, 'year')->textInput(['class' => 'span5']) ?>

<?= $form->field($model, 'owner')->textInput(['class' => 'span5', 'maxlength' => 45]) ?>

<?= $form->field($model, 'published')->dropDownList(['1' => 'Ja', '0' => 'Nee']); ?>

<?php
echo DropZone::widget([
    'name' => 'filename',
    'storedFiles' => [],
    'url' => 'index.php?r=image/upload',
    'options' => [
  'maxFilesize' => '2000',
  'dictDefaultMessage' => 'Plaats hier het bestand dat u wilt uploaden',
  'dictFallbackMessage' => 'Uw browser wordt niet ondersteund',
  'dictInvalidFileType' => 'Dit bestands formaat wordt niet ondersteund. Converteer het a.u.b. naar PDF.',
  'dictFileTooBig' => 'Het bestand dat u probeert te uploaden is te groot.',
  
    ]
]);
?>

<div class="form-actions">
<?= Html::submitButton($model->isNewRecord ? 'Maak aan' : 'Bewaar', ['class' => 'btn btn-primary']) ?>
</div>
<?php ActiveForm::end(); ?>
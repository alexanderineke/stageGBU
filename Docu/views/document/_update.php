<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\imperavi\src\Widget;
use yii\widgets\dropzone\DropZone;
use yii\widgets\tagit\Tagit;
use yii\helpers\Url;
use yii\web\JsExpression;

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

//work with ActiveForm
/*
$form->field($model, 'tags')->widget(Tagit::className(), [
    'clientOptions' => [
        'tagSource' => Url::to(['tag/get-autocomplete']),
        'autocomplete' => [
            'delay' => 200,
            'minLength' => 1,
        ],
        'singleField' => true,
        'beforeTagAdded' => new JsExpression(<<<EOF
function(event, ui){
    if (!ui.duringInitialization) {
        console.log(event);
        console.log(ui);
    }
}
EOF
        ),
    ],
]);
*/
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
    $tags .=$tag->id . ',';
    $values[$i]['id'] = $tag->id;
    $values[$i]['tag'] = $tag->name;
}

$tags = substr($tags, 0, -1);

//hier moet een tagit widget komen
//work with hidden input
echo yii\helpers\Html::hiddenInput('mytag', '', ['id' => 'Document_tags']);
echo Tagit::widget([
    'renderTag' => false,
    'id' => 'Document_tags',
    'name' => 'mytag',
    'value' => $values,
        // 'clientOptions' => [
        //     'availableTags' => ['aaa', 'bbb']
        // ]
]);
?>

<?= $form->field($model, 'tags_previous')->hiddenInput(['value' => $tags]); ?>

<?= $form->field($model, 'year')->textInput(['class' => 'span5']); ?>

<?= $form->field($model, 'owner')->textInput(['class' => 'span5', 'maxlength' => 45]); ?>

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

<?= Html::submitButton($model->isNewRecord ? 'Maak aan' : 'Bewaar', ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end(); ?>
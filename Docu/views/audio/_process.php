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
$values = array();
foreach ($model->tags as $i => $tag) {
    $tags .= $tag->id . ',';
    $values[$i]['id'] = $tag->id;
    $values[$i]['tag'] = $tag->name;
}
$tags = substr($tags, 0, -1);

// Hier moet een externe widget komen
?>
<?= $form->field($model, 'tags')->widget(Tagit::className(), [
    'clientOptions' => [
        'tagSource' => Url::to(['tag/search']),
     //   'availableTags' => Url::to(['tag/search']),
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
    ?>
 <?php
//work with hidden input
echo yii\helpers\Html::hiddenInput('mytag', '', ['id' => 'myTagId']);
echo Tagit::widget([
    'renderTag' => false,
    'id' => 'myTagId',
    'name' => 'mytag',
    'value' => ['a', 'b'],
    'clientOptions' => [
        'availableTags' => ['aaa', 'bbb']
    ]
]);

//work with hidden input (input init value)
echo yii\helpers\Html::hiddenInput('mytag2', 'a,b,c,d', ['id' => 'myTagId2']);
echo Tagit::widget([
    'renderTag' => false,
    'id' => 'myTagId2',
    'name' => 'mytag2',
    'clientOptions' => [
        'availableTags' => ['aaa', 'bbb']
    ]
]);

//auto render with autocomplete
echo Tagit::widget([
    'id' => 'Audio_tags',
    'name' => 'tagswidget',
    'value' => $values,
    'clientOptions' => [
        'tagSource' => Url::to(['tag/get-autocomplete']),
        'autocomplete' => [
            'delay' => 0,
            'minLength' => 1,
        ],
    ]
]); 
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

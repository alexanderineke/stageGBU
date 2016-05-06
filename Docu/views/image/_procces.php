<?php
$form = ActiveForm::begin([
            'id' => 'images-form',
            'action' => ['image/process'],
            'enableAjaxValidation' => false,
            'method' => 'get',
            //?↓
            'options' => 'form',
                //
        ]);
?>

<p class="help-block">Velden met een <span class="required">*</span> zijn verplicht.</p>

<?php echo $form->errorSummary($model); ?>

<?php echo $form->field($model, 'title', array('class' => 'span5', 'maxlength' => 64)); ?>

<?php echo $form->labelEx($model, 'description')->label(['Label Of Description', 'minHeight' => 150, 'class' => 'span8', 'lang' => 'nl']); ?>	

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
$this->widget('ext.tagIt.ETagIt', [
    'id' => 'Image_tags',
    'url' => $this->createUrl('tag/search'),
    'options' => [],
    'values' => $values,
]);
?>

<?= $form->field($model, 'tags_previous')->hiddenInput(['value' => $tags]) ?>

<?= $form->field($model, 'included_file')->hiddenInput(['value' => $file['location'] . '/' . $file['file']]) ?>

<?= $form->field($model, 'collection')->label(['Label Of collection', 'minHeight' => 150, 'class' => 'span8', 'lang' => 'nl']); ?>	

<?= $form->field($model, 'Image[collection]')->dropDownList($items) ?>

<?= $form->field($model, 'year')->textInput(['class' => 'span5']) ?>

<?= $form->field($model, 'owner')->textInput(['class' => 'span5', 'maxlength' => 45]) ?>

<?= $form->field($model, 'file')->label(['Label Of file', 'minHeight' => 150, 'class' => 'span8', 'lang' => 'nl']); ?>	

<?= $form->field($model, 'published')->dropDownList($items) ?>

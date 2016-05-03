<?php

$form = ActiveForm::begin([
            'id' => 'audio-form',
            'action' => ['audio/process'],
            'enableAjaxValidation' => false,
            'method' => 'get',
            //?↓
            'options' => 'form',
                //
        ]);
?>

<p class="help-block">Velden met een <span class="required">*</span> zijn verplicht.</p>

<?php echo $form->errorSummary($model); ?>

<?php echo $form->field($model, 'title')->textInput(['class' => 'span5', 'maxlength' => 64]); ?>

<?php echo $form->field($model, 'description'); ?>

<?php

//Hier moet een variant komen van ImperaviRedactorWidget
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
//Hier moet een variant komen van ETagIt
?>

<?php echo $form->field($model, 'tags_previous')->hiddenInput(['value'=>$tags])->label(false);?>

<?php echo $form->field($model, 'year')->textInput(['class'=>'span5']);?>

<?php echo $form->field($model, 'owner')->textInput(['class'=>'span5','maxlength'=>45]);?>

<?php echo $form->field($model, 'published')->dropDownList(['1' => 'Ja', '0' => 'Nee']);?>

<?php
//Hier moet de dropzone widget komen
?>

<?= Html::submitButton($model->isNewRecord ? 'Maak aan' : 'Bewaar', ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end(); ?>
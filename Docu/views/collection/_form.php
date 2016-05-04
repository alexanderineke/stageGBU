<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Collection */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="collection-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['class' => 'span5', 'maxlength' => 64]) ?>

    <?= $form->field($model, 'description')->label(['Label Of Description', 'minHeight' => 150, 'class' => 'span8', 'lang' => 'nl']) ?>

    <?= $form->field($model, 'published')->dropDownList($items) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Maak aan' : 'Bewerk', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

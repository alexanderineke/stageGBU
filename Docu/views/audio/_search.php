<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Search */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="audio-search">

    <?php
    $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
    ]);
    ?>

    <?= $form->field($model, 'id')->textInput(['class' => 'span5', 'maxlength' => 10]) ?>

    <?= $form->field($model, 'user_id')->textInput(['class' => 'span5']) ?>

    <?= $form->field($model, 'title')->textInput(['class' => 'span5', 'maxlength' => 64]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6, 'cols' => 50, 'class' => 'span8']) ?>

    <?= $form->field($model, 'year')->textInput(['class' => 'span5']) ?>

    <?= $form->field($model, 'owner')->textInput(['class' => 'span5', 'maxlength' => 45]) ?>

    <?php // echo $form->field($model, 'owner') ?>

    <?php // echo $form->field($model, 'created_on') ?>

    <?php // echo $form->field($model, 'modified_on') ?>

    <?php // echo $form->field($model, 'published')  ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

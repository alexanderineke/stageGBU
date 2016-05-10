<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="document-search">

    <?php
    $form = ActiveForm::begin([
                'action' => Yii::$app->createUrl($this->route),
                'method' => 'get',
    ]);
    ?>

    <?= $form->field($model, 'id')->textInput(['class' => 'span5', 'maxlength' => 10]) ?>

    <?= $form->field($model, 'user_id')->textInput(['class' => 'span5']) ?>

    <?= $form->field($model, 'title')->textInput(['class' => 'span5', 'maxlength' => 64]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6, 'cols' => 50, 'class' => 'span8']) ?>

    <?= $form->field($model, 'year')->textInput(['class' => 'span5']) ?>

    <?= $form->field($model, 'owner')->textInput(['class' => 'span5', 'maxlength' => 45]) ?>

    <div class="form-actions">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>

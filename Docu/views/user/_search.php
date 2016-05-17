<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="user_search">

    <?php
    $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
    ]);
    ?>

    <?= $form->field($model, 'id')->textInput(['class' => 'span5']) ?>

    <?= $form->field($model, 'username')->textInput(['class' => 'span5', 'maxlength' => 128]) ?>

    <?= $form->field($model, 'email')->textInput(['class' => 'span5', 'maxlength' => 128]) ?>

    <div class="form-group">
         <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

        <?php ActiveForm::end(); ?>

</div>

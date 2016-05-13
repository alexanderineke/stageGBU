<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['class'=>'span5','maxlength' => 128]) ?>

    <?= $form->field($model, 'password')->passwordInput(['class'=>'span5','maxlength' => 128]) ?>
    
    <?= $form->field($model, 'repeat_password')->passwordInput(['class'=>'span5','maxlength' => 128]) ?>
    
    <?= $form->field($model, 'email')->textInput(['class'=>'span5','maxlength' => 128]) ?>

    <?= $form->field($model, 'roles')->dropDownList(['user'=>'Gebruiker', 'moderator'=>'Moderator', 'admin'=>'Beheerder']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

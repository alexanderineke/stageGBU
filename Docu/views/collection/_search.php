<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Search */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="collection-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id')->textInput(['class' => 'span5']) ?>

    <?= $form->field($model, 'user_id')->textInput(['class' => 'span5']) ?>

    <?= $form->field($model, 'title')->textInput(['class' => 'span5', 'maxlength'=> 64]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6, 'cols' => 50, 'class' => 'span8'])  ?>

    <?= $form->field($model, 'created_on')->textInput(['class' => 'span5']) ?>

    <?= $form->field($model, 'modified_on')->textInput(['class' => 'span5']) ?>

    <?= $form->field($model, 'published')->textInput(['class' => 'span5']) ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

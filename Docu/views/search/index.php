<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'Zoeken';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>

<?php
$form = ActiveForm::begin([
            'action' => ['search/results'],
            'method' => 'get',
            'id' => 'searchForm',
            'type' => 'search',
            'enableAjaxValidation' => false,
            'options' => ['class' => 'well'],
        ]);
?>

<?= $form->field($model, 'keyword')->textInput(['class' => 'input-medium', 'prepend' => '<i class="icon-search"></i>']) ?> ?>

<?= $form->field($model, 'checkboxes')->checkboxList(['Afbeeldingen', 'Documenten', 'Audio',]) ?>

<?= Html::submitButton('Zoek') ?>

<?php ActiveForm::end() ?>



<?php
$this->title = 'Zoeken';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>

<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', [
    'action' => ['search/results'],
    //'method'=>'get',
    'id' => 'searchForm',
    'type' => 'search',
    'htmlOptions' => ['class' => 'well'],
        ]);
?>

<?= $form->field($model, 'keyword')->textInput(['class' => 'input-medium', 'prepend' => '<i class="icon-search"></i>']) ?> ?>

<?= $form->field($model, 'checkboxes')->checkboxList(['Afbeeldingen', 'Documenten', 'Audio',]) ?>


<?php $this->widget('bootstrap.widgets.TbButton', ['buttonType' => 'submit', 'label' => 'Zoek']); ?>

<?php $this->endWidget(); ?>



<?php
$this->breadcrumbs=array(
	'Zoeken',
);

?>

<h1>Zoeken</h1>

<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'action'=>array('search/results'),
    //'method'=>'get',
    'id'=>'searchForm',
    'type'=>'search',
    'htmlOptions'=>array('class'=>'well'),
)); ?>

<?php echo $form->textFieldRow($model, 'keyword', array('class'=>'input-medium', 'prepend'=>'<i class="icon-search"></i>')); ?>


<?php echo $form->checkBoxListRow(
            $model,
            'checkboxes',
            array(
                'Afbeeldingen',
                'Documenten',
                'Audio',
            )
        ); ?>




<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>'Zoek')); ?>

<?php $this->endWidget(); ?>



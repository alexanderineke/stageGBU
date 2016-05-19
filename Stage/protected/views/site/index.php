<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;
Yii::app()->db;
?>

	<?php $this->beginWidget('bootstrap.widgets.TbHeroUnit', array(
	    'heading'=>CHtml::encode(Yii::app()->name),
	)); ?>
 
    <p>Dit is de hoofdpagina</p>
    <?php if (!empty(Yii::app()->user->roles)){?><p>Jij bent een <?=Yii::app()->user->roles?></p><?php } ?>
 
<?php $this->endWidget(); ?>

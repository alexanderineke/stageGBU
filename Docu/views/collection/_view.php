<?php
use Yii;
use yii\helpers\Html;
?>
<div class="view">
	<b><?php echo Html::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo Html::a(Html::encode($data->id),['view','id'=>$data->id]); ?>
	<br />

	<b><?php echo Html::encode($data->getAttributeLabel('user_id')); ?>:</b>
	<?php echo Html::encode($data->user_id); ?>
	<br />

	<b><?php echo Html::encode($data->getAttributeLabel('title')); ?>:</b>
	<?php echo Html::encode($data->title); ?>
	<br />

	<b><?php echo Html::encode($data->getAttributeLabel('description')); ?>:</b>
	<?php echo Html::encode($data->description); ?>
	<br />

	<b><?php echo Html::encode($data->getAttributeLabel('created_on')); ?>:</b>
	<?php echo Html::encode($data->created_on); ?>
	<br />

	<b><?php echo Html::encode($data->getAttributeLabel('modified_on')); ?>:</b>
	<?php echo Html::encode($data->modified_on); ?>
	<br />

	<b><?php echo Html::encode($data->getAttributeLabel('published')); ?>:</b>
	<?php echo Html::encode($data->published); ?>
	<br />
</div>
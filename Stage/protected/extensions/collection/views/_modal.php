<?php $this->beginWidget('bootstrap.widgets.TbModal', array('id'=>'collection_modal')); ?>
 
<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4>Voeg toe aan collectie</h4>
</div>
<?php 

if($list){
    echo CHtml::beginForm(Yii::app()->createUrl('collection/add'), 'post'); ?>
    <div class="modal-body">
        <?php
            echo CHtml::dropDownList(
                'collection',
                null,
                $list
            );
            echo CHtml::hiddenField('id',$id); 
            echo CHtml::hiddenField('type',$type); 
        ?>        
    </div>
     
    <div class="modal-footer">
        <?php $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'=>'submit',
            'type'=>'primary',
            'label'=>'Voeg toe',
        )); ?>
        <?php $this->widget('bootstrap.widgets.TbButton', array(
            'label'=>'Sluiten',
            'url'=>'#',
            'htmlOptions'=>array('data-dismiss'=>'modal'),
        )); 
        ?>
    </div>
     
    <?php 
        echo CHtml::endForm();
}else{
?>
    <div class="modal-body">
        <p>Je hebt nog geen collecties, klik <a href="<?php echo Yii::app()->createUrl('collection/create'); ?>">hier</a> om er één aan te maken.</p>
    </div>
     
    <div class="modal-footer">
        <?php $this->widget('bootstrap.widgets.TbButton', array(
            'label'=>'Sluiten',
            'url'=>'#',
            'htmlOptions'=>array('data-dismiss'=>'modal'),
        )); 
        ?>
    </div>
<?php
}
    $this->endWidget(); 
?>
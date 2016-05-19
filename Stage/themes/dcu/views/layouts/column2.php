<?php /* @var $this Controller */ ?>
<?php $this->beginContent('//layouts/main'); ?>

<div class="row">
    <?php if (Yii::app()->user->checkAccess('moderator')){ ?>
        <div class="span9">
            <div id="content">
                <?php echo $content; ?>
            </div>
        </div>
        <div class="span3">
            <div id="sidebar">
            <?php
                $this->widget('bootstrap.widgets.TbMenu', array(
                    'type'=>'list',
                    'items'=>$this->menu,
                    'htmlOptions'=>array('class'=>'well'),
                ));
            ?>
            </div>
        </div>
    <?php }else{ ?>
        <div class="span12">
            <div id="content">
                <?php echo $content; ?>
            </div>
        </div>
    <?php } ?>
</div>

<?php $this->endContent(); ?>
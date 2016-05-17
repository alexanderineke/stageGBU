<?php
$this->beginContent('@app/views/layouts/main.php');

use yii\widgets\Breadcrumbs;
use yii\widgets\Menu;
?>

<div class="container">
    <div class="row-fluid" id="content-wrap">
        <div class="span9">
            <?php // if (!empty($this->breadcrumbs)) { ?>
            <div id="breadcrumbs">
                <?=
                Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ])
                ?>
            </div><!-- breadcrumbs -->
            <?php //}  ?> 

            <div id="content">

                <?= $content ?>
            </div><!-- content -->
        </div>
        <div class="span3">
            <div id="sidebar">

                <?=
                Menu::widget([
                    'items' => $this->menu,
                ]);
//$this->widget('bootstrap.widgets.TbMenu', array('type' => 'list', 'items' => $this->menu)); 
                ?>
            </div><!-- sidebar -->
        </div>
    </div>
</div>
<?php $this->endContent(); ?>


<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Menu;
/* @var $this yii\web\View */
/* @var $model app\models\Collection */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Collections', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Menu::widget([
    'items'=>[
   	['label'=>'Acties','visible'=>Yii::$app->user->getIdentity('moderator')],
	['label'=>'Lijst van collecties','url'=>['index'],'icon'=>'list','visible'=>Yii::$app->user->getIdentity('moderator')],
	['label'=>'Maak collectie aan','url'=>['create'],'icon'=>'file','visible'=>Yii::$app->user->getIdentity('moderator')],
        ['label'=>'Bewerk collectie','url'=>['update','id'=>$model->id],'icon'=>'pencil','visible'=>Yii::$app->user->getIdentity('moderator')],
        ['label'=>'Verwijder collectie','url'=>'#','icon'=>'trash','linkOptions'=>['submit'=>['delete','id'=>$model->id],'confirm'=>'Weet je zeker dat je deze collectie wilt verwijderen?'],'visible'=>Yii::$app->user->getIdentity('admin')],
        ['label'=>'Beheer collectie','url'=>['admin'],'icon'=>'list-alt','visible'=>Yii::$app->user->getIdentity('admin')],
        
]]);


?>

<div class="collection-view">

    
    <div class="row">
	<?php if($model->thumb){ ?>
	<div class="span3">
		<?php echo CHtml::image('uploads/afbeeldingen/'.$model->thumb->location.'/'.$model->thumb->file.$model->thumb->format, $model->title, ['class'=>'img-polaroid']); ?>
	</div>
        <?php } ?>
	<div class="span9">
		<h1><?= Html::encode($this->title) ?></h1>
		<?php echo $model->description; ?>
		<small class="collection-thumb-items"><?php echo (count($model->documents)+count($model->images)+count($model->collections)); ?> items</small>
	</div>
</div>
    

    <?php if ($model->collections) { ?>
        <h2>Subcollecties</h2>
        <div class="row">
            <?php foreach ($model->collections as $collection) { ?>
                <article class="span4 collection-thumb">
                    <div class="row">
                        <?php if ($collection->thumb || !Yii::$app->user->isGuest) { ?>
                            <div class="span1">
                                <?php
                                if ($collection->thumb) {
                                    echo CHtml::image('uploads/afbeeldingen/' . $collection->thumb->location . '/thumb/' . $collection->thumb->file . $collection->thumb->format, $collection->title, ['class' => 'img-polaroid collection-thumb-img']);
                                }
                                ?>
                                <?php if (!Yii::$app->user->isGuest) { ?>
                                    <?php echo CHtml::link("<i class=\"icon-trash icon-white\"></i>", Yii::$app->createUrl("collection/deletecollection", ["id" => $_GET["id"], "collection" => $collection->id]), ["class" => "btn btn-primary"]); ?>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <a href="<?php echo Yii::$app->createUrl("collection/view", ["id" => $collection->id]); ?>"  class="span3">
                            <h3 class="collection-thumb-title"><?php echo $collection->title; ?></h3>
                            <p><?php echo substr(strip_tags($collection->description), 0, 70); ?>...</p>
                            <small class="collection-thumb-items"><?php echo (count($collection->documents) + count($collection->images) + count($collection->collections)); ?> items</small>
                        </a>
                    </div>
                </article>
            <?php } ?>
        </div>
    <?php } ?>
        
<h3>Documenten</h3>
<?php
$arr = [];
foreach ($model->documents as $key => $value) {
    $arr[] = $value;
}

$dataProv = new CArrayDataProvider($arr);
$this->widget('bootstrap.widgets.TbGridView', [
    'type' => 'striped bordered condensed',
    'dataProvider' => $dataProv,
    'pager' => ['class' => 'bootstrap.widgets.TbPager', 'prevPageLabel' => '&laquo;', 'nextPageLabel' => '&raquo;'],
    'columns' =>
    (!Yii::$app->user->isGuest ?
            [ //Ingelogd
        [
            'name' => 'title',
            'header' => 'Titel',
            'type' => 'raw',
            'value' => 'CHtml::link( $data->title, Yii::$app->createUrl("document/view",array("id"=>$data->id)))',
        ],
        [
            'name' => 'Acties',
            'type' => 'raw',
            'htmlOptions' => [
                'style' => 'width: 100px; text-align: center;',
            ],
            'value' => 'CHtml::link( "<i class=\"icon-trash icon-white\"></i>", Yii::$app->createUrl("collection/deletedocument",array("id"=>$_GET["id"], "document"=>$data->id)))',
        ]
            ] :
            [ //Niet ingelogd
        [
            'name' => 'title',
            'header' => 'Titel',
            'type' => 'raw',
            'value' => 'CHtml::link( $data->title, Yii::$app->createUrl("document/view",array("id"=>$data->id)))',
        ]
            ]
    ),
        ]
);
?>
    
<h3>Afbeeldingen</h3>
<?php
$arr = [];
foreach ($model->images as $key => $value) {
    $arr[] = $value;
}

$dataProv = new CArrayDataProvider($arr);
$this->widget('bootstrap.widgets.TbGridView', [
    'type' => 'striped bordered condensed',
    'dataProvider' => $dataProv,
    'pager' => ['class' => 'bootstrap.widgets.TbPager', 'prevPageLabel' => '&laquo;', 'nextPageLabel' => '&raquo;'],
    'columns' =>
    (!Yii::$app->user->isGuest ?
            [ //Ingelogd
        [
            'name' => 'title',
            'header' => 'Titel',
            'type' => 'raw',
            'value' => 'CHtml::link( $data->title, Yii::$app->createUrl("image/view",array("id"=>$data->id)))',
        ],
        [
            'name' => 'Acties',
            'type' => 'raw',
            'htmlOptions' => [
                'style' => 'width: 100px; text-align: center;',
            ],
            'value' => 'CHtml::link( "<i class=\"icon-trash icon-white\"></i>", Yii::$app->createUrl("collection/deleteimage",array("id"=>$_GET["id"], "image"=>$data->id)))',
        ]
    ] :
            [ //Niet ingelogd
        [
            'name' => 'title',
            'header' => 'Titel',
            'type' => 'raw',
            'value' => 'CHtml::link( $data->title, Yii::$app->createUrl("image/view",array("id"=>$data->id)))',
        ]
    ]
    ),
        ]
);
?>
<?php /*
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user_id',
            'title',
            'description:ntext',
            'created_on',
            'modified_on',
            'published',
        ],
    ]) ?>
*/ ?>

</div>

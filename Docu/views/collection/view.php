<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Menu;
use yii\helpers\Url;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use app\components\ECollection;

/* @var $this yii\web\View */
/* @var $model app\models\Collection */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Collections', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

echo Menu::widget([
    'items' => [
        ['label' => 'Acties', 'visible' => !Yii::$app->user->isGuest],
        ['label' => 'Lijst van collecties', 'url' => ['index'], 'icon' => 'list', 'visible' => !Yii::$app->user->isGuest],
        ['label' => 'Maak collectie aan', 'url' => ['create'], 'icon' => 'file', 'visible' => !Yii::$app->user->isGuest],
        ['label' => 'Bewerk collectie', 'url' => ['update', 'id' => $model->id], 'icon' => 'pencil', 'visible' => !Yii::$app->user->isGuest],
]]);
?>

<div class="collection-view">


    <div class="row">
        <?php if ($model->thumb) { ?>
            <div class="span3">
                <?= Html::img('uploads/afbeeldingen/' . $model->thumb->location . '/' . $model->thumb->file . $model->thumb->format, ['class' => 'img-polaroid']); ?>
            </div>
        <?php } ?>
        <div class="span9">
            <h1><?= Html::encode($this->title) ?></h1>
            <?php echo $model->description; ?>
            <small class="collection-thumb-items"><?php echo (count($model->documents) + count($model->images) + count($model->collections)); ?> items</small>
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
                                    echo Html::img('uploads/afbeeldingen/' . $collection->thumb->location . '/thumb/' . $collection->thumb->file . $collection->thumb->format, ['class' => 'img-polaroid collection-thumb-img']);
                                }
                                ?>
                                <?php if (!Yii::$app->user->isGuest) { ?>
                                    <?php echo Html::a("<i class=\"icon-trash icon-white\"></i>", Url::to("@web/index.php?r=collection%2Fdeletecollection&id=" . $_GET["id"] . "&collection=" . $collection->id), ["class" => "btn btn-primary"]); ?>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <a href="<?php echo Url::to("@web/index.php?r=collection%2Fview&id=" . $collection->id); ?>"  class="span3">
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
    $dataProv = new ArrayDataProvider([
        'allModels' => $model->documents,
        'sort' => [
            'attributes' => ['id', 'title'],
        ],
        'pagination' => [
            'pageSize' => 25,
        ],
    ]);

    echo GridView::widget([
        'dataProvider' => $dataProv,
        'pager' => [
            'prevPageLabel' => '&laquo;',
            'nextPageLabel' => '&raquo;',
        ],
        'columns' => (!Yii::$app->user->isGuest ?
                [ //Ingelogd
            [
                'header' => 'Titel',
                'format' => 'raw',
                'value' => function($data) {
                    return Html::a($data->title, Url::to('@web/index.php?r=document%2Fview&id=' . $data->id));
                },
            ],
            [
                'header' => 'Acties',
                'format' => 'raw',
                'contentOptions' => [
                    'style' => 'width: 100px; text-align: center;',
                ],
                'value' => function($data) {
            return Html::a("<i class=\"icon-trash icon-white\"></i>", Url::to('collection/deletedocument', ['id' => $_GET['id'], 'document' => $data->id]));
        },
            ]
                ] :
                [ //Niet ingelogd
            [
                'header' => 'Titel',
                'format' => 'raw',
                'value' => function($data) {
                    return Html::a($data->title, Url::to('@web/index.php?r=document%2Fview&id=' . $data->id));
                },
            ]
                ]
        ),
    ]);
    ?>            
    <h3>Afbeeldingen</h3>
    <?php
    $dataProv = new ArrayDataProvider([
        'allModels' => $model->images,
        'sort' => [
            'attributes' => ['id', 'title'],
        ],
        'pagination' => [
            'pageSize' => 25,
        ],
    ]);

    echo GridView::widget([
        'dataProvider' => $dataProv,
        'pager' => [
            'prevPageLabel' => '&laquo;',
            'nextPageLabel' => '&raquo;',
        ],
        'columns' => (!Yii::$app->user->isGuest ?
                [ //Ingelogd
            [
                'header' => 'Titel',
                'format' => 'raw',
                'value' => function($data) {
                    return Html::a($data->title, Url::to('@web/index.php?r=image%2Fview&id=' . $data->id));
                },
            ],
            [
                'header' => 'Acties',
                'format' => 'raw',
                'contentOptions' => [
                    'style' => 'width: 100px; text-align: center;',
                ],
                'value' => function($data) {
            return Html::a("<i class=\"icon-trash icon-white\"></i>", Url::to('collection/deleteimage', ['id' => $_GET['id'], 'image' => $data->id]));
        },
            ]
                ] :
                [ //Niet ingelogd
            [
                'header' => 'Titel',
                'format' => 'raw',
                'value' => function($data) {
                    return Html::a($data->title, Url::to('@web/index.php?r=image%2Fview&id=' . $data->id));
                }
            ]
                ]
        ),
    ]);
    ?>

    <h3>Audio bestanden</h3>
    <?php
    $dataProv = new ArrayDataProvider([
        'allModels' => $model->audios,
        'sort' => [
            'attributes' => ['id', 'title'],
        ],
        'pagination' => [
            'pageSize' => 25,
        ],
    ]);

    echo GridView::widget([
        'dataProvider' => $dataProv,
        'pager' => [
            'prevPageLabel' => '&laquo;',
            'nextPageLabel' => '&raquo;',
        ],
        'columns' => (!Yii::$app->user->isGuest ?
                [ //Ingelogd
            [
                'header' => 'Titel',
                'format' => 'raw',
                'value' => function($data) {
                    return Html::a($data->title, Url::to('@web/index.php?r=audio%2Fview&id=' . $data->id));
                },
            ],
            [
                'header' => 'Acties',
                'format' => 'raw',
                'contentOptions' => [
                    'style' => 'width: 100px; text-align: center;',
                ],
                'value' => function($data) {
            return Html::a("<i class=\"icon-trash icon-white\"></i>", Url::to('collection/deleteaudio', ['id' => $_GET['id'], 'audio' => $data->id]));
        },
            ]
                ] :
                [ //Niet ingelogd
            [
                'header' => 'Titel',
                'format' => 'raw',
                'value' => function($data) {
                    return Html::a($data->title, Url::to('@web/index.php?r=audio%2Fview&id=' . $data->id));
                }
            ]
                ]
        ),
    ]);
    echo ECollection::widget([
        'file_id' => $model->id,
        'file_type' => 'collection',
    ]);
    ?>
</div>

<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Menu;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Collecties';
$this->params['breadcrumbs'][] = $this->title;
echo Menu::widget([
    'items' => [
        ['label' => 'Acties', 'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Maak collectie aan', 'url' => ['create'], 'icon' => 'file', 'visible' => !Yii::$app->user->isGuest],
        ['label' => 'Uitgelichte collecties', 'icon' => 'eye-open', 'url' => Url::to("@web/index.php?r=collection%2Fview&id=17"), 'visible' => !Yii::$app->user->isGuest],
]]);
?>
<div class="collection-index">

    
<h1><?= Html::encode($this->title) ?></h1>

<div class="search-form" style="display: none">
    <?php $this->render('_search', ['model' => $model], true); ?>
</div>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'title',
        'created_on',
        'modified_on',
       ['class' => 'yii\grid\ActionColumn'],
    ],
])
?>
</div>

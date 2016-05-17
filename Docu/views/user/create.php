<?php

use yii\helpers\Html;
use yii\widgets\Menu;
/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = 'Maak Gebruiker aan';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

<<<<<<< HEAD
echo Menu::widget([
    'items' =>[
=======
// nog niet zeker correct
$this->params['menu'][] = [
>>>>>>> origin/master
   	['label'=>'Acties','visible'=>Yii::$app->user->getIdentity('moderator')],
	['label'=>'Lijst van gebruikers','url'=>['index'],'icon'=>'list','visible'=>Yii::$app->user->getIdentity('moderator')],
	['label'=>'Beheer gebruikers','url'=>['admin'],'icon'=>'list-alt','visible'=>Yii::$app->user->getIdentity('admin')],
]]);

?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>

</div>

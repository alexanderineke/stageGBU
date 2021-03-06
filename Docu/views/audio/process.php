<?php
use yii\widgets\Menu;
$this->title = 'Verwerken';
$this->params['breadcrumbs'][] = ['label' => 'Audio', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Meerdere aanmaken', 'url' => []];
$this->params['breadcrumbs'][] = $this->title;

echo Menu::widget([
    'items' => [
        ['label' => 'Acties', 'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Lijst van audio bestanden', 'url' => ['index'], 'icon' => 'list', 'visible' => Yii::$app->user->getIdentity('moderator')],
        ['label' => 'Beheer audio bestanden', 'url' => ['admin'], 'icon' => 'list-alt', 'visible' => Yii::$app->user->getIdentity('admin')],
    ],
]);
?>

<h1>Verwerk audio <?php echo $file['file']; ?></h1>
<?php echo $this->render('_process', ['model'=>$model,'file'=>$file]); ?>
<?php

namespace app\components;

use Yii;
use yii\bootstrap\Button;
use yii\helpers\Html;
use yii\base\Widget;
use app\models\Collection;
use yii\helpers\ArrayHelper;

class ECollection extends Widget {

    /**
     * @var string Type of file. Example: image or documentent
     */
    public $file_type = false;

    /**
     * @var int The id of the file being added to the collection
     */
    public $file_id = false;

    public function run() {
        if (!Yii::$app->user->isGuest) {

            echo Html::a('Voeg toe aan collectie', '#', ['data-toggle' => 'modal',
                'data-target' => '#collection_modal', 'class' => 'btn btn-primary']);

            $list = ArrayHelper::map(Collection::find()
                                    ->where(['user_id' => \Yii::$app->user->id])
                                    ->andWhere(['published' => 1])
                                    ->orWhere(['id' => 17])
                                    ->all(), 'id', 'title'
            );

            return $this->render('_modal.php', array(
                        'type' => $this->file_type,
                        'id' => $this->file_id,
                        'list' => $list,
            ));
        }
    }

}

?>
<?php
use yii\helpers\Html;

foreach ($model->images as $image) {
    $smallImage = Yii::getAlias("@web"). "/uploads/afbeeldingen/" . $image->location . "/thumb/" . $image->file . $image->format;
    $imageLocation = Yii::getAlias("@web"). "/uploads/afbeeldingen/" . $image->location . "/" . $image->file . $image->format;
    echo '<li>';
    echo Html::a('<img src="' . $smallImage . '" alt="' . htmlspecialchars($model->title) . '"/>', ['image/view', 'id' => $model->id], ['data-largesrc' => $imageLocation,
        'data-title' => htmlspecialchars($model->title),
        'data-description' => htmlspecialchars($model->description)
            ]
    );
    echo "</li>";
}
?>


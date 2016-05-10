<?php

foreach ($data->images as $image) {
    $smallImage = Yii::app()->request->baseUrl . "/uploads/afbeeldingen/" . $image->location . "/thumb/" . $image->file . $image->format;
    $imageLocation = Yii::app()->request->baseUrl . "/uploads/afbeeldingen/" . $image->location . "/" . $image->file . $image->format;
    echo '<li>';
    echo CHtml::link('<img src="' . $smallImage . '" alt="' . htmlspecialchars($data->title) . '"/>', ['image/view', 'id' => $data->id], ['data-largesrc' => $imageLocation,
        'data-title' => htmlspecialchars($data->title),
        'data-description' => htmlspecialchars($data->description)
            ]
    );
    echo "</li>";
}
?>

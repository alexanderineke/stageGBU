<?php

foreach ($model->documents as $document) {
    $documentThumb = Yii::getAlias("@web") . "/uploads/documenten/" . $document->location . "/" . $document->file . '_m.jpg';
    $documentThumbFull = Yii::getAlias("@web") . "/uploads/documenten/" . $document->location . "/" . $document->file . '_b.jpg';
    echo '<a href="' . $documentThumbFull . '" class="swipebox" data-itemid="' . htmlspecialchars($model->id) . '"  title="' . htmlspecialchars($model->title) . '"><img src="' . $documentThumb . '" alt="' . htmlspecialchars($model->title) . '"/></a>';
}
?>

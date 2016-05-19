<?php
	foreach ($data->documents as $document) {
	    $documentThumb = Yii::app()->request->baseUrl . "/uploads/documenten/" . $document->location . "/" .$document->file .'_m.jpg';
	    $documentThumbFull = Yii::app()->request->baseUrl . "/uploads/documenten/" . $document->location . "/" . $document->file .'_b.jpg';
	    echo '<a href="'.$documentThumbFull.'" class="swipebox" data-itemid="'.htmlspecialchars($data->id).'"  title="'.htmlspecialchars($data->title).'"><img src="'.$documentThumb.'" alt="'.htmlspecialchars($data->title).'"/></a>';
	}
?>

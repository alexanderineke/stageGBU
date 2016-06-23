<?php
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Button;
?>
<?php Modal::begin(['id' => 'collection_modal']); ?>

<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h4>Voeg toe aan collectie</h4>
</div>
<?php
if ($list) {
    echo Html::beginForm(['collection/add'], 'post');
    ?>
    <div class="modal-body">
        <?php

        echo Html::dropDownList('collection', null, $list);
        echo Html::hiddenInput('id', $id);
        echo Html::hiddenInput('type', $type);
        ?>        
    </div>

    <div class="modal-footer">
        <?= Html::submitButton('Voeg Toe', ['class' => 'btn btn-primary']); ?>
        <?= Html::a('Sluiten', '#', ['data-dismiss' => 'modal']); ?>
    </div>

    <?php
    echo Html::endForm();
}  else {
    ?>
    <div class="modal-body">
        <p>Je hebt nog geen collecties, klik <a href="<?php echo Url::to(['collection/create']); ?>">hier</a> om er één aan te maken.</p>
    </div>

    <div class="modal-footer">
        <?= Html::a('Sluiten', '#', ['data-dismiss' => 'modal']); ?>
    </div>
    <?php
}
Modal::end();
?>
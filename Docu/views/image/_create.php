<?php
$form = ActiveForm::begin([
            'id' => 'image-form',
            'action' => ['image/process'],
            'enableAjaxValidation' => false,
            'method' => 'get',
            //?↓
            'options' => 'form',
                //
        ]);
?>

<p class="help-block">Velden met een <span class="required">*</span> zijn verplicht.</p>

<?= $form->field($model, 'title')->textInput(['class' => 'span5', 'maxlength' => 64]) ?>

<?php
echo \kato\DropZone::widget([
    'options' => [
        'maxFilesize' => '200',
        'dictDefaultMessage' => 'Plaats hier het bestand dat u wilt uploaden',
        'dictFallbackMessage' => 'Uw browser wordt niet ondersteund',
        'dictInvalidFileType' => 'Dit bestands formaat wordt niet ondersteund. Converteer het a.u.b. naar PDF.',
        'dictFileTooBig' => 'Het bestand dat u probeert te uploaden is te groot.',
        'clickable' => true,
        'accept' => ['audio/mpeg3', 'audio/x-mpeg-3', 'audio/mpeg', 'audio/mp3'],
        'url' => $this->createUrl('audio/batchupload'),
    ]
]);
?>
<div class="form-actions">
    <?php
    $this->widget('bootstrap.widgets.TbButton', [
        'buttonType' => 'submit',
        'type' => 'primary',
        'label' => 'Maak aan',
    ]);
    ?>
</div>

<?php $this->endWidget(); ?>
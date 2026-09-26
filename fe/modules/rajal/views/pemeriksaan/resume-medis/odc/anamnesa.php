<?php
use yii\helpers\Html;
?>
<div class="form-group row" id="anamnesa-row">
    <h5 style="margin-left: 10px;">Anamnesis</h5>
    <div class="col-md-6">
        <?= $form->field($model, 'keluhan_utama')->textArea(['rows' => 5])->label($model->getAttributeLabel('keluhan_utama'),['class' => 'text-bold']); ?>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <?= $form->field($model, 'riwayat_penyakit_dahulu')->textArea(['rows' => 5])->label($model->getAttributeLabel('riwayat_penyakit_dahulu'),['class' => 'text-bold']); ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'pemeriksaan_fisik')->textArea(['rows' => 5])->label($model->getAttributeLabel('pemeriksaan_fisik'),['class' => 'text-bold']); ?>
    </div>
</div>
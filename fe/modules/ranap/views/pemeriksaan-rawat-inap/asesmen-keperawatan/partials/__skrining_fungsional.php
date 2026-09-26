<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$inputOptions = [
    'class' => 'form-control input-sm doco-number skor-input nullable',
    'style' => 'width:80px',
    'max' => 99,
    'maxlength' => 2,
];
?>
<style>
    .popover {
    max-width: 500px;   /* atur lebar popover */
    width: 500px;       /* bisa fix width kalau perlu */
    z-index: 10;      /* biar di atas tab/menu */
}
.popover-body {
    white-space: normal; /* biar teks panjang bisa wrap */
    text-align: left;
}

</style>
<div class="row form-row">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">Skrining Fungsional</h5>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-6">
                <?= $form->field($model, 'personal_hygiene')->textInput($inputOptions) ?>
                    <?= $form->field($model, 'mandi')->textInput($inputOptions) ?>
                    <?= $form->field($model, 'makan')->textInput($inputOptions) ?>
                    <?= $form->field($model, 'toileting')->textInput($inputOptions) ?>
                    <?= $form->field($model, 'menaiki_tangga')->textInput($inputOptions) ?>
                </div>
                <div class="col-sm-6">
                <?= $form->field($model, 'memakai_pakaian')->textInput($inputOptions) ?>
                    <?= $form->field($model, 'kontrol_bab')->textInput($inputOptions) ?>
                    <?= $form->field($model, 'kontrol_bak')->textInput($inputOptions) ?>
                    <?= $form->field($model, 'ambulasi_kursi_roda')->textInput($inputOptions) ?>
                    <?= $form->field($model, 'transfer_kursi_tempat_tidur')->textInput($inputOptions) ?>
                </div>
            </div>

            <hr/>

            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'total_skor')->textInput([
                        'readonly' => true,
                        'id' => 'total-skor',
                        'class' => 'form-control input-sm nullable',
                        'style' => 'width:120px'
                    ])->label(Yii::t('fe', 'Total Skor') . ' <i class="fa fa-info-circle"
                            data-toggle="popover"
                            data-html="true"
                            data-trigger="hover focus"
                            data-placement="right"
                            data-content="
                            Penentuan Kategori:<br>
                            • Skor 0 - 24 : Ketergantungan Total<br>
                            • Skor 25 - 49 : Ketergantungan Berat<br>
                            • Skor 50 - 74 : Ketergantungan Sedang<br>
                            • Skor 75 - 90 : Ketergantungan Ringan<br>
                            • Skor 91 - 99 : Ketergantungan Minimal<br><br>
                            Catatan:<br>
                            • Ketergantungan Sedang, Berat dan Total : Laporkan ke DPJP untuk konsultasi dengan Dokter Rehabilitasi Medis.<br>
                            • Ketergantungan Minimal dan Ringan : Evaluasi setiap 2 hari atau bila ada perubahan faktor ketergantungan.
                            "
                    style="cursor:pointer"></i>') ?>
                    <?= $form->field($model, 'kategori')->textInput([
                        'readonly' => true,
                        'id' => 'kategori',
                        'class' => 'form-control input-sm nullable',
                    ]) ?>
                    <?= $form->field($model, 'catatan')->textarea([
                        'readonly' => true,
                        'rows' => 2,
                        'id' => 'catatan',
                        'class' => 'form-control input-sm nullable',
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>

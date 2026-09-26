<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;

?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>

<div class="modal-body">
    <h5 class="panel-title text-center mb-10">Apa tujuan kunjungan ini?</h5>

    <?php
    $form = ActiveForm::begin([
        'id' => 'tujuan-kunjungan-form',
        'type' => ActiveForm::TYPE_VERTICAL,
        'enableAjaxValidation'=>false, 
        'enableClientValidation'=>false,
        'action' => '',    
        'formConfig' => [
            'labelSpan' => 3, 
            'deviceSize' => ActiveForm::SIZE_SMALL
        ],
    ]);
    ?>

    <?= $form->field($model, 'tujuanKunj')
        ->dropDownList([
                '1'=>Yii::t('fe', 'Prosedur'),
                '2'=>Yii::t('fe', 'Konsul Dokter'),
            ],
            [
                'id'=>'tujuan_kunjungan_1',
                'class'=>'select2',
                'prompt'=>'— PILIH —',
            ]
        )->label(false);
    ?>
</div>

<div class="modal-footer">
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn btn-simpan-tujuan bg-teal btn-sm btn-save']); ?>
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
</div>

<?php 
$this->registerJs('
    $(".btn-simpan-tujuan").on("click", function(event) {
        event.preventDefault();
        var tujuan_kunjungan = $("#tujuan_kunjungan_1").val();
        var url;

        if (tujuan_kunjungan != 0 && tujuan_kunjungan != null || tujuan_kunjungan != "" && tujuan_kunjungan != "undefined") {
            $("#tujuan_kunjungan").val(tujuan_kunjungan);

            if (tujuan_kunjungan == 1) {
                url = "/pendaftaran/daftar-rajal/tujuan-prosedur-bpjs";
            } else if (tujuan_kunjungan == 2) {
                url = "/pendaftaran/daftar-rajal/assesment-pelayanan-bpjs";
            }
            
            modalTujuanKunjunganBpjs(url);
        } else {
            docoNotification("warning", "Peringatan!", "Tujuan kunjungan belum dipilih");
            return false;
        }
    });
', View::POS_END);
?>

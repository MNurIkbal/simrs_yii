<?php
use yii\web\View;

$classForm = 'form-control input-sm';
?>

<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">L. Identifikasi Kebutuhan Privasi Pasien</h5>
        </div>
        <div class="panel-body">
            <div class="row">
                <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">
                    Jenis kebutuhan privasi yang diharapkan pasien pada saat wawancara klinis, pemeriksaan,
                    prosedur/tindakan, pengobatan dan transportasi.
                </p>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'privasi_lawan_jenis')
                        ->label(Yii::t('fe', '1. Privasi Terhadap Lawan Jenis'))
                        ->radioList(['1' => 'Ya', '0' => 'Tidak'],['inline' => true]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'privasi_orang_lain')
                        ->label(Yii::t('fe', '2. Privasi Terhadap Orang Lain'))
                        ->radioList(['1' => 'Ya', '0' => 'Tidak'],['inline' => true]); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'privasi_bagian_tubuh')
                        ->label(Yii::t('fe', '3. Privasi Terhadap Bagian Tubuh Tertentu'))
                        ->radioList(['1' => 'Ya', '0' => 'Tidak'],['inline' => true]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'privasi_informasi')
                        ->label(Yii::t('fe', '4. Privasi Terhadap Informasi Penyakitnya'))
                        ->radioList(['1' => 'Ya', '0' => 'Tidak'],['inline' => true]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">M. Nilai-Nilai Pribadi</h5>
        </div>
        <div class="panel-body">
            <div class="row">
                <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">
                    Permintaan hak perlindungan atas nilai-nilai pribadi :
                </p>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'nilai_pribadi')
                        ->label(false)
                        ->checkboxList([
                            '1' => 'Menolak pulang pada hari tertentu',
                            '2' => 'Menolak diberikan imunisasi pada anaknya',
                            '3' => 'Pasien perempuan menolak dilayani oleh petugas laki-laki atau sebaliknya',
                            '4' => 'Tidak memakan suatu jenis makanan tertentu',
                            '5' => 'Lain-Lain'
                        ]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'nilai_pribadi_lainnya')
                        ->label(Yii::t('fe', 'Sebutkan'))
                        ->textInput(['class' => 'form-control input-sm']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">N. Diagnosa Kebidanan dan Masalah</h5>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'diagnosa_kebidanan')
                        ->label(false)
                        ->textArea(['class' => 'form-control']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">O. Rencana Asuhan Kebidanan</h5>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'rencana_kebidanan')
                        ->label(false)
                        ->textArea(['class' => 'form-control']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var nilai_pribadi_lainnya = "'.$model->nilai_pribadi_lainnya.'"
var _modelForm = "GinekologiForm"

$(document).ready(function(){
    const checkboxNP = $("input[name=\'GinekologiForm[nilai_pribadi][]\'][value=\'5\']");
    const otherNP = $(`#${_modelIdForm}-nilai_pribadi_lainnya`);

    otherNP.prop("readonly", true);

    if(asesmenMedisId) {
        if(nilai_pribadi_lainnya) {
            otherNP.prop("readonly", false);
        }
    }
    checkboxNP.change(function () {
        if(checkboxNP.is(":checked")) {
            otherNP.prop("readonly", false);
        }
        else {
            otherNP.val("").prop("readonly", true);
        }
    });
})
', View::POS_END);
?>

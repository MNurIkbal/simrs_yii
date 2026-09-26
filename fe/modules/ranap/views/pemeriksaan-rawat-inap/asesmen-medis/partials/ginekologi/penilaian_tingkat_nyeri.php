<?php

use yii\helpers\Html;
use yii\web\View;

$classFormNumber = 'form-control doco-number';
$classForm = 'form-control input-sm';
?>
<style>
.box-scale {
    margin-top: 10px;
    padding-top: 10px;
    padding-bottom: 10px;
}

.box-scale-header {
    margin-bottom: 3px !important;
}

table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
  padding: 5px;
}
</style>
<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">E. Penilaian Tingkat Nyeri</h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'terdapat_keluhan')->radioList(
                            [
                                '1' => 'Ya',
                                '0' => 'Tidak',
                            ], [
                                'itemOptions' => [
                                    'class' => 'terdapat_keluhan'
                                ],
                                'inline' => 'true',
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'terdapat_keluhan_lainnya')
                            ->textInput(['class' => $classForm])->label(Yii::t('fe', 'Bila Ya, Bagaimana Tingkat Nyerinya ?')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'waktu_nyeri')->radioList(
                            [
                                '1' => '< 24 Jam',
                                '2' => '< 1 Minggu',
                                '3' => '< 4 Minggu',
                                '4' => '< 1 - 2 Minggu',
                                '5' => '< 3 - 4 Minggu',
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'frekuensi')->radioList(
                            [
                                '1' => 'Kadang Kala',
                                '2' => 'Hilang Timbul',
                                '3' => 'Sering',
                                '4' => 'Menetap'
                            ])->label(); ?>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <p style="margin-left:20px;margin-bottom:20px;text-align:center;font-weight:bold;">Lokasi/Area Nyeri </p>
                    </div>
                    <div class="row" style="text-align:center;">
                        <div class="image-frame">
                            <?php
                                echo Html::img( '@web/media/img/img-pemeriksaan/bagian_tubuh_medis.jpg', [
                                    'width'=> 500,
                                    'height'=> 520,
                                ]);
                            ?>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pencetus_nyeri')
                            ->label(Yii::t('fe', 'Pencetus Pemberat Timbulnya Nyeri'))
                            ->checkboxList(
                            [
                                '1' => 'Beraktifitas',
                                '2' => 'Olah Raga Fisik',
                                '3' => 'Membungkukkan Badan',
                                '4' => 'Batuk',
                                '5' => 'Berdiri',
                                '6' => 'Stress',
                                '7' => 'Duduk',
                                '8' => 'Diet',
                                '9' => 'Lain-Lain',
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pencetus_nyeri_lainnya')
                            ->textInput(['class' => $classForm])->label(false); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pereda_nyeri')
                            ->label(Yii::t('fe', 'Yang Dapat Meredakan Nyeri'))
                            ->checkboxList(
                            [
                                '1' => 'Tirah Baring',
                                '2' => 'Miring Kanan/Kiri',
                                '3' => 'Istirahat',
                                '4' => 'Obat-Obatan',
                                '5' => 'Relaksasi',
                                '6' => 'Lain-Lain',
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pereda_nyeri_lainnya')
                            ->textInput(['class' => $classForm])->label(false); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'nyeri_dirasakan')
                            ->label(Yii::t('fe', 'Nyeri Yang Dirasakan Seperti'))
                            ->checkboxList(
                            [
                                '1' => 'Tertusuk',
                                '2' => 'Terbakar',
                                '3' => 'Kesemutan',
                                '4' => 'Diiris-Iris',
                                '5' => 'Tajam',
                                '6' => 'Tertekan/Tertimpa Beban Berat',
                                '7' => 'Mati Rasa/Kebas/Kaku',
                                '8' => 'Lain-Lain',
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'nyeri_dirasakan_lainnya')
                            ->textInput(['class' => $classForm])->label(false); ?>
                        </div>
                    </div>
                    <br>
                    <div class="row usia2">
                        <div class="col-md-12 text-center">
                            <p style="font-weight:bold;">Skala Nyeri <span style="font-weight:bold;font-style:italic;">(Wong Baker Faces Pain Scale/Numeric Rating Pain)</span></p>
                            <p style="font-weight:bold;font-size:16px;">PAIN MEASUREMENT SCALE</p>
                            <div class="box-scale">
                                <div class="box-scale-header">
                                    <img src="/media/img/all-emote.svg" alt="">
                                </div>
                                <div class="box-scale-line box-scale-line__separator">
                                    <div class="box-scale-line__point">&nbsp;</div>
                                    <div class="box-scale-line__point">&nbsp;</div>
                                    <div class="box-scale-line__point">&nbsp;</div>
                                    <div class="box-scale-line__point">&nbsp;</div>
                                    <div class="box-scale-line__point">&nbsp;</div>
                                    <div class="box-scale-line__point">&nbsp;</div>
                                    <div class="box-scale-line__point">&nbsp;</div>
                                    <div class="box-scale-line__point">&nbsp;</div>
                                    <div class="box-scale-line__point">&nbsp;</div>
                                    <div class="box-scale-line__point">&nbsp;</div>
                                    <div class="box-scale-line__point">&nbsp;</div>
                                    <div class="box-scale-line__hidePercentage"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kategori_nyeri')
                            ->radioList(
                            [
                                '1' => 'Nyeri Ringan',
                                '2' => 'Nyeri Sedang',
                                '3' => 'Nyeri Berat',
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'skor_nyeri')
                            ->label(false)
                            ->radioList(
                            [
                                0 => 0,
                                1 => 1,
                                2 => 2,
                                3 => 3,
                                4 => 4,
                                5 => 5,
                                6 => 6,
                                7 => 7,
                                8 => 8,
                                9 => 9,
                                10 => 10,
                            ], ['inline' => true, 'itemOptions' => ['class' => 'skor_nyeri']]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'total_skor_nyeri')
                            ->label(Yii::t('fe', 'Skor/Skala Nyeri'))
                            ->textInput(['class' => $classFormNumber, 'readonly' => true]); ?>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group highlight-addon has-size-sm">
                                <label class="control-label has-star col-sm-3" for="ginekologiform-nilai0">Nilai 0 : </label>
                                <div class="col-sm-9">
                                    <span>Nyeri Tidak Dirasakan</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group highlight-addon has-size-sm">
                                <label class="control-label has-star col-sm-3" for="ginekologiform-nilai0">Nilai 2 : </label>
                                <div class="col-sm-9">
                                    <span>Nyeri Dirasakan Sedikit Saja</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group highlight-addon has-size-sm">
                                <label class="control-label has-star col-sm-3" for="ginekologiform-nilai0">Nilai 4 : </label>
                                <div class="col-sm-9">
                                    <span>Nyeri Dirasakan Hilang Timbul</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group highlight-addon has-size-sm">
                                <label class="control-label has-star col-sm-3" for="ginekologiform-nilai0">Nilai 6 : </label>
                                <div class="col-sm-9">
                                    <span>Nyeri Yang Dirasakan Anak dan Dewasa Lebih Banyak</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group highlight-addon has-size-sm">
                                <label class="control-label has-star col-sm-3" for="ginekologiform-nilai0">Nilai 8 : </label>
                                <div class="col-sm-9">
                                    <span>Nyeri Yang Dirasakan Secara Keseluruhan</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group highlight-addon has-size-sm">
                                <label class="control-label has-star col-sm-3" for="ginekologiform-nilai0">Nilai 10 : </label>
                                <div class="col-sm-9">
                                    <span>Nyeri Sekali Disertai Menangis</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var pencetus_nyeri_lainnya = "'.$model->pencetus_nyeri_lainnya.'"
var pereda_nyeri_lainnya = "'.$model->pereda_nyeri_lainnya.'"
var nyeri_dirasakan_lainnya = "'.$model->nyeri_dirasakan_lainnya.'"
var terdapat_keluhan_lainnya = "'.$model->terdapat_keluhan_lainnya.'"

var $kategoriNyeri = "'.$model->kategori_nyeri.'"
var total_skor_nyeri = "'.$model->total_skor_nyeri.'"

if(!asesmenMedisId) {
    $("input[name=\'GinekologiForm[total_skor_nyeri]\']").val(0)
}

$(document).ready(function(){
    const checkboxPencetusNyeri = $("input[name=\'GinekologiForm[pencetus_nyeri][]\'][value=\'9\']");
    const checkboxPeredaNyeri = $("input[name=\'GinekologiForm[pereda_nyeri][]\'][value=\'6\']");
    const checkboxNyeriDirasakan = $("input[name=\'GinekologiForm[nyeri_dirasakan][]\'][value=\'8\']");
    const kategoriNyeri = $("input[name=\'GinekologiForm[kategori_nyeri]\']");

    const pencetusNyeriLainnya = $(`#${_modelIdForm}-pencetus_nyeri_lainnya`);
    const peredaNyeriLainnya = $(`#${_modelIdForm}-pereda_nyeri_lainnya`);
    const nyeriDirasakaniLainnya = $(`#${_modelIdForm}-nyeri_dirasakan_lainnya`);
    const keluhanLainnya = $(`#${_modelIdForm}-terdapat_keluhan_lainnya`);
    
    pencetusNyeriLainnya.prop("readonly", true);
    peredaNyeriLainnya.prop("readonly", true);
    nyeriDirasakaniLainnya.prop("readonly", true);
    keluhanLainnya.prop("readonly", true);

    if(asesmenMedisId) {
        if(pencetus_nyeri_lainnya) {
            pencetusNyeriLainnya.prop("readonly", false);
        }
        if(pereda_nyeri_lainnya) {
            peredaNyeriLainnya.prop("readonly", false);
        }
        if(nyeri_dirasakan_lainnya) {
            nyeriDirasakaniLainnya.prop("readonly", false);
        }
        if(terdapat_keluhan_lainnya) {
            keluhanLainnya.prop("readonly", false);
        }
        
        if($kategoriNyeri) {
            if($kategoriNyeri == "1") {
                updateNyeriRingan()
            }
            else if($kategoriNyeri == "2") {
                updateNyeriSedang()
            }
            else {
                updateNyeriBerat()
            }
        }

        $("input[name=\'GinekologiForm[total_skor_nyeri]\']").val(total_skor_nyeri)
    }
    
    checkboxPencetusNyeri.change(function () {
        if(checkboxPencetusNyeri.is(":checked")) {
            pencetusNyeriLainnya.prop("readonly", false);
        }
        else {
            pencetusNyeriLainnya.val("").prop("readonly", true);
        }
    });
    checkboxPeredaNyeri.change(function () {
        if(checkboxPeredaNyeri.is(":checked")) {
            peredaNyeriLainnya.prop("readonly", false);
        }
        else {
            peredaNyeriLainnya.val("").prop("readonly", true);
        }
    });
    checkboxNyeriDirasakan.change(function () {
        if(checkboxNyeriDirasakan.is(":checked")) {
            nyeriDirasakaniLainnya.prop("readonly", false);
        }
        else {
            nyeriDirasakaniLainnya.val("").prop("readonly", true);
        }
    });
    kategoriNyeri.on("change", function(){
        var _val = $(this).val()
        $(".skor_nyeri").prop("checked", false)
        $("input[name=\'GinekologiForm[total_skor_nyeri]\']").val(0)
        if(_val == "1") {
            updateNyeriRingan()
        }
        else if(_val == "2") {
            updateNyeriSedang()
        }
        else {
            updateNyeriBerat()
        }
    })
    $(".skor_nyeri").on("change", function(){
        $("input[name=\'GinekologiForm[total_skor_nyeri]\']").val($(this).val())
    })
    function updateNyeriRingan() {
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'0\']").prop("disabled", false);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'1\']").prop("disabled", false);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'2\']").prop("disabled", false);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'3\']").prop("disabled", false);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'4\']").prop("disabled", true);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'5\']").prop("disabled", true);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'6\']").prop("disabled", true);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'7\']").prop("disabled", true);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'8\']").prop("disabled", true);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'9\']").prop("disabled", true);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'10\']").prop("disabled", true);
    }
    function updateNyeriSedang() {
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'0\']").prop("disabled", true);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'1\']").prop("disabled", true);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'2\']").prop("disabled", true);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'3\']").prop("disabled", true);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'4\']").prop("disabled", false);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'5\']").prop("disabled", false);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'6\']").prop("disabled", false);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'7\']").prop("disabled", true);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'8\']").prop("disabled", true);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'9\']").prop("disabled", true);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'10\']").prop("disabled", true);
    }
    function updateNyeriBerat() {
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'0\']").prop("disabled", true);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'1\']").prop("disabled", true);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'2\']").prop("disabled", true);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'3\']").prop("disabled", true);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'4\']").prop("disabled", true);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'5\']").prop("disabled", true);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'6\']").prop("disabled", true);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'7\']").prop("disabled", false);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'8\']").prop("disabled", false);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'9\']").prop("disabled", false);
        $("input[name=\'GinekologiForm[skor_nyeri]\'][value=\'10\']").prop("disabled", false);
    }
    $(document).on("change", ".terdapat_keluhan", function(){
        if($(this).is(":checked")) {
            if($(this).val() == "1") {
                keluhanLainnya.prop("readonly", false);
            }
            else {
                keluhanLainnya.val("").prop("readonly", true);
            }
        }
    })
})

', View::POS_END);
?>

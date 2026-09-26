<?php

use yii\helpers\Html;
use yii\web\View;

$classForm = 'form-control input-sm';
?>

<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">H. Kebutuhan Edukasi Pasien/Keluarga</h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kesediaan_keluarga')->radioList(
                            [
                                'Tidak' => 'Tidak',
                                'Ya' => 'Ya',
                            ],
                            ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'paraf')->textInput(
                            ['class' => $classForm]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'hambatan_keluarga_edukasi')->radioList(
                            [
                                'Tidak' => 'Tidak',
                                'Ya' => 'Ya',
                            ],
                            ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-3">
                            <?= $form->field($model, 'hambatan_keluarga')->checkboxList(
                            [
                                'Pendengaran' => 'Pendengaran',
                                'Penglihatan' => 'Penglihatan',
                                'Kognitif' => 'Kognitif',
                                'Fisik' => 'Fisik',
                                'Emosi' => 'Emosi',
                                'Lainnya' => 'Lainnya',
                            ],
                            []); ?>
                        </div>
                        <div class="col-sm-3">
                            <?= $form->field($model, 'hambatan_keluarga_lainnya')->textInput(
                            ['class' => $classForm])->label(false); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pendidikan_ortu')->textInput(
                            ['class' => $classForm]); ?>
                        </div>
                        <div class="col-sm-3">
                            <?= $form->field($model, 'agama')->radioList(
                            [
                                'Islam' => 'Islam',
                                'Kristen' => 'Kristen',
                                'Katolik' => 'Katolik',
                                'Hindu' => 'Hindu',
                                'Budha' => 'Budha',
                                'Lainnya' => 'Lainnya',
                            ],
                            ['itemOptions' => ['class' => 'agama']]); ?>
                        </div>
                        <div class="col-sm-3">
                            <?= $form->field($model, 'agama_lainnya')->textInput(
                            ['class' => $classForm])->label(false); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'penerjemah')->radioList(
                            [
                                'Tidak' => 'Tidak',
                                'Ya' => 'Ya',
                            ],
                            ['inline' => true, 'itemOptions' => ['class' => 'penerjemah']]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'penerjemah_lainnya')->textInput(
                            ['class' => $classForm]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kebutuhan_edukasi')->checkboxList(
                            [
                                'Diagnosa Penyakit' => 'Diagnosa Penyakit',
                                'Obat-Obatan' => 'Obat-Obatan',
                                'Penggunaan Alat-Alat Medis' => 'Penggunaan Alat-Alat Medis',
                                'Diet dan Nutrisi' => 'Diet dan Nutrisi',
                                'Rehabilitasi Medik' => 'Rehabilitasi Medik',
                                'Manajemen Nyeri' => 'Manajemen Nyeri',
                                'Pemberian ASI' => 'Pemberian ASI',
                                'Lainnya' => 'Lainnya'
                            ],
                            []); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kebutuhan_edukasi_lainnya')->textInput(
                            ['class' => $classForm])->label(false); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var hambatan_keluarga_lainnya = "'.$model->hambatan_keluarga_lainnya.'"
var agama_lainnya = "'.$model->agama_lainnya.'"
var penerjemah_lainnya = "'.$model->penerjemah_lainnya.'"
var kebutuhan_edukasi_lainnya = "'.$model->kebutuhan_edukasi_lainnya.'"

$(document).ready(function(){
    const checkboxHambatan = $("input[name=\'NeonatusForm[hambatan_keluarga][]\'][value=\'Lainnya\']");
    const checkboxEdukasi = $("input[name=\'NeonatusForm[kebutuhan_edukasi][]\'][value=\'Lainnya\']");

    const hambatanLainnya = $("#neonatusform-hambatan_keluarga_lainnya");
    const agamaLainnya = $("#neonatusform-agama_lainnya");
    const penerjemahLainnya = $("#neonatusform-penerjemah_lainnya");
    const edukasiLainnya = $("#neonatusform-kebutuhan_edukasi_lainnya");

    hambatanLainnya.prop("readonly", true);
    agamaLainnya.prop("readonly", true);
    penerjemahLainnya.prop("readonly", true);
    edukasiLainnya.prop("readonly", true);

    if(asesmenMedisId) {
        if(hambatan_keluarga_lainnya) {
            hambatanLainnya.prop("readonly", false);
        }
        if(agama_lainnya) {
            agamaLainnya.prop("readonly", false);
        }
        if(penerjemah_lainnya) {
            penerjemahLainnya.prop("readonly", false);
        }
        if(kebutuhan_edukasi_lainnya) {
            edukasiLainnya.prop("readonly", false);
        }
    }

    checkboxHambatan.change(function () {
        if(checkboxHambatan.is(":checked")) {
            hambatanLainnya.prop("readonly", false);
        }
        else {
            hambatanLainnya.val("").prop("readonly", true);
        }
    });

    checkboxEdukasi.change(function () {
        if(checkboxEdukasi.is(":checked")) {
            edukasiLainnya.prop("readonly", false);
        }
        else {
            edukasiLainnya.val("").prop("readonly", true);
        }
    });

    $(document).on("change", ".penerjemah", function(){
        if($(this).is(":checked")) {
            if($(this).val() == "Ya") {
                penerjemahLainnya.prop("readonly", false);
            }
            else {
                penerjemahLainnya.val("").prop("readonly", true);
            }
        }
    })
    $(document).on("change", ".agama", function(){
        if($(this).is(":checked")) {
            if($(this).val() == "Lainnya") {
                agamaLainnya.prop("readonly", false);
            }
            else {
                agamaLainnya.val("").prop("readonly", true);
            }
        }
    })
})

', View::POS_END);
?>

<?php

/**
 * @Author: Sigit
 * @Date:   2018-09-27 09:48:48
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\DatePicker;

?>

<style type="text/css">
    .wrapper {
        text-align: center;
    }

    .button {
        position: absolute;
        top: 50%;
    }
    .datepicker>div{
        display:block;
    }
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title text-center"><?= Yii::t('fe', 'Proses Pendaftaran Online') ?></h5>
</div>
<div class="modal-body">
    <div class="col-md-6">
        <?php $form = ActiveForm::begin([
            'id' => 'form',
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => [
                'labelSpan' => 5,
                'deviceSize' => ActiveForm::SIZE_SMALL
            ]
        ]) ?>
        <?= Html::hiddenInput('pasien_id', $data['pasien_id'], ['readonly' => 'readonly']) ?>
        <?= Html::hiddenInput('pendaftaranol_id', $data['pendaftaranol_id'], ['readonly' => 'readonly']) ?>
        <?= Html::hiddenInput('status_daftar_ol', '', ['id' => 'status_daftar_ol']) ?>
        <?php if (isset($data['no_rekam_medik']) && $data['no_rekam_medik'] != ''): ?>
        <div class="row">
            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "No Rekam Medis") ?></b></label>
            <div class="col-sm-7">
                <p><b>:</b>&nbsp;<?= $data['no_rekam_medik'] ?></p>
            </div>
        </div>
        <?php endif ?>
        <?php if (isset($dataPasien['stat_pasien']) && $dataPasien['stat_pasien'] != ''): ?>
        <div class="row">
            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Klasifikasi Pasien") ?></b></label>
            <div class="col-sm-7">
                <p><b>:</b>&nbsp;<?= $dataPasien['statuspasien_nama'] ?></p>
            </div>
        </div>
        <?php endif ?>
        <div class="form-group">
            <?php if (!isset($data['no_rekam_medik']) || $data['no_rekam_medik'] == ''): ?>
                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "No Rekam Medis") ?></b></label>
                <div class="col-sm-7">
                    <?= Html::textInput('no_rekam_medik', '', ['class' => 'form-control input-sm', 'id' => 'no_rekam_medik']) ?>
                </div>
            <?php endif ?>
        </div>
        <div class="form-group">
            <?php if (!isset($dataPasien['stat_pasien']) || $dataPasien['stat_pasien'] == ''): ?>
                <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Klasifikasi Pasien") ?></b></label>
                <div class="col-sm-7">
                    <?= Select2::widget([
                        'id' => 'stat_pasien',
                        'name' => 'stat_pasien',
                        'data' => $listKlasifikasiPasien,
                        'options' => [
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '--Pilih--')
                        ],
                    ]) ?>
                </div>
            <?php endif ?>
        </div>
        <div class="form-group">
            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Cara Bayar") ?></b></label>
            <div class="col-sm-7">
                <?= Select2::widget([
                    'id' => 'carabayar_id',
                    'name' => 'carabayar_id',
                    'data' => $listCaraBayar,
                    'value' => $data['carabayar_id'],
                    'options' => [
                        'class' => 'form-control select2',
                        'prompt' => \Yii::t('fe', '--Pilih--')
                    ],
                ]) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Penjamin") ?></b></label>
            <div class="col-sm-7">
                <?= Select2::widget([
                    'id' => 'penjamin_id',
                    'name' => 'penjamin_id',
                    'data' => $initPenjamin,
                    'value' => $data['penjamin_id'],
                    'options' => [
                        'class' => 'form-control select2',
                        'prompt' => \Yii::t('fe', '--Pilih--')
                    ],
                ]) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "No Asuransi") ?></b></label>
            <div class="col-sm-7">
                <?php
                    $no_asuransi = isset($data['no_asuransi']) ? $data['no_asuransi'] : '-';
                ?>
                <?= Html::textInput('no_asuransi', $no_asuransi, ['class' => 'form-control input-sm', 'required', 'id' => 'no_asuransi']) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "No Rujukan") ?></b></label>
            <div class="col-sm-7">
                <?php
                    $no_rujukan = isset($data['no_rujukan']) ? $data['no_rujukan'] : '-';
                ?>
                <?= Html::textInput('no_rujukan', $no_rujukan, ['class' => 'form-control input-sm', 'id' => 'no_rujukan']) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Nama Pasien") ?></b></label>
            <div class="col-sm-7">
                <?php
                    $nama_pasien = isset($data['nama_pasien']) ? $data['nama_pasien'] : '-';
                ?>
                <?= Html::textInput('nama_pasien', $nama_pasien, ['class' => 'form-control input-sm', 'id' => 'nama_pasien']) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Tempat Lahir") ?></b></label>
            <div class="col-sm-7">
                <?php
                    $tempat_lahir = isset($data['tempat_lahir']) ? $data['tempat_lahir'] : '-';
                ?>
                <?= Html::textInput('tempat_lahir', $tempat_lahir, ['class' => 'form-control input-sm', 'id' => 'tempat_laWhir']) ?>
            </div>
        </div>
        <div class="form-group">
            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Tanggal Lahir") ?></b></label>
            <div class="col-sm-7">
            <?=
                DatePicker::widget([
                    'name' => 'tanggal_lahir',
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'value' => $data['tanggal_lahir'],
                    'readonly' => true,
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd MM yyyy',
                    ]
                ]); ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div class="col-md-6">
        <!-- <div class="row">
            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Pangkat") ?></b></label>
            <div class="col-sm-7">
                <p><b>:</b>&nbsp;<?= isset($data['pangkat']) ? $data['pangkat'] : '-' ?></p>
            </div>
        </div>
        <div class="row">
            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "NRP") ?></b></label>
            <div class="col-sm-7">
                <p><b>:</b>&nbsp;<?= isset($data['nrp']) ? $data['nrp'] : '-' ?></p>
            </div>
        </div>
        <div class="row">
            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Kesatuan") ?></b></label>
            <div class="col-sm-7">
                <p><b>:</b>&nbsp;<?= isset($data['kesatuan']) ? $data['kesatuan'] : '-' ?></p>
            </div>
        </div> -->
        <div class="row">
            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Alamat") ?></b></label>
            <div class="col-sm-7">
                <p><b>:</b>&nbsp;<span><?= isset($data['alamat_pasien']) ? $data['alamat_pasien'] : '-' ?></span></p>
            </div>
        </div>
        <div class="row">
            <label class="text-left control-label col-sm-5"></label>
            <div class="col-sm-7">
                <p>&nbsp;&nbsp;<?= isset($data['kelurahan_nama']) ? $data['kelurahan_nama'] : '-' ?></p>
            </div>
        </div>
        <div class="row">
            <label class="text-left control-label col-sm-5"></label>
            <div class="col-sm-7">
                <p>&nbsp;&nbsp;<?= isset($data['kecamatan_nama']) ? $data['kecamatan_nama'] : '-' ?></p>
            </div>
        </div>
        <div class="row">
            <label class="text-left control-label col-sm-5"></label>
            <div class="col-sm-7">
                <p>&nbsp;&nbsp;<?= isset($data['kabupaten_nama']) ? $data['kabupaten_nama'] : '-' ?></p>
            </div>
        </div>
        <div class="row">
            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Nama Ibu") ?></b></label>
            <div class="col-sm-7">
                <p><b>:</b>&nbsp;<?= isset($data['nama_ibu']) ? $data['nama_ibu'] : '-' ?></p>
            </div>
        </div>
        <div class="row">
            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Jenis kelamin") ?></b></label>
            <div class="col-sm-7">
                <p><b>:</b>&nbsp;<?= isset($data['jenis_kelamin']) ? $data['jenis_kelamin'] : '-' ?></p>
            </div>
        </div>
        <div class="row">
            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Poli Tujuan") ?></b></label>
            <div class="col-sm-7">
                <p><b>:</b>&nbsp;<?= isset($data['ruangan_nama']) ? $data['ruangan_nama'] : '-' ?></p>
            </div>
        </div>
        <div class="row">
            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Dokter") ?></b></label>
            <div class="col-sm-7">
                <p><b>:</b>&nbsp;<?= isset($data['nama_pegawai']) ? $data['nama_pegawai'] : '-' ?></p>
            </div>
        </div>
        <div class="row">
            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Jadwal") ?></b></label>
            <div class="col-sm-7">
                <p><b>:</b>&nbsp;<?= isset($data['jadwal']) ? $data['jadwal'] : '-' ?></p>
            </div>
        </div>
        <div class="row">
            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "No Antrian Dokter") ?></b></label>
            <div class="col-sm-7">
                <p><b>:</b>&nbsp;<?= isset($data['no_antrian']) ? $data['no_antrian'] : '-' ?></p>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="wrapper">
        <?= Html::button('<b><i class="fa fa-check"></i></b>'. Yii::t('fe', 'Setujui'),[
            'class' => 'btn btn-info btn-labeled btn-xs btn-setuju-tolak text-center',
            'data-status' => $statusSetujui,
            'action' => Url::to([
                '/pendaftaran/pendaftaran-online/proses',
                'id' => DocoHelpers::encrypt($data['pendaftaranol_id']),
            ]),
        ]) ?>
        <?= Html::button('<b><i class="fa fa-times"></i></b>'. Yii::t('fe', 'Tolak'),[
            'class' => 'btn btn-danger btn-labeled btn-xs btn-setuju-tolak text-center',
            'data-status' => $statusTolak,
            'action' => Url::to([
                '/pendaftaran/pendaftaran-online/proses',
                'id' => DocoHelpers::encrypt($data['pendaftaranol_id']),
            ]),
        ]) ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.pickadate').pickadate({
            format: 'dd mmmm yyyy',
            // max: new Date(),
            // onStart: function () {
            //     var date = new Date()
            //     this.set('select', [date.getFullYear(), date.getMonth(), date.getDate()]);
            // }
        });
        $(".kv-plugin-loading").remove();

        var carabayar_id = $("#carabayar_id").val();
        var penjamin_id = $("#penjamin_id").val();

        if (carabayar_id != "") {
            $("#penjamin_id").val("").trigger("change");
            $("#penjamin_id").attr("disabled", true);

            $.ajax({
                type: "GET",
                dataType: "json",
                url: "/pendaftaran/pendaftaran-online/get-penjamin-portal?carabayar_id="+carabayar_id,
                success: function(response) {
                    $('#penjamin_id').find("option").remove().end().append($("<option>", {value : ""}).text("--Pilih--"));

                    if (response.length > 0) {
                        $.each(response, function(index, item) {
                            $('#penjamin_id').append($("<option>", {value : item.id}).text(item.text));
                        });
                    }

                    $('#penjamin_id').val(penjamin_id).trigger("change");
                }
            }).done(function() {
                $("#penjamin_id").removeAttr("disabled");
            });
        } else {
            $("#penjamin_id").val("").trigger("change");
            $("#penjamin_id").attr("disabled", true);
        }
    });

    $(document).on("click", ".btn-setuju-tolak", function(event) {
        event.preventDefault();

        $("#status_daftar_ol").val($(this).data("status"));

        $(this).docoForm("click", {
            data: $("#form").serializeArray(),
            success : function(data) {
                location.reload();
            }
        });
    });

    $(document).on("change", "#carabayar_id", function(event) {
        event.preventDefault();

        var carabayar_id = $(this).val();

        if (carabayar_id != "") {
            $("#penjamin_id").val("").trigger("change");
            $("#penjamin_id").attr("disabled", true);

            $.ajax({
                type: "GET",
                dataType: "json",
                url: "/pendaftaran/pendaftaran-online/get-penjamin-portal?carabayar_id="+carabayar_id,
                success: function(response) {
                    $('#penjamin_id').find("option").remove().end().append($("<option>", {value : ""}).text("--Pilih--"));

                    if (response.length > 0) {
                        $.each(response, function(index, item) {
                            $('#penjamin_id').append($("<option>", {value : item.id}).text(item.text));
                        });
                    }
                }
            }).done(function() {
                $("#penjamin_id").removeAttr("disabled");
            });
        } else {
            $("#penjamin_id").val("").trigger("change");
            $("#penjamin_id").attr("disabled", true);
        }
    });
</script>
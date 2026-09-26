<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\file\FileInput;
use app\components\DocoHelpers;

?>
<style type="text/css">
    .classMin {
        margin-left: -30px;
    }
    .modal-content {
        border-radius: 10px !important;
        /* padding: 10px; */
    }
    .modal-header {
        padding: 10px 10px;
        border-top-right-radius: 10px;
        border-top-left-radius: 10px;
        background-color: #D5E9FF !important;
    }
    .modal-body {
        padding : 20px 20px;
    }
    .bg-inverse {
        background-color: #fff;
        border-color: #37474f;
        color: #01223b;
        border: none;
        font-size: 16px;
    }
    .modal-title {
        font-size: 16px;
        font-weight: 500;
    }
    .modal-content[class*=bg-] .modal-header .close, .modal-header[class*=bg-] .close {
        color: #01223b;
        font-size: 28px;
        font-weight: bold;
        line-height: 14px;
        background-color: #D5E9FF !important;
    }

    .btn-next{
        transition-duration: 0.4s !important;
        background-color: #014d8a;
        color: #ffffff;
        font-weight: 500;
    }

    .btn-next:hover{
        background-color: #014d8a;
        color: #ffffff;
        font-weight: 500;
    }
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
    <?php
        $form = ActiveForm::begin([
            'id' => 'checkin-mjkn-form',
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'type' => ActiveForm::TYPE_VERTICAL,
            'formConfig' => [
                'labelSpan' => 3,
                'deviceSize' => ActiveForm::SIZE_SMALL
            ]
        ]);
    ?>
</div>
<div class="modal-body">
    <div class="form-group">
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($modelForm, 'kode_booking')->textInput([
                    'class' => 'form-control input-xlg',
                    'autocomplete' => "off",
                    'placeholder' => "Masukan Kode Booking",
                    "style" => "text-transform:uppercase"
                ]) ?>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?=Html::button(\Yii::t('fe', 'Check-In'), ['type'=> 'submit', 'class' => 'btn btn btn-lg btn-block btn-next', 'id' => 'checkin-mjkn']); ?>
    <?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    var urlPrintAntrianPoli = "<?=Url::home().'pendaftaran/daftar-rajal/print-antrian-poli?'?>";
    var urlAplikasiFingerBpjs = "<?=$url_aplikasi_finger_bpjs?>";
    $(document).ready(function() {
        $('#checkin-mjkn').click(function(e){
            e.preventDefault();
            let konfig_url_cetak = $('#konfig-url-cetak').val()
            let antrian_jenis_id = (new URL(document.location)).searchParams.get('jenis_id');
            e.preventDefault()
            let kodeBooking = ($('#antriancheckinmjkn-kode_booking').val()).toUpperCase();
            if(kodeBooking == ''){
                docoNotification("warning", "Perhatian!", "Kode Booking Belum Terisi!");
                return false
            }
            let payload = {
                'kodebooking': kodeBooking,
                'is_fingerprint_checked' : true,
            }

            if (konfigAutoDaftar) {
                checkInAutoDaftar(payload, antrian_jenis_id);
            } else {
                checkInManual($(this), payload, antrian_jenis_id);
            }
        })
    })

    function checkInManual(selector, payload, antrian_jenis_id)
    {
        $(selector).docoForm("click", {
            url: '/antrian/dashboard/checkin-mjkn',
            type: 'POST',
            dataType: 'json',
            data: payload,
            skipConfirm: true,
            beforeSend: function (data) {
            },
            success: function (res) {
                let antrian_id = res?.response?.pendaftaran?.antrian_id;
                let data  = {
                    "antrian_id": antrian_id,
                    "no_antrian" : "-",
                    "jenis_antrian_id": 312,
                    "jenisantrian_id" : 2112,
                    "antrian_jenis_id" : antrian_jenis_id,
                }

                if (!res?.response?.data?.is_above_17 || (res?.response?.is_fingerprint_checked != undefined && res.response.is_fingerprint_checked == true)) {
                    $('#modal_backdrop').modal('hide');
                    swal({
                        title:"Check-in Berhasil!",
                        text:"Silahkan menuju ke poli tujuan",
                        type:"success",
                        confirmButtonText: "Selesai",
                    }, function (i) {
                        $.ajax({
                            type: 'POST',
                            url: '/antrian/dashboard/cetak-antrian-v2',
                            timeout: (60 * 1000),
                            data: data,
                            success: function (res){
                                location.reload();
                                $.redirect("/antrian/dashboard/cetak-antrian-v2", data);
                            },
                            error: function (error) {
                            }
                        });
                        // window.open(urlPrintAntrianPoli+"?antrian_id="+antrian_id+"&jumlah=2"); // Print Antrian Poli
                    });
                } else {
                    window.open(urlAplikasiFingerBpjs); // open aplikasi bpjs finger
                }
            },
            error: function(data){
            }
        });
    }

    function checkInAutoDaftar(payload, antrian_jenis_id)
    {
        $.ajax({
            url: '/antrian/dashboard/checkin-mjkn',
            type: 'POST',
            dataType: 'json',
            data: payload,
            global: false,
            beforeSend: function (data) {
                PNotify.removeAll();
                showLoaderCustom('Memproses data', '(20%)', 'Sistem sedang memeriksa reservasi Anda, mohon tunggu sebentar...');
            },
            complete: function () {
                updateLoaderCustom('Memproses data', '(50%)', 'Reservasi Anda berhasil ditemukan. Sistem sedang mempersiapkan data kunjungan Anda.')
            },
            success: function (res) {
                let responsePasien = res?.response?.pendaftaranol;
                let randString = res?.response?.randString;
                let data  = {
                    "antrian_id": responsePasien?.antrian_id,
                    "no_antrian" : responsePasien?.no_antrian,
                    "jenis_antrian_id": 312,
                    "jenisantrian_id" : 2121,
                    "antrian_jenis_id" : antrian_jenis_id,
                }

                if (!res?.response?.data?.is_above_17 || (res?.response?.is_fingerprint_checked != undefined && res.response.is_fingerprint_checked == true)) {
                    let payloadAutoDaftar = setPayloadAutoDaftar(responsePasien)
                    listenStatusReservationUpdate(payloadAutoDaftar, randString, data);

                    $.ajax({
                        url: '/antrian/dashboard/auto-register-process',
                        type: 'POST',
                        dataType: 'json',
                        global: false,
                        data: {
                            randString: randString,
                            reservationItems: JSON.stringify(payloadAutoDaftar)
                        },
                        success: function () {
                            updateLoaderCustom('Memproses Data', '(70%)', 'Reservasi Anda berhasil ditemukan. Sistem sedang membuat data kunjungan Anda.');
                        },
                        error: function () {
                            hideLoaderCustom();
                            docoNotification('warning', 'Perhatian!', 'Proses pembuatan Kunjungan Anda gagal. <b>Silakan hubungi petugas pendaftaran untuk melakukan pendaftaran secara manual.</b>');
                        }
                        });

                } else {
                    hideLoaderCustom();
                    docoNotification('warning', 'Perhatian!', 'Silakan verifikasi lewat sidik jari atau FRISTA terlebih dahulu sebelum Check-In. <b>Setelah melakukan verifikasi, silakan tekan tombol Check-In kembali.</b>', false);
                    window.open(urlAplikasiFingerBpjs); // open aplikasi bpjs finger
                }
            },
            error: function(data){
                hideLoaderCustom();
                if (data?.status == 422) {
                    docoNotification('warning', 'Perhatian!', data?.responseJSON?.response?.message+' <b>Silahkan ke pendaftaran untuk mendaftarkan Kunjungan anda.</b>');
                } else {
                    docoNotification('error', 'Perhatian!', 'Sistem sedang mengalami gangguan. <b>Silahkan ke pendaftaran untuk melakukan check in manual.</b>');
                }
                
            }
        });
    }
    </script>

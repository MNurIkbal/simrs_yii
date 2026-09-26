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
</div>
<div class="modal-body">
<h5>Data Rujukan</h5>
<div class="form-group">
    <?php
        $form = ActiveForm::begin([
            'id' => 'data-rujukan-form',
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'type' => ActiveForm::TYPE_VERTICAL,
            'formConfig' => [
                'labelSpan' => 3,
                'deviceSize' => ActiveForm::SIZE_SMALL
            ]
        ]);
    ?>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($modelForm, 'nomor', [
                    'addon' => [
                        'append' => [
                            'content' => Html::button('Cari', ['id'=>'cari-nomor-identitas','class'=>'btn btn-next','style'=>'background-color: #014d8a;']),
                            'asButton' => true
                        ]
                    ]
            ])->textInput([
                'class' => 'form-control',
                'autocomplete' => "off",
                'placeholder' => "Masukan Nomor Rekam Medik/Nomor Pendaftaran/Nomor BPJS"
            ]) ?>
            <?= $form->field($modelForm, 'nama_poli')->textInput([
                'class' => 'form-control',
                'readonly' => true,
                'autocomplete' => "off",
            ]) ?>
            <?= $form->field($modelForm, 'nama_dokter')->textInput([
                'class' => 'form-control',
                'autocomplete' => "off",
                'readonly' => true,
            ]) ?>
            <?= $form->field($modelForm, 'dokter_id')->hiddenInput()->label(false)?>
            <?= $form->field($modelForm, 'ruangan_id')->hiddenInput()->label(false)?>
            <?= $form->field($modelForm, 'konsulpoli_id')->hiddenInput()->label(false)?>
        </div>
        <div class="modal-footer">
            <?=Html::button(\Yii::t('fe', 'Simpan'), ['type'=> 'submit', 'id'=>'simpan-pasien-routing', 'class' => 'btn btn btn-lg btn-next']); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
</div>

<script type="text/javascript">
    var urlPrintAntrianPoli = "<?=Url::home().'pendaftaran/daftar-rajal/print-antrian-poli?'?>";
    var urlAplikasiFingerBpjs = "<?=$url_aplikasi_finger_bpjs?>";
    $(document).ready(function() {
        //dari button cari
        $('#cari-nomor-identitas').click(function(e) {
            if($('[name="PasienRoutingRujukanForm[nomor]"]').val() == ''){
                docoNotification("warning", "Perhatian!", "Nomor Identitas Belum Terisi!");
                return false
            }else{
                getListKonsulPasienRouting()
                $('[name="PasienRoutingRujukanForm[nomor]"]').blur()
                return false
            }
        })

        var getListKonsulPasienRouting = () => {
            let wrapperParents = $('#modal-konsul-pasien-routing')
            showLoader('Memuat Halaman...')
            $('#modal-konsul-pasien-routing .modal-content').docoLoad({
                url: '/antrian/dashboard/modal-list-konsul-pasien-routing',
                dataType: 'html',
                data: {'nomor_identitas': $('[name="PasienRoutingRujukanForm[nomor]"]').val()},
                success: function (data) {
                    hideLoader()
                    $('#modal-konsul-pasien-routing .modal-content').parents('.modal').modal('show')
                    $('#modal-konsul-pasien-routing').css('z-index', '1950')
                    $('#modal-konsul-pasien-routing').find('.modal-dialog').css('width', '1280px')
                },
                error: function (res) {
                    let _response = JSON.parse(res.responseText);
                    docoNotification('error', 'Proses Gagal!', _response?.metadata?.message);
                    hideLoader()
                }
            })
        }
        $('#simpan-pasien-routing').click(function(e){
            let konfig_url_cetak = $('#konfig-url-cetak').val()
            let antrian_jenis_id = (new URL(document.location)).searchParams.get('jenis_id');
            e.preventDefault()
            let konsulpoliId = $('[name="PasienRoutingRujukanForm[konsulpoli_id]').val()
            if(konsulpoliId == ''){
                docoNotification("warning", "Perhatian!", "Harus memilih konsul poli terlebih dahulu!");
                return false
            }
            let payload = {
                'konsulpoli_id': konsulpoliId,
                'is_fingerprint_checked' : true,
            }
            $(this).docoForm("click", {
                url: '/antrian/dashboard/pasien-routing',
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
                        "jenisantrian_id" : 2121,
                        "antrian_jenis_id" : antrian_jenis_id,
                    }

                    if (res?.response?.is_fingerprint_checked != undefined && res.response.is_fingerprint_checked == true) {
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
                                timeout: (5 * 1000),
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
        })
    })
    </script>

<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\file\FileInput;
use app\components\DocoHelpers;
use app\components\DocoConstants;
?>
<style type="text/css">
    .classMin {
        margin-left: -30px;
    }
    .modal-content {
        border-radius: 10px !important;
        padding: 10px;
    }
    .modal-header {
        padding: 10px 10px;
        border-top-right-radius: 10px !important;
        border-top-left-radius: 10px !important;
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
        text-align: center;
    }
    .modal-content[class*=bg-] .modal-header .close, .modal-header[class*=bg-] .close {
        color: #01223b;
        font-size: 28px;
        font-weight: bold;
        line-height: 14px;
    }
    .card-carabayar {
        margin: auto;
        width: 100%;
        cursor: default;
    }
    .card-carabayar .card-name {
        margin-bottom: 12px !important;
        margin-top: 0px !important;
    }
</style>
<div class="modal-header bg-inverse">
    <?php
    $form = ActiveForm::begin([
        'id' => 'sync-form',
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => [
            'labelSpan' => 3,
            'deviceSize' => ActiveForm::SIZE_SMALL
        ],
        'options' => [
            'role' => 'form',
        ]
    ]);
    ?>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="card card-carabayar">
                <h4 class="card-name">Pasien Lama</h4>
                <button type="button" class="btn btn-primary"  data-toggle="modal", data-target="#modal-pasien-bpjs-onsite" action="/antrian/dashboard/onsite-bpjs/">Pilih</button>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-carabayar" id="pasien-baru-content" data-carabayar="<?= $carabayar; ?>" data-ruangan="<?= $ruangan; ?>" data-jenis-antrian="<?= $jenisantrian_id; ?>">
                <h4 class="card-name">Pasien Baru</h4>
                <button type="submit" id="pasien-baru" class="btn btn-primary">Pilih</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var __daftarKontrol = [];
    $('#pasien-baru').click((e) => {
        e.preventDefault()
        let carabayarId = $('#pasien-baru-content').data('carabayar')
        let ruanganId = $('#pasien-baru-content').data('ruangan')
        let jenisantrianId = $('#pasien-baru-content').data('jenis-antrian')
        let antrian_jenis_id = (new URL(document.location)).searchParams.get('jenis_id');
        let payload = {
            'carabayar_id': carabayarId,
            'ruangan_id': ruanganId,
            'jenisantrian_id': jenisantrianId,
            'antrian_jenis_id': antrian_jenis_id,
        }
        $.ajax({
            url: '/antrian/dashboard/create-antrian-v2',
            type: 'POST',
            dataType: 'json',
            data: payload,
            beforeSend: function (data) {
            },
            success: function (res) {
                console.log(res)
                if(res?.response){
                    if(res?.response?.data?.length == 0){
                        let errTitle = res.response.title
                        let errMessage = res.response.message
                        docoNotification('warning', errTitle, errMessage);
                    }else{
                        let responseMessage = res.response.text
                        docoNotification('success', 'Proses Berhasil!', responseMessage);
                        let antrian_id = res?.response?.data?.antrian_id
                        let no_antrian = res?.response?.data?.no_antrian

                        $('#modal_backdrop').modal('hide');
                        $('#modal_status_pasien').modal('hide');
                        let data = {
                            "antrian_id": antrian_id,
                            "no_antrian": no_antrian,
                            "jenisantrian_id": jenisantrianId,
                            "nama_antrian": (jenisantrianId == <?= DocoConstants::ANTRIAN_BPJS?>) ? 'BPJS' : 'Umum',
                            "antrian_jenis_id": antrian_jenis_id,
                        }
                        console.log(data)

                        $.ajax({
                            type: 'POST',
                            url: '/antrian/dashboard/cetak-antrian-v2',
                            timeout: (5 * 1000),
                            data: data,
                            success: function (res){
                                location.reload();
                                $.redirect("/antrian/dashboard/cetak-antrian-v2",{
                                    antrian_id: antrian_id,
                                    no_antrian: no_antrian,
                                    jenisantrian_id: jenisantrianId,
                                    nama_antrian: (jenisantrianId == <?= DocoConstants::ANTRIAN_BPJS?>) ? 'BPJS' : 'Umum',
                                    antrian_jenis_id: antrian_jenis_id,
                                });
                            },
                            error: function (error) {
                            }
                        });
                    }
                }
            },
            error: function(data){
                if(data?.responseJSON){
                    let response = data?.responseJSON?.response
                    let errorMessage = response.message
                    docoNotification('warning','Perhatian!', errorMessage);
                }
            }
        });
    })
</script>

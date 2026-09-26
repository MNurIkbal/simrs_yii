<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
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
        padding: 10px;
    }
    .modal-header {
        padding: 10px 10px;
        border-top-right-radius: 10px;
        border-top-left-radius: 10px;
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
    }
    .card-carabayar {
        margin-bottom: 0;
    }
    .card-carabayar .nama {
        margin-bottom: 0 0 10px 0;
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
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var defaultAntrian = location.search.split('default=')[1];
        $('.modal-body').empty();
        if (carabayar) {
            var data = klasifikasiPasien.cara_bayar;
            var el = '<div class="row">';
            $.each(carabayar, function(key, value) {
                if (data.indexOf(value.groupcarabayar_id.toString()) !== -1) {
                    console.log('masuk');
                    el += `
                        <div class="col-sm-6">
                            <div class="card card-carabayar" data-carabayar="${value.groupcarabayar_id}">
                                <h4 class="nama">${value.groupcarabayar_nama}</h4>
                                <button type="button" class="btn btn-primary">Pilih</button>
                            </div>
                        </div>
                        `;
                }
            });
            el += `</div>`;

            $('.modal-body').html(el);
        }
        $('.card-carabayar').on('click', function(e) {
            e.preventDefault();
            postData.carabayar_pilih = $(this).data('carabayar');
            var form = $('#ambil-antrian-form');
            var url = form.attr('action');
            let antrian_jenis_id = (new URL(document.location)).searchParams.get('jenis_id');

            $('.card-carabayar .card-checked').remove();
            var elm = `<div class="card-checked"><i class="fa fa-check fa-lg"></i></div>`;
            $(this).prepend(elm);
            $.ajax({
                url: form.attr('action'),
                type: form.attr('method'),
                data: postData,
                success: function (res) {

                    var succMessage = 'Proses Berhasil!';
                    var succText = 'Antrian Dicetak';
                    var msg = res.response;
                    var noantrian = '';
                    var jenis_id = 'Mq';
                    var antrian_id = 0;
                    var no_antrian_poli = '';
                    if(typeof msg.text != 'undefined'){
                        succText = msg.text;
                    }
                    if(typeof msg.message != 'undefined'){
                        succMessage = msg.message;
                    }
                    new PNotify({
                        title: succMessage,
                        text: succText,
                        addclass: 'alert alert-success alert-arrow-right alert-styled-right',
                        type: 'success'
                    });
                    if(typeof msg.data.no_antrian != 'undefined'){
                        noantrian = msg.data.no_antrian;
                    }
                    if(typeof msg.data.jenisantrian_id != 'undefined'){
                        jenis_id = msg.data.jenisantrian_id;
                    }
                    if(typeof msg.data.antrian_id != 'undefined'){
                        antrian_id = msg.data.antrian_id;
                    }
                    if(typeof msg.data.no_antrian_poli != 'undefined'){
                        no_antrian_poli = msg.data.no_antrian_poli;
                    }
                    $('#modal_backdrop').modal('hide');
                    
                    if(konfig_url_cetak == ''){
                        $.redirect("/antrian/dashboard/cetak-antrian-dashboard",{no_antrian:noantrian,jenisantrian_id:jenis_id,antrian_id:antrian_id,no_antrian_poli:no_antrian_poli, detail_id: detail_id, antrian_jenis_id: antrian_jenis_id,tipe: "default"});
                    }else{
                        $.ajax({
                            type: 'GET',
                            url: konfig_url_cetak,
                            timeout: (5 * 1000),
                            data: res['response']['data']['cetak'],
                            success: function (res){
                                if (defaultAntrian != undefined) {
                                    window.location.href = '/antrian?default=false';
                                } else {
                                    location.reload();
                                }
                            },
                            error: function () {
                                $.redirect("/antrian/dashboard/cetak-antrian-dashboard",{no_antrian:noantrian,jenisantrian_id:jenis_id,antrian_id:antrian_id,no_antrian_poli:no_antrian_poli, detail_id: detail_id, antrian_jenis_id: antrian_jenis_id});
                            }
                        });
                    }
                },
                error: function (res) {
                    $('body').find('.confirm-dialog-overlay').remove();
                    $('body').find('#confirm-dialog').remove();
                    var errMessage = 'Gagal Diproses';
                    var errText = 'Terjadi Kesalahan';
                    new PNotify({
                        title: errMessage,
                        text: errText,
                        addclass: 'alert alert-danger alert-arrow-right alert-styled-right',
                        type: 'danger'
                    });
                }
            });
        });
    })
    </script>

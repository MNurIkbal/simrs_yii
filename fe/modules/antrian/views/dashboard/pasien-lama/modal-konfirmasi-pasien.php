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

    .informasi-pasien {
      font-size: 14px;
    }

    .btn-confirm-pasien{
        transition-duration: 0.4s !important;
        background-color: #014d8a;
        color: #ffffff;
        font-weight: 500;
    }

    .btn-confirm-pasien:hover{
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
    <div class="row informasi-pasien">
        <div class="col-md-3">
            Nama Pasien
        </div>
        <div class="col-md-9">
            : <?=ArrayHelper::getValue($data, 'nama_pasien')?>
        </div>
        <div class="col-md-3">
            No Rekam Medik
        </div>
        <div class="col-md-9">
            : <?=ArrayHelper::getValue($data, 'no_rekam_medik')?>
        </div>
        <div class="col-md-3">
            NIK
        </div>
        <div class="col-md-9">
            : <?=ArrayHelper::getValue($data, 'no_identitas_pasien')?>
        </div>
        <div class="col-md-3">
            Jenis Kelamin
        </div>
        <div class="col-md-9">
            : <?=ArrayHelper::getValue($data, 'jenis_kelamin_nama')?>
        </div>
        <div class="col-md-3">
            Tempat Tanggal Lahir
        </div>
        <div class="col-md-9">
            : <?=ArrayHelper::getValue($data, 'tempat_lahir').', '.(ArrayHelper::getValue($data, 'tanggal_lahir') != null ? date('d-m-Y', strtotime(ArrayHelper::getValue($data, 'tanggal_lahir'))) : '') ?>
        </div>
        <div class="col-md-3">
            Alamat
        </div>
        <div class="col-md-9">
            : <?=ArrayHelper::getValue($data, 'alamat_pasien')?>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?=Html::button('Kembali',['class' => 'btn btn-default btn-sm', 'data-dismiss' => 'modal']); ?>
    <?=Html::submitButton('Selanjutnya', ['class' => 'btn btn-confirm-pasien btn-sm', 'id' => 'next-konfirmasi-data-pasien']); ?>
</div>

<script type="text/javascript">
    var pasienEnc = "<?=ArrayHelper::getValue($data,'pasien_id')?>";
    $(document).ready(function() {
        $('#next-konfirmasi-data-pasien').on('click', function() {
            let antrian_jenis_id = (new URL(document.location)).searchParams.get('jenis_id');
            window.location.href = '/antrian/dashboard/antrian-v2?id='+pasienEnc+'&jenis_id='+antrian_jenis_id;
        });
    });
</script>

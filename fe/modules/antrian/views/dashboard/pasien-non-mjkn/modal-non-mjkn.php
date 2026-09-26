<?php

use yii\helpers\Url;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\web\View;
use app\components\DocoConstants;

?>
<style type="text/css">
    .classMin {
        margin-left: -30px;
    }
    .modal-content {
        border-radius: 10px !important;
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
        margin-top: 20px;
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
    <div class="form-group">
        <?php
            $form = ActiveForm::begin([
                'id' => 'reservasi-mjkn-form',
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
                <?= $form->field($modelForm, 'kode_booking')->textInput([
                    'class' => 'form-control input-sm kode_booking',
                    'autocomplete' => "off",
                    'placeholder' => "Masukan Kode Booking"
                ]) ?>
            </div>
            <div class="modal-footer">
                <?=Html::button(\Yii::t('fe', 'Lanjutkan'), ['type'=> 'submit', 'class' => 'btn btn btn-lg btn-next', 'id' => 'cek-kode-booking']); ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#cek-kode-booking").bind('click');
        $("#cek-kode-booking").on("click", function (e) {
            e.preventDefault();
            let antrianJenisId = new URL(document.location).searchParams.get(
                "jenis_id"
            );
            let kodeBooking = $(".kode_booking").val();
            if (kodeBooking == "" || kodeBooking == null) {
                docoNotification("warning", "Perhatian!", "Kode Booking Belum Terisi!");
                return false
            }
            else {
                getDataPasienNonMjkn()
            }
        });

        $('#reservasinonmjkn-kode_booking').on('input', function (e) {
            $(this).val($(this).val().toUpperCase());
        })

        const getDataPasienNonMjkn = () => {
            var wrapperParents = $('#modal-pasien-non-mjkn')
            showLoader('Memuat Halaman...')
            $('#modal-pasien-non-mjkn .modal-content').docoLoad({
                url: '/antrian/dashboard/cek-kode-booking',
                dataType: 'html',
                data: {'kode_booking': $('.kode_booking').val()},
                success: function (data) {
                    hideLoader()
                    $('#modal-pasien-non-mjkn .modal-content').parents('.modal').modal('show')
                    wrapperParents.css('z-index', '1950')
                    wrapperParents.find('.modal-dialog').css('width', '800px')
                },
                error: function (res) {
                    let _response = JSON.parse(res?.responseText);
                    docoNotification('error', 'Proses Gagal!', _response?.response?.message);
                    hideLoader()
                }
            })
        }
    });
</script>

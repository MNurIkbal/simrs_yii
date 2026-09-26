<?php

use yii\helpers\ArrayHelper;
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

    .btn-selesai{
        transition-duration: 0.4s !important;
        background-color: #014d8a;
        color: #ffffff;
        font-weight: 500;
        margin-top: 20px;
    }

    .btn-selesai:hover{
        background-color: #014d8a;
        color: #ffffff;
        font-weight: 500;
    }
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<?php
    $form = ActiveForm::begin([
        'id' => 'pilih-penjamin-mjkn-form',
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
        'type' => ActiveForm::TYPE_VERTICAL,
        'formConfig' => [
            'labelSpan' => 3,
            'deviceSize' => ActiveForm::SIZE_SMALL
        ]
    ]);
?>
<div class="modal-body">
    <div class="form-group">
        <div class="row">
            <div class="col-md-3" style="font-size: 12px;">Nama Pasien</div>
            <div class="col-md-3" style="font-size: 12px;">: <?= ArrayHelper::getValue($pendaftaran, 'nama_pasien') ?></div>
        </div>
        <div class="row">
            <div class="col-md-3" style="font-size: 12px;">TTL - Usia</div>
            <div class="col-md-6" style="font-size: 12px;">: <?= $keterangan ?></div>
        </div>
        <div class="row">
            <div class="col-md-3" style="font-size: 12px;">Tanggal Reservasi</div>
            <div class="col-md-6" style="font-size: 12px;">: <?= $tanggalReservasi ?></div>
        </div>
        <div class="row">
            <div class="col-md-3" style="font-size: 12px;">Ruangan</div>
            <div class="col-md-6" style="font-size: 12px;">: <?= ArrayHelper::getValue($pendaftaran, 'ruangan_nama') ?></div>
        </div>
        <div class="row">
            <div class="col-md-3" style="font-size: 12px;">Dokter</div>
            <div class="col-md-6" style="font-size: 12px;">: <?= ArrayHelper::getValue($pendaftaran, 'nama_pegawai') ?></div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-3" style="font-size: 12px;">Penjamin <span style="color:red;">*</span></div>
            <div class="col-md-6">
                <?= $form->field($modelForm, 'penjamin_id')->dropDownList($listPenjamin,[
                    'class' => 'select2 penjamin_id',
                    'prompt' => "Pilih Penjamin"
                ])->label(false) ?>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?=Html::button(\Yii::t('fe', 'Kembali'), ['type'=> 'button', 'class' => 'btn btn-lg btn-kembali', 'style' => 'margin-top:20px;']); ?>
    <?=Html::button(\Yii::t('fe', 'Daftar'), ['type'=> 'submit', 'class' => 'btn btn-lg btn-selesai', 'id' => 'daftar-non-mjkn']); ?>
</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('.penjamin_id').select2();

        var randString = "<?= $randString ?>";
        var pendaftaran = '<?= json_encode($pendaftaran) ?>';
        var kodeBooking = "<?= $kodeBooking ?>";
        var antrianPoli = "<?= DocoConstants::ANTRIAN_POLI ?>";
        var urlDashboard = '/antrian/dashboard'
        var urlCetakTracer = `/antrian/dashboard/print-antrian-poli`;

        $('.btn-kembali').on('click', function(){
            $("#modal-pasien-non-mjkn").modal("hide");
        })

        function cetakTracer(pendaftaranId) {
            $("#modal-pasien-non-mjkn").modal("hide");
            $("#modal_backdrop").modal("hide");
            let antrian_jenis_id = (new URL(document.location)).searchParams.get('jenis_id');
            var fullUrl = `/antrian/dashboard/cetak-antrian-non-mjkn?jenisantrian_id=${antrian_jenis_id}&pendaftaran_id=${pendaftaranId}`
            window.location.replace(fullUrl)
        }

        $('#daftar-non-mjkn').on('click', async function(e){
            e.preventDefault();
            const $btn = $(this);

            $btn.data('original-text', $btn.text());
            $btn.prop('disabled', true).text("Loading ...");
            const originalText = $btn.data('original-text') || 'Daftar';
            
            let config = await $.getJSON("./../../json/setup.json")
            if (config.origin == "true") {
                var socket = io.connect(window.location.origin);
            } else {
                var socket = io.connect(config.ip+':'+config.port);
            }

            var payload = JSON.parse(pendaftaran)
                payload.penjamin_id = $('.penjamin_id').val()

            $(this).docoForm("click", {
                url: `${urlDashboard}/reservasi-non-mjkn`,
                type: "POST",
                dataType: "json",
                skipConfirm: true,
                skipSuccessNotif: true,
                data: {
                    kode_booking: kodeBooking,
                    penjamin_id: payload.penjamin_id,
                    payload: JSON.stringify(payload),
                    randString: randString
                },
                success: function (response) {
                    socket.on(`bulk-register:${randString}`, (res) => {
                        const _data = $.parseJSON(res);
                        const { registration_status, message, pendaftaran_id } = _data
                        
                        if(registration_status) {
                            docoNotification('success', 'Berhasil', message)
                            cetakTracer(pendaftaran_id)
                        }
                        else {
                            docoNotification('error', 'Error', message)
                        }

                        $btn.prop('disabled', false).text(originalText);
                    })
                },
            });
        })
    });
</script>

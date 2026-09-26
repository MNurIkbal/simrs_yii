<?php

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

$pasienId = ArrayHelper::getValue($dataView, 'pasien_id');
$noRm = ArrayHelper::getValue($dataView, 'no_rm');
$nama = ArrayHelper::getValue($dataView, 'nama');
$jenisKelamin = ArrayHelper::getValue($dataView, 'jenis_kelamin');
$dataPasien = "$noRm - $nama ($jenisKelamin)";
?>
<div class="modal-header bg-inverse">
    <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
    <h5 class="modal-title">Konfirmasi</h5>
</div>
<div class="modal-body">
    <div class="form-group">
        <h5 class="m-0">Pasien masih mempunyai program terapi !</h5>
        <p class="text-muted text-lg mr-0 ml-0 mb-0 mt-2">Pasien</p>
        <p class="m-0 text-lg"><?= $dataPasien ?></p>
    </div>
</div>
<div class="modal-footer">
    <button type="button" id="btn-cancel" class="btn btn-danger">
        Batalkan Pemeriksaan
    </button>
    <button type="button" id="btn-continue" class="btn btn-info">
        Lanjutkan Pemeriksaan
    </button>
</div>

<?php
$phpVars = [
    'pasienId' => $pasienId
];
$this->registerJsVar('modalAvailVars', $phpVars);
?>

<script type="text/javascript">
    var pasienId = modalAvailVars.pasienId
    async function callFisioConfirmation(isContinue) {
        const result = await $.ajax({
            url: `/ranap/pemeriksaan-rawat-inap/fisioterapi-show-modal-available-program`,
            type: `POST`,
            data: {
                is_continue: isContinue,
                pasien_id: pasienId
            },  
            success: function(res){
                docoNotification('success', res?.title, res?.text);
            }
        });
        $(`#modal_backdrop`).modal('hide');

    }
    $(document).ready(function() {
        $(`#btn-continue`).off(`click`);
        $(`#btn-cancel`).off(`click`);
        $(`#btn-continue`).on(`click`, function() {
            callFisioConfirmation(true);
        })
        $(`#btn-cancel`).on(`click`, function() {
            callFisioConfirmation(false);
        })
        // Auto Trigger Modal (Below) - Documentation (Example)
        // $("#btn-modal-fisioterapi-available-program").attr("data-target", "#modal_backdrop");
        // $("#btn-modal-fisioterapi-available-program").attr("data-toggle", "modal");
        // $("#btn-modal-fisioterapi-available-program").attr("action", "/ranap/pemeriksaan-rawat-inap/fisioterapi-show-modal-available-program");
        // $("#btn-modal-fisioterapi-available-program").trigger("click");
    });
</script>
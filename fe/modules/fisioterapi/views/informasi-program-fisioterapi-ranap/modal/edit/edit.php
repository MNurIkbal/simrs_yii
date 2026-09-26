<?php

use yii\web\View;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\DateTimePicker;
use app\components\DocoConstants;
use kartik\widgets\ActiveForm;
?>
<style>
    .table-left td {
        border-left: none !important;
        border-right: none !important;
    }

    .table-left th {
        border: none !important;
    }

    .table-right td {
        border-left: none !important;
        border-right: none !important;
    }

    .table-right th {
        border: none !important;
    }

    .modal-body {
        margin-top: -10px;
    }

    .content-right {
        margin-bottom: 20px;
    }

    .content-right .dataTables_wrapper .dataTables_scroll {
        overflow-x: hidden;
    }

    .content-right .dataTables_wrapper .dataTables_scroll {
        border: 0.1px solid #bbb;
    }

    .kv-datetime-remove {
        display: none !important;
    }
</style>


<?php
$form = ActiveForm::begin([
    'id' => 'order-penunjang-form',
    // 'type' => ActiveForm::TYPE_HORIZONTAL,
    'action' => 'update-data',
    'enableClientValidation' => false,
    'formConfig' => ['deviceSize' => ActiveForm::SIZE_SMALL]
]);
// echo Html::hiddenInput('tmp_ruangan_id', null, [
//     'id' => 'tmp-ruangan-id'
// ]);
// echo Html::hiddenInput('penjamin_id', $penjaminId, [
//     'id' => 'instruksipenunjang-penjamin_id'
// ]);
// echo Html::hiddenInput('kelaspelayanan_id', $kelaspelayananId, [
//     'id' => 'instruksipenunjang-kelaspelayanan_id'
// ]);
// echo Html::activeHiddenInput($model, 'pendaftaran_id');
// echo Html::activeHiddenInput($model, 'instalasi_id', [
//     'id' => 'instruksipenunjang-instalasi_id'
// ]);
// echo Html::activeHiddenInput($model, 'pasienadmisi_id');
// echo Html::activeHiddenInput($model, 'cppt_id');
// echo Html::activeHiddenInput($model, 'ruangan');
?>

<div class="modal-header">
    <button type="button" class="close close-modal-jadwal" data-dismiss="modal">&times;</button>
    <h5 class="modal-title text-bold">Edit Order Fisioterapi</h5>
</div>
<div class="modal-body form-modal-fisioterapi">
    <hr/>
    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-sm-6">
                    <span>Tanggal Permintaan : </span>
                    <span style="font-weight: bold;">24/25/2022</span>
                </div>
                <div class="col-sm-6">
                    <span>Diagnosa : </span>
                    <span style="font-weight: bold;">A0.7 - Balandidatisa</span>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12" style="margin-bottom: 5px">
            <hr>
            <p class="text-bold">Tabel Pemeriksaan unit penunjang</p>
            <button type='button' id="btn-tambah-pemeriksaan" style='margin-right: 5px' class='btn btn-labeled btn-info btn-xs' data-width="80%" data-href="edit-tambah-tindakan"><b><i class='fa fa-plus'></i></b> Tambah</button>
            <button type='button' id="btn-reset-pemeriksaan" style='margin-right: 5px' class='btn btn-labeled btn-danger btn-xs'><b><i class='fa fa-trash'></i></b> Kosongkan</button>
        </div>
        <div class="col-sm-12">
            <div class="table-responsive">
                <table class="table table-hover" id="tbl-order-penunjang">
                    <thead>
                        <tr class="bg-inverse">
                            <th>No</th>
                            <th>Jenis Pemeriksaan</th>
                            <th>Nama Pemeriksaan</th>
                            <th>Catatan</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="5" class="text-center no-data-row">Belum Ada Data yang Diinputkan</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer text-right">
    <button type='button' style='margin-right: 25px' id="btn-save-penunjang" class='btn btn-labeled btn-info btn-xs'><b><i class='fa fa-save'></i></b> Simpan</button>
</div>

<?php ActiveForm::end() ?>


<?php
$this->registerJs($this->render('js/edit.js'), View::POS_END);
?>
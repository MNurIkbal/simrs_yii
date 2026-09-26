<?php

use yii\helpers\Html;
use yii\web\View;
?>
<style>
    table.datatable.detail-rsp {
        border-bottom: 0 !important
    }
</style>
<div class="modal-header" style="background-color:#37474f; color: #ffffff" id="modal-history-tindakan">
    <button type="button" class="close close-modal-riwayat-tindakan" style="color: #ffffff;" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Riwayat Tindakan</h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered" style="width: 1158px;" id="table-history-tindakan">
                <thead>
                    <tr class="bg-inverse">
                        <th></th>
                        <th class="text-center">Tanggal Tindakan</th>
                        <th class="text-center">Nama Tindakan/Paket</th>
                        <th class="text-center">Dokter Pemeriksa</th>
                        <th class="text-center">Dokter Delegasi</th>
                        <th class="text-center">Perawat 1</th>
                        <th class="text-center">Perawat 2</th>
                        <th class="text-center">Qty</th>
                        <th class="text-center">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="9" class="text-center">Data tidak tersedia.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$this->registerJs($this->render('js/_history_tindakan.js'), View::POS_END);
?>

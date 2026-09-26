<?php

use yii\helpers\Html;
use yii\web\View;
?>
<style>
    table.datatable.detail-rsp {
        border-bottom: 0 !important
    }
</style>
<div class="modal-header" style="background-color:#37474f; color: #ffffff" id="modal-history">
    <button type="button" class="close close-modal-riwayatresep" style="color: #ffffff" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Riwayat Resep</h5>
</div>
<div class="modal-body">
    <div class="row">
        <table class="table table-bordered" style="margin-bottom: 8px;" id="table-history-resep">
            <thead>
                <tr class="bg-inverse">
                    <!-- <th class="text-center"></th> dikomen dulu karna fitur multiple belom dibuat hehe -->
                    <th class="text-center">Tanggal Resep</th>
                    <th class="text-center">Tipe</th>
                    <th class="text-center">Kronis</th>
                    <th class="text-center">Dokter</th>
                    <th class="text-center">Instalasi / Ruangan</th>
                    <th class="text-center">Signa</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" class="text-center">Data tidak tersedia.</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$this->registerJs($this->render('js/_history_resep.js'), View::POS_END);
?>

<?php

use yii\web\View;
use yii\helpers\Url;
use app\components\DocoHelpers;
use app\components\widgets\DocoTableWidget;
?>
<!-- Modal Header -->
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Pilih Program</h5>
</div>
<!-- Modal Body -->
<div class="modal-body">
    <div class="row">
        <div class="col-md-12 table_program_terapi_wrapper">
            <?= DocoHelpers::generateToolbar([
                'periksa' => [
                    'title' => 'Periksa',
                    'icon' => 'fa fa-stethoscope',
                    'attributes' => [
                        'id' => 'btn-periksa-ranap-modal',
                        'data-options' => 'click',
                        'disabled' => true
                    ]
                ]
            ], 'table_program_terapi_wrapper') ?>
            <table id="table_pilih_program_ranap" class="table table-striped table-condensed table-hover" style="width: 100%;">
                <thead>
                    <tr class="bg-inverse">
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center" colspan="10">Data tidak ditemukan</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$jsVar = [
    'pasien_id' => $pasien_id,
    'pendaftaran_id' => $pendaftaran_id,
    'jenis_pelayanan' => $jenis_pelayanan
];
$this->registerJsVar("jsVar", $jsVar);
$this->registerJs($this->render("js/modal_pilih_program_ranap.js"), View::POS_END, "js");

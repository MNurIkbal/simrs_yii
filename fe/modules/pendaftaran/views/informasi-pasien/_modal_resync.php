<?php

use yii\helpers\Html;
use yii\web\View;
use app\components\DocoConstants;
use app\components\DocoHelpers;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close close-modal-sync" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12" style="padding: 10px;">
            <button type="button" class="btn-modal-sync close-modal-pemeriksaan btn btn-info btn-labeled btn-xs" data-options="click"><b><i class="fa fa-refresh"></i></b>
            <?=Yii::t('fe', 'Sync')?></button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">

            <div class="panel-body">
                <div class="advanced-filter"></div>
                <table id="example" class="table table-condensed" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="5%"></th>
                            <th width="1"><?= \Yii::t("fe", "No"); ?></th>
                            <th><?= \Yii::t("fe", "No Rekam Medik"); ?></th>
                            <th><?= \Yii::t("fe", "No Pendaftaran"); ?></th>
                            <th><?= \Yii::t("fe", "Nama Pasien"); ?></th>
                            <th><?= \Yii::t("fe", "Ruangan"); ?></th>
                            <th><?= \Yii::t("fe", "Dokter"); ?></th>
                            <th><?= \Yii::t("fe", "Status"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="8"><?= \Yii::t("fe", "Data tidak ditemukan."); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer text-left">

</div>

<?php
$this->registerJs($this->render("../../../../web/js/dataTables.checkboxes.min.js"), View::POS_END);
$this->registerJs("
var table;
var data;

$(document).ready(function() {
    table = $('#example').DataTable({
        displayLength: 10,
        filter: true,
        columnDefs: [ {
            orderable: false,
            // className: 'select-checkbox',
            targets:   0,
            checkboxes: {
                selectRow: true,
                stateSave: false,
                selectAllPages: true
            }
        }],
        select: {
            style: 'multi',
            selector: 'tr'
        },
        processing: true,
        serverSide: true,
        ajax: baseUrl+'pendaftaran/informasi-pasien/get-data-sync',
        columns: [
            {
                data: 'id',
                searchable: false,
                orderable: false,
                defaultContent: '',
            },
            {
                data: 'rowNum',
                name : 'No',
                searchable: false,
                orderable: false
            },
            {
                data: 'no_rekam_medik',
                name : 'No Rekam Medik',
                searchable: false,
                orderable: false,
            },
            {
                name : 'No Pendaftaran',
                data: 'no_pendaftaran',
                searchable: false,
                orderable: false,
            },
            {
                name: 'Nama Pasien',
                data: 'nama_pasien',
                searchable: false,
                orderable: false,
                defaultContent: '',
            },
            {
                name: 'Ruangan Tujuan',
                data: 'ruangan_nama',
                searchable: false,
                orderable: false,
                defaultContent: '',
            },
            {
                name: 'Dokter',
                data: 'nama_pegawai',
                searchable: false,
                orderable: false,
                defaultContent: '',
            },
            {
                name: 'Satatus',
                data: 'is_sync',
                searchable: false,
                orderable: false,
                defaultContent: '',
            },
        ],
    });

    $('.dataTables_filter').hide();

    $('.filter-form').datatableBootstrapFilter(table,
        [
        ], {}, true
    );

});

$(document).on('click', '.close', function() {
    location.reload()
});

$(document).on('click', '.btn-modal-sync', function() {
    let data = table.column(0).checkboxes.selected().toArray();

    if (typeof table.row('.selected').data() === 'undefined') {
        docoNotification('warning', 'Terjadi Kesalahan', 'Belum ada data yang dipilih!');
        return true;
    }

    $.ajax({
        type: 'POST',
        url: baseUrl+'pendaftaran/informasi-pasien/re-sync',
        data: {
            data: data,
        },
        dataType: 'JSON',
        success: function (res) {
            docoNotification('success', 'Success', 'Sinkronisasi sedang dilakukan');
            setTimeout(function() {
                location.reload()
            }, 5000);
        }
    });
})
", View::POS_END, 'jsModal')
?>
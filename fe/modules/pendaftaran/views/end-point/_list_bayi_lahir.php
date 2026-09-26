<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\web\View;
use yii\helpers\Url;
use app\components\DocoHelpers;
?>

<?php
$form = ActiveForm::begin([
    'id' => 'ajax-form',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]);
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="panel-toolbar clearfix">
        <?= DocoHelpers::generateToolbar([
            'search',
            'reset'=> [
                'attributes'=>[
                    'data-parent' => '.filter-form'
                ]
            ],
            'pilih-bayi' => [
                'title' => 'Pilih',
                'icon' => 'fa fa-check',
                'attributes' => [
                    'data-options' => 'click',
                ]
            ],
        ]) ?>
    </div><br>
    <div class="row">
        <div class="advanced-filter">
        </div>
        <table id="table-bayi" class="table table-striped table-condensed table-hover table-pilih-bayi" style="width:100%">
            <thead>
                <tr class="bg-inverse">
                    <th width="1">&nbsp;</th>
                    <th width="80">No</th>
                    <th><?=\Yii::t("fe", "No Rekam Medik");?></th>
                    <th><?=\Yii::t("fe", "Bayi");?></th>
                    <th><?=\Yii::t("fe", "Berat Badan");?></th>
                    <th><?=\Yii::t("fe", "Panjang Badan");?></th>
                    <th><?=\Yii::t("fe", "Jenis Kelamin");?></th>
                    <th><?=\Yii::t("fe", "Kondisi Bayi");?></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<?php

 $this->registerJs("
    // Global Var
    var pendaftaran_id = '".$pendaftaran_id."';
    var table;

    var emptyTable = '".(\Yii::t("fe", "Tidak ada data yang tersedia"))."';
    var info = '".(\Yii::t("fe", "Menampilkan _START_ sampai _END_ dari _TOTAL_ data"))."';
    var infoEmpty = '".(\Yii::t("fe", "Menampilkan 0 sampai 0 dari 0 data"))."';
    var infoFiltered = '".(\Yii::t("fe", "(disaring dari _MAX_ total data)"))."';
    var lengthMenu = '".(\Yii::t("fe", "Menampilkan _MENU_ data"))."';
    var loadingRecords = '".(\Yii::t("fe", "Memuat..."))."';
    var processing = '".(\Yii::t("fe", "Memproses..."))."';
    var search = '".(\Yii::t("fe", "Cari:"))."';
    var zeroRecords = '".(\Yii::t("fe", "Tidak ada data yang ditemukan"))."';
    var sortAscending = '".(\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terkecil ke yang terbesar"))."';
    var sortDescending = '".(\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terbesar ke yang terkecil"))."';

    $('.btn-pilih-bayi').on('click', function(){
        var listTable = table.row('.selected').data();
        var kelahiranbayi_id = listTable.kelahiranbayi_id;
        $('#modal_backdrop').modal('toggle');
        $('.kelahiranbayi-id').val(kelahiranbayi_id);
        getDataBayi(pendaftaran_id);
        $('#propinsi_id').trigger('change');
    });

    // Event Reload
    $(document).on('click', '.data-reload', function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $('#table-bayi').docoTabel({
            filter: false,
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   0
            }],
            select: {
                style:    'os',
                selector: 'tr'
            },
            sorting: [[3, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+'pendaftaran/end-point/get-data-bayi?pendaftaran_id=' + pendaftaran_id,
            language: {
                emptyTable: emptyTable,
                info: info,
                infoEmpty: infoEmpty,
                infoFiltered: infoFiltered,
                lengthMenu: lengthMenu,
                loadingRecords: loadingRecords,
                processing: processing,
                search: search,
                zeroRecords: zeroRecords,
                aria: {
                    sortAscending: sortAscending,
                    sortDescending: sortDescending
                }
            },
            columns: [
                {
                    title: '', 
                    data: null, 
                    defaultContent: '', 
                    searchable: false, 
                    orderable: false,
                    width: '10%'
                },
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(\Yii::t('fe', 'No Rekam Medik'))."', data: 'no_rekam_medik', searchable: false},
                {title: '".(\Yii::t('fe', 'Bayi'))."', data: 'bayi_urut', searchable: false},
                {title: '".(\Yii::t('fe', 'Berat Badan'))."', data: 'berat_badan', searchable: false},
                {title: '".(\Yii::t('fe', 'Panjang Badan'))."', data: 'tinggi_badan', searchable: false},
                {title: '".(\Yii::t('fe', 'Jenis Kelamin'))."', data: 'jeniskelamin_bayi', searchable: false},
                {title: '".(\Yii::t('fe', 'Kondisi Bayi'))."', data: 'kondisi_bayi'},
            ]
        });

        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table);
        
    });

        ", View::POS_END, 'js-kuning');

?>
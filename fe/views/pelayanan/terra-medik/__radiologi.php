<?php
use app\components\DocoConstants;
use yii\bootstrap\Modal;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DateTimePicker;
?>

<div class="panel-body">
    <div class="row">
        <table class="table table-bordered datatable-basic dataTable tb-terra-medik" id="tb-terra-radiologi" style="width:100%;">
            <thead>
                <tr class="bg-inverse">
                    <th width="8px">No</th>
                    <th><?= Yii::t('fe', 'No RM') ?></th>
                    <th><?= Yii::t('fe', 'Nama Pasien') ?></th>
                    <th><?= Yii::t('fe', 'No Resep') ?></th>
                    <th><?= Yii::t('fe', 'Detail') ?></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<?php

$this->registerJs("
    var pasien_terra = '$pasien_terra';

    var detail_radiologi = [];

    var radiologi_columns = ['No', 'Jenis Periksa', 'Dokter Radiologi', 'Diagnosa', 'Diagnosa Klinik'];

    // Tabel
    var tabel_radiologi = null;

    // Initiate page
    $('#tab-radiologi').on('click', function () {
        let formWrapper = $(`.body-history-terra .filter-forms`);

        if (tabel_radiologi == null) {
            // Generate Table
            tabel_radiologi = $('#tb-terra-radiologi').docoTabel({
                filter: false,
                displayLength: 10,
                stateSave: false,
                processing: true,
                serverSide: true,
                destroy : true,
                cacheFilter: false,
                paging: true,
                ajax: {
                    url : url +'/get-data-terra-radiologi?pasien_terra=' + pasien_terra,
                    data : {
                        advancedFilter: serializeArrayToJson(formWrapper),
                    }
                },
                columns: [
                    {
                        title: 'No',
                        data: 'no',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: 'No Rekam Medik',
                        data: 'no_rm',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: 'Tanggal Order - Selesai',
                        data: 'tgl_order',
                        searchable: false,
                        orderable: false,
                        render: function (data, type, row, meta) {
                            let tanggal_order = row.tgl_order != null ? convertDateByFormat(row.tgl_order, 'd/M/Y') : ' - ';
                            let tanggal_selesai = row.tgl_selesai != null ? convertDateByFormat(row.tgl_selesai, 'd/M/Y') : ' - ';
                            let html = tanggal_order+' - '+tanggal_selesai;
                            return html;
                        }
                    },
                    {
                        title: 'Dokter Perujuk',
                        data: 'doctor_perujuk',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: 'Detail',
                        data: 'detail',
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row, meta) {
                            detail_radiologi[meta.row] = data;
                            let html = '<button type=\"button\" class=\"btn btn-sm btn-success show-detail\"><i class=\"fa fa-plus-square-o\"></i></button>';
                            return html;
                        }
                    },
                ],
                initComplete: function( settings, json ) {

                }
            });

            $('#tb-terra-radiologi').on('click', '.show-detail', ({currentTarget}) => {
                if ($('#tb-terra-radiologi tbody tr').length > 0) {
                    let _this = $(currentTarget);
                    let data = {data : detail_radiologi, columns : radiologi_columns};
                    processExpandDetail(_this, data);
                }
            });
        }

    });

", View::POS_END, 'js-radiologis');

?>

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
        <table class="table table-bordered datatable-basic dataTable tb-terra-medik" id="tb-terra-obat" style="width:100%;">
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

    var detail_resep = [];

    var obat_columns =  ['No', 'Nama Obat', 'Satuan', 'Jumlah', 'Signa'];

    // Tabel
    var tabel_obat = null;

    // Initiate page
    $('#tab-obat').on('click', function () {
        let formWrapper = $(`.body-history-terra .filter-forms`);

        if (tabel_obat == null) {
            // Generate Table
            tabel_obat = $('#tb-terra-obat').docoTabel({
                filter: false,
                displayLength: 10,
                stateSave: false,
                processing: true,
                serverSide: true,
                destroy : true,
                cacheFilter: false,
                paging: true,
                ajax: {
                    url : url +'/get-data-terra-resep?pasien_terra=' + pasien_terra,
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
                        title: 'Nama Dokter',
                        data: 'nama_dokter',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: 'No Resep',
                        data: 'no_resep',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: 'Detail',
                        data: 'detail',
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row, meta) {
                            detail_resep[meta.row] = data;
                            html = '<button type=\"button\" class=\"btn btn-sm btn-success show-detail\"><i class=\"fa fa-plus-square-o\"></i></button>';
                            return html;
                        }
                    },
                ],
                initComplete: function( settings, json ) {

                }
            });
        }

        $('#tb-terra-obat').on('click', '.show-detail', ({currentTarget}) => {
            if ($('#tb-terra-obat tbody tr').length > 0) {
                let _this = $(currentTarget);
                let data = {data : detail_resep, columns : obat_columns};
                processExpandDetail(_this, data);
            }
        });

    });

", View::POS_END, 'js-obats');
?>

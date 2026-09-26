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
?>

<div class="panel-body">
    <div class="row">
        <table class="table table-bordered datatable-basic dataTable tb-terra-medik" id="tb-terra-mcu" style="width:100%;">
            <thead>
                <tr class="bg-inverse">
                    <th width="10%">No</th>
                    <th><?= Yii::t('fe', 'No Pendaftaran') ?></th>
                    <th><?= Yii::t('fe', 'Paket Pemeriksaan') ?></th>
                    <th><?= Yii::t('fe', 'Hasil Pemeriksaan') ?></th>
                    <th><?= Yii::t('fe', 'Aksi') ?></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<?php

$this->registerJs("
    var pasien_terra = '$pasien_terra';

    var detail_mcu = [];

    var mcu_columns = ['No', 'Nama Pemeriksaan', 'Daftar Pemeriksaan MCU'];


    // Tabel
    var tabel_mcu = null;

    // Initiate page
    $('#tab-mcu').on('click', function () {
        let formWrapper = $(`.body-history-terra .filter-forms`);

        if (tabel_mcu == null) {
            // Generate Table
            tabel_mcu = $('#tb-terra-mcu').docoTabel({
                filter: false,
                displayLength: 10,
                stateSave: false,
                processing: true,
                serverSide: true,
                destroy : true,
                cacheFilter: false,
                paging: true,
                ajax: {
                    url : url +'/get-data-terra-mcu?pasien_terra=' + pasien_terra,
                    data : {
                        advancedFilter: serializeArrayToJson(formWrapper),
                    }
                },
                columns: [
                    {
                        title: 'No',
                        data: 'no',
                        width: '10%',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: 'No Pendaftaran',
                        data: 'regis_id',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: 'Paket Pemeriksaan',
                        data: 'paket_pemeriksaan',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: 'Dokter Koordinator',
                        data: 'dokter_koordinator',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: 'Detail',
                        data: 'detail',
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row, meta) {
                            detail_mcu[meta.row] = data;
                            let html = '<button type=\"button\" class=\"btn btn-sm btn-success show-detail\"><i class=\"fa fa-plus-square-o\"></i></button>';
                            return html;
                        }
                    },
                ],
                initComplete: function( settings, json ) {

                }
            });

            $('#tb-terra-mcu').on('click', '.show-detail', ({currentTarget}) => {
                if ($('#tb-terra-mcu tbody tr').length > 0) {
                    let _this = $(currentTarget);
                    let data = {data : detail_mcu, columns : mcu_columns};
                    processExpandDetail(_this, data);
                }
            });
        }
    });

", View::POS_END, 'js-mcu');
?>

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
        <table id="tb-terra-resumemedis" class="table table-bordered datatable-basic dataTable tb-terra-resumemedis" style="width:100%;">
            <thead>
                <tr class="bg-inverse">
                    <th width="8px">No</th>
                    <th><?= Yii::t('fe', 'No Pendaftaran') ?></th>
                    <th><?= Yii::t('fe', 'Tanggal Pendaftaran') ?></th>
                    <th><?= Yii::t('fe', 'Dokter DPJP') ?></th>
                    <th><?= Yii::t('fe', 'Diagnosa') ?></th>
                    <th><?= Yii::t('fe', 'Anamnesa') ?></th>
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

    var detail_resumemedis = [];

    var resumemedis_columns = [
        'No', 'Alergi', 'Pemeriksaan Fisik', 'Riwayat Penyakit', 'Indikasi Pasien Dirawat',
        'Lab', 'Rad', 'Lain-lain', 'Terapi dan Tindakan Medis', 'Konsultasi', 'Obat di RS',
        'Obat Pulang', 'Kondisi Keluar RS', 'Tindak Lanjut'
    ];

    // Tabel
    var tabel_resumemedis = null;

    // Initiate page
    $('#tab-resumemedis').on('click', function () {
        let formWrapper = $(`.body-history-terra .filter-forms`);

        if (tabel_resumemedis == null) {
            // Generate Table
            tabel_resumemedis = $('#tb-terra-resumemedis').docoTabel({
                filter: false,
                displayLength: 10,
                stateSave: false,
                processing: true,
                serverSide: true,
                destroy : true,
                cacheFilter: false,
                paging: true,
                ajax: {
                    url : url +'/get-data-terra-resumemedis?pasien_terra=' + pasien_terra,
                    data : {
                        advancedFilter : serializeArrayToJson(formWrapper),
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
                        title: 'No Pendaftaran',
                        data: 'regis_id',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: 'Tanggal Pendaftaran',
                        data: 'regdate',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: 'Dokter DPJP',
                        data: 'dpjp',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: 'Diagnosa',
                        data: 'diagnosa',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: 'Anamnesa',
                        data: 'anamnesa',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: 'Detail',
                        data: 'detail',
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row, meta) {
                            detail_resumemedis[meta.row] = data;
                            let html = '<button type=\"button\" class=\"btn btn-sm btn-success show-detail\"><i class=\"fa fa-plus-square-o\"></i></button>';
                            return html;
                        }
                    },
                ],
                initComplete: function( settings, json ) {

                }
            });

            $('#tb-terra-resumemedis').on('click', '.show-detail', ({currentTarget}) => {
                if ($('#tb-terra-resumemedis tbody tr').length > 0) {
                    let _this = $(currentTarget);
                    let data = {data : detail_resumemedis, columns : resumemedis_columns};
                    processExpandDetail(_this, data);
                }
            });
        }
    });

", View::POS_END, 'js-resumemedis');

?>

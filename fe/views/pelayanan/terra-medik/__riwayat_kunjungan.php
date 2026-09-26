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
        <table class="table table-bordered datatable-basic dataTable tb-terra-medik" id="tb-terra-riwayatkunjungan" style="width:100%;">
            <thead>
                <tr class="bg-inverse">
                    <th>No</th>
                    <th><?= Yii::t('fe', 'Tanggal Pendaftaran') ?></th>
                    <th><?= Yii::t('fe', 'No Pendaftaran') ?></th>
                    <th><?= Yii::t('fe', 'Instalasi/Kamar/No Kamar') ?></th>
                    <th><?= Yii::t('fe', 'Dokter') ?></th>
                    <th><?= Yii::t('fe', 'Catatan Perujuk') ?></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<?php

$this->registerJs("
    var pasien_terra = '$pasien_terra';

    var detail_riwayatkunjungan = [];

    // Tabel
    var tabel_riwayatkunjungan = null;

    // Initiate page
    $('#tab-riwayatkunjungan').on('click', function () {
        let formWrapper = $(`.body-history-terra .filter-forms`);
        console.log('tab-riwayatkunjungan');
        console.log('yakali');

        if (tabel_riwayatkunjungan == null) {
            // Generate Table
            tabel_riwayatkunjungan = $('#tb-terra-riwayatkunjungan').docoTabel({
                filter: false,
                displayLength: 10,
                stateSave: false,
                processing: true,
                serverSide: true,
                destroy : true,
                cacheFilter: false,
                paging: true,
                ajax: {
                    url : url +'/get-data-riwayat-kunjungan?pasien_terra=' + pasien_terra,
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
                        title: 'Tanggal Pendaftaran',
                        data: 'tgl_pendaftaran',
                        width: '15%',
                        searchable: false,
                        orderable: false,
                        render : function (data, type, row){
                            return data ? convertDateByFormat(data, 'd/M/Y h:i:s') : ' - ' ;
                        }
                    },
                    {
                        title: 'No Pendaftaran',
                        data: 'pendaftaranold_id',
                        width: '15%',
                        searchable: false,
                        orderable: false
                    },

                    {
                        title: 'Instalasi/Kamar/No Tempat Tidur',
                        data: 'pendaftaranold_id',
                        width: '15%',
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row, meta){
                            let html = '';
                            html += '<b>Instalasi : </b>' + (row.instalasi_nama ? row.instalasi_nama : ' - ') + '<br>';

                            if(row.tipe_pendaftaran == 'IP'){
                                html += '<b>Kamar : </b>' + (row.kamar ? row.kamar : ' - ' )+ '<br>';
                                html += '<b>No Tempat Tidur : </b>' + (row.no_tempat_tidur ? row.no_tempat_tidur : ' - ') + '<br>';
                            }
                            return html;
                        }
                    },
                    {
                        title: 'Dokter',
                        width: '15%',
                        data: 'dokter_nama',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: 'Catatan',
                        width: '30%',
                        data: 'catatan_perujuk',
                        searchable: false,
                        orderable: false,
                    },
                ],
                initComplete: function( settings, json ) {

                }
            });
        }

    });

", View::POS_END, 'js-riwayatkunjungan');

?>

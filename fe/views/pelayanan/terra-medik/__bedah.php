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
        <table class="table table-bordered datatable-basic dataTable tb-terra-medik" id="tb-terra-bedah" style="width:100%;">
            <thead>
                <tr class="bg-inverse">
                    <th width="10%">No</th>
                    <th><?= Yii::t('fe', 'No Pendaftaran') ?></th>
                    <th><?= Yii::t('fe', 'Tanggal Tindakan') ?></th>
                    <th><?= Yii::t('fe', 'Tanggal Selesai') ?></th>
                    <th><?= Yii::t('fe', 'Nama Tindakan') ?></th>
                    <th><?= Yii::t('fe', 'Jenis Pembedah') ?></th>
                    <th><?= Yii::t('fe', 'Tenaga Medis') ?></th>
                    <th width="10%"><?= Yii::t('fe', 'Aksi') ?></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<?php

$this->registerJs("
    var pasien_terra = '$pasien_terra';

    var detail_bedah = [];

    var bedah_columns = ['No', 'Log Operasi', 'Post Operasi', 'Jaringan Incisi', 'Luas Operasi', 'Pemeriksaan Patologi Anatomi', 'Hasil'];

    // Tabel
    var tabel_bedah = null;

    // Initiate page
    $('#tab-bedah').on('click', function () {
        let formWrapper = $(`.body-history-terra .filter-forms`);

        if (tabel_bedah == null) {
            // Generate Table
            tabel_bedah = $('#tb-terra-bedah').docoTabel({
                filter: false,
                stateSave: false,
                displayLength: 10,
                processing: true,
                serverSide: true,
                destroy : true,
                cacheFilter: false,
                paging: true,
                ajax: {
                    url : url +'/get-data-terra-bedah?pasien_terra=' + pasien_terra,
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
                        title: 'Tanggal Tindakan',
                        data: 'tgl_tindakan',
                        searchable: false,
                        orderable: false,
                        render: function (data, type, row, meta) {
                            let html = (data != null || data != '') ? convertDateByFormat(data, 'd/M/Y') : ' - ';
                            return html;
                        }
                    },
                    {
                        title: 'Tanggal Selesai',
                        data: 'tgl_selesai',
                        searchable: false,
                        orderable: false,
                        render: function (data, type, row, meta) {
                            let html = (data != null) ? convertDateByFormat(data, 'd/M/Y') : ' - ';
                            return html;
                        }
                    },
                    {
                        title: 'Nama Tindakan',
                        data: 'nama_tindakan',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: 'Jenis Pembedah',
                        data: 'jenis_pembedahan',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: 'Tenaga Medis',
                        data: 'tenaga_medis',
                        searchable: false,
                        orderable: false,
                        render: function (data, type, row, meta){
                            let html = '';
                            if(data instanceof Object){
                                html += (data.dokter_operator && data.dokter_operator != '' ) ? data.dokter_operator+'<br>' : '';
                                html += (data.dokter_anastesi && data.dokter_anastesi != '' ) ? data.dokter_anastesi+'<br>' : '';
                                html += (data.asisten_operator && data.asisten_operator != '' ) ? data.asisten_operator+'<br>' : '';
                                html += (data.asisten_anastesi && data.asisten_anastesi != '' ) ? data.asisten_anastesi+'<br>' : '';
                            }else{
                                html = ' - '
                            }

                            return html;
                        }
                    },
                    {
                        title: 'Detail',
                        data: 'detail',
                        searchable: false,
                        orderable: false,
                        width: '10%',
                        render: function(data, type, row, meta) {
                            detail_bedah[meta.row] = data;
                            let html = '<button type=\"button\" class=\"btn btn-sm btn-success show-detail\"><i class=\"fa fa-plus-square-o\"></i></button>';
                            return html;
                        }
                    },
                ],
                initComplete: function( settings, json ) {

                }
            });

            $('#tb-terra-bedah').on('click', '.show-detail', ({currentTarget}) => {
                if ($('#tb-terra-bedah tbody tr').length > 0) {
                    let _this = $(currentTarget);
                    let data = {data : detail_bedah, columns : bedah_columns};
                    processExpandDetail(_this, data);
                }
            });
        }
    });

", View::POS_END, 'js-bedah');
?>

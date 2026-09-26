<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\file\FileInput;
use app\components\DocoHelpers;
?>
<style type="text/css">
    .classMin {
        margin-left: -30px;
    }
    .modal-content {
        border-radius: 10px !important;
        padding: 10px;
    }
    .modal-header {
        padding: 10px 10px;
        border-top-right-radius: 10px !important;
        border-top-left-radius: 10px !important;
    }
    .modal-body {
        padding : 20px 20px;
    }
    .bg-inverse {
        background-color: #fff;
        border-color: #37474f;
        color: #01223b;
        border: none;
        font-size: 16px;
    }
    .modal-title {
        font-size: 16px;
        font-weight: 500;
        text-align: center;
    }
    .modal-content[class*=bg-] .modal-header .close, .modal-header[class*=bg-] .close {
        color: #01223b;
        font-size: 28px;
        font-weight: bold;
        line-height: 14px;
    }
    .card-carabayar {
        margin: auto;
        width: 100%;
        cursor: default;
    }
    .card-carabayar .card-name {
        margin-bottom: 12px !important;
        margin-top: 0px !important;
    }

    .pilih-rujukan{
        transition-duration: 0.4s !important;
        background-color: #014d8a;
        color: #ffffff;
        font-weight: 500;
    }

    .pilih-rujukan:hover{
        transition-duration: 0.4s !important;
        background-color: #014d8a;
        color: #ffffff;
        font-weight: 500;
    }

    .pilih-rencana{
        transition-duration: 0.4s !important;
        background-color: #014d8a;
        color: #ffffff;
        font-weight: 500;
    }

    .pilih-rencana:hover{
        transition-duration: 0.4s !important;
        background-color: #014d8a;
        color: #ffffff;
        font-weight: 500;
    }

    th { font-size: 13px; }
    td { font-size: 11px; }
</style>
<div class="modal-header bg-inverse">
    <?php
    $form = ActiveForm::begin([
        'id' => 'sync-form',
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => [
            'labelSpan' => 3,
            'deviceSize' => ActiveForm::SIZE_SMALL
        ],
        'options' => [
            'role' => 'form',
        ]
    ]);
    ?>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default panel-bordered">
                <div class="panel-heading">
                    <a href="#rencana-collapse" data-toggle="collapse" aria-expanded="true" aria-controls="rencana-collapse"><h6 class="panel-title">Konsultasi Poliklinik</h6></a>
                </div>
                <div class="panel-body collapse in" id="rencana-collapse">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table
                                    class="table table-striped table-condensed table-hover"
                                    id="table-pasien-routing-konsul" style="width: 100%">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th><?=Yii::t('fe', 'No Rujukan'); ?></th>
                                            <th><?=Yii::t('fe', 'Tanggal Rujukan'); ?></th>
                                            <th><?=Yii::t('fe', 'No. Kartu'); ?></th>
                                            <th><?=Yii::t('fe', 'Nama'); ?></th>
                                            <th><?=Yii::t('fe', 'PPK Perujuk'); ?></th>
                                            <th><?=Yii::t('fe', 'Subspesialis'); ?></th>
                                            <th><?=Yii::t('fe', 'Aksi'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        /** Datatable Rujukan */
        tableKonsul = $('#table-pasien-routing-konsul').docoTabel({
            displayLength: 10,
            processing: true,
            serverSide: true,
            ordering: false,
            order: [[1, 'asc']],
            ajax: baseUrl + 'antrian/dashboard/datatable-list-konsul?nomor_identitas='+$('[name="PasienRoutingRujukanForm[nomor]').val(),
            columns: [
                {
                    title: 'No',
                    searchable: false,
                    data: 'poliklinik_id',
                    orderable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                {
                    title: 'Tanggal Konsul Poli',
                    data: 'tgl_konsulpoli',
                    name: 'tgl_konsulpoli',
                    searchable: false,
                    orderable: false,
                    render: function(data, type, row, meta) {
                        return moment(data).format('DD/MM/YYYY HH:mm');
                    }
                },
                {
                    title: 'Poliklinik Asal',
                    data: 'nama_poli_asal',
                    name: 'nama_poli_asal',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: 'Poliklinik Tujuan',
                    data: 'nama_poli',
                    name: 'nama_poli',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: 'Dokter Konsul',
                    data: 'dokter_konsul',
                    name: 'dokter_konsul',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: 'Status Konsul',
                    data: 'status_konsul',
                    name: 'status_konsul',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: 'Aksi',
                    data: 'aksi',
                    name: 'aksi',
                    searchable: false,
                    orderable: false,
                    render: (data, type, row, meta) => {
                        return `<button class='btn btn-sm btn-next pilih-konsul'
                                    data-konsulpoli_id='${row.konsulpoli_id}'
                                    data-nama_dokter='${row.dokter_konsul}'
                                    data-nama_poli='${row.nama_poli}'>
                                     Pilih
                                </button>`;
                    }
                }
            ],
            drawCallback: (settings) => {
                $('.pilih-konsul').bind('click', ({delegateTarget}) => {
                    $('#antrianonsiteform-nomor_rujukan').val($(delegateTarget).data('no-rujukan'))
                    $('#modal-konsul-pasien-routing').modal('hide');
                    $('[name="PasienRoutingRujukanForm[konsulpoli_id]').val($(delegateTarget).data('konsulpoli_id'));
                    $('[name="PasienRoutingRujukanForm[nama_dokter]').val($(delegateTarget).data('nama_dokter'));
                    $('[name="PasienRoutingRujukanForm[nama_poli]').val($(delegateTarget).data('nama_poli'));
                })
            },
        });
        $('.dataTables_filter').hide();
    });
</script>

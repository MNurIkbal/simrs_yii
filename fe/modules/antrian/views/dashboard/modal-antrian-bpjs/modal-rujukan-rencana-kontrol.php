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
        color: #ffffff !important;
        font-weight: 500;
    }

    .pilih-rujukan:hover{
        transition-duration: 0.4s !important;
        background-color: #014d8a;
        color: #ffffff !important;
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
    <button type="button" class="close" onclick="$('#modal-rujukan-rencana').modal('hide');">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default panel-bordered">
                <div class="panel-heading active">
                    <a href="#rujukan-collapse" id="collapse-head-rujukan" data-toggle="collapse" aria-controls="rujukan-collapse" aria-expanded="true">
                        <h6 class="panel-title">Daftar Rujukan</h6>
                    </a>
                </div>
                <div class="panel-body collapse in" id="rujukan-collapse">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table
                                    class="table table-striped table-condensed table-hover"
                                    id="table-rujukan" style="width: 100%">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th><?=Yii::t('fe', 'No Rujukan'); ?></th>
                                            <th><?=Yii::t('fe', 'Tanggal Rujukan'); ?></th>
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
        <div class="col-md-12">
            <div class="panel panel-default panel-bordered">
                <div class="panel-heading">
                    <a href="#rencana-collapse" data-toggle="collapse" aria-expanded="true" aria-controls="rencana-collapse"><h6 class="panel-title">Rencana Kontrol</h6></a>
                </div>
                <div class="panel-body collapse in" id="rencana-collapse">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table
                                    class="table table-striped table-condensed table-hover"
                                    id="table-rencana-kontrol" style="width: 100%">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th><?=Yii::t('fe', 'No Rujukan'); ?></th>
                                            <th><?=Yii::t('fe', 'Tanggal Rujukan'); ?></th>
                                            <th><?=Yii::t('fe', 'PPK Perujuk'); ?></th>
                                            <th><?=Yii::t('fe', 'Subspesialis'); ?></th>
                                            <th><?=Yii::t('fe', 'Nama Dokter'); ?></th>
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
        <?php echo Html::hiddenInput('suggest_kode_poli' , null, ['id' => 'suggest-kode-poli']); ?>
        <?php echo Html::hiddenInput('suggest_kode_poli' , null, ['id' => 'no-surat-kontrol']); ?>
        <?php echo Html::hiddenInput('suggest_kode_dokter' , null, ['id' => 'suggest-kode-dokter']); ?>
        <?php echo Html::hiddenInput('suggest_nama_dokter' , null, ['id' => 'suggest-nama-dokter']); ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        /** Reset data Kontrol */
        __daftarKontrol = [];
        setTimeout(() => {
        $('.pilih-rujukan').prop('disabled', false);
        }, 5000);
        /** Datatable Rujukan */
        tableRujukan = $('#table-rujukan').docoTabel({
            displayLength: 10,
            processing: true,
            serverSide: false,
            ordering: false,
            ajax: baseUrl + 'antrian/dashboard/get-rujukan?jenis_identitas='+ $('input[name="AntrianOnsiteForm[jenis_identitas]"]:checked').val() +'&nomor_identitas=' + $('#antrianonsiteform-nomor_identitas').val(),
            columns: [
                {
                    title: 'No Rujukan',
                    data: 'no_rujukan',
                    name: 'no_rujukan',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: 'Tanggal Rujukan',
                    data: 'tgl_rujukan',
                    name: 'tgl_rujukan',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: 'PPK Perujuk',
                    data: 'ppk_perujuk',
                    name: 'ppk_perujuk',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: 'Subspesialis',
                    data: 'subspesialis',
                    name: 'subspesialis',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: 'Aksi',
                    data: 'aksi',
                    name: 'aksi',
                    searchable: false,
                    orderable: false,
                    render: (columnData, row, data) => {
                        return `<button class='btn btn-sm pilih-rujukan' data-kode-poli='${data.kode_poli}' data-no-rujukan='${data.no_rujukan}' data-asal-rujukan='${data.asal_rujukan}' disabled="disabled"> Pilih </button>`
                    }
                }
            ],
            drawCallback: (settings) => {
                $('.pilih-rujukan').bind('click', ({delegateTarget}) => {
                    var asalRujukan = $(delegateTarget).data('asal-rujukan')
                    var noRujukan = $(delegateTarget).data('no-rujukan')
                    $('#antrianonsiteform-nomor_rujukan').val($(delegateTarget).data('no-rujukan'))
                    $('#antrianonsiteform-nomor_surat_kontrol').parent().hide();
                    $('#antrianonsiteform-nomor_surat_kontrol').val(null);
                    $('#suggest-kode-dokter').val(null);
                    $('#suggest-nama-dokter').val(null);


                    let listRencanaKontrol = $('#table-rencana-kontrol').DataTable().rows(function(idx,data,node){
                        return data.no_rujukan == $(delegateTarget).data('no-rujukan')
                    }).data().toArray();

                    //sort by tglrujukan desc
                    listRencanaKontrol = listRencanaKontrol.sort((a, b) => {
                      return new Date(b.tgl_rujukan) - new Date(a.tgl_rujukan);
                    });

                    listRencanaKontrol = listRencanaKontrol.length > 0 ? listRencanaKontrol[0] : null;

                    if (docoHelper.isObjectNotEmptyAndNotNull(listRencanaKontrol) && new Date(listRencanaKontrol.tgl_rujukan).toDateString() < new Date().toDateString()) {
                        $(function(){
                            new PNotify({
                                title: "Perhatian",
                                text: "No Rujukan "+listRencanaKontrol.no_rujukan+" sudah pernah berkunjung dan Memiliki Rencana Kontrol dengan No Surat Kontrol "+listRencanaKontrol.no_surat_kontrol+" , Namun Surat kontrol tidak sesuai tanggal hari ini. <b>Silahkan Hubungi Administrasi Pendaftaran untuk penyesuaian Tanggal Kontrol.</b>",
                                addclass: "alert alert-warning alert-arrow-right alert-styled-right",
                                type: "warning"
                            });
                        });
                    } else {
                        $('#modal-rujukan-rencana').modal('hide');
                        let kodePoli = docoHelper.isObjectNotEmptyAndNotNull(listRencanaKontrol) ? listRencanaKontrol.kode_poli : $(delegateTarget).data('kode-poli');
                        let asalRujukan = $(delegateTarget).data('asal-rujukan');
                        let kodeDokter = docoHelper.isObjectNotEmptyAndNotNull(listRencanaKontrol) ? listRencanaKontrol.kode_dokter : null;
                        let namaDokter = docoHelper.isObjectNotEmptyAndNotNull(listRencanaKontrol) ? listRencanaKontrol.nama_dokter : null;
                        let noSuratKontrol = docoHelper.isObjectNotEmptyAndNotNull(listRencanaKontrol) ? listRencanaKontrol.no_surat_kontrol : null;

                        if (noSuratKontrol && noSuratKontrol.length > 0) {
                            $('#antrianonsiteform-nomor_surat_kontrol').val(noSuratKontrol);
                            $('#antrianonsiteform-nomor_surat_kontrol').parent().show();
                            $('.field-frm-antrian-dokter_id').hide();

                        }

                        if ($(delegateTarget).data('kode-poli') != undefined) {
                            $(`#frm-antrian-poli-tujuan-all option[value='${kodePoli}']`).prop("selected", true).change();
                            $('#poli-tujuan-selected').val(kodePoli);
                            $('#suggest-kode-poli').val(kodePoli);
                            $('#asal-rujukan').val(asalRujukan);
                            $('#suggest-kode-dokter').val(kodeDokter);
                            $('#suggest-nama-dokter').val(namaDokter);
                        }
                    }
                })
            },
        });
        $('.dataTables_filter').hide();

        /** Datatable Rencana Kontrol */
        tableRencanaKontrol = $('#table-rencana-kontrol').docoTabel({
            displayLength: 10,
            processing: true,
            serverSide: false,
            ordering: false,
            ajax: baseUrl + 'antrian/dashboard/get-rencana-kontrol?jenis_identitas='+ $('input[name="AntrianOnsiteForm[jenis_identitas]"]:checked').val() +'&nomor_identitas=' + $('#antrianonsiteform-nomor_identitas').val()+'&jenis_kontrol=2',
            columns: [
                {
                    title: 'No Rujukan / Rencana Kontrol',
                    data: 'rujuk_rencana',
                    name: 'no_rujukan',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: 'Tanggal Rujukan',
                    data: 'tgl_rujukan',
                    name: 'tgl_rujukan',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: 'PPK Perujuk',
                    data: 'ppk_perujuk',
                    name: 'ppk_perujuk',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: 'Subspesialis',
                    data: 'subspesialis',
                    name: 'subspesialis',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: 'Nama Dokter',
                    data: 'nama_dokter',
                    name: 'nama_dokter',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: 'Aksi',
                    data: 'aksi',
                    name: 'aksi',
                    searchable: false,
                    orderable: false,
                    render: (columnData, row, data) => {
                        return `<button class='btn btn-sm pilih-rencana' data-kode-dokter='${data.kode_dokter}' data-no-rujukan='${data.no_rujukan}' data-no-surat-kontrol='${data.no_surat_kontrol}' data-kode-poli='${data.kode_poli}' data-tgl-rujukan='${data.tgl_rujukan}'> Pilih </button>`
                    }
                }
            ],
            drawCallback: (settings) => {
                $($('#table-rencana-kontrol').DataTable().rows().data()).each((key, value) => {
                    if (typeof __daftarKontrol[value.no_rujukan] == 'undefined') {
                        __daftarKontrol[value.no_rujukan] = value.no_surat_kontrol
                    }
                });
                $('.pilih-rencana').bind('click', ({delegateTarget}) => {
                    let tglRujukan = $(delegateTarget).data('tgl-rujukan');
                    let tglRujukanConvert = new Date(tglRujukan);
                    let noSuratKontrol = $(delegateTarget).data('no-surat-kontrol');
                    let kodeDokter = $(delegateTarget).data('kode-dokter');
                    let namaDokter = $(delegateTarget).data('nama-dokter');
                    let today = new Date();
                    tglRujukanConvert.setHours(0, 0, 0, 0);
                    today.setHours(0, 0, 0, 0);
                    
                    if (tglRujukanConvert < today) {
                        $(function(){
                            new PNotify({
                                title: "Perhatian",
                                text: "No Surat Kontrol "+noSuratKontrol+" , tidak sesuai tanggal hari ini. <b>Silahkan Hubungi Administrasi Pendaftaran untuk penyesuaian Tanggal Kontrol",
                                addclass: "alert alert-warning alert-arrow-right alert-styled-right",
                                type: "warning"
                            });
                        });
                    } else {
                        $('#antrianonsiteform-nomor_rujukan').val($(delegateTarget).data('no-rujukan'));
                        $('#antrianonsiteform-nomor_surat_kontrol').val($(delegateTarget).data('no-surat-kontrol'));
                        $('#antrianonsiteform-nomor_surat_kontrol').parent().show();
                        $('#modal-rujukan-rencana').modal('hide');
                        let is_found = false;
                        if($(delegateTarget).data('kode-poli') != undefined){
                            var asalRujukan = $(delegateTarget).data('asal-rujukan')
                            var noRujukan = $(delegateTarget).data('no-rujukan')
                            $('#antrianonsiteform-nomor_rujukan').val($(delegateTarget).data('no-rujukan'))
                            $('#modal-rujukan-rencana').modal('hide');
                            let is_found = false;
                            if($(delegateTarget).data('kode-poli') != undefined){
                                $(`#frm-antrian-poli-tujuan-all option[value='${$(delegateTarget).data('kode-poli')}']`).prop("selected", true).change();
                                $('#poli-tujuan-selected').val($(delegateTarget).data('kode-poli'))
                                $('#asal-rujukan').val($(delegateTarget).data('asal-rujukan'))
                                $('#suggest-kode-poli').val($(delegateTarget).data('kode-poli'))
                                $('#no-surat-kontrol').val($(delegateTarget).data('no-surat-kontrol'))
                                $('#suggest-kode-dokter').val(kodeDokter);
                                $('#suggest-nama-dokter').val(namaDokter);
                                $('.field-frm-antrian-dokter_id').hide();
                            }
                        }
                    }
                })

                $('.pilih-rujukan').prop('disabled', false);
            },
        });
        $('.dataTables_filter').hide();
    });
</script>
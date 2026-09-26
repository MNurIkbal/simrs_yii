<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-07-12 16:00:39
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-10-31 12:49:05
 */
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use app\components\DocoHelpers;
use app\widgets\DHDatePickerWidget;
use app\widgets\DHDateRangePickerWidget;
?>

<?php 
$form = ActiveForm::begin([
    'id' => 'ajax-form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>

<style>
    .form-group.new-filter{
        margin-right: 10px;
    }
    .AnyTime-win{
        z-index: 99999;
    }
</style>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="panel-toolbar clearfix form-group">
        <?= DocoHelpers::generateToolbar([
            'search'
        ], '#list-pengajuan') ?>
        
        <!-- For Fixing Bugs (Async Element) -->
        <?= Html::button("<b><i class='fa fa-refresh'></i></b>".Yii::t('fe', 'Muat Ulang '), [
            'class' => 'btn btn-info btn-labeled btn-xs data-reset',
            'style' => 'display:none;'
        ]) ?>
        <?= Html::button("<b><i class='fa fa-refresh'></i></b>".Yii::t('fe', 'Muat Ulang '), [
            'class' => 'btn btn-info btn-labeled btn-xs data-reset-custom',
        ]) ?>
        <!-- End -->

        <?= Html::button("<b><i class='fa fa-plus'></i></b>".Yii::t('fe', 'Tambah '), [
            'class' => 'btn btn-info btn-labeled btn-xs',
            'id' => 'tambah-pengajuan',
            'data-parent' => $id
        ]) ?>
    </div>
    <hr>
    <div class="row">
        <div class="advanced-filter"></div>
    </div>
    <div class="row">
        <table id="list-pengajuan" class="table table-striped table-condensed table-hover" style="width:100%">
            <thead>
                <tr class="bg-inverse">
                    <th width="1"></th>
                    <th><?=\Yii::t("fe", "No");?></th>
                    <th><?=\Yii::t("fe", "Data Pasien");?></th>
                    <th><?=\Yii::t("fe", "No Pendaftaran");?></th>
                    <th><?=\Yii::t("fe", "No Rekam Medik");?></th>
                    <th><?=\Yii::t("fe", "No Invoice");?></th>
                    <th><?=\Yii::t("fe", "Tanggal Masuk");?></th>
                    <th><?=\Yii::t("fe", "Tanggal Keluar");?></th>
                    <th><?=\Yii::t("fe", "No SEP");?></th>
                    <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                    <th><?=\Yii::t("fe", "Instalasi / Ruangan");?></th>
                    <th><?=\Yii::t("fe", "Instalasi");?></th>
                    <th><?=\Yii::t("fe", "Ruangan");?></th>
                    <th><?=\Yii::t("fe", "Tagihan");?></th>
                    <th><?=\Yii::t("fe", "Jumlah Dibayarkan Pasien");?></th>
                    <th><?=\Yii::t("fe", "Jumlah Pengajuan");?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center" colspan="14"><?=\Yii::t("fe", "Data tidak ditemukan");?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php ActiveForm::end(); ?>

<?php
    $phpVars = [
        'form_filters' => [
            'tgl_keluar' => DHDateRangePickerWidget::widget()
        ]
    ];

    $this->registerJsVar('phpVars', $phpVars);
?>

<script type="text/javascript">
    var list_pengajuan;
    var _cacheTagihan = {};
    var {form_filters} = phpVars

    $(document).on(`click`, `.data-reset-custom`, function(e) {
        // For Fixing Bugs Async Element
        $(`.myDateCustom`).attr(`value`, '');
        $(`.data-reset`).trigger(`click`);
    });

    $(document).ready(function() {
        list_pengajuan = $("#list-pengajuan").docoTabel({
            filter: true,
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "multi",
                selector: "tr"
            },
            sorting: [], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            stateSave: false,
            ajax: baseUrl+"penjamin-asuransi/informasi-pengajuan-klaim/get-data-list-pasien?id=<?= $id ?>",
            columns: [
                {
                    title: "", 
                    data: null, 
                    defaultContent: "", 
                    searchable: false, 
                    orderable: false,
                    width: "1%"
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "Data Pasien",  
                    data: "nama_pasien", 
                    searchable: false, 
                    orderable: false,
                    render: (data, type, row, meta) => {
                        let namaPasien = row.nama_pasien
                        let noRm = row.no_rekam_medik
                        let noPendaftaran = row.no_pendaftaran
                        return "<b>" + namaPasien + "</b>" + "<br>" + noRm + "<br>" + noPendaftaran
                    }
                },
                {
                    title: "No Pendaftaran",
                    data: "no_pendaftaran",
                    searchable: true,
                    visible: false
                },
                {
                    title: "No Rekam Medik",
                    data: "no_rekam_medik", searchable: true,
                    visible: false
                },
                {
                    title: "No Invoice",
                    data: "no_pembayaran",
                    searchable: true
                },
                {
                    title: "Tanggal Masuk",
                    data: "tgl_pendaftaran",
                    searchable: false
                },
                {
                    title: "Tanggal Keluar",
                    data: "tglpasienpulang",
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return data ? data : "-"
                    }
                },
                {
                    title: "No SEP",
                    data: "nosep",
                    searchable: false,
                    render: (data, type, row, meta) => {
                        return data ? data : "-"
                    }
                },
                {
                    title: "Nama Pasien",
                    data: "nama_pasien",
                    searchable: true,
                    visible: false
                },
                {
                    title: "Instalasi / Ruangan",
                    data: "instalasi_nama",
                    searchable: false,
                    orderable: false,
                    render: (data, type, row, meta) => {
                        let instalasiNama = data
                        let ruanganNama = row.ruangan_nama
                        return "<b>" + instalasiNama + "</b>" + "<br>" + ruanganNama
                    }
                },
                {
                    title: "Instalasi",
                    data: "instalasi_nama",
                    searchable: false,
                    visible: false
                },
                {
                    title: "Ruangan",
                    data: "ruangan_nama",
                    searchable: false,
                    visible: false
                },
                {
                    title: "Tagihan", 
                    data: "total_tagihan_label", 
                    orderable: false,
                    searchable: false
                },
                {
                    title: "Jumlah Pasien Bayar", 
                    data: "total_sdh_bayar_label", 
                    orderable: false,
                    searchable: false
                },
                {
                    title: "Jumlah Pengajuan", 
                    data: "total_pengajuan_label", 
                    orderable: false,
                    searchable: false
                },
            ],
            scrollCollapse: true
        });

        $('.dataTables_filter').hide();
        
        generateFilter('', 'filter-form');
        
        $('.filter-form').datatableBootstrapFilter(list_pengajuan, [
            [7, form_filters.tgl_keluar],
        ], {
            7:0,
            3:1,
            5:2,
            9:3,
            4:4
        }, true);
    });

    $("#tambah-pengajuan").on("click", function (event) {
        event.preventDefault();
        var _idParnt = $(this).data('parent');
        var _count = Object.keys(_cacheTagihan).length;
        if (!_count) {
            docoNotification('error','Proses Gagal!', 'Tidak boleh kosong.'); 
            return false;
        }
        $().docoForm('click',{
            data : {
                data_pengajuan : JSON.stringify(_cacheTagihan)
            },
            url : '/penjamin-asuransi/informasi-pengajuan-klaim/simpan-tambah-pasien?id='+_idParnt,
            success : function (data) {
                $.each(_cacheTagihan,function (k,v) {
                    _totalPengajuan = parseInt(_totalPengajuan) + parseInt(v.piutang)
                });
                list_pengajuan.draw();
                table.draw();
                _cacheTagihan = {};
            }
        });
    })

    $(document).on("click", "#list-pengajuan tr", function(event){
        event.preventDefault();
        var tbl = $(this).hasClass("selected");
        var result = list_pengajuan.row(this).data();
        var cacheId = result.primary + '' +result.admisi; 
        if (tbl) {
            _cacheTagihan[cacheId] = {
                pasien_id : result.pasien_id,
                piutang : result.total_asuransi,
                pendaftaran_id: result.pendaftaran_id,
                pasienadmisi_id: (result.pasienadmisi_id == 0) ? null : result.pasienadmisi_id,
                pembayaranpelayanan_id: result?.pembayaranpelayanan_id
            };
        } else {
            delete _cacheTagihan[cacheId];
        }
    });
</script>
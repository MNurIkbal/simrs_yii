<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-07-11 14:53:35
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-08-02 18:53:31
 */

use yii\helpers\Html;
use yii\web\View;
use app\components\DocoConstants;
use app\components\DocoHelpers;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close close-modal-pemeriksaan" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12" style="padding: 10px;">
            <button type="button" class="btn-modal-add-pemeriksaan close-modal-pemeriksaan btn btn-info btn-labeled btn-xs" data-options="click"><b><i class="fa fa-plus"></i></b>
            <?=Yii::t('fe', 'Tambah')?></button>
        </div>
    </div>
    <div class="row">
        <?php $colSize = 'col-md-12'; ?>
        <?php if($instalasi_id != DocoConstants::INSTALASI_MCU) : $colSize = 'col-md-6' ?>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12 filter-jenis"></div>
                </div>
                <div class="panel-toolbar clearfix">
                    <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset' => ['attributes' => ['data-parent' => '.filter-jenis', 'class' => 'reset-on-modal-form btn btn-info btn-labeled btn-xs data-reset data-reload-tindakan']],
                    ], '#table-jenis');?>
                </div>
                <table id="table-jenis" class="table datatable-basic table-striped table-hover dataTable no-footer">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th><?=Yii::t('fe', 'No')?></th>
                            <th><?= Yii::t("fe", "Kode Pemeriksaan"); ?></th>
                            <th><?=Yii::t('fe', 'Jenis pemeriksaan')?></th>
                            <th><?=Yii::t('fe', 'Nama pemeriksaan')?></th>
                        </tr>
                    </thead>
                    <tbody> 
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        <div class="<?=$colSize?>">
            <div class="row">
                <div class="col-md-12 filter-paket"></div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset' => ['attributes' => ['data-parent' => '.filter-paket', 
                    'class' => 'btn btn-info btn-labeled btn-xs data-reload-paket']],
                ], '#table-paket');?>
            </div>
            <table id="table-paket" class="table datatable-basic table-striped table-hover dataTable no-footer" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th></th>
                        <th><?=Yii::t('fe', 'No')?></th>
                        <th><?= Yii::t("fe", "Detail"); ?></th>
                        <th><?= Yii::t("fe", "Kode Pemeriksaan"); ?></th>
                        <th><?=Yii::t('fe', 'Nama paket')?></th>
                        <th><?=Yii::t('fe', 'Nama pemeriksaan')?></th>
                    </tr>
                </thead>
                <tbody> 
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal-footer text-left">
    
</div>

<?php 
$this->registerJs($this->render('../../../../../web/js/dataTables.checkboxes.min.js'), View::POS_END);
$this->registerJs("
    var instalasi_id = '".$instalasi_id."';
    var penjaminId = (typeof _formPendaftaran.tipePasien.penjamin_id !== 'undefined') ? _formPendaftaran.tipePasien.penjamin_id : $('.selectPenjamin').val()
    var table_jenis
    var table_paket
    var state = false
    var localstorage = window.localStorage
    var params = 'ruangan_id=' +$('#ruangan_id').val()+ '&penjamin_id='+penjaminId+'&kelaspelayanan_id='+$('.selectKp').val()+'&instalasi_id='+$('.selectInstalasi').val()
    
    $(document).on('click', '.data-reload-paket', function () {
        // table_paket.checkboxes.deselectAll();
        localStorage.clear();
        // $('.advancedFilter [type=reset]').click();
        // $('.advancedFilterDo').click();
        table_paket.clear().destroy();
        generateDtPaket()
    });

    $(document).on('click', '.data-reload-tindakan', function () {
        // table_jenis.checkboxes.deselectAll();
        // table_jenis.columns().checkboxes.deselectAll();
        table_jenis.clear().destroy();
        generateDtTindakan()
    });

    var title = (instalasi_id == '".DocoConstants::INSTALASI_MCU."') ? 'Tambah Paket MCU' : 'Tambah Pemeriksaan '+$('.selectInstalasi').find(':selected').text();

    $(document).ready(function() {
        $('.modal-title').html(title)
        generateDtPaket()
        if($('#table-jenis').length) {
            generateDtTindakan()
        }
    })

    $('.btn-modal-add-pemeriksaan').on('click', function(e){
        e.preventDefault()
        var tindakan_row = []; 
        var tmpTindakan = []; 
        var tmpPaket = table_paket.column(0).checkboxes.selected().toArray();
        var paket_nama;

        if($('#table-jenis').length) {
            tmpTindakan = table_jenis.column(0).checkboxes.selected().toArray();
        }
        
        $.ajax({
            type: 'GET',
            url: baseUrl+'pendaftaran/daftar/list-tarif-tindakan-paket?'+params,
            data: {
                listPaket: tmpPaket,
                listTindakan: tmpTindakan
            },
            dataType: 'JSON',
            success: function (res) {
                var pemeriksaan_row = res;
                $.each(pemeriksaan_row, function(k,v){
                    if(instalasi_id == '".DocoConstants::INSTALASI_MCU."') { // untuk mcu
                        state = true;
                        v.namapakettindakan = v.tipepaket_nama;
                        paket_nama = v.namapakettindakan;
                        _formPendaftaran.listPenunjang[v.tipepaket_id] = v
                    }
                    else {
                        if(typeof _formPendaftaran.listPenunjang[v.tipepaket_id] === 'undefined') {
                            state = true
                            v.is_cyto = 'false'
                            v.namapemeriksaan = (typeof v.daftartindakan_nama !== 'undefined' && v.daftartindakan_nama != null) 
                                            ? v.daftartindakan_nama : (typeof v.pemeriksaanlab_nama !== 'undefined' && v.pemeriksaanlab_nama != null) 
                                            ? v.pemeriksaanlab_nama : '';
        
                            v.jenispemeriksaan = (typeof v.jenispemeriksaanlab_nama !== 'undefined' && v.jenispemeriksaanlab_nama != null) 
                                            ? v.jenispemeriksaanlab_nama : (typeof v.tipepaket_nama !== 'undefined' && v.tipepaket_nama != null) 
                                            ? v.tipepaket_nama : (typeof v.jenispemeriksaanrad_nama !== 'undefined' && v.jenispemeriksaanrad_nama != null) 
                                            ? v.jenispemeriksaanrad_nama : '';
                            _formPendaftaran.listPenunjang[v.tariftindakan_id] = v
                        }
                        else{
                            paket_nama = (typeof v.jenispemeriksaanlab_nama !== 'undefined') 
                                    ? v.jenispemeriksaanlab_nama : (typeof v.tipepaket_nama !== 'undefined') ? v.tipepaket_nama : '';
                            state = false
                            return false
                        }
                    }
                });
                if(state == false){
                    var msg = (instalasi_id == '".DocoConstants::INSTALASI_MCU."') ? 'Paket ' : 'Pemeriksaan ';
                    if(paket_nama) {
                        docoNotification('error', 'Terjadi Kesalahan', msg + paket_nama +' sudah diinputkan');
                    } else {
                        docoNotification('error', 'Terjadi Kesalahan', msg + ' belum ada yang diinputkan');
                    }
                }else{
                    if(instalasi_id == '".DocoConstants::INSTALASI_MCU."') {
                        loadpaketmcu(_formPendaftaran.listPenunjang)
                    }
                    else {
                        loadpemeriksaan(_formPendaftaran.listPenunjang)
                    }
                    
                    $('#modal_backdrop').modal('toggle')
                }
            }
        });
    })

    function generateDtTindakan() {
        table_jenis = $('#table-jenis').DataTable({
            displayLength: 10,
            filter: true,
            columnDefs: [ {
                orderable: false,
                // className: 'select-checkbox',
                targets:   0,
                checkboxes: {
                    selectRow: true,
                    stateSave: false,
                    selectAllPages: true
                }
            }],
            select: {
                style: 'multi',
                selector: 'tr'
            },
            sorting: [[2, 'asc']], 
            processing: true,
            serverSide: true,
            ajax: baseUrl+'pendaftaran/daftar/get-tarif-tindakan?'+params,
            columns:[
                {
                    data: 'tariftindakan_id',
                    searchable: false,
                    orderable: false,
                    visible: true,
                    defaultContent: ''
                },
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '" . (\Yii::t("fe", "Kode pemeriksaan")) . "', data: 'kode', name: 'kode'},
                {title: '" . (\Yii::t("fe", "Jenis pemeriksaan")) . "', data: 'jenispemeriksaanlab_nama', name: 'jenispemeriksaanlab_nama'},
                {title: '" . (\Yii::t("fe", "Nama pemeriksaan")) . "', data: 'daftartindakan_nama',searchable: true},
            ]
        });
        
        $('.dataTables_filter').hide();
        $('.filter-jenis').datatableBootstrapFilter(table_jenis);
    }

    function generateDtPaket() {
        table_paket = $('#table-paket').DataTable({
            filter: true,
            displayLength: 10,
            columnDefs: [
                {
                    orderable: false,
                    // className: 'select-checkbox',
                    targets: 0,
                    checkboxes: {
                        selectRow: true,
                        stateSave: false,
                    },
                    defaultContent: ''
                },
            ],
            select: {
                style: 'multi',
                selector: 'tr'
            },
            sorting: [[3, 'asc']], 
            processing: true,
            serverSide: true,
            ajax: baseUrl+'pendaftaran/daftar/get-tarif-paket?'+params,
            columns:[
                {
                    data: 'tariftindakan_id',
                    searchable: false,
                    orderable: false,
                    visible: true,
                    defaultContent: ''
                },
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'detail',
                    searchable: false,
                    orderable: false,
                    visible: (instalasi_id == '".DocoConstants::INSTALASI_MCU."') ? true : false,
                },
                {
                    title: '" . (\Yii::t("fe", "Kode pemeriksaan")) . "',
                    data: 'kode',
                    name: 'kode'
                },
                {
                    title: '" . (\Yii::t("fe", "Nama paket")) . "', 
                    data: 'tipepaket_nama'
                },
                
                {
                    title: '" . (\Yii::t("fe", "Nama pemeriksaan")) . "', 
                    data: 'pemeriksaanlab_nama', 
                    searchable: true,
                    visible: '".$visible."',
                },
            ]
        });

        $('.dataTables_filter').hide();
        $('.filter-paket').datatableBootstrapFilter(table_paket);
    }
    
    ", View::POS_END, 'jsModal')

?>
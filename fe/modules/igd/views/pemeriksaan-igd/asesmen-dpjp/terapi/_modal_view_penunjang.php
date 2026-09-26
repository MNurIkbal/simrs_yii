<?php

/**
 *  CLONE FROM PENDAFTARAN
 * @Author: Rizal
 * @Date:   2018-07-26 14:53:35
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-07-24 11:22:57
 */

use yii\helpers\Html;
use yii\web\View;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close close-modal-pemeriksaan" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12" style="padding: 10px;">
            <button type="button" class="btn-modal-add-pemeriksaan close-modal-pemeriksaan btn btn-info btn-labeled btn-xs" data-options="click"><b><i class="fa fa-plus"></i></b><?=Yii::t('fe', 'Tambah')?></button>
        </div>
    </div>
    <div class="row">
        <div class="list_tindakan">
            <div class="row">
                <div class="col-sm-12">
                    <div class="filter-jenis"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <button class="btn btn-sm btn-info btn-filter" data-parent=".filter-jenis"><?=Yii::t('fe', 'Cari')?></button>
                    <button class="btn btn-sm btn-info btn-reset" data-parent=".filter-jenis"><?=Yii::t('fe', 'Muat ulang')?></button>

                </div>
            </div>
            <table id="table-jenis" class="table datatable-basic table-striped table-hover dataTable no-footer">
                <thead>
                    <tr class="bg-inverse">
                        <th></th>
                        <th><?=Yii::t('fe', 'No')?></th>
                        <th><?=Yii::t('fe', 'Jenis pemeriksaan')?></th>
                        <th><?=Yii::t('fe', 'Nama pemeriksaan')?></th>
                    </tr>
                </thead>
                <tbody> 
                </tbody>
            </table>
        </div>

        <div class="list_paket">
            <div class="row">
                <div class="col-md-11">
                    <div class="filter-paket"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <button class="btn btn-sm btn-info btn-filter" data-parent=".filter-paket"><?=Yii::t('fe','Cari')?></button>
                    <button class="btn btn-sm btn-info btn-reset" data-parent=".filter-paket"><?=Yii::t('fe','Muat ulang')?></button>
                </div>
            </div>
            <table id="table-paket" class="table datatable-basic table-striped table-hover dataTable no-footer">
                <thead>
                    <tr class="bg-inverse">
                        <th></th>
                        <th><?=Yii::t('fe', 'No')?></th>
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

$this->registerJs("
    var table_jenis
    var table_paket
    var localstorage = window.localStorage
    var params = 'ruangan_id=' +$('#penunjang_ruangan_id').val()
    params += '&penjamin_id='+$('#penunjang_penjamin_id').val()
    params += '&kelaspelayanan_id='+$('#penunjang_kelaspelayanan_id').val()
    params += '&instalasi_id='+$('#penunjang_instalasi_id').val()
    console.log(params)
    $(document).ready(function() {
        table_paket = $('#table-paket').docoTabel({
            filter: true,
            displayLength: 10,
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   0,
                checkboxes: {
                    selectRow: true
                }
            }],
            select: {
                style: 'multi',
                selector: 'tr'
            },
            sorting: [[2, 'asc']], 
            processing: true,
            serverSide: true,
            ajax: baseUrl+'igd/end-point/get-tarif-paket?'+params,
            columns:[
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: '',
                },
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '" . (\Yii::t("fe", "Nama paket")) . "', data: 'tipepaket_nama'},
                {title: '" . (\Yii::t("fe", "Nama pemeriksaan")) . "', data: 'jenispemeriksaanlab_nama'},
            ]
        });
        table_jenis = $('#table-jenis').docoTabel({
            displayLength: 10,
            filter: true,
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   0,
                checkboxes: {
                    selectRow: true
                }
            }],
            select: {
                style: 'multi',
                selector: 'tr'
            },
            sorting: [[2, 'asc']], 
            processing: true,
            serverSide: true,
            ajax: baseUrl+'igd/end-point/get-tarif-tindakan?'+params,
            columns:[
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: '',
                },
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '" . (\Yii::t("fe", "Jenis pemeriksaan")) . "', data: 'jenispemeriksaanlab_nama', name: 'jenispemeriksaanlab_nama'},
                {title: '" . (\Yii::t("fe", "Nama pemeriksaan")) . "', data: 'daftartindakan_nama'},
            ]
        });
        $('.dataTables_filter').hide();
        $('.filter-jenis').datatableBootstrapFilter(table_jenis, 
            [
                [2, '<input class=\"form-control\">' ],
            ]
        );
        $('.filter-paket').datatableBootstrapFilter(table_paket, 
            [
                [2, '<input class=\'form-control\'>' ],
            ]
        );
    })
    $(document).on('click','.btn-filter', function(){
        var parent = $(this).attr('data-parent')
        $(parent + ' .advancedFilterDo').trigger('click')
    })
    $(document).on('click', '.btn-reset', function(){
        var parent = $(this).attr('data-parent')
        $(parent).find('[type=reset]').click();
        $(parent).find('.advancedFilterDo').click();
    })
    $('.btn-modal-add-pemeriksaan').on('click', function(){
        var tindakan_row = table_jenis.rows('.selected').data().toArray()
        var paket_row = table_paket.rows('.selected').data().toArray()
        var paket_nama
        var arr = tindakan_row.concat(paket_row)
        var state = true
        $.each(arr, function(k,v){
            if(typeof pemeriksaanlab[v.tariftindakan_id] === 'undefined'){
                state = true
                v.is_cyto = 'false'
                v.jenispemeriksaan = (typeof v.jenispemeriksaanlab_nama !== 'undefined') ? v.jenispemeriksaanlab_nama : (typeof v.tipepaket_nama !== 'undefined') ? v.tipepaket_nama : '';
                v.namapemeriksaan = (typeof v.daftartindakan_nama !== 'undefined') ? v.daftartindakan_nama : (typeof v.nama_tindakan_paket !== 'undefined') ? v.nama_tindakan_paket : '';
                pemeriksaanlab[v.tariftindakan_id] = v
            }else{
                paket_nama = (typeof v.jenispemeriksaanlab_nama !== 'undefined') ? v.jenispemeriksaanlab_nama : (typeof v.tipepaket_nama !== 'undefined') ? v.tipepaket_nama : '';
                state = false
                return false
            }

            if (!$.isEmptyObject(pemeriksaanlab)) {
                $('#penunjang_instalasi_id2').val($('#penunjang_instalasi_id').val());
                $('#penunjang_ruangan_id2').val($('#penunjang_ruangan_id').val());
                $('#penunjang_instalasi_id').attr('disabled', true);
                $('#penunjang_ruangan_id').attr('disabled', true);
            }
        })
        if(state == false){
            docoNotification('error', 'Terjadi Kesalahan', 'Pemeriksaan '+ paket_nama +' sudah diinputkan')
        }else{
            loadpemeriksaan(pemeriksaanlab)
            $('#modal_backdrop').modal('toggle')
        }
    })
    
    ", View::POS_END, 'jsModal')

?>
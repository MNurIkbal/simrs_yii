<?php

/**
 *  CLONE FROM PENDAFTARAN
 * @Author: Rizal
 * @Date:   2018-07-26 14:53:35
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-07-24 11:22:57
 */

use app\components\DocoConstants;
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
        <div class="list_checkbox">
            <div class="row">
                <?php
                if($result){
                    foreach ($result as $header => $detail_header) {
                ?>
                    <div class="col-sm-3">
                    <table id="table-checkbox" class="table datatable-basic table-striped table-hover dataTable no-footer">
                <?php
                    echo '<thead><tr class="bg-inverse"><th>'. $header .'</th></tr></thead>';
                    if(!empty($detail_header)) {
                        foreach ($detail_header as $key => $detail2) {
                                echo '<tr class="row-default"><td>
                                <input 
                                    type = "checkbox"
                                    id = "' . $detail2['daftartindakan_id'] . '"
                                    class = "cb_penunjang"
                                    data-jenis                     ="' . $detail2['jenis'] . '"
                                    data-tariftindakan_id          ="' . $detail2['tariftindakan_id'] . '"
                                    data-ruangan_id                ="' . $detail2['ruangan_id'] . '"
                                    data-ruangan_nama              ="' . $detail2['ruangan_nama'] . '"
                                    data-instalasi_id              ="' . $detail2['instalasi_id'] . '"
                                    data-instalasi_nama            ="' . $detail2['instalasi_nama'] . '"
                                    data-ruanganpaket_id           ="' . $detail2['ruanganpaket_id'] . '"
                                    data-ruanganpaket_nama         ="' . $detail2['ruanganpaket_nama'] . '"
                                    data-perdatarif_id             ="' . $detail2['perdatarif_id'] . '"
                                    data-perdanama_sk              ="' . $detail2['perdanama_sk'] . '"
                                    data-kelaspelayanan_id         ="' . $detail2['kelaspelayanan_id'] . '"
                                    data-kelaspelayanan_nama       ="' . $detail2['kelaspelayanan_nama'] . '"
                                    data-penjamin_id               ="' . $detail2['penjamin_id'] . '"
                                    data-penjamin_nama             ="' . $detail2['penjamin_nama'] . '"
                                    data-kelompoktindakan_id       ="' . $detail2['kelompoktindakan_id'] . '"
                                    data-kelompoktindakan_nama     ="' . $detail2['kelompoktindakan_nama'] . '"
                                    data-kategoritindakan_id       ="' . $detail2['kategoritindakan_id'] . '"
                                    data-kategoritindakan_nama     ="' . $detail2['kategoritindakan_nama'] . '"
                                    data-daftartindakan_id         ="' . $detail2['daftartindakan_id'] . '"
                                    data-daftartindakan_nama       ="' . $detail2['daftartindakan_nama'] . '"
                                    data-tipepaket_id              ="' . $detail2['tipepaket_id'] . '"
                                    data-tipepaket_nama            ="' . $detail2['tipepaket_nama'] . '"
                                    data-komponentarif_id          ="' . $detail2['komponentarif_id'] . '"
                                    data-komponentarif_nama        ="' . $detail2['komponentarif_nama'] . '"
                                    data-harga_tariftindakan       ="' . $detail2['harga_tariftindakan'] . '"
                                    data-persencyto_tindakan       ="' . $detail2['persencyto_tindakan'] . '"
                                    data-persendiskon_tindakan     ="' . $detail2['persendiskon_tindakan'] . '"
                                    data-is_default                ="' . $detail2['is_default'] . '"
                                    data-is_akomodasi              ="' . $detail2['is_akomodasi'] . '"
                                    data-carabayar_id              ="' . $detail2['carabayar_id'] . '"
                                    data-is_konsultasi             ="' . $detail2['is_konsultasi'] . '"
                                    data-kamarruangan_nokamar      ="' . $detail2['kamarruangan_nokamar'] . '"
                                    data-kamarruangan_id           ="' . $detail2['kamarruangan_id'] . '"
                                    data-ambulan_id                ="' . $detail2['ambulan_id'] . '"
                                    data-no_polisi                 ="' . $detail2['no_polisi'] . '"
                                    data-kelompokpemeriksaanlab_id ="' . $detail2['kelompokpemeriksaanlab_id'] . '"
                                    data-nama_kelompok             ="' . $detail2['nama_kelompok'] . '"
                                    data-jenispemeriksaanlab_id    ="' . $detail2['jenispemeriksaanlab_id'] . '"
                                    data-jenispemeriksaanlab_nama  ="' . $detail2['jenispemeriksaanlab_nama'] . '"
                                    data-pemeriksaanlab_id         ="' . $detail2['pemeriksaanlab_id'] . '"
                                    data-pemeriksaanlab_nama       ="' . $detail2['pemeriksaanlab_nama'] . '"
                                    data-persen_penyulit           ="' . $detail2['persen_penyulit'] . '"
                                > '
                                . $detail2['daftartindakan_nama'] .
                                '</td></tr>';
                            }
                        }
                        ?>
                    </table>
                    <br>
                    </div>
                <?php
                    }
                }
                else{
                    echo '<p style="text-align:center">Tidak Ada Data.</p>';
                }
                ?>
            </div>
        </div>

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
        list_pemeriksaanlab.forEach(myFunction);
        function myFunction(item, index) {
            $('#'+item+'').prop('checked', true);
        }
        if ($('#penunjang_instalasi_id').val() == '".DocoConstants::INSTALASI_ID_LAB."' || $('#penunjang_instalasi_id').val() == '".DocoConstants::INSTALASI_ID_RAD."' || $('#penunjang_instalasi_id').val() == '".DocoConstants::INSTALASI_ID_BEDAH."') {
            $('.list_checkbox').addClass('col-md-12');
            $('.list_tindakan').hide();
            $('.list_paket').hide();
            $('.btn-modal-add-pemeriksaan').hide();
        } else if ($('#penunjang_instalasi_id').val() == '12') {
            $('.list_tindakan').addClass('col-md-12');
            $('.list_paket').hide();
            $('.list_checkbox').hide();
        } else {
            $('.list_tindakan').addClass('col-md-6');
            $('.list_paket').addClass('col-md-6');
            $('.list_checkbox').hide();
        }
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
    $('.btn-filter').on('click', function(){
        var parent = $(this).attr('data-parent')
        $(parent + ' .advancedFilterDo').trigger('click')
    })
    $('.btn-reset').on('click', function(){
        var parent = $(this).attr('data-parent')
        $(parent).find('[type=reset]').click();
        $(parent).find('.advancedFilterDo').click();
    })
    $('.btn-modal-add-pemeriksaan').on('click', function(){
        let dataPost = [];
        if ($('#penunjang_instalasi_id').val() == '".DocoConstants::INSTALASI_ID_LAB."' || $('#penunjang_instalasi_id').val() == '".DocoConstants::INSTALASI_ID_RAD."' || $('#penunjang_instalasi_id').val() == '".DocoConstants::INSTALASI_ID_BEDAH."') {
            $(':checkbox:checked').each(function (i) {
                if((list_pemeriksaanlab.filter(e => e == $(this).attr('data-daftartindakan_id').toString())) == ''){
                    list_pemeriksaanlab.push($(this).attr('data-daftartindakan_id'))
                    dataPost.push({
                        jenis: ($(this).attr('data-jenis') !== '' ) ? $(this).attr('data-jenis') : null,
                        tariftindakan_id: ($(this).attr('data-tariftindakan_id') !== '' ) ? $(this).attr('data-tariftindakan_id') : null,
                        ruangan_id: ($(this).attr('data-ruangan_id') !== '' ) ? $(this).attr('data-ruangan_id') : null,
                        ruangan_nama: ($(this).attr('data-ruangan_nama') !== '' ) ? $(this).attr('data-ruangan_nama') : null,
                        instalasi_id: ($(this).attr('data-instalasi_id') !== '' ) ? $(this).attr('data-instalasi_id') : null,
                        instalasi_nama: ($(this).attr('data-instalasi_nama') !== '' ) ? $(this).attr('data-instalasi_nama') : null,
                        ruanganpaket_id: ($(this).attr('data-ruanganpaket_id') !== '' ) ? $(this).attr('data-ruanganpaket_id') : null,
                        ruanganpaket_nama: ($(this).attr('data-ruanganpaket_nama') !== '' ) ? $(this).attr('data-ruanganpaket_nama') : null,
                        perdatarif_id: ($(this).attr('data-perdatarif_id') !== '' ) ? $(this).attr('data-perdatarif_id') : null,
                        perdanama_sk: ($(this).attr('data-perdanama_sk') !== '' ) ? $(this).attr('data-perdanama_sk') : null,
                        kelaspelayanan_id: ($(this).attr('data-kelaspelayanan_id') !== '' ) ? $(this).attr('data-kelaspelayanan_id') : null,
                        kelaspelayanan_nama: ($(this).attr('data-kelaspelayanan_nama') !== '' ) ? $(this).attr('data-kelaspelayanan_nama') : null,
                        penjamin_id: ($(this).attr('data-penjamin_id') !== '' ) ? $(this).attr('data-penjamin_id') : null,
                        penjamin_nama: ($(this).attr('data-penjamin_nama') !== '' ) ? $(this).attr('data-penjamin_nama') : null,
                        kelompoktindakan_id: ($(this).attr('data-kelompoktindakan_id') !== '' ) ? $(this).attr('data-kelompoktindakan_id') : null,
                        kelompoktindakan_nama: ($(this).attr('data-kelompoktindakan_nama') !== '' ) ? $(this).attr('data-kelompoktindakan_nama') : null,
                        kategoritindakan_id: ($(this).attr('data-kategoritindakan_id') !== '' ) ? $(this).attr('data-kategoritindakan_id') : null,
                        kategoritindakan_nama: ($(this).attr('data-kategoritindakan_nama') !== '' ) ? $(this).attr('data-kategoritindakan_nama') : null,
                        daftartindakan_id: ($(this).attr('data-daftartindakan_id') !== '' ) ? $(this).attr('data-daftartindakan_id') : null,
                        daftartindakan_nama: ($(this).attr('data-daftartindakan_nama') !== '' ) ? $(this).attr('data-daftartindakan_nama') : null,
                        tipepaket_id: ($(this).data('tipepaket_id') !== '' ) ? $(this).data('data-tipepaket_id') : null,
                        tipepaket_nama: ($(this).attr('data-tipepaket_nama') !== '' ) ? $(this).attr('data-tipepaket_nama') : null,
                        komponentarif_id: ($(this).attr('data-komponentarif_id') !== '' ) ? $(this).attr('data-komponentarif_id') : null,
                        komponentarif_nama: ($(this).attr('data-komponentarif_nama') !== '' ) ? $(this).attr('data-komponentarif_nama') : null,
                        harga_tariftindakan: ($(this).attr('data-harga_tariftindakan') !== '' ) ? $(this).attr('data-harga_tariftindakan') : null,
                        persencyto_tindakan: ($(this).attr('data-persencyto_tindakan') !== '' ) ? $(this).attr('data-persencyto_tindakan') : null,
                        persendiskon_tindakan: ($(this).attr('data-persendiskon_tindakan') !== '' ) ? $(this).attr('data-persendiskon_tindakan') : null,
                        is_default: ($(this).attr('data-is_default') !== '' ) ? $(this).attr('data-is_default') : false,
                        is_akomodasi: ($(this).attr('data-is_akomodasi') !== '' ) ? $(this).attr('data-is_akomodasi') : false,
                        carabayar_id: ($(this).attr('data-carabayar_id') !== '' ) ? $(this).attr('data-carabayar_id') : null,
                        is_konsultasi: ($(this).attr('data-is_konsultasi') !== '' ) ? $(this).attr('data-is_konsultasi') : false,
                        kamarruangan_nokamar: ($(this).attr('data-kamarruangan_nokamar') !== '' ) ? $(this).attr('data-kamarruangan_nokamar') : null,
                        kamarruangan_id: ($(this).attr('data-kamarruangan_id') !== '' ) ? $(this).attr('data-kamarruangan_id') : null,
                        ambulan_id: ($(this).attr('data-ambulan_id') !== '' ) ? $(this).attr('data-ambulan_id') : null,
                        no_polisi: ($(this).attr('data-no_polisi') !== '' ) ? $(this).attr('data-no_polisi') : null,
                        kelompokpemeriksaanlab_id: ($(this).attr('data-kelompokpemeriksaanlab_id') !== '' ) ? $(this).attr('data-kelompokpemeriksaanlab_id') : null,
                        nama_kelompok: ($(this).attr('data-nama_kelompok') !== '' ) ? $(this).attr('data-nama_kelompok') : null,
                        jenispemeriksaanlab_id: ($(this).attr('data-jenispemeriksaanlab_id') !== '' ) ? $(this).attr('data-jenispemeriksaanlab_id') : null,
                        jenispemeriksaanlab_nama: ($(this).attr('data-jenispemeriksaanlab_nama') !== '' ) ? $(this).attr('data-jenispemeriksaanlab_nama') : null,
                        pemeriksaanlab_id: ($(this).attr('data-pemeriksaanlab_id') !== '' ) ? $(this).attr('data-pemeriksaanlab_id') : null,
                        pemeriksaanlab_nama: ($(this).attr('data-pemeriksaanlab_nama') !== '' ) ? $(this).attr('data-pemeriksaanlab_nama') : null,
                        persen_penyulit: ($(this).attr('data-persen_penyulit') !== '' ) ? $(this).attr('data-persen_penyulit') : null
                    });
                }
            });
        }

        var tindakan_row = table_jenis.rows('.selected').data().toArray()
        var paket_row = table_paket.rows('.selected').data().toArray()
        var paket_nama
        var arr = tindakan_row.concat(paket_row).concat(dataPost)
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
    
    $('.cb_penunjang').on('click', function(){
         if($(this).prop('checked') == true){
                let dataPost = [];
        if ($('#penunjang_instalasi_id').val() == '".DocoConstants::INSTALASI_ID_LAB."' || $('#penunjang_instalasi_id').val() == '".DocoConstants::INSTALASI_ID_RAD."' || $('#penunjang_instalasi_id').val() == '".DocoConstants::INSTALASI_ID_BEDAH."') {
            list_pemeriksaanlab.push($(this).attr('data-daftartindakan_id'))
            dataPost.push({
                jenis: ($(this).attr('data-jenis') !== '' ) ? $(this).attr('data-jenis') : null,
                tariftindakan_id: ($(this).attr('data-tariftindakan_id') !== '' ) ? $(this).attr('data-tariftindakan_id') : null,
                ruangan_id: ($(this).attr('data-ruangan_id') !== '' ) ? $(this).attr('data-ruangan_id') : null,
                ruangan_nama: ($(this).attr('data-ruangan_nama') !== '' ) ? $(this).attr('data-ruangan_nama') : null,
                instalasi_id: ($(this).attr('data-instalasi_id') !== '' ) ? $(this).attr('data-instalasi_id') : null,
                instalasi_nama: ($(this).attr('data-instalasi_nama') !== '' ) ? $(this).attr('data-instalasi_nama') : null,
                ruanganpaket_id: ($(this).attr('data-ruanganpaket_id') !== '' ) ? $(this).attr('data-ruanganpaket_id') : null,
                ruanganpaket_nama: ($(this).attr('data-ruanganpaket_nama') !== '' ) ? $(this).attr('data-ruanganpaket_nama') : null,
                perdatarif_id: ($(this).attr('data-perdatarif_id') !== '' ) ? $(this).attr('data-perdatarif_id') : null,
                perdanama_sk: ($(this).attr('data-perdanama_sk') !== '' ) ? $(this).attr('data-perdanama_sk') : null,
                kelaspelayanan_id: ($(this).attr('data-kelaspelayanan_id') !== '' ) ? $(this).attr('data-kelaspelayanan_id') : null,
                kelaspelayanan_nama: ($(this).attr('data-kelaspelayanan_nama') !== '' ) ? $(this).attr('data-kelaspelayanan_nama') : null,
                penjamin_id: ($(this).attr('data-penjamin_id') !== '' ) ? $(this).attr('data-penjamin_id') : null,
                penjamin_nama: ($(this).attr('data-penjamin_nama') !== '' ) ? $(this).attr('data-penjamin_nama') : null,
                kelompoktindakan_id: ($(this).attr('data-kelompoktindakan_id') !== '' ) ? $(this).attr('data-kelompoktindakan_id') : null,
                kelompoktindakan_nama: ($(this).attr('data-kelompoktindakan_nama') !== '' ) ? $(this).attr('data-kelompoktindakan_nama') : null,
                kategoritindakan_id: ($(this).attr('data-kategoritindakan_id') !== '' ) ? $(this).attr('data-kategoritindakan_id') : null,
                kategoritindakan_nama: ($(this).attr('data-kategoritindakan_nama') !== '' ) ? $(this).attr('data-kategoritindakan_nama') : null,
                daftartindakan_id: ($(this).attr('data-daftartindakan_id') !== '' ) ? $(this).attr('data-daftartindakan_id') : null,
                daftartindakan_nama: ($(this).attr('data-daftartindakan_nama') !== '' ) ? $(this).attr('data-daftartindakan_nama') : null,
                tipepaket_id: ($(this).data('tipepaket_id') !== '' ) ? $(this).data('data-tipepaket_id') : null,
                tipepaket_nama: ($(this).attr('data-tipepaket_nama') !== '' ) ? $(this).attr('data-tipepaket_nama') : null,
                komponentarif_id: ($(this).attr('data-komponentarif_id') !== '' ) ? $(this).attr('data-komponentarif_id') : null,
                komponentarif_nama: ($(this).attr('data-komponentarif_nama') !== '' ) ? $(this).attr('data-komponentarif_nama') : null,
                harga_tariftindakan: ($(this).attr('data-harga_tariftindakan') !== '' ) ? $(this).attr('data-harga_tariftindakan') : null,
                persencyto_tindakan: ($(this).attr('data-persencyto_tindakan') !== '' ) ? $(this).attr('data-persencyto_tindakan') : null,
                persendiskon_tindakan: ($(this).attr('data-persendiskon_tindakan') !== '' ) ? $(this).attr('data-persendiskon_tindakan') : null,
                is_default: ($(this).attr('data-is_default') !== '' ) ? $(this).attr('data-is_default') : false,
                is_akomodasi: ($(this).attr('data-is_akomodasi') !== '' ) ? $(this).attr('data-is_akomodasi') : false,
                carabayar_id: ($(this).attr('data-carabayar_id') !== '' ) ? $(this).attr('data-carabayar_id') : null,
                is_konsultasi: ($(this).attr('data-is_konsultasi') !== '' ) ? $(this).attr('data-is_konsultasi') : false,
                kamarruangan_nokamar: ($(this).attr('data-kamarruangan_nokamar') !== '' ) ? $(this).attr('data-kamarruangan_nokamar') : null,
                kamarruangan_id: ($(this).attr('data-kamarruangan_id') !== '' ) ? $(this).attr('data-kamarruangan_id') : null,
                ambulan_id: ($(this).attr('data-ambulan_id') !== '' ) ? $(this).attr('data-ambulan_id') : null,
                no_polisi: ($(this).attr('data-no_polisi') !== '' ) ? $(this).attr('data-no_polisi') : null,
                kelompokpemeriksaanlab_id: ($(this).attr('data-kelompokpemeriksaanlab_id') !== '' ) ? $(this).attr('data-kelompokpemeriksaanlab_id') : null,
                nama_kelompok: ($(this).attr('data-nama_kelompok') !== '' ) ? $(this).attr('data-nama_kelompok') : null,
                jenispemeriksaanlab_id: ($(this).attr('data-jenispemeriksaanlab_id') !== '' ) ? $(this).attr('data-jenispemeriksaanlab_id') : null,
                jenispemeriksaanlab_nama: ($(this).attr('data-jenispemeriksaanlab_nama') !== '' ) ? $(this).attr('data-jenispemeriksaanlab_nama') : null,
                pemeriksaanlab_id: ($(this).attr('data-pemeriksaanlab_id') !== '' ) ? $(this).attr('data-pemeriksaanlab_id') : null,
                pemeriksaanlab_nama: ($(this).attr('data-pemeriksaanlab_nama') !== '' ) ? $(this).attr('data-pemeriksaanlab_nama') : null,
                persen_penyulit: ($(this).attr('data-persen_penyulit') !== '' ) ? $(this).attr('data-persen_penyulit') : null
            });
        }

        var tindakan_row = table_jenis.rows('.selected').data().toArray()
        var paket_row = table_paket.rows('.selected').data().toArray()
        var paket_nama
        var arr = tindakan_row.concat(paket_row).concat(dataPost)
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
        }
            }
            else if($(this).prop('checked') == false){
                var daftartindakan_id_cb = $(this).data('daftartindakan_id')
                var kunci = $('[data-id='+daftartindakan_id_cb+']').data('key')
                delete pemeriksaanlab[$('[data-id='+daftartindakan_id_cb+']').data('key')]
                list_pemeriksaanlab = list_pemeriksaanlab.filter(e => e !== $(this).data('daftartindakan_id').toString())
                loadpemeriksaan(pemeriksaanlab)
            }
        
    })
    ", View::POS_END, 'jsModal')

?>
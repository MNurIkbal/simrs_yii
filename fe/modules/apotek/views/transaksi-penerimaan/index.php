<?php

/**
 * @author Randy Vianda Putra
 * @copyright 15 January 2018 aweutist
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', $this->title), 'url' => ['rumah-sakit']];

?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <?=Breadcrumbs::widget([
                    'homeLink' => [ 
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= Html::button('<b><i class="fa fa-search"></i></b>'.Yii::t('fe', 'Cari'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                    ]);
                ?>
                <?= Html::button('<b><i class="fa fa-refresh"></i></b>'.Yii::t('fe', ' Muat Ulang'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                    ]);
                ?>
                <?= Html::button('<b><i class="fa fa-print"></i></b>'.Yii::t('fe', ' Print'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                    ]);
                ?>
                <?= Html::a('<b><i class="fa fa-file-pdf-o"></i></b>'.Yii::t('fe', ' Cetak PDF'), 'javascript:void(0);',
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'onclick' => "_export_pdf(this.id,'.filter-form')",
                        'id' => 'pdf',
                        'data-sources' => "/rm/lap-kunjungan/export-pdf"
                    ]);
                ?>
                <?= Html::a('<b><i class="fa fa-file-excel-o"></i></b>'.Yii::t('fe', ' Unduh Excel'), 'javascript:void(0);',
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'onclick' => "_export_excel(this.id,'.filter-form')",
                        'id' => 'excel',
                        'data-sources' => "/rm/lap-kunjungan/export-excel"
                    ]);
                ?>
            
            </div>

            <div class="panel-body" style="padding:10px;">
                <!-- Form Cari No Resep -->
                <div class="col-md-12 panel panel-flat" style="margin-top:10px;">
                    <div class="panel-heading">
                        <h4 class="panel-title"><?= Yii::t('fe', 'Data Mutasi') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                        <div class="heading-elements">
                            <ul class="icons-list">
                                <li><a data-action="collapse"></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php
                            $form = ActiveForm::begin([
                                'id' => 'ajax-form', 
                                'options' => ['class' => 'form-horizontal'],
                                'action' =>['obat-alkes-kasus/create']
                            ]); 
                        ?>
                        <div class="form-group">
                            <label class="col-lg-2 control-label"><?= Yii::t('fe', 'No Mutasi') ?></label>
                            <div class="col-lg-6">
                                <div class="input-group">
                                    <?= Html::activeDropDownList($model, 'no_resep',
                                        ArrayHelper::map([], 'no_resep', 'name'), [
                                            'class' => 'select2 no_resep',
                                            'prompt' => Yii::t('fe', '-- Pilih --')
                                        ]) 
                                    ?>
                                    <?= Html::hiddenInput('TransaksiResep[no_resep]', '', ['class' => 'reseptur_id']); ?>
                                    <span class="input-group-addon">
                                        <?php
                                            echo Html::a('<i class="fa fa-list-ul"></i>
                                                <i class="fa fa-search"></i>',
                                                Url::home().'apotek/transaksi-resep/list-resep',[
                                                'data-toggle' => 'modal',
                                                'data-target' => '#modal_backdrop-lg'
                                            ]);
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 col-md-offset-2">
                            <?= Html::button('<i class="fa fa-search"></i> '. Yii::t('fe', "Cari"), ['class' => 'btn btn-primary cari']); ?>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
                
                <!-- Informasi Resep -->
                <div class="col-md-12 panel panel-flat" id="informasi" style="margin-top:10px;">
                    <div class="panel-heading">
                        <h4 class="panel-title"><?= Yii::t('fe', 'Tabel Mutasi') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                        <div class="heading-elements">
                            <ul class="icons-list">
                                <li><a data-action="collapse"></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="tabel-obat" 
                            data-source="<?=Url::home();?>apotek/transaksi-resep/list-resep"
                            data-filter=".form-filter">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="1">No</th>
                                    <th><?= Yii::t('fe', 'Nama Obat Alkes') ?></th>
                                    <th><?= Yii::t('fe', 'Qty Mutasi') ?></th>
                                    <th><?= Yii::t('fe', 'Terima') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="default-value">
                                    <td>1</td>
                                    <td>Acarbose</td>
                                    <td>10</td>
                                    <td><input type="checkbox" class="styled"></td>
                                </tr>
                            </tbody>
                        </table>
                        <br>
                        <br>
                        <br>
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <label class="col-lg-4 control-label"><?= Yii::t('fe', 'Pegawai mengetahui') ?></label>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <?= Html::activeDropDownList($model, 'no_resep',
                                            ArrayHelper::map([], 'no_resep', 'name'), [
                                                'class' => 'select2 no_resep',
                                                'prompt' => Yii::t('fe', '-- Pilih --')
                                            ]) 
                                        ?>
                                        <?= Html::hiddenInput('TransaksiResep[no_resep]', '', ['class' => 'reseptur_id']); ?>
                                        <span class="input-group-addon">
                                            <?php
                                                echo Html::a('<i class="fa fa-list-ul"></i>
                                                    <i class="fa fa-search"></i>',
                                                    Url::home().'apotek/transaksi-resep/list-resep',[
                                                    'data-toggle' => 'modal',
                                                    'data-target' => '#modal_backdrop-lg'
                                                ]);
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 pull-right">
                                <label class="col-lg-4 control-label"><?= Yii::t('fe', 'Pegawai menyetujui') ?></label>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <?= Html::activeDropDownList($model, 'no_resep',
                                            ArrayHelper::map([], 'no_resep', 'name'), [
                                                'class' => 'select2 no_resep',
                                                'prompt' => Yii::t('fe', '-- Pilih --')
                                            ]) 
                                        ?>
                                        <?= Html::hiddenInput('TransaksiResep[no_resep]', '', ['class' => 'reseptur_id']); ?>
                                        <span class="input-group-addon">
                                            <?php
                                                echo Html::a('<i class="fa fa-list-ul"></i>
                                                    <i class="fa fa-search"></i>',
                                                    Url::home().'apotek/transaksi-resep/list-resep',[
                                                    'data-toggle' => 'modal',
                                                    'data-target' => '#modal_backdrop-lg'
                                                ]);
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Untuk Kebutuhan Modal Global -->
<div id="modal_backdrop-lg" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- End -->
<?php 
    $this->registerCss($this->render('../assets/css/apotek.css'));
    $this->registerJs("
    
        // var trListerner = {};
        // var _parentOption = $('.no_resep');
        // var _no = 0;
        // $('#informasi').hide();
        // $('#informasi-resep').hide();
        // $(document).ready(function() {
        //     optionResep();
        // });

        // var optionResep = function () {
            
        //     var _options = $('<option></option>');
        //     _parentOption.html('');
        //     _parentOption.append('<option value=\"\">-- Pilih Semua --</option>');
        //     if (typeof transResep != 'undefined') {
        //         $.each(transResep, function(key, val) {
        //             _parentOption.append('<option value=\"'+ key +'\">'+ key +'</option>');
        //         });
        //     }
        // }
        // _parentOption.change(function (e) {
        //     var id = $(this).val();
        //     var reseptur_id = $('.reseptur_id');
        //     if (typeof id !== 'undefined') {
        //         reseptur_id.val(id);
        //     }
        // });

        // $(document).on('click', '.batal', function (e) {
        //     $('.no_resep').val(null).trigger('change');
        //     $('.cari').trigger('click');
        // });

        // $(document).on('click', '.ulang', function (e) {
        //     e.preventDefault();
        //     $('.no_resep').val(null).trigger('change');
        //     $('.cari').trigger('click');
        // });

        // $(document).on('click', '.cari', function (e) {
        //     var _no_resep = $('.no_resep').val();
        //     var _nama_barang = $('#nama_barang').val();
        //     var _header = transResep[_no_resep];
        //     var _html = '';
            
        //     if (typeof _header !== 'undefined') {
        //         // set header
        //         $('#informasi').show();
        //         $('.header_noResep').text(_header.no_resep)
        //         $('.header_nama').text(_header.nama_pasien)
        //         $('.header_totalTagihan').text(_header.instalasi)
        //         $('.header_jenis').text(_header.no_pendaftaran)
        //         $('.header_tanggal').text(_header.nama_dokter)
                
        //         var id = _header.reseptur_id;
        //         $.ajax({
        //             type: 'GET',
        //             url: '/apotek/transaksi-resep/get-detail-resep?id='+id,
        //             success: function(res) {
        //                 var _tr = $('<tr></tr>');
        //                 var _td = $('<td></td>');
        //                 var _tabel = $('#tabel-obat');
        //                 if (res != '') {
        //                     var data = JSON.parse(res);
        //                     var _no = 0;
        //                     $('.default-value').attr('style','display:none');
        //                     $('.resep').empty().remove();
        //                     $.each(data, function (x, y) {
        //                         if (typeof data[x] !== 'undefined') {
        //                             _no++;                                
        //                             _html += '<tr class=\"resep\">';
        //                                 _html += '<td class=\"numbering\">'+ _no +'</td>';
        //                                 _html += '<td>'+ y.obatalkes_namalain +'</td>';
        //                                 _html += '<td>Rp. '+ docoHelper.convertToRupiah(y.hargasatuan_reseptur) +'</td>';
        //                                 _html += '<td>Rp. '+ docoHelper.convertToRupiah(y.ppn)+'</td>';
        //                                 _html += '<td class=\"qty\">'+ y.qty_reseptur +'</td>';
        //                                 _html += '<td class=\"total_harga\" data-sub=\"'+ y.hargajual_reseptur +'\">Rp. '+ docoHelper.convertToRupiah(y.hargajual_reseptur) +'</td>';
        //                             _html += '</tr>';

        //                             $('.save').attr('data-id', y.reseptur_id);
        //                         } else {

        //                         }
        //                         data[x] = y;
                                
        //                     });
                            
        //                     if (_html === '') {
        //                         _html += '<tr>';
        //                         _html += '<td colspan=\"6\" class=\"text-center\">Data Tidak Ditemukan</td>';
        //                         _html += '</tr>';
        //                     }
        //                     $('#tabel-obat').prepend(_html);
        //                     var sum_subtotalItem = 0;
        //                     $.each($('.total_harga'), function() {
        //                         var value = parseInt($(this).data('sub'));
        //                         sum_subtotalItem += value;
        //                     });
        //                     $('#subtotalItem').html('Rp. ' + docoHelper.convertToRupiah(sum_subtotalItem));
        //                     $('#informasi-resep').show();
        //                 }
        //             }
        //         })
        //     } else {
        //         $('.resep').empty().remove();
        //         $('#subtotalItem').empty().remove();
        //         $('#informasi').hide();
        //         $('#informasi-resep').hide();
        //     }
        // });
        
        // $(document).on('click', '.save', function (e) {
        //     e.preventDefault();
        //     var _id = $(this).data('id');
        //     var _url = '/apotek/transaksi-resep/save-rs';
        //     console.log(_url)
        //     $.ajax({
        //         type: 'GET',
        //         url: _url,
        //         data: 'id='+_id,
        //         success: function() {
        //         }
        //     })
        // });
    
        // $(document).load(function() {
        //     $('#informasi').hide();
        //     $('#informasi-resep').hide();
        // });
    ");
?>
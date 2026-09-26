<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-05 17:53:56
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-12-11 15:54:02
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe','Informasi Penjualan Resep Karyawan'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style type="text/css">
    .tabel {
        font-size: 14px;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $title ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'back',
                    'print'=>[
                        'attributes'=>[
                            'data-target'=>'/apotek/informasi-penjualan-resep-karyawan/print-detail?id='.$id.'&noresep='.$data['noresep'].'&'
                        ]
                    ],
                    'copy' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Salin resep'),
                        'icon' => 'fa fa-copy',
                        'method' => 'not exist',
                        'attributes' => [
                            'disabled' => $disableButton,
                            'class' => 'data-copy',
                            'data-options'=>'click',
                            'action'=>'/apotek/informasi-penjualan-resep-karyawan/copy-resep?id='.$id.'&noresep='.$data['noresep'].'&pendaftaranid='.$data['pendaftaran_id']
                        ] 
                    ],

                ]);?>                
            </div>
            <div class="panel-body">                
                <!-- Informasi Resep -->
                <div class="col-md-12 panel panel-flat" id="informasi" style="margin-top:10px;">
                    <div class="panel-heading">
                        <h4 class="panel-title text-center"><?= Yii::t('fe', 'Rincian Tagihan Penjualan Obat Alkes') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                        <h4 class="panel-title text-center"><?= Yii::t('fe', 'Apotek') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                        <h4 class="panel-title"><?= Yii::t('fe', 'Data pasien') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                    </div>
                    <div class="panel-body">
                        <input type="hidden" name="" class="iter" value="<?=$data['iter']?>">
                        <table width="100%" cellpadding="10" class="tabel">
                            <tbody>
                                <tr>
                                    <td class="bold"><?= Yii::t('fe', 'No resep') ?></td>
                                    <td>:</td>
                                    <td class="header_noResep"><?=!empty($data['noresep']) ? $data['noresep'] : '-' ?></td>
                                    <td class="bold"><?= Yii::t('fe', 'Tanggal penjualan') ?></td>
                                    <td>:</td>
                                    <td class="header_dokter"><?= !empty($data['tglpenjualan']) ? date('d F Y', strtotime($data['tglpenjualan'])) : '-'?></td>
                                    <td class="bold"><?= Yii::t('fe', 'Cara bayar') ?></td>
                                    <td>:</td>
                                    <td class="header_instalasi"><?= !empty($data['carabayar_nama']) ? $data['carabayar_nama'] : '-'?></td>
                                    <td class="bold"><?= Yii::t('fe', 'Iter') ?></td>
                                    <td>:</td>
                                    <td class="header_ruangan iterasi"><?= !empty($data['iter']) ? $data['iter']: 0 ?></td>
                                </tr>
                                <tr>
                                    <td class="bold"><?= Yii::t('fe', 'Nama karyawan') ?></td>
                                    <td>:</td>
                                    <td class="header_namaPasien"><?= !empty($data['nama_karyawan']) ? $data['nama_karyawan'] : '-' ?></td>
                                    
                                    <td class="bold"><?= Yii::t('fe', 'Dokter resep') ?></td>
                                    <td>:</td>
                                    <td class="header_dokter"><?= !empty($data['nama_pegawai']) ? $data['nama_pegawai'] : '-' ?></td>
                                    
                                    <td class="bold"><?= Yii::t('fe', 'Penjamin') ?></td>
                                    <td>:</td>
                                    <td class="header_ruangan"><?= !empty($data['penjamin_nama']) ? $data['penjamin_nama']: '-'?></td>
                                    
                                    <td class="bold"></td>
                                    <td></td>
                                    <td class="header_ruangan"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Info Penjualan Resep Bebas -->
                <div class="col-md-12 panel panel-flat" style="margin-top:10px;">
                    <div class="panel-heading">
                        <h4 class="panel-title"><?= Yii::t('fe', 'Data obat alkes') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                        <div class="heading-elements">
                            <ul class="icons-list">
                                <li><a data-action="collapse"></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="test-footer"></div>
                    <div class="panel-body">
                        <table class="table datatable-basic table-striped table-hover dataTable" id="example" style="width: 100%">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="1">No</th>
                                    <th><?= Yii::t('fe', 'Racikan').' / '.Yii::t('fe', 'Non Racikan') ?></th>
                                    <th><?= Yii::t('fe', 'R ke') ?></th>
                                    <th><?= Yii::t('fe', 'Signa') ?></th>
                                    <th><?= Yii::t('fe', 'Nama Obat Alkes') ?></th>
                                    <th><?= Yii::t('fe', 'Harga Satuan') ?></th>
                                    <th><?= Yii::t('fe', 'Qty').' '.'(Rp.)' ?></th>
                                    <th><?= Yii::t('fe', 'Sub Total').' '.'(Rp.)' ?></th>
                                </tr>                               
                            </thead>
                            <tbody>

                            </tbody>   
                            <tfoot>
                                
                            </tfoot>
                        </table>                        
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?php 
    $this->registerCss($this->render('../assets/css/apotek.css'));
    $this->registerJs("
        var btn;
        $(document).ready(function(){
            btn = $('.data-copy').clone();
            var _test = function (data) {
                if($('.footer-total').length < 1){
                    $('.datatable-basic').find('tfoot').append('<tr class=\'footer-total\'><td class=\'text-right\' style=\'width: 70%\'></td><td>Total  (Rp.)</td><td class=\'text-right\'>'+data.totalobat+'</td></tr>');
                }
            }

            $('.data-copy').on('click', function () {
                var _url = $(this).attr('action');
                $().docoForm('click',{
                    url : _url,
                    success : function (data) {
                        var _iter = $('.iterasi').html();
                        _iter = parseInt(_iter) - 1;
                        $('.iterasi').html(_iter);
                        if(_iter < 1){
                            $('.data-copy').attr('disabled','disabled');
                        }
                    }
                });
            })

            table = $('#example').docoTabel({
                filter: true,               
                sorting: [[2, 'asc']], 
                displayLength: 10,
                processing: true,
                serverSide: true,
                scrollX: true,    
                ajax: function(data, callback, settings){
                    $.ajax({
                        url: baseUrl+'apotek/informasi-penjualan-resep-karyawan/get-data-obat?id=".$_GET['id']."',
                        data: data,
                        success: function(data)
                        {
                            _test(data)
                            callback(data);
                        }   
                    });
                    
                },
                columns: [                
                    {
                        title: 'No',
                        data: 'rowNum',
                        searchable: false,
                        orderable: false
                    },
                    {title: '".(\Yii::t('fe', 'Racikan')).' / '.(\Yii::t('fe', 'Non Racikan'))."', data: 'jenis_racikan',name:'rke',searchable: false,orderable: false},
                    {title: '".(\Yii::t('fe', 'R ke'))."', data: 'rke',searchable: false,orderable: false},
                    {title: '".(\Yii::t('fe', 'Signa'))."', data: 'signa_oa',searchable: false,orderable: false},
                    {title: '".(\Yii::t('fe', 'nama obat alkes'))."', data: 'obatalkes_nama',searchable: false,orderable: false},
                    {title: '".(\Yii::t('fe', 'Harga satuan')).' '.'(Rp.)'."', data: 'hargajual_oa',searchable: false,orderable: false,class:'text-right'},
                    {title: '".(\Yii::t('fe', 'Qty'))."',  data: 'qty_oa',searchable: false,orderable: false},    
                    {title: '".(\Yii::t('fe', 'Sub Total')).' '.'(Rp.)'."',  data: 'totaltagihan',searchable: false,orderable: false,class:'text-right'},
                ],
                
            });
        $('.dataTables_filter').hide();
        $(table.table().footer()).html('Your html content here ....');
        if($('.iter').val() == 0 || $('.iter').val() == ''){
            $('.data-copy').attr('disabled','disabled');            
        }  
    });
    var disableButton = function(){            
        $('.data-copy').remove();        
        btn.attr('disabled','disabled');
        $('.panel-toolbar').append(btn);
        
    }
        ",VIEW::POS_END, 'js-kuning');
?>
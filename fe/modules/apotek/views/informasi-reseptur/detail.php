<?php

/**
 * @author Randy Vianda Putra
 * @copyright 17 January 2018 aweutist
 */

use app\components\DocoConstants;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Informasi Reseptur', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style type="text/css">
    table {
      margin: 0 auto !important;
      /* width: 100% !important; */
      /* height: 200% !important; */
      clear: both !important;
      border-collapse: collapse !important;
      table-layout: auto !important;
      word-wrap:break-word !important;
      /* white-space: nowrap; */
    }
    th, td {
        white-space: normal !important;
        font-size: 11px;
        padding:10px
    }
    .padding-style {
        padding-top: 10px;
        padding-left: 25px;
        padding-right: 25px;
    }
    .notif-resep {
        font-size: 25px;
        font-weight: 700;
        color: #fc4503;
    }
    .tipe-resep {
        font-weight: 700;
        color: #fc4503;
        margin-right: 60px;
        font-size: 17px !important;
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
                        <h3 class="panel-title"><b><?= $title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?php 
                $toollbar = [
                    'back',
                    'log-custom'=>[
                        'type'=>'button',
                        'title' => \Yii::t('fe', 'Log Perubahan'),
                        'icon' => 'fa fa-file-text-o',
                        'attributes' => [
                            'id'          => 'btn-log',
                            'data-width'  => '90%',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action'      => '/apotek/informasi-reseptur/log-perubahan?id='.$id.'&type='.$type.'&modal=is_modal',true,
                        ]
                    ],
                    'print-custom'=>[
                        'type'=>'button',
                        'title' => \Yii::t('fe', 'Cetak'),
                        'icon' => 'fa fa-print',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id'=>'btn-cetak-resep',
                            'class'=>'cetak-resep',
                            'target'=>'_blank',
                            'class'=>'spa',
                            'data-options'=>'link',
                            'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-resep?id='.$id.'&noresep='.$no_resep.'&nomor='.$nomor.'&'
                        ]
                    ],
                    'batal' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Batalkan Resep'),
                        'icon' => 'fa fa-times',
                        'method' => '#',
                        'attributes' => [
                            'id' => 'btn-batal-resep',
                            'data-options'=>'click',
                            'disabled' => ($status_resep == DocoConstants::STATUS_RESEPTUR_DISERAHKAN || $status_resep == DocoConstants::STATUS_RESEPTUR_BATAL) ? true : false
                        ]
                    ],
                    'print-resep'=>[
                        'type'=>'button',
                        'title' => \Yii::t('fe', 'Cetak Resep'),
                        'icon' => 'fa fa-print',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id'=>'btn-cetak-resep-detail',
                            'class'=>'cetak-resep-detail',
                            'target'=>'_blank',
                            'class'=>'spa',
                            'data-options'=>'link',
                            'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-resep-detail?id='.$id.'&nomor='.$nomor.'&type=resep'
                        ]
                    ],
                    'print-salinan-resep'=>[
                        'type'=>'button',
                        'title' => \Yii::t('fe', 'Cetak Salinan Resep'),
                        'icon' => 'fa fa-print',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id'=>'btn-cetak-salinan-resep-detail',
                            'class'=>'cetak-salinan-resep-detail',
                            'target'=>'_blank',
                            'class'=>'spa',
                            'data-options'=>'link',
                            'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-resep-detail?id='.$id.'&nomor='.$nomor.'&type=salinan'
                        ]
                    ],
                ];
                if($etiket && $etiket_new){
                    $toollbar['cetak-oral'] = [
                        'type' => 'button',
                        'icon' => 'fa fa-print',
                        'title' => \Yii::t('fe', 'Cetak Etiket Oral'),
                        'attributes' => [
                            'id' => 'btn-cetak-oral',
                            'target'=>'_blank',
                            'data-options'=>'link',
                            'data-target' => '/apotek/worklist/print-etiket?identifier='.$no_resep.'&is_oral=true',
                        ],
                    ];
                    $toollbar['cetak-non-oral'] = [
                        'type' => 'button',
                        'icon' => 'fa fa-print',
                        'title' => \Yii::t('fe', 'Cetak Etiket Non-Oral'),
                        'attributes' => [
                            'id' => 'btn-cetak-non-oral',
                            'target'=>'_blank',
                            'data-options'=>'link',
                            'data-target' => '/apotek/worklist/print-etiket?identifier='.$no_resep.'&is_oral=false',
                        ],
                    ];
                    $toollbar['cetak-oral-new'] = [
                        'type' => 'button',
                        'icon' => 'fa fa-print',
                        'title' => \Yii::t('fe', 'Cetak Etiket Oral'),
                        'attributes' => [
                            'id' => 'btn-cetak-oral-new',
                            'target'=>'_blank',
                            'data-options'=>'link',
                            'data-target' => '/apotek/worklist/print-etiket-new?identifier='.$no_resep.'&is_oral=true',
                        ],
                    ];
                    $toollbar['cetak-non-oral-new'] = [
                        'type' => 'button',
                        'icon' => 'fa fa-print',
                        'title' => \Yii::t('fe', 'Cetak Etiket Non-Oral'),
                        'attributes' => [
                            'id' => 'btn-cetak-non-oral-new',
                            'target'=>'_blank',
                            'data-options'=>'link',
                            'data-target' => '/apotek/worklist/print-etiket-new?identifier='.$no_resep.'&is_oral=false',
                        ],
                    ];  
                }else if($etiket_new){
                    $toollbar['cetak-oral-new'] = [
                        'type' => 'button',
                        'icon' => 'fa fa-print',
                        'title' => \Yii::t('fe', 'Cetak Etiket Oral'),
                        'attributes' => [
                            'id' => 'btn-cetak-oral-new',
                            'target'=>'_blank',
                            'data-options'=>'link',
                            'data-target' => '/apotek/worklist/print-etiket-new?identifier='.$no_resep.'&is_oral=true',
                        ],
                    ];
                    $toollbar['cetak-non-oral-new'] = [
                        'type' => 'button',
                        'icon' => 'fa fa-print',
                        'title' => \Yii::t('fe', 'Cetak Etiket Non-Oral'),
                        'attributes' => [
                            'id' => 'btn-cetak-non-oral-new',
                            'target'=>'_blank',
                            'data-options'=>'link',
                            'data-target' => '/apotek/worklist/print-etiket-new?identifier='.$no_resep.'&is_oral=false',
                        ],
                    ];   
                }else{
                    $toollbar['cetak-oral'] = [
                        'type' => 'button',
                        'icon' => 'fa fa-print',
                        'title' => \Yii::t('fe', 'Cetak Etiket Oral'),
                        'attributes' => [
                            'id' => 'btn-cetak-oral',
                            'target'=>'_blank',
                            'data-options'=>'link',
                            'data-target' => '/apotek/worklist/print-etiket?identifier='.$no_resep.'&is_oral=true',
                        ],
                    ];
                    $toollbar['cetak-non-oral'] = [
                        'type' => 'button',
                        'icon' => 'fa fa-print',
                        'title' => \Yii::t('fe', 'Cetak Etiket Non-Oral'),
                        'attributes' => [
                            'id' => 'btn-cetak-non-oral',
                            'target'=>'_blank',
                            'data-options'=>'link',
                            'data-target' => '/apotek/worklist/print-etiket?identifier='.$no_resep.'&is_oral=false',
                        ],
                    ];
                }
                if ($pasienadmisi_id != null) {
                    $toollbar['cetak-multiple-resep'] = [
                        'type' => 'button',
                        'icon' => 'fa fa-print',
                        'title' => \Yii::t('fe', 'Cetak Multiple Obat'),
                        'attributes' => [
                            'id' => 'btn-cetak-multi-resep',
                            'data-width'  => '90%',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action'      => '/apotek/informasi-reseptur/cetak-multiple-resep?id='.$id.'&nomor='.$no_resep.'&modal=is_modal',true,
                        ],
                    ];
                }
                $toollbar['kronis'] = [
                    'type'  => 'button',
                    'title' => \Yii::t('fe', 'Generate Resep Kronis'),
                    'icon'  => 'fa fa-history',
                    'attributes' => [
                        'id'          => 'btn-generate-kronis',
                        'data-width'  => '90%',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'action'      => '/apotek/informasi-reseptur/generate-resep-kronis?id='.$id.'&nomor='.$no_resep.'&modal=is_modal',true,
                        'disabled' => true // enable/disable by js
                    ]
                ];
                ?>
                <?=DocoHelpers::generateToolbar($toollbar)?>
                <button type="button" id="btn-history-resep" class="btn btn-labeled btn-info btn-xs" data-width="80%" data-href="/apotek/informasi-reseptur/modal-history-resep?pasien_id=<?= $pasienId ?>&no_resep=<?= $no_resep ?>" >
                    <b><i class='fa fa-history'></i></b> History Resep
                </button>
            </div>

            <?php if($status_resep == DocoConstants::STATUS_RESEPTUR_BATAL): ?>
                <div class="row padding-style">
                    <div class="col-md-12">
                        <span class="notif-resep">RESEP DIBATALKAN</span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="panel-body" style="padding:10px;">
                <!-- Informasi Resep -->
                <div class="col-md-3">
                    <div class="panel panel-default" id="informasi" style="margin-top:10px;">
                        <div class="panel-heading">
                            <h5 class="panel-title"><?= Yii::t('fe', 'Informasi Resep Pasien') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?php
                                    if (preg_match('/^RST/', $no_resep)) {
                                        echo "Nomor Reseptur";
                                    } else {
                                        echo "Nomor Resep";
                                    }
                                    ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $no_resep ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'No pendaftaran') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $no_pendaftaran ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'No Rekam Medik') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $no_rekam_medik ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Nama pasien') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $nama_pasien ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Tanggal Lahir') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $tgl_lahir ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Alamat') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $alamat ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'No Telepon') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $no_telepon_pasien ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'TB / BB') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $tb ?> cm / <?= $bb ?> kg</div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Dokter resep') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $nama_pegawai ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Dokter DPJP') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $dpjp_nama ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Instalasi') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $instalasi_nama ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Ruangan') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $ruangan_nama ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Cara bayar') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $carabayar_nama ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Kelas pelayanan') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $kelaspelayanan_nama ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Penjamin') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $penjamin_nama ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Diagnosa') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $diagnosa ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Iter') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= $iter ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-pasien bold"><?= Yii::t('fe', 'Catatan') ?></div>
                                    <div class="col-md-7 detail-pasien text-left"><?= strip_tags($catatan) ?></div>
                                </div>
                            </div>
                            <hr/>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-riwayat bold">Catatan Penting Pasien</div>
                                    <div class="col-md-7 detail-riwayat text-left"><?= ArrayHelper::getValue($riwayat_personal,'catatanpenting_pasien'); ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-riwayat bold">Riwayat Penyakit</div>
                                    <div class="col-md-7 detail-riwayat text-left"><?= ArrayHelper::getValue($riwayat_personal,'riwayat_penyakit'); ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-riwayat bold">Riwayat Alergi</div>
                                    <div class="col-md-7 detail-riwayat text-left"><?= ArrayHelper::getValue($riwayat_personal,'riwayat_alergi'); ?></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-5 detail-riwayat bold">Riwayat Obat</div>
                                    <div class="col-md-7 detail-riwayat text-left"><?= ArrayHelper::getValue($riwayat_personal,'riwayat_obat'); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="panel panel-default" id="informasi" style="margin-top:10px;">
                        <div class="panel-heading">
                            <h5 class="panel-title"><?= Yii::t('fe', 'Informasi Obat Pasien') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li class="tipe-resep"><?= $kategori_resep_nama ?></li>
                                </ul>
                            </div>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <?php if(count($data_racikan) > 0) { ?>
                                <?= $this->render('../assets/racikan-freetext', [
                                    'data_racikan' => $data_racikan
                                ]) ?>
                            <?php } ?>

                            <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="example"
                                data-source="<?= Url::home(); ?>apotek/transaksi-resep/list-resep"
                                data-filter=".form-filter" style="width: 100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?= Yii::t('fe', 'R ke') ?></th>
                                        <th><?= Yii::t('fe', 'Nama Obat Alkes') ?></th>
                                        <th><?= Yii::t('fe', 'Signa') ?></th>
                                        <th><?= Yii::t('fe', 'Harga Satuan (Rp.)') ?></th>
                                        <th><?= Yii::t('fe', 'Qty') ?></th>
                                        <th><?= Yii::t('fe', 'Satuan') ?></th>
                                        <th><?= Yii::t('fe', 'Catatan') ?></th>
                                        <th><?= Yii::t('fe', 'Obat Kronis') ?></th>
                                        <th><?= Yii::t('fe', 'Sub Total (Rp.)') ?></th>
                                    </tr>
                                </thead>
                                <tbody id="list-obat">
                                    <tr>
                                        <td colspan="8" class="text-center"><?= Yii::t('fe', 'Data tidak ditemukan') ?></td>
                                    </tr>
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
</div>
<?php
    $this->registerJs($this->render('/assets/js/history-resep/__modal_history_resep.js'), View::POS_END);
    $this->registerCss($this->render('../assets/css/apotek.css'));
    $this->registerJs("
        var btn;
        var status_reseptur = '".$status_resep."'
        var racikan_freetext = ".count($data_racikan).";
        var disable_button_kronis = ".json_encode($button_kronis, true).";
        $(document).ready(function(){
            btn = $('.data-copy').clone();
            var _test = function (data) {
                if($('.footer-total').length < 1){
                    $('.datatable-basic').find('tfoot').append('<tr class=\'footer-subtotal\'><td class=\'text-right\' style=\'width: 69%\'></td><td><strong>Sub Total (Rp.)</strong></td><td class=\'text-right value\'>0</td></tr>');
                    $('.datatable-basic').find('tfoot').append('<tr class=\'footer-biayaadmin\'><td class=\'text-right\' style=\'width: 69%\'></td><td><strong>Biaya Admin (Rp.)</strong></td><td class=\'text-right value\'>0</td></tr>');
                    $('.datatable-basic').find('tfoot').append('<tr class=\'footer-total\'><td class=\'text-right\' style=\'width: 69%\'></td><td><strong>Total (Rp.)</strong></td><td class=\'text-right value\'>0</td></tr>');
                }
            }
            table = $('#example').docoTabel({
                filter: true,
                sorting: [[2, 'asc']],
                bInfo: false,
                paging: false,
                bPaginate: false,
                // displayLength: 10,
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: function(data, callback, settings){
                    $.ajax({
                        url: baseUrl+'apotek/informasi-reseptur/get-data-obat?id=".$_GET['id']."&noresep=".$nomor."&status_reseptur=' + status_reseptur,
                        data: data,
                        success: function(data)
                        {
                            _test(data)
                            callback(data);
                        }
                    });

                },
                drawCallback: function(setting){
                    let data = this.api().rows().data();
                    let dataCount = data.length;
                    let kronis_count = data.reduce(function(total, item){
                            if (item.is_kronis_raw) return total + 1;
                            return total;
                        }, 0)

                    if(dataCount == 0) {
                        $('#btn-cetak-resep').attr('disabled','disabled')
                        $('#btn-cetak-resep-detail').attr('disabled','disabled')
                        $('#btn-cetak-salinan-resep-detail').attr('disabled','disabled')
                        $('#btn-cetak-oral').attr('disabled','disabled')
                        $('#btn-cetak-non-oral').attr('disabled','disabled')
                        $('#btn-cetak-oral-new').attr('disabled','disabled')
                        $('#btn-cetak-non-oral-new').attr('disabled','disabled')
                    }

                    //disable kronis
                    // jika total obat kronis didalam detail tidak ada dan button kronis enable, maka disable kan button
                    if (disable_button_kronis || kronis_count <= 0) {
                        $('#btn-generate-kronis').attr('disabled', true);
                    } else {
                        $('#btn-generate-kronis').attr('disabled', false);
                    }

                    if(racikan_freetext > 0) {
                        $('#btn-cetak-resep-detail').removeAttr('disabled')
                        $('#btn-cetak-salinan-resep-detail').removeAttr('disabled')
                    }
                },
                columns: [
                    {
                        title: 'No',
                        data: 'rowNum',
                        searchable: false,
                        orderable: false
                    },
                    {title: '".(\Yii::t('fe', 'R ke'))."', data: 'rke',searchable: false,orderable: false,},
                    {title: '".(\Yii::t('fe', 'nama obat alkes'))."', data: 'obatalkes_nama',searchable: false,orderable: false},
                    {title: '".(\Yii::t('fe', 'Signa'))."', data: 'signa_nama',searchable: false,orderable: false},
                    {title: '".(\Yii::t('fe', 'Harga satuan').' (Rp.) ')."', data: 'hargajual_satuan',searchable: false,orderable: false, class:'text-right' },
                    {title: '".(\Yii::t('fe', 'Qty'))."',  data: 'qty',searchable: false,orderable: false},
                    {title: '".(\Yii::t('fe', 'satuan'))."',  data: 'satuan_input',searchable: false,orderable: false},
                    {title: '".(\Yii::t('fe', 'Catatan'))."',  data: 'etiket',searchable: false,orderable: false},
                    {title: '".(\Yii::t('fe', 'Obat Kronis'))."',  data: 'is_kronis',searchable: false,orderable: false, class:'text-center'},
                    {title: '".(\Yii::t('fe', 'Sub Total').' (Rp.) ')."',  data: 'totaltagihan',searchable: false,orderable: false, class:'text-right'},
                ],
                footerCallback:  function (row, data, start, end, display) {
                    if (data.length > 0) {
                        let subTotal = 0;
                        $.each(data, function () {
                            subTotal += this.totalharga_jual;
                        });

                        console.log(subTotal);
                        let biayaadministrasi = data[0].biayaadministrasiresep != undefined ? data[0].biayaadministrasiresep : 0;
                        let totalTagihan = subTotal + biayaadministrasi;
                        $('.footer-subtotal').find('td.value').html(docoHelper.convertToRupiah(subTotal));
                        $('.footer-biayaadmin').find('td.value').html(docoHelper.convertToRupiah(biayaadministrasi));
                        $('.footer-total').find('td.value').html(docoHelper.convertToRupiah(totalTagihan));
                    }
                },

            });
            $(document).on('click', '#btn-batal-resep', function(e){
                $(this).docoForm('click', {
                    url: '/apotek/informasi-reseptur/batal-resep?no_resep=".$nomor."',
                    confirmMessage: 'Apakah anda yakin ingin membatalkan resep ini ?',
                    title: 'Sukses',
                    method: 'POST',
                    type: 'json',
                    success: function() {
                        window.location.replace('/apotek/informasi-reseptur');
                    },
                    error: function(response) {
                        var respon = response.responseJSON;
                        if(typeof respon != undefined) {
                            docoNotification('error', 'Proses Gagal', respon.response.message);
                        } else {
                            docoNotification('error', 'Proses Gagal', 'Terjadi Kesalahan');
                        }
                    }
                });

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

<?php

/**
 * @author Randy Vianda Putra
 * @copyright 17 January 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', 'Detail Stok Opname');
$this->params['breadcrumbs'][] = ['label' => 'Informasi Stok Opname', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">                
                <?=DocoHelpers::generateToolbar([
                    'back',
                    // 'print'=>[
                    //     'attributes'=>[
                    //         'data-options'=>'/apotek/informasi-stok-opname/print?id='.$so_id.'&no='.@$data_header['nostokopname'],
                    //     ]
                    // ],
                ]);?> 
                <?= Html::button('<b><i class="fa fa-print"></i></b>' . \Yii::t('fe', 'Cetak'), ['class' => 'btn btn-info btn-labeled btn-xs', 'id' => 'btn-print']) ?>               
            </div>
            <div class="panel-body">                
                <!-- Informasi Resep -->
                <div class="col-md-12 panel panel-flat" id="informasi" style="margin-top:10px;">
                    <div class="panel-heading">
                        <?php
                            $periode_awal = !empty($data_header['periode_awal']) ? date('d-m-Y', strtotime($data_header['periode_awal'])) : '-';
                            $periode_akhir = !empty($data_header['periode_akhir']) ? date('d-m-Y', strtotime($data_header['periode_akhir'])) : '-';
                        ?>
                        <h4 class="panel-title text-center"><?= Yii::t('fe', 'DETAIL STOK OPNAME') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                        <h4 class="panel-title text-center"><?= Yii::t('fe', $data_header['ruangan_nama']) ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                        <!-- <h4 class="panel-title text-center"><?= Yii::t('fe', 'Periode') ?> <?= $periode_awal ?> s/d <?= $periode_akhir ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4> -->
                    </div>
                    <div class="panel-body">
                        <table width="80%" cellpadding="10" class="tabel">
                            <tbody>
                                <tr>
                                    <td class="bold"><b><?= Yii::t('fe', 'Tanggal stok opname') ?></b></td>
                                    <td> : </td>
                                    <td class="header_noResep"><?=@$data_header['tglstokopname']?></td>
                                    <td class="bold"><b><?= Yii::t('fe', 'Tanggal Formulir') ?></b></td>
                                    <td> : </td>
                                    <td class="header_noResep"><?=@$data_header['tglformulir']?></td>
                                    <!-- <td class="bold"><?= Yii::t('fe', 'Periode stok') ?></td>
                                    <td class="header_namaPasien"><?= $periode_awal ?> s/d <?= $periode_akhir ?></td> -->
                                </tr>
                                <tr>
                                    <td class="bold"><b><?= Yii::t('fe', 'No stok opname') ?></b></td>
                                    <td> : </td>
                                    <td class="header_noPendaftaran"><?=@$data_header['nostokopname']?></td>
                                    <td class="bold"><b><?= Yii::t('fe', 'No formulir stok opname') ?></b></td>
                                    <td> : </td>
                                    <td class="header_dokter"><?=@$data_header['noformulir']?></td>
                                </tr>
                                <tr>
                                    <td class="bold"><b><?= Yii::t('fe', 'Jenis stok opname') ?></b></td>
                                    <td> : </td>
                                    <td class="header_noPendaftaran"><?=@$data_header['jenis_stokopname']?></td>
                                </tr>
                            </tbody>
                        </table>
                        <br>
                        <br>
                        <br>
                        <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="tabel-obat">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="1">No</th>
                                    <th><?= Yii::t('fe', 'Nama barang') ?></th>
                                    <th><?= Yii::t('fe', 'Kelompok Barang') ?></th>
                                    <th><?= Yii::t('fe', 'Sub Kelompok Barang') ?></th>
                                    <th><?= Yii::t('fe', 'Tanggal Kadaluarsa') ?></th>
                                    <th><?= Yii::t('fe', 'Stok Sistem') ?></th>
                                    <th><?= Yii::t('fe', 'Stok Fisik') ?></th>
                                    <th><?= Yii::t('fe', 'Selisih') ?></th>
                                    <th><?= Yii::t('fe', 'Kondisi') ?></th>
                                </tr>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $no = 1;
                                    $total_stok_sistem = $total_stok_fisik = $total_selisih_stok = $total_harga_sistem = $total_harga_fisik = $total_harga_netto = 0;
                                    foreach ($data_detail as $key => $value) {
                                        $total_stok_sistem += $value['volume_sistem'];
                                        $total_stok_fisik += $value['volume_fisik'];
                                        $selisih = $value['volume_sistem'] - $value['volume_fisik'];
                                        $total_selisih_stok += $selisih;
                                        $total_harga_sistem += $value['volume_sistem'] * $value['harganetto'];
                                        $total_harga_fisik += $value['volume_fisik'] * $value['harganetto'];
                                        $total_harga_netto = $total_harga_sistem - $total_harga_fisik;
                                ?>  
                                        <tr>
                                            <td><?= $no ?></td>
                                            <td><?= $value['barang_nama'] ?></td>
                                            <td><?= $value['kelompok_barang'] ?></td>
                                            <td><?= $value['subkelompok_barang'] ?></td>
                                            <td><?= isset($value['tglkadaluarsa']) ? $value['tglkadaluarsa'] : '-' ?></td>
                                            <td><?= $value['volume_sistem'] ?></td>
                                            <td><?= $value['volume_fisik'] ?></td>
                                            <td>( <?= $selisih ?> )</td>
                                            <td><?= $value['kondisibarang'] ?></td>
                                        </tr>
                                <?php
                                    $no++;
                                    }
                                ?>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><?= Yii::t('fe', 'Total Stok Sistem') ?></td>
                                        <td><?= $total_stok_sistem ?></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><?= Yii::t('fe', 'Total Stok Fisik') ?></td>
                                        <td><?= $total_stok_fisik ?></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><?= Yii::t('fe', 'Total Selisih Stok') ?></td>
                                        <td>( <?= $total_selisih_stok ?> )</td>
                                        <td></td>
                                    </tr>
                                    <!-- <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><?= Yii::t('fe', 'Total Harga Netto Stok Sistem') ?></td>
                                        <td><?= "Rp. ".number_format($total_harga_sistem, 0, ',', '.'); ?></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><?= Yii::t('fe', 'Total Harga Netto Stok Fisik') ?></td>
                                        <td><?= "Rp. ".number_format($total_harga_fisik, 0, ',', '.'); ?></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><?= Yii::t('fe', 'Total Harga Netto Stok') ?></td>
                                        <td>( <?= "Rp. ".number_format($total_harga_netto, 0, ',', '.'); ?> )</td>
                                        <td></td>
                                    </tr> -->
                            </tbody>
                        </table>
                        <div class="clear"><br></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?php 
    $no = @$data_header['nostokopname'];
    $this->registerJs('
        

    $("#btn-print").on("click",function (event) {
        window.location = "/gudang/informasi-stok-opname/print?id='.$so_id.'&no='.$no.'";
    })

    ',VIEW::POS_END, 'js-kuning');
?>
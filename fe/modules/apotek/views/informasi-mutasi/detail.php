<?php

/**
 * @author Randy Vianda Putra
 * @copyright 16 January 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', $breadcrumb), 'url' => ['obat-alkes']];
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
                        <h3 class="panel-title"><b><?= $this->title ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'pdf' => [
                            'type' => 'link',
                            'attributes' => [
                                'data-options' => 'link',
                                'class' => 'btn btn-info btn-labeled btn-xs data-print',
                                'id' => 'cetak-pdf',
                                'url' => '/apotek/informasi-mutasi/print?id=' . $id
                            ]
                        ],
                        'back' => [
                            'attributes' => [
                                'href' => '/apotek/informasi-mutasi/obat-alkes-keluar'
                            ]],
                    ]);?>
            </div>
            <div class="panel-body">
                <!-- Informasi Resep -->
                <div class="col-md-12 panel panel-flat" id="informasi" style="margin-top:10px;">
                    <div class="panel-heading">
                        <h4 class="panel-title text-center"><?= Yii::t('fe', 'Mutasi Obat Alkes') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                    </div>
                    <div class="panel-body">
                        <table width="100%" class="table-condensed">
                            <tbody>
                                <tr>
                                    <td class=""><b><?= Yii::t('fe', 'Nomor Mutasi') ?></b></td>
                                    <td class=""><?=!empty($header['nomutasioa']) ? $header['nomutasioa'] : "-"; ?></td>
                                    <td class=""><b><?= Yii::t('fe', 'Nomor Penerimaan') ?></b></td>
                                    <td class=""><?=!empty($header['reference']) ? $header['reference'] : "-"; ?></td>
                                    <td class=""><b><?= Yii::t('fe', 'Pegawai Mengetahui') ?></b></td>
                                    <td class=""><?=!empty($header['pegawai_mengetahui']) ? $header['pegawai_mengetahui'] : "-"; ?></td>
                                    <td class=""><b><?= Yii::t('fe', 'Status') ?></b></td>
                                    <td class=""><?=!empty($header['statusmutasi']) ? $header['statusmutasi'] : "-"; ?></td>
                                </tr>
                                <tr>
                                    <td class=""><b><?= Yii::t('fe', 'Ruangan Asal') ?></b></td>
                                    <td class=""><?=!empty($header['ruangan_asal']) ? $header['ruangan_asal'] : "-"; ?></td>
                                    <td class=""><b><?= Yii::t('fe', 'Ruangan Tujuan') ?></b></td>
                                    <td class=""><?=!empty($header['ruangan_nama']) ? $header['ruangan_nama'] : "-"; ?></td>
                                    <td class=""><b><?= Yii::t('fe', 'Pengirim') ?></b></td>
                                    <td class=""><?=!empty($header['pegawai_mutasi']) ? $header['pegawai_mutasi'] : "-"; ?></td>
                                    <td class=""><b><?= Yii::t('fe', 'Penerima') ?></b></td>
                                    <td class=""><?=!empty($header['nama_pegawai_penerima']) ? $header['nama_pegawai_penerima'] : "-"; ?></td>
                                </tr>
                                <tr>
                                    <td class=""><b><?= Yii::t('fe', 'Tanggal Kirim') ?></b></td>
                                    <td class=""><?=!empty($header['tglmutasioa']) ? date("d M Y", strtotime($header['tglmutasioa'])) : "-"; ?></td>
                                    <td class=""><b><?= Yii::t('fe', 'Tanggal Terima') ?></b></td>
                                    <td class=""><?=!empty($header['tgl_terima']) ? date("d M Y", strtotime($header['tgl_terima'])) : "-"; ?></td>
                                </tr>
                            </tbody>
                        </table>
                        <br>
                        <br>
                        <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="tabel-obat">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="1">No</th>
                                    <th><?= Yii::t('fe', 'Nama Obat Alkes') ?></th>
                                    <th><?= Yii::t('fe', 'Tanggal Kadaluarsa') ?></th>
                                    <th><?= Yii::t('fe', 'Qty Mutasi') ?></th>
                                    <th><?= Yii::t('fe', 'Qty Konversi') ?></th>
                                </tr>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if(count($data_obatalkes)):
                                    foreach ($data_obatalkes as $key => $value){
                                    ?>
                                        <tr class="default-value">
                                            <td><?=$value['rowNum']?></td>
                                            <td><?=$value['obatalkes_nama']?></td>
                                            <td><?=$value['expired']?></td>
                                            <td><?=$value['jumlah_mutasi']. ' '. $value['satuan_mutasi']?></td>
                                            <td><?=$value['jumlah_mutasi']. ' '. $value['satuan_mutasi']?></td>
                                        </tr>
                                    <?php
                                    }
                                else:
                                    ?>
                                    <tr>
                                        <td class="text-center" colspan="4"><?=Yii::t('fe', 'Data Tidak Ditemukan!')?></td>
                                    </tr>
                                    <?php
                                endif;
                                ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    $("#cetak-pdf").on("click",function (event) {
        event.preventDefault();
        var url = window.location.origin;
        var target = $(this).attr(\'data-target\');
        window.open(url+target);
    });
    $(".data-back").on("click", function (event) {
        event.preventDefault();
        window.history.back();
    });
',View::POS_END,'b-index');

?>

<script src=""></script>
<?php
    $this->registerCss($this->render('../assets/css/apotek.css'));
?>



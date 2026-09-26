<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-09 11:00:02
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-03-19 15:02:40
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Informasi Pemesanan Obat Alkes', 'url' => ['index']];
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
                <?=
                    DocoHelpers::generateToolbar([
                        'back',
                    ]);
                ?>
                <?=
                    Html::button('<b><i class="fa fa-file-pdf-o"></i></b>' . \Yii::t('fe', 'Cetak'), [
                        'class' => 'btn btn-labeled btn-xs btn-info',
                        'id' => 'btn-print',
                        'data-target' => '/apotek/transaksi-pemesanan/cetak-pdf?id='.$id
                    ]);
                ?>
            </div>
            <div class="panel-body">
                <!-- Informasi Resep -->
                <div class="col-md-12 panel panel-flat" id="informasi" style="margin-top:10px;">
                    <div class="panel-heading">
                        <h4 class="panel-title text-center"><?= Yii::t('fe', 'PEMESANAN OBAT ALKES') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>

                    </div>
                    <div class="panel-body">
                        <table width="80%" cellpadding="10" class="tabel">
                            <tbody>
                                <tr>
                                    <td class="bold"><?= Yii::t('fe', 'Tanggal pemesanan') ?></td>
                                    <td class="header_noResep"><?=$data['tglpemesanan']?></td>
                                    <td class="bold"><?= Yii::t('fe', 'Nomor pemesanan') ?></td>
                                    <td class="header_namaPasien"><?=$data['nopemesanan']?></td>
                                </tr>
                                <tr>
                                    <td class="bold"><?= Yii::t('fe', 'Tanggal minta dikirim') ?></td>
                                    <td class="header_noPendaftaran"><?=$data['tglmintadikirim']?></td>
                                    <td class="bold"><?= Yii::t('fe', 'Ruangan Tujuan') ?></td>
                                    <td class="header_noPendaftaran"><?=$data['ruangan_tujuan']?></td>
                                </tr>
                                <tr>
                                    <td class="bold">&nbsp;</td>
                                    <td class="bold">&nbsp;</td>
                                    <td class="bold"><?= Yii::t('fe', 'Ruangan Pemesan') ?></td>
                                    <td class="header_ruangan"><?=$data['ruangan_pemesan']?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Info Penjualan Resep Bebas -->
                <div class="col-md-12 panel panel-flat" style="margin-top:10px;">
                    <div class="panel-heading">
                        <h4 class="panel-title"><?= Yii::t('fe', 'Data Obat Alkes') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
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
                                    <th><?= Yii::t('fe', 'Nama Obat Alkes') ?></th>
                                    <th><?= Yii::t('fe', 'Qty') ?></th>
                                    <th><?= Yii::t('fe', 'Satuan besar') ?></th>
                                    <th><?= Yii::t('fe', 'Qty') ?></th>
                                    <th><?= Yii::t('fe', 'Satuan kecil') ?></th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>

                            </tfoot>
                        </table>
                        <?= Yii::t('fe', 'Catatan :'); ?>
                        <textarea name="" id="" disabled cols="30" rows="10" class="form-control"><?= $data['keterangan_pesan']; ?></textarea>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?php
    $this->registerCss($this->render('../assets/css/apotek.css'));
    $this->registerJs("
        $(document).ready(function(){
            table = $('#example').docoTabel({
                filter: true,
                sorting: [[1, 'asc']],
                displayLength: 10,
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: function(data, callback, settings){
                    $.ajax({
                        url: baseUrl+'apotek/inf-pemesanan-obat-alkes/get-data-obat?id=".$_GET['id']."',
                        data: data,
                        success: function(data)
                        {
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
                    {title: '".(\Yii::t('fe', 'Nama Obat Alkes'))."', data: 'obatalkes_namalain',searchable: false,orderable: false},
                    {title: '".(\Yii::t('fe', 'Qty'))."', data: 'qty_besar',searchable: false,orderable: false},
                    {title: '".(\Yii::t('fe', 'Satuan besar'))."',  data: 'satuan_besar',searchable: false,orderable: false},
                    {title: '".(\Yii::t('fe', 'Qty'))."',  data: 'jumlah_pesan',searchable: false,orderable: false},
                    {title: '".(\Yii::t('fe', 'Satuan kecil'))."',  data: 'satuan_kecil',searchable: false,orderable: false},
                ],

            });
        $('.dataTables_filter').hide();

        $('#btn-print').on('click',function (event) {
            event.preventDefault();
            var url = window.location.origin;
            var target = $(this).attr(\"data-target\");
            window.open(url+target);
        });
    });
        ",VIEW::POS_END, 'js-kuning');
?>
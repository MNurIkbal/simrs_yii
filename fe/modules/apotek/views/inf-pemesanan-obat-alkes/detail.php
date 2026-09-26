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

<style>
    .table-slim th,  .table-slim td{
        padding: 6px 10px !important;
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar($btn_toolbar);?>
            </div>
            <div class="panel-body">
                <!-- Informasi Resep -->
                <div class="col-md-12 panel panel-flat" id="informasi" style="margin-top:10px;">
                    <div class="panel-body">
                        <table class="table table-borderless table-slim" style="width: 100%;font-size: 13px;">
                            <tbody>
                                <tr>
                                    <th style="width: 150px"><?= Yii::t('fe', 'Tanggal Pemesanan') ?></th>
                                    <td>: <?= $data['tglpemesanan'] ?></td>
                                    <th style="width: 200px"><?= Yii::t('fe', 'Instalasi - Ruangan Pemesan') ?></th>
                                    <td>: <?= $data['instalasi_pemesan']." - ".$data['ruangan_pemesan'] ?></td>
                                    <th style="width: 120px"><?= Yii::t('fe', 'Nama Pemesan') ?></th>
                                    <td>: <?= $data['pemesan'] ?></td>
                                </tr>
                                <tr>
                                    <th><?= Yii::t('fe', 'Tanggal Kirim') ?></th>
                                    <td>: <?= $data['tglmutasioa'] ?></td>
                                    <th><?= Yii::t('fe', 'No. Pemesanan') ?></th>
                                    <td>: <?= $data['nopemesanan'] ?></td>
                                    <th><?= Yii::t('fe', 'Nama Pengirim') ?></th>
                                    <td>: <?= $data['pengirim'] ?></td>
                                </tr>
                                <tr>
                                    <th><?= Yii::t('fe', 'Status') ?></th>
                                    <td>: <?= $data['status_distribusi'] ?></td>
                                    <th><?= Yii::t('fe', 'No. Pengiriman') ?></th>
                                    <td>: <?= $data['nomutasioa'] ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Info Penjualan Resep Bebas -->
                <div class="col-md-12 panel panel-flat" style="margin-top:10px;">
                    <div class="panel-heading">
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
                                    <th><?= Yii::t('fe', 'Qty Pesan') ?></th>
                                    <th><?= Yii::t('fe', 'Satuan') ?></th>
                                    <th><?= Yii::t('fe', 'Qty Kirim') ?></th>
                                    <th><?= Yii::t('fe', 'Satuan') ?></th>
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
        $(document).ready(function(){
            table = $('#example').docoTabel({
                filter: false,
                sorting: [[1, 'asc']],
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: function(data, callback, settings){
                    $.ajax({
                        url: baseUrl+'apotek/inf-pemesanan-obat-alkes/data-detail?id=".$_GET['id']."',
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
                    {
                        title: '".(\Yii::t('fe', 'Nama Obat Alkes'))."',
                        data: 'obatalkes_nama',
                        searchable: false
                    },
                    {
                        title: '".(\Yii::t('fe', 'Qty Pesan'))."',
                        data: 'qty_besar',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: '".(\Yii::t('fe', 'Satuan'))."',
                        data: 'satuan_besar',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: '".(\Yii::t('fe', 'Qty Kirim'))."',
                        data: 'jumlah_mutasi',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: '".(\Yii::t('fe', 'Satuan'))."',
                        data: 'satuan_kirim',
                        searchable: false,
                        orderable: false
                    },
                ],

            });
        $('.dataTables_filter').hide();

        $('#btn-print').on('click',function (event) {
            event.preventDefault();
            var url = window.location.origin;
            var target = $(this).attr(\"data-target\");
            window.open(url+target);
        });

        $('#batal-pemesanan').on('click',function(){
            $(this).docoForm('click', {
                url: $(this).data('url'),
                method: 'GET',
                skipErrorNotif: true,
                confirmMessage: 'Apakah Anda yakin untuk membatalkan pemesanan obat alkes ini?',
                success: function (data) {
                    setTimeout(() => {
                        window.location.href = '/apotek/inf-pemesanan-obat-alkes/';
                    }, 200);
                }
            });
        });
    });
        ",VIEW::POS_END, 'js-kuning');
?>
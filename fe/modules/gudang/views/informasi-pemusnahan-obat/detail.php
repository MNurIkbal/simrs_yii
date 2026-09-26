<?php

/**
 * @author Randy Vianda Putra
 * @copyright 8 Juni 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$source = Yii::t('fe', $headTitle);
$this->params['breadcrumbs'][] = ['label' => $instalasi, 'url' => []];
$this->params['breadcrumbs'][] = ['label' => $source, 'url' => ['/gudang/informasi-pemusnahan-obat']];
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
                <?=
                    DocoHelpers::generateToolbar($btn_toolbar);
                ?>
            </div>
            <div class="panel-body">
                <br>
                <!-- Informasi -->
                <div class="row">
                    <div class="col-md-4">
                        <table width="100%">
                            <tr>
                                <td><b><?= Yii::t('fe', 'Tanggal Pemusnahan') ?></b></td>
                                <td>:&emsp;</td>
                                <td class="header_tglpemusnahan"><?= !empty($data['tglpemusnahan']) ? date('d-M-Y', strtotime($data['tglpemusnahan'])) : '-'?></td>
                            </tr>
                            <tr>
                                <td width="120"><b><?= Yii::t('fe', 'Nomor Pemusnahan') ?></b></td>
                                <td width="1">:&emsp;</td>
                                <td class="header_nopemusnahan"><?=!empty($data['nopemusnahan']) ? $data['nopemusnahan'] : '-' ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <table width="90%" style="margin-left: 20px">
                            <tbody>
                                <tr>
                                    <td width="120"><b><?= Yii::t('fe', 'Pegawai Pelaksana') ?></b></td>
                                    <td width="1">:&emsp;</td>
                                    <td class="header_pegawai_mengetahui"><?= !empty($data['nama_pegawai']) ? $data['nama_pegawai'] : '-'?></td>
                                </tr>
                                <tr>
                                    <td width="120"><b><?= Yii::t('fe', 'Pegawai Mengetahui') ?></b></td>
                                    <td width="1">:&emsp;</td>
                                    <td class="header_pegawai_mengetahui"><?= !empty($data['pegawai_mengetahui']) ? $data['pegawai_mengetahui'] : '-'?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-4">
                        <table width="100%">
                            <tr>
                                <td width="190"><b><?= Yii::t('fe', 'Status Pemusnahan') ?></b></td>
                                <td width="1">:&emsp;</td>
                                <td class="header_verifikasi"><?= $data['status_pemusnahan']?></td>
                            </tr>
                            <tr>
                                <td width="190"><b><?= Yii::t('fe', 'Total Harga (Rp.)') ?></b></td>
                                <td width="1">:&emsp;</td>
                                <td class="header_total"><b><?= !empty($data['total_harganetto']) ? DocoHelpers::formatNumber($data['total_harganetto']) : '-' ?></b></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <br>

                <table class="table datatable-basic table-striped table-hover dataTable" id="example" style="width: 100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?= Yii::t('fe', 'Kode Obat Alkes') ?></th>
                            <th><?= Yii::t('fe', 'Nama Obat Alkes') ?></th>
                            <th><?= Yii::t('fe', 'Tanggal Expired') ?></th>
                            <th><?= Yii::t('fe', 'Qty') ?></th>
                            <th><?= Yii::t('fe', 'Satuan') ?></th>
                            <th><?= Yii::t('fe', 'Jumlah Harga Netto (Rp.)') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
    $this->registerJs("
        var btn;
        $(document).ready(function(){
            btn = $('.data-copy').clone();
            table = $('#example').docoTabel({
                filter: true,
                sorting: [[1, 'asc']],
                displayLength: 10,
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: baseUrl+'gudang/informasi-pemusnahan-obat/get-data-pemusnahan?id=".$_GET['id']."',
                columns: [
                    {
                        title: 'No',
                        data: 'rowNum',
                        searchable: false,
                        orderable: false
                    },
                    {title: '".(\Yii::t('fe', 'Kode Obat Alkes'))."', data: 'obatalkes_kode', searchable: false, orderable: false},
                    {title: '".(\Yii::t('fe', 'Nama Obat Alkes'))."', data: 'obatalkes_nama', searchable: false, orderable: false},
                    {title: '".(\Yii::t('fe', 'Tanggal Expired'))."', data: 'tglkadaluarsa', searchable: false, orderable: false},
                    {
                        title: '".(\Yii::t('fe', 'Qty'))."',
                        data: 'stok',
                        searchable: false,
                        orderable: false,
                        class: 'text-right',
                        width: '140px'
                    },
                    {title: '".(\Yii::t('fe', 'Satuan'))."',  data: 'satuan_kecil', searchable: false, orderable: false},
                    {title: '".(\Yii::t('fe', 'Jumlah Harga Netto (Rp.)'))."',  data: 'jumlah_harganetto', class: 'text-right', searchable: false, orderable: false},
                ],
            });
        $('.dataTables_filter').hide();

        $('#verifikasi-pemusnahan').on('click',function(){
            $.ajax({
              method: 'GET',
              url: $(this).data('url')
            }).done(function(data, textStatus, jqXHR) {
                var data = jqXHR.responseJSON;
                docoNotification('success', data.response.message, data.response.text);
                setTimeout(() => {
                    window.location.href = '/gudang/informasi-pemusnahan-obat';
                }, 150);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                var data = jqXHR.responseJSON;
                docoNotification('error', data.response.message, data.response.text);
            });
        });

        $('#batal-pemusnahan').on('click',function(){
            $.ajax({
              method: 'GET',
              url: $(this).data('url')
            }).done(function(data, textStatus, jqXHR) {
                var data = jqXHR.responseJSON;
                docoNotification('success', data.response.message, data.response.text);
                setTimeout(() => {
                    window.location.href = '/gudang/informasi-pemusnahan-obat';
                }, 150);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                var data = jqXHR.responseJSON;
                docoNotification('error', data.response.message, data.response.text);
            });
        });
    });
    ",VIEW::POS_END, 'js-kuning');
?>
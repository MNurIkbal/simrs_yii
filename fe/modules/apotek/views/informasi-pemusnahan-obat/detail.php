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
$this->params['breadcrumbs'][] = ['label' => $instalasi, 'url' => []];
$this->params['breadcrumbs'][] = ['label' => 'Informasi', 'url' => ['/apotek']];
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
                        'print'=>[
                            'attributes' => [
                                'data-target' => '/apotek/informasi-pemusnahan-obat/print-pemusnahan?id='.$id.'&nopemusnahan='.$data['nopemusnahan'].'&',
                            ]
                        ],
                    ]);
                ?>
            </div>
            <div class="panel-body">
                <br>
                <!-- Informasi -->
                <table width="100%" cellpadding="10" class="tabel">
                    <tbody>
                        <tr>
                            <td class="bold"><?= Yii::t('fe', 'No pemusnahan') ?></td>
                            <td class="header_nopemusnahan"><?=!empty($data['nopemusnahan']) ? $data['nopemusnahan'] : '-' ?></td>
                            <td class="bold"><?= Yii::t('fe', 'Instalasi pelaksana') ?></td>
                            <td class="header_instalasi_nama"><?= !empty($data['instalasi_nama']) ? $data['instalasi_nama'] : '-' ?></td>
                            <td class="bold"><?= Yii::t('fe', 'Pegawai mengetahui') ?></td>
                            <td class="header_pegawai_mengetahui"><?= !empty($data['pegawai_mengetahui']) ? $data['pegawai_mengetahui'] : '-'?></td>
                        </tr>
                        <tr>
                            <td class="bold"><?= Yii::t('fe', 'Tanggal pemusnahan') ?></td>
                            <td class="header_tglpemusnahan"><?= !empty($data['tglpemusnahan']) ? date('d-M-Y', strtotime($data['tglpemusnahan'])) : '-'?></td>
                            <td class="bold"><?= Yii::t('fe', 'Ruang pelaksana') ?></td>
                            <td class="header_ruangan"><?= !empty($data['ruangan_nama']) ? $data['ruangan_nama'] : '-' ?></td>
                            <td class="bold"><?= Yii::t('fe', 'Pegawai menyetujui') ?></td>
                            <td class="header_pegawai_menyetujui"><?= !empty($data['pegawai_menyetujui']) ? $data['pegawai_menyetujui'] : '-'?></td>
                        </tr>
                    </tbody>
                </table>
                <br>
                <!-- Info Penjualan Resep Bebas -->
                <table class="table datatable-basic table-striped table-hover dataTable" id="example" style="width: 100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?= Yii::t('fe', 'Nama Obat Alkes') ?></th>
                            <th><?= Yii::t('fe', 'Tanggal Expired') ?></th>
                            <th><?= Yii::t('fe', 'Qty') ?></th>
                            <th><?= Yii::t('fe', 'Satuan Kecil') ?></th>
                            <th><?= Yii::t('fe', 'Jumlah Harga Netto') ?></th>
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
    $this->registerCss($this->render('../assets/css/apotek.css'));
    $this->registerJs("
        var btn;
        $(document).ready(function(){
            btn = $('.data-copy').clone();
            var _test = function (data) {
                if($('.footer-total').length < 1){
                    $('.datatable-basic').find('tfoot').append('<tr class=\'footer-total\'><td class=\'text-right\' style=\'width: 70%\'></td><td>Total</td><td>'+data.totalobat+'</td></tr>');
                }
            }
            table = $('#example').docoTabel({
                filter: true,
                sorting: [[2, 'asc']], 
                displayLength: 10,
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: function(data, callback, settings){
                    $.ajax({
                        url: baseUrl+'apotek/informasi-pemusnahan-obat/get-data-pemusnahan?id=".$_GET['id']."',
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
                    {title: '".(\Yii::t('fe', 'Nama Obat Alkes'))."', data: 'obatalkes_nama', searchable: false, orderable: false},
                    {title: '".(\Yii::t('fe', 'Tanggal Expired'))."', data: 'tglkadaluarsa', searchable: false, orderable: false},
                    {title: '".(\Yii::t('fe', 'Qty'))."',  data: 'stok', searchable: false, orderable: false},
                    {title: '".(\Yii::t('fe', 'Satuan Kecil'))."',  data: 'satuan_kecil', searchable: false, orderable: false},
                    {title: '".(\Yii::t('fe', 'Jumlah Harga Netto'))."',  data: 'jumlah_harganetto', searchable: false, orderable: false},
                ],
            });
        $('.dataTables_filter').hide();
        // $(table.table().footer()).html('Your html content here ....');
    });
    ",VIEW::POS_END, 'js-kuning');
?>
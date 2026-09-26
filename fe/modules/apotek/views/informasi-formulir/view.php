<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-04-23 14:47:11
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-03-15 14:08:38
 * @Last Modified by:   Muhamad Lukman Hakim
 * @Last Modified time: 2021-03-24 15:39:00
 * @Description: 
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' =>  Yii::$app->docoVars->workspace("instalasi_name"), 'url' => ['/apotek']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['stok-opname']];
$this->params['breadcrumbs'][] = $subtitle;

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
                        <h3 class="panel-title"><b><?= $subtitle; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'back' => ['attributes' => ['href' => $default_url]],
                    'print-custom'=>[
                        'type'=>'button',
                        'title' => \Yii::t('fe', 'Cetak PDF'),
                        'icon' => 'fa fa-file-pdf-o',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id'=>'btn-cetak-form-so',
                            'class'=>'cetak-form-so',
                            'target'=>'_blank',
                            'class'=>'spa',
                            'data-options'=>'link',
                            'data-target' => '/apotek/transaksi-formulir/print-pdf?id='.$id.'&formulir='.@$data['noformulir']
                        ]
                    ]
                ]);?>
            </div>
            <div class="panel-body">
                <!-- Informasi Resep -->
                <div class="col-md-12 panel panel-flat" id="informasi">
                    <div class="panel-heading text-center">
                        <h3 class="panel-title"><?= $ruangan_nama ?></h3>
                    </div>
                    <div class="panel-body" style="margin:10px;">
                        <div class="col-md-6">
                            <div class="col-md-3 bold"><?= Yii::t('fe', 'Tanggal Formulir') ?></div>
                            <div class="col-md-3"><?=date('d-M-Y', strtotime(@$data['tglformulir']));?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-3 bold"><?= Yii::t('fe', 'Nomor Formulir') ?></div>
                            <div class="col-md-3"><?=@$data['noformulir'];?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 panel panel-flat" id="informasi" style="float: right;">
                    <br>
                    <div class="panel-body">
                        <table width="100%" class="table table-striped table-condensed table-hover" id="tabel-obat">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="1">No</th>
                                    <th><?= Yii::t('fe', 'Nama Obat Alkes') ?></th>
                                    <th><?= Yii::t('fe', 'Rak Obat') ?></th>
                                    <th><?= Yii::t('fe', 'Stok Sistem') ?></th>
                                    <th width="10px"><?= Yii::t('fe', 'Stok Fisik') ?></th>
                                    <th><?= Yii::t('fe', 'Catatan') ?></th>
                                </tr>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php 
    $this->registerCss($this->render('../assets/css/apotek.css'));
    $this->registerJs($this->render('js/informasi_formulir.js'));
    $this->registerJs("
        $(document).ready(function(){
            table = $('#tabel-obat').docoTabel({
                filter: false,
                sorting: [[2,'desc']],
                displayLength: 10,
                processing: true,
                serverSide: true,
                scrollX: true,
                paging: false,
                ajax: baseUrl+'apotek/informasi-formulir/get-data-detail?id=". $id ."',
                columns: [
                    {
                        title: 'No',
                        data: 'rowNum',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: '".Yii::t('fe', 'Nama Obat Alkes')."',
                        data: 'obatalkes_namalain',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: '".Yii::t('fe', 'Rak Obat')."',
                        data: 'rakobat_nama',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: '".Yii::t('fe', 'Stok Sistem')."',
                        data: 'stok_sistem',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: '".\Yii::t("fe", "Stok Fisik")."',
                        data: 'stok_fisik',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: '".\Yii::t("fe", "Catatan")."',
                        data: 'kondisi',
                        searchable: false,
                        orderable: false,
                    }
                ]
            });

            $('.dataTables_filter').hide();
        });
        ");
?>

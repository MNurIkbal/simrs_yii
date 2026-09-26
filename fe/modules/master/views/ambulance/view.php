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
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['obat-alkes']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style type="text/css">
    .plat-nomor{
        font-size: 18px;
        font-weight: 900;
        letter-spacing: 2px;
        color: #fff;
    }
    .background-plat{
        /*border: 1px solid transparent;*/
        text-align: center;
        display: inline-block;
        position: relative;
        width: 165px;
        padding: 5px;
        border-color: #fff;
        border-radius: 5px;
        box-sizing: border-box;
        background-color: #54be8b; /*#001;*/
        /*border: 1px solid #291ce8;*/
    }
    .f-right{
        float: right;
        text-align: -webkit-right;
    }
    .p-17{
        padding:17px 0px !important;
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
                                'url' => '/master/ambulance/cetak?id=' . $id
                            ]
                        ],
                        'back',
                    ]);?>
            </div> 
            <div class="panel-body">                
                <div class="row">
                    <div class="col-md-12" id="informasi" style="margin-top:10px;">
                        <!-- Detail Ambulam start -->
                        <div class="panel panel-heading p-17">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="text-left control-label col-sm-2">
                                            <strong><?= Yii::t("fe", "Nama Barang") ?></strong>
                                        </div>
                                        <div class="col-sm-10">
                                            <div class="col-sm-1" style="width: 2%;"><?= Yii::t("fe", " : ") ?></div>
                                            <div class="col-sm-11">
                                                <?= $header['barang_nama'] ?><strong><?= ' - ' ?></strong>
                                                <?= $header['is_emergency'] ?><strong><?= ' - ' ?></strong>
                                                <?= $header['barang_merk'] ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="col-md-12 f-right">
                                            <div class="background-plat">
                                                <span class="plat-nomor">
                                                    <?= $header['no_polisi'] ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="text-left control-label col-sm-2">
                                            <strong><?= Yii::t("fe", "Note") ?></strong>
                                        </div>
                                        <div class="col-sm-10">
                                            <div class="col-sm-1" style="width: 2%;"><?= Yii::t("fe", " : ") ?></div>
                                            <div class="col-sm-11"><?= $header['keterangan'] ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- Detail Ambulam end -->

                        <!-- riwayat pasien start -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 ><strong><?=Yii::t('fe', 'Tarif Ambulan')?></strong></h5>
                            </div>
                            <div class="panel-body">
                                <table class="table datatable-basic table-striped table-hover dataTable no-footer" style="width:100%">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th>No</th>
                                            <th><?=\Yii::t("fe", "Tindakan");?></th>
                                            <th><?=\Yii::t("fe", "Biaya Tetap");?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach ($tindakan as $key => $value) : ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= $value['daftartindakan_nama'] ;?></td>
                                                <td><?= $value['biaya_tetap'] ;?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                         <!-- riwayat pasien end -->

                         <!-- Default Obat Alkes Yang Dibawa start -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 ><strong><?=Yii::t('fe', 'Default Obat Alkes Yang Dibawa')?></strong></h5>
                            </div>
                            <div class="panel-body">
                                <table class="table datatable-basic table-striped table-hover dataTable no-footer" style="width:100%">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th>No</th>
                                            <th><?=\Yii::t("fe", "Nama Obat Alkes");?></th>
                                            <th style="text-align: right;"><?=\Yii::t("fe", "Qty");?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach ($obat as $key => $value) : ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><?= $value['obatalkes_nama'] ;?></td>
                                                <td style="text-align: right;"><?= $value['qty'].' '.$value['satuanunit_nama'] ;?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                         <!-- Default Obat Alkes Yang Dibawa end -->
                    
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





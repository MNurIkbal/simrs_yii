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
use app\components\DocoConstants;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Informasi Adjustment', 'url' => ['index']];
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
                        <h3 class="panel-title"><b><?= $title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'back',
                    'print-custom'=>[
                        'type'=>'button',
                        'title' => \Yii::t('fe', 'Cetak'),
                        'icon' => 'fa fa-print',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id'=>'btn-cetak-adjustment',
                            'class'=>'cetak-adjustment',
                            'target'=>'_blank',
                            'class'=>'spa',
                            'data-options'=>'link',
                            'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.'adjustment-obat-alkes/cetak?no_adjusmen='.$no_adjustment.'&tipe='.$jenis_adjustment
                        ]
                    ],

                ]);?>
                <?=DocoHelpers::generateToolbar([
                    'pdf' => [
                        'type' => 'link',
                        'attributes' => [
                            'data-options' => 'link',
                            'class' => 'btn btn-info btn-labeled btn-xs data-print',
                            'id' => 'cetak-pdf'
                        ]
                    ],
                ]);?>
            </div>
            <div class="panel-body" style="padding:10px;">
                <!-- Informasi Resep -->
                <div class="col-md-12">
                    <div class="panel panel-default" id="informasi" style="margin-top:10px;">
                        <div class="panel-heading">
                            <h5 class="panel-title"><?= Yii::t('fe', 'Adjustment Obat Alkes') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="col-md-5 detail-adjustment bold"><?= Yii::t('fe', 'No. Adjustment') ?></div>
                                    <div class="col-md-5 detail-adjustment text-left"><?= $no_adjustment ?></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="col-md-5 detail-adjustment bold"><?= Yii::t('fe', 'Jenis Adjustment') ?></div>
                                    <div class="col-md-5 detail-adjustment text-left"><?php
                                    if($jenis_adjustment == DocoConstants::ADJUSTMENT_MASUK) {
                                        echo "Adjustment Masuk";
                                    } else if($jenis_adjustment == DocoConstants::ADJUSTMENT_KELUAR) {
                                        echo "Adjustment Keluar";
                                    }
                                    ?></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="col-md-5 detail-adjustment bold"><?= Yii::t('fe', 'Tanggal Adjustment') ?></div>
                                    <div class="col-md-5 detail-adjustment text-left"><?= $tgl_adjustment ?></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="col-md-5 detail-adjustment bold"><?= Yii::t('fe', 'Pegawai Mengetahui') ?></div>
                                    <div class="col-md-5 detail-adjustment text-left"><?= $pegawai_mengetahui ?></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="col-md-5 detail-adjustment bold"><?= Yii::t('fe', 'Pegawai Menyetujui') ?></div>
                                    <div class="col-md-5 detail-adjustment text-left"><?= $pegawai_menyetujui ?></div>
                                </div>
                                <div class="col-md-4">
                                    <div class="col-md-5 detail-adjustment bold"><?= Yii::t('fe', 'Pegawai Input') ?></div>
                                    <div class="col-md-5 detail-adjustment text-left"><?= $pegawai_input; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="panel panel-default" id="informasi">
                        <div class="panel-heading">
                            <h5 class="panel-title"><?= Yii::t('fe', 'Detail Obat') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <table class="table datatable-basic table-striped table-hover dataTable no-footer t-detail-adjustment" id="example" style="width: 100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?= Yii::t('fe', 'Kode Obat Alkes') ?></th>
                                        <th><?= Yii::t('fe', 'Nama Obat Alkes') ?></th>
                                        <?php if($jenis_adjustment == DocoConstants::ADJUSTMENT_MASUK): ?>
                                        <th><?= Yii::t('fe', 'Qty Penerimaan') ?></th>
                                        <?php else: ?>
                                        <th><?= Yii::t('fe', 'Qty Pengeluaran') ?></th>
                                        <?php endif; ?>
                                        <th><?= Yii::t('fe', 'Qty Konversi') ?></th>
                                        <th></th>
                                        <?php if($jenis_adjustment == DocoConstants::ADJUSTMENT_MASUK): ?><th></th><?php endif; ?>
                                        <th><?= Yii::t('fe', 'No. Batch') ?></th>
                                        <th><?= Yii::t('fe', 'Keterangan') ?></th>
                                    </tr>
                                </thead>
                                <tbody id="list-adjustment">
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
    $obatalkes = $_GET['id'];
    // jenis_adjustment = 1 = adjustment keluar
    // jenis_adjustmet = 0 = adjustment masuk
    if($jenis_adjustment == '1') {
        $this->registerJs($this->render('js/detail-obat-keluar.js'));
        $type = 1;
    } else {
        $this->registerJs($this->render('js/detail-obat-masuk.js'));
        $type = 0;
    }
    $this->registerJs("
        var obatalkes = '$obatalkes';
        var no_adjustment = '$no_adjustment';
        var jenis = '$jenis_adjustment';

       $('#cetak-pdf').on('click',function (event) {
            event.preventDefault();
            window.open('/gudang/adjustment-obat-alkes/cetak?no_adjusmen='+'$no_adjustment'+'&tipe='+$type);
        });
    ",VIEW::POS_END, 'js-kuning');
    
?>

<?php

use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use yii\web\View;
use yii\helpers\Html;
use app\components\DocoConstants;
use yii\helpers\ArrayHelper;
use app\components\DHtml;

$this->title = isset($title) ? $title : Yii::t('fe', 'Rekam Medis');
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medis', 'url' => ['index']];
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
                    <h3 class="panel-title"><b>
                        <?php echo $this->title; ?>
                    </b></h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
                </div>
                <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
            </div>

            <div class="panel-body">
                <div class="col-md-12 filter-form">
                    <form class="advancedFilter" onsubmit="return false;">
                        <div class="row" id="ffBody"></div>
                        <div class="row" id="ffBody">
                            <div class="form-group col-md-3">
                                <label>Range Tanggal :</label>
                                <div class="form-group">
                                    <div class='input-group'>
                                        <input value="<?= date('01-M-Y') ?>" type='text' id='rangeDemoStart' class='form-control startDate' />
                                        <span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span>
                                        <input value="<?= date('d-M-Y') ?>" type='text' id='rangeDemoFinish' class='form-control endDate' />
                                        <input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <?php foreach($listLaporan as $key => $value): ?>
                                <div class="col-sm-12">
                                    <?= Html::activeRadio($model, 'jenisLaporan', [
                                        'label' => ArrayHelper::getValue($value, 'jenis_laporan'), 
                                        'value' => ArrayHelper::getValue($value, 'jenis_laporan'), 
                                        'class' => 'laporan_kasir',
                                        'id' => "jenis_laporan_". ArrayHelper::getValue($value, 'konfiglaporan_id')
                                    ]) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <hr>
                        <div class="btn-group pull-left">
                            <?= Html::button('<b><i class="fa fa-file-excel-o"></i></b>'.Yii::t('fe', ' Export Excel'),
                                [
                                    'class' => 'btn btn-info btn-labeled btn-xs',
                                    'id' => 'export-excel'
                                ]);
                            ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("
$(document).ready(function () {
    $('.flex-1').addClass('hidden')
    dateRangeHelper('.startDate', '.endDate', '.targetDate');
    $('#cari').prop('disabled', false);
});
var laporan_pembelian = ".DocoConstants::LAPORAN_PEMBELIAN.";
var pengeluaran_kasir = ".DocoConstants::LAPORAN_PENGELUARAN_KASIR.";
var kas_harian = ".DocoConstants::LAPORAN_KAS_HARIAN.";
var penerimaan_harian = ".DocoConstants::LAPORAN_PENERIMAAN_HARIAN.";
var rekap_kasir = ".DocoConstants::REKAP_LAPORAN_KASIR.";
", View::POS_END , "b-index");
$this->registerJs($this->render('index.js'), View::POS_END);
?>

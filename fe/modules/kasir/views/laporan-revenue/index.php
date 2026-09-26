<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use kartik\widgets\DatePicker;
use yii\web\JsExpression;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .dataTables_scroll {
        max-height: 1000px;
        overflow: auto;
        position: relative;
    }

    .bg-yellow {
        background-color: #FCF3CF;
        color: #000000;
        font-weight: bold;
    }

    .bg-total {
        background-color: #EAECEE;
        color: #000000;
        font-weight: bold;
    }

    .bg-discount {
        background-color: #ABEBC6;
        color: #000000;
        font-weight: bold;
    }

    .bg-total-revenue {
        background-color: #AED6F1;
        color: #000000;
        font-weight: bold;
    }

    .bg-grand-total {
        background-color: #F7DC6F;
        color: #000000;
        font-weight: bold;
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
                      <h3 class="panel-title"><b><?= Yii::t('fe', $this->title); ?></b></h3>
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
                <?=DocoHelpers::generateToolbar([
                    'search' => [
                        'attributes' => [
                            'data-parent'=>'.filter-revenue',
                            'class' => 'btn btn-info btn-labeled btn-xs cari-revenue'
                        ]
                    ],
                    'reset' => [
                        'attributes' => [
                            'data-parent'=>'.filter-revenue',
                            'class' => 'btn btn-info btn-labeled btn-xs reset-revenue'
                        ]
                    ],
                    'excel' => [
                        'attributes' => [
                            'data-parent' => '.filter-revenue',
                            'class' => 'btn btn-info btn-labeled btn-xs excel-revenue'
                        ]
                    ],
                    // Cetakan Backend Non BG Proses
                    // 'excel-detail' => [
                    //     'title' => 'Unduh Detail Excel',
                    //     'icon' => 'fa fa-file-excel-o',
                    //     'attributes' => [
                    //         'data-parent' => '.filter-revenue',
                    //         'class' => 'btn btn-info btn-labeled btn-xs excel-revenue',
                    //         'id'=>'detail-revenue',
                    //         'data-options' => 'link'
                    //     ]
                    // ],
                    'excel-bgprocess' => [
                        'type' => 'button',
                        'title' => 'Unduh Detail Excel',
                        'icon' => 'fa fa-file-excel-o',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id'=>'excel-bgprocess',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '50%',
                            'data-url' => '/kasir/laporan-revenue/show-popup?id=',
                            'data-conditions' => 'pendaftaran_id'
                        ]
                    ],

                ]);?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <form action="" id="search-form">
                        <div class="col-sm-12">
                            <div class="col-md-3">
                                <label>Pilih Periode : </label>
                                <?= Html::dropDownList('jenis_filter', 'date_range', [
                                    'date_range' => "Per Tanggal (Date Range)",
                                    'bulan_tahun' => "Per Bulan & Tahun",
                                ], [
                                    'class'=>'form-control select2',
                                    'id'=>'filter_jenis_periode',
                                    'prompt'=>Yii::t('fe', '--Pilih Periode--'),
                                ]); ?>
                            </div>
                            <div class="col-md-3 div-date-range">
                                <label>Pilih Range Tanggal : </label>
                                <div class="input-group"><input type="text" id="rangeDemoStart" value="<?= date("1-M-Y") ?>" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="<?= date("d-M-Y") ?>" readonly="true" class="form-control endDate"/><input type="text" style="display:none" class="targetDate"></div>
                            </div>
                            <div class="col-md-3 filter_bulan_tahun" style="display: none;">
                                <label>Pilih Periode Bulan: </label>
                                <?= Html::textInput('periode_bulan_tahun', date('Y').'-'.date('m'), [
                                    'class'=>'form-control',
                                    'id'=>'filter_bulan_tahun',
                                    'type' => 'month',
                                    'min' => '2015-01',
                                    'max' => date('Y').'-'.date('m'),
                                ]); ?>
                            </div>
                            <div class="col-md-3">
                                <label>Pilih Kategori : </label>
                                <?= Html::dropDownList('kategori', '', [], [
                                    'class'=>'form-control',
                                    'id'=>'filter_kategori',
                                    'prompt'=>Yii::t('fe', '----Pilih Semua----'),
                                ]); ?>
                            </div>
                            <div class="col-md-3">
                                <label>Pilih Unit : </label>
                                <?= DepDrop::widget([
                                    'name' => 'unit',
                                    'options' => [
                                        'disabled' => false,
                                        'class' => 'form-control select2 selectUnit'
                                    ],
                                    'pluginOptions' => [
                                       'depends'  => ['filter_kategori'],
                                       'placeholder' => '--Pilih Semua--',
                                       'url' =>'laporan-revenue/get-unit-revenue'
                                    ]
                                ]); ?>
                            </div>
                        </div>
                    </form>
                </div><br>
                <table id="example" class="table" style="width:100%;">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?=\Yii::t("fe", "DESCRIPTIONS");?></th>
                            <?php for ($j=1; $j <= 31; $j++) : ?>
                                <th id="<?= $j ?>"><?= Yii::t('fe', $j) ?></th>
                            <?php endfor; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="33"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs($this->render("index.js"));
$this->registerJs('
    var _listKategori = '.json_encode($kategori).';
', View::POS_END, 'index');

?>

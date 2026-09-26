<?php

/**
 * @author Randy Vianda Putra
 * @todo View Informasi Pasien Laboratorium
 * @copyright 09 Juli 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\widgets\Breadcrumbs;
use app\components\DocoTableHelper;
use yii\helpers\ArrayHelper;



$this->title = \Yii::t('fe', 'Informasi Pasien Laboratorium Intregasi LIS');
$this->params['breadcrumbs'][] = ['label' => 'Laboratorium', 'url' => ['/laboratorium']];
$this->params['breadcrumbs'][] = $title;
?>


<style>

    .my-legend .legend-title {
    text-align: left;
    margin-bottom: 8px;
    font-weight: bold;
    font-size: 90%;
    }
  .my-legend .legend-scale ul {
    margin: 0;
    padding: 0;
    float: left;
    list-style: none;
    }
  .my-legend .legend-scale ul li {
    display: block;
    float: left;
    width: 50px;
    margin-bottom: 6px;
    margin-right: 5px;
    text-align: center;
    font-size: 80%;
    list-style: none;
    }
  .my-legend ul.legend-labels li span {
    display: block;
    float: left;
    height: 15px;
    width: 50px;
    border: solid 0.2px;
    }
  .my-legend .legend-source {
    font-size: 70%;
    color: #999;
    clear: both;
    }
  .my-legend a {
    color: #777;
    }

    .square-sukses {
    height: 30px;
    width: 120px;
    background-color: #26A65B;
    color:#ffffff;
    padding: 5px 0 5px 10px;
    margin-right:20px;
    }
    .square-batal {
    height: 30px;
    width: 70px;
    background-color: #D24D57;
    color:#ffffff;
    padding: 5px 0 5px 10px;
    }

    .antrian{
    height: 30px;
    width: 90px;
    }

    .modal-dialog{
        width:  90%; 
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
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
                <?= DocoHelpers::generateToolbar([
                        'search'=>[
                            'attributes'=>[
                                'id'=>'btn-cari'
                            ]
                        ],
                        'reset'=> [
                            'attributes'=>[
                                'id' => 'btn-reset',
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        // 'speciment' => [
                        //     'title' => \Yii::t('fe', 'Speciment'),
                        //     'icon' => 'fa fa-info',
                        //     'attributes' => [
                        //         'id' => 'btn-speciment',
                        //         // 'data-target' => '/laboratorium/speciment/index?id=',
                        //         'data-options'=>'modal',
                        //         'data-target'=>'#modal_backdrop',
                        //         'data-url' => '/laboratorium/informasi-pasien-lab-wynacom/set-dokter?id=',
                        //         'disabled' => 'disabled',
                        //     ]
                        // ],
                        'hasil' => [
                            'title' => \Yii::t('fe', 'Hasil Pemeriksaan'),
                            'icon' => 'fa fa-eye',
                            'attributes' => [
                                'class' => 'hidden btn btn-info btn-labeled btn-xs btn-toolbar',
                                'id'=>'btn-hasil',
                                'data-target' => '/laboratorium/integrasi-lis-hasil/index?id=',
                                'disabled' => 'disabled',
                            ]
                        ],
                        'cetak' => [
                            'title' => \Yii::t('fe', 'Cetak Pemeriksaan'),
                            'icon' => 'fa fa-print',
                            'attributes' => [
                                'id' => 'btn-cetak',
                                'data-target' => '/laboratorium/integrasi-lis-hasil/cetak?id=',
                                'data-pages' => '_blank',
                                'disabled' => 'disabled',
                            ]
                        ],
                        'tagihan' => [
                            'title' => \Yii::t('fe', 'Rincian Tagihan'),
                            'icon' => 'fa fa-money',
                            'attributes' => [
                                'id'=>'cetak-tagihan',
                                'data-target' => '/laboratorium/informasi-pasien-lab-wynacom/print-rincian?id=',
                                'data-pages' => '_blank',
                                'disabled' => 'disabled',
                            ]
                        ],
                        'edit-pemeriksaan-wyn' => [
                            'title' => \Yii::t('fe', 'Edit Pemeriksaan'),
                            'icon' => 'fa fa-edit',
                            'attributes' => [
                                'data-options' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-width' => '75%',
                                'id' => 'edit-pemeriksaan',
                                // 'data-url' => '/laboratorium/informasi-pasien-lab/form-edit-pemeriksaan?id=',
                            ]
                        ],
                        // 'batal' => [
                        //     'title' => \Yii::t('fe', 'Batal'),
                        //     'icon' => 'fa fa-close',
                        //     'attributes' => [
                        //         'id' => 'btn-batal',
                        //         'class' => 'spa',
                        //         'data-options' => 'link',
                        //         'data-error-message'=>'',
                        //         'data-target' => '/laboratorium/informasi-pasien-lab-wynacom/form-batal?id=',
                        //     ]
                        // ],
                    ],'#table-pasien-lab');
                ?>
            </div>

            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class='my-legend'>
                            <div class='legend-title'>Keterangan</div>
                            <div class='legend-scale'>
                            <ul class='legend-labels'>
                                <li><span style='background:#D24D57;'></span>BATAL</li>
                                <li><span style='background:#FFFfff;'></span>BELUM DIPROSES</li>
                                <li><span style='background:#26A65B;'></span>DIPROSES</li>
                                <li><span style='background:#ff985a;'></span>SEBAGIAN</li>
                                <li><span style='background:#85C1E9;'></span>SELESAI</li>
                                <!-- <li><span style='background:#2574A9;'></span>PERIKSA</li>
                                <li><span style='background:#F5D76E;'></span>AMBIL SAMPEL</li> -->
                            </ul>
                        </div>
                    
                        <table 
                            class="table datatable-basic table-striped table-hover dataTable no-footer"
                            id="table-pasien-lab"
                            data-source="<?=Url::home();?>master/kamar/get-data"
                            data-filter=".form-filter"
                            data-test="true"
                            style="width: 100%;"
                        >
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="5%"></th>
                                    <th><?=Yii::t('fe', 'No Antrian'); ?></th>
                                    <th><?=Yii::t('fe', 'Tanggal Pendaftaran'); ?></th>
                                    <th><?=Yii::t('fe', 'No Pendaftaran'); ?></th>
                                    <th><?=Yii::t('fe', 'No Rekam Medis'); ?></th>
                                    <th><?=Yii::t('fe', 'Tanggal Lahir'); ?></th>
                                    <th><?=Yii::t('fe', 'Dokter'); ?></th>
                                    <th><?=Yii::t('fe', 'Cara Bayar'); ?></th>
                                    <th><?=Yii::t('fe', 'No Lab'); ?></th>
                                    <th><?=Yii::t('fe', 'Asal Rujukan'); ?></th>
                                    <th><?=Yii::t('fe', 'Status'); ?></th>
                                    <th><?=Yii::t('fe', 'History'); ?></th>
                                    <th></th>
                                    <th><?=Yii::t('fe', 'Instalasi'); ?></th>
                                    <th><?=Yii::t('fe', 'Ruangan'); ?></th>
                                </tr>    
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <audio id="playerAudio" preload="auto" tabindex="0" controls="" type="audio/mpeg" hidden='true'></audio>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    // save into localStorage
    localStorage.clear();
    // Global vars

    var isException = "'.$isException.'"
    const updateUrl = "/laboratorium/inf-pasien-rujukan-lab/update?id=";
    const approveUrl = "/laboratorium/inf-pasien-rujukan-lab/form-aproval?id=";
    const batalUrl = "/laboratorium/informasi-pasien-lab-wynacom/form-batal?id=";
    const cetakUrl = "/laboratorium/informasi-pasien-lab-wynacom/print-rincian?id=";

    var filterTanggalPendaftaran = \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" value="'. date('d-M-Y') .'"/><span class="input-group-addon" style="border-left:0; border-right:0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate" value="'. date('d-M-Y') .'" /><input type="text" style="display:none" class="targetDate"></div>\';

    var filterTanggalLahir = \'<div class="input-group"><input type="text" id="rangeLahirStart" class="form-control rangeLahirStart"/><span class="input-group-addon" style="border-left:0; border-right:0;">-</span><input type="text" id="rangeLahirFinish" class="form-control rangeLahirFinish"/><input type="text" style="display:none" class="targetDateLahir"></div>\';

    var dropdownStatus = \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('kode', '', $statusFilter, ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Pilih')]))) . '\';

    var asal_rujukan = \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('asal_rujukan', '', ArrayHelper::map($asalRujukan, 'asalrujukan_nama', 'asalrujukan_nama'), ['id' => 'asal_rujukan', 'class' => 'form-control select2 ', 'prompt' => \Yii::t('fe', 'Pilih')])
)) . '\';

    var instalasi = \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('instalasi', '', ArrayHelper::map($instalasi, 'instalasi_id', 'instalasi_nama'), ['id' => 'instalasi', 'class' => 'form-control select2 ', 'prompt' => \Yii::t('fe', 'Pilih'), ]))) . '\';
    
    $("#instalasi").select2InfinityScroll({
        url: "/kasir/inf-pasien-sudah-bayar/filters?type=instalasi",
        callbackData: (param) => {
            return {
                payload: {
                    ...param,
                }
            }
        }
    })
    var dropdownCaraBayar =  \'' . (preg_replace(
        "/[\n\t\r]/i",
        '',
        Html::dropDownList(
            'carabayar_nama',
            '',
            ArrayHelper::map($cara_bayar, 'carabayar_nama', 'carabayar_nama'),
            [
                'id' => 'filter_carabayar',
                'class' => 'form-control select2 dep-to-child',
                'prompt' => \Yii::t('fe', '--Cara bayar--'),
                // 'data-url' => Url::home() . 'kasir/end-point/get-penjamin',
                // 'data-depend_id' => 'filter_penjamin',
                // 'data-depend_prompt' => \Yii::t('fe', '--Penjamin--'),
                // 'data-storage' => 'penjamin',
                // 'data-key' => 'penjamin_nama',
            ]
        )
    )) . '\';
     


        var autocompleteDokter = \'<div class="input-group">' . (preg_replace(
    "/[\n\t\r]/i",
    '',
    Select2::widget([
        'name' => 'dokter_perujuk',
        'options' => ['placeholder' => \Yii::t('fe', 'Dokter'), 'autocomplete' => 'off'],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 3,
            'language' => [
                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
            ],
            'ajax' => [
                'url' => Url::home() . (Yii::$app->controller->module->id) . '/inf-pasien-rujukan-lab/get-dokter',
                'dataType' => 'json',
                'data' => new JsExpression('function(params) { return {q:params.term}; }')
            ],
        ],
    ])
)) . '</div>\'

', View::POS_END, 'b-index');
$this->registerJs($this->render('js/index.js'), View::POS_END);
?>

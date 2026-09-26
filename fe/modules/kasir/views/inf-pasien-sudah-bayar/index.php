<?php

use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
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
                        <h3 class="panel-title"><b><?= Yii::t('fe', 'Informasi Pasien Sudah Bayar'); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?php
                    $defaultBtn = [
                    'search',
                    'reset',
                    // 'lihat' => [
                    //     'type' => 'link',
                    //     'title' => \Yii::t('fe', 'Lihat'),
                    //     'icon' => 'fa fa-eye',
                    //     'method' => '#',
                    //     'attributes' => [
                    //         'class' => 'data-lihat',
                    //         'data-target' => Url::home().('kasir/inf-pasien-sudah-bayar/preview?id='),
                    //         'data-conditions'=>'pendaftaran_id,tipe_pasien'
                    //     ]
                    // ],
                    'batal-bayar' =>[
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Batal'),
                        'icon' => 'fa fa-close',
                        'method' => '#',
                        'attributes' => [
                            'class'=>'data-batal-bayar',
                            'data-options'=>'modal',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '50%',
                            'data-additional' => 'data-rm',
                            'data-url' => '/kasir/inf-pasien-sudah-bayar/cancel?id=',
                            'data-conditions'=>'pendaftaran_id,pembayaran_id,penjualanresep_id'
                        ]
                    ],
                    'cetak-kwitansi'=>[
                        'type'=>'button',
                        'title' => \Yii::t('fe', 'Cetak Kwitansi'),
                        'icon' => 'fa fa-print',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id' => 'btn-cetak-kwitansi',
                            'data-options'=>'modal',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '50%',
                            'data-url' => '/kasir/inf-pasien-sudah-bayar/cetak-kwitansi?id=',
                            'data-conditions'=>'pembayaran_id,groupcarabayar_id,penjamin_id'
                        ]
                    ],
                    'invoice' => $btnInvoice,
                    'detailinvoice' => $btnDetailInvoice,
                    'print-sip' => [
                        'type' => 'button',
                        'title' => 'Print Surat Izin Pulang',
                        'icon' => 'fa fa-print',
                        'method' => '#',
                        'attributes' => [
                            'id' => 'print-sip',
                            'data-options' => 'link',
                            'disabled' =>  true
                        ]
                    ],
                    // 'excel',
                    'excel-bgprocess' => [
                        'type' => 'button',
                        'title' => 'Unduh excel',
                        'icon' => 'fa fa-file-excel-o',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id'=>'excel-bgprocess',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '50%',
                            'data-url' => '/kasir/inf-pasien-sudah-bayar/show-popup-excel?id=',
                            'data-conditions' => 'pendaftaran_id'
                        ]
                    ],
                ];

                // $defaultBtn['detail-invoice-inacbgs'] = [
                //     'title' => Yii::t('fe', "Claim INACBGS"),
                //     'icon' => 'fa fa-print',
                //     'method' => '#',
                //     'attributes' => [
                //         'id'=>'detail-invoice-inacbgs',
                //         'data-options' => 'link',
                //         'disabled' => true
                //     ]
                // ];

                ?>
                <?=DocoHelpers::generateToolbar($defaultBtn,'#table-informasi');?>
            </div>

            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <table id="table-informasi" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Tanggal Pembayaran");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Masuk");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Stop Akomodasi");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Pulang");?></th>
                            <th><?=\Yii::t("fe", "No Pembayaran");?></th>
                            <th><?=\Yii::t("fe", "Instalasi - Ruangan Akhir");?></th>
                            <th><?=\Yii::t("fe", "Instalasi Akhir");?></th>
                            <th><?=\Yii::t("fe", "Ruangan Akhir");?></th>
                            <th><?=\Yii::t("fe", "No Pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "No SEP");?></th>
                            <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th><?=\Yii::t("fe", "No Rekam Medik");?></th>
                            <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th><?=\Yii::t("fe", "Cara Bayar - Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Cara Bayar");?></th>
                            <th><?=\Yii::t("fe", "Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Masuk");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Pulang");?></th>
                            <th><?=\Yii::t("fe", "Jumlah Tagihan");?></th>
                            <th><?=\Yii::t("fe", "Diskon");?></th>
                            <th><?=\Yii::t("fe", "Jumlah Dibayar Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Jumlah Dibayar Pasien");?></th>
                            <th><?=\Yii::t("fe", "Pegawai Kasir");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Masuk");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Pulang");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var table;
var dataCollect;
var PASIEN_ALKES = "'.DocoConstants::PASIEN_ALKES.'";
var INSTALASI_RANAP = '.DocoConstants::INSTALASI_ID_RI.';

var _filterTanggal = \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" readonly="true" class="form-control endDate"/><input type="text" style="display:none" class="targetDate" col-index=2></div>\'
var _filterTanggalMasuk = \'<div class="input-group"><input type="text" value="'.date('d-M-Y', strtotime('-1 months')).'" id="rangeDemoStartIn" class="form-control startDateIn"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" value="'.date('d-M-Y', strtotime('+1 months')).'"  id="rangeDemoFinishIn" class="form-control endDateIn" readonly="readonly"/><input type="text" style="display:none" class="targetDateIn"></div>\'
var _filterTanggalPulang = \'<div class="input-group"><input type="text" value="'.date('d-M-Y', strtotime('-1 months')).'" id="rangeDemoStartOut" class="form-control startDateOut"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" value="'.date('d-M-Y', strtotime('+1 months')).'"  id="rangeDemoFinishOut" class="form-control endDateOut" readonly="readonly"/><input type="text" style="display:none" class="targetDateOut"></div>\' 
var _filterCaraBayar = 
    \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
    Html::dropDownList('carabayar_nama', '',
        [],
        [
            'id' => 'filter_carabayar',
            'class' => 'form-control select2',
            'prompt' => \Yii::t('fe', '--Pilih Cara bayar--'),
        ]
    )
    )).'</div>\'

var _filterPenjamin = 
    \''.(preg_replace("/[\n\t\r]/i", '',
    Html::dropDownList('penjamin_nama[]', '',
        [],
        [
            'id' => 'filter_penjamin',
            'class' => 'form-control',
            'multiple' => 'multiple',
        ]
    )
    )).'\';

var _filterInstalasi = 
    \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
        Html::dropDownList('instalasi_id1', '',
            [],
            [
            'id' => 'filter_instalasi',
            'class' => 'form-control select2',
            'prompt' => \Yii::t('fe', '--Pilih Instalasi akhir--'),
            ]
        )
    )).'</div>\'

var _filterCheckNominal = \'<div class="input-group"><input type="checkbox" id="check_nominal" value="1"/></div>\'

',View::POS_END,'b-index');
$this->registerJs($this->render('js/index.js'), View::POS_END);

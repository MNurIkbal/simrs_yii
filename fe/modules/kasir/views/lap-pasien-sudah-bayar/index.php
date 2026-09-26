<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use app\components\DocoHelpers;

$this->title = \Yii::t('fe', 'Laporan Pasien Sudah Bayar');
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
                        <h3 class="panel-title"><b><?= Yii::t('fe', 'Laporan Pasien Sudah Bayar'); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>

            <div class="panel-toolbar clearfix">
                <div class="btn-group pull-left">
                    <?=DocoHelpers::generateToolbar(['search','reset',
                    'excel-bgprocess' => [
                        'type' => 'button',
                        'title' => 'Unduh excel',
                        'icon' => 'fa fa-file-excel-o',
                        'method' => 'not-exist',
                        'attributes' => 
                            [
                            'id'=>'excel-bgprocess',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '50%',
                            'data-url' => '/kasir/lap-pasien-sudah-bayar/show-popup?id=',
                            ]
                        ],
                    'pdf-bgprocess' => [
                       'type' => 'button',
                       'title' => 'Unduh PDF',
                       'icon' => 'fa fa-file-excel-o',
                       'method' => 'not-exist',
                       'attributes' => [
                           'id'=>'pdf-bgprocess',
                           'data-options' => 'excel-serconn',
                           'data-target' => '#modal_backdrop',
                           'data-width' => '50%',
                           'data-url' => '/kasir/lap-pasien-sudah-bayar/show-popup-pdf?',
                       ]
                ],]);?>
                </div>
            </div>

            <div class="panel-body">
                <div class="advanced-filter">
                </div>

                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Tanggal pembayaran");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Masuk - Keluar");?></th>
                            <th><?=\Yii::t("fe", "Instalasi - Ruangan akhir");?></th>
                            <th><?=\Yii::t("fe", "No pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "Nama pasien");?></th>
                            <th><?=\Yii::t("fe", "Nomor Rekam Medik");?></th>
                            <th><?=\Yii::t("fe", "Cara Bayar");?></th>
                            <th><?=\Yii::t("fe", "Cara Bayar");?></th>
                            <th><?=\Yii::t("fe", "Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Masuk");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Pulang");?></th>
                            <th><?=\Yii::t("fe", "Jumlah Tagihan");?></th>
                            <th><?=\Yii::t("fe", "Diskon");?></th>
                            <th><?=\Yii::t("fe", "Jumlah Dibayar Penjamin");?></th>
                            <th><?=\Yii::t("fe", "Jumlah Dibayar Pasien");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="11"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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
    var _dataPenjamin = \''.json_encode(ArrayHelper::map($resMaster['penjamin'], 'penjamin_nama', 'penjamin_nama') ).'\';
    var _filterTanggal = \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" value="'.date('d-M-Y').'" readonly="true" class="form-control endDate"/><input type="text" style="display:none" class="targetDate" col-index=1></div>\'
    var _filterTanggalMasuk = \'<div class="input-group"><input type="text" value="'.date('d-M-Y', strtotime('-1 months')).'" id="rangeDemoStartIn" class="form-control startDateIn"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" value="'.date('d-M-Y', strtotime('+1 months')).'"  id="rangeDemoFinishIn" class="form-control endDateIn" readonly="readonly"/><input type="text" style="display:none" class="targetDateIn"></div>\'
    var _filterTanggalPulang = \'<div class="input-group"><input type="text" value="'.date('d-M-Y', strtotime('-1 months')).'" id="rangeDemoStartOut" class="form-control startDateOut"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" value="'.date('d-M-Y', strtotime('+1 months')).'"  id="rangeDemoFinishOut" class="form-control endDateOut" readonly="readonly"/><input type="text" style="display:none" class="targetDateOut"></div>\' 
    var _filterCaraBayar = \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
        Html::dropDownList('carabayar_id', '',
            [],
            [
                'id' => 'filter_carabayar',
                'class' => 'form-control select2',
                'prompt' => \Yii::t('fe', '--Pilih Cara bayar--'),
            ]
        )
    )).'</div>\'

    var _filterPenjamin = \''.(preg_replace("/[\n\t\r]/i", '',
        Html::dropDownList('penjamin_id[]', '',
            [],
            [
                'id' => 'filter_penjamin',
                'class' => 'form-control select2',
                'multiple' => 'multiple',
            ]
        )
    )).'\'
', View::POS_END, 'b-index');
$this->registerJs($this->render('index.js'), View::POS_END);
?>

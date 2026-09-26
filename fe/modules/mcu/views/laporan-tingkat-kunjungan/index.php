<?php
/**
 * @author     (rizal <rizal.faidin@sirs.co.id>)
 * @description 
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;

$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")), 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                  <div class="column-1">
                    <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                  </div>
                  <div class="column-2">
                    <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                    'excel' => [
                        // 'attributes' => [
                        //     'data-target'=>'/master/tarif-tindakan/export-excel?',
                        // ],
                    ],
                ], '#tbl-laporan-tingkat-kunjungan-mcu');?>
            </div>

            <div class="panel-body">
                <div class="advanced-filter"></div>
                <table id="tbl-laporan-tingkat-kunjungan-mcu" class="table table-striped table-condensed table-hover" style="width:100%;">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th> 
                            <th><?=\Yii::t("fe", "Tanggal Registrasi");?></th> 
                            <th><?=\Yii::t("fe", "No Registrasi");?></th> 
                            <th><?=\Yii::t("fe", "No Rekam Medik");?></th> 
                            <th><?=\Yii::t("fe", "Nama Pasien");?></th> 
                            <th><?=\Yii::t("fe", "Nama Paket");?></th> 
                            <th><?=\Yii::t("fe", "Tarif Paket");?></th> 
                            <th><?=\Yii::t("fe", "Payer");?></th> 
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
$this->registerJsFile(
    '/js/app/dcms/date-range-filter.js',
    [
        'depends' => [
            'app\assets\AppAsset',
        ]
    ]
);

$this->registerJs('
    var rangeRegDate = \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" value='.date('d-m-Y').'  data-value=' . date('d-m-Y') . ' /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate" value='.date('d-m-Y').' data-value='.date('d-m-Y').' /><input type="text" style="display:none" class="targetDate" id="targetDate" col-index=2 readonly="true"></div>\';
    var dropDownPackageType =  \'' . (preg_replace(
        "/[\n\t\r]/i",
        '',
        Html::dropDownList(
            'tipe_paket',
            '',
            ArrayHelper::map($tipePaket, 'tipepaket_kode', 'tipepaket_nama'),
            [
                'id' => 'filter_tipepaket',
                'class' => 'form-control select2',
                'prompt' => \Yii::t('fe', '-- Semua Tipe Paket --')
            ]
        )
    )) . '\';

    var dropDownPayer =  \'' . (preg_replace(
        "/[\n\t\r]/i",
        '',
        Html::dropDownList(
            'penjamin',
            '',
            ArrayHelper::map($penjamin, 'penjamin_id', 'penjamin_nama'),
            [
                'id' => 'filter_penjamin',
                'class' => 'form-control select2',
                'prompt' => \Yii::t('fe', '-- Semua Payer --')
            ]
        )
    )) . '\';
    ' . $this->render('index.js'), View::POS_END, 'b-index');
?>

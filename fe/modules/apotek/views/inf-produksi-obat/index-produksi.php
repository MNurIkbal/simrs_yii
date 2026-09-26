<?php

/**
 * @Author: Asri Nurul M
 * @Date:   2024-06-20
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Informasi Produksi Obat', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<style>
    .tooltip {
        margin-top: 100px;
        z-index: 99999 !important;

    }
    .tooltip-inner {
        max-width: 500px!important;
        text-align: left!important;
        display: grid;
        flex-wrap: wrap;
        word-break: break-all;
        z-index: 99999 !important;
        position: relative !important;
        text-align: justify;
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
                        <h3 class="panel-title"><b><?= $title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <div class="col-md-12">
                    <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                        'lihat' => [
                            'type' => 'link',
                            'title' => \Yii::t('fe', 'Lihat'),
                            'icon' => 'fa fa-eye',
                            'method' => '#',
                            'attributes' => [
                                'id' => 'btn-lihat',
                                'data-target' => '/apotek/inf-produksi-obat/detail-produksi?id=',
                                'disabled' => true,
                            ]
                        ],
                        'define-material' => [
                            'type' => 'button',
                            'title' => 'Define Material',
                            'icon' => 'fa fa-cog',
                            'method' => 'not-exist',
                            'attributes' => [
                                'id' => 'btn-define-material',
                                'disabled' => true,
                                'data-target' => '/apotek/inf-produksi-obat/define-material?',
                                'data-options' => 'click',
                            ]
                          ],
                          'excel' => [
                              'title' => Yii::t('fe', 'Unduh Excel'),
                              'attributes'=>[
                                  'data-target'=> Url::home().'apotek/inf-produksi-obat/export-excel-produksi?'
                              ]
                          ],

                    ]);?>
                </div>
            </div>
            <div class="panel-body">
                <div class="advanced-filter">

                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "No.Pemesanan");?></th>
                            <th><?=\Yii::t("fe", "Pemesan");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                            <th><?=\Yii::t("fe", "No.Produksi");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Produksi");?></th>
                            <th><?=\Yii::t("fe", "Approve");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Pemesanan");?></th>
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
        var status_dropdown = \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
            Html::dropDownList('status_id', '', $_status,
                [
                    'class' => 'form-control select2',
                    'prompt' => \Yii::t('fe', 'ALL')
                ]
            ))).'</div>\';
    ', View::POS_END,'js-kuning');
    $this->registerJs($this->render('js/index-produksi.js'));
 ?>
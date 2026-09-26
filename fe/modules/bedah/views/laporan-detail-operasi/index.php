<?php

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

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Bedah Sentral', 'url' => ['/bedah']];
$this->params['breadcrumbs'][] = $title;

?>
<style>
    /* tr.dtrg-group.dtrg-end.dtrg-level-1 {
        background-color: #FCF3CF !important;
    } */
    tr.dtrg-group.dtrg-end.dtrg-level-0 {
        background-color: #D5F5E3 !important;
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
                        'search' => [
                            'attributes' => [
                                'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-search--datatable',
                                'data-table-id' => 'example',
                                'data-options' => 'click',
                            ]
                        ],
                        'reset' => [
                            'attributes' => [
                                'class' => 'btn btn-info btn-labeled btn-xs btn-toolbar btn-reset',
                                'data-table-id' => 'example',
                                'data-options' => 'click',
                            ]
                        ],
                        'excel-bgprocess' => [
                            'type' => 'button',
                            'title' => 'Unduh Excel Detail Operasi',
                            'icon' => 'fa fa-file-excel-o',
                            'method' => 'not-exist',
                            'attributes' => [
                                'id'=>'excel-bgprocess',
                                'data-options' => 'excel-serconn',
                                'data-target' => '#modal_backdrop',
                                'data-width' => '50%',
                                'data-url' => '/bedah/laporan-detail-operasi/show-popup?type=1&',
                            ]
                        ],
                        'excel-bgprocess-2' => [
                            'type' => 'button',
                            'title' => 'Unduh Excel Rekapitulasi Operasi',
                            'icon' => 'fa fa-file-excel-o',
                            'method' => 'not-exist',
                            'attributes' => [
                                'id'=>'excel-bgprocess2',
                                'data-options' => 'excel-serconn',
                                'data-target' => '#modal_backdrop',
                                'data-width' => '50%',
                                'data-url' => '/bedah/laporan-detail-operasi/show-popup?type=2&',
                            ]
                        ],
                    ]);
                ?>
            </div>

            <div class="panel-body">
                <div class="table-wrapper table-scroll-x"><br>
                    <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="example" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th><?=Yii::t('fe', 'Golongan Operasi'); ?></th>
                                <th><?=Yii::t('fe', 'Tindakan Operasi'); ?></th>
                                <th><?=Yii::t('fe', 'Kegiatan Operasi'); ?></th>
                                <th><?=Yii::t('fe', 'Dokter Operator'); ?></th>
                                <th><?=Yii::t('fe', 'Qty Tindakan'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" style="background-color: #AED6F1;"><b>Grand Total</b></th>
                                <th style="background-color: #AED6F1;text-align:right;"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs($this->render('js/index.js'), View::POS_END); ?>


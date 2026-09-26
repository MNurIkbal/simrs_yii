<?php

/**
 * @author: [Ardi Pratama][ardi.pratama@sirs.co.id]
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;

$this->title = Yii::t('fe', 'Informasi Antrean');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['/pendaftaran/dashboard']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style media="screen">
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

    .row__not-completed-resep {
        background: #5F9EA0 !important;
        color : #fff !important;
    }

    .row__not-completed-non-resep {
        background: #D2691E !important;
        color : #fff !important;
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
                    <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
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
                    'search',
                    'reset' => [
                        'attributes' => [
                            'id' => 'reset-table-antreanbpjs',
                            'class' =>'btn btn-info btn-labeled btn-xs btn-reset'
                        ]
                    ],
                    'resendtask' => [
                        'title' => \Yii::t('fe', 'Resend Task'),
                        'icon' => 'fa fa-send',
                        'attributes' => [
                            'action' => '/pendaftaran/informasi-antrean-bpjs/resend-task?primary=',
                            'class' => 'btn-aksi',
                            'data-options' => 'click',
                            'id' => 'btn-resend-task',
                        ]
                    ],
                    'sync' => [
                        'title' => \Yii::t('fe', 'Sync'),
                        'icon' => 'fa fa-refresh',
                        'attributes' => [
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => '/pendaftaran/informasi-antrean-bpjs/modal-sync-by-date',
                        ],
                    ],
                ], '#table-antreanbpjs'); ?>
            </div>

            <div class="panel-body">
                <div class="advanced-filter"></div>
                <div class="row">
                  <div class="col-md-5">
                      <div class='my-legend'>
                          <div class='legend-title'>Keterangan</div>
                          <div class='legend-scale'>
                              <ul class='legend-labels'>
                                  <li><span style='background:#5F9EA0;'></span>Pasien Belum Lengkap Dengan Resep</li>
                                  <li><span style='background:#D2691E;'></span>Pasien Belum Lengkap Tanpa Resep</li>
                              </ul>
                          </div>
                      </div>
                  </div>
                </div>
                <div class="panel-body">
                    <table id="table-antreanbpjs" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="5"><input type="checkbox" class="pickMe select-checkbox" name="pickMe" value="all"></th>
                                <th width="1"><?= \Yii::t("fe", "No"); ?></th>
                                <th><?=Yii::t('fe', 'Tanggal Pendaftaran')?></th>
                                <th><?=Yii::t('fe', 'No Pendaftaran')?></th>
                                <th><?=Yii::t('fe', 'Kode Booking')?></th>
                                <th><?=Yii::t('fe', 'Nama Pasien')?></th>
                                <th><?=Yii::t('fe', 'Jenis Pasien')?></th>
                                <th><?=Yii::t('fe', 'Nomor BPJS')?></th>
                                <th><?=Yii::t('fe', 'Nomor Referensi')?></th>
                                <th><?=Yii::t('fe', 'No Antrian')?></th>
                                <th><?=Yii::t('fe', 'Task Id 1')?></th>
                                <th><?=Yii::t('fe', 'Task Id 2')?></th>
                                <th><?=Yii::t('fe', 'Task Id 3')?></th>
                                <th><?=Yii::t('fe', 'Task Id 4')?></th>
                                <th><?=Yii::t('fe', 'Task Id 5')?></th>
                                <th><?=Yii::t('fe', 'Task Id 6')?></th>
                                <th><?=Yii::t('fe', 'Task Id 7')?></th>
                                <th><?=Yii::t('fe', 'Task Id 99')?></th>
                                <th><?=Yii::t('fe', 'Create Antrean')?></th>
                                <th><?=Yii::t('fe', 'Status Antrean')?></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs($this->render('index.js'));
?>

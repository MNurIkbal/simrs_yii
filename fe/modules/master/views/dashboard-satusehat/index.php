<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use yii\web\View;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("instalasi_name"), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .table-condensed > tbody > tr > td {
        padding: 8px 10px;
    }
    .btnfoo {
        margin-bottom: 5px;
    }
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
    background-color: #ffffff;
    padding: 5px 0 5px 10px;
    margin-right:20px;
    }
.square-dp {
    height: 30px;
    width: 120px;
    background-color: #DAF7A6;
    padding: 5px 0 5px 10px;
    margin-right:20px;
    }
.square-mp {
    height: 30px;
    width: 120px;
    background-color: #D1F2EB;
    padding: 5px 0 5px 10px;
    margin-right:20px;
    }
.square-batal {
    height: 30px;
    width: 70px;
    background-color: #D24D57;
    padding: 5px 0 5px 10px;
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
                      <h3 class="panel-title"><b><?= $this->title ?></b></h3>
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
                        'search',
                        'reset',
                        'resend'=> [
                            'title' => Yii::t('fe', 'Resend'),
                            'icon' => 'fa fa-paper-plane',
                            'attributes' => [
                                'id' => 'resend-satusehat',
                                'data-options' => 'click',
                                'disabled' => true
                            ]
                        ],
                        'sinkronisasi'=> [
                            'title' => Yii::t('fe', 'Sinkronisasi'),
                            'icon' => 'fa fa-refresh',
                            'attributes' => [
                                'id' => 'sinkronisasi-satusehat',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-width' => '50%',
                                'action' => '/master/dashboard-satusehat/sinkronisasi',
                            ]
                        ],
                    ], '#table-patient');?>    
            </div>
            <div class="panel-body">
                <div class="advanced-filter"></div>
                <div class="col-md-6">
                    <div class='my-legend'>
                        <div class='legend-title'>Keterangan</div>
                        <div class='legend-scale'>
                            <ul class='legend-labels'>
                                <li><span class="square-sukses"></span>Selesai</li>
                                <li><span class="square-mp"></span>Diproses</li>
                                <li><span class="square-batal"></span>Gagal</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <table id="table-patient" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th class="text-center"><?= Yii::t('fe', 'Resend All'); ?><br>
                                <?= Html::checkbox('select_all', 0, ['class' => 'resend-all']); ?>
                            </th>
                            <th><?=Yii::t("fe", "No.");?></th>
                            <th><?=Yii::t("fe", "Detail");?></th>
                            <th><?=Yii::t("fe", "Status");?></th>
                            <th><?=Yii::t('fe', 'Sync ID API')?></th>
                            <th><?=Yii::t('fe', 'No. Pendaftaran')?></th>
                            <th><?=Yii::t('fe', 'Tanggal Sync')?></th>
                            <th><?=Yii::t('fe', 'Tanggal Resend')?></th>
                            <th><?=Yii::t('fe', 'Satu Sehat ID')?></th>
                            <th><?=Yii::t('fe', 'Satu Sehat Type')?></th>
                            <th><?=Yii::t('fe', 'Satu Sehat State')?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="8">Data tidak ditemukan.</td>
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
        var _filterType = \''.(preg_replace("/[\n\t\r]/i", '',
            Html::dropDownList('list_type[]', '',
                $list_satu_sehat_type,
                [
                    'id' => 'filter_type',
                    'class' => 'form-control select2 list_type',
                    'multiple' => 'multiple',
                ]
            )
        )).'\'

        var _filterState = \''.(preg_replace("/[\n\t\r]/i", '',
            Html::dropDownList('list_state[]', '',
                $list_satu_sehat_state,
                [
                    'id' => 'filter_state',
                    'class' => 'form-control select2 list_state',
                    'multiple' => 'multiple',
                ]
            )
        )).'\'

        var _filterStatus = \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
            Html::dropDownList('status', '', 
                $list_satu_sehat_status,
                [
                    'id' => 'status_integrasi',
                    'class' => 'form-control select2',
                    'prompt' => Yii::t('fe', '-- Pilih --'),
                ]
            ))).'</div>\';
        
    ', View::POS_END,'js-kuning');
    $this->registerJs($this->render('js/index.js'));
 ?>
<?php

use app\components\DocoConstants;
use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('fe', 'Monitoring Dokumen Rekam Medis');
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medis', 'url' => ['/rm/monitoring-dokumen']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .panel-data p {
        max-width: 200px;
        overflow-wrap: break-word;
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
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-body">
                <div class="row">
                    <form action="#" id="filter-monitoring">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Tanggal</label>
                                <div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate" /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">Nama / No. RM / No. Pendaftaran</label>
                                <input type="text" id="nama-rm-pendaftaran" class="form-control" placeholder="Nama / No. RM / No. Pendaftaran" />
                            </div>
                        </div>
                        <div class="col-md-1">
                            <br>
                            <button class="btn btn-success btn-xs btn-labeled" style="margin-top: 5px">
                                <b>
                                    <i class="fa fa-search"></i>
                                </b>
                                Cari
                            </button>
                        </div>
                    </form>
                    <div class="col-md-12">
                        
                    </div>
                </div>
                <div class="scrolled-div">
                    <div class="row">
                        <?php 
                            foreach($lookupList as $key => $lookupItem) 
                            {
                                ?>
                                <div class="col-xs-3">
                                    <div class="panel panel-white monitoring-panel" id="monitoring-panel-<?=$lookupItem['kode']?>" data-status_rekam_medik="<?=$lookupItem['lookup_id']?>" <?= isset($lookupItem['instalasi_id']) ? 'data-instalasi_id='.$lookupItem['instalasi_id'] : ''?>>
                                        <div class="panel-heading">
                                            <div class="div-title">
                                                <h5 class="panel-title"> <b> <?=$lookupItem['title']?></b></h5>
                                            </div>
                                            <div class="div-action-title" style="display: none;">
                                                <h5 class="panel-title"><b>
                                                    <a href="#" class="fa fa-list" onClick="checklistAll(this)"></a>
                                                    &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                                    <a href="#" class="fa fa-file-text" title="request" onClick="sendToAll(this, 'request')"></a>&nbsp&nbsp
                                                    <a href="#" class="fa fa-cube" title="delivery" data-toggle="tooltip" data-placement="bottom" onClick="sendToAll(this, 'delivery')"></a>&nbsp&nbsp
                                                    <a href="#" class="fa fa-warning" title="issues" data-toggle="tooltip" data-placement="bottom" onClick="sendToAll(this, 'issues')"></a>&nbsp&nbsp
                                                    <?php if($isRM) : ?><a href="#" class="fa fa-envelope" title="receive" data-toggle="tooltip" data-placement="bottom" onClick="sendToAll(this, 'receive')"></a>&nbsp&nbsp<?php endif; ?>
                                                    <?php if($isRM) : ?><a href="#" class="fa fa-rotate-left" title="return" data-toggle="tooltip" data-placement="bottom" onClick="sendReturnAll(this, event)"></a>&nbsp&nbsp<?php endif; ?>
                                                    <?php if((!$isRM && ($lookupItem['lookup_id'] == DocoConstants::MONITORING_RM_REQUEST || $lookupItem['lookup_id'] == DocoConstants::MONITORING_RM_DELIVERY)) || ($isRM && !($lookupItem['lookup_id'] == DocoConstants::MONITORING_RM_REQUEST || $lookupItem['lookup_id'] == DocoConstants::MONITORING_RM_DELIVERY))) : ?><a href="#" class="fa fa-calendar" title="remind" data-toggle="tooltip" data-placement="bottom" onClick="sendToAll(this, 'remind')"></a><?php endif; ?>
                                                </b></h5>
                                            </div>
                                        </div>
                                        <div class="panel-body bg-gray m-h-500 panel-data" style="height: 500px; overflow-y: scroll">
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        ?>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="rakModal" tabindex="-1" role="dialog">
                  <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">Return</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <div class="form-group">
                            <label>Rak</label>
                            <div>
                                <?php echo Html::dropDownList('lokasirak', null, $rakList,
                                    [
                                        'id' => 'dropdown_rak',
                                        'class' => 'form-control',
                                        'style' => 'padding : 9px 12px !important;',
                                        'prompt' => \Yii::t('fe', '-- Pilih --'),
                                        'data-depend_prompt' => \Yii::t('fe', '-- Pilih --'),
                                    ]); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Sub Rak</label>
                            <div>
                                <?php echo Html::dropDownList('lokasirak', null, [],
                                    [
                                        'id' => 'dropdown_subrak',
                                        'class' => 'form-control',
                                        'style' => 'padding : 9px 12px !important;',
                                        'prompt' => \Yii::t('fe', '-- Pilih --'),
                                    ]); ?>
                            </div>
                        </div>                        
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary btn-simpan">Simpan</button>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var subrak = '. json_encode($subrakList).';
    var isRM = ' . ($isRM ? 'true' : 'false') . '
    var remindId = ' . $remindId . '
    var CONST_MONITORING_RM_REQUEST = "'.DocoConstants::MONITORING_RM_REQUEST.'"
    var CONST_MONITORING_RM_DELIVERY = "'.DocoConstants::MONITORING_RM_DELIVERY.'"
', View::POS_END);
$this->registerJs($this->render('js/index.js'), View::POS_END);
?>
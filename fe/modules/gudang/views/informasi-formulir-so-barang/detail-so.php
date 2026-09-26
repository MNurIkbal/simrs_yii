<?php

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("instalasi_name"), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $header, 'url' => [""]];
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
                <div class="btn-group pull-left">
                    <?=DocoHelpers::generateToolbar($btn_toolbar, '#detailSo');?>
                </div>
            </div>
            <div class="panel-body">
                <br>
                <div class="row" style="font-size: 16px">
                    <div class="col-md-4">
                        <div class="row">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Tanggal Stok Opname") ?></b></label>
                            <div class="col-sm-5">
                                <p style="font-size: 13px"><b>:</b>&nbsp; <?=isset($data['tglstokopname']) ? date('d-M-Y H:i:s', strtotime($data['tglstokopname'])) : '-' ?> </p>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Nomor Stok Opname") ?></b></label>
                            <div class="col-sm-5">
                                <p style="font-size: 13px"><b>:</b>&nbsp; <?= ArrayHelper::getValue($data, 'nostokopname', '-') ?> </p>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Jenis Stok Opname") ?></b></label>
                            <div class="col-sm-5">
                                <p style="font-size: 13px"><b>:</b>&nbsp; <?= ArrayHelper::getValue($data, 'jenis_stokopname', '-') ?> </p>
                            </div>
                        </div>
                        <?php if($isWithVerified):?>
                            <div class="row">
                                <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Status Verifikasi") ?></b></label>
                                <div class="col-sm-5">
                                    <p style="font-size: 13px"><b>:</b>&nbsp; <?=isset($data['is_verifikasi']) ? ($data['is_verifikasi'] == TRUE) ? \Yii::t('fe', 'Sudah Verifikasi') : \Yii::t('fe', 'Belum Verifikasi') : '-' ?> </p>
                                </div>
                            </div>
                        <?php endif;?>
                    </div>
                    <div class="col-md-4 col-md-offset-4">
                        <div class="row">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Tanggal Formulir Stok Opname") ?></b></label>
                            <div class="col-sm-5">
                                <p style="font-size: 13px"><b>:</b>&nbsp; <?=isset($data['tglformulir']) ? date('d-M-Y H:i:s', strtotime($data['tglformulir'])) : '-' ?> </p>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Nomor Formulir Stok Opname") ?></b></label>
                            <div class="col-sm-5">
                                <p style="font-size: 13px"><b>:</b>&nbsp; <?= ArrayHelper::getValue($data, 'noformulir', '-') ?> </p>
                            </div>
                        </div>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-md-12">
                        <table id="detailSo" class="table table-striped table-condensed table-hover" style="width:100%">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="1">No</th>
                                    <th><?=\Yii::t("fe", "Nama Barang");?></th>
                                    <th><?=\Yii::t("fe", "Stok Saat Stok Opname");?></th>
                                    <th><?=\Yii::t("fe", "Stok Fisik");?></th>
                                    <th><?=\Yii::t("fe", "Selisih Stok Opname");?></th>
                                    <th><?=\Yii::t("fe", "Stok Saat ini");?></th>
                                    <th><?=\Yii::t("fe", "Selisih Saat ini");?></th>
                                    <th><?=\Yii::t("fe", "Harga Netto");?></th>
                                    <th><?=\Yii::t("fe", "Total Harga Netto");?></th>
                                    <th><?=\Yii::t("fe", "Total Selisih");?></th>
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
                <div class="row" style="font-size: 16px">
                    <div class="col-md-5">
                        <div class="row">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Total Harga Fisik") ?></b></label>
                            <div class="col-sm-5">
                                <p style="font-size: 13px"><b>:</b>&nbsp; <?= DocoHelpers::rupiahDisplay($total_harga_fisik) ?> </p>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Total Harga Sistem") ?></b></label>
                            <div class="col-sm-5">
                                <p style="font-size: 13px"><b>:</b>&nbsp; <?= DocoHelpers::rupiahDisplay($total_harga_sistem) ?> </p>
                            </div>
                        </div>
                        <div class="row">
                            <label class="text-left control-label col-sm-5" style="padding: 0 10px"><b><?= Yii::t("fe", "Selisih") ?></b></label>
                            <div class="col-sm-5">
                                <p style="font-size: 13px"><b>:</b>&nbsp; <?= DocoHelpers::rupiahDisplay($total_selisih) ?> </p>
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
    var table;
    var stokopnamebarang_id = "'.$stokopnamebarang_id.'";
    var is_verifikasi = "'.$is_verifikasi.'";
', View::POS_END,'detail-stok-opname');
$this->registerJs($this->render('js/detail-so.js'), View::POS_END);
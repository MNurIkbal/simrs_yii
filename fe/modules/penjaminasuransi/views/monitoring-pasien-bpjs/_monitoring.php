<?php

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use kartik\widgets\DepDrop;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;

// Some variables
$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 
'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
echo Html::hiddenInput('pendaftaran_id', $pendaftaran_id, ['class' => 'pendaftaran_id']);
echo Html::hiddenInput('pasienadmisi_id', $pasienadmisi_id, ['class' => 'pasienadmisi_id']);
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
                    'back',
                ]) ?>
            </div>
            <div class="panel-body">
                <div class="row">
                  <div class="panel panel-default">
                      <div class="panel-heading">
                          <h6 class="panel-title"><b>Informasi Pasien</b></h6>
                      </div>
                      <div class="panel-body">
                          <div class="row">
                              <div class="col-md-12">
                                  <div class="row">
                                      <div class="col-md-3">
                                          <label class="text-left control-label col-sm-5"><b>
                                              <?= Yii::t("fe", "No Rekam Medik") ?></b>
                                          </label>
                                          <div class="col-sm-7">
                                              <p><b>:</b>&nbsp;<?= ArrayHelper::getValue($dataPasien, 'no_rekam_medik') ?></p>
                                          </div>
                                      </div>
                                      <div class="col-md-3">
                                          <label class="text-left control-label col-sm-5"><b>
                                              <?= Yii::t("fe", "No SEP") ?></b>
                                          </label>
                                          <div class="col-sm-7">
                                              <p><b>:</b>&nbsp;<?= ArrayHelper::getValue($dataPasien, 'nosep') ?></p>
                                          </div>
                                      </div>
                                      <div class="col-md-3">
                                          <label class="text-left control-label col-sm-5"><b>
                                              <?= Yii::t("fe", "Penjamin") ?></b>
                                          </label>
                                          <div class="col-sm-7">
                                              <p><b>:</b>&nbsp;<?= ArrayHelper::getValue($dataPasien, 'penjamin_nama') ?></p>
                                          </div>
                                      </div>
                                      <div class="col-md-3">
                                          <label class="text-left control-label col-sm-5"><b>
                                              <?= Yii::t("fe", "Tanggal Masuk") ?></b>
                                          </label>
                                          <div class="col-sm-7">
                                              <p><b>:</b>&nbsp;<?= isset($dataPasien['tgl_pendaftaran']) ? date('d-M-Y', strtotime($dataPasien['tgl_pendaftaran']) ) : '' ?></p>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="row">
                                      <div class="col-md-3">
                                          <label class="text-left control-label col-sm-5"><b>
                                              <?= Yii::t("fe", "Nama Pasien") ?></b>
                                          </label>
                                          <div class="col-sm-7">
                                              <p><b>:</b>&nbsp;<?= ArrayHelper::getValue($dataPasien, 'nama_pasien') ?></p>
                                          </div>
                                      </div>
                                      <div class="col-md-3">
                                          <label class="text-left control-label col-sm-5"><b>
                                              <?= Yii::t("fe", "Hak Kelas") ?></b>
                                          </label>
                                          <div class="col-sm-7">
                                              <p><b>:</b>&nbsp;<?= ArrayHelper::getValue($dataPasien, 'hak_kelas') ?></p>
                                          </div>
                                      </div>
                                      <div class="col-md-3">
                                          <label class="text-left control-label col-sm-5"><b>
                                              <?= Yii::t("fe", "Ruangan") ?></b>
                                          </label>
                                          <div class="col-sm-7">
                                              <p><b>:</b>&nbsp;<?= ArrayHelper::getValue($dataPasien, 'ruangan_nama') ?></p>
                                          </div>
                                      </div>
                                      <div class="col-md-3">
                                          <label class="text-left control-label col-sm-5"><b>
                                              <?= Yii::t("fe", "Tanggal Keluar") ?></b>
                                          </label>
                                          <div class="col-sm-7">
                                              <p><b>:</b>&nbsp;<?= isset($dataPasien['tglpasienpulang']) ? date('d-M-Y', strtotime($dataPasien['tglpasienpulang']) ) : '' ?></p>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="row">
                                      <div class="col-md-3">
                                          <label class="text-left control-label col-sm-5"><b>
                                              <?= Yii::t("fe", "Dokter DPJP") ?></b>
                                          </label>
                                          <div class="col-sm-7">
                                              <p><b>:</b>&nbsp;<?= ArrayHelper::getValue($dataPasien, 'dokter_dpjp') ?></p>
                                          </div>
                                      </div>
                                      <div class="col-md-3">
                                          <label class="text-left control-label col-sm-5"><b>
                                              <?= Yii::t("fe", "Diagnosa Utama") ?></b>
                                          </label>
                                          <div class="col-sm-7">
                                              <p><b>:</b>&nbsp;<?= $diagnosaUtamaNama ?></p>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
                </div>
                <div class="row">
                  <div class="panel panel-default">
                      <div class="panel-heading">
                          <h6 class="panel-title"><b>Tagihan Pasien</b></h6>
                      </div>
                      <div class="panel-body">
                          <div class="row">
                              <div class="col-md-12">
                                  <div class="row">
                                      <div class="col-md-3">
                                          <label class="text-left control-label col-sm-6"><b>
                                              <?= Yii::t("fe", "Tagihan Pasien") ?></b>
                                          </label>
                                          <div class="col-sm-6">
                                              <p style="font-size: 15px;color: #34bfa3"><b>:&nbsp;Rp. <?= DocoHelpers::formatNumber($dataPasien['tagihan_rs']) ?></b></p>
                                          </div>
                                      </div>
                                      <div class="col-md-3">
                                          <label class="text-left control-label col-sm-6"><b>
                                              <?= Yii::t("fe", "Tarif Inacbgs") ?></b>
                                          </label>
                                          <div class="col-sm-6">
                                              <p style="font-size: 15px;color: #34bfa3"><b>:&nbsp; Rp. <font class="tarif_inacbg"><?= DocoHelpers::formatNumber($dataPasien['tarif_inacbg']) ?></font> </b></p>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
                </div>
                <?php foreach ($model as $key => $value) : 
                    $kelompok = ArrayHelper::getValue($value, 'kelompok');
                    $monitorBpjsId = ArrayHelper::getValue($value, 'monitorbpjs_id');
                    $persen = ArrayHelper::getValue($value, 'persen', 0);
                    $subTotal = ArrayHelper::getValue($value, 'sub_total', 0);
                    $persenKelompok = ArrayHelper::getValue($value, 'persen_kelompok', 0);
                    $persenKelompok = is_null($persenKelompok) ? ' (0%)' : ' ('.$persenKelompok.'%)';
                    $totalPersenKelompok = ArrayHelper::getValue($value, 'total_persenkelompok', 0);
                    $totalPersenKelompok = is_null($totalPersenKelompok) ? 0 : $totalPersenKelompok;
                ?>
                <div class="wrapper dashboard">
                    <div class="col-md-4">  
                      <div class="rang">
                          <div class="rang-title">
                            <h1><?= $kelompok ?></h1>
                            <p style="font-size: 20px;color: #34bfa3"><strong>
                              <font class="sub_total" id="sub_total_<?= $monitorBpjsId?>"><?= DocoHelpers::formatNumber($subTotal)  ?></font><span style="color: #a3cd3b" id="persenTagihan-<?= $monitorBpjsId?>"><?= $persenKelompok ?></span>
                              </strong></p>
                          </div>
                          <svg class="meter">
                              <circle class="meter-left" r="96" cx="135" cy="142"></circle>
                              <circle class="meter-center" r="96" cx="135" cy="142"></circle>
                              <circle class="meter-right" r="96" cx="135" cy="142"></circle>
                              <polygon class="meter-clock" points="129,145 137,50 145,145"></polygon>
                              <circle class="meter-circle" r="10" cx="137" cy="145"></circle>
                          </svg>
                          <br>
                          <input type="number" value="<?= $persen ?>" class="aja" id="ranger" data-primary="<?= $monitorBpjsId?>" data-subtotal="<?= $subTotal?>">&nbsp;<strong>(%)</strong><br><br>
                          <p>*<i><strong>berapa persen dari tarif inacbgs</strong></i></p>
                          <p style="font-size: 15px;color: #34bfa3"><strong>Persentase Pengelompokan</strong></p>
                          <p style="font-size: 20px;color: #34bfa3" class="persen-inacbg" id="persen-inacbg-<?= $monitorBpjsId?>"><strong>
                            <?= DocoHelpers::formatNumber($totalPersenKelompok) ?></strong></p>
                          <p><?=Html::button(\Yii::t('fe', '<i class="fa fa-eye"></i> Tagihan'),['class' => 'btn btn-info bg-slate btn-sm', 'data-toggle' => 'modal', 
                          'data-target' => '#modal_backdrop', 'data-width' => '90%', 
                          'action' => '/penjamin-asuransi/monitoring-pasien-bpjs/detail-tagihan?id='.$id.'&monitorbpjs_id='.$monitorBpjsId]); ?></p>
                      </div>
                    </div>
                </div>
                <?php endforeach; ?>       
            </div>
        </div>
    </div>
</div>

<?php
$this->registerCss($this->render('css/monitor.css'));
$this->registerJs($this->render('js/monitor.js'));



<?php

/**
 * @author Arief Saputra
 * @description pilih antrian
**/

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Panggil Antrian'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<audio id="playerAudio" preload="auto" tabindex="0" controls="" type="audio/mpeg" hidden='true'></audio>
<input type="hidden" name="" class="params-header" value="<?=isset($param) ? $param : '' ?>">
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
                      <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias") . ($loket_nama ? ' - ' . Yii::t('fe', 'Loket') . ' ' . $loket_nama : ''); ?></b></h3>
                      <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                  </div>
              </div>
              <!-- end -->
              <div class="heading-elements">
                    <ul class="icons-list">
                        <?php if ($param == 'rajal' || $param == 'penunjang') : ?>
                            <li>
                                <a
                                    class="btn btn-link"
                                    href="/antrian/display-antrian/pilih-loket?jenisantrian_id=<?= $jenisantrian_id; ?>&redirect=antrian/display-antrian/panggil-antrian"
                                >
                                    <?= Yii::t('fe', 'Pindah loket'); ?>
                                </a>

                                <?= Html::hiddenInput('loket', $loket_nama, ['id' => 'loket']); ?>
                            </li>
                        <?php endif; ?>
                        
                    </ul>
                </div>
            </div>

            <div class="panel panel-white">
                <div class="panel-heading">
                    <legend class="text-bold"><?=Yii::t('fe','Data kunjungan')?></legend>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <!-- <li><a data-action="collapse"></a></li> -->
                        </ul>
                    </div>
                </div>
                <div class="panel-body no-border">
                    
                    <div class='row'>
                        <div class="col-lg-6">
                            <div class="col-lg-6">
                                <?= Html::hiddenInput('antrian_id', '', ['id' => 'hide_antrian_id']); ?>
                                <?= Html::hiddenInput('limit_antrian', '', ['id' => 'hide_limit_antrian']); ?>
                                <div class="row">
                                    <div class="col-lg-12 text-center">
                                        <h2>No Antrian</h2>
                                        <span id="no_antrian" style="font-size: 60px;">
                                            <?php echo "-"; ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 text-center">
                                        Panggilan Ke : <span id="jumlah_panggil"> x </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 text-center">
                                        Sisa Antrian : <span id="queue"> x </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="btn-group-vertical">
                                    <button id="btnNext" type="button" class="btn btn-lg btn-info">
                                        <i class="fa fa-arrow-right"></i> No. Berikutnya
                                    </button>
                                    <button id="btnPilih" type="button" class="btn btn-lg btn-success btnPilih">
                                        <i class="fa fa-check"></i> Pilih
                                    </button>
                                    <button id="btnPanggilUlang" type="button" class="btn btn-lg btn-primary btnPanggilUlang">
                                        <i class="fa fa-volume-up"></i> Panggil Ulang
                                    </button>
                                    <button id="btnLewati" type="button" class="btn btn-lg btn-warning" 
                                        data-confirm-message="<?= Yii::t('fe', 'Apakah anda yakin untuk lewati antrian?'); ?>">
                                        <i class="fa fa-share"></i> Lewati
                                    </button>
                                    <button id="btnBatal" type="button" class="btn btn-lg btn-danger btnBatal"
                                    data-confirm-message="<?= Yii::t('fe', 'Apakah anda yakin untuk membatalkan data ini?'); ?>">
                                        <i class="fa fa-times"></i> Batal
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <fieldset>
                                <legend>No Antrian yang di lewati</legend>
                                    <table 
                                        class="table datatable-basic table-striped table-hover dataTable no-footer" 
                                        id="table-antrian-lewati"
                                        style="width:100%;"
                                    >
                                        <thead>
                                            <tr class="bg-inverse">
                                                <th><?= Yii::t('fe', 'No antrian'); ?></th>
                                                <th><?= Yii::t('fe', 'Aksi'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="2" class="text-center"><?= Yii::t('fe', 'Data tidak ditemukan') ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                            </fieldset>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>


&nbsp;
<div class="clearfix">
</div>


<?php
$this->registerJs('
');
$this->registerJs($this->render('js/pilih-antrian.js'));
?>

<?php

/**
 * @author Yaya
 * @copyright 26 April 2018 
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\typeahead\Typeahead;
use yii\web\JsExpression;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', $title), 'url' => ['index']];

?>
<?php 
    $form = ActiveForm::begin([
        'id' => 'ajax-form', 
        'enableAjaxValidation'=>false, 
        'enableClientValidation'=>false,
        'type' => ActiveForm::TYPE_VERTICAL,
    ]); 
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                            <?php echo Breadcrumbs::widget([
                                  'homeLink' => [ 
                                                  'label' => Yii::t('fe', 'Home'),
                                                  'url' => Yii::$app->homeUrl,
                                             ],
                                  'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                               ]); 
                            ?>
                    </div>
                </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'save-submit',
                    'back'
                ]);?>
            </div>
            <div class="panel-body">
                <br>
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= $this->title ?></b></h6>
                        </div><br>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="text-left control-label col-sm-3">
                                            <b>No Pendaftaran</b>
                                        </label>
                                        <div class="col-sm-5">
                                             <span class="no_pendaftaran"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="text-left control-label col-sm-3">
                                            <b>No Rekam Medik</b>
                                        </label>
                                        <div class="col-sm-5">
                                             <span class="no_rekam_medik"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="text-left control-label col-sm-3">
                                            <b>Nama Pasien</b>
                                        </label>
                                        <div class="col-sm-5">
                                            <span class="nama_pasien"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="text-left control-label col-sm-3">
                                            <b>Tanggal Lahir</b>
                                        </label>
                                        <div class="col-sm-5">
                                             <span class="tanggal_lahir"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="text-left control-label col-sm-3">
                                            <b>Tanggal Pendaftaran</b>
                                        </label>
                                        <div class="col-sm-5">
                                             <span class="tgl_pendaftaran"></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="text-left control-label col-sm-3">
                                            <b>Tanggal Piutang</b>
                                        </label>
                                        <div class="col-sm-5">
                                             <span class="tgl_pemberianpiutang"></span>
                                        </div>
                                    </div><br>
                                    <div class="row">
                                        <label class="text-left control-label col-sm-3">
                                            <b><?= $model->getAttributeLabel('pegawai_id') ?></b>
                                        </label>
                                        <div class="col-sm-5">
                                             <span class="nama_pegawai"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row">
                                        <label class="text-left control-label col-sm-3">
                                            <b>Total Tagihan</b>
                                        </label>
                                        <div class="col-sm-5">
                                            <p style="text-align: right;font-size: 17px;font-weight: bold;background-color: #ABEBC6"><strong><span class="total_piutang"></span></strong></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="text-left control-label col-sm-3">
                                            <b>Sudah Bayar</b>
                                        </label>
                                        <div class="col-sm-5">
                                            <p style="text-align: right;font-size: 17px;font-weight: bold;background-color: #ABEBC6"><strong><span class="total_sudahbayar"></span></strong></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="text-left control-label col-sm-3">
                                            <b>Sisa Piutang</b>
                                        </label>
                                        <div class="col-sm-5">
                                            <p style="text-align: right;font-size: 17px;font-weight: bold;background-color: #ABEBC6"><strong><span class="total_sisapiutang"></span></strong></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row detail-form">
                                <?= Html::hiddenInput('PembayaranPiutangForm[total_sisapiutang]', '', ['id' => 'total_sisapiutang']); ?>

                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <?= $form->field($model, 'tgl_pembayaranpiutang', [
                                                'inputOptions' => [
                                                    'class' => 'tgl_pembayaranpiutang',
                                                    'readonly' => true,
                                                ],
                                                'addon' => [
                                                    'append' => ['content'=>'<i class="fa fa-calendar"></i>'],
                                                ]
                                            ]); ?>
                                        </div>
                                        <div class="col-md-3">
                                            <?= $form->field($model, 'total_bayarpiutang')->textInput(['class' => 'form-control doco-number', 'value' => 0]); ?>
                                        </div>
                                        <div class="col-md-3">
                                            <?= $form->field($model, 'metode_pembayaran')->dropDownList([], [
                                                'class' => 'select2 selectMetode',
                                                'prompt' => '— PILIH —',
                                            ])->label(Yii::t('fe', 'Metode Pembayaran')); ?>
                                        </div>
                                        <div class="col-md-3 div-jenisnontunai">
                                            <?= $form->field($model, 'jenisnontunai_id')->dropDownList([], [
                                                'class' => 'select2 selectJenis',
                                                'prompt' => '— PILIH —',
                                            ])->label(Yii::t('fe', 'Jenis Non Tunai')); ?>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <?= $form->field($model, 'catatan')->textArea([], [
                                                    'rows' => '6',
                                                ]);
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php
$this->registerJs('
var no_rekam_medik = "'.$data['no_rekam_medik'].'";
var no_pendaftaran = "'.$data['no_pendaftaran'].'";
var nama_pegawai = "'.$data['nama_pegawai'].'";
var nama_pasien = "'.$data['nama_pasien'].'";
var tanggal_lahir = "'.$data['tanggal_lahir'].'";
var tgl_pendaftaran = "'.$data['tgl_pendaftaran'].'";
var tgl_pemberianpiutang = "'.$data['tgl_pemberianpiutang'].'";
var total_tagihan = "'.$data['tagihan'].'";
var total_sudahbayar = "'.$data['total_bayarpiutang'].'";
var total_sisapiutang = "'.$data['total_sisapiutang'].'";
var total_piutang = "'.$data['total_piutang'].'";
var metode_non_tunai = "'.$metodeNonTunai.'";

', View::POS_END);
$this->registerJs($this->render('js/index.js'), View::POS_END); 
?>
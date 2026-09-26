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
                <?= Html::submitButton('<b><i class="fa fa-floppy-o"></i></b>'.Yii::t('fe', ' Simpan'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs btn-simpan',
                        'disabled' => true,
                    ]);
                ?>
            </div>
            <div class="panel-body">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= $this->title ?></b></h6>
                        </div><br>
                        <div class="panel-body">
                            <?= Html::hiddenInput('pendaftaran_id_hidden', '', ['id' => 'pendaftaran_id_hidden']); ?>
                            <?= Html::hiddenInput('total_tagihan', '', ['id' => 'total_tagihan']); ?>
                            <?= Html::hiddenInput('tagihan_ranap', '', ['id' => 'tagihan_ranap']); ?>
                            <?= Html::hiddenInput('penjualanresep_id', '', ['id' => 'penjualanresep_id']); ?>
                            <?= Html::hiddenInput('no_resep', '', ['id' => 'no_resep']); ?>
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-6">
                                        <div class="col-sm-8">
                                            <?= $form->field($model, 'pendaftaran_id', [
                                            'addon' => [
                                                'append' => ['content'=>'<i class="fa fa-refresh" id="refresh-pendaftaran_id"></i>'],
                                            ]
                                            ])->dropDownList([],[
                                                'class' => 'select2',
                                                'id' => 'pendaftaran_id',
                                            ])->label($model->getAttributeLabel('no_pendaftaran')); ?>
                                        </div>
                                    </div>
                                </div>
                            </div><hr>
                            <div class="form-group detail-form">
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <?= $form->field($model, 'tgl_pemberianpiutang', [
                                                'inputOptions' => [
                                                    'class' => 'tgl_pemberianpiutang',
                                                    'readonly' => true,
                                                ],
                                                'addon' => [
                                                    'append' => ['content'=>'<i class="fa fa-calendar"></i>'],
                                                ]
                                            ]); ?>
                                        </div>
                                        <div class="col-md-3">
                                            <?= $form->field($model, 'pegawai_id', [
                                                'addon' => [
                                                    'append' => ['content'=>'<i class="fa fa-refresh" id="refresh-pegawai_id"></i>'],
                                                ]
                                            ])->dropDownList([], [
                                                    'prompt' => 'Pilih',
                                                    'placeholder' => $model->getAttributeLabel('pegawai_id')
                                                ]);
                                            ?>
                                        </div>
                                        <div class="col-md-3 data_karyawan">
                                            <?= $form->field($model, 'pegawaimengetahui_id', [
                                                'addon' => [
                                                    'append' => ['content'=>'<i class="fa fa-refresh" id="refresh-karyawan_id"></i>'],
                                                ]
                                                ])->dropDownList([],[
                                                    'class' => 'select2',
                                                    'id' => 'karyawan_id',
                                                ])->label($model->getAttributeLabel('pegawaimengetahui_id')); ?>
                                                <?= Html::hiddenInput('PemberianPiutangForm[is_checkpegawai]', '',['id' => 'is_checkpegawai']); ?>
                                        </div>
                                        <div class="col-md-3">
                                            <?= $form->field($model, 'total_piutang')->textInput(['class' => 'form-control doco-number']); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-md-6">
                                            <?= $form->field($model, 'catatan')->textArea([], [
                                                    'rows' => '6',
                                                ]);
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div><br><hr>
                            <div class="row panel-informasi">
                                <div class="col-md-6">
                                    <div class="panel panel-white">
                                        <div class="panel-heading">
                                            <h6 class="panel-title">Detail </h6>
                                        </div>
                                        <div class="panel-body">
                                            <div class="row">
                                                <label class="text-left control-label col-sm-5">
                                                    <b>No Rekam Medik</b>
                                                </label>
                                                <div class="col-sm-7">
                                                     <span class="no_rekam_medik"></span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label class="text-left control-label col-sm-5">
                                                    <b>Nama Pasien</b>
                                                </label>
                                                <div class="col-sm-7">
                                                    <span class="nama_pasien"></span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label class="text-left control-label col-sm-5">
                                                    <b>Tanggal Lahir</b>
                                                </label>
                                                <div class="col-sm-7">
                                                     <span class="tanggal_lahir"></span>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label class="text-left control-label col-sm-5">
                                                    <b>Tanggal Pendaftaran</b>
                                                </label>
                                                <div class="col-sm-7">
                                                     <span class="tgl_pendaftaran"></span>
                                                </div>
                                            </div><br>
                                            <div class="row">
                                                <label class="text-left control-label col-sm-5">
                                                    <b>Tagihan</b>
                                                </label>
                                                <div class="col-sm-7">
                                                    <span class="total_tagihan"></span> 
                                                </div>
                                                <input type="hidden" id="total-pembulatan">
                                                <input type="hidden" id="pembulatan">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="panel panel-white">
                                        <div class="panel-heading">
                                            <h6 class="panel-title">Piutang </h6>
                                        </div>
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-sm-8">
                                                    <?= $form->field($model, 'total_bayarpiutang', [
                                                    'inputOptions' => [
                                                        'id' => 'total_bayarpiutang',
                                                        'readonly' => true,
                                                    ],
                                                    ]); ?> 
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-8">
                                                    <?= $form->field($model, 'total_sisapiutang', [
                                                    'inputOptions' => [
                                                        'id' => 'total_sisapiutang',
                                                        'readonly' => true,
                                                    ],
                                                    ]); ?> 
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
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php
$this->registerJs($this->render('js/index.js'), View::POS_END); 
?>
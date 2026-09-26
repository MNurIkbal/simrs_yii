<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-22 11:44:01
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-22 15:24:14
 */

use app\components\DHtml;
use app\components\DocoHelpers;

use kartik\widgets\DatePicker;
use kartik\widgets\ActiveForm;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;


$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<style type="text/css">
    label{
        font-weight: bold;
    }

    .info-pasien span {
        display: block;
        margin-bottom: 15px;
        padding: 10px 0;
        background: #eee;
    }

    .info-pasien span label {
        display: block;
        padding: 0;
        font-size: 12pt;
    }
    .column-info {
        min-height: auto;
    }
    textarea {
        resize: none;
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
                      <h3 class="panel-title"><b><?= ucwords($this->title) ?></b></h3>
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
                    'new' => [
                        'type'=>'button',
                        'title' => Yii::t('fe', 'Baru'),
                        'icon' => 'fa fa-pencil',
                        'attributes' => [
                            'data-options' => "click",
                            'id' => "btn-new",
                        ]
                    ],
                    'save' => [
                        'type'=>'button',
                        'title' => Yii::t('fe', 'Simpan'),
                        'icon' => 'fa fa-save',
                        'attributes' => [
                            'data-options' => "click",
                            'id' => "btn-save",
                        ]
                    ],
                    'kwitansi' => [
                        'title' => Yii::t('fe', 'Print Kwitansi'),
                        'icon' => 'fa fa-eye',
                        'attributes' => [
                            'data-target' => $module."print-kwitansi",
                            'data-options' => 'click',
                            'class' => "print-bkk-kw",
                            'id' => "print-kwitansi",
                            'disabled' => true
                        ]
                    ],
                    'bkk' => [
                        'title' => Yii::t('fe', 'Print BKK'),
                        'icon' => 'fa fa-eye',
                        'attributes' => [
                            'data-target' => $module."print-bkk",
                            'data-options' => 'click',
                            'class' => "print-bkk-kw",
                            'id' => "print-bkk",
                            'disabled' => true
                        ]
                    ],
                ]);?>
            </div>
            <div class="panel-body">
                <?php
                $form = ActiveForm::begin([
                    'id'=>'returtagihan-form',
                    'enableAjaxValidation' => false,
                    'enableClientValidation' => false,
                    // 'type' => ActiveForm::TYPE_HORIZONTAL,
                    'action' => "/kasir/retur-tagihan/save",
                    'formConfig' => [
                        'labelSpan' => 3,
                        'deviceSize' => ActiveForm::SIZE_SMALL
                    ],
                    'options' => [
                        'skip-confirm' => "true"
                    ],
                ]);
                ?>
                <?= $form->field($model, "tandabuktibayar_id")->hiddenInput(["id"=> "transaksi_tandabuktibayar_id"])->label(false) ?>

                <div class="col-md-5">
                    <h6 class="panel-title">Nomor Pembayaran / Kwitansi</h6>
                        <p>
                            <?= $form->field($model, "nobuktibayar", [
                                'addon' => [
                                    'append' => ['content'=>'<i class="fa fa-refresh" id="refresh-nobuktibayar"></i>'],
                                ],
                                'horizontalCssClasses' => [
                                    'label' => 'control-label col-md-4',
                                    'wrapper' => "col-md-8"
                                ]
                            ])->dropDownList([],
                                    [
                                        'class' => 'form-control search-kwitansi',
                                        'tabindex' => 1
                                    ]
                            )->label(false); ?>
                        </p>
                </div>
                <hr width="100%" align="center">
                <div class="col-md-12 panel-informasi" id="informasi">
                    <div class="panel panel-default">
                        <a id="info-heading" data-toggle="collapse" href="#infopasien" role="button" aria-expanded="false" aria-controls="infopasien" >
                                <div class="panel-heading flex-container">
                                    <h6 class="panel-title"><?= Yii::t('fe', 'Informasi Pasien') ?></h6>

                                    <p class="p-data" id="data-pasien">
                                        <span class="clearable" id="head_transaksi_no_rekam_medik">-</span> - 
                                        <b><span class="clearable" id="head_transaksi_nama_pasien">-</span></b> 
                                        (<span class="clearable" id="head_transaksi_tgl_lahir">-</span>)
                                    </p>

                                    <ul class="icons-list">
                                        <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                    </ul>

                                </div>
                            </a>
                        <div class="panel-body column-info multi-collapse collapse" id="infopasien" aria-expanded="true">
                            <div class="flex-container">
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-12 font-design"><b>Instalasi Akhir</b></label>
                                    <b><p class="col-sm-12"><span class="clearable" id="transaksi_instalasi_akhir">-</span></p></b>

                                    <label class="text-left control-label col-sm-12 font-design"><b>Ruangan Akhir</b></label>
                                    <b><p class="col-sm-12"><span class="clearable" id="transaksi_ruangan_akhir">-</span></p></b>

                                    <label class="text-left control-label col-sm-12 font-design"><b>Tanggal Pendaftaran</b></label>
                                    <b><p class="col-sm-12"><span class="clearable" id="transaksi_tgl_pendaftaran">-</span></p></b>

                                    <label class="text-left control-label col-sm-12 font-design"><b>No Pendaftaran</b></label>
                                    <b><p class="col-sm-12"><span class="clearable" id="transaksi_no_pendaftaran">-</span></p></b>

                                </div>
                                <div class="col-xs-4">
                                    <label class="text-left control-label col-sm-12 font-design"><b>Pasien</b></label>
                                    <b><p class="col-sm-12"><span class="clearable" id="transaksi_nama_pasien">-</span></p></b>

                                    <label class="text-left control-label col-sm-12 font-design"><b>Tanggal Lahir</b></label>
                                    <b><p class="col-sm-12"><span class="clearable" id="transaksi_tgl_lahir">-</span></p></b>
                                    
                                    <label class="text-left control-label col-sm-12 font-design"><b>No. Rekam Medik</b></label>
                                    <b><p class="col-sm-12"><span class="clearable" id="transaksi_no_rekam_medik">-</span></p></b>
                                    
                                    <label class="text-left control-label col-sm-12 font-design"><b>Tanggal Keluar</b></label>
                                    <b><p class="col-sm-12"><span class="clearable" id="transaksi_tgl_keluar">-</span></p></b>
                                </div>
                                <div class="col-xs-4">
                                    <label class="text-left control-label col-sm-12 font-design"><b>Cara Bayar</b></label>
                                    <b><p class="col-sm-12"><span class="clearable" id="transaksi_carabayar_nama">-</span></p></b>

                                    <label class="text-left control-label col-sm-12 font-design"><b>Penjamin</b></label>
                                    <b><p class="col-sm-12"><span class="clearable" id="transaksi_penjamin_nama">-</span></p></b>
                                    
                                    <label class="text-left control-label col-sm-12 font-design"><b>Kelas Pelayanan</b></label>
                                    <b><p class="col-sm-12"><span class="clearable" id="transaksi_kelaspelayanan_nama">-</span></p></b>

                                    <label class="text-left control-label col-sm-12 font-design"><b>Hak Kelas</b></label>
                                    <b><p class="col-sm-12"><span class="clearable" id="transaksi_hak_kelas">-</span></p></b>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 detail-form">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><?= Yii::t('fe', 'Pembayaran Retur') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h6>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <?= $form->field($model, "tglbuktibayar", [
                                                'horizontalCssClasses' => [
                                                    'label' => 'control-label col-md-4',
                                                    'wrapper' => "col-md-8"
                                                ]
                                            ])->textInput([
                                                'class' => 'form-control',
                                                'id' => "transaksi_tglbuktibayar",
                                                'readonly' => true
                                            ]); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <?= $form->field($model, "jmlpembayaran", [
                                                'horizontalCssClasses' => [
                                                    'label' => 'control-label col-md-4',
                                                    'wrapper' => "col-md-8"
                                                ],
                                                'addon' => ['prepend' => ['content'=>'Rp.']],
                                            ])->textInput([
                                                'class' => 'form-control doco-number',
                                                'id' => "transaksi_jmlpembayaran",
                                                'readonly' => true,
                                                'value' => 0
                                            ]); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <?= $form->field($model, "total_biayaretur", [
                                                'horizontalCssClasses' => [
                                                    'label' => 'control-label col-md-4',
                                                    'wrapper' => "col-md-8"
                                                ],
                                                'addon' => ['prepend' => ['content'=>'Rp.']],
                                            ])->textInput([
                                                'class' => 'form-control doco-number',
                                                'id' => "total-retur",
                                                'readonly' => true,
                                                'value' => 0
                                            ]); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <?= $form->field($model, "tunai", [
                                                'horizontalCssClasses' => [
                                                    'label' => 'control-label col-md-4',
                                                    'wrapper' => "col-md-8"
                                                ],
                                                'addon' => ['prepend' => ['content'=>'Rp.']],
                                            ])->textInput([
                                                'class' => 'form-control doco-number',
                                                'value' => '0',
                                                'id' => 'tunai',
                                                'tabindex' => 2
                                            ]); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <?= $form->field($model, "nontunai", [
                                                'horizontalCssClasses' => [
                                                    'label' => 'control-label col-md-4',
                                                    'wrapper' => "col-md-8"
                                                ],
                                                'addon' => ['prepend' => ['content'=>'Rp.']],
                                            ])->textInput([
                                                'class' => 'form-control doco-number',
                                                'value' => '0',
                                                'id' => 'nontunai',
                                                'tabindex' => 3
                                            ]); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <?= $form->field($model, "keterangan", [
                                                'horizontalCssClasses' => [
                                                    'label' => 'control-label col-md-4',
                                                    'wrapper' => "col-md-8"
                                                ]
                                            ])->textarea([
                                                'class' => 'form-control tb-ecollection',
                                                'id' => 'keterangan',
                                                'rows' => '4',
                                                'tabindex' => 4
                                            ]); ?>
                                        </div>
                                    </div>
                                    <?=Html::activeHiddenInput($model, 'no_pendaftaran', ['class'=>'no_pendaftaran', 'id'=>'no_pendaftaran'])?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
</div>



<?php
$this->registerJs(
    "
    var pembulatan_keatas = ".$konfig["is_pembulatankeatas"].";
    var satuan_pembulatan = ".$konfig["satuanpembulatan"].";

    "
    .$this->render('js/returtagihan.js'), VIEW::POS_END, 'js');
?>
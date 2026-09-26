<?php

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A product of PT Citraraya Nusatama
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use app\components\DocoHelpers;
use kartik\widgets\DateTimePicker;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace('modul_alias'), 'url' => ['/']];
$this->params['breadcrumbs'][] = ['label' => 'Transaksi', 'url' => ['/']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .datepicker > div {
        display: block;
    }
    .kv-datetime-remove {
        display: none !important;
    }
</style>
<div class="row">
    <div class="panel panel-white">
        <div class="panel-heading">
            <div class="row">
                <div class="column-1">
                    <img src="<?= Yii::$app->docoVars->workspace("modul_icon") ?>">
                </div>
                <div class="column-2">
                    <h3 class="panel-title"><b><?= Yii::t('fe', $title) ?></b></h3>
                    <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])) ?>
                </div>
            </div>
        </div>
        <div class="panel-toolbar clearfix">
            <?=
                Html::button("<b><i class='fa fa-floppy-o'></i></b>" . Yii::t('fe', 'Simpan'), [
                    'class' => 'btn btn-info btn-labeled btn-xs',
                    'id' => 'simpan-pengajuan'
                ])
            ?>
            <?=
                Html::a("<b><i class='fa fa-arrow-left'></i></b>" . Yii::t('fe', 'Kembali'), Yii::$app->request->referrer, [
                    'class' => 'btn btn-info btn-labeled btn-xs',
                ])
            ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <?php
                        $form = ActiveForm::begin([
                            'id' => 'proses-form',
                            'enableAjaxValidation' => false,
                            'enableClientValidation' => true,
                            'type' => ActiveForm::TYPE_HORIZONTAL,
                            'formConfig' => [
                                'labelSpan' => 3,
                                'deviceSize' => ActiveForm::SIZE_SMALL
                            ],
                            'options' => [
                                'role' => 'form',
                            ]
                        ])
                    ?>
                    <div class="row">
                        <div class="col-md-6">
                            <?=
                                $form->field($model, 'tanggal_keluar', [
                                    'horizontalCssClasses' => [
                                        'label'   => 'text-left control-label col-sm-5',
                                        'wrapper' => 'col-md-7'
                                    ]
                                ])->textInput([
                                    'class'    => 'form-control input-sm tanggal_masuk',
                                    'readonly' => true
                                ])
                            ?>
                        </div>
                        <div class="col-md-6">
                            <label class="text-left control-label col-sm-5"><b>
                                <?= Yii::t("fe", "Tanggal Pengajuan") ?></b>
                            </label>
                            <div class="col-sm-7">
                                <div class="input-group">
                                    <span class="input-group-addon input-group-addon-cutom">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    <?= 
                                        Html::textInput("PengajuanKlaimForm[tgl_pengajuanklaim]", null,[
                                            'class' => 'input-sm pickadate-input form-control',
                                            'id' => "tgl_pengajuanklaim",
                                        ]);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?=
                                $form->field($model, 'carabayar_nama', [
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-5',
                                        'wrapper' => 'col-md-7'
                                    ]
                                ])->textInput([
                                    'class' => 'form-control input-sm',
                                    'value' => $cachePenjamin['carabayar_nama'],
                                    'readonly' => true
                                ])->label(Yii::t('fe', 'Cara Bayar')); 
                            ?>
                        </div>
                        <div class="col-md-6">
                            <label class="text-left control-label col-sm-5"><b>
                                <?= Yii::t("fe", "Tanggal Jatuh Tempo") ?></b>
                            </label>
                            <div class="col-sm-7">
                                <div class="input-group">
                                    <span class="input-group-addon input-group-addon-cutom">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    <?= 
                                        Html::textInput("PengajuanKlaimForm[tgl_jatuhtempo]", null,[
                                            'class' => 'input-sm pickadate-input form-control',
                                            'id' => "tgl_jatuhtempo",
                                        ]);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?=
                            $form->field($model, 'penjamin_nama', [
                                'horizontalCssClasses' => [
                                    'label'   => 'text-left control-label col-sm-5',
                                    'wrapper' => 'col-md-7'
                                ]
                            ])->textInput([
                                'class' => 'form-control input-sm',
                                'value' => $cachePenjamin['penjamin_nama'],
                                'readonly' => true
                            ])->label(Yii::t('fe', 'Penjamin'))
                            ?>
                        </div>
                        <div class="col-md-6">
                            <?=
                            $form->field($model, 'no_pengajuanklaim', [
                                'horizontalCssClasses' => [
                                    'label'   => 'text-left control-label col-sm-5',
                                    'wrapper' => 'col-md-7'
                                ]
                            ])->textInput([
                                'class' => 'form-control input-sm',
                            ])->label(Yii::t('fe', 'No. Pengajuan'))
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?=
                            $form->field($model, 'instalasi_nama', [
                                'horizontalCssClasses' => [
                                    'label'   => 'text-left control-label col-sm-5',
                                    'wrapper' => 'col-md-7'
                                ]
                            ])->textInput([
                                'class'    => 'form-control input-sm instalasi_nama',
                                'readonly' => true
                            ])->label(Yii::t('fe', 'Instalasi'))
                            ?>
                        </div>
                        <div class="col-md-6">
                            <?=
                                $form->field($model, 'total_piutang', [
                                    'horizontalCssClasses' => [
                                        'label'   => 'text-left control-label col-sm-5',
                                        'wrapper' => 'col-md-7'
                                    ]
                                ])->textInput([
                                    'class'    => 'form-control input-sm total_piutang doco-number',
                                    'readonly' => true
                                ])->label(Yii::t('fe', 'Total Pengajuan'))
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?=
                                $form->field($model, 'ruangan_nama', [
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-5',
                                        'wrapper' => 'col-md-7'
                                    ]
                                ])->textInput([
                                    'class' => 'form-control input-sm ruangan_nama',
                                    'readonly' => true
                                ])->label(Yii::t('fe', 'Ruangan'))
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?=
                                $form->field($model, 'catatan', [
                                    'horizontalCssClasses' => [
                                        'label'   => 'text-left control-label col-sm-5',
                                        'wrapper' => 'col-md-7'
                                    ]
                                ])->textArea([
                                    'rows' => 5,
                                ])
                            ?>
                        </div>
                    </div>
                    <hr>
                    <table id="tabel_detail" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th><?= \Yii::t("fe", "No") ?></th>
                                <th><?= \Yii::t("fe", "No Pendaftaran") ?></th>
                                <th><?= \Yii::t("fe", "No Rekam Medik") ?></th>
                                <th><?= \Yii::t("fe", "No Invoice") ?></th>
                                <th><?= \Yii::t("fe", "Tanggal Masuk") ?></th>
                                <th><?= \Yii::t("fe", "Tanggal Keluar") ?></th>
                                <th><?= \Yii::t("fe", "No SEP") ?></th>
                                <th><?= \Yii::t("fe", "Nama Pasien") ?></th>
                                <th><?= \Yii::t("fe", "Instalasi") ?></th>
                                <th><?= \Yii::t("fe", "Ruangan") ?></th>
                                <th><?= \Yii::t("fe", "Tagihan") ?></th>
                                <th><?= \Yii::t("fe", "Jumlah Pasien Bayar") ?></th>
                                <th><?= \Yii::t("fe", "Jumlah Discount") ?></th>
                                <th><?= \Yii::t("fe", "Jumlah Pengajuan") ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" colspan="13"><?= \Yii::t("fe", "Data tidak ditemukan."); ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label text-left col-sm-5"></label>&nbsp;&nbsp;
                        </div>
                    </div>
                    <?php
                        echo $form->field($model, 'carabayar_id')->hiddenInput(['value' => $cachePenjamin['carabayar_id']])->label(false);
                        echo $form->field($model, 'penjamin_id')->hiddenInput(['value' => $cachePenjamin['penjamin_id']])->label(false);
                        echo $form->field($model, 'tgl_pelayanandari')->hiddenInput()->label(false);
                        echo $form->field($model, 'tgl_pelayanansampai')->hiddenInput()->label(false);
                        echo $form->field($model, 'tgl_keluardari')->hiddenInput()->label(false);
                        echo $form->field($model, 'tgl_keluarsampai')->hiddenInput()->label(false);
                        echo $form->field($model, 'pendaftaran_id')->hiddenInput(['class' => 'pendaftaran_id'])->label(false);
                        ActiveForm::end();
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs($this->render('js/_form_detail_klaim.js'), View::POS_END);
?>
<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\ActiveField;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
use kartik\widgets\DepDrop;
use kartik\widgets\FileInput;
use kartik\widgets\ColorInput;
?>

<style>
    .datepicker>div{
        display:block;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>

                <?php
                $form = ActiveForm::begin([
                    'id' => 'konfig-form',
                    'action' => '/master/konfig-system/konfig-billing?id='.$id_encrypt,
                    'enableAjaxValidation' => false,
                    'enableClientValidation' => false,
                        // 'type' => ActiveForm::TYPE_INLINE,
                    // 'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => [
                        'labelSpan' => 3,
                        'deviceSize' => ActiveForm::SIZE_SMALL
                    ],
                    'options' => [
                        'role' => 'form',
                        'enctype' => 'multipart/form-data'
                    ]
                ]);
                ?>     
                <fieldset title="1" onmouseover="this.title='';">
                    <legend class="text-uppercase font-size-sm font-weight-bold"><?= Yii::t('fe', 'Konfigurasi Billing') ?></legend>
                    <div class="row">
                        <div class="col-md-12">
                            <legend class="text-uppercase font-size-sm font-weight-bold"><?= Yii::t('fe', 'Perhitungan Biaya Admin') ?></legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= $form->field($model, 'default_biaya')->checkbox(['class' => 'styled', 'id' => 'default_biaya'])->label(Yii::t('fe', '<b>Default Biaya Admin RI</b>')); ?>
                                </div>
                            </div>
                            <?php
                            if ($showField == 1) {
                                $display = 'display: show';
                            }else{
                                $display = 'display: none';
                            }
                            ?>
                            <div class="row persentase-cls" style="<?php echo $display ?>">
                                <div class="col-md-6">
                                    <?= $form->field($model, 'adm_persen', ['labelOptions' => ['class' => 'text-left']])
                                        ->textInput(['placeholder' => $model->getAttributeLabel('persentaselb'), 
                                            'class' => 'form-control input-sm doco-decimal-100',
                                            'id' => 'adm_persen',
                                        ]); ?>
                                </div>
                            </div>
                            <div class="row daftartindakan-cls" style="<?php echo $display ?>">
                                <div class="col-md-6">
                                <?= $form->field($model, 'adm_tindakan_id',[
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-8'
                                        ],
                                    ])->dropDownList([$setValTindakan],[
                                        'class' => 'select2',
                                        'id' => 'adm_tindakan_id',
                                        'tabindex' => '2'
                                    ])->label(Yii::t('fe', 'Daftar Tindakan')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    <div class="row">
                        <div class="col-md-12">
                            <legend class="text-uppercase font-size-sm font-weight-bold"><?= Yii::t('fe', 'Pembulatan Billing') ?></legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= $form->field($model, 'is_pembulatankeatas',[
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ],
                                        ])->dropDownList([
                                            1 => 'Round up',
                                            0 => 'Round Down',
                                        ],[
                                            'class' => 'select2',
                                            'id' => 'is_pembulatankeatas',
                                            'tabindex' => '2'
                                        ])->label(Yii::t('fe', 'Jenis Pembulatan')); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                <?= $form->field($model, 'satuanpembulatan',[
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-8'
                                        ],
                                    ])->dropDownList($getPembulatan,[
                                        'class' => 'select2',
                                        'id' => 'satuanpembulatan',
                                        'tabindex' => '2'
                                    ])->label(Yii::t('fe', 'Satuan Pembulatan')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <legend class="text-uppercase font-size-sm font-weight-bold"><?= Yii::t('fe', 'Manage Tagihan') ?></legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= $form->field($model, 'kelola_tagihan', [
                                            'labelOptions' => [
                                                'class' => 'text-left'
                                            ]])
                                        ->textInput([
                                            'placeholder' => $model->getAttributeLabel('kelola_tagihan'), 
                                            'class' => 'form-control input-sm doco-number text-right',
                                            'id' => 'kelola_tagihan',
                                        ]); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <legend class="text-uppercase font-size-sm font-weight-bold"><?= Yii::t('fe', 'KONFIGURASI PEMBAYARAN') ?></legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= $form->field($model, 'edit_billing')->checkbox([
                                        'class' => 'styled', 
                                        'id' => 'edit_billing'
                                    ])->label(Yii::t('fe', '<b>Edit Transaksi</b>')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <legend class="text-uppercase font-size-sm font-weight-bold"><?= Yii::t('fe', 'KONFIGURASI PEMBAYARAN') ?></legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= $form->field($model, 'is_set_plafon')->checkbox([
                                        'class' => 'styled', 
                                        'id' => 'set_plafon'
                                    ])->label(Yii::t('fe', '<b>Plafon Edit Tagihan</b>')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <legend class="text-uppercase font-size-sm font-weight-bold"><?= Yii::t('fe', 'KONFIGURASI PENATA JASA') ?></legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= $form->field($model, 'is_show_obat_form_penatajasa')->checkbox([
                                        'class' => 'styled', 
                                        'id' => 'edit_penatajasa'
                                    ])->label(Yii::t('fe', '<b>Hide Datagrid Tindakan BMHP</b>')); ?>
                                </div>
                            </div>
                            <br>
                            <div class="row kelompoktindakan-cls" style="<?php echo $display ?>">
                                <div class="col-md-6">
                                    <?= $form->field($model, 'konfig_kelompok_tindakan',[
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ],
                                        ])->dropDownList([],[
                                            'class' => 'select2',
                                            'id' => 'konfig_kelompok_tindakan',
                                            'multiple' => 'multiple',
                                        ])->label(Yii::t('fe', 'Kelompok Tindakan (Remarks)')); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <br>
                <button id="btn-save" type="submit" class="btn bg-success-600 btn-huge-finish stepy-finish">Simpan <i class="icon-check position-right"></i></button>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("
    var showFiled = {$showField};
    var setValKlpTindakan = ".json_encode($setValKlpTindakan)."
   var xqqq = 1
    // set checkbox sesuai kondisi dengan data yang sudah ada
    $(document).ready(function() {
        if(showFiled === 1){
            document.getElementById('default_biaya').checked = true;
        }else{
            document.getElementById('default_biaya').checked = false;
        }
        $('.doco-number').trigger('change');
        $('.styled, .multiselect-container input').uniform({
            radioClass: 'choice'
        });
    });
",View::POS_END);
?>
<?php
$this->registerCss($this->render('../assets/css/wizard.css'));
$this->registerJs($this->render('../assets/js/konfig-sistem.js'),View::POS_END);
?>

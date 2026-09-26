<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\file\FileInput;
use app\components\DocoHelpers;
use yii\web\View;
?>
<style type="text/css">
    .classMin {
        margin-left: -30px; 
    }
    .modal-content {
        border-radius: 10px !important;
        padding: 0px;
    }
    .header-onsite {
        padding: 10px 10px;
        border-top-right-radius: 10px;
        border-top-left-radius: 10px;
        background-color: #D5E9FF !important;
    }
    .modal-body {
        padding : 20px 20px;
    }
    .bg-inverse {
        background-color: #fff;
        border-color: #37474f;
        color: #01223b;
        border: none;
        font-size: 16px;
    }
    .modal-title {
        font-size: 16px;
        font-weight: 500;
    }
    .modal-content[class*=bg-] .header-onsite .close, .header-onsite[class*=bg-] .close {
        color: #01223b;
        font-size: 28px;
        font-weight: bold;
        line-height: 14px;
        background-color: #D5E9FF !important;
    }

    .btn-batal{
        transition-duration: 0.4s !important;
        color: #014d8a;
        font-weight: 500;
    }

    .btn-batal:hover {
        background-color: #D5E9FF;
        cursor: pointer;
        color: #014d8a;
        font-weight: 500;
    }

    .btn-next{
        transition-duration: 0.4s !important;
        background-color: #014d8a;
        color: #ffffff !important;
        font-weight: 500;
    }

    .btn-next:hover{
        background-color: #014d8a;
        color: #ffffff !important;
        font-weight: 500;
    }

    .confirm-title {
        margin: auto;
        width: 60%;
        border: 3px solid #014d8a;
        border-color: #014d8a;
        background-color: #D5E9FF;
        border-radius: 10px;
        padding: 5px;
    }

    .text-title{
        text-align: center;
        font-weight: 600;
        color: #000000;
    }

    .label-information{
        margin-top: 10px;
        margin-right: 50px;
    }

    .label-information label{
        font-weight: 600;
        color: #000000;
    }

    .row-label{
        display: flex;
        justify-content: center;
        margin-bottom: 5px;
    }

    .tooltip-inner {
        width: 180px;
    }

    .label-component {
        display: flex;
        justify-content: center;
    }
    .confirm-title-patient__no-rm, .confirm-title-patient__no-rujukan{
        font-size: 16px;
        display: block;
        font-weight: 600;
        text-align: center;
    }
    .label-information-pasien{
        padding-top : 25px;
        font-size: 15px;
    }
    #frm-pasien-jenis_identitas input[type=radio] {
        width: 21px;
        height: 21px;
    }
    #frm-pasien-jenis_identitas label.radio-inline {
        padding-left: 30px;
        font-size: 17px;
    }
</style>
<?php 
    $confirm=false;
?>
<div class="modal-header header-onsite bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<div class="modal-body">
    <div class="col-md-12">
        <?=$this->render('_tab', [
            'activeTab' => $activeTab,
        ])?>
    </div>
    <div class="form-group">
        <div class="row first-step">
            <div class="col-md-12">
                <?php
                    $form = ActiveForm::begin([
                        'id' => 'antrian-onsite-bpjs',
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        'type' => ActiveForm::TYPE_VERTICAL,
                        'formConfig' => [
                            'labelSpan' => 3,
                            'deviceSize' => ActiveForm::SIZE_SMALL
                        ],
                    ]);
                ?>
                <?= $form->field($modelForm, 'jenis_identitas')->radioList(
                    [0 => 'NIK', 1 => 'Nomor Kartu BPJS'],
                    ['inline'=>true, 'id'=>'frm-pasien-jenis_identitas', 'class' => 'jenisidentitas']
                )->label(Yii::t('fe', 'Jenis Identitas')); ?>

                <?= $form->field($modelForm, 'nomor_identitas',[
                    'addon' => [
                        'append' => [
                            'content' => Html::button('Cari', ['id'=>'nomor-identitas-btn-cari','class'=>'btn btn-success btn-xlg','style'=>'background-color: #014d8a;']), 
                            'asButton' => true
                        ]
                    ]
                ])->textInput([
                    'class' => 'form-control input-xlg',
                    'autocomplete' => "off",
                    'placeholder' => "Masukan Nomor Identitas",
                    // 'data-toggle' => 'tooltip',
                    // 'data-trigger'=> 'focus',
                    // 'data-placement' => 'right',
                    // 'title' => "Tekan Enter untuk mencari daftar Rujukan & Rencana Kontrol"
                ])->label(Yii::t('fe', 'Nomor Identitas')); ?>

                <?= $form->field($modelForm, 'nomor_rujukan')->textInput([
                    'class' => 'form-control input-xlg',
                    'placeholder' => 'Cari Nomor Rujukan berdasarkan NIK / No Kartu',
                    'readonly' => true
                ]) ?>
                <?= $form->field($modelForm, 'nomor_surat_kontrol', [
                    'options'=> ['style' => 'display:none']
                ])->textInput([
                    'class' => 'form-control input-xlg',
                    'placeholder' => 'Cari Nomor Surat Kontrol berdasarkan NIK / No Kartu',
                    'readonly' => true,
                ]) ?>
                <?= $form->field($modelForm, 'poli_tujuan')
                    ->dropDownList(
                        [],
                        [
                            'id'=>'frm-antrian-poli-tujuan-all',
                            'class'=>'select2 select2poli',
                            'prompt'=>'— PILIH —'
                        ]
                    )
                    ->label(Yii::t('fe', 'Poli Tujuan'));
                ?>
                <?= $form->field($modelForm, 'nama_dokter')
                    ->dropDownList(
                        [],
                        [
                            'id'=>'frm-antrian-dokter_id',
                            'class'=>'select2 select2dokter'
                        ]
                    )
                    ->label(Yii::t('fe', 'Nama Dokter'));
                ?>
                <input type="hidden" id="poli-tujuan-selected">
                <input type="hidden" id="asal-rujukan">
            </div>
            <div class="modal-footer">
                <?=Html::button(\Yii::t('fe', 'Batal'),['class' => 'btn btn-xlg btn-batal', 'data-dismiss' => 'modal']); ?>
                <?=Html::button(\Yii::t('fe', 'Selanjutnya'), ['class' => 'stepy-finish btn btn-xlg btn-next', 'id' => 'next-step']); ?>
            </div>
        </div>
    </div>
    <div class="row second-step">
        <div class="col-md-12">
            <div class="confirm-title">
                <h5 class="text-title confirm-title-patient__name"></h5>
                <div class="confirm-title-patient__no-rm"></div>
            </div>
        </div>
        <div class="col-md-12 label-information-pasien">
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="col-md-5 col-sm-4"><?= Yii::t('fe', 'Tanggal Periksa') ?></div>
                    <div class="col-md-1 col-sm-1 text-left">:</div>
                    <div class="col-md-6 col-sm-7 bold text-left label-tgl-periksa"></div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="col-md-5 col-sm-4"><?= Yii::t('fe', 'No HP') ?></div>
                    <div class="col-md-1 col-sm-1 text-left">:</div>
                    <div class="col-md-6 col-sm-7 bold text-left label-no-hp"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="col-md-5 col-sm-4"><?= Yii::t('fe', 'NIK') ?></div>
                    <div class="col-md-1 col-sm-1 text-left">:</div>
                    <div class="col-md-6 col-sm-7 bold text-left label-nik"></div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="col-md-5 col-sm-4"><?= Yii::t('fe', 'No. Ref') ?></div>
                    <div class="col-md-1 col-sm-1 text-left">:</div>
                    <div class="col-md-6 col-sm-7 bold text-left label-no-referensi"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-12">
                    <div class="col-md-5 col-sm-4"><?= Yii::t('fe', 'No BPJS') ?></div>
                    <div class="col-md-1 col-sm-1 text-left">:</div>
                    <div class="col-md-6 col-sm-7 bold text-left label-no-kartu"></div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="col-md-5 col-sm-4"><?= Yii::t('fe', 'No. SKO') ?></div>
                    <div class="col-md-1 col-sm-1 text-left">:</div>
                    <div class="col-md-6 col-sm-7 bold text-left label-no-surat-kontrol"></div>
                </div>
            </div>
        </div>
        <div class="col-md-12" style="margin: 10px 0px 10px 0px;">
            <hr style="width: 80%; margin:auto;">
        </div>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-6">
                <?= $form->field($modelForm, 'poli_tujuan', [
                        'inputOptions' => [
                            'id' => 'frm-antrian-poli-tujuan',
                            'class'=>'form-control input-xlg',
                            'readonly' => true,
                            'required' => false
                        ]
                    ])->textInput([
                        'class' => 'form-control input-xlg',
                     ])->label(Yii::t('fe', 'Poli Tujuan')) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($modelForm, 'nama_dokter', [
                        'inputOptions' => [
                            'id' => 'frm-nama-dokter',
                            'class'=>'form-control input-xlg',
                            'readonly' => true,
                            'required' => false
                        ]
                    ])->textInput([
                        'class' => 'form-control input-xlg',
                     ])->label(Yii::t('fe', 'Nama Dokter')) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                <?= $form->field($modelForm, 'jam_praktek', [
                        'inputOptions' => [
                            'id' => 'frm-jam-praktek',
                            'class'=>'form-control input-xlg',
                            'readonly' => true,
                            'required' => false
                        ]
                    ])->textInput([
                        'class' => 'form-control input-xlg',
                     ])->label(Yii::t('fe', 'Jam Praktek')) ?>
                </div>
                <div class="col-md-6">
                <?= $form->field($modelForm, 'jenis_kunjungan')
                    ->dropDownList(
                        $jenis_kunjungan,
                        [
                            'id'=>'frm-jenis-kunjungan',
                            'class'=>'select2 select2kunjungan'
                        ]
                    )
                    ->label(Yii::t('fe', 'Jenis Kunjungan'));
                ?>
                    <div class="form-group highlight-addon">
                        <label class="control-label has-star" for="frm-jenis-kunjungan">Jenis Kunjungan</label>
                        <input type="text" class="form-control input-xlg" id="text-jenis-kunjungan" readonly>
                    </div>
                </div>
            </div>
            <?php echo Html::hiddenInput('pasien_id' , null, ['id' => 'pasien-id']); ?>
            <?php echo Html::hiddenInput('no_rekam_medik' , null, ['id' => 'no-rekam-medik']); ?>
            <?php echo Html::hiddenInput('suggest_kode_dokter' , null, ['id' => 'suggest_kode_dokter']); ?>
        </div>
        <div class="modal-footer">
            <?=Html::button(\Yii::t('fe', 'Kembali'),['class' => 'btn btn-lg btn-batal', 'id' => 'back-step']); ?>
            <?=Html::button(\Yii::t('fe', 'Selesai'), ['class' => 'btn btn btn-lg btn-next', 'id' => 'submit-button']); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('.jenisidentitas').on('change', function() {
            var jenisIdentitas = $('input[name="AntrianOnsiteForm[jenis_identitas]"]:checked').val();
            if(jenisIdentitas == 1) {
                $('#antrianonsiteform-nomor_identitas').attr('maxlength', 13);
            } else {
                $('#antrianonsiteform-nomor_identitas').removeAttr('maxlength');
            }

            $('#antrianonsiteform-nomor_identitas').val('');
        })
    })
    </script>
<?php
    $this->registerCss($this->render('../../assets/css/antrian-bpjs.css'));
    $this->registerJs("
        var fullscreen = 0;
    " .$this->render('../../assets/js/antrian-pendaftaran-page.js'), View::POS_END, 'js' );
?>
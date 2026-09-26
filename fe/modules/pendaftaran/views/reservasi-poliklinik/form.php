<?php

/**
 * @Author: Sigit
 * @Date:   2019-03-28 16:03:50
 */

use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\select2\Select2;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('fe', 'Tambah Reservasi Poli');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Reservasi Poliklinik'), 'url' => ['/reservasi-poliklinik/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .panel .panel-toolbar {
        padding: 6px 10px 6px 14px;
        border-top: 1px solid #cccccc;
        border-bottom: 1px solid #cccccc;
        background-color: #ffffff !important;
    }
    .dt-pasien {
        margin-top: 2px !important;
        margin-bottom: 4px !important;
        text-align: left !important;
    }
    .dd-pasien {
        margin-top: 2px !important;
        margin-bottom: 4px !important;
        padding-top: 2px;
    }

    .thumb /*img:not(.media-preview)*/ {
/*    width: 60% !important;
    max-width: 100%;
    height: auto;*/

    width: 111px !important;
    height: 111px !important;
    }

    .thumbnail.no-padding{
    width: 111px !important;
    height: 111px !important;   
    }

</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias") ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])) ?>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div id="data-pasien">
                    <h4 class="text-bold"><?= Yii::t('fe', 'Data Pasien') ?></h4><hr>
                    <div id="cari-pasien">
                        <div class="col-md-6">
                            <div class="form-group highlight-addon">
                                <?= Html::label(Yii::t('fe', 'No. Rekam Medik'), 'cari_pasien', [
                                    'class' => 'control-label col-sm-4'
                                ]) ?>
                                <div class="col-sm-8" style="padding-left: 14px;padding-right: 0px;">
                                    <?= Select2::widget([
                                        'name' => 'cari_pasien',
                                        'value' => '',
                                        'initValueText' => '',
                                        'options' => [
                                            'id' => 'cari_pasien',
                                            'placeholder' => Yii::t('fe', 'No. Rekam Medik / Nama Pasien / No. BPJS / No. Telepon'),
                                        ],
                                        'addon' => [
                                            'prepend' => [
                                                'content'=> Html::checkbox('chk-tipe-pasien', true, ['class' => 'notUniform', 'onchange' => 'setTipePasien(this);']) . ' ' . Yii::t('fe', 'Pasien lama'),
                                                'options'=>[]
                                            ]
                                        ],
                                        'pluginOptions' => [
                                            'allowClear' => false,
                                            'minimumInputLength' => 3,
                                            'ajax' => [
                                                'url' => \yii\helpers\Url::to(['/pendaftaran/reservasi-poliklinik/get-pasien']),
                                                'dataType' => 'json',
                                                'delay' => 500,
                                                'data' => new JsExpression('
                                                    function(params) {
                                                        return {
                                                            q : params.term,
                                                        };
                                                    }
                                                ')
                                            ],
                                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                            'templateResult' => new JsExpression(
                                                'function(result) {
                                                    return result.text;
                                                }'
                                            ),
                                            'templateSelection' => new JsExpression(
                                                'function (selection) {
                                                    return selection.text;
                                                }'
                                            ),
                                        ],
                                        'pluginEvents' => [
                                            'change' => 'function() {
                                                var id = $(this).val();

                                                if (id) {
                                                    $("#reservasipoliklinikform-pasien_id").val(id);

                                                    getInfoPasien(id);
                                                } else {
                                                    $("#reservasipoliklinikform-pasien_id").val(null);

                                                    clearInfoPasien();
                                                }
                                            }'
                                        ],
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div><br>
                    <div id="info-pasien" style="display: none;">
                        <div class="panel panel-flat">
                            <div class="panel-heading">
                                <h6 class="panel-title">
                                    <i class="fa fa-user"></i><span class='inf-pasien-title-nama'><strong>Tn. Nama Pasien</strong></span>
                                    <small class='inf-pasien-title-norm'>No. Rekam Medis : 0000001</small>
                                    <a class="heading-elements-toggle"><i class="icon-more"></i></a>
                                </h6>
                                <div class="heading-elements">
                                    <ul class="icons-list">
                                        <li><a data-action="collapse" class="collapse-pasien"></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="panel-toolbar inf-pasien-toolbar clearfix">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= DocoHelpers::generateToolbar([
                                            'Batal' => [
                                                'type' => 'button',
                                                'title' => \Yii::t('fe', 'Batal'),
                                                'icon' => 'fa fa-close',
                                                'attributes' => [
                                                    'class' => 'btn-batal-info-pasien',
                                                    'data-options'=>'click',
                                                ]
                                            ],
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="col-md-4">
                                    <dl class="dl-horizontal">
                                        <dt class="dt-pasien"><?= Yii::t('fe', 'Nama pasien') ?></dt>
                                        <dd class="dd-pasien" id='inf-pasien-namapasien'>-</dd>
                                        <dt class="dt-pasien"><?= Yii::t('fe', 'Tempat lahir') ?></dt>
                                        <dd class="dd-pasien" id='inf-pasien-tempatlahir'>-</dd>
                                        <dt class="dt-pasien"><?= Yii::t('fe', 'Tanggal lahir') ?></dt>
                                        <dd class="dd-pasien" id='inf-pasien-tanggallahir'>-</dd>
                                        <dt class="dt-pasien"><?= Yii::t('fe', 'Umur') ?></dt>
                                        <dd class="dd-pasien" id='inf-pasien-umur'>-</dd>
                                    </dl>
                                </div>
                                <div class="col-md-4">
                                    <dl class="dl-horizontal">
                                        <dt class="dt-pasien"><?= Yii::t('fe', 'Jenis kelamin') ?></dt>
                                        <dd class="dd-pasien" id='inf-pasien-jeniskelamin'>-</dd>
                                        <dt class="dt-pasien"><?= Yii::t('fe', 'Alamat pasien') ?></dt>
                                        <dd class="dd-pasien" id='inf-pasien-alamatpasien'>-</dd>
                                        <dt class="dt-pasien"><?= Yii::t('fe', 'No telepon') ?></dt>
                                        <dd class="dd-pasien" id='inf-pasien-notelepon'>-</dd>
                                        <dt class="dt-pasien"><?= Yii::t('fe', 'No mobile') ?></dt>
                                        <dd class="dd-pasien" id='inf-pasien-nomobile'>-</dd>
                                    </dl>
                                </div>
                                <div class='col-md-4 col-sm-12'>
                                    <div class="row">
                                        <div class="col-md-6 col-md-offset-3">
                                            <div class="thumbnail no-padding">
                                                <div class="thumb" id="pasang_image"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><br>
                <div id="data-reservasi-poliklinik">
                    <h4 class="text-bold"><?= Yii::t('fe', 'Data Reservasi Poliklinik') ?></h4>
                    
                    <div class="panel-body">
                        <div class="col-md-12">
                            <?php $form = ActiveForm::begin([
                                'id' => 'form-reservasi-poliklinik',
                                'enableClientValidation' => false,
                                'type' => ActiveForm::TYPE_HORIZONTAL,
                                'formConfig' => [
                                    'labelSpan' => 4,
                                    'deviceSize' => ActiveForm::SIZE_SMALL
                                ],
                            ]) ?>

                            <?= Html::activeHiddenInput($model, 'pasien_id') ?>
                            <?= Html::activeHiddenInput($model, 'jadwaldokter_id') ?>
                            <?= Html::activeHiddenInput($model, 'jadwalbukapoli_id') ?>
                            <?= Html::activeHiddenInput($model, 'jam_mulai') ?>
                            <?= Html::activeHiddenInput($model, 'jam_tutup') ?>
                            <?= Html::hiddenInput('konfig_antrian', $kuotaAntrian, ['id' => 'konfig-antrian']) ?>

                            <div class="row">
                                <div class="col-sm-6 data-pasien-extend"  style="display: none;">

                                    <?= $form->field($modelPasien, 'jenisidentitas')->dropDownList(ArrayHelper::map($dataForm['lookup']['jenis_identitas'], 'lookup_id', 'lookup_value'), [
                                        'class' => 'select2 select2pasien jenis_identitas',
                                        'id'=>'frm-pasien-jenisidentitas',
                                        'prompt' => '— PILIH —',
                                    ])->label(Yii::t('fe', 'Jenis Identitas')); ?>

                                    <?= $form->field($modelPasien, 'no_identitas_pasien')->textInput([
                                        'class' => 'form-control input-sm no_identitas_pasien',
                                        'id' => 'no_identitas_pasien',
                                    ]) ?>

                                    <?= $form->field($modelPasien, 'namadepan')->dropDownList(ArrayHelper::map($dataForm['lookup']['nama_depan'], 'lookup_id', 'lookup_value'), [
                                        'class' => 'select2 select2pasien',
                                        'id'=>'frm-pasien-namadepan',
                                        'prompt' => '— PILIH —',
                                    ])->label(Yii::t('fe', 'Nama Depan')); ?>


                                    <?= $form->field($modelPasien, 'nama_pasien', [
                                        'inputOptions' => [
                                            'id' => 'frm-pasien-nama_pasien',
                                            'class' => 'form-control input-sm'
                                        ]
                                    ]); ?>

                                    <?= $form->field($modelPasien, 'tempat_lahir', [
                                        'inputOptions' => [
                                            'id' => 'frm-pasien-tempat_lahir',
                                            'class' => 'form-control input-sm'
                                        ]
                                    ]) ?>

                                    <div class="form-group highlight-addon has-size-sm field-frm-pasien-tanggal_lahir required">
                                        <label class="control-label has-star col-sm-4" for="frm-pasien-tanggal_lahir">Tanggal Lahir</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="frm-pasien-tanggal_lahir" class="form-control" name="PasienForm[tanggal_lahir]" data-mask="99-99-9999">
                                            <input type="hidden" id="frm-pasien-type" class="form-control" name="PasienForm[type]" value="0">
                                        </div>
                                    </div>

                                </div>
                                <div class="col-sm-6 data-pasien-extend" style="display: none;">

                                    <?= $form->field($modelPasien, 'jeniskelamin')
                                        ->radioList(
                                            ArrayHelper::map($dataForm['lookup']['jenis_kelamin'], 'lookup_id', 'lookup_value'),
                                            ['inline'=>true, 'id'=>'frm-pasien-jeniskelamin']
                                        )
                                        ->label(Yii::t('fe', 'Jenis Kelamin'));
                                    ?>

                                    <?= $form->field($modelPasien, 'no_telepon_pasien', [
                                        'inputOptions' => ['id' => 'frm-pasien-no_telepon_pasien']
                                    ]); ?>

                                    <?= $form->field($modelPasien, 'alamat_pasien')->textArea([
                                        'id' => 'frm-pasien-alamat_pasien'
                                    ]); ?>

                                </div>
                                <div class="col-sm-12 data-pasien-extend" style="display: none;">
                                    <hr>
                                </div>
                                <div class="col-sm-6">
                                    <?= $form->field($model, 'tgl_pendaftaranol')->textInput([
                                        'class' => 'form-control input-sm tgl_pendaftaranol',
                                        'id' => 'tgl_pendaftaranol',
                                    ]) ?>

                                    <?= $form->field($model, 'ruangan_id')->widget(DepDrop::classname(), [
                                        'type' => DepDrop::TYPE_SELECT2,
                                        'options' => [
                                            'placeholder' => Yii::t('fe', '- Pilih -'),
                                            'id' => 'ruangan_id',
                                        ],
                                        'pluginOptions' => [
                                            'depends' => ['tgl_pendaftaranol'],
                                            'loadingText' => Yii::t('fe', 'Tidak ada data'),
                                            'url' => Url::to(['/pendaftaran/reservasi-poliklinik/get-ruangan'])
                                        ]
                                    ]) ?>

                                    <?php if ($kuotaDokter): ?>
                                    <?= $form->field($model, 'pegawai_id')->widget(DepDrop::classname(), [
                                        'type' => DepDrop::TYPE_SELECT2,
                                        'options' => [
                                            'placeholder' => Yii::t('fe', '- Pilih -'),
                                            'id' => 'pegawai_id'
                                        ],
                                        'pluginOptions' => [
                                            'depends' => ['tgl_pendaftaranol', 'ruangan_id'],
                                            'loadingText' => Yii::t('fe', 'Tidak ada data'),
                                            'url' => Url::to(['/pendaftaran/reservasi-poliklinik/get-dokter'])
                                        ]
                                    ]) ?>

                                    <?php if (isset($is_nourut) && $is_nourut == true) { ?>
                                        <?= $form->field($model, 'nomor_urut')->widget(DepDrop::classname(), [
                                            'type' => DepDrop::TYPE_SELECT2,
                                            'options' => [
                                                'placeholder' => Yii::t('fe', '- Pilih -'),
                                            ],
                                            'pluginOptions' => [
                                                'depends' => ['ruangan_id', 'pegawai_id', 'tgl_pendaftaranol'],
                                                'loadingText' => Yii::t('fe', 'Tidak ada data'),
                                                'url' => Url::to(['daftar/get-nomor-urut']),
                                            ]
                                        ]) ?>
                                        <?= Html::hiddenInput('is_nomor_urut', $is_nourut, []) ?>

                                    <?php } ?>

                                    <?php endif ?>

                                    <?= $form->field($model, 'jam_kunjungan')->widget(DepDrop::classname(), [
                                        'type' => DepDrop::TYPE_SELECT2,
                                        'options' => [
                                            'placeholder' => Yii::t('fe', '- Pilih -'),
                                        ],
                                        'pluginOptions' => [
                                            'depends' => ['tgl_pendaftaranol', 'ruangan_id', 'pegawai_id', 'konfig-antrian'],
                                            'loadingText' => Yii::t('fe', 'Tidak ada data'),
                                            'url' => Url::to(['/pendaftaran/reservasi-poliklinik/get-jam-kunjungan'])
                                        ]
                                    ]) ?>
                                </div>

                                <div class="col-sm-6">
                                    <?= $form->field($model, 'carabayar_id')->widget(Select2::classname(), [
                                        'data' => ArrayHelper::map($caraBayar, 'carabayar_id', 'carabayar_nama'),
                                        'options' => [
                                            'placeholder' => Yii::t('fe', '- Pilih -'),
                                            'class' => 'carabayar_id',
                                            'options'=> $carabayarOptions,
                                        ],
                                    ]) ?>

                                    <?= $form->field($model, 'penjamin_id')->widget(DepDrop::classname(), [
                                        'type' => DepDrop::TYPE_SELECT2,
                                        'options' => [
                                            'placeholder' => Yii::t('fe', '- Pilih -'),
                                        ],
                                        'pluginOptions' => [
                                            'depends' => ['reservasipoliklinikform-carabayar_id'],
                                            'loadingText' => Yii::t('fe', 'Tidak ada data'),
                                            'url' => Url::to(['/pendaftaran/reservasi-poliklinik/get-penjamin-depdrop'])
                                        ]
                                    ]) ?>

                                    <?php if ($konfigSystem['is_support_jkn']) { ?>
                                        <?= $form->field($model, 'jeniskunjungan')->widget(Select2::classname(), [
                                            'data' => [
                                                '1096' => Yii::t('fe', 'Faskes tingkat 1'),
                                                '1099' => Yii::t('fe', 'Faskes tingkat 2 (RS)'),
                                            ],
                                            'options' => [
                                                'placeholder' => Yii::t('fe', '- Pilih -'),
                                                'id' => 'jeniskunjungan',
                                                'class' => 'jeniskunjungan',
                                            ],
                                        ]) ?>

                                        <?= $form->field($model, 'no_rujukan', [
                                            'inputOptions' => ['id' => 'no_rujukan', 'class' => 'form-bpjs']
                                        ]); ?>

                                        <?= $form->field($model, 'no_bpjs', [
                                            'inputOptions' => ['id' => 'no_bpjs', 'class' => 'form-bpjs']
                                        ]); ?>
                                    <?php } ?>

                                    <?= $form->field($model, 'keterangan')->textArea([
                                        'rows' => 3
                                    ]) ?>
                                </div>
                            </div>
                            
                            <?php ActiveForm::end() ?>
                        </div>
                    </div>
                    <br>
                    <div class="panel-toolbar inf-pasien-toolbar clearfix">
                        <div class="row">
                            <div class="col-md-6">
                                <?= DocoHelpers::generateToolbar([
                                    'save' => [
                                        'attributes' => [
                                            'form_id' => 'form-reservasi-poliklinik'
                                        ]
                                    ],
                                ]) ?>
                            </div>
                        </div>
                    </div>
                    <br>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs("
    var reservasiAwal = ".$reservasiAwal.";
    var reservasiAkhir = ".$reservasiAkhir.";

", View::POS_END, 'reservasi-poli') ?>

<?php $this->registerJs("
    function setTipePasien (_this) {
        if($(_this).prop('checked') == false){
            $('#cari_pasien').attr('disabled', true);
            $('.data-pasien-extend').css('display','block');
            $('#frm-pasien-type').val(1);
        }
        else if($(_this).prop('checked') == true){
            $('#cari_pasien').attr('disabled', false);
            $('.data-pasien-extend').css('display','none');
            $('#frm-pasien-type').val(0);
        }
    }

", View::POS_END, 'reservasi-poli2') ?>

<?php
    $this->registerJs($this->render('js/reservasi-poli.js'));
?>
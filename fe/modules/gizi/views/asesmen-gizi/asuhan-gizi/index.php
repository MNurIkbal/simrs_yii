<?php

/**
 * @Author: rizal
 * @Date:   2018-11-28 10:41:34
 */
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
?>
<style type="text/css">
    .d-inline-grid{
        display: inline-grid;
    }
</style>

<?php $form = ActiveForm::begin([
    'id' => 'form-asuhan-gizi',
    'action'=> '/gizi/asesmen-gizi/simpan-asuhan-gizi?id='.$id,
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL],
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
]) ?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5 class="panel-title"><?= Yii::t('fe', 'Riwayat Gizi dan Makanan') ?></h5>
                </div>
                <div class="panel-body">
                    <div class='col-sm-6'>
                        <?= $form->field($model, 'alergi_makanan')->textArea(['rows'=>2]) ?>
                        <?= $form->field($model, 'pantangan_makanan')->textArea(['rows'=>2]); ?>
                        <?= $form->field($model, 'catatan')->textArea(['rows'=>2]) ?>
                    </div>
                    <div class='col-sm-6'>
                        <?= $form->field($model, 'ketidaksukaan_makanan')->textArea(['rows'=>2]) ?>
                        <?= $form->field($model, 'pengalaman_diet',[
                                'horizontalCssClasses' => [
                                        'label' => 'control-label col-sm-6',
                                        'wrapper' => 'col-md-6'
                                    ],
                                ])
                                ->radioList([1=>'Ada', 0=>'Tidak'], ['inline'=>'true']); ?>
                        <div class="pengalaman_diet hidden">
                            <?= $form->field($model, 'pengalaman_diet_desc')
                                ->textArea(['rows'=>2])
                                ->label(''); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5 class="panel-title"><?= Yii::t('fe', 'Antropometri') ?></h5>
                </div>
                <div class="panel-body">
                    <div class='row'>
                        <label class="control-label col-sm-2">Riwayat Penurunan BB</label>
                        <div class='col-sm-2 d-inline-grid required'> 
                            <label class="control-label">BB Saat Ini</label> 
                            <?= $form->field($model, 'bb_saatini', [
                                'addon'=>[
                                    'append' => [
                                        'content'=>'Kg'
                                    ]
                                ],
                            ])->textInput([
                                'class'=>'form-control hitung_bb hitung_imt doco-number-wcomma',
                                'id'=>'bb_saatini', 
                            ])->label(false); ?>
                        </div>
                        <div class='col-sm-2 d-inline-grid required'> 
                            <label class="control-label ">PB / TB</label> 
                            <?= $form->field($model, 'pb_tb', [
                                'addon'=>[
                                    'append' => [
                                        'content'=>'Cm'
                                    ]
                                ],
                            ])->textInput([
                                'class'=>'form-control hitung_imt docoNumberOnly',
                                'id'=>'pb_tb', 
                            ])->label(false); ?>
                        </div>
                        <div class='col-sm-3 d-inline-grid'> 
                            <label class="control-label ">IMT</label> 
                            <?= $form->field($model, 'label_imt')->textInput(['tabindex' => '-1', 'disabled'=>true, 'id'=>'label_imt'])->label(false); ?>
                            <?= Html::activeHiddenInput($model, 'imt') ?>
                        </div>
                        <div class='col-sm-3 d-inline-grid'> 
                            <label class="control-label ">Status Gizi</label> 
                            <?= $form->field($model, 'label_status_gizi')->textInput(['tabindex' => '-1', 'disabled'=>true, 'id'=>'label_status_gizi'])->label(false); ?>
                            <?= Html::activeHiddenInput($model, 'status_gizi') ?>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-sm-2 col-sm-offset-2 d-inline-grid required'> 
                            <label class="control-label ">BB Biasanya</label> 
                            <?= $form->field($model, 'bb_biasanya', [
                                'addon'=>[
                                    'append' => [
                                        'content'=>'Kg'
                                    ]
                                ],
                            ])->textInput([
                                'class'=>'form-control hitung_bb doco-number-wcomma',
                                'id'=>'bb_biasanya', 
                                'value'=>$dataAsesmen['bb_biasanya'], 
                            ])->label(false); ?>
                        </div>
                        <div class='col-sm-2 d-inline-grid'> 
                            <label class="control-label ">Penurunan BB</label> 
                            <?= $form->field($model, 'label_penurunan_bb', [
                                'addon'=>[
                                    'append' => [
                                        'content'=>'%'
                                    ]
                                ],
                            ])->textInput([
                                'id'=>'label_penurunan_bb',
                                'disabled'=>true,
                                'tabindex' => '-1',
                            ])->label(false); ?>
                            <?= Html::activeHiddenInput($model, 'penurunan_bb') ?>
                        </div>
                        <div class='col-sm-3 d-inline-grid required'> 
                            <label class="control-label ">Kurun Waktu</label> 
                            <?= $form->field($model, 'kurun_waktu', [
                                'addon'=>[
                                    'append' => [
                                        'content'=>'Minggu'
                                    ]
                                ],
                            ])->textInput(['class'=>'form-control docoNumberOnly'])->label(false); ?>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-sm-6'>
                            <?= $form->field($model, 'pengukuran_lainnya')->textArea(['rows'=>2]) ?>
                        </div>
                        <div class='col-sm-6'>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5 class="panel-title"><?= Yii::t('fe', 'Biokimia Terkait Gizi') ?></h5>
                </div>
                <div class="panel-body">
                    <div class='col-sm-6'>
                        <?= $form->field($model, 'biokimia')->textArea(['rows'=>2]) ?>
                    </div>
                    <div class='col-sm-6'>
                        <?= $form->field($model, 'prosedur')->textArea(['rows'=>2]) ?>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5 class="panel-title"><?= Yii::t('fe', 'Fisik Klinis - Gizi') ?></h5>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class='col-sm-11 col-sm-offset-1'>
                            <div class='col-sm-3'>
                                <?= $form->field($model, 'antropi_otot_lengan')->checkbox(); ?>
                                <?= $form->field($model, 'nafsu_makan')->checkbox(); ?>
                                <?= $form->field($model, 'kembung')->checkbox(); ?>
                                <?= $form->field($model, 'gangguan_menelan')->checkbox(); ?>
                            </div>
                            <div class='col-sm-3'>
                                <?= $form->field($model, 'udem')->checkbox(); ?>
                                <?= $form->field($model, 'mual')->checkbox(); ?>
                                <?= $form->field($model, 'konstipasi')->checkbox(); ?>
                                <?= $form->field($model, 'gangguan_mengunyah')->checkbox(); ?>
                            </div>
                            <div class='col-sm-3'>
                                <?= $form->field($model, 'hilang_lemak_subkutan')->checkbox(); ?>
                                <?= $form->field($model, 'muntah')->checkbox(); ?>
                                <?= $form->field($model, 'diare')->checkbox(); ?>
                                <?= $form->field($model, 'gangguan_menghisap')->checkbox(); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class='col-sm-6'>
                            <?= $form->field($model, 'kulit') ?>
                            <?= $form->field($model, 'kepala_dan_mata') ?>
                            <?= $form->field($model, 'gigi_geligi') ?>
                        </div>
                    </div>
                    <div class="row required">
                        <label class="control-label col-sm-2 ">Tekanan Darah</label>
                        <div class='col-sm-2'> 
                            <?= $form->field($model, 'tekanan_darah_mm', [
                                'addon'=>[
                                    'append' => [
                                        'content'=>'Mm'
                                    ]
                                ],
                            ])->textInput([
                                'class'=>'form-control hitung_tekanan_darah docoNumberOnly',
                                'id'=>'mm',
                            ])->label(false); ?>
                        </div>
                        <div class='col-sm-2'> 
                            <?= $form->field($model, 'tekanan_darah_hg', [
                                'addon'=>[
                                    'append' => [
                                        'content'=>'Hg'
                                    ]
                                ],
                            ])->textInput([
                                'class'=>'form-control hitung_tekanan_darah docoNumberOnly',
                                'id'=>'hg',
                            ])->label(false); ?>
                        </div>
                        <div class='col-sm-3 d-inline-grid required'> 
                            <label class="control-label ">Detak Nadi</label> 
                            <?= $form->field($model, 'detak_nadi', [
                                'addon'=>[
                                    'append' => [
                                        'content'=>'/ menit'
                                    ]
                                ],
                            ])->textInput([
                                'class'=>'form-control docoNumberOnly'
                            ])->label(false); ?>
                        </div>
                        <div class='col-sm-3 d-inline-grid required'> 
                            <label class="control-label ">Denyut Jantung</label> 
                            <?= $form->field($model, 'denyut_jantung')->dropDownList(
                                ArrayHelper::map($lookup['denyut_jantung'], 'lookup_name', 'lookup_name'), 
                                ['class'=>'form-control input-sm', 'prompt'=>'-- Pilih --']
                            )->label(false); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class='col-sm-3 col-sm-offset-2 d-inline-grid'> 
                            <?= $form->field($model, 'label_tekanan_darah_mmhg', [
                                'addon'=>[
                                    'append' => [
                                        'content'=>'MmHg'
                                    ]
                                ],
                            ])->textInput([
                                'class'=>'form-control mmhg', 
                                'id' => 'label_tekanan_darah_mmhg',
                                'disabled'=>true,
                                'tabindex' => '-1',
                            ])->label(false); ?>
                            <?= Html::activeHiddenInput($model, 'tekanan_darah_mmhg') ?>
                        </div>
                        <div class='col-sm-3 col-sm-offset-1 d-inline-grid required'> 
                            <label class="control-label ">Pernapasan</label> 
                            <?= $form->field($model, 'pernapasan', [
                                'addon'=>[
                                    'append' => [
                                        'content'=>'/ menit'
                                    ]
                                ],
                            ])->textInput([
                                'class'=>'form-control docoNumberOnly'
                            ])->label(false); ?>
                        </div>
                        <div class='col-sm-3 d-inline-grid required'> 
                            <label class="control-label ">Suhu Tubuh</label> 
                            <?= $form->field($model, 'suhu_tubuh', [
                                'addon'=>[
                                    'append' => [
                                        'content'=>"&#8451;"
                                    ]
                                ],
                            ])->textInput([
                                'class'=>'form-control docoNumberOnly'
                            ])->label(false); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class='col-sm-5 col-sm-offset-2'> 
                            <?= $form->field($model, 'label_tekanan_darah_kondisi')->textInput([
                                'class'=>'form-control', 
                                'disabled'=>true, 
                                'tabindex' => '-1',
                                'id' => 'label_tekanan_darah_kondisi',
                            ])->label(false); ?>
                            <?= Html::activeHiddenInput($model, 'tekanan_darah_kondisi') ?>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='col-sm-6'>
                            <?= $form->field($model, 'data_lain')->textArea(['rows'=>2]) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5 class="panel-title"><?= Yii::t('fe', 'Diagnosa Gizi') ?></h5>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <!-- Diagnosa gizi -->
                            <?= $form->field($model, 'diagnosa_gizi')->textInput(['class'=>' input-tags','data-role' => 'tagsinput'])?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5 class="panel-title"><?= Yii::t('fe', 'Intervensi Gizi') ?></h5>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'tujuan') ?>
                            <?= $form->field($model, 'materi') ?>
                            <?= $form->field($model, 'sasaran') ?>
                            <?= $form->field($model, 'preskripsi_diet') ?>
                            <?= $form->field($model, 'jenis_diet')->dropDownList(ArrayHelper::map($master['jenisdiet'], 'jenisdiet_nama', 'jenisdiet_nama'), ['class'=>'select2', 'multiple'=>true]) ?>
                            <?= $form->field($model, 'rute') ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'edukasi_gizi') ?>
                            <?= $form->field($model, 'media') ?>
                            <?= $form->field($model, 'target_intervensi')->textArea(['rows'=>2]) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5 class="panel-title"><?= Yii::t('fe', 'Rencana Monitoring Evaluasi Gizi') ?></h5>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-7">
                            <?= $form->field($model, 'rencana_evaluasi') ?>
                        </div>
                    </div>
                </div>
            </div>

            <?= Html::activeHiddenInput($model, 'pendaftaran_id') ?>
            <div class="row">
                <div class="col-sm-6">
                    <?php
                        echo Html::submitButton("<b><i class='fa fa-floppy-o'></i></b>" . Yii::t('fe', 'Simpan'), [
                            'class' => 'btn btn-info btn-labeled btn-xs data-save',
                            'id' => 'save-asuhan-gizi',
                        ]);
                    ?>
                </div>
                <div class="col-sm-6 text-right">
                    <?php
                        echo Html::button("<b><i class='fa fa-refresh'></i></b>" . Yii::t('fe', 'Ulang'), [
                            'class' => 'btn btn-info btn-labeled btn-xs data-reset',
                        ]);
                    ?>
                </div>
            </div>

        </div>
    </div>

</div>


<?php ActiveForm::end() ?>


<?php $this->registerJs('
    $(document).ready(function() {
        $(".field-mm, .field-hg, .field-tekanan_darah_mmhg").addClass("d-inline-grid");
    });
    $(".input-tags").tagsinput();
    $(".select2").select2();
    var listBmi = '.json_encode($master['bmi']).'
    var listTekananDarah = '.json_encode($master['klasifikasitekanandarah']).'
    var pendaftaran_id = "'.$id.'"
' . $this->render('js/index.js'), View::POS_END) ?>
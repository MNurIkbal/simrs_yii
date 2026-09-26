<?php

/**
 * @Author: Rizal
 * @Date:   2018-09-10 14:10:00
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\helpers\ArrayHelper;
use kartik\widgets\DepDrop;
use kartik\datetime\DateTimePicker;
?>
<style type="text/css">
    .checkbox2 label:after{
        content: '';
        display: table;
        clear: both;
    }
    .checkbox2 .cr {
        position: relative;
        display: inline-block;
        border: 1px solid #a9a9a9;
        border-radius: .25em;
        width: 1.3em;
        height: 1.3em;
        float: left;
        margin-right: .5em;
    }
    .checkbox2 .cr .cr-icon {
        position: absolute;
        font-size: .8em;
        line-height: 0;
        top: 50%;
        left: 20%;
    }
    .checkbox2 label input[type="checkbox"] {
        display: none;
    }
    .checkbox2 label input[type="checkbox"] + .cr > .cr-icon {
        transform: scale(3) rotateZ(-20deg);
        opacity: 0;
        transition: all .3s ease-in;
    }

    .checkbox2 label input[type="checkbox"]:checked + .cr > .cr-icon {
        transform: scale(1) rotateZ(0deg);
        opacity: 1;
    }

    .checkbox2 label input[type="checkbox"]:disabled + .cr {
        opacity: .5;
    }

    .hide_column_dt {
        display : none;
    }
    h6.panel-title, .h6.panel-title {
        font-size: 15px;
        margin-bottom: 15px;
    }
</style>
<?php
    // Ini di komen untuk melepas validasi ketika pasien pulang kode ini jagnan di hapus
    if(false):
    // if($statusInstruksiTindakan):
?>
                     <!-- <div> -->
    <h6 class="panel-title">
        <span class='inf-pasien-title-nama'><strong>Nama Pasien : <?= $data_pasien['nama_pasien'] ?> </strong></span>
        <small class='inf-pasien-title-norm'>No. Rekam Medis : <?= $data_pasien['no_rekam_medik'] ?> </small>
        <span class="text-danger text-notifpasien" style="display:-webkit-inline-box;"><?= $messageInstruksiTindakan ?> </span>
        <?= Html::hiddenInput('antrian_id', '', ['class'=>'antrian-id']); ?>
        <?= Html::hiddenInput('pasien_id', '', ['class'=>'pasien-id']); ?>
        <?= Html::hiddenInput('norm', '', ['id'=>'norm']); ?>
        <a class="heading-elements-toggle"><i class="icon-more"></i></a>
    </h6>
    <!-- </div> -->
<?php endif ?>
<div class="clear"></div>
<?php
$form = ActiveForm::begin([
    'id' => 'kesimpulan-form',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'action' => '/igd/pemeriksaan-igd/save-kesimpulan',
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]);
?>
<div class="row form_pasien_pulang">
    <div class="col-md-6 select2-md">
        <?= $form->field($modelPasienPulang, 'carakeluar_id', [
                // 'labelOptions' => ['class' => 'text-right']
            ])
            ->dropDownList(
                ArrayHelper::map($data_carakeluar, 'carakeluar_id', 'carakeluar_nama'),
                ['class'=>'select2',
                'prompt' => '--Pilih--'])
            ->label(Yii::t('fe','Cara Keluar'));
        ?>
        <div class="hidden" id="dokterdpjp-div">
            <?=
                $form->field($modelPasienPulang, 'dpjp_id', [])->dropDownList([], [
                    'prompt' => 'Pilih',
                    'class' => 'form-control',
                    'style' => 'padding:9px!important;',
                ]);
            ?>
        </div>
        <div class="hidden" id="kamarruanganjenis-div">
            <?=
                $form->field($modelPasienPulang, 'kamarruangan_jenis', [])->dropDownList([], [
                    'prompt' => 'Pilih',
                    'class' => 'form-control',
                    'style' => 'padding:9px!important;',
                ]);
            ?>
        </div>
        <div class="hidden" id="tempattidurtujuan-div">
            <?=
                $form->field($modelPasienPulang, 'tempattidurtujuan_id', [])->dropDownList([], [
                    'prompt' => 'Pilih',
                    'class' => 'form-control',
                    'style' => 'padding:9px!important;',
                ]);
            ?>
        </div>
        <div class="hidden" id="dokterspesialis_id-div">
            <?=
                $form->field($modelPasienPulang, 'dokterspesialis_id', [])->dropDownList([], [
                    'prompt' => 'Pilih',
                    'class' => 'form-control',
                    'style' => 'padding:9px!important;',
                ]);
            ?>
        </div>
        <?=$form->field($modelPasienPulang, 'tglpasienpulang')
            ->widget(DateTimePicker::className(),[
                'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                'readonly' => true,
                'convertFormat' => true,
                'pluginOptions' => [
                    'format' => 'dd-MM-yyyy HH:mm:ss',
                    'autoclose' => true,
                    'todayBtn' => true,
                ]
            ])
            ->label(Yii::t('fe','Tanggal Keluar / Pindah Ruangan'));
        ?>
        
        <!-- Keterangan Meninggal -->
        <div class="keterangan-meninggal">
            <div  class="form-group required">
                <label for="no_surat_kematian" class="col-lg-4 control-label">
                    <?= Yii::t('fe', 'Nomor Surat Kematian'); ?>
                </label>
                <div class="col-lg-8">
                    <?= Html::activeTextInput($modelPasienPulang, 'no_surat_kematian',[
                        'class' => 'form-control'
                    ])?>
                    <div class="help-block"></div>
                </div>
            </div>
            <div>
                <?=$form->field($modelPasienPulang, 'status_jenazah')->textInput([
                    'class' => 'form-control'
                ])?>
            </div>
            <div>
                <?=$form->field($modelPasienPulang, 'tgl_kremasi')
                    ->widget(DateTimePicker::className(),[
                        'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                        'readonly' => true,
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'format' => 'dd-MM-yyyy HH:mm:ss',
                            'autoclose' => true,
                            'todayBtn' => true,
                        ]
                    ])
                    ->label(Yii::t('fe','Tanggal dimakamkan/dikremasi'));
                ?>
            </div>
            <div>
                <?=$form->field($modelPasienPulang, 'nama_pemeriksa_jenazah')->textInput([
                    'class' => 'form-control'
                ])?>
            </div>
            <div>
                <?=$form->field($modelPasienPulang, 'kelompok_kematian')->textInput([
                    'class' => 'form-control'
                ])?>
            </div>
            <div>
                <?=$form->field($modelPasienPulang, 'penyebab_langsung')->textInput([
                    'class' => 'form-control'
                ])?>
            </div>
            <div>
                <?=$form->field($modelPasienPulang, 'penyebab_antara')->textInput([
                    'class' => 'form-control'
                ])?>
            </div>
            <div>
                <?=$form->field($modelPasienPulang, 'penyebab_utama_bayi')->textInput([
                    'class' => 'form-control'
                ])?>
            </div>
            <div>
                <?=$form->field($modelPasienPulang, 'penyebab_utama_ibu')->textInput([
                    'class' => 'form-control'
                ])?>
            </div>
            <div>
                <?=$form->field($modelPasienPulang, 'pihak_menerima')->textInput([
                    'class' => 'form-control'
                ])?>
            </div>
        </div>
    </div>

    <div class='col-md-6 select2-md'>
        <?=
            $form->field($modelPasienPulang, 'kondisikeluar_id', [
                // 'labelOptions' => ['class' => 'text-right']
            ])->dropDownList($kondisi_keluar, [
                'options' => $kondisikeluar_options,
                'prompt' => '-- Pilih --',
                'class' => 'form-control select2',
                'disabled' => true,
                'style' => 'padding:9px!important;',
                'id' => 'kondisipulang_id'
                ])
            ->label(Yii::t('fe','Kondisi Pasien'));
        ?>
        <div class="hidden" id="catatantindakan-div">
            <?=$form->field($modelPasienPulang, 'catatan_tindakan')->textArea([
                'class' => 'form-control',
                'row' => 5
            ])?>
        </div>
        <div class="hidden" id="catatanlain-div">
            <?=$form->field($modelPasienPulang, 'catatan_lain')->textArea([
                'class' => 'form-control',
                'row' => 5
            ])?>
        </div>
        <div class="hidden" id="infeksi-div">
            <?=
            $form->field($modelPasienPulang, 'infeksi')->radioList([
                'inf' => 'Infeksi',
                'non_inf' => 'Non infeksi',
            ]);?>
        </div>

        <?=$form->field($modelPasienPulang, 'tgl_meninggal')
            ->widget(DateTimePicker::className(),[
                'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
                'readonly' => true,
                'convertFormat' => true,
                'pluginOptions' => [
                    'format' => 'dd-MM-yyyy HH:mm:ss',
                    'autoclose' => true,
                    'todayBtn' => true,
                ]
            ])
            ->label(Yii::t('fe','Tanggal Meninggal'));
        ?>
        <!-- Keterangan Meninggal -->
        <div class="keterangan-meninggal">
            <div>
                <?=$form->field($modelPasienPulang, 'tempat_kematian')->textInput([
                    'class' => 'form-control'
                ])?>
            </div>
            <div>
                <?=$form->field($modelPasienPulang, 'kualifikasi_pemeriksa')->textInput([
                    'class' => 'form-control'
                ])?>
            </div>
            <div>
                <?=$form->field($modelPasienPulang, 'waktu_pemeriksaan_jenazah')
                    ->widget(DateTimePicker::className(),[
                        'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
                        'readonly' => true,
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'format' => 'dd-MM-yyyy HH:mm:ss',
                            'autoclose' => true,
                            'todayBtn' => true,
                        ]
                    ])
                    ->label(Yii::t('fe','Waktu Pemeriksaan Jenazah'));
                ?>
            </div>
            <div>
                <?=$form->field($modelPasienPulang, 'dasar_diagnosis')->textInput([
                    'class' => 'form-control'
                ])?>
            </div>
            <div>
                <?=$form->field($modelPasienPulang, 'penyebab_dasar')->textInput([
                    'class' => 'form-control'
                ])?>
            </div>
            <div>
                <?=$form->field($modelPasienPulang, 'kondisi_lain')->textInput([
                    'class' => 'form-control'
                ])?>
            </div>
            <div>
                <?=$form->field($modelPasienPulang, 'penyebab_lain_bayi')->textInput([
                    'class' => 'form-control'
                ])?>
            </div>
            <div>
                <?=$form->field($modelPasienPulang, 'penyebab_lain_ibu')->textInput([
                    'class' => 'form-control'
                ])?>
            </div>
            <div>
                <?=$form->field($modelPasienPulang, 'hubungan_penerima')->textInput([
                    'class' => 'form-control'
                ])?>
            </div>
        </div>
        <?=
        $form->field($modelPasienPulang, 'pendaftaran_id')->hiddenInput(['class'=>'pendaftaran_id'])->label(false);
        ?>
        <?=
        $form->field($modelPasienPulang, 'pasien_id')->hiddenInput(['class'=>'pasien_id'])->label(false);
        ?>
        <?=
        $form->field($modelPasienPulang, 'ruanganakhir_id')->hiddenInput(['class'=>'ruanganakhir_id'])->label(false);
        ?>
        <?=Html::hiddenInput('PasienPulangForm[nosep]', $data_pasien["nosep"]);?>

    </div>


</div>
<div class="div_pulang_keluar">
<!-- pasien keluar -->
<?php
echo Yii::$app->controller->renderPartial('kesimpulan/_kesimpulan_keluar', [
    'modelKesimpulanKeluar' => $modelKesimpulanKeluar,
    'form' => $form,
    'data_gcsEye' => $data_gcsEye,
    'data_gcsVerbal' => $data_gcsVerbal,
    'data_gcsMotorik' => $data_gcsMotorik,
    'gcsEyeOptions' => $gcsEyeOptions,
    'gcsVerbalOptions' => $gcsVerbalOptions,
    'gcsMotorikOptions' => $gcsMotorikOptions,
    'data_metodegcs' => $resMaster['metodegcs'],
    'data_gcs' => $resMaster['gcs'],
    'data_listgcs' => $resListGcs,
    'inisialKeluar' => $inisialKeluar
    ]);
    ?>

<!-- pasien pulang -->
<?php
echo Yii::$app->controller->renderPartial('kesimpulan/_kesimpulan_pulang', [
    'modelKesimpulanPulang' => $modelKesimpulanPulang,
    'form' => $form,
    'inisialPulang' => $inisialPulang
]);
?>
<!-- obat dibawa pulang -->
<?php
echo Yii::$app->controller->renderPartial('kesimpulan/obat_dibawa_pulang', [
    'data_obat' => $data_obat,
]);
?>
</div>
<div class="panel panel-default hidden">
    <div class="panel-heading">
        <div id="head_panel_kesimpulan_obat" class="checkbox2 head_kesimpulan_obat form-group">
            <label class="control-label col-md-1">
                <?=Html::checkbox('checked_kesimpulan_obat',false,['class'=>'styled','id'=>'checked_kesimpulan_obat'])?>
                <span class="cr"><i class="cr-icon fa fa-check"></i></span>
            </label>
            <div class="col-md-11">
                <h5 class="panel-title"><?=Yii::t('fe','Obat Saat Pulang')?></h5>
            </div>
        </div>
    </div>

    <div id="col_kesimpulan_obat" class="panel-collapse collapse">
        <div class="panel-body">

            <div class="row form_obat_pulang">
                <div class='col-md-3'>
                    <div class="form-group required">
                        <label class="control-label col-sm-4">Dokter</label>
                        <div class="col-sm-8">
                            <?=Html::textInput('dokter_reseptur',$val_dokter_reseptur,['id'=>'dokter_reseptur','readonly'=>true,'class'=>'form-control'])?>
                            <?=$form->field($modelReseptur,'pegawai_id')->hiddenInput()->label(false)?>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <?=$form->field($modelReseptur,'tglreseptur')->textInput(['readonly'=>true])->label(Yii::t('fe', 'Tanggal Resep'));?>
                </div>
                <div class="col-md-3">
                    <?=$form->field($modelReseptur,'ruangan_id')->widget(Select2::classname(), [
                        'data' => $listDataApotek,
                        'options' => [
                            'id' => 'select_depo',
                            'class' => 'form-control input-sm select2',
                            'placeholder' => '-- Pilih Depo --',
                        ],
                    ])->label(Yii::t('fe', 'Depo Tujuan'));?>
                </div>
                <div class="col-md-3">
                    <?=$form->field($modelReseptur,'iter')->textInput([
                        'id' => 'reseptur_iter',
                        'class' => 'form-control input-sm docoNumberOnly',
                    ])->label(Yii::t('fe', 'Iterasi'));?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading collapsed" data-toggle="collapse" href="#collapse-nr" aria-expanded="false">
                            <h5 class="panel-title"><?=Yii::t('fe', 'Non racikan')?></h5>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse" data-toggle="collapse" href="#collapse-nr" class="collapsed" aria-expanded="false"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div id="collapse-nr" class="panel-collapse collapse in" aria-expanded="true" style="">
                            <div class="panel-body">
                                <div class="col-md-12 form_non_racikan">
                                    <div class="row">
                                        <div class="col-md-6">
                                        <?= $form->field($modelResepturDetailNonRacikan, 'obatalkes_id', [
                                                'labelOptions' => ['class' => 'text-right required']
                                            ])->widget(DepDrop::classname(), [
                                                'type' => DepDrop::TYPE_SELECT2,
                                                'data' => [],
                                                // 'data' => $isEditReseptur == true ? ArrayHelper::map($initObatAlkes, 'obatalkes_id', 'obatalkes_namalain') : [],
                                                'options' => [
                                                    'id' => 'depdrop_reseptur_nr',
                                                    'class' => 'form-control reseptur-reset newselect2'
                                                ],
                                                'pluginOptions' => [
                                                    'depends' => ['select_depo'],
                                                    'placeholder' => \Yii::t('fe', '-- Pilih Nama Obat --'),
                                                    'url' => Url::to(['/igd/pemeriksaan-igd/list-obat-alkes-depo'])
                                                ],
                                            ]
                                        ) ?>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="text-right control-label col-sm-4"><?= Yii::t('fe', 'Stok Tersedia') ?></label>
                                                <div class="col-md-6">
                                                    <?= Html::textInput('stok_sisa_nr', 0, ['id' => 'stok_sisa_nr',
                                                        'class' => 'form-control input-sm docoNumberOnly',
                                                        'readonly' => true
                                                    ]) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?=$form->field($modelResepturDetailNonRacikan, 'qty_reseptur', ['labelOptions' => ['class' => 'text-right required']])
                                                ->textInput([
                                                    'id' => 'qty_nonracikan_id',
                                                    'class' => 'form-control input-sm doco-decimal',
                                                    'maxlength' => '8',
                                                ]); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?=$form->field($modelResepturDetailNonRacikan, 'satuankecil_nama', ['labelOptions' => ['class' => 'text-right']])
                                                ->textInput([
                                                    'id' => 'satuan_reseptur_nr',
                                                    'class' => 'form-control input-sm field-satuankecil',
                                                    'readonly' => 'readonly',
                                                ])->label(Yii::t('fe', 'Satuan Kecil')); ?>
                                            <?=Html::hiddenInput('reseptur_non_racikan_satuankecil_id',null,['id'=>'reseptur_non_racikan_satuankecil_id'])?>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group highlight-addon has-size-sm field-harga_reseptur_nr_view">
                                                <label class="text-right control-label col-sm-4" for="harga_reseptur_nr_view">Harga Satuan</label>
                                                <div class="col-sm-8">
                                                    <input type="text" id="harga_reseptur_nr_view" class="form-control input-sm field-harga" name="ResepturNrDetailForm[hargasatuan_reseptur_view]" readonly="readonly">

                                                    <div class="help-block"></div>
                                                </div>
                                            </div>
                                            <?=$form->field($modelResepturDetailNonRacikan, 'hargasatuan_reseptur', ['labelOptions' => ['class' => 'text-right']])
                                                ->textInput([
                                                    'id' => 'harga_reseptur_nr',
                                                    'class' => 'form-control input-sm field-harga',
                                                    'readonly' => 'readonly',
                                                    'type' => 'hidden'
                                                ])->label(false);
                                            ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?= $form->field($modelResepturDetailNonRacikan, 'signa_reseptur', [
                                                    'labelOptions' => ['class' => 'text-right required']
                                                ])->widget(Select2::classname(), [
                                                    'data' => $listDataSigna,
                                                    'options' => [
                                                        'id' => 'signa_nonracikan_id',
                                                        'class' => 'form-control input-sm',
                                                        'prompt' => Yii::t('fe', '--Pilih signa--')
                                                    ],
                                                ]
                                            ) ?>
                                            <?= Html::activeHiddenInput($modelResepturDetailNonRacikan, 'satuankecil_id', ['id' => 'satuan_id_reseptur_nr']); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <div class="pull-right" style="margin-right:5px">
                                    <?= Html::button('<i class="fa fa-plus"></i> '.Yii::t('fe', 'Tambah'), [
                                        'class' => 'btn btn-success btn-md save-non-racikan',
                                        'value' => 'submit-nonracikan',
                                        'id' => 'save-non-racikan',
                                        'onclick' => 'submitNonracikan(this)',
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading collapsed" data-toggle="collapse" href="#collapse-r" aria-expanded="false">
                            <h5 class="panel-title"><?=Yii::t('fe', 'Racikan')?></h5>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse" data-toggle="collapse" href="#collapse-r" class="collapsed" aria-expanded="false"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div id="collapse-r" class="panel-collapse collapse in" aria-expanded="true" style="">
                            <div id="list_racikan" class="list_racikan panel-body">
                                <div class="col-md-12 form_racikan">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?=$form->field($modelResepturDetailRacikan, 'rke', ['labelOptions' => ['class' => 'text-right required']])
                                            ->textInput([
                                                'class' => 'form-control input-sm docoNumberOnly r-ke cek-racikan',
                                            ])->label(Yii::t('fe', 'R ke /')) ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?= $form->field($modelResepturDetailRacikan, 'signa_reseptur', [
                                                    'labelOptions' => ['class' => 'text-right required']
                                                ])->widget(Select2::classname(), [
                                                    'data' => $listDataSigna,
                                                    'options' => [
                                                        'class' => 'form-control input-sm cek-racikan',
                                                        'prompt' => Yii::t('fe', '--Pilih signa--')
                                                    ],
                                                ]
                                            ) ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 first_racikan group_input_racikan">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?= $form->field($modelResepturDetailRacikan, '[0]obatalkes_id', [
                                                    'labelOptions' => ['class' => 'text-right required']
                                                ])->widget(DepDrop::classname(), [
                                                    'type' => DepDrop::TYPE_SELECT2,
                                                    'data' => [],
                                                    // 'data' => $isEditReseptur == true ? ArrayHelper::map($initObatAlkes, 'obatalkes_id', 'obatalkes_namalain') : [],
                                                    'options' => [
                                                        'data-iteration' => 0,
                                                        'class' => 'form-control racikan_append newselect2 cek-racikan'
                                                    ],
                                                    'pluginOptions' => [
                                                        'depends' => ['select_depo'],
                                                        'placeholder' => \Yii::t('fe', '-- Pilih Nama Obat --'),
                                                        'url' => Url::to(['/igd/pemeriksaan-igd/list-obat-alkes-depo'])
                                                    ]
                                                ]
                                            ) ?>
                                            <?=Html::hiddenInput('ResepturDetailForm[0][obatalkes_nama]',false,['id'=>'reseptur_racikan_obatalkesnama','class'=>'field-obatalkesnama']); ?>
                                        </div>

                                        <div class="col-md-6">
                                            <?=$form->field($modelResepturDetailRacikan, '[0]qty_reseptur', ['labelOptions' => ['class' => 'text-right required']])
                                                ->textInput([
                                                    'id' => 'qty_reseptur-0',
                                                    'class' => 'form-control input-sm qty-reseptur doco-decimal cek-racikan',
                                                    'maxlength' => '8',
                                                ]); ?>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <?=$form->field($modelResepturDetailRacikan, '[0]satuankecil_nama', ['labelOptions' => ['class' => 'text-right']])
                                                ->textInput([
                                                    'id' => 'racikan_append_satuan_0',
                                                    'class' => 'form-control input-sm field-satuankecil',
                                                    'readonly' => 'readonly',
                                                ])->label(Yii::t('fe', 'Satuan Kecil')); ?>
                                            <?=Html::hiddenInput('ResepturDetailForm[0][satuankecil_id]',false,['id'=>'reseptur_racikan_satuankecil_id','class'=>'field-satuankecil_id']); ?>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label class="text-right control-label col-sm-5"><?= Yii::t('fe', 'Stok Tersedia') ?></label>
                                                <div class="col-sm-7">
                                                    <?= Html::textInput('ResepturDetailForm[0][stok_tersedia]', 0, [
                                                        'id' => 'stok_sisa_r_0',
                                                        'class' => 'form-control input-sm field-stok-sisa docoNumberOnly',
                                                        'readonly' => true
                                                    ]) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <?= Html::button('<i class="fa fa-plus"></i>', ['class' => 'btn btn-success', 'id' => 'btnAppendRacikan', 'onclick' => 'appendRacikan(this)']); ?>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group highlight-addon has-size-sm field-[0]hargasatuan_reseptur_view">
                                                <label class="text-right control-label col-sm-4" for="[0]hargasatuan_reseptur_view">Harga Satuan</label>
                                                <div class="col-sm-8">
                                                    <input type="text" id="racikan_append_harga_0_view" class="form-control input-sm field-harga_view" name="ResepturNrDetailForm[[0]hargasatuan_reseptur_view]" readonly="readonly">

                                                    <div class="help-block"></div>
                                                </div>
                                            </div>
                                            <?=$form->field($modelResepturDetailRacikan, '[0]hargasatuan_reseptur', ['labelOptions' => ['class' => 'text-right']])
                                                ->textInput([
                                                    'id' => 'racikan_append_harga_0',
                                                    'class' => 'form-control input-sm field-harga',
                                                    'readonly' => 'readonly',
                                                    'type'=> 'hidden'
                                                ])->label(false); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <div class="pull-right" style="margin-right:5px">
                                    <?= Html::button('<i class="fa fa-plus"></i> '.Yii::t('fe', 'Tambah'), [
                                        'class' => 'btn btn-success btn-md save-racikan',
                                        'value' => 'submit-racikan',
                                        'id' => 'save-racikan',
                                        'onclick' => 'submitRacikan(this)',
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">

                            <table id="tabel-reseptur-kesimpulan" class="table table-striped table-hover datatable-basic dataTable" style="width:100%;">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th>No</th>
                                        <th><?=Yii::t('fe', 'Racikan / non racikan')?></th>
                                        <th><?=Yii::t('fe', 'R ke-')?></th>
                                        <th><?=Yii::t('fe', 'Nama obat')?></th>
                                        <th><?=Yii::t('fe', 'Satuan kecil')?></th>
                                        <th><?=Yii::t('fe', 'Signa')?></th>
                                        <th><?=Yii::t('fe', 'Stok Tersedia')?></th>
                                        <th><?=Yii::t('fe', 'Qty')?></th>
                                        <th><?=Yii::t('fe', 'Harga satuan (Rp.)')?></th>
                                        <th><?=Yii::t('fe', 'Jumlah harga (Rp.)')?></th>
                                        <th><?=Yii::t('fe', 'Aksi')?></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- pasien pulang -->
<?php
echo Yii::$app->controller->renderPartial('kesimpulan/_kesimpulan_jenazah', [
    'modelJenazah' => $modelJenazah,
    'form' => $form,
    'listJk' => $listJk,
    'listHubungan' => $listHubungan,
    'id' => DocoHelpers::encrypt($id)
]);
?>
<div class="panel panel-default long-form" id="form_rujukan_pasien" hidden=true>
    <div class="panel-body">
        <div class="tabbable">
        </div>
    </div>
</div>
<div class="row">
    <hr>
</div>

<div class="row">
    <div class="col-md-12" style="margin-left: 5px">
        <button  type="button"
        id="save-kesimpulan"
        class="btn bg-teal"
        data-instruksi="<?= $statusInstruksiTindakan ?>"
        data-messages="<?= $messageInstruksiTindakan ?>"
        ><i class="fa fa-floppy-o"></i> Simpan</button>
        <?php
            $classBtnCetak = '';
            $classBtnCetakSuratKematian = '';
            if($is_hidden_cetak){
                $classBtnCetak = 'hidden';
            }
            if($is_hidden_cetak_surat_kematian){
                $classBtnCetakSuratKematian = 'hidden';
            }
        ?>
        <button type="button" id="cetak-kesimpulan" class="btn bg-teal <?=$classBtnCetak?>"><i class="fa fa-print"></i> Cetak</button>
        <button type="button" id="cetak-keterangan-meninggal" class="btn bg-teal  <?=$classBtnCetakSuratKematian?>"><i class="fa fa-print"></i> Cetak</button>
    </div>
</div>
<?php ActiveForm::end(); ?>

<?php
$this->registerJs($this->render('_js/index.js'), View::POS_END);
$this->registerJs("
    // $(document).ready(function () {
    //     $('#save-kesimpulan').prop('disabled',".$statusInstruksiTindakan.");
    // });

    var inisialKeluar = ".$inisialKeluar.";
    if(inisialKeluar == 1){
        $('#checked_kesimpulan_keluar').prop('checked',true);
        $('#col_kesimpulan_keluar').collapse('show');
    }
    var inisialPulang = ".$inisialPulang.";
    if(inisialPulang == 1){
        $('#checked_kesimpulan_pulang').prop('checked',true);
        $('#col_kesimpulan_pulang').collapse('show');
    }
    var konfig_keramat_spri = '".$konfig_keramat_spri."';
    var id_dpjp = ".$data_pasien['dokter_jaga_id'].";
    var nama_dpjp = '".$data_pasien['dokter_jaga']."';
", View::POS_END, 'jsbot');
?>

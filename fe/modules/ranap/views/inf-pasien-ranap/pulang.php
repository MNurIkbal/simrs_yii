<?php

/**
 * @author Sunarko
 * @Date 26/06/2018
**/

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\web\JsExpression;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\datetime\DateTimePicker;
use kartik\widgets\DatePicker;
use kartik\widgets\Select2;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style>
    .label-meninggal span{
        color : red;
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
                      <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                      <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
              </div>
              <!-- end -->
                <?php if($statusInstruksiTindakan): ?>
                     <!-- <div> -->
                    <h6 class="panel-title">
                        <span class='inf-pasien-title-nama'><strong>Nama Pasien : <?= $model->nama_pasien ?> </strong></span>
                        <small class='inf-pasien-title-norm'>No. Rekam Medis : <?= $model->no_rekam_medik ?> </small>
                        <span class="text-danger text-notifpasien" style="display:-webkit-inline-box;"><?= $messageInstruksiTindakan ?> </span>
                        <?= Html::hiddenInput('antrian_id', '', ['class'=>'antrian-id']); ?>
                        <?= Html::hiddenInput('pasien_id', '', ['class'=>'pasien-id']); ?>
                        <?= Html::hiddenInput('norm', '', ['id'=>'norm']); ?>
                        <a class="heading-elements-toggle"><i class="icon-more"></i></a>
                    </h6>
                    <!-- </div> -->
                <?php endif ?>

                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'simpan-data' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Simpan'),
                            'icon' => 'fa fa-floppy-o',
                            'method' => 'not exist',
                            'attributes' => [
                                'id' => 'btn-pasien-pulang',
                                'data-options' => 'click',
                                'class' => 'bg-teal data-simpan',
                                'disabled' => false
                            ]
                        ],
                        'custom-reset'=>[
                            'type' => 'click',
                            'title' => \Yii::t('fe', 'Muat Ulang'),
                            'icon' => 'fa fa-refresh',
                            'attributes' => [
                                'id' => 'btn-reset',
                            ]
                        ],
                        'kembali'=>[
                          'title' => \Yii::t('fe', 'Kembali'),
                          'icon' => 'fa fa-arrow-left',
                          'attributes' => [
                                'data-options' => 'link',
                                'data-target'=> '/ranap/inf-pasien-pulang',
                          ]
                        ],
                        // 'back',
                    ]);
                ?>
            </div>

            <?php
                $form = ActiveForm::begin([
                    'id'=>'pulang-kamar-form',
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['showErrors' => true,'labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL, 'enableAjaxValidation' => false,'enableClientValidation' => false],
                ]);
            ?>
            
            <div class="panel panel-white">
                <div class="panel-body">
                    <?=Yii::$app->controller->renderPartial('__patient', [
                        'data_pasien' => $data_pasien,
                        'modelPulang' => $modelPulang,
                        'daysLamaRawat' => $daysLamaRawat
                    ]);?>
                    <div>
                        <div class="col-md-6">
                            
                            <?php //$form->field($model, 'no_rekam_medik')->textInput(['readonly'=>true]); ?>
                            <?php //$form->field($model, 'tgl_pendaftaran')->textInput(['readonly'=>true]); ?>
                            <?php //$form->field($model, 'no_pendaftaran')->textInput(['readonly'=>true]); ?>
                            <?php //$form->field($model, 'nama_pasien')->textInput(['readonly'=>true]); ?>
                            <?= $form->field($modelPulang, 'carakeluar_id', [
                                'labelOptions' => ['class' => 'text-left']
                            ])->dropDownList($cara_keluar, [
                                'prompt' => '-- Pilih --', 
                                'class' => 'form-control select2 caraKeluar',  
                                'style' => 'padding:9px!important;', 
                                'id' => 'carakeluar_id'
                            ])->label('Cara Keluar'. Html::tag('span', '',['class'=>'required'])); ?>
                            <?=$form->field($modelPulang, 'kondisikeluar_id', [
                                'labelOptions' => ['class' => 'text-left']
                            ])->dropDownList($kondisi_keluar, [
                                'prompt' => '-- Pilih --', 
                                'class' => 'form-control select2',
                                'disabled' => true,  
                                'style' => 'padding:9px!important;', 
                                'id' => 'kondisikeluar_id'
                            ])->label('Kondisi Pulang'. Html::tag('span', '',['class'=>'required'])); ?>
                            <?= $form->field($modelPulang, 'penerima_pasien')->textInput(); ?>
                            <?= $form->field($modelPulang, 'keterangan_keluar')->textarea(['rows' => '3'],['class' => 'form-control']); ?>
                            
                            <?= $form->field($modelPulang, 'is_meninggal')->checkbox(['id'=>'is_meninggal'])->label(false); ?>
                                <div class="col-sm-offset-4" id="error_PasienPulangFormis_meninggal"></div>
                            <div class="form-group field-tgl_meninggal">
                                <label for="tgl_meninggal" class="col-sm-4 control-label label-meninggal">
                                    <?= Yii::t('fe', 'Tanggal Meninggal <span color="red">*</span>'); ?>
                                </label>
                                <div class="col-sm-8">
                                    <?=DateTimePicker::widget([
                                                'model' => $modelPulang,
                                                'attribute' => 'tgl_meninggal',
                                                'options' => [
                                                    'id' => 'tgl_meninggal',
                                                    'class' => 'form-control input-sm',
                                                    'disabled' => true
                                                ],
                                                'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                                                'readonly' => true,
                                                'convertFormat' => true,
                                                'pluginOptions' => [
                                                    'format' => 'dd-M-yyyy HH:mm:ss',
                                                    'autoclose' => true,
                                                    'todayBtn' => true,
                                                    'endDate' => date('Y-m-d H:i:s'),
                                                    'minuteStep' => 1,
                                                ]
                                            ]);
                                        ?>
                                </div>
                                <div  class="form-group required">
                                    <label for="no_surat_kematian" class="col-lg-4 control-label">
                                        <?= Yii::t('fe', 'Nomor Surat Kematian'); ?>
                                    </label>
                                    <div class="col-lg-8">
                                        <?= Html::activeTextInput($modelPulang, 'no_surat_kematian',[
                                            'class' => 'form-control'
                                        ])?>
                                        <div class="help-block"></div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div>
                                    <?=$form->field($modelPulang, 'status_jenazah')->textInput([
                                        'class' => 'form-control'
                                    ])?>
                                </div>
                                <div>
                                    <?=$form->field($modelPulang, 'tgl_kremasi')
                                        ->widget(DateTimePicker::className(),[
                                            'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                                            'readonly' => true,
                                            'convertFormat' => true,
                                            'pluginOptions' => [
                                                'format' => 'dd-MM-yyyy HH:mm:ss',
                                                'autoclose' => true,
                                                'todayBtn' => true,
                                                'minuteStep' => 1,
                                            ]
                                        ])
                                        ->label(Yii::t('fe','Tanggal dimakamkan/dikremasi')); 
                                    ?>
                                </div>
                                <div>
                                    <?=$form->field($modelPulang, 'nama_pemeriksa_jenazah')->textInput([
                                        'class' => 'form-control'
                                    ])?>
                                </div>
                                <div>
                                    <?=$form->field($modelPulang, 'kelompok_kematian')->textInput([
                                        'class' => 'form-control'
                                    ])?>
                                </div>
                                <div>
                                    <?=$form->field($modelPulang, 'penyebab_langsung')->textInput([
                                        'class' => 'form-control'
                                    ])?>
                                </div>
                                <div>
                                    <?=$form->field($modelPulang, 'penyebab_antara')->textInput([
                                        'class' => 'form-control'
                                    ])?>
                                </div>
                                <div>
                                    <?=$form->field($modelPulang, 'penyebab_utama_bayi')->textInput([
                                        'class' => 'form-control'
                                    ])?>
                                </div>
                                <div>
                                    <?=$form->field($modelPulang, 'penyebab_utama_ibu')->textInput([
                                        'class' => 'form-control'
                                    ])?>
                                </div>
                                <div>
                                    <?=$form->field($modelPulang, 'pihak_menerima')->textInput([
                                        'class' => 'form-control'
                                    ])?>
                                </div>
                            </div>
                            <?= $form->field($modelPulang,'is_pelayanan_jenazah',['wrapperOptions'=>['id'=>'container_is_pelayanan_jenazah']])->checkbox(['id'=>'is_pelayanan_jenazah'])->label(false); ?>
                            <?= $form->field($modelPulang, 'pasiendirujukkeluar_id')->checkbox(['id'=>'pasiendirujukkeluar_id'])->label(false); ?>
                            <div class="col-sm-offset-4" id="error_PasienPulangFormpasiendirujukkeluar_id"></div>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($modelPulang, 'is_rencanakontrol')->checkbox(['class' => 'form-control', 'id'=>'is_rencanakontrol'])->label('Rencana Kontrol'); ?>
                            <?= $form->field($modelPulang, 'tgl_rencanakontrol', [
                                'labelOptions' => ['class' => 'text-left'],
                                'addon' => ['append' => ['content' => '<i class="fa fa-calendar"></i>']],
                            ])->textInput([
                                'class' => 'form-control input-sm datepickNext',
                                'disabled'=>true ,
                                'id' => 'tgl_rencanakontrol',
                                'placeholder' => Yii::t('fe', $modelPulang->getAttributeLabel('tgl_rencanakontrol'))
                            ])->label('Tanggal Rencana Kontrol'); ?>
                            <div class="col-sm-offset-4" id="error_PasienPulangFormis_rencanakontrol"></div>
                            <?= $form->field($modelPulang, 'nama_spesialis', [
                                'horizontalCssClasses' => [
                                    'label' => 'col-md-4',
                                    'wrapper' => 'col-md-8'
                                ],
                                'addon' => [
                                    'append' => [
                                        'content' => Html::a('<i class="fa fa-hospital-o "></i>',null, [
                                            'data-toggle' => 'modal',
                                            'data-target' => '#modal_pencarian_spesialis',
                                            'data-width' => '1000px',
                                            'data-popup' => "tooltip",
                                            'id' => 'btn-pencarian-spesialis',
                                            'action' =>'/pendaftaran/rencana-kontrol-inap/modal-pencarian-spesialis',
                                            'title' => Yii::t("fe","Pencarian Spesialis")
                                        ])
                                    ]
                                ]
                            ])->textInput([
                                'id' => 'nama_spesialis',
                                'class' => 'form-control',
                                'placeholder' => $modelPulang->getAttributeLabel('nama_spesialis'),
                                'readonly' => true
                            ])->label($modelPulang->getAttributeLabel('nama_spesialis')) ?>
                            <?= $form->field($modelPulang, 'dokterdpjp_nama')->textInput([
                                'id' => 'dokterdpjp_nama',
                                'class' => 'form-control',
                                'placeholder' => $modelPulang->getAttributeLabel('dokterdpjp_nama'),
                                'readonly' => true
                            ])->label($modelPulang->getAttributeLabel('dokterdpjp_nama')) ?>
                            <?= Html::activeHiddenInput($modelPulang, 'dokterdpjp_kode', ['id' => 'dokterdpjp_kode']); ?>
                            <?= Html::activeHiddenInput($modelPulang, 'kode_poli', ['id' => 'kode_poli']); ?>
                            <?= Html::activeHiddenInput($modelPulang, 'no_kartu', ['id' => 'no_kartu']); ?>
                            
                            <?php //$form->field($model, 'jenis_kelamin')->textInput(['readonly'=>true]); ?>
                            <?php //$form->field($model, 'tanggal_lahir')->textInput(['readonly'=>true])->label('Tanggal Lahir'); ?>
                            <?php //$form->field($model, 'umur')->textInput(['readonly'=>true])->label('Umur'); ?>
                            <?php //$form->field($model, 'jeniskasuspenyakit_nama')->textInput(['readonly'=>true])->label('Kasus Penyakit'); ?>
                            <input type="hidden" name="tgl_admisi_x" id="tgl_admisi_x" value="<?php echo $tgl_admisi_x ?>">
                            <input type="hidden" name="tglpasienpulang_x" id="tglpasienpulang_x" value="<?php echo $tglpasienpulang_x ?>">
                            <?php
                                // echo $form->field($model, 'tgl_admisi', [
                                // 'addon' => [
                                //     'append' => [
                                //         ['content' => '<i class="fa fa-calendar"></i>'],
                                //     ],
                                // ] ])->textInput([
                                //     'class' => 'datetime',
                                //     'disabled' => true,
                                //     'autocomplete' => "off",
                                //     'readonly' => true])->label('Tanggal Masuk Kamar');
                            ?>
                            <?php
                                // echo $form->field($modelPulang, 'tglpasienpulang', [
                                // 'addon' => [
                                //     'append' => [
                                //         ['content' => '<i class="fa fa-calendar"></i>'],
                                //     ],
                                // ] ])->textInput([
                                //     'class' => 'datetime',
                                //     'disabled' => true,
                                //     'autocomplete' => "off",
                                //     'readonly' => true])->label('Tanggal Keluar Kamar');
                            ?>
                            
                            <!-- <?=$form->field($modelPulang, 'lama_rawat', [
                                'labelOptions' => ['class' => 'text-left'],
                                'addon' => ['append' => ['content' => Yii::t('fe', 'Hari')]],
                            ])->textInput([
                                'class' => 'form-control input-sm docoNumberOnly',
                                'readonly'=> true,
                                'value'=>$daysLamaRawat,
                                'placeholder' => Yii::t('fe', $modelPulang->getAttributeLabel('lama_rawat'))
                            ]); ?> -->

                            <!-- <?=$form->field($modelPulang, 'hari_perawatan', [
                                'labelOptions' => ['class' => 'text-left'],
                                'addon' => ['append' => ['content' => Yii::t('fe', 'Hari')]],
                            ])->textInput([
                                'class' => 'form-control input-sm docoNumberOnly',
                                'placeholder' => Yii::t('fe', $modelPulang->getAttributeLabel('hari_perawatan'))
                            ]); ?> -->
                            <?= $form->field($model, 'pendaftaran_id')->hiddenInput()->label(false);?>
                            <?= $form->field($model, 'pasienadmisi_id')->hiddenInput()->label(false);?>
                            <?= $form->field($modelPulang, 'pasien_id')->hiddenInput()->label(false);?>
                            <?=Html::hiddenInput('PasienPulangForm[nosep]', $data_pasien["nosep"]);?>
                            
                            <div class="form-group field-tgl_meninggal">
                                <div>
                                    <?=$form->field($modelPulang, 'tempat_kematian')->textInput([
                                        'class' => 'form-control'
                                    ])?>
                                </div>
                                <div>
                                    <?=$form->field($modelPulang, 'kualifikasi_pemeriksa')->textInput([
                                        'class' => 'form-control'
                                    ])?>
                                </div>
                                <div>
                                    <?=$form->field($modelPulang, 'waktu_pemeriksaan_jenazah')
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
                                    <?=$form->field($modelPulang, 'dasar_diagnosis')->textInput([
                                        'class' => 'form-control'
                                    ])?>
                                </div>
                                <div>
                                    <?=$form->field($modelPulang, 'penyebab_dasar')->textInput([
                                        'class' => 'form-control'
                                    ])?>
                                </div>
                                <div>
                                    <?=$form->field($modelPulang, 'kondisi_lain')->textInput([
                                        'class' => 'form-control'
                                    ])?>
                                </div>
                                <div>
                                    <?=$form->field($modelPulang, 'penyebab_lain_bayi')->textInput([
                                        'class' => 'form-control'
                                    ])?>
                                </div>
                                <div>
                                    <?=$form->field($modelPulang, 'penyebab_lain_ibu')->textInput([
                                        'class' => 'form-control'
                                    ])?>
                                </div>
                                <div>
                                    <?=$form->field($modelPulang, 'hubungan_penerima')->textInput([
                                        'class' => 'form-control'
                                    ])?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-white" id="display_hidden" hidden=true>
                <div class="panel-body">
                    <div><br>
                        <div class="col-md-6">
                            <?=$form->field($modelPulang, 'tgldirujuk', [
                                'labelOptions' => ['class' => 'text-left'],
                                'addon' => ['append' => ['content' => '<i class="fa fa-calendar"></i>']],
                            ])->textInput([
                                'class' => 'form-control input-sm datepick',
                                'id' => 'tgldirujuk',
                            ])->label('Tanggal Dirujuk'. Html::tag('span', '',['class'=>'required'])); ?>
                            
                            <?=$form->field($modelPulang, 'pegawai_id', [
                                'labelOptions' => ['class' => 'text-left']
                            ])->dropDownList($pegawai, [
                                'prompt' => '-- Pilih --', 
                                'class' => 'form-control select2',  
                                'style' => 'padding:9px!important;', 
                                'id' => 'pegawai_id'
                            ])->label('Dokter'. Html::tag('span', '',['class'=>'required'])); ?>
                            
                            <?=$form->field($modelPulang, 'rujukankeluar_id', [
                                'labelOptions' => ['class' => 'text-left']
                            ])->dropDownList($rujukan_keluar, [
                                'prompt' => '-- Pilih --', 
                                'class' => 'form-control select2',  
                                'style' => 'padding:9px!important;', 
                                'id' => 'rujukankeluar_id'
                            ])->label('Rujukan Keluar'. Html::tag('span', '',['class'=>'required'])); ?>
                            <?= $form->field($modelPulang, 'nosuratrujukan')->textInput()->label('No. Surat Rujukan'. Html::tag('span', '',['class'=>'required'])); ?>
                            <?= $form->field($model, 'ruangan_nama')->textInput(['readonly'=>true])->label('Ruangan Asal'); ?>
                            <!-- <?=$form->field($modelPulang, 'ruanganasal_id', [
                                'labelOptions' => ['class' => 'text-left']
                            ])->dropDownList($ruangan_asal, [
                                'prompt' => '-- Pilih --', 
                                'class' => 'form-control select2',
                                'disabled' => true,
                                'style' => 'padding:9px!important;',
                                'id' => 'ruanganasal_id'
                            ]); ?> -->
                            <?= $form->field($modelPulang, 'ruanganasal_id')->hiddenInput()->label(false);?>
                            <?= $form->field($modelPulang, 'catatandokterperujuk')->textarea(['rows' => '3'],['class' => 'form-control']); ?>
                            <?= $form->field($modelPulang, 'alasandirujuk')->textarea(['rows' => '3'],['class' => 'form-control']); ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($modelPulang, 'hasilpemeriksaan_ruj')->textarea(['rows' => '3'],['class' => 'form-control']); ?>
                            <?= $form->field($modelPulang, 'diagnosasementara_ruj')->textarea(['rows' => '3'],['class' => 'form-control']); ?>
                            <?= $form->field($modelPulang, 'pengobatan_ruj')->textarea(['rows' => '3'],['class' => 'form-control']); ?>
                            <?= $form->field($modelPulang, 'lainlain_ruj')->textarea(['rows' => '3'],['class' => 'form-control']); ?>
                           
                        </div>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end() ?>

            <?php
                $form = ActiveForm::begin([
                    'id'=>'pelayanan-jenazah-form',
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['showErrors' => true,'labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL, 'enableAjaxValidation' => false,'enableClientValidation' => false],
                ]);
            ?>
            <div class="panel panel-white" id="display_hidden_jenazah" hidden=true>
                <div class="panel-body">
                    <div class="tabbable">
                        <ul class="nav nav-tabs nav-tabs-highlight nav-justified">
                            <li class="active"><a href="#highlighted-justified-tab1" data-toggle="tab">Kondisi Pasien & Penanggung Jawab</a></li>
                            <li><a href="#highlighted-justified-tab2" data-toggle="tab">Obat & Tindakan</a></li>
                            <li><a href="#highlighted-justified-tab3" data-toggle="tab">Linen & Alat yang masih melekat</a></li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active" id="highlighted-justified-tab1">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?=\Yii::t('fe', 'Kondisi Pasien')?></label>
                                            <div class="col-md-6">
                                                <?=Html::activeTextArea($mPelayananJenazah, 'kondisi_pasien', ['class'=> 'form-control', 'rows' => 3])?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group required">
                                            <label class="control-label col-md-4"><?=\Yii::t('fe', 'Nama Penanggung Jawab')?></label>
                                            <div class="col-md-5">
                                                <?=Html::activeTextInput($mPelayananJenazah, 'nama_pj', ['class'=> 'form-control'])?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group required">
                                            <label class="control-label col-md-2"><?=\Yii::t('fe', 'Jenis Kelamin')?></label>
                                            <div class="col-md-5">
                                                <?= $form->field($mPelayananJenazah, 'jenis_kelamin',[
                                                    'template' => '{input}'
                                                    ])->radioList($jeniskelamin,['inline'=>true])->label(false) ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-offset-2" id="error_PelayananJenazahFormjenis_kelamin"></div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group required">
                                            <label class="control-label col-md-4"><?=\Yii::t('fe', 'Umur (Thn)')?></label>
                                            <div class="col-md-5">
                                                <?=Html::activeTextInput($mPelayananJenazah, 'umur', ['class'=> 'docoNumberOnly form-control'])?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group required">
                                            <label class="control-label col-md-2"><?=\Yii::t('fe', 'No Telp \ Hp')?></label>
                                            <div class="col-md-5">
                                                <?=Html::activeTextInput($mPelayananJenazah, 'no_telp', ['class'=> 'docoNumberOnly form-control'])?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group required">
                                            <label class="control-label col-md-4"><?=\Yii::t('fe', 'Hubungan Keluarga')?></label>
                                            <div class="col-md-5">
                                                <?=Html::activeDropdownlist($mPelayananJenazah,'hub_keluarga', $hubkeluarga, ['class'=> 'form-control select2', 'prompt' => \Yii::t('fe', '-- Pilih --')])?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?=\Yii::t('fe', 'Alamat')?></label>
                                            <div class="col-md-6">
                                                <?=Html::activeTextArea($mPelayananJenazah, 'alamat', ['class'=> 'form-control', 'rows' => 3])?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="tab-pane" id="highlighted-justified-tab2">
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <form id="form-tindakan">
                                                <div class="col-md-4">
                                                    <div class="form-group required">
                                                        <label class="control-label col-md-3"><?=\Yii::t('fe', 'Tindakan')?></label>
                                                        <div class="col-md-8">
                                <?php 
                                echo Select2::widget([
                                    'name' => 'tindakan_jenazah',
                                    'options' => [
                                        'id' => 'tindakan_jenazah',
                                        'placeholder' => 'Cari Tindakan'],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'minimumInputLength' => 0,
                                        'ajax' => [
                                            'url' => "cari-tindakan-jenazah",
                                            'dataType' => 'json',
                                            'delay' => 250,
                                            'data' => new JsExpression('function (params) {
                                                var query = {
                                                    term: params.term,
                                                    page: params.page || 1,
                                                    id:pendaftaranId
                                                  }
                                                  return query; 
                                                }'),
                                            'processResults' => new JsExpression('
                                                function (data, params) {
                                                    params.page = params.page || 1;

                                                    return {
                                                    results: data.result,
                                                        pagination: {
                                                            more: data.pagination
                                                                    }
                                                            };
                                                    }'),
                                                        ],
                                                ],
                                            ]);?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group required">
                                                        <label class="control-label col-md-3"><?=\Yii::t('fe', 'Qty')?></label>
                                                        <div class="col-md-6">
                                                            <?=Html::textInput('tindakan_qty', '', ['id'=>'tindakan_qty','class'=> 'form-control doco-number'])?>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <a class="btn btn-success btn-sm" id="btn-add-tindakan-jenazah"><i class="fa fa-plus fa-sm"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="row">
                                            <table class="table table-striped table-hover datatable-basic dataTable" style="width:100%;" id="tabel-tindakan">
                                                <thead>
                                                    <tr>
                                                        <th><?=Yii::t('fe', 'No')?></th>
                                                        <th><?=Yii::t('fe', 'Nama Tindakan')?></th>
                                                        <th><?=Yii::t('fe', 'Qty')?></th>
                                                        <th><?=Yii::t('fe', 'Harga')?></th>
                                                        <th><?=Yii::t('fe', 'Aksi')?></th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <br><hr>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <form id="form-obat">
                                                <div class="col-md-4">
                                                    <div class="form-group required">
                                                        <label class="control-label col-md-2"><?=\Yii::t('fe', 'Obat')?></label>
                                                        <div class="col-md-8"> 
                                    <?php echo Select2::widget([
                                        'name' => 'obat-jenazah',
                                        'options' => ['id'=>'obat_jenazah','placeholder' => 'Cari Obat'],
                                        'pluginOptions' => [
                                            'allowClear' => true,
                                            'minimumInputLength' => 0,
                                            'ajax' => [
                                                'url' => "cari-obat-jenazah",
                                                'dataType' => 'json',
                                                'delay' => 250,
                                                'data' => new JsExpression('function (params) {
                                                    var query = {
                                                        term: params.term,
                                                        page: params.page || 1,
                                                        id:pendaftaranId
                                                      }
                                                      return query; 
                                                    }'),
                                                'processResults' => new JsExpression('
                                                    function (data, params) {
                                                        dataobat = data.dataObat;
                                                        params.page = params.page || 1;

                                                    return {
                                                    results: data.result,
                                                        pagination: {
                                                            more: data.pagination
                                                                    }
                                                            };
                                                    }'),
                                                    ],
                                            ],
                                        ]);?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group required">
                                                        <label class="control-label col-md-3"><?=\Yii::t('fe', 'Qty')?></label>
                                                        <div class="col-md-6">
                                                            <?=Html::textInput('qty_obat', '', [
                                                                'id'=>'qty_obat' ,'class'=> 'form-control doco-number'])?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3"><?=\Yii::t('fe', 'Satuan')?></label>
                                                        <div class="col-md-6">
                                                            <?php
                                                            echo DepDrop::widget([
                                                                'name' => 'satuan_obat',
                                                                'options' => ['id'=>'satuan_obat','class'=>'select2'],
                                                                'pluginOptions' => [
                                                                    'depends' => ['obat_jenazah'],
                                                                    'url'=>'set-satuan-obat-jenazah'
                                                                ]
                                                            ]);
                                                            ?>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <a class="btn btn-success btn-sm" id="btn-add-obat-jenazah"><i class="fa fa-plus fa-sm"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="row">
                                            <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="tabel-obat" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th><?=Yii::t('fe', 'No')?></th>
                                                        <th><?=Yii::t('fe', 'Nama Obat Alkes')?></th>
                                                        <th><?=Yii::t('fe', 'Qty')?></th>
                                                        <th><?=Yii::t('fe', 'Satuan')?></th>
                                                        <th><?=Yii::t('fe', 'Harga')?></th>
                                                        <th><?=Yii::t('fe', 'Aksi')?></th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="tab-pane" id="highlighted-justified-tab3">
                                <br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <form id="form-linen">
                                                <div class="col-md-4">
                                                    <div class="form-group required">
                                                        <label class="control-label col-md-2"><?=\Yii::t('fe', 'Linen')?></label>
                                                        <div class="col-md-8">
                                            <?php echo Select2::widget([
                                                'name' => 'linen-jenazah',
                                                'options' => [
                                                    'id' => 'linen_jenazah',
                                                    'placeholder' => 'Cari Linen'
                                                ],
                                                'pluginOptions' => [
                                                    'allowClear' => true,
                                                    'minimumInputLength' => 0,
                                                    'ajax' => [
                                                        'url' => "cari-linen-jenazah",
                                                        'dataType' => 'json',
                                                        'delay' => 250,
                                                        'data' => new JsExpression('function (params) {
                                                              var query = {
                                                                search: params,
                                                              }
                                                              return params; 
                                                            }'),
                                                'processResults' => new JsExpression('
                                                    function (data, params) {
                                                        params.page = params.page || 1;

                                                        return {
                                                        results: data.result,
                                                            pagination: {
                                                                more: data.pagination
                                                                        }
                                                                };
                                                        }'),
                                                    ],
                                                ],
                                            ]);?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group required">
                                                        <label class="control-label col-md-3"><?=\Yii::t('fe', 'Qty')?></label>
                                                        <div class="col-md-6">
                                                            <?=Html::textInput('qty_linen', '', [
                                                                'id' => 'qty_linen',
                                                                'class'=> 'form-control doco-number'])?>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <a class="btn btn-success btn-sm" id="btn-add-linen-jenazah"><i class="fa fa-plus fa-sm"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="row">
                                            <table class="table table-striped table-hover datatable-basic dataTable" style="width:100%;" id="tabel-linen">
                                                <thead>
                                                    <tr>
                                                        <th><?=Yii::t('fe', 'No')?></th>
                                                        <th><?=Yii::t('fe', 'Nama Linen')?></th>
                                                        <th><?=Yii::t('fe', 'Qty')?></th>
                                                        <th><?=Yii::t('fe', 'Aksi')?></th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div><br><hr>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <form id="form-alat">
                                                <div class="col-md-4">
                                                    <div class="form-group required">
                                                        <label class="control-label col-md-5"><?=\Yii::t('fe', 'Alat yang masih terpasang')?></label>
                                                        <div class="col-md-7">
                                            <?php echo Select2::widget([
                                                'name' => 'alat-jenazah',
                                                'options' => [
                                                    'id'=>'alat_jenazah',
                                                    'placeholder' => 'Cari Alat Obat'
                                                ],
                                                'pluginOptions' => [
                                                    'allowClear' => true,
                                                    'minimumInputLength' => 0,
                                                    'ajax' => [
                                                        'url' => "cari-alat-jenazah",
                                                        'dataType' => 'json',
                                                        'delay' => 250,
                                                        'data' => new JsExpression('function (params) {
                                                              var query = {
                                                                search: params,
                                                              }
                                                              return params; 
                                                            }'),
                                                                        
                                                'processResults' => new JsExpression('
                                                    function (data, params) {
                                                        params.page = params.page || 1;

                                                        return {
                                                        results: data.result,
                                                            pagination: {
                                                                more: data.pagination
                                                                        }
                                                                };
                                                        }'),
                                                        ],
                                                    ],
                                                ]);?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group required">
                                                        <label class="control-label col-md-3"><?=\Yii::t('fe', 'Qty')?></label>
                                                        <div class="col-md-6">
                                                            <?=Html::textInput('qty_alat', '', [
                                                                'id' => 'qty_alat',
                                                                'class'=> 'form-control doco-number'])?>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <a class="btn btn-success btn-sm" id="btn-add-alat-jenazah"><i class="fa fa-plus fa-sm"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="row">
                                            <table class="table table-striped table-hover datatable-basic dataTable" style="width:100%;" id="tabel-alat">
                                                <thead>
                                                    <tr>
                                                        <th><?=Yii::t('fe', 'No')?></th>
                                                        <th><?=Yii::t('fe', 'Nama Alat')?></th>
                                                        <th><?=Yii::t('fe', 'Qty')?></th>
                                                        <th><?=Yii::t('fe', 'Aksi')?></th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div><br><hr>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end() ?>
            
            <div class="panel panel-default long-form" id="form_rujukan_pasien" hidden=true>
                <div class="panel-body">
                    <div class="tabbable">
                    </div>
                </div>
            </div>

            <div id="modal_pencarian_spesialis" class="modal">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content"></div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
    $this->registerJs('
        var pendaftaranId = "'.$id.'";
        var pasienadmisiId = "'.$modelPulang->pasien_id.'";
        var table_jenazah_tindakan;
        var table_jenazah_obat;
        var table_jenazah_linen;
        var table_jenazah_alat;
        var dataobat = [];

        var rencanaKontrol  = {};
        rencanaKontrol.jnsKontrol = 1;
        var noKartu = "'.$no_identitas_pasien.'";
        $("#no_kartu").val(noKartu);

        function pilihDpjp(identifier) {
            const kode_poli = $(identifier).data("kode_poli");
            const nama_spesialis = $(identifier).data("nama_spesialis");
            const dokterdpjp_kode = $(identifier).data("dokterdpjp_kode");
            const dokterdpjp_nama = $(identifier).data("dokterdpjp_nama");

            $("#kode_poli").val(kode_poli);
            $("#nama_spesialis").val(nama_spesialis);
            $("#dokterdpjp_kode").val(dokterdpjp_kode);
            $("#dokterdpjp_nama").val(dokterdpjp_nama);

            $("#modal_pencarian_spesialis").modal("toggle");
        }

    ', View::POS_END);
    $this->registerJs($this->render('pasien_pulang.js'));
?>

<?php

/**
 * @author Johndoe
 * @description UI Pendaftaran Ranap
**/

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                <?php echo Breadcrumbs::widget([
                      'homeLink' => [ 
                                      'label' => Yii::t('fe', 'Home'),
                                      'url' => Yii::$app->homeUrl,
                                 ],
                      'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                   ]); 
                ?>

                <div class="heading-elements">
                    <ul class="icons-list">
                        <li>
                            <button 
                                type="button" class="btn btn-primary" 
                                action="/pendaftaran/daftar/pilih-antrian" 
                                data-width='900px'
                                data-toggle="modal" data-target="#modal_backdrop"
                            >
                                Panggil Antrian
                            </button>

                            <?= Html::hiddenInput('loket', $active_workspace['loket'][0]['loket_nama'], ['id'=>'loket']); ?>
                        </li>
                        <li class="hidden"><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            
            <div class="panel-body" style="padding:10px;">
            <?php 
                $form = ActiveForm::begin([
                    'id' => 'form-daftar-ranap', 
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['showErrors' => true, 'labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                ]); 

                echo $form->field($modelRanap, 'kamarruangan_id')->hiddenInput(['id' => 'kamarruangan_id'])->label(false);
                echo $form->field($modelRanap, 'kamartempattidur_id')->hiddenInput(['id' => 'kamartempattidur_id'])->label(false);
                echo $form->field($model, 'instalasi_id')->hiddenInput(['id' => 'instalasi_id'])->label(false);
                echo $form->field($model, 'pendaftaran_id')->hiddenInput(['id' => 'pendaftaran_id'])->label(false);
                echo $form->field($model, 'tgl_pendaftaran')->hiddenInput(['id' => 'tgl_pendaftaran'])->label(false);
                echo $form->field($model, 'pasien_id')->hiddenInput(['id' => 'pasien_id'])->label(false);
                echo $form->field($model, 'golonganumur_id')->hiddenInput(['id' => 'golonganumur_id'])->label(false);
                echo $form->field($model, 'status_periksa')->hiddenInput(['id' => 'status_periksa'])->label(false);
                echo $form->field($model, 'status_pasien')->hiddenInput(['id' => 'status_pasien'])->label(false);
                echo $form->field($model, 'status_masuk')->hiddenInput(['id' => 'status_masuk'])->label(false);
                echo $form->field($model, 'kunjungan')->hiddenInput(['id' => 'kunjungan'])->label(false);
                echo Html::hiddenInput('kelaspelayanan_id_hidden', '', ['id' => 'kelaspelayanan_id_hidden']);
            ?>
            <fieldset class="content-group">
                <legend class="text-bold">Data Pasien</legend>
                <div class="row">
                    <div class="col-md-4">
                        <?php
                        echo $form->field($modelPasien, 'no_rekam_medik', [
                            
                        ])->widget(Select2::classname(), [
                            'options' => [
                                'id' => 'no_rekam_medik',
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 3,
                                'language' => [
                                    'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                                ],
                                'ajax' => [
                                    'url' => \yii\helpers\Url::to(['/pendaftaran/referensi/norm']),
                                    'dataType' => 'json',
                                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                ],
                                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                                'templateResult' => new JsExpression(
                                    'function(no_rekam_medik) { 
                                        return no_rekam_medik.text; 
                                    }'
                                ),
                                'templateSelection' => new JsExpression(
                                    'function (no_rekam_medik) {
                                        return no_rekam_medik.text; 
                                    }'
                                ),
                            ],
                            'pluginEvents' => [
                                'change' => 'function() {
                                    var data_id = $(this).val();
                                    getInfoPasien(data_id);
                                }'
                            ],
                        ]);
                        ?>
                    </div>
                </div>

                <div class='panel panel-flat inf-pasien' style='display: none;'>
                    <div class="panel-heading">
                        <h6 class="panel-title"><span class='inf-pasien-title-nama'><strong>Tn. Nama Pasien</strong></span> 
                            <small class='inf-pasien-title-norm'>No. Rekam Medis : 0000001</small>
                            <a class="heading-elements-toggle"><i class="icon-more"></i></a>
                        </h6>
                        <div class="heading-elements">
                            <ul class="icons-list">
                                <li><a data-action="collapse"></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="panel-toolbar clearfix">
                        <?=DocoHelpers::generateToolbar([
                            'update_pasien' => [
                                'type' => 'button',
                                'title' => \Yii::t('fe', 'Ubah'),
                                'icon' => 'fa fa-pencil',
                                'attributes' => [
                                    'class' => 'btn-pasien-ubah',
                                    'data-options'=>'click',
                                ] 
                            ],
                            // 'detail',
                        ]);?>
                    </div>
                    <div class="panel-body">
                        <div class='col-md-4 col-sm-12'>
                            <dl class="dl-horizontal">
                                <dt><?= Yii::t('fe', 'No identitas'); ?></dt>
                                <dd id='inf-pasien-noidentitas'>lorem ipsum</dd>
                                <dt><?= Yii::t('fe', 'Nama pasien'); ?></dt>
                                <dd id='inf-pasien-namapasien'>lorem ipsum</dd>
                                <dt><?= Yii::t('fe', 'Nama panggilan'); ?></dt>
                                <dd id='inf-pasien-namapanggilan'>lorem ipsum</dd>
                                <dt><?= Yii::t('fe', 'Tempat lahir'); ?></dt>
                                <dd id='inf-pasien-tempatlahir'>lorem ipsum</dd>
                                <dt><?= Yii::t('fe', 'Tanggal lahir'); ?></dt>
                                <dd id='inf-pasien-tanggallahir'>lorem ipsum</dd>
                                <dt><?= Yii::t('fe', 'Umur'); ?></dt>
                                <dd id='inf-pasien-umur'>lorem ipsum</dd>
                                <dt><?= Yii::t('fe', 'Jenis kelamin'); ?></dt>
                                <dd id='inf-pasien-jeniskelamin'>lorem ipsum</dd>
                                <dt><?= Yii::t('fe', 'Status perkawinan'); ?></dt>
                                <dd id='inf-pasien-statusperkawinan'>lorem ipsum</dd>
                                <dt><?= Yii::t('fe', 'Nama ibu'); ?></dt>
                                <dd id='inf-pasien-namaibu'>lorem ipsum</dd>
                                <dt><?= Yii::t('fe', 'Nama ayah'); ?></dt>
                                <dd id='inf-pasien-namaayah'>lorem ipsum</dd>
                                <dt><?= Yii::t('fe', 'Anak ke'); ?></dt>
                                <dd id='inf-pasien-anakke'>lorem ipsum</dd>
                                <dt><?= Yii::t('fe', 'Jumlah bersaudara'); ?></dt>
                                <dd id='inf-pasien-jumlahbersaudara'>lorem ipsum</dd>
                            </dl>
                        </div>
                        <div class='col-md-4 col-sm-12'>
                            <dl class="dl-horizontal">
                                <dt><?= Yii::t('fe', 'Alamat pasien'); ?></dt>
                                <dd id='inf-pasien-alamatpasien'>lorem ipsum</dd>
                                <dt><?= Yii::t('fe', 'RT / RW'); ?></dt>
                                <dd id='inf-pasien-rtrw'>lorem ipsum</dd>
                                <dt><?= Yii::t('fe', 'Kelurahan'); ?></dt>
                                <dd id='inf-pasien-kelurahan'>lorem ipsum</dd>
                                <dt><?= Yii::t('fe', 'Kecamatan'); ?></dt>
                                <dd id='inf-pasien-kecamatan'>lorem ipsum</dd>
                                <dt><?= Yii::t('fe', 'Kota'); ?></dt>
                                <dd id='inf-pasien-kota'>lorem ipsum</dd>
                                <dt><?= Yii::t('fe', 'Propinsi'); ?></dt>
                                <dd id='inf-pasien-propinsi'>lorem ipsum</dd>
                                <dt><?= Yii::t('fe', 'No telepon'); ?></dt>
                                <dd id='inf-pasien-notelepon'>lorem ipsum</dd>
                                <dt><?= Yii::t('fe', 'Alamat email'); ?></dt>
                                <dd id='inf-pasien-alamatemail'>lorem ipsum</dd>
                                <dt><?= Yii::t('fe', 'Warga negara'); ?></dt>
                                <dd id='inf-pasien-warganegara'>lorem ipsum</dd>
                                <dt><?= Yii::t('fe', 'Suku'); ?></dt>
                                <dd id='inf-pasien-suku'>lorem ipsum</dd>
                                <dt><?= Yii::t('fe', 'Agama'); ?></dt>
                                <dd id='inf-pasien-agama'>lorem ipsum</dd>
                            </dl>
                        </div>
                        <!-- photo here -->
                        <div class='col-md-4 col-sm-12'>
                         <!-- image -->
                            <div class="row">
                                <div class="col-md-6 col-md-offset-3">
                                    <div class="thumbnail no-padding">
                                        <div class="thumb">
                                            <img id="profilePict" src="/media/img/icon-app/default.jpg" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class='panel panel-flat form-data-pasien' style='display: none;'>
                    <div class="panel-heading">
                        <h6 class="panel-title"><span class='inf-pasien-title-nama'><strong>Tn. Nama Pasien</strong></span> 
                            <small class='inf-pasien-title-norm'>No. Rekam Medis : 0000001</small>
                            <a class="heading-elements-toggle"><i class="icon-more"></i></a>
                        </h6>
                        <div class="heading-elements">
                            <ul class="icons-list">
                                <li><a data-action="collapse"></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="panel-toolbar clearfix">
                        <?=DocoHelpers::generateToolbar([
                            'batal' => [
                                'type' => 'button',
                                'title' => \Yii::t('fe', 'Batal'),
                                'icon' => 'fa fa-close',
                                'attributes' => [
                                    'class' => 'btn-pasien-batal',
                                    'data-options'=>'click',
                                ] 
                            ],
                            'update' => [
                                'type' => 'button',
                                'title' => \Yii::t('fe', 'Simpan'),
                                'icon' => 'fa fa-pencil',
                                'attributes' => [
                                    'class' => 'btn-pasien-simpan-ubah',
                                    'data-options'=>'click',
                                ] 
                            ],
                            // 'detail',
                        ]);?>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-4">
                            <?= $form->field($modelPasien, 'jenisidentitas')
                                ->dropDownList(
                                    ArrayHelper::map($data['lookup']['jenis_identitas'], 'lookup_id', 'lookup_value'), 
                                    ['id'=>'frm-pasien-jenisidentitas','prompt'=>'— PILIH —']);
                            ?>

                            <?= $form->field($modelPasien, 'no_identitas_pasien', [
                                'inputOptions'=>['id'=>'frm-pasien-no_identitas_pasien'],
                            ]); ?>

                            <?= $form->field($modelPasien, 'namadepan')
                                ->dropDownList(
                                    ArrayHelper::map($data['lookup']['nama_depan'], 'lookup_id', 'lookup_value'), 
                                    ['id'=>'frm-pasien-namadepan','prompt'=>'— PILIH —']
                                ); 
                            ?>
                            <?= $form->field($modelPasien, 'nama_pasien', [
                                'inputOptions' => ['id' => 'frm-pasien-nama_pasien'] 
                                ]); 
                            ?>
                            <?= $form->field($modelPasien, 'nama_bin', [
                                'inputOptions' => ['id' => 'frm-pasien-nama_bin'] 
                                ]) ?>
                            <?= $form->field($modelPasien, 'tempat_lahir', [
                                'inputOptions' => ['id' => 'frm-pasien-tempat_lahir'] 
                                ]) ?>
                            <?= $form->field($modelPasien, 'tanggal_lahir', [
                                'addon' => [
                                    'append' => [
                                        ['content' => '<i class="fa fa-calendar "></i>'],
                                    ],
                                ] ])->textInput(['class' => 'pickadate', 'id'=>'frm-pasien-tanggal_lahir']) ?>
                            <?= $form->field($modelPasien, 'umur', [
                                'inputOptions'=>['id' => 'frm-pasien-umur']]); ?>
                            <?= $form->field($modelPasien, 'jeniskelamin')
                                ->radioList(
                                    ArrayHelper::map($data['lookup']['jenis_kelamin'], 'lookup_id', 'lookup_value'), 
                                    ['inline'=>true, 'id'=>'frm-pasien-jeniskelamin']
                                ); 
                            ?>
                            <?= $form->field($modelPasien, 'statusperkawinan')
                                ->dropDownList(
                                    ArrayHelper::map($data['lookup']['status_perkawinan'], 'lookup_id', 'lookup_value'), 
                                    ['id'=>'frm-pasien-statusperkawinan','prompt'=>'— PILIH —']
                                )
                            ?>

                            <?= $form->field($modelPasien, 'nama_ibu', [
                                'inputOptions' => ['id' => 'frm-pasien-nama_ibu'] 
                                ]) ?>

                            <?= $form->field($modelPasien, 'nama_ayah', [
                                'inputOptions' => ['id' => 'frm-pasien-nama_ayah'] 
                                ]) ?>
                            <?= $form->field($modelPasien, 'anakke', [
                                'inputOptions' => ['id' => 'frm-pasien-anakke'] 
                                ]) ?>
                            <?= $form->field($modelPasien, 'jumlah_bersaudara', [
                                'inputOptions' => ['id' => 'frm-pasien-jumlah_bersaudara'] 
                                ]) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($modelPasien, 'alamat_pasien')->textArea(['id' => 'frm-pasien-alamat_pasien'] ); ?>
                            
                            <?= $form->field($modelPasien, 'rt', [
                                'inputOptions' => ['id' => 'frm-pasien-rt'] 
                                ]); ?>
                            <?= $form->field($modelPasien, 'rw', [
                                'inputOptions' => ['id' => 'frm-pasien-rw'] 
                                ]); ?>

                            <?= $form->field($modelPasien, 'propinsi_id')
                                ->dropDownList(
                                    ArrayHelper::map($data['master']['propinsi'], 'propinsi_id', 'propinsi_nama'), 
                                    ['id'=>'frm-pasien-propinsi_id','prompt'=>'— PILIH —']
                                )
                            ?>
                            <?= $form->field($modelPasien, 'kabupaten_id')->widget(DepDrop::classname(), [
                                'options'=>['id'=>'frm-pasien-kabupaten_id'],
                                'pluginOptions'=>[
                                    'depends'=>['frm-pasien-propinsi_id'],
                                    'placeholder'=>'-- PILIH --',
                                    'url'=>Url::to(['/master/kabupaten/list-kabupaten'])
                                ],
                            ]); ?>
                            <?= $form->field($modelPasien, 'kecamatan_id')->widget(DepDrop::classname(), [
                                'options'=>['id'=>'frm-pasien-kecamatan_id'],
                                'pluginOptions'=>[
                                    'depends'=>['frm-pasien-kabupaten_id'],
                                    'placeholder'=>'-- PILIH --',
                                    'url'=>Url::to(['/master/kecamatan/list-kecamatan'])
                                ]
                            ]); ?>
                            <?= $form->field($modelPasien, 'kelurahan_id')->widget(DepDrop::classname(), [
                                'options'=>['id'=>'frm-pasien-kelurahan_id'],
                                'pluginOptions'=>[
                                    'depends'=>['frm-pasien-kecamatan_id'],
                                    'placeholder'=>'-- PILIH --',
                                    'url'=>Url::to(['/master/kelurahan/list-kelurahan'])
                                ]
                            ]); ?>
                            <?= $form->field($modelPasien, 'no_telepon_pasien', [
                                'inputOptions' => ['id' => 'frm-pasien-no_telepon_pasien'] 
                                ]); ?>
                            <?= $form->field($modelPasien, 'no_mobile_pasien', [
                                'inputOptions' => ['id' => 'frm-pasien-no_mobile_pasien'] 
                                ]); ?>
                            <?= $form->field($modelPasien, 'alamatemail', [
                                'inputOptions' => ['id' => 'frm-pasien-alamatemail'] 
                                ]); ?>
                            <?= $form->field($modelPasien, 'pekerjaan_id')
                                ->dropDownList(
                                    ArrayHelper::map($data['master']['pekerjaan'], 'pekerjaan_id', 'pekerjaan_nama'), 
                                    ['id'=>'frm-pasien-pekerjaan_id','prompt'=>'— PILIH —']
                                ) 
                            ?>
                            <?= $form->field($modelPasien, 'suku_id')
                                ->dropDownList(
                                    ArrayHelper::map($data['master']['suku'], 'suku_id', 'suku_nama'), 
                                    ['id'=>'frm-pasien-suku_id','prompt'=>'— PILIH —']
                                )
                            ?>
                            <?= $form->field($modelPasien, 'warga_negara')
                                ->dropDownList(
                                    ArrayHelper::map($data['lookup']['warga_negara'], 'lookup_id', 'lookup_value'), 
                                    ['id'=>'frm-pasien-warga_negara','prompt'=>'— PILIH —']
                                )
                            ?>
                            <?= $form->field($modelPasien, 'agama')
                                ->dropDownList(
                                    ArrayHelper::map($data['lookup']['agama'], 'lookup_id', 'lookup_value'), 
                                    ['id'=>'frm-pasien-agama','prompt'=>'— PILIH —']
                                ) 
                            ?>
                        </div>

                        <!-- image -->
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-6 col-md-offset-3">
                                    <div class="thumbnail no-padding">
                                        <div class="thumb">
                                            <img id="profilePict" src="/media/img/icon-app/default.jpg" alt="">
                                            <div class="caption-overflow">
                                                <span>
                                                    <a id="open-camera" href="#" class="btn bg-success-400 btn-icon" data-popup="lightbox"><i class="fa fa-camera"></i> Ambil Foto</a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                
                <div class="row" style="margin-top: 20px;">
                    <div class="panel panel-success panel-bordered">
                        <div class="panel-heading">
                            <h6 class="panel-title">Penanggung Jawab Pasien</h6>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-6">
                                <?= $form->field($modelPenanggungjawab, 'pengantar')
                                    ->dropDownList(
                                        ArrayHelper::map($data['lookup']['pengantar'], 'lookup_id', 'lookup_value'), 
                                        ['id'=>'pengantar','prompt'=>'— PILIH —']
                                    )->label(Yii::t('fe', 'Pengantar'))
                                ?>
                                <?= $form->field($modelPenanggungjawab, 'penanggungjawab_nama')->label(Yii::t('fe', 'Nama')); ?>
                                <?= $form->field($modelPenanggungjawab, 'penanggungjawab_jeniskelamin')
                                    ->radioList(
                                        ArrayHelper::map($data['lookup']['jenis_kelamin'], 'lookup_id', 'lookup_value'), 
                                        ['inline'=>true]
                                    )->label(Yii::t('fe', 'Jenis Kelamin')); 
                                ?>
                                <?= $form->field($modelPenanggungjawab, 'jenisidentitas')
                                    ->dropDownList(
                                        ArrayHelper::map($data['lookup']['jenis_identitas'], 'lookup_id', 'lookup_value'), 
                                        ['id'=>'pj-jenisidentitas','prompt'=>'— PILIH —']
                                    )->label(Yii::t('fe', 'Jenis Identitas'))
                                ?>
                                <?= $form->field($modelPenanggungjawab, 'no_identitas', [
                                    'addon' => [
                                        'append' => [
                                            ['content' => '<i class="fa fa-list "></i>'],
                                        ],
                                    ] ]) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($modelPenanggungjawab, 'hubungankeluarga')
                                    ->dropDownList(
                                        ArrayHelper::map($data['lookup']['hubungan'], 'lookup_id', 'lookup_value'),
                                        ['id'=>'hubungankeluarga','prompt'=>'— PILIH —']
                                    ) 
                                ?>
                                <?= $form->field($modelPenanggungjawab, 'penanggungjawab_tempatlahir')->label(Yii::t('fe', 'Tempat Lahir')); ?>
                                <?= $form->field($modelPenanggungjawab, 'penanggungjawab_tgllahir', [
                                    'addon' => [
                                        'append' => [
                                            ['content' => '<i class="fa fa-calendar "></i>'],
                                        ],
                                    ] ])->textInput(['class' => 'pickadate'])->label(Yii::t('fe', 'Tanggal Lahir')) ?>
                                <?= $form->field($modelPenanggungjawab, 'umur')->staticInput(); ?>
                                <?= $form->field($modelPenanggungjawab, 'penanggungjawab_alamat')->textArea()->label(Yii::t('fe', 'Alamat')); ?>
                                <?= $form->field($modelPenanggungjawab, 'penanggungjawab_notelp')->label(Yii::t('fe', 'No. Telepon')); ?>
                                <?= $form->field($modelPenanggungjawab, 'penanggungjawab_nohp')->label(Yii::t('fe', 'No. Hp')); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <legend class="text-bold">Data Kunjungan</legend>
                <div class="row">
                    <div class="col-md-4">
                        <div class="panel panel-success panel-bordered">
                            <div class="panel-heading">
                                <h6 class="panel-title">Data Admisi</h6>
                                <div class="heading-elements">
                                    <ul class="icons-list">
                                        <li><a data-action="collapse"></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="panel-body">
                                <?= $form->field($modelRanap, 'tgl_admisi', [
                                'addon' => [
                                    'append' => [
                                        ['content' => '<i class="fa fa-calendar "></i>'],
                                    ],
                                ] ])->textInput(['class' => 'pickadate'])->label(Yii::t('fe', 'Tanggal Admisi')) ?>
                                <?= $form->field($modelRanap, 'jeniskasuspenyakit_id')->dropDownList(ArrayHelper::map($data['jeniskasus'], 'jeniskasuspenyakit_id', 'jeniskasuspenyakit_nama'), ['id'=>'jeniskasuspenyakit_id','prompt'=>'— PILIH —'])->label(Yii::t('fe', 'Jenis Kasus Penyakit')) ?>
                                <?= $form->field($modelRanap, 'ruangan_id')->widget(DepDrop::classname(), [
                                    'options'=>['id'=>'ruangan_id', 'class' => 'form-control select2 autoListRuangan'],
                                    'pluginOptions'=>[
                                        'depends'=>['jeniskasuspenyakit_id'],
                                        'placeholder'=>'-- PILIH --',
                                        'url'=>Url::to(['/pendaftaran/daftar/get-list-ruangan'])
                                    ]
                                ])->label(Yii::t('fe','Ruangan')); ?>
                                <div class="form-group">
                                    <label class="control-label col-sm-4">&nbsp;</label>
                                    <div class="col-sm-8">
                                    <?= Html::button("<i class='fa fa-search'> ". Yii::t('fe', 'Pilih Bed')."</i>", [
                                        'class' => 'btn bg-slate',
                                        'id' => 'cari_kamar',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal_backdrop',
                                        'data-width' => '90%',
                                        'data-action' => '/pendaftaran/daftar/pilih-tempat-tidur?id=',
                                        ]); ?>
                                    </div>
                                </div>
                                <?= $form->field($modelRanap, 'kamartempattidur_id_label')->textInput(['readonly'=>'true', 'id' => 'kamartempattidur_id_label'])
                                ->label(Yii::t('fe','No Tempat Tidur'));
                                ?>
                                <?= $form->field($modelRanap, 'kelaspelayanan_id')->dropDownList(ArrayHelper::map($data['kelas_pelayanan'], 'kelaspelayanan_id', 'kelaspelayanan_nama'), ['id'=>'kelaspelayanan_id','prompt'=>'— PILIH —'])->label(Yii::t('fe', 'Kelas Pelayanan')) ?>
                                <?= $form->field($modelRanap, 'pegawai_id')->dropDownList(ArrayHelper::map($data['dokter'], 'pegawai_id', 'nama_pegawai'), ['id'=>'pegawai_id','prompt'=>'— PILIH —'])->label(Yii::t('fe', 'Dokter')) ?>
                                <?= $form->field($modelRanap, 'carabayar_id')->dropDownList(ArrayHelper::map($data['cara_bayar'], 'carabayar_id', 'carabayar_nama'), ['id'=>'carabayar_id','prompt'=>'— PILIH —'])->label(Yii::t('fe', 'Cara Bayar')) ?>
                                <?= $form->field($modelRanap, 'penjamin_id')->dropDownList(ArrayHelper::map($data['penjamin'], 'penjamin_id', 'penjamin_nama'), ['id'=>'penjamin_id','prompt'=>'— PILIH —'])->label(Yii::t('fe', 'Penjamin')) ?>
                                <?= $form->field($modelRanap, 'additional_data')->textArea()->label(Yii::t('fe', 'Keterangan Pendaftaran')); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="panel panel-success panel-bordered">
                            <div class="panel-heading">
                                <h6 class="panel-title">Tarif Karcis</h6>
                                <div class="heading-elements">
                                    <ul class="icons-list">
                                        <li><a data-action="collapse"></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="panel-body">
                                <table id="table-karcis" class="table table-striped table-condensed table-hover" style="width:100%">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th class="karcis-title"><?=\Yii::t("fe", "Karcis");?></th>
                                            <th class="harga-title"><?=\Yii::t("fe", "Harga");?></th>
                                            <th width="20"><?=\Yii::t("fe", "Aksi");?></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="4" class="text-center"><?=Yii::t('fe','Data tidak tersedia')?></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" style="text-align:right">Total:</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="panel panel-success panel-bordered">
                            <div class="panel-heading">
                                <h6 class="panel-title">Rujukan</h6>
                                <div class="heading-elements">
                                    <ul class="icons-list">
                                        <li><a data-action="collapse"></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="panel-body">
                                <?= $form->field($modelRujukan, 'asalrujukan_id'); ?>
                                <?= $form->field($modelRujukan, 'no_rujukan'); ?>
                                <?= $form->field($modelRujukan, 'rujukandari_id'); ?>
                                <?= $form->field($modelRujukan, 'nama_perujuk'); ?>
                                <?= $form->field($modelRujukan, 'tanggal_rujukan'); ?>
                                <?= $form->field($modelRujukan, 'kodediagnosa_rujukan'); ?>

                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="panel panel-success panel-bordered">
                            <div class="panel-heading">
                                <h6 class="panel-title">BPJS</h6>
                                <div class="heading-elements">
                                    <ul class="icons-list">
                                        <li><a data-action="collapse"></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="panel-body">
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Similique hic doloribus aut vel a debitis eos. Quibusdam dolorem nam sint cumque quam. Perspiciatis rem beatae placeat hic! Corporis, at doloremque.
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="panel panel-success panel-bordered">
                            <div class="panel-heading">
                                <h6 class="panel-title">Asuransi Baru</h6>
                                <div class="heading-elements">
                                    <ul class="icons-list">
                                        <li><a data-action="collapse"></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="panel-body">
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Culpa dicta sed recusandae. Possimus aspernatur velit maiores quis culpa asperiores illo ex dolor distinctio totam perspiciatis, quas in cumque molestias eaque.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-success panel-bordered">
                            <div class="panel-heading">
                                <h6 class="panel-title">Riwayat Kunjungan Pasien</h6>
                                <div class="heading-elements">
                                    <ul class="icons-list">
                                        <li><a data-action="collapse"></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="panel-body">
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Nemo sequi illum fugiat repellendus molestiae, ex dignissimos maxime repudiandae libero, rerum adipisci sit deleniti excepturi in nisi et voluptatum, non praesentium?
                            </div>
                        </div>
                    </div>
                </div>
                <?= Html::submitButton(' Simpan', ['class' =>'btn btn-primary fa fa-plus ']) ?>
            </fieldset>
            <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade camera-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="row">
                <div id="my_camera" class="col-md-12"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-antrian" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Panggil Antrian</h4>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="panel bg-success">
                                <div class="panel-heading">
                                    <h6 class="panel-title">ANTRIAN SAAT INI</h6>
                                </div>
                                <div class="panel-body">
                                    <h1 class="no-margin text-black" style="font-size: 35px;" >A 9999</h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No Antrian</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php for ($i=0; $i < 10 ; $i++) :?> 
                                    <tr>
                                        <td>A <?=$i; ?></td>
                                        <td><button type="button" class="btn btn-info btn-rounded btn-xs">Pilih</button></td>
                                    </tr>
                                <?php endfor; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="text-center">
                    <div class="btn-group btn-group-justified">
                        <div class="btn-group">
                            <button type="button" class="btn btn-danger"><i class="fa fa-ban"></i> BATAL</button>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn bg-primary">PANGGIL ULANG <i class="fa fa-refresh"></i></button>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn bg-info">BERIKUTNYA <i class="fa fa-forward"></i></button>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn bg-success">TERIMA <i class="fa fa-check-circle"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$this->registerJs($this->render('js/index.js'));
$this->registerJs("
$('#form-daftar-ranap').docoForm('submit',{
    success : function(data) {
        console.log(data);
        if (data.metadata.status == 200) {
            document.location = 'ranap'; 
        }
    }
});

$(document).on('change','#kelaspelayanan_id', function(e){
    arrTarif = [];
    dataTarif = [];
    var kpId = $(this).val();
    var ruanganId = $('#ruangan_id').val();
    var karcisTitle;
    var hargaTitle;
    var pasienStatus = 1;
    if(karcisTitle == ''){
        karcisTitle = $('.karcis-title').text();
    }
    if(hargaTitle == ''){
        hargaTitle =$('.harga-title').text();
    }
    
    if($('input[name=chk-statuspasien]').is(':checked')){
        pasienStatus = 0;
    }
    var tbl;
    tbl = $('#table-karcis').docoTabel({
        filter: true,
        destroy: true,
        sorting: [[0, 'asc']], 
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl+'pendaftaran/daftar/get-karcis?ruangan_id='+ruanganId+'&kp_id='+kpId+'&status='+pasienStatus,
        fnFooterCallback: function(row, data, start, end, display) {
              var api = this.api();
               var intVal = function ( i ) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '')*1 :
                        typeof i === 'number' ?
                            i : 0;
                };
              total_tarif = api
                .column(3)
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
              $( api.column(2).footer() ).addClass('kolom-total-tarif').html(total_tarif);
          },
        drawCallback: function(settings){
            var api = this.api();
            $.each(api.rows().data(), function(key, val){
                arrTarif.push(val);
                $.each(arrTarif, function(key, val){
                    if(arrTarif[key]['checked'] === true){
                        dataTarif.push(val);
                    }
                })
            });
        },
        columns: [
            {title: karcisTitle, data: 'jenistarif_nama',searchable: false,orderable: false},
            {title: hargaTitle, data: 'harga_tariftindakan',searchable: false,orderable: false},
            {title: '',  data: 'aksi',searchable: false,orderable: false},
            {data: 'tmp_total', visible:false, searchable: false, orderable: false}
        ],
        
    });
    $('.dataTables_filter').hide();
});

$(document).on('click','.check-aksi', function(){
    let subtotal_tarif;
    var key = $(this).attr('data-key'); 
    if($(this).is(':checked')){
        total_tarif = total_tarif+arrTarif[key].harga_tariftindakan;
        dataTarif[key] = arrTarif[key];
    }
    else{
        total_tarif = total_tarif-arrTarif[key].harga_tariftindakan;
        if(typeof dataTarif[key] != 'undefined'){
            delete dataTarif[key];
        }
    }
    $('.dataTables_scrollFoot').find('.kolom-total-tarif').html(total_tarif);
});
    
")
?>


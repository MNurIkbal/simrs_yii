<?php
// Author : Ramdhan Nurrachman

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

$this->title = \Yii::t('fe', 'Update Profil Rumah Sakit');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .datepicker>div{
        display:block;
    }

    .input-group-addon{
        padding: 4px 9px;
    }

    .sp-preview-inner{

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
                      <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title); ?></b></h3>
                      <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                  </div>
              </div>
              <!-- end -->
                
            </div>
           

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>

                <?php
                // $is_disabled = empty($id) ? false : true;
                $form = ActiveForm::begin([
                    'id' => 'konfig-form',
                    'action' => '/master/profil-rumah-sakit/index?id='.$id_encrypt,
                    'enableAjaxValidation' => false,
                    'enableClientValidation' => false,
                        // 'type' => ActiveForm::TYPE_INLINE,
                    'type' => ActiveForm::TYPE_HORIZONTAL,
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
                        <legend class="text-semibold">Data Dasar</legend>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'nokode_rumahsakit', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('nokode_rumahsakit'), 'class' => 'form-control input-sm']); ?>
                                    </div>

                                    <div class="col-md-6">
                                            <?= $form->field($model, 'kelas_rumahsakit')
                                                ->dropDownList(
                                                    $kelas_rumahsakit,
                                                    ['id' => 'frm-kelas-rumahsakit', 'class' => 'select2', 'prompt' => '-- Pilih --']
                                                )
                                                ->label(Yii::t('fe', 'Kelas rumah sakit'));
                                            ?>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                    <?php
                                    $model->tglregistrasi = date('d-M-Y', strtotime($model->tglregistrasi));
                                    ?>
                                    <?= $form->field($model, 'tglregistrasi', ['horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-md-3',
                                        'wrapper' => 'col-md-9'
                                    ]])->widget(DatePicker::classname(), [
                                        'name' => 'date_12',
                                        'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                        'value' => date('dd-M-yyyy'),
                                    // 'readonly' => true,
                                        'pluginOptions' => [
                                            'autoclose' => true,
                                            'format' => 'dd-M-yyyy',
                                            // 'endDate' => "0d",
                                        ]
                                    ]);
                                    ?>
                                    </div>

                                    <div class="col-md-6">
                                            <?= $form->field($model, 'namadirektur_rumahsakit', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('namadirektur_rumahsakit'), 'class' => 'form-control input-sm']); ?>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'nama_rumahsakit', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('nama_rumahsakit'), 'class' => 'form-control input-sm']); ?>
                                    </div>

                                    <div class="col-md-6">
                                            <?= $form->field($model, 'nama_penyelenggara', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('nama_penyelenggara'), 'class' => 'form-control input-sm']); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                          <?= $form->field($model, 'jenis_rumahsakit')
                                                ->dropDownList(
                                                    $jenis_rumahsakit,
                                                    ['id' => 'frm-jenis-rumahsakit', 'class' => 'select2', 'prompt' => '-- Pilih --']
                                                )
                                                ->label(Yii::t('fe', 'Jenis rumah sakit'));
                                            ?>  
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                    </fieldset>
                    <fieldset title="2" onmouseover="this.title='';">
                        <legend class="text-semibold">Lokasi dan Bangunan</legend>

                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'alamatlokasi_rumahsakit', ['labelOptions' => ['class' => 'text-left']])->textarea(array('rows' => 2, 'cols' => 5)); ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'no_telp_profilrs', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('no_telp_profilrs'), 'class' => 'form-control input-sm']); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'propinsi_id')
                                    ->dropDownList(
                                        $propinsi,
                                        ['id' => 'frm-rs-propinsi_id', 'class' => 'select2', 'prompt' => '-- Pilih --']
                                    )
                                    ->label(Yii::t('fe', 'Provinsi'));
                                ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'no_faksimili', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('no_faksimili'), 'class' => 'form-control input-sm']); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'kabupaten_id')->widget(DepDrop::classname(), [
                                    'options' => ['id' => 'frm-rs-kabupaten_id', 'class' => 'select2', ],
                                    'pluginOptions' => [
                                        'depends' => ['frm-rs-propinsi_id'],
                                        'placeholder' => '-- Pilih --',
                                        'url' => Url::to(['/master/kabupaten/list-kabupaten'])
                                    ],
                                    'data'=>[$model['kabupaten_id']=>''],
                                ])->label(Yii::t('fe', 'Kabupaten / kota')); ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'email', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('email'), 'class' => 'form-control input-sm']); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'kecamatan_id')->widget(DepDrop::classname(), [
                                    'options' => ['id' => 'frm-rs-kecamatan_id', 'class' => 'select2', ],
                                    'pluginOptions' => [
                                        'depends' => ['frm-rs-kabupaten_id'],
                                        'placeholder' => '-- Pilih --',
                                        'url' => Url::to(['/master/kecamatan/list-kecamatan'])
                                    ],
                                    'data'=>[$model['kecamatan_id']=>''],
                                ])->label(Yii::t('fe', 'Kecamatan')); ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'notelphumas', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('notelphumas'), 'class' => 'form-control input-sm']); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'kelurahan_id')->widget(DepDrop::classname(), [
                                    'options' => ['id' => 'frm-rs-kelurahan_id', 'class' => 'select2', ],
                                    'pluginOptions' => [
                                        'initialize'=>true,
                                        'depends' => ['frm-rs-kecamatan_id'],
                                        'initDepends' => ['frm-rs-propinsi_id'],
                                        'placeholder' => '-- Pilih --',
                                        'url' => Url::to(['/master/kelurahan/list-kelurahan'])
                                    ],
                                    'data'=>[$model['kelurahan_id']=>''],
                                ])->label(Yii::t('fe', 'Kelurahan')); ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'website', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('website'), 'class' => 'form-control input-sm']); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'kode_pos', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('kode_pos'), 'class' => 'form-control input-sm']); ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'luastanah', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('luastanah'), 'class' => 'form-control input-sm']); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'luasbangunan', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('luasbangunan'), 'class' => 'form-control input-sm']); ?>
                            </div>
                            <div class="col-md-6"></div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'latitude', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('latitude'), 'class' => 'form-control input-sm']); ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'longtitude', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('longtitude'), 'class' => 'form-control input-sm']); ?>
                            </div>
                        </div>
                        <br>
                    </fieldset>

                    <fieldset title="3" onmouseover="this.title='';">
                        <legend class="text-semibold">Perizinan</legend>

                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'nomor_suratizin', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('nomor_suratizin'), 'class' => 'form-control input-sm']); ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'visi', ['labelOptions' => ['class' => 'text-left']])->textarea(array('rows' => 2, 'cols' => 5)); ?>
                               <?= Html::hiddenInput('ProfilRumahSakitForm[motto]', '', ['id' => 'motto']); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                $model->tgl_suratizin = date('d-M-Y', strtotime($model->tgl_suratizin));
                                ?>
                                <?= $form->field($model, 'tgl_suratizin', ['horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-md-3',
                                    'wrapper' => 'col-md-9'
                                ]])->widget(DatePicker::classname(), [
                                    'name' => 'date_12',
                                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                    'value' => date('dd-M-yyyy'),
                                    // 'readonly' => true,
                                    'pluginOptions' => [
                                        'autoclose' => true,
                                        'format' => 'dd-M-yyyy',
                                        // 'endDate' => "0d",
                                    ]
                                ]);
                                ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'misi', ['labelOptions' => ['class' => 'text-left']])->textarea(array('rows' => 2, 'cols' => 5)); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'oleh_suratizin', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('oleh_suratizin'), 'class' => 'form-control input-sm']); ?>
                            </div>
                            <div class="col-md-6">
                            
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'sifat_suratizin', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('sifat_suratizin'), 'class' => 'form-control input-sm']); ?>
                            </div>
                            <div class="col-md-6"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                        <?php
                                         $model->masaberlaku_dari = date('d-M-Y', strtotime($model->masaberlaku_dari));
                                        ?>
                                        <?= $form->field($model, 'masaberlaku_dari', ['horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-md-3',
                                            'wrapper' => 'col-md-9'
                                        ]])->widget(DatePicker::classname(), [
                                            'name' => 'date_12',
                                            'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                            'value' => date('dd-M-yyyy'),
                                    // 'readonly' => true,
                                            'pluginOptions' => [
                                                'autoclose' => true,
                                                'format' => 'dd-M-yyyy',
                                                // 'endDate' => "0d",
                                            ]
                                        ]);
                                        ?>
                                </div>
                            <div class="col-md-6">
                                <?php
                                 $model->masaberlaku_sampai = date('d-M-Y', strtotime($model->masaberlaku_sampai));
                                ?>
                                <?= $form->field($model, 'masaberlaku_sampai', ['horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-md-3',
                                    'wrapper' => 'col-md-9'
                                ]])->widget(DatePicker::classname(), [
                                    'name' => 'date_12',
                                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                    'value' => date('dd-M-yyyy'),
                                    // 'readonly' => true,
                                    'pluginOptions' => [
                                        'autoclose' => true,
                                        'format' => 'dd-M-yyyy',
                                        // 'endDate' => "0d",
                                    ]
                                ]);
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'status_penyelenggara', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('status_penyelenggara'), 'class' => 'form-control input-sm']); ?>
                            </div>
                            
                        </div>
                        <br>
                    </fieldset>

                    <fieldset title="4" onmouseover="this.title='';">
                        <legend class="text-semibold">Pengaturan Gambar</legend>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model,'path_logorumahsakit', [
                                    'addon' => ['prepend' => ['content'=>Url::base(true)]]
                                ])->textInput(['readonly'=>true]); ?>
                            </div>
                            <div class="col-md-6">
                                
                                <?php
                                echo $form->field($model, 'logo_rumahsakit', [
                                    'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8',
                                    'id' => 'file',
                                ]])->widget(FileInput::classname(), [
                                    'pluginOptions' => [
                                        'initialPreview'=>$preview_logo_rumahsakit,
                                        'initialPreviewAsData'=>true,
                                        'showUpload' => false,
                                        'maxFileSize' => 2000,
                                        'previewSettings' => ['image'=>['width'=>'200px','height'=>'120px']],
                                        'allowedFileExtensions' => ['jpg', 'png', 'jpeg', 'gif'],
                                    ],
                                    'options' => ['accept' => 'image/*', 'class' => 'file-input', 'id' => 'file'],
                                    'pluginEvents' => [
                                        "fileclear" => "function() { 
                                            $('#is_deleted_logo_rumahsakit').val(1);
                                         }",
                                        "fileimageloaded" => "function() { 
                                            $('#is_deleted_logo_rumahsakit').val(0);
                                         }"
                                     ]

                                ])->fileInput()->label(Yii::t('fe', 'Logo Rumah Sakit').' <i>(150x150)(Max 2MB)</i>');
                                ?>
                                <?= Html::hiddenInput('is_deleted_logo_rumahsakit','0',['id'=>'is_deleted_logo_rumahsakit'])?> 
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model,'path_gambar_login', [
                                    'addon' => ['prepend' => ['content'=>Url::base(true)]]
                                ])->textInput(['readonly'=>true]); ?>
                            </div>
                            <div class="col-md-6">
                                
                                <?php
                                echo $form->field($model, 'gambar_login', [
                                    'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8',
                                    'id' => 'file-gambar-login',
                                ]])->widget(FileInput::classname(), [
                                    'pluginOptions' => [
                                        'initialPreview'=>$preview_gambar_login,
                                        'initialPreviewAsData'=>true,
                                        'showUpload' => false,
                                        'maxFileSize' => 2000,
                                        'previewSettings' => ['image'=>['width'=>'200px','height'=>'120px']],
                                        'allowedFileExtensions' => ['jpg', 'png', 'jpeg', 'gif'],
                                    ],
                                    'options' => ['accept' => 'image/*', 'class' => 'file-input', 'id' => 'widget-file-gambar-login'],
                                    'pluginEvents' => [
                                        "fileclear" => "function() { 
                                            $('#is_deleted_gambar_login').val(1);
                                         }",
                                        "fileimageloaded" => "function() { 
                                            $('#is_deleted_gambar_login').val(0);
                                         }"
                                     ]

                                ])->fileInput()->label(Yii::t('fe', 'Gambar Login').' <i>(640x350)(Max 2MB)</i>');
                                ?>
                                <?= Html::hiddenInput('is_deleted_gambar_login','0',['id'=>'is_deleted_gambar_login'])?> 
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model,'path_background_login', [
                                    'addon' => ['prepend' => ['content'=>Url::base(true)]]
                                ])->textInput(['readonly'=>true]); ?>
                            </div>
                            <div class="col-md-6">
                                
                                <?php
                                echo $form->field($model, 'background_login', [
                                    'hintType' => ActiveField::HINT_SPECIAL,
                                    'hintSettings' => ['placement' => 'right', 'onLabelClick' => true, 'onLabelHover' => false],
                                    'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8',
                                    'id' => 'file-background-login',
                                ]])->widget(FileInput::classname(), [
                                    'pluginOptions' => [
                                        'initialPreview'=>$preview_background_login,
                                        'initialPreviewAsData'=>true,
                                        'showUpload' => false,
                                        'maxFileSize' => 2000,
                                        'previewSettings' => ['image'=>['width'=>'200px','height'=>'120px']],
                                        'allowedFileExtensions' => ['jpg', 'png', 'jpeg', 'gif'],
                                    ],
                                    'options' => ['accept' => 'image/*', 'class' => 'file-input', 'id' => 'widget-file-background-login'],
                                    'pluginEvents' => [
                                        "fileclear" => "function() { 
                                            $('#is_deleted_background_login').val(1);
                                         }",
                                        "fileimageloaded" => "function() { 
                                            $('#is_deleted_background_login').val(0);
                                         }"
                                    ]

                                ])->fileInput()->label(Yii::t('fe', 'Background Login').' <i>(1360x800)(Max 2MB)</i>');
                                ?>
                                <?= Html::hiddenInput('is_deleted_background_login','0',['id'=>'is_deleted_background_login'])?> 
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model,'path_logo_header', [
                                    'addon' => ['prepend' => ['content'=>Url::base(true)]]
                                ])->textInput(['readonly'=>true]); ?>
                            </div>
                            <div class="col-md-6">
                                
                                <?php
                                echo $form->field($model, 'logo_header', [
                                    'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8',
                                    'id' => 'file-logo-header',
                                ]])->widget(FileInput::classname(), [
                                    'pluginOptions' => [
                                        'initialPreview'=>$preview_logo_header,
                                        'initialPreviewAsData'=>true,
                                        'showUpload' => false,
                                        'maxFileSize' => 2000,
                                        'previewSettings' => ['image'=>['width'=>'200px','height'=>'120px']],
                                        'allowedFileExtensions' => ['jpg', 'png', 'jpeg', 'gif'],
                                    ],
                                    'options' => ['accept' => 'image/*', 'class' => 'file-input', 'id' => 'widget-file-logo-header'],
                                    'pluginEvents' => [
                                        "fileclear" => "function() { 
                                            $('#is_deleted_logo_header').val(1);
                                         }",
                                        "fileimageloaded" => "function() { 
                                            $('#is_deleted_logo_header').val(0);
                                         }"
                                     ]

                                ])->fileInput()->label(Yii::t('fe', 'Logo Header').' <i>(234x60)(Max 2MB)</i>');
                                ?>
                                <?= Html::hiddenInput('is_deleted_logo_header','0',['id'=>'is_deleted_logo_header'])?> 
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                echo $form->field($model, 'warna_header')->widget(ColorInput::classname(), [
                                    'width' => '50%',
                                    'options' => [
                                        'placeholder' => 'Pilih Warna ...',
                                        'readonly'=>true
                                    ],
                                ]);
                                ?>
                            </div>
                            <div class="col-md-6">
                                <?php
                                echo $form->field($model, 'font_header')->widget(ColorInput::classname(), [
                                    'width' => '50%',
                                    'options' => [
                                        'placeholder' => 'Pilih Warna ...',
                                        'readonly'=>true
                                    ],
                                ]);
                                ?>
                            </div>
                        </div>
                        <br>
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

    const redirectUrl = '/master/profil-rumah-sakit/index';
     $('#btn-kembali').on('click', function () {
          $(location).attr('href', redirectUrl);
      });

");
?>
<?php
$this->registerCss($this->render('../assets/css/wizard.css'));
$this->registerJs($this->render('../assets/js/konfig-profil-rs.js'));
?>

<?php

/**
 * * @author Naufal Ziyad L
 * * @Februari 15 2018
 * 
 * * last update : bacengjs (Bambang.Hermawan@sirs.co.id)
 * * A product of PT. Citraraya Nusatama
 * * Powered by Sirs
 * 
**/

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use app\components\DocoHelpers;
use kartik\widgets\FileInput;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$model->tanggal_lahir = date('d-m-Y', strtotime($model->tanggal_lahir));
$isShowPj = !empty($attrPj) ? true : false;
$isShowKp = !empty($attrKp) ? true : false;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                  <div class="column-1">
                    <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                  </div>
                  <div class="column-2">
                    <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'back-custom' => [
                        'title' => \Yii::t('fe', 'Kembali'),
                        'icon' => 'fa fa-arrow-left',
                        'attributes' => [
                            'id' => 'btn-back-custom',
                            'data-options' => 'link',
                            'data-target'=> Url::home().$linkBeforeUpdateWithoutSlash,
                            'target'=> '_self'
                        ]
                    ],
                    'save'=>[
                        'attributes'=>[
                            //'data-target'=>'form-daftar-rajal'
                            'id' => 'btn-save',
                            'data-toggle'=> 'modal',
                        ]
                    ],
                    'cetak-data-pasien' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Cetak data pasien'),
                        'icon' => 'fa fa-print',
                        'method' => '',
                        'attributes' => [
                            'id'=>'btn-cetak-kartu-pasien',
                            'data-options'=>'click',
                            'data-target' => Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/cetak-data-pasien?id='.$encryptId,
                        ]
                    ],
                ]);?>
                <?php 
                    echo Html::button('<b><i class="fa fa-eye"></i></b>' . Yii::t('fe','Riwayat perubahan data pasien'),[
                        'class' => 'btn btn-info btn-labeled btn-xs data-filter',
                        'action' => Url::home().'pendaftaran/informasi-pencarian-pasien/riwayat-perubahan-data-pasien?pasien_id='.$encryptId,
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'data-width' => '50%',
                    ]);
                ?>
            </div>
            <?=Html::hiddenInput('kabupatenid', $model->kabupaten_id, ['class' => 'kabupatenid'])?>
            <?=Html::hiddenInput('kecamatanid', $model->kecamatan_id, ['class' => 'kecamatanid'])?>
            <?=Html::hiddenInput('kelurahanid', $model->kelurahan_id, ['class' => 'kelurahanid'])?>
            <div class="panel-body" style="padding:10px;">
            <?php
                $form = ActiveForm::begin([
                    'id' => 'form-daftar-rajal',
                    'type' => ActiveForm::TYPE_VERTICAL,
                    'enableClientValidation' => false,
                    'enableAjaxValidation' => false,
                    'formConfig' => ['showErrors' => true, 'labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                ]);
            ?>
            <fieldset class="content-group">
                <!-- <legend class="text-bold">Data Pasien</legend> -->
                <div class="row">
                    <div class="col-md-5">
                        <?= $form->field($model, 'no_rekam_medik')->textInput(['readonly' => true]) ?>
                        <div class="form-group">
                        <?php if (!empty($model->additional_identitas)): ?>
                            <?php foreach ($model->additional_identitas as $key => $value): ?>
                                <div class="row identitas">
                                    <div class="col-sm-6">
                                        <?php $model->jenisidentitas = isset($value->jenisidentitas) ? $value->jenisidentitas : ''; ?>

                                        <?= $form->field($model, 'jenisidentitas[]')->dropDownList(
                                            $ddlJenisIdentitas, [
                                                'id' => 'jenis-identitas-'.$key,
                                                'class' => 'select2 jenis_identitas',
                                                'prompt' => '— PILIH —',
                                                'value' => $model->jenisidentitas
                                            ]
                                        ) ?>
                                    </div>
                                    <div class="col-sm-5">
                                        <?php $model->no_identitas_pasien = isset($value->no_identitas_pasien) ? $value->no_identitas_pasien : ''; ?>

                                        <?= $form->field($model, 'no_identitas_pasien[]')->textInput([
                                            'id' => 'no-identitas-pasien-'.$key,
                                            'class' => 'no_identitas_pasien',
                                            'value' => $model->no_identitas_pasien
                                        ]) ?>
                                    </div>
                                    <?php if ($key == 0): ?>
                                        <div class="col-sm-1">
                                            <label class="control-label"><b style="color:white;">Aksi</b></label>
                                            <?= Html::button('+', ['class' => 'btn btn-success tambah-jenis']); ?>
                                        </div>
                                    <?php else : ?>
                                        <div class="col-sm-1">
                                            <label class="control-label"><b style="color:white;">Aksi</b></label>
                                            <?= Html::button('X', ['class' => 'btn btn-danger hapus-jenis']); ?>
                                        </div>
                                    <?php endif ?>
                                </div>
                            <?php endforeach ?>
                        <?php else : ?>
                            <div class="row identitas">
                                <div class="col-sm-6">
                                    <?= $form->field($model, 'jenisidentitas[]')->dropDownList(
                                        $ddlJenisIdentitas, [
                                            'id' => 'jenis-identitas-0',
                                            'class' => 'select2 jenis_identitas',
                                            'prompt' => '— PILIH —'
                                        ]
                                    ) ?>
                                </div>
                                <div class="col-sm-5">
                                    <?= $form->field($model, 'no_identitas_pasien[]')->textInput([
                                        'id' => 'no-identitas-pasien-0',
                                        'class' => 'no_identitas_pasien'
                                    ]) ?>
                                </div>
                                <div class="col-sm-1">
                                    <label class="control-label"><b style="color:white;">Aksi</b></label>
                                    <?= Html::button('+', ['class' => 'btn btn-success tambah-jenis']); ?>
                                </div>
                            </div>
                        <?php endif ?>
                        </div>
                        <div class="form-group">
                            <?php if (isset($is_hide_alias) && $is_hide_alias == true): ?>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <?= $form->field($model, 'nama_pasien', [
                                            'inputOptions' => [
                                                'class' => 'form-control'
                                            ]
                                        ])->textInput(['class' => 'name-validate-class']); ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <?= $form->field($model, 'namadepan')->dropDownList($ddlNamaDepan, [
                                            'class' => 'select2 select2pasien',
                                            'id'=>'frm-pasien-namadepan',
                                            'prompt' => '— PILIH —',
                                        ])->label(Yii::t('fe', 'Nama Depan')); ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <?= $form->field($model, 'nama_pasien', [
                                            'inputOptions' => [
                                                'class' => 'form-control'
                                            ]
                                        ])->textInput(['class' => 'name-validate-class']); ?>
                                    </div>
                                </div>
                            <?php endif ?>
                            
                        </div>
                        <?= $form->field($model, 'nama_panggilan')->textInput(['class' => 'name-validate-class']); ?>
                        <?= $form->field($model, 'tempat_lahir') ?>
                        <?= $form->field($model, 'tanggal_lahir', [
                            'addon' => [
                                'append' => [
                                    ['content' => '<i id="btn_addon_tgllahir" class="fa fa-calendar "></i>'],
                                ],
                            ] ])->textInput(['class' => '', 'id'=>'frm-pasien-tanggal_lahir','data-mask'=>'99-99-9999']) ?>
                        <?=Html::activeHiddenInput($model, 'tmp_tanggal_lahir', ['class'=>'tmp_tanggal_lahir', 'id'=>'tmp_tanggal_lahir'])?>
                        <?= $form->field($model, 'umur', [
                            'inputOptions'=>['id' => 'frm-pasien-umur', 'readonly'=>true]]); ?>
                        <?= $form->field($model, 'jeniskelamin')
                            ->radioList(
                                $ddlJenisKelamin,
                                [
                                    'inline'=>true,
                                    'id'=>'frm-pasien-jeniskelamin'
                                ]
                            )
                            ->label(Yii::t('fe', 'Jenis Kelamin'));
                        ?>
                        <?= $form->field($model, 'golongandarah')
                            ->dropDownList(
                                $ddlGolonganDarah,
                                [
                                    'id'=>'frm-pasien-golongandarah',
                                    'class' => 'select2',
                                    'prompt' => '-- PILIH --'
                                ]
                            )
                            ->label(Yii::t('fe', 'Golongan Darah'));
                        ?>
                        <div id="error_InfPencarianPasienFormgolongandarah"></div>
                        <?= $form->field($model, 'statusperkawinan')->dropDownList($ddlStatusPerkawinan, ['class'=>'select2','prompt'=>'— PILIH —']) ?>
                        <?= $form->field($model, 'nama_ibu')->textInput(['class' => 'name-validate-class']); ?>
                        <?= $form->field($model, 'nama_ayah')->textInput(['class' => 'name-validate-class']); ?>
                        <?= $form->field($model, 'anakke') ?>
                        <?= $form->field($model, 'jumlah_bersaudara') ?>
                        <div class="col-sm-3 add-pj">
                            <?= Html::button('<i class="fa fa-plus"></i> '.Yii::t('fe', 'Tambah Penanggung Jawab'), [
                                'class' => 'btn btn-success btn-sm', 
                                'id' => 'add-pj'
                            ]); ?>
                        </div>
                        <div class="col-sm-3 remove-pj" style="margin-left: 75px; margin-right: 100px;">
                            <?= Html::button('<i class="fa fa-trash"></i> '.Yii::t('fe', 'Hapus Penanggung Jawab'), [
                                'class' => 'btn btn-danger btn-sm', 
                                'id' => 'remove-pj'
                            ]); ?>
                        </div>
                        <div class="col-sm-3 add-kp" style="margin-top: 8px;">
                            <?= Html::button('<i class="fa fa-plus"></i> '.Yii::t('fe', 'Tambah Data Keluarga'), [
                                'class' => 'btn btn-success btn-sm', 
                                'id' => 'add-kp'
                            ]); ?>
                        </div>
                        <div class="col-sm-3 remove-kp" style="margin-left: 75px; margin-top: 8px;">
                            <?= Html::button('<i class="fa fa-trash"></i> '.Yii::t('fe', 'Hapus Data Keluarga'), [
                                'class' => 'btn btn-danger btn-sm', 
                                'id' => 'remove-kp'
                            ]); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'alamat_pasien')->textArea(); ?>
                        <?= $form->field($model, 'rt'); ?>
                        <?= $form->field($model, 'rw'); ?>
                        <?= $form->field($model, 'propinsi_id')->dropDownList($ddlPropinsi, ['id'=>'frm-pasien-propinsi_id','class'=>'select2','prompt'=>'— PILIH —']) ?>

                        <?= $form->field($model, 'kabupaten_id')->widget(DepDrop::classname(), [
                'data'=>$ddlkabupaten,
                            'options'=>['id'=>'frm-pasien-kabupaten_id','class'=>'select2'],
                            'pluginOptions'=>[
                                'depends'=>['frm-pasien-propinsi_id'],
                                'placeholder'=>'-- PILIH --',
                                'url'=>Url::to(['/master/kabupaten/list-kabupaten?selected='.$model->kabupaten_id])
                            ]
                        ]); ?>

                        <?= $form->field($model, 'kecamatan_id')->widget(DepDrop::classname(), [
                'data'=>$ddlkecamatan,
                            'options'=>['id'=>'frm-pasien-kecamatan_id','class'=>'select2'],
                            'pluginOptions'=>[
                                'depends'=>['frm-pasien-kabupaten_id'],
                                'placeholder'=>'-- PILIH --',
                                'url'=>Url::to(['/master/kecamatan/list-kecamatan?selected='.$model->kecamatan_id])
                            ]
                        ]); ?>

                        <?= $form->field($model, 'kelurahan_id')->widget(DepDrop::classname(), [
                'data'=>$ddlkelurahan,
                            'options'=>['id'=>'frm-pasien-kelurahan_id','class'=>'select2'],
                            'pluginOptions'=>[
                                'depends'=>['frm-pasien-kecamatan_id'],
                                'placeholder'=>'-- PILIH --',
                                'url'=>Url::to(['/master/kelurahan/list-kelurahan?selected='.$model->kelurahan_id])
                            ]
                        ]); ?>
                        <?= $form->field($model, 'no_telepon_pasien'); ?>
                        <?= $form->field($model, 'alamatemail')->input('email'); ?>
                        <?= $form->field($model, 'pendidikan_id')->dropDownList($ddlPendidikan, ['id'=>'pendidikan_id','class'=>'select2','prompt'=>'— PILIH —']) ?>
                        <?= $form->field($model, 'pekerjaan_id')->dropDownList($ddlPekerjaan, ['id'=>'pekerjaan_id','class'=>'select2','prompt'=>'— PILIH —']) ?>
                        <?= $form->field($model, 'suku_id')->dropDownList($ddlSuku, ['id'=>'suku_id','class'=>'select2','prompt'=>'— PILIH —']) ?>
                        <?= $form->field($model, 'warga_negara')->dropDownList($ddlWargaNegara, ['id'=>'warga_negara','class'=>'select2','prompt'=>'— PILIH —']) ?>
                        <?= $form->field($model, 'agama')->dropDownList($ddlAgama, ['id'=>'agama','class'=>'select2','prompt'=>'— PILIH —']) ?>
                        <?= $form->field($model, 'catatanpenting_pasien')->textarea(['rows' => '3']) ?>
                        </div>
                    <div class="col-md-2">
                        <div class="row">
                            <div class="col-md-6 col-md-offset-3">
                                <div class="thumbnail no-padding">
                                    <div class="thumb">
                                          <?php
                                          if(!empty($model->photopasien)){
                                              echo Html::img('@web/media/img/pasien/'.$model->photopasien, ['class' => 'img-responsive img-display profilePict']);
                                          }else{ ?>
                                            <img id="profilePict" src="/media/img/icon-app/default.jpg" alt="">
                                          <?php } ?>

                                        <?php
                                            echo $form->field($model, 'photopasien', ['horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-12',
                                                'wrapper' => 'col-md-12',
                                                ]])->widget(FileInput::classname(), [
                                                    'pluginOptions' => [
                                                      'showCaption' => false,
                                                      'showRemove' => false,
                                                      'showUpload' => false,
                                                      'browseClass' => 'btn btn-primary btn-block',
                                                      'browseIcon' => '<i class="glyphicon glyphicon-camera"></i> ',
                                                      'browseLabel' =>  'Select Photo'
                                                    ],
                                                    'options' => ['accept' => 'image/*', 'id'=>'file_input'],
                                                ])->label(false);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row data_pj_pasien hidden">
                    <div class="col-md-12">
                        <hr>
                        <div class="panel panel-default">
                        <a id="info-heading" data-toggle="collapse" href="#info_pj_pasien" role="button" aria-expanded="true" aria-controls="info_pj_pasien">
                            <div class="panel-heading flex-container">
                                <h6 class="panel-title informasi_pasien">
                                    <span><b><?= Yii::t('fe', 'Penanggung Jawab ') ?></b></span>
                                </h6>
                                
                                <ul class="icons-list">
                                    <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                </ul>
                            </div>
                        </a>

                        <div class="panel-body column-info multi-collapse in" id="info_pj_pasien">
                            <div id="form-input-pj" >
                                <div class="form-group" id="form-pj-content">
                                    <div class="col-sm-6">
                                        <?php
                                            echo $form->field($modelPj, 'pj_pengantar', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ]
                                            ])->dropDownList(ArrayHelper::map($data_lookup['pengantar'], 'lookup_id', 'lookup_value'), [
                                                'prompt' => '-',
                                                'data-urutan' => 1
                                            ]);

                                            echo $form->field($modelPj, 'pj_nama', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ]
                                            ])->textInput();

                                            echo $form->field($modelPj, 'pj_jk', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ]
                                            ])->radioList(
                                                ArrayHelper::map($data_lookup['jenis_kelamin'], 'lookup_id', 'lookup_value'),
                                                [
                                                    'inline' => true,
                                                    'item' => function($index, $label, $name, $checked, $value) {
                                                        $return = '<label class="radio-inlineo">';
                                                            $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" class="styled">';
                                                            $return .= '<i></i>';
                                                            $return .= '<span style="margin-left:5px;">' . $label . '</span>';
                                                        $return .= '</label>';

                                                        return $return;
                                                    }
                                                ]
                                            );
                                            ?>
                                            <div id="error_PjpasienFormpj_jk" style="margin-left:178px;"></div>
                                            <?php
                                            echo $form->field($modelPj, 'pj_jenis_identitas', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ]
                                            ])->dropDownList(
                                                ArrayHelper::map($data_lookup['jenis_identitas'], 'lookup_id', 'lookup_value'), [
                                                'prompt' => '-'
                                            ]);

                                            echo $form->field($modelPj, 'pj_no_identitas', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ]
                                            ])->textInput();

                                            echo $form->field($modelPj, 'pj_hubungan', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ]
                                            ])->dropDownList(ArrayHelper::map($data_lookup['hubungan_keluarga'], 'lookup_id', 'lookup_value'), [
                                                'prompt' => '-'
                                            ]);
                                        ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <?php
                                            echo $form->field($modelPj, 'pj_tempat_lahir', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ]
                                            ])->textInput();

                                            echo $form->field($modelPj, 'pj_tanggal_lahir', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ],
                                                'addon' => [
                                                    'append' => [
                                                        ['content' => '<i id="pj-date" class="fa fa-calendar "></i>'],
                                                    ],
                                                ]
                                            ])->textInput([
                                                'class'=>'pickadate-w-month dateusia',
                                                'data-mask'=>'99-99-9999'
                                            ]);

                                            echo $form->field($modelPj, 'pj_umur', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ]
                                            ])->textInput(['class'=>'umurtext', 'readonly'=>true]);

                                            echo $form->field($modelPj, 'pj_no_telepon', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ],
                                            ])->textInput();

                                            echo $form->field($modelPj, 'pj_alamat', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ]
                                            ])->textArea();
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="row data_keluarga_pasien hidden">
                    <div class="col-md-12">
                        <hr>
                        <div class="panel panel-default">
                        <a id="info-heading" data-toggle="collapse" href="#info_keluarga_pasien" role="button" aria-expanded="true" aria-controls="info_keluarga_pasien">
                            <div class="panel-heading flex-container">
                                <h6 class="panel-title informasi_pasien">
                                    <span><b><?= Yii::t('fe', 'Data Keluarga ') ?></b></span>
                                </h6>
                                
                                <ul class="icons-list">
                                    <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                </ul>
                            </div>
                        </a>

                        <div class="panel-body column-info multi-collapse in" id="info_keluarga_pasien">
                            <div id="form-input-kp" >
                                <div class="form-group" id="form-kp-content">
                                    <div class="col-sm-6">
                                        <?php
                                            echo $form->field($modelKp, 'keluarga_namadepan', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ]
                                            ])->dropDownList($ddlNamaDepan, [
                                                'prompt' => '-'
                                            ]);

                                            echo $form->field($modelKp, 'keluarga_nama', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ]
                                            ])->textInput();

                                            echo $form->field($modelKp, 'keluarga_jk', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ]
                                            ])->radioList(
                                                ArrayHelper::map($data_lookup['jenis_kelamin'], 'lookup_id', 'lookup_value'),
                                                [
                                                    'inline' => true,
                                                    'item' => function($index, $label, $name, $checked, $value) {
                                                        $return = '<label class="radio-inlineo">';
                                                            $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" class="styled">';
                                                            $return .= '<i></i>';
                                                            $return .= '<span style="margin-left:5px;">' . $label . '</span>';
                                                        $return .= '</label>';

                                                        return $return;
                                                    }
                                                ]
                                            );
                                            ?>
                                            <div id="error_KeluargaPasienFormkeluarga_jk" style="margin-left:178px;"></div>
                                            <?php
                                            echo $form->field($modelKp, 'keluarga_propinsi_id')
                                                ->dropDownList($ddlPropinsi, 
                                                [
                                                    'id'=>'frm-kp-propinsi_id',
                                                    'class'=>'select2',
                                                    'prompt'=>'— PILIH —'
                                                ]
                                            );

                                            echo $form->field($modelKp, 'keluarga_kabupaten_id')->widget(DepDrop::classname(), [
                                                'data'=>$ddlkabupaten,
                                                'options'=>['id'=>'frm-kp-kabupaten_id','class'=>'select2'],
                                                'pluginOptions'=>[
                                                    'depends'=>['frm-kp-propinsi_id'],
                                                    'placeholder'=>'-- PILIH --',
                                                    'url'=>Url::to(['/master/kabupaten/list-kabupaten?selected='.$modelKp->keluarga_kabupaten_id])
                                                ]
                                            ]);
                    
                                            echo $form->field($modelKp, 'keluarga_kecamatan_id')->widget(DepDrop::classname(), [
                                                'data'=>$ddlkecamatan,
                                                'options'=>['id'=>'frm-kp-kecamatan_id','class'=>'select2'],
                                                'pluginOptions'=>[
                                                    'depends'=>['frm-kp-kabupaten_id'],
                                                    'placeholder'=>'-- PILIH --',
                                                    'url'=>Url::to(['/master/kecamatan/list-kecamatan?selected='.$modelKp->keluarga_kecamatan_id])
                                                ]
                                            ]);
                    
                                            echo $form->field($modelKp, 'keluarga_kelurahan_id')->widget(DepDrop::classname(), [
                                                'data'=>$ddlkelurahan,
                                                'options'=>['id'=>'frm-kp-kelurahan_id','class'=>'select2'],
                                                'pluginOptions'=>[
                                                    'depends'=>['frm-kp-kecamatan_id'],
                                                    'placeholder'=>'-- PILIH --',
                                                    'url'=>Url::to(['/master/kelurahan/list-kelurahan?selected='.$modelKp->keluarga_kelurahan_id])
                                                ]
                                            ]);

                                        ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <?php
                                            echo $form->field($modelKp, 'keluarga_rt', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ],
                                            ])->textInput();

                                            echo $form->field($modelKp, 'keluarga_rw', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ],
                                            ])->textInput();

                                            echo $form->field($modelKp, 'keluarga_alamat', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ]
                                            ])->textArea();

                                            echo $form->field($modelKp, 'keluarga_no_telepon', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ],
                                            ])->textInput();

                                            echo $form->field($modelKp, 'keluarga_pekerjaan_id', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ]
                                            ])->dropDownList($ddlPekerjaan, [
                                                'prompt' => '-'
                                            ]);

                                            echo $form->field($modelKp, 'keluarga_hubungan', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ]
                                            ])->dropDownList(ArrayHelper::map($data_lookup['hubungan_keluarga'], 'lookup_id', 'lookup_value'), [
                                                'prompt' => '-'
                                            ]);

                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                                              
                <div class="row hidden" style="margin-left:15px">
                    <?= Html::submitButton("<i class='fa fa-floppy-o'> Simpan</i>", ['class' => 'btn bg-teal']) ?>
                </div>
            </fieldset>
            <?php
             echo Html::hiddenInput('pendaftaran_id', empty($attrKunjungan) ? "" : $attrKunjungan['pendaftaran_id'], ['id' => 'pendaftaran_id']);
             echo Html::hiddenInput('pj_id', empty($attrPj) ? "" : $attrPj['penanggungjawab_id'], ['id' => 'pj_id']);
             echo Html::hiddenInput('is_deleted_pj', 0, ['id' => 'is_deleted_pj']);
             echo Html::hiddenInput('keluargapasien_id', empty($attrKp) ? "" : $attrKp['keluargapasien_id'], ['id' => 'keluargapasien_id']);
             echo Html::hiddenInput('is_deleted_kp', 0, ['id' => 'is_deleted_kp']);

                $form->type = ActiveForm::TYPE_HORIZONTAL;
            ?>
            <div class="modal fade camera-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="confirm-form">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-inverse">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h5 class="modal-title">Konfirmasi <?= $title; ?></h5>
                        </div>
                        <div class="modal-body">
                            <?= $form->field($model, 'username')->textInput(['readonly' => true]); ?>
                            <?= $form->field($model, 'password')->passwordInput(); ?>
                            <?= $form->field($model, 'reason')->textarea(); ?>
                        </div>
                        <div class="modal-footer">
                            <?=Html::button(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), [
                                'class' => 'btn btn bg-teal btn-sm btn-konfirm',
                                'data-target'=>'form-daftar-rajal'
                            ]); ?>
                            <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
                        </div>
                    </div>
                </div>
            </div>
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

<div class="modal-preview fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="row">
                <div id="my_camera" class="col-md-12"></div>
            </div>
        </div>
    </div>
</div>


<?php
$this->registerJs("
    var arrayJenisIdentitas = [];
    var dataPj = '".json_encode($modelPj)."';
    var dataPjTera = '".json_encode($attrPjTera)."';
    var statePj = '".$isShowPj."'
    var dataKp = '".json_encode($modelKp)."';
    var stateKp = '".$isShowKp."';
    var is_close = '".$is_close."';

    function validateFormatNama(input) {
      input.parent('.form-group').siblings('.help-block').removeClass('error');
      input.parent('.form-group').siblings('.help-block').html('');
      let patern =  /^[^'\"`]+$/;
      let check = patern.test(input.val());

      if (!check && input.val() != '') {
        input.parent('.form-group').addClass('has-error');
        input.siblings('.help-block').html('<i class=\"fa fa-exclamation-circle\"></i>Mengandung karakter yang tidak diperbolehkan (<code>\' \" `</code>)');
        input.siblings('.help-block').addClass('error');
        return false;
      } else {
        input.parent('.form-group').removeClass('has-error');
        return true;
      }

    }

    $(document).on('blur', '.name-validate-class', function () {
      validateFormatNama($(this));
    });


    $('.pickadate').pickadate({
            formatSubmit: 'yyyy-mm-dd',
        });
    function take_snapshot() {
            // take snapshot and get image data
            Webcam.snap( function(data_uri) {
                $('#profilePict').attr('src',data_uri);
            } );
        }

    $('#file_input').on('click',function(){
        $('#profilePict').hide();
        $('.profilePict').hide();
        $('.thumbnail').removeClass('thumbnail');
        $('.file-preview').removeClass('file-preview');
    });

    $(document).on('click', '.fileinput-remove', function(){
        $('#profilePict').show();
        $('.profilePict').show();
    });



  $( document ).ready(function() {
    $('#pjpasienform-pj_jenis_identitas,#pjpasienform-pj_hubungan,#pjpasienform-pj_pengantar').select2();
    $('#keluargapasienform-keluarga_pekerjaan_id,#keluargapasienform-keluarga_hubungan, #keluargapasienform-keluarga_namadepan').select2()
    var input_date_tgl_info = $('#frm-pasien-tanggal_lahir').pickadate({
        editable: true,
        format:'dd-mm-yyyy',
        formatSubmit:'dd-mm-yyyy',
        selectMonths: true,
        selectYears: true,
        onClose: function() {
            $('.datepicker').focus();
        }
    });
    var input_date_tgl_info = $('#frm-pasien-tanggal_lahir').pickadate({
                editable: true,
                onClose: function() {
                    $('.datepicker').focus();
                }
            });
    input_date_tgl_info.pickadate('picker').set('select', '{$model->tanggal_lahir}', { format: 'dd-mm-yyyy' });
    var picker_date = input_date_tgl_info.pickadate('picker');
        $('#btn_addon_tgllahir').on('click',function(event){
            if (picker_date.get('open')) {
                picker_date.close();
            } else {
                picker_date.open();
            }
            event.stopPropagation();
        });
    setTimeout(function(){
        $('#frm-pasien-propinsi_id').trigger('depdrop:change');
        }, 300);
    $('#frm-pasien-tanggal_lahir').trigger('change');
  });
  $(document).on('change', '#frm-pasien-tanggal_lahir', function () {
      var umur = generateUmur($(this).val());
      $('#frm-pasien-umur').val(umur);
  });

  $('#btn-cetak-kartu-pasien').click(function(e){
    var url = window.location.origin;
    var target = $(this).attr('data-target');
    window.open(url+target);
  });

  $('#btn-save').click(function(e){    
    e.preventDefault();
    $('#confirm-form').modal('show');
    return false;
  });

  $('.btn-konfirm').click(function(e){    
    e.preventDefault();
    $('#form-daftar-rajal').submit();
  });

  $('#form-daftar-rajal').submit(function (event) {
        event.preventDefault();

        var next = true;
        var data = new FormData();
        var _value = $(this).serializeArray();
        var file = document.getElementById('file_input').files[0];
        $('#tmp_tanggal_lahir').val($('#frm-pasien-tanggal_lahir').val());

        data.append('InfPencarianPasienForm[photopasien]', file);

        $.each(_value, function(key, value) {
            data.append(value.name, value.value);
        });
        
        // name: PasienForm[reason] -> Already sended by ajax
        // value: asd
        // console.log(_value);

        $(this).docoForm('submit', {
            data: data,
            dataType: false,
            contentType: false,
            processData: false,
            method: 'post',
            isUpload: true,
            success: function (data) {
                if(is_close == 'true') {
                    localStorage.setItem('isPasienUpdated', 'true');
                    // Delay 2 seconds
                    setTimeout(function() {
                        window.close()
                    }, 2000);
                } else {
                    window.location.href = '{$linkBeforeUpdate}'
                }
            },
            error: function (data) {
                if (data.responseJSON.response.data != null || data.responseJSON.response.data != undefined) {
                    response = data.responseJSON.response.data;
                    if (Array.isArray(response?.nama_pasien) && response.nama_pasien.length > 0 && response.nama_pasien[0] != null) {
                        docoNotification('error', 'Proses Gagal!', response.nama_pasien[0]);
                    }
                }

                /*$('.no_identitas_pasien').each(function(key, obj) {
                    if (!$(this).val()) {
                        next = false;

                        $(this).parent().addClass('has-error');
                        $(this).parent().find('.help-block').html('<i class=fa aria-hidden=true></i> &nbsp;No Identitas Pasien cannot be blank.');
                        $(this).parent().find('.fa').addClass('fa-exclamation-circle');

                        docoNotification('error', 'Proses Gagal !', 'Terjadi kesalahan, silahkan cek inputan.');
                    }
                });

                $('.jenis_identitas').each(function(key, obj) {
                    if (!$(this).val()) {
                        next = false;

                        $(this).parent().addClass('has-error');
                        $(this).parent().find('.help-block').html('<i class=fa aria-hidden=true></i> &nbsp;Jenis Identitas cannot be blank.');
                        $(this).parent().find('.fa').addClass('fa-exclamation-circle');

                        docoNotification('error', 'Proses Gagal !', 'Terjadi kesalahan, silahkan cek inputan.');
                    }
                });*/
            }
        });
    });

    $(document).on('click', '.tambah-jenis', function(event) {
        var next = true;
        var html = $('.identitas:last').clone();
        arrayJenisIdentitas = [];
        html.find('span').remove();
        html.find('select').select2();
        html.find('select').val(null).trigger('change.select2');
        html.find('.no_identitas_pasien').val(null);
        html.find('.tambah-jenis').html('X');
        html.find('.tambah-jenis').removeClass('btn-success');
        html.find('.tambah-jenis').addClass('btn-danger');
        html.find('.tambah-jenis').addClass('hapus-jenis');
        html.find('.tambah-jenis').removeClass('tambah-jenis');
        html.find('.tambah-jenis').prop('id', null);

        $('.no_identitas_pasien').each(function(key, obj) {
            if (!$(this).val()) {
                next = false;

                $(this).parent().addClass('has-error');
                $(this).parent().find('.help-block').html('<i class=fa aria-hidden=true></i> &nbsp;No Identitas Pasien cannot be blank.');
                $(this).parent().find('.fa').addClass('fa-exclamation-circle');

                docoNotification('error', 'Proses Gagal !', 'Terjadi kesalahan, silahkan cek inputan.');
            }
        });

        $('.jenis_identitas').each(function(key, obj) {
            if (!$(this).val()) {
                next = false;

                $(this).parent().addClass('has-error');
                $(this).parent().find('.help-block').html('<i class=fa aria-hidden=true></i> &nbsp;Jenis Identitas cannot be blank.');
                $(this).parent().find('.fa').addClass('fa-exclamation-circle');

                docoNotification('error', 'Proses Gagal !', 'Terjadi kesalahan, silahkan cek inputan.');
            }

            arrayJenisIdentitas.push($(this).val());
        });

        if (next) {
            // Append in the last
            $('.identitas:last').after(html);
        }
    });

    $(document).on('click', '.hapus-jenis', function(event) {
        $(this).parent().parent().remove();
    });

    $(document).on('change', '.jenis_identitas', function(event) {
        if (jQuery.inArray($(this).val(), arrayJenisIdentitas) !== -1) {
            $(this).val(null).trigger('change.select2');

            $(this).parent().addClass('has-error');
            $(this).parent().find('.help-block').html('<i class=fa aria-hidden=true></i> &nbsp;Jenis Identitas sudah dipilih.');
            $(this).parent().find('.fa').addClass('fa-exclamation-circle');

            docoNotification('error', 'Proses Gagal !', 'Terjadi kesalahan, silahkan cek inputan.');
        }
    });

    $(document).on('blur', '.no_identitas_pasien', function(event) {
        if ($(this).val()) {
            $(this).parent().removeClass('has-error');
            $(this).parent().find('.help-block').html('');
        }
    });
",View::POS_END,'daftar-rajal');
$this->registerJs($this->render('js/update.js'), View::POS_END, 'update-pasien');
?>

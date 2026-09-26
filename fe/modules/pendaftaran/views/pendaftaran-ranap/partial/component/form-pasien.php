<?php
    use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use kartik\widgets\DepDrop;
    use kartik\widgets\ActiveForm;
    use yii\web\View;
    use app\components\DocoConstants;
?>
<?php
    $form = ActiveForm::begin([
        'id' => 'tipe-pasien',
        'type' => ActiveForm::TYPE_VERTICAL,
        'enableClientValidation'=>false,
        'enableAjaxValidation'=>false,
        // 'formConfig' => ['showErrors' => true, 'labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
    ]);
?>
<div id="form-input-pasien" style="display: none;">
    <div class="form-group" id="form-pasien-content">
        <div class="div-pasien">
            <div class="row">
                <div class="col-md-4">
                    <div class="row col-md-12 identitas">
                        <div class="hidden">
                            <?= $form->field($modelPasien, 'jenisidentitas[]')->dropDownList(ArrayHelper::map($data_lookup['jenis_identitas'], 'lookup_id', 'lookup_value'), [
                                'class' => 'select2 select2pasien jenis_identitas',
                                'id'=>'frm-pasien-jenisidentitas',
                                'prompt' => '— PILIH —',
                            ])->label(Yii::t('fe', 'Jenis Identitas')); ?>
                        </div>
                        <div class="col-sm-12">
                            <?= $form->field($modelPasien, 'no_identitas_pasien[]', [
                                'inputOptions' => [
                                    'id' => 'frm-pasien-no_identitas_pasien',
                                    'class' => 'form-control input-sm',
                                    'maxlength' => 16,
                                    'tabindex' => '1'
                                ],
                                'addon' => [
                                    'prepend' => [
                                        'content'=> Yii::t('fe', 'KTP'),
                                        'options'=>[]
                                    ]
                                ]
                            ])->textInput(['class' => 'no_identitas_pasien'])->label('NIK'); ?>
                        </div>
                    </div>
                    <div class="row col-md-12">
                        <div class="col-sm-4">
                            <?= $form->field($modelPasien, 'namadepan')->dropDownList(ArrayHelper::map($data_lookup['nama_depan'], 'lookup_id', 'lookup_value'), [
                                'class' => 'select2 select2pasien',
                                'id'=>'frm-pasien-namadepan',
                                'prompt' => '— PILIH —',
                                'tabindex' => '2'
                            ])->label(Yii::t('fe', 'Sebutan')); ?>
                        </div>
                        <div class="col-sm-8">
                            <?= $form->field($modelPasien, 'nama_pasien', [
                                'inputOptions' => [
                                    'id' => 'frm-pasien-nama_pasien',
                                    'class' => 'form-control input-sm has star',
                                    'tabindex' => '3'
                                ]
                            ]); ?>
                        </div>
                    </div>
                    <div class="row col-md-12">
                        <div class="col-sm-6">
                            <?= $form->field($modelPasien, 'tempat_lahir', [
                                'inputOptions' => [
                                    'id' => 'frm-pasien-tempat_lahir',
                                    'class' => 'form-control input-sm',
                                    'tabindex' => '4'
                                ]
                            ]) ?>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group <?= (isset($instalasi_id)) ? (($instalasi_id != DocoConstants::INSTALASI_ID_RD) ? 'required' : '') : 'required' ?>">
                                <label class="control-label has-star">Tanggal Lahir</label><span> dd-mm-yyyy</span>
                                <div class="input-group inline-datepicker">
                                        <input type="text" id="frm-pasien-tanggal_lahir" class="form-control" name="PasienForm[tanggal_lahir]" data-mask="99-99-9999" tabindex="5">
                                        <span class="input-group-addon"><i id="btn_addon_tgllahir" class="fa fa-calendar "></i></span>
                                    </div>
                            </div>
                        </div>
                    </div>
                    <div class="row col-md-12">
                        <div class="col-sm-12">
                            <?= $form->field($modelPasien, 'umur', [
                                'inputOptions' => [
                                    'id' => 'frm-pasien-umur',
                                    'readonly' => true,
                                    'tabindex' => '6'
                                ]
                            ]); ?>
                        </div>
                    </div>
                    <div class="row col-md-12">
                        <div class="col-sm-12">
                            <?= $form->field($modelPasien, 'jeniskelamin')
                                ->radioList(
                                    ArrayHelper::map($data_lookup['jenis_kelamin'], 'lookup_id', 'lookup_value'),
                                    ['inline'=>true, 'id'=>'frm-pasien-jeniskelamin', 'tabindex' => '7']
                                )
                                ->label(Yii::t('fe', 'Jenis Kelamin'));
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row col-md-12" style="padding:0;">
                        <div class="col-sm-6">
                            <?= $form->field($modelPasien, 'golongandarah')
                                ->dropDownList(
                                    ArrayHelper::map($data_lookup['golongan_darah'], 'lookup_id', 'lookup_value'),
                                    [
                                        'id'=>'frm-pasien-golongandarah',
                                        'class' => 'select2',
                                        'prompt' => '-- PILIH --',
                                        'tabindex' => '8'
                                    ]
                                )
                                ->label(Yii::t('fe', 'Golongan Darah'));
                            ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($modelPasien, 'statusperkawinan')
                                ->dropDownList(
                                    ArrayHelper::map($data_lookup['status_perkawinan'], 'lookup_id', 'lookup_value'),
                                    ['id'=>'frm-pasien-statusperkawinan', 'class'=>'select2  select2pasien', 'prompt'=>'— PILIH —', 'tabindex' => '9']
                                )
                                ->label(Yii::t('fe', 'Status Perkawinan'))
                            ?>
                        </div>
                    </div>
                    <div class="row col-md-12" style="padding:0;">
                        <div class="col-sm-6">
                            <?= $form->field($modelPasien, 'propinsi_id')
                                ->dropDownList(
                                    ArrayHelper::map($data_master['propinsi'], 'propinsi_id', 'propinsi_nama'),
                                    [
                                        'id'=>'frm-pasien-propinsi_id',
                                        'class'=>'select2 select2pasien',
                                        'options'=>$optionsProv,
                                        'prompt'=>'— PILIH —',
                                        'tabindex' => '10'
                                    ]
                                )
                                ->label(Yii::t('fe', 'Propinsi'));
                            ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($modelPasien, 'kabupaten_id')
                                ->dropDownList(
                                    [],
                                    ['id'=>'kabupatenForm', 'class'=>'select2 select2pasien frm-pasien-kabupaten_id', 'prompt'=>'— PILIH —', 'tabindex' => '11']
                                )->label(Yii::t('fe', 'Kabupaten/Kota'))
                            ?>
                        </div>
                    </div>
                    <div class="row col-md-12" style="padding:0;">
                        <div class="col-sm-6">
                            <?= $form->field($modelPasien, 'kecamatan_id')
                                ->dropDownList(
                                    [],
                                    ['id'=>'frm-pasien-kecamatan_id', 'class'=>'select2 select2pasien','prompt'=>'— PILIH —', 'tabindex' => '12']
                                )->label(Yii::t('fe', 'Kecamatan'))
                            ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($modelPasien, 'kelurahan_id')
                                ->dropDownList(
                                    [],
                                    ['id'=>'frm-pasien-kelurahan_id', 'class'=>'select2 select2pasien','prompt'=>'— PILIH —', 'tabindex' => '13']
                                )->label(Yii::t('fe', 'Kelurahan'))
                            ?>
                        </div>
                    </div>
                    <div class="row col-md-12" style="padding:0;">
                        <div class="col-sm-6">
                            <?= $form->field($modelPasien, 'rt', [
                                'inputOptions' => [
                                    'id' => 'frm-pasien-rt',
                                    'class' => 'form-control input-sm',
                                    'maxlength' => 2,
                                    'tabindex' => 14
                                ]
                            ]) ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($modelPasien, 'rw', [
                                'inputOptions' => [
                                    'id' => 'frm-pasien-rw',
                                    'class' => 'form-control input-sm',
                                    'maxlength' => 2,
                                    'tabindex' => 15
                                ]
                            ]) ?>
                        </div>
                    </div>
                    <div class="row col-md-12" style="padding:0;">
                        <div class="col-sm-12">
                            <?= $form->field($modelPasien, 'no_telepon_pasien', [
                                'inputOptions' => [
                                    'id' => 'frm-pasien-no_telepon_pasien',
                                    'maxlength' => 13,
                                    'tabindex' => 16
                                ]
                            ]); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row col-md-12" style="padding:0;">
                        <div class="col-sm-4">
                            <?= $form->field($modelPasien, 'alamatdepan')
                                ->dropDownList(
                                    ArrayHelper::map($data_lookup['alamat_depan'], 'lookup_id', 'lookup_value'),
                                    ['id'=>'frm-pasien-alamatdepan', 'class'=>'select2 select2pasien','prompt'=>'— PILIH —', 'tabindex' => 17]
                                )
                            ?>
                        </div>
                        <div class="col-sm-8">
                            <?= $form->field($modelPasien, 'alamat_pasien')->textArea([
                                'id' => 'frm-pasien-alamat_pasien',
                                'rows' => 2,
                                'tabindex' => 18
                            ]); ?>
                        </div>
                    </div>
                    <div class="row col-md-12" style="padding:0;">
                        <div class="col-sm-6">
                            <?php $modelPasien->warga_negara = '308'; ?>
                            <?= $form->field($modelPasien, 'warga_negara')
                                ->dropDownList(
                                    ArrayHelper::map($data_lookup['warga_negara'], 'lookup_id', 'lookup_value'),
                                    [
                                        'id'=>'frm-pasien-warga_negara',
                                        'class'=>'select2 select2pasien',
                                        'prompt'=>'— PILIH —',
                                        'tabindex' => 19
                                    ]
                                )
                            ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($modelPasien, 'suku_id')
                                ->dropDownList(
                                    ArrayHelper::map($data_master['suku'], 'suku_id', 'suku_nama'),
                                    ['id'=>'frm-pasien-suku_id', 'class'=>'select2 select2pasien','prompt'=>'— PILIH —', 'tabindex' => 20]
                                )
                                ->label(Yii::t('fe', 'Suku'));
                            ?>
                        </div>
                        <div class="col-sm-5" style="display:none;">
                            <?= $form->field($modelPasien, 'alamatemail', [
                                'inputOptions' => ['id' => 'frm-pasien-alamatemail']
                            ])->label(Yii::t('fe', 'Alamat Email')); ?>
                        </div>
                    </div>
                    <div class="row col-md-12" style="display:none;">
                        <div class="col-sm-6">
                            <?= $form->field($modelPasien, 'nama_ibu', [
                                'inputOptions' => ['id' => 'frm-pasien-nama_ibu']
                            ]) ?>   
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($modelPasien, 'nama_ayah', [
                                'inputOptions' => ['id' => 'frm-pasien-nama_ayah']
                            ]) ?>
                        </div>
                    </div>
                    <div class="row col-md-12" style="display:none;">
                        <div class="col-sm-3">
                            <?= $form->field($modelPasien, 'anakke', [
                                'inputOptions' => [
                                    'id' => 'frm-pasien-anakke',
                                    'class' => 'form-control input-sm'
                                ]
                            ]) ?>
                        </div>
                        <div class="col-sm-3">
                            <?= $form->field($modelPasien, 'jumlah_bersaudara', [
                                'inputOptions' => [
                                    'id' => 'frm-pasien-jumlah_bersaudara',
                                    'class' => 'form-control input-sm'
                                ]
                            ]) ?>
                        </div>
                    </div>
                    <div class="row col-md-12" style="padding:0;">
                        <div class="col-sm-6">
                            <?= $form->field($modelPasien, 'pendidikan_id')
                                ->dropDownList(
                                    ArrayHelper::map($data_master['pendidikan'], 'pendidikan_id', 'pendidikan_nama'),
                                    ['id'=>'frm-pasien-pendidikan_id', 'class'=>'select2 select2pasien','prompt'=>'— PILIH —', 'tabindex' => 21]
                                )->label(Yii::t('fe', 'Pendidikan'))
                            ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($modelPasien, 'pekerjaan_id')
                                ->dropDownList(
                                    ArrayHelper::map($data_master['pekerjaan'], 'pekerjaan_id', 'pekerjaan_nama'),
                                    ['id'=>'frm-pasien-pekerjaan_id', 'class'=>'select2 select2pasien','prompt'=>'— PILIH —', 'tabindex' => 22]
                                )->label(Yii::t('fe', 'Pekerjaan'))
                            ?>
                        </div>
                    </div>
                    <div class="row col-md-12" style="padding:0;">
                        <div class="col-sm-6">
                            <?= $form->field($modelPasien, 'bahasa_sehari')
                                ->dropDownList(
                                    ArrayHelper::map($data_lookup['bahasa_sehari'], 'lookup_id', 'lookup_value'),
                                    ['id'=>'frm-pasien-bahasa_sehari', 'class'=>'select2 select2pasien','prompt'=>'— PILIH —', 'tabindex' => 23]
                                )
                            ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($modelPasien, 'agama')
                                ->dropDownList(
                                    ArrayHelper::map($data_lookup['agama'], 'lookup_id', 'lookup_value'),
                                    ['id'=>'frm-pasien-agama', 'class'=>'select2 select2pasien','prompt'=>'— PILIH —', 'tabindex' => 24]
                                )
                            ?>
                        </div>
                    </div>
                    <div class="row col-md-12" style="padding:0;">
                        <div class="col-sm-6 div-data-keluarga has-error">
                            <?php
                                echo Html::button(Yii::t('fe', 'Data Keluarga'),[
                                    'class' => 'btn btn-info btn-sm',
                                    'id'=>'btn-data-keluarga',
                                    'data-toggle' => 'modal',
                                    //'data-target' => '#modal_data_keluarga',
                                ]);
                            ?>
                            <div class="help-block-data-keluarga"><span style="color: black;">Data Keluarga Harus Diisi</span> <span style="color: red;">*</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade camera-modal-lg" tabindex="false" role="dialog" aria-labelledby="mySmallModalLabel" id="modal_data_keluarga">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-inverse">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h5 class="modal-title">Data Keluarga</h5>
                    </div>
                    <div class="modal-body">
                        <div class="col-md-12">
                            <div class="col-sm-6">
                                <div class="row col-md-12">
                                    <div class="col-sm-4">
                                        <?= $form->field($modelKp, 'keluarga_namadepan')
                                        ->dropDownList(ArrayHelper::map($data_lookup['nama_depan'], 'lookup_id', 'lookup_value'), [
                                            'class'=>'select2 select2pasien',
                                            'prompt' => '— PILIH —',
                                        ])->label(Yii::t('fe', 'Sebutan')); ?>
                                    </div>
                                    <div class="col-sm-8">
                                        <?= $form->field($modelKp, 'keluarga_nama', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-7',
                                                'tabindex' => '26'
                                            ]
                                        ])->textInput(); ?>
                                    </div>
                                </div>
                                <div class="hidden">
                                    <div class="col-sm-12">
                                        <?php
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
                                    </div>
                                </div>
                                <div class="row col-md-12">
                                    <div class="col-sm-6">
                                        <?= $form->field($modelKp, 'keluarga_propinsi_id', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ]
                                            ])->dropDownList(
                                                ArrayHelper::map($data_master['propinsi'], 'propinsi_id', 'propinsi_nama'),
                                                [
                                                    'id'=>'form-keluarga-propinsi_id',
                                                    'class'=>'select2 select2pasien',
                                                    'options'=>$optionsProv,
                                                    'prompt'=>'— PILIH —'
                                                ]
                                            )->label(Yii::t('fe', 'Propinsi'));
                                        ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <?= $form->field($modelKp, 'keluarga_kabupaten_id', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ]
                                            ])->widget(DepDrop::classname(), [
                                                'data'=>$ddlkabupaten,
                                                'options'=>['id'=>'form-keluarga-kabupaten_id','class'=>'select2'],
                                                'pluginOptions'=>[
                                                    'depends'=>['form-keluarga-propinsi_id'],
                                                    'placeholder'=>'-- PILIH --',
                                                    'url'=>Url::to(['/master/kabupaten/list-kabupaten?selected='.$modelKp->keluarga_kabupaten_id])
                                                ]
                                            ]);
                                        ?>
                                    </div>
                                </div>
                                <div class="row col-md-12">
                                    <div class="col-sm-6">
                                        <?= $form->field($modelKp, 'keluarga_kecamatan_id', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ]
                                            ])->widget(DepDrop::classname(), [
                                                'data'=>$ddlkecamatan,
                                                'options'=>['id'=>'form-keluarga-kecamatan_id','class'=>'select2'],
                                                'pluginOptions'=>[
                                                    'depends'=>['form-keluarga-kabupaten_id'],
                                                    'placeholder'=>'-- PILIH --',
                                                    'url'=>Url::to(['/master/kecamatan/list-kecamatan?selected='.$modelKp->keluarga_kecamatan_id])
                                                ]
                                            ]);
                                        ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <?= $form->field($modelKp, 'keluarga_kelurahan_id', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ]
                                            ])->widget(DepDrop::classname(), [
                                                'options'=>['id'=>'form-keluarga-kelurahan_id','class'=>'select2'],
                                                'pluginOptions'=>[
                                                    'depends'=>['form-keluarga-kecamatan_id'],
                                                    'placeholder'=>'-- PILIH --',
                                                    'url'=>Url::to(['/master/kelurahan/list-kelurahan?selected='.$modelKp->keluarga_kelurahan_id])
                                                ]
                                            ]);
                                        ?>
                                    </div>
                                </div>
                                <div class="row col-md-12">
                                    <div class="col-sm-3">
                                        <?= $form->field($modelKp, 'keluarga_rt', [
                                            'inputOptions' => [
                                                'id' => 'form-keluarga-rt',
                                                'class' => 'form-control input-sm',
                                                'maxlength' => 2,
                                                'tabindex' => '31'
                                            ]
                                        ]) ?>
                                    </div>
                                    <div class="col-sm-3">
                                        <?= $form->field($modelKp, 'keluarga_rw', [
                                            'inputOptions' => [
                                                'id' => 'form-keluarga-rw',
                                                'class' => 'form-control input-sm',
                                                'maxlength' => 2,
                                                'tabindex' => '32'
                                            ]
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-12">
                                    <div class="col-sm-4">
                                        <?= $form->field($modelKp, 'alamatdepan', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ]
                                            ])->dropDownList(
                                            ArrayHelper::map($data_lookup['alamat_depan'], 'lookup_id', 'lookup_value'),
                                                [
                                                    'class'=>'select2 select2pasien',
                                                    'prompt' => '— PILIH —',
                                                ]
                                            )->label(Yii::t('fe', 'Sebutan Jalan'))
                                        ?>
                                    </div>
                                    <div class="col-sm-8">
                                        <?= $form->field($modelKp, 'keluarga_alamat', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7',
                                                    'tabindex' => '34'
                                                ]
                                            ])->textArea();
                                        ?>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="col-sm-12">
                                        <?= $form->field($modelKp, 'keluarga_no_telepon', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ],
                                                'inputOptions' => [
                                                    'maxlength' => 12,
                                                    'tabindex' => '35'
                                                ]
                                            ])->textInput();
                                        ?>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="col-sm-12">
                                        <?= $form->field($modelKp, 'keluarga_pekerjaan_id', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ]
                                            ])->dropDownList(
                                            ArrayHelper::map($data_master['pekerjaan'], 'pekerjaan_id', 'pekerjaan_nama'),
                                                [
                                                    'class'=>'select2 select2pasien',
                                                    'prompt' => '— PILIH —',
                                                ]
                                            )->label(Yii::t('fe', 'Pekerjaan'))
                                        ?>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="col-sm-12">
                                        <?= $form->field($modelKp, 'keluarga_hubungan', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-7'
                                                ]
                                            ])->dropDownList(ArrayHelper::map($data_lookup['hubungan_keluarga'], 'lookup_id', 'lookup_value'), [
                                                'class'=>'select2 select2pasien',
                                                'prompt' => '— PILIH —',
                                            ]); 
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Batal'),['id' => 'btn-data-keluarga-batal', 'class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
                        <?=Html::button(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'),['class' => 'btn btn-info btn-sm', 'data-dismiss' => 'modal']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?= $form->field($modelPasien, 'additional_data')->hiddenInput(['value'=> (isset($instalasi_id)) ? $instalasi_id : ""])->label(false);?>
</div>
<?php ActiveForm::end(); ?>

<?php $this->registerJs("
    var propinsi_id = '".$modelPasien->propinsi_id."';
    var kabupaten_id = '".$modelPasien->kabupaten_id."';
    var arrayJenisIdentitas = [];

    $(document).on('click', '.tambah-jenis', function(event) {
        var next = true;
        var html = $('.identitas:last').clone();
        arrayJenisIdentitas = [];
        html.find('span').remove();
        html.find('select').select2();
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
        $(this).parent().parent().parent().remove();
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

    $(document).ready(function($) {
        $('#frm-pasien-namadepan').attr('tabindex', 2);
        $('#frm-pasien-golongandarah').attr('tabindex', 8);
        $('#frm-pasien-statusperkawinan').attr('tabindex', 9);
        $('#frm-pasien-propinsi_id').attr('tabindex', 10);
        $('#kabupatenForm').attr('tabindex', 11);
        $('#frm-pasien-kecamatan_id').attr('tabindex', 12);
        $('#frm-pasien-kelurahan_id').attr('tabindex', 13);
        $('#frm-pasien-alamatdepan').attr('tabindex', 17);
        $('#frm-pasien-warga_negara').attr('tabindex', 19);
        $('#frm-pasien-suku_id').attr('tabindex', 20);
        $('#frm-pasien-pendidikan_id').attr('tabindex', 21);
        $('#frm-pasien-pekerjaan_id').attr('tabindex', 22);
        $('#frm-pasien-bahasa_sehari').attr('tabindex', 23);
        $('#frm-pasien-agama').attr('tabindex', 24);

        $('#keluargapasienform-keluarga_namadepan').attr('tabindex', 25);
        $('#keluargapasienform-keluarga_nama').attr('tabindex', 26);
        $('#form-keluarga-propinsi_id').attr('tabindex', 27);
        $('#form-keluarga-kabupaten_id').attr('tabindex', 28);
        $('#form-keluarga-kecamatan_id').attr('tabindex', 29);
        $('#form-keluarga-kelurahan_id').attr('tabindex', 30);
        $('#keluargapasienform-alamatdepan').attr('tabindex', 33);
        $('#keluargapasienform-keluarga_alamat').attr('tabindex', 34);
        $('#keluargapasienform-keluarga_no_telepon').attr('tabindex', 35);
        $('#keluargapasienform-keluarga_pekerjaan_id').attr('tabindex', 36);
        $('#keluargapasienform-keluarga_hubungan').attr('tabindex', 37);
    });
    

", View::POS_END) ?>

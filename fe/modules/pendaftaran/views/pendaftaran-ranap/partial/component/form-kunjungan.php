<?php
    use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use kartik\widgets\DepDrop;
    use kartik\select2\Select2;
    use yii\web\JsExpression;
    use app\components\DocoConstants;
?>
<div id="form-input-kunjugan" style="display: none;">
    <div id="form-kunjungan-content">
        <div class='col-md-6'>
            <?= 
                $form->field($modelAdmisi, 'tgl_admisi', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ],
                    'addon' => ['append' => [
                        'content' => '<i class="fa fa-calendar"></i>'
                    ]
                ]
                ])->textInput([
                    'placeholder' => $modelAdmisi->getAttributeLabel('tgl_pendaftaran'),
                    'class' => 'form-control input-sm pickadate',
                    'id' => 'datetime',
                    'autocomplete' => "off",
                    'readonly' => true
                ]);
            ?>

            <?=Html::activeHiddenInput($modelKunjungan, 'jeniskasuspenyakit_id', ['value'=>23])?>

            <div class="row">
                <div class="col-md-3">
                    <?=
                        $form->field($modelAdmisi, 'hakkelas_id', [
                            'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-4',
                                'wrapper' => 'col-md-7'
                            ],
                        ])->dropDownList($kelaspelayanan, [
                            'class' => 'selectKp',
                            'id'=>'hakkelas_id',
                            'prompt' => Yii::t('fe', '--Pilih--')
                        ]);
                    ?>
                </div>
                <div class="col-md-4">
                    <?=
                        $form->field($modelAdmisi, 'kelaspermintaan_id', [
                            'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-4',
                                'wrapper' => 'col-md-7'
                            ],
                        ])->dropDownList($kelaspelayanan, [
                            'class' => 'selectKp',
                            'id'=>'kelaspermintaan_id',
                            'prompt' => Yii::t('fe', '--Pilih--')
                        ]);
                    ?>
                </div>
                <div class="col-md-5">
                <?=
                    $form->field($modelKunjungan, 'kelaspelayanan_id', [
                        'horizontalCssClasses' => [
                            'label' => 'text-left control-label col-sm-4',
                            'wrapper' => 'col-md-7'
                        ],
                        'addon' => [
                            'append' => [
                                'content'=>Html::button(Yii::t('fe','Kamar'), [
                                    'id'=>'btnCariKamar',
                                    'class' => 'btn btn-default',
                                ]),
                                'asButton'=>true
                            ]
                        ]
                    ])->dropDownList($kelaspelayanan, [
                        'class' => 'selectKp',
                        'id'=>'kelaspelayanan_id',
                        'prompt' => Yii::t('fe', '--Pilih--')
                    ])->label('Kelas Perawatan');
                ?>
                </div>
            </div>

            <div class="row">
                <!-- <div class="col-md-12"> -->
                    <!-- <div class="form-group highlight-addon field-ruangan_id required"> -->
                        <div class="col-md-6">
                            <?= $form->field($modelAdmisi, 'kamarruangan_nokamar', [
                                'inputOptions'=>['id'=>'nokamar', 'readonly'=>true],
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-7'
                                ],
                            ]); ?>
                        </div>

                        <div class="col-md-6" style="margin-top: 25px;">
                            <label class="control-label has-star">Ruangan : </label>
                            <span id="ruanganLabelValue" style="color: green;font-weight: bold;"></span>
                            <?=Html::activeHiddenInput($modelKunjungan, 'ruangan_id', ['id'=> 'ruanganIdHidden'])?>
                        </div>
                    <!-- </div> -->
                <!-- </div> -->
            </div>
            <div class="col-md-12 is_pasientitipan hidden">
                <?= $form->field($modelAdmisi, 'is_pasientitipan', [
                    'options' => [
                        'tag' => false,
                    ],
                ])->checkbox([
                    'label' => 'Kamar Titipan',
                    'value' => 1,
                    'class' => 'styled action-checked',
                ])->label(false);
            ?>
            </div>
            <div class="kelas_ditagihkan hidden">
                <?= $form->field($modelAdmisi, 'kelas_ditagihkan_id', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ],
                'addon' => [
                    'append' => [
                        'content' => Html::button(Yii::t('fe','Kamar'), [
                            'id' => 'btnCariKamarTitipan',
                            'class' => 'btn btn-default',
                        ]),
                        'asButton'=>true
                    ]
                ]
            ])->dropDownList($kelaspelayanan, [
                'class' => 'selectKt',
                'id' => 'kelas_ditagihkan_id',
                'prompt' => Yii::t('fe', '--Pilih--')
            ]) ?>
                <div class="form-group highlight-addon field-ruangan_titipan_id required">
                    <div class="col-md-8">
                        <label class="control-label has-star">Ruangan Titipan</label>
                        <span id="ruanganTitipanLabelValue" style="color: green;font-weight: bold;"></span>
                        <?=Html::activeHiddenInput($modelAdmisi, 'ruangan_titipan_id', ['id'=> 'ruanganTitipanIdHidden'])?>
                    </div>
                </div>
            </div>
            <!-- <p class="space_p"></p> -->
            <div class="row">
                <div class="col-md-12">
                    <?=
                        $form->field($modelAdmisi, 'prosedurmasuk_id', [
                            'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-4',
                                'wrapper' => 'col-md-7'
                            ],
                        ])->dropDownList($prosedurMasuk, [
                            'class' => 'selectProsedurMasuk',
                            'id'=>'prosedurmasuk_id',
                            'prompt' => Yii::t('fe', '--Pilih--')
                        ]);
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?= 
                        $form->field($modelAdmisi, 'diagnosa_awal', [
                            'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-4',
                                'wrapper' => 'col-md-7'
                            ]
                        ])->textArea(); 
                    ?>
                </div>
            </div>

        </div>
        <div class="col-md-6">
            <?= 
                $form->field($modelAdmisi, 'dokterpengirim_id',[
                   'horizontalCssClasses' => [
                       'label' => 'col-md-4',
                       'wrapper' => 'col-md-8'
                   ]
                ])->dropDownList([], [
                    'prompt' => 'Pilih Dokter',
                    'placeholder' => Yii::t('fe','Dokter Pengirim')
                ]);
            ?>
            <?= 
                $form->field($modelAdmisi, 'pegawai_id', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
                ])->dropDownList([], [
                    'id' => 'pegawai_id',
                    'class' => 'form-control',
                    'disabled' => true,
                    'prompt' => Yii::t('fe', '--Pilih--')
                ])->label('Dokter DPJP');
            ?>
            <div class="row dokter-konsul">
                <div class="col-md-5">
                    <?= 
                        $form->field($modelAdmisi, 'dokterkonsul_id[]',[
                        'horizontalCssClasses' => [
                            'label' => 'col-md-4',
                            'wrapper' => 'col-md-8'
                        ]
                        ])->dropDownList([], [
                            'prompt' => 'Pilih Dokter',
                            'placeholder' => Yii::t('fe','Dokter Konsul')
                        ]);
                    ?>
                </div>
                <div class="col-sm-1">
                    <div class="form-group highlight-addon has-size-sm">
                        <label class="control-label"><b style="color:white;float:right;">Aksi</b></label>
                        <?= Html::button('+', [
                            'class' => 'btn btn-success tambah-dokter-konsul'
                        ]) ?>
                    </div>
                    <div class="help-block"></div>
                </div>
            </div>
            <?= $form->field($modelAdmisi, 'keterangan', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
            ])->textArea(); 
            ?>
            <?php if (isset($is_limit_tagihan) && $is_limit_tagihan) { ?>
            <?= $form->field($modelKunjungan, 'limit_tagihan', [
            'horizontalCssClasses' => [
                'label' => 'text-left control-label col-sm-4',
                'wrapper' => 'col-md-7'
            ],
            'addon' => [
                'prepend' => [
                    'asButton' => false,
                    'content' => 'Rp.'
                ]
            ]
        ])->textInput([
                'placeholder' => $modelKunjungan->getAttributeLabel('limit_tagihan'),
                'class' => 'form-control input-sm doco-number',
                'id' => 'limit_tagihan'
        ]) ?>
            <?php } ?>

            <!-- hidden field -->
            <?php
        echo $form->field($modelAdmisi, 'bookingkamar_id', [
            'inputOptions'=>[
                'id'=>'bookingkamar_id'
            ]
        ])->hiddenInput()->label(false);
        ?>
            <?php
        echo $form->field($modelAdmisi, 'kamarruangan_id', [
            'inputOptions'=>[
                'id'=>'kamarruangan_id'
            ]
        ])->hiddenInput()->label(false);
        ?>
            <?php
        echo $form->field($modelAdmisi, 'kamar_titipan_id', [
            'inputOptions'=>[
                'id'=>'kamar_titipan_id'
            ]
        ])->hiddenInput()->label(false);
        ?>
            <?php
        echo $form->field($modelAdmisi, 'kamartempattidur_id', [
            'inputOptions'=>[
                'id'=>'kamartempattidur_id'
            ]
        ])->hiddenInput()->label(false);
        ?>

            <?php
        echo $form->field($modelAdmisi, 'tempattidur_titipan_id', [
            'inputOptions'=>[
                'id'=>'tempattidur_titipan_id'
            ]
        ])->hiddenInput()->label(false);
        ?>

            <?=
        Html::hiddenInput('temp_ruangan_id', '', ['id'=>'temp_ruangan_id']);
        ?>

            <?php
            if (!empty($id_booking)) {
                echo $form->field($modelAdmisi, 'pendaftaran_id', [
                    'inputOptions' => [
                        'id' => 'pendaftaran-id'
                    ]
                ])->hiddenInput()->label(false);
            } else {
                echo Html::activeHiddenInput($modelAdmisi, 'pendaftaran_id', ['class'=>'pendaftaran-id']);
            }
        ?>
            <?=Html::activeHiddenInput($modelAdmisi, 'pasien_id', ['class'=>'pasien-id'])?>
            <?=Html::activeHiddenInput($modelAdmisi, 'instalasi_id', ['value'=>$instalasi_id])?>
            <?=Html::activeHiddenInput($modelAdmisi, 'asuransipasien_id', ['class'=>'asuransipasien-id'])?>
            <?=Html::activeHiddenInput($modelAdmisi, 'bpjs_id', ['class'=>'bpjs-id'])?>
            <?=Html::hiddenInput('HiddenPenanggungJawab', 0, ['id' => 'HiddenPenanggungJawab']) ?>
            <input type="hidden" name="pasientitipan" id="pasienTitipanValue" value="0">
            <input type="hidden" name="pasienaps" id="pasienApsValue" value="0">
            <input type="hidden" name="kelaspelayanan_selected" id="kelasPelayananSelected" value="">
            <input type="hidden" name="kelaspelayanantagihan_selected" id="kelasPelayananTagihanSelected" value="">
        </div>
    </div>
</div>
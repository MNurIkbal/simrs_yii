<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
?>

<style type="text/css">
    .padding-top {
    padding-top: 10px;
    }
    .asi {
    padding-left: 0px;
    }
</style>

<div class="row riwayat-persalinan">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h6 class="panel-title"><?=Yii::t('fe', 'Riwayat Persalinan Ibu')?></h6>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body">
                <br>
                <div class="col-md-6">

                    <div class="form-group">
                        <label class="control-label col-sm-3">Riwayat Kandungan</label>
                        <div class="col-sm-3">
                            <?= Html::activeCheckBox($model, 'paritas', [
                                'value' => '1',
                                'label' => 'Paritas',
                                'id' => 'paritas',
                                'class' => 'padding-top checkbox-disabled'
                            ]) ?>
                        </div>
                        <label class="control-label col-sm-6">
                            <?= Html::activeTextInput($model, 'paritas_lainnya', ['class' => 'form-control paritas-lainnya', ]) ?>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3"></label>
                        <div class="col-sm-3">
                            <?= Html::activeCheckBox($model, 'abortus', [
                                'value' => '1',
                                'label' => 'Abortus',
                                'id' => 'abortus',
                                'class' => 'checkbox-disabled'
                            ]) ?>
                        </div>
                        <label class="control-label col-sm-6">
                            <?= Html::activeTextInput($model, 'abortus_lainnya', ['class' => 'form-control abortus-lainnya', ]) ?>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3"></label>
                        <div class="col-sm-3">
                            <?= Html::activeCheckBox($model, 'meninggal', [
                                'value' => '1',
                                'label' => 'Meninggal',
                                'id' => 'meninggal',
                                'class' => 'checkbox-disabled'
                            ]) ?>
                        </div>
                        <label class="control-label col-sm-6">
                            <?= Html::activeTextInput($model, 'meninggal_lainnya', ['class' => 'form-control meninggal-lainnya', ]) ?>
                        </label>
                    </div>

                    <div class="form-group">
                    <label class="control-label col-sm-3">Riwayat Partus ditolong oleh</label>
                        <div class="col-sm-3">
                            <?= Html::activeCheckBox($model, 'partus_dokter', [
                                'value' => '1',
                                'label' => 'Dokter',
                                'id' => 'partus_dokter',
                            ]) ?>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="control-label col-sm-3"></label>
                        <div class="col-sm-3" style="margin-top:-15px;">
                            <?= Html::activeCheckBox($model, 'partus_bidan', [
                                'value' => '1',
                                'label' => 'Bidan',
                                'id' => 'partus_bidan',
                            ]) ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3"></label>
                        <div class="col-sm-3">
                            <?= Html::activeCheckBox($model, 'partus', [
                                'value' => '1',
                                'label' => 'Lain-lain',
                                'id' => 'partus',
                                'class' => 'checkbox-disabled'
                            ]) ?>
                        </div>
                        <label class="control-label col-sm-6">
                            <?= Html::activeTextInput($model, 'partus_lainnya', ['class' => 'form-control partus-lainnya', ]) ?>
                        </label>
                    </div>
                                <br>
                    <div class="form-group">
                        <label class="control-label col-sm-3"><?=$model->attributeLabels()['lama_kehamilan']?></label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <?=Html::activeTextInput($model, 'lama_kehamilan', ['class'=>'form-control docoNumberOnly'])?>
                                <span class="input-group-addon" id="basic-addon2"><?=Yii::t('fe', 'Minggu')?></span>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-md-6">

                    <div class="form-group">
                        <div class="col-sm-5">
                            <?=$form->field($model, 'komplikasi',[
                                'horizontalCssClasses' => [
                                'wrapper' => 'col-md-5'
                            ]
                            ])->radioList($data_komplikasi,['class' => 'fisik'])->label('Komplikasi',['class' => 'control-label has-star col-sm-7']);?>
                        </div>
                        <br>
                        <label class="control-label col-sm-4">
                        <?= Html::activeTextInput($model, 'komplikasi_lainnya', ['class' => 'form-control komplikasi-lainnya', ]) ?>
                        </label>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-5">
                            <?=$form->field($model, 'neotanus',[
                                'horizontalCssClasses' => [
                                'wrapper' => 'col-md-5'
                            ]
                            ])->radioList($data_neotanus,['class' => 'fisik'])->label('Masalah Neotanus',['class' => 'control-label has-star col-sm-7']);?>
                        </div>
                        <br>
                        <label class="control-label col-sm-4">
                        <?= Html::activeTextInput($model, 'neotanus_lainnya', ['class' => 'form-control neotanus-lainnya', ]) ?>
                        </label>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-5">
                            <?=$form->field($model, 'maternal',[
                                'horizontalCssClasses' => [
                                'wrapper' => 'col-md-5'
                            ]
                            ])->radioList($data_maternal,['class' => 'fisik'])->label('Masalah Maternal',['class' => 'control-label has-star col-sm-7']);?>
                        </div>
                        <br>
                        <label class="control-label col-sm-4">
                        <?= Html::activeTextInput($model, 'maternal_lainnya', ['class' => 'form-control maternal-lainnya', ]) ?>
                        </label>
                    </div>

                </div>
            </div>
            
        </div>
    </div>
</div>



<div class="row riwayat-tumbuh-kembang">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h6 class="panel-title"><?=Yii::t('fe', 'Riwayat Tumbuh Kembang')?></h6>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body">
                <br>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-sm-4">BB anak saat lahir</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <?=Html::activeTextInput($model, 'berat_badan_anak', ['class'=>'form-control systolic doco-decimal-wcomma'])?>
                                <span class="input-group-addon" id="basic-addon2"><?=Yii::t('fe', 'Gram')?></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4">TB anak saat lahir</label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <?=Html::activeTextInput($model, 'tinggi_badan_anak', ['class'=>'form-control systolic doco-decimal-wcomma'])?>
                                <span class="input-group-addon" id="basic-addon2"><?=Yii::t('fe', 'Cm')?></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4">Kelainan bawaan</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <?=Html::activeTextInput($model, 'kelainan_anak', ['class'=>'form-control'])?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4">ASI sampai usia</label>
                        <div class="col-sm-8 asi">
                            <div class="input-group">
                                <?php echo $form->field($model, 'asi', [
                                        'addon' => [
                                            'append' => [
                                                'asButton' => true,
                                                'content' => Html::activeDropDownList($model, 'asi_addon', ArrayHelper::map($waktu_tumbuh_kembang, 'waktu', 'waktu'), [
                                                        'class' => 'input-group-selected btn btn-default dropdown-toggle',
                                                    ]),
                                            ],
                                        ],
                                    ])->textInput(['class' => 'doco-decimal-wcomma'])->label(false);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                    <label class="control-label col-sm-4">Susu formula dimulai dari usia</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <?php echo $form->field($model, 'susu_formula', [
                                        'addon' => [
                                            'append' => [
                                                'asButton' => true,
                                                'content' => Html::activeDropDownList($model, 'susu_formula_addon', ArrayHelper::map($waktu_tumbuh_kembang, 'waktu', 'waktu'), [
                                                        'class' => 'input-group-selected btn btn-default dropdown-toggle',
                                                    ]),
                                            ],
                                        ],
                                    ])->textInput(['class' => 'doco-decimal-wcomma'])->label(false);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4">Makanan padat dimulai dari usia</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <?php echo $form->field($model, 'makanan_padat', [
                                        'addon' => [
                                            'append' => [
                                                'asButton' => true,
                                                'content' => Html::activeDropDownList($model, 'makanan_padat_addon', ArrayHelper::map($waktu_tumbuh_kembang, 'waktu', 'waktu'), [
                                                        'class' => 'input-group-selected btn btn-default dropdown-toggle',
                                                    ]),
                                            ],
                                        ],
                                    ])->textInput(['class' => 'doco-decimal-wcomma'])->label(false);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4">Makanan tambahan dimulai dari usia</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <?php echo $form->field($model, 'makanan_tambahan', [
                                        'addon' => [
                                            'append' => [
                                                'asButton' => true,
                                                'content' => Html::activeDropDownList($model, 'makanan_tambahan_addon', ArrayHelper::map($waktu_tumbuh_kembang, 'waktu', 'waktu'), [
                                                        'class' => 'input-group-selected btn btn-default dropdown-toggle',
                                                    ]),
                                            ],
                                        ],
                                    ])->textInput(['class' => 'doco-decimal-wcomma'])->label(false);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4">Tengkurap</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <?php echo $form->field($model, 'tengkurap', [
                                        'addon' => [
                                            'append' => [
                                                'asButton' => true,
                                                'content' => Html::activeDropDownList($model, 'tengkurap_addon', ArrayHelper::map($waktu_tumbuh_kembang, 'waktu', 'waktu'), [
                                                        'class' => 'input-group-selected btn btn-default dropdown-toggle',
                                                    ]),
                                            ],
                                        ],
                                    ])->textInput(['class' => 'doco-decimal-wcomma'])->label(false);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label col-sm-4">Duduk</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <?php echo $form->field($model, 'duduk', [
                                        'addon' => [
                                            'append' => [
                                                'asButton' => true,
                                                'content' => Html::activeDropDownList($model, 'duduk_addon', ArrayHelper::map($waktu_tumbuh_kembang, 'waktu', 'waktu'), [
                                                        'class' => 'input-group-selected btn btn-default dropdown-toggle',
                                                    ]),
                                            ],
                                        ],
                                    ])->textInput(['class' => 'doco-decimal-wcomma'])->label(false);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4">Merangkak</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <?php echo $form->field($model, 'merangkak', [
                                        'addon' => [
                                            'append' => [
                                                'asButton' => true,
                                                'content' => Html::activeDropDownList($model, 'merangkak_addon', ArrayHelper::map($waktu_tumbuh_kembang, 'waktu', 'waktu'), [
                                                        'class' => 'input-group-selected btn btn-default dropdown-toggle',
                                                    ]),
                                            ],
                                        ],
                                    ])->textInput(['class' => 'doco-decimal-wcomma'])->label(false);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4">Berdiri</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <?php echo $form->field($model, 'berdiri', [
                                        'addon' => [
                                            'append' => [
                                                'asButton' => true,
                                                'content' => Html::activeDropDownList($model, 'berdiri_addon', ArrayHelper::map($waktu_tumbuh_kembang, 'waktu', 'waktu'), [
                                                        'class' => 'input-group-selected btn btn-default dropdown-toggle',
                                                    ]),
                                            ],
                                        ],
                                    ])->textInput(['class' => 'doco-decimal-wcomma'])->label(false);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4">Berjalan</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <?php echo $form->field($model, 'berjalan', [
                                        'addon' => [
                                            'append' => [
                                                'asButton' => true,
                                                'content' => Html::activeDropDownList($model, 'berjalan_addon', ArrayHelper::map($waktu_tumbuh_kembang, 'waktu', 'waktu'), [
                                                        'class' => 'input-group-selected btn btn-default dropdown-toggle',
                                                    ]),
                                            ],
                                        ],
                                    ])->textInput(['class' => 'doco-decimal-wcomma'])->label(false);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>


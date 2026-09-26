<?php

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

?>

<?php
$form = ActiveForm::begin([
    'id' => 'ranap-form',
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => [
        'labelSpan' => 3,
        'deviceSize' => ActiveForm::SIZE_SMALL
    ],
]);
?>

<div class='col-md-6'>
    <div class="panel panel-white panel-kunjungan">
        <div class="panel-heading">
            <h5 class="panel-title"><?=Yii::t('fe','Formulir kunjungan rawat inap')?></h5>
            <div class="heading-elements">
                <ul class="icons-list">
                    <!-- <li><a data-action="collapse"></a></li> -->
                </ul>
            </div>
        </div>
        <div class="panel-body">
            <?= $form->field($modelAdmisi, 'bookingkamar_no', [
                'inputOptions'=>[
                    'placeholder'=>'--No pemesanan--',
                    'readonly'=>true,
                    'id'=>'bookingkamar_no'
                ],
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ],
                'addon' => [
                    'prepend' => [
                        'content'=>'<input type="checkbox" id="is_booked"> pesan kamar'
                    ],
                    'append' => [
                        'content'=>'
                            <button
                                type="button"
                                action="/pendaftaran/daftar/modal-bookingkamar"
                                data-width="80%"
                                data-toggle="modal" 
                                data-target="#modal_backdrop"
                                class="btn btn-info btn-caribooking"
                                disabled="disabled">
                            Cari</button>',
                        'asButton'=>true
                    ]
                ]
            ]); ?>


            
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
                'placeholder' => $modelAdmisi->getAttributeLabel('tgl_admisi'),
                'class' => 'form-control input-sm pickadate',
                'id' => 'datetime',
                'autocomplete' => "off",
                'readonly' => true
            ]);
            ?>

            <?=
                $form->field($modelAdmisi, 'jeniskasuspenyakit_id', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList($jeniskasus, [
                    'class' => 'select2 selectJeniskasus',
                    'id'=>'jeniskasuspenyakit_id',
                    'prompt' => Yii::t('fe', '--Pilih--')
                ]);
            ?>

            <?=
                $form->field($modelAdmisi, 'kelaspelayanan_id', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList($kelaspelayanan, [
                    'class' => 'select2',
                    'id'=>'kelaspelayanan_id',
                    'prompt' => Yii::t('fe', '--Pilih--')
                ]);
            ?>
            <?=
            $form->field($modelAdmisi, 'ruangan_id', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ],
                'addon' => [
                    'append' => [
                        'content'=>Html::button(Yii::t('fe','Kamar'), [
                            'id'=>'cari_kamar',
                            'class' => 'btn btn-default',
                            'data-width'=>"90%",
                            'data-target'=>'#modal_backdrop',
                            'href'=>Url::to(['end-point/pilih-tempat-tidur-bayi'])
                        ]),
                        'asButton'=>true
                    ]
                ]
            ])->widget(DepDrop::classname(), [
                'name' => 'ruangan_id',
                'options' => [
                    'disabled' => false,
                    'class' => 'form-control select2 selectRuangan',
                    'id' => 'ruangan_id',
                ],
                'pluginOptions' => [
                    'depends' => ['jeniskasuspenyakit_id', 'kelaspelayanan_id'],
                    'placeholder' => Yii::t('fe', '-- Pilih --'),
                    'url' => Url::to(['daftar/get-list-ruangan', 'instalasi_id'=>$instalasi_id])
                ]
            ]);
            ?>

            <?= $form->field($modelAdmisi, 'kamarruangan_nokamar', [
                'inputOptions'=>['id'=>'nokamar', 'readonly'=>true],
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ],
            ]); ?>

            <?php 
            echo $form->field($modelAdmisi, 'pegawai_id', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
                ])->widget(DepDrop::classname(), [
                    'name' => 'pegawai_id',
                    'options' => [
                        'disabled' => false,
                        'class' => 'form-control select2',
                    ],
                    'pluginOptions' => [
                        'depends' => ['ruangan_id'],
                        'placeholder' => Yii::t('fe', '-- Pilih --'),
                        'url' => Url::to(['daftar/get-dokter','param'=>$param])
                    ]
                ]);
            ?>

            <?php
                echo $form->field($modelAdmisi, 'carabayar_id', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList($carabayar, [
                    'class' => 'select2 selectCarabayar',
                    'id'=>'selectCarabayar',
                    'prompt' => '-',
                    'options'=>$carabayarOptions
                ]);
            ?>
            <?=
            $form->field($modelAdmisi, 'penjamin_id', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
                ])->widget(DepDrop::classname(), [
                    'name' => 'penjamin_id',
                    'options' => [
                        'disabled' => false,
                        'class' => 'form-control select2 selectPenjamin',
                        'id' => 'ruangan_select',
                    ],
                    'pluginOptions' => [
                        'depends' => ['selectCarabayar'],
                        'placeholder' => Yii::t('fe', '-- Pilih --'),
                        'url' => Url::to(['daftar/get-penjamin'])
                    ]
                ]);
            ?>

            <?=
                $form->field($modelAdmisi, 'asalrujukan_id', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList(ArrayHelper::map($listRujukan, 'asalrujukan_id', 'asalrujukan_namalainnya'), [
                    'class' => 'select2 selectRujukan',
                    'prompt' => '-',
                    'id'=>'asalrujukan_id'

                ]);
            ?>

            <?= $form->field($modelAdmisi, 'keterangan',[
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textArea(); 
            ?>

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
            echo $form->field($modelAdmisi, 'kamartempattidur_id', [
                'inputOptions'=>[
                    'id'=>'kamartempattidur_id'
                ]
            ])->hiddenInput()->label(false);
            ?>

            <?=
            Html::hiddenInput('temp_ruangan_id', '', ['id'=>'temp_ruangan_id']);
            ?>

            <?php
                // if (!empty($id_booking)) {
                //     echo $form->field($modelAdmisi, 'pendaftaran_id', [
                //         'inputOptions' => [
                //             'id' => 'pendaftaran-id'
                //         ]
                //     ])->hiddenInput()->label(false);
                // } else {
                //     echo Html::activeHiddenInput($modelAdmisi, 'pendaftaran_id', ['class'=>'pendaftaran-id']);
                // }
            ?>
            <?=Html::activeHiddenInput($modelAdmisi, 'kelahiranbayi_id', ['class'=>'kelahiranbayi-id'])?>
            <?=Html::activeHiddenInput($modelAdmisi, 'pendaftaran_id', ['class'=>'pendaftaran-id'])?>
            <?=Html::activeHiddenInput($modelAdmisi, 'pasien_id', ['class'=>'pasien-id'])?>
            <?=Html::activeHiddenInput($modelAdmisi, 'instalasi_id', ['value'=>$instalasi_id])?>
            <?=Html::activeHiddenInput($modelAdmisi, 'asuransipasien_id', ['class'=>'asuransipasien-id'])?>
            <?=Html::activeHiddenInput($modelAdmisi, 'bpjs_id', ['class'=>'bpjs-id'])?>
            <?=Html::hiddenInput('HiddenPenanggungJawab', 0, ['id' => 'HiddenPenanggungJawab']) ?>

        </div>
    </div>
</div>

<div class='col-md-6'>
    <div class="panel panel-white panel-pj">
        <div class="panel-heading ">
            <h5 class="panel-title"><?=Yii::t('fe','Penanggung jawab pasien')?></h5>
            <div class="heading-elements">
                <ul class="icons-list">                        
                    <li><a data-action="collapse" class="toggle-list-pjawab"></a></li>
                </ul>
            </div>
        </div>
        <div class="panel-body">
            <?=
                $form->field($modelPj, 'pengantar', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList(ArrayHelper::map($listResponses['pengantar'], 'lookup_id', 'lookup_value'), [
                    'class' => 'select2',
                    'prompt' => '-'
                ]);
            ?>
            <?=
                $form->field($modelPj, 'penanggungjawab_nama', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textInput();
            ?>
            <?=
                $form->field($modelPj, 'penanggungjawab_jeniskelamin', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->radioList(
                            ArrayHelper::map($listResponses['jenis_kelamin'], 'lookup_id', 'lookup_value'), 
                            ['inline'=>true]
                        );;
            ?>
            <?=
                $form->field($modelPj, 'jenisidentitas', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList(ArrayHelper::map($listResponses['jenis_identitas'], 'lookup_id', 'lookup_value'), [
                    'class' => 'select2',
                    'prompt' => '-'
                ]);
            ?>
            <?=
                $form->field($modelPj, 'no_identitas', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textInput();
            ?>
            <?=
                $form->field($modelPj, 'hubungankeluarga', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList(ArrayHelper::map($listResponses['hubungan_keluarga'], 'lookup_id', 'lookup_value'), [
                    'class' => 'select2',
                    'prompt' => '-'
                ]);
            ?>
            <?=
                $form->field($modelPj, 'penanggungjawab_tempatlahir', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]                
                ])->textInput();
            ?>
            <?=
                $form->field($modelPj, 'penanggungjawab_tgllahir', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ],
                    'addon' => [
                        'append' => [
                            ['content' => '<i class="fa fa-calendar "></i>'],
                        ],
                    ]
                ])->textInput(['class'=>'pickadate-w-month dateusia']);
            ?>
            <?=
                $form->field($modelPj, 'penanggungjawab_umur', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textInput(['class'=>'umurtext', 'readonly'=>true]);
            ?>
            <?=
                $form->field($modelPj, 'penanggungjawab_alamat', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textArea();
            ?>
            <?=
                $form->field($modelPj, 'penanggungjawab_notelp', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ],
                ])->textInput();
            ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

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

// $param = isset($_GET['param']) ? $_GET['param'] : '';

?>

<?php
$form = ActiveForm::begin([
    'id' => 'igd-form',
    // 'action' => '/pendaftaran/transaksi-pemesanan/save-cache',
    'enableClientValidation'=>false,
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => [
        'labelSpan' => 3,
        'deviceSize' => ActiveForm::SIZE_SMALL
    ],
]);
?>

<div class='col-md-6'>
    <div class="panel panel-white">
        <div class="panel-heading">
            <h5 class="panel-title"><?=Yii::t('fe','Formulir kunjungan')?></h5>
            <div class="heading-elements">
                <ul class="icons-list">
                    <!-- <li><a data-action="collapse" class="toggle-list-kunjungan"></a></li> -->
                </ul>
            </div>
        </div>
        <div class="panel-body">
            <?= $form->field($modelKunjungan, 'tgl_pendaftaran', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ],
                    'addon' => ['append' => [
                            'content' => '<i class="fa fa-calendar"></i>'
                        ]
                    ]
                    ])->textInput([
                        'placeholder' => $modelKunjungan->getAttributeLabel('tgl_pendaftaran'),
                        'class' => 'form-control input-sm pickadate',
                        'id' => 'datetime',
                        'autocomplete' => "off",
                        'readonly' => true
                    ]);
                ?>
            <?php
            if($param != 'penunjang'){
                echo $form->field($modelKunjungan, 'ruangan_id', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList($ruangan, [
                    'class' => 'select2 selectRuangan',
                    'id'=>'ruangan_id',
                    'prompt' => '-'
                ]);
            }else{
                echo $form->field($modelKunjungan, 'instalasi_id', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList($listInstalasi, [
                    'class' => 'select2 selectInstalasi',
                    'id'=>'instalasi_id',
                    'prompt' => '-'
                ]);
                echo $form->field($modelKunjungan, 'ruangan_id', [
                    'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                    ]
                ])->widget(DepDrop::classname(), [
                    'data'=>[],
                    'options'=>['class'=>'select2 selectRuangan', 'id'=>'ruangan_id'],
                    'pluginOptions'=>[
                        'class'=>'select2',
                        'depends'=>['instalasi_id'],
                        'placeholder'=>'',
                        'url'=>Url::to(['get-ruangan'])
                    ]
                ]);
            }
            ?>
            <?=
            $form->field($modelKunjungan, 'jeniskasuspenyakit_id', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
                ])->widget(DepDrop::classname(), [
                    'name' => 'jeniskasuspenyakit_id',
                    'options' => [
                        'disabled' => false,
                        'class' => 'form-control select2',
                    ],
                    'pluginOptions' => [
                        'depends' => ['ruangan_id'],
                        'placeholder' => Yii::t('fe', '-- Pilih --'),
                        'url' => Url::to(['daftar/get-jenis-kasus-penyakit'])
                    ],
                    'pluginEvents'=>[
                        "depdrop:afterChange"=>"function(event, id, value) {
                            if ($('#selectCarabayar').is(':focus')) {
                                $('#kunjunganform-jeniskasuspenyakit_id').focus();
                            }
                        }",
                    ]
                ]);
            ?>
            <?=
            $form->field($modelKunjungan, 'kelaspelayanan_id', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
                ])->widget(DepDrop::classname(), [
                    'name' => 'jenis_kasus_penyakit_id',
                    'options' => [
                        'disabled' => false,
                        'class' => 'form-control select2 selectKp',
                    ],
                    'pluginOptions' => [
                        'depends' => ['ruangan_id'],
                        'placeholder' => Yii::t('fe', '-- Pilih --'),
                        'url' => Url::to(['daftar/get-kelas-pelayanan'])
                    ]
                ]);
            ?>
            <?php if ($pemilihanDokter): ?>
            <?= $form->field($modelKunjungan, 'dokter_id', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
                ])->widget(DepDrop::classname(), [
                    'name' => 'dokter_id',
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
            <?php endif ?>

            <?php
                echo $form->field($modelKunjungan, 'carabayar_id', [
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
            $form->field($modelKunjungan, 'penjamin_id', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-7'
                ]
                ])->widget(DepDrop::classname(), [
                    'name' => 'penjamin_id',
                    'options' => [
                        'disabled' => false,
                        'class' => 'form-control select2 selectPenjamin',
                        'id' => 'penjamin_id',
                    ],
                    'pluginOptions' => [
                        'depends' => ['selectCarabayar'],
                        'placeholder' => Yii::t('fe', '-- Pilih --'),
                        'url' => Url::to(['daftar/get-penjamin'])
                    ],
                    'pluginEvents'=>[
                        "depdrop:afterChange"=>"function(event, id, value) {
                            if ($('#selectCarabayar').is(':focus')) {
                                $('#penjamin_id').focus();
                            }
                            getKarcis();
                        }",
                    ]
                ]);
            ?>
            <?=
                $form->field($modelKunjungan, 'asalrujukan_id', [
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
            <?=
                $form->field($modelKunjungan, 'keadaan_masuk', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList(ArrayHelper::map($listResponses['keadaan_masuk'], 'lookup_id', 'lookup_value'), [
                    'class' => 'select2',
                    'prompt' => '-'
                ]);
            ?>
            <?=
                $form->field($modelKunjungan, 'transportasi', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->dropDownList(ArrayHelper::map($listResponses['transportasi'], 'lookup_id', 'lookup_value'), [
                    'class' => 'select2',
                    'prompt' => '-'
                ]);
            ?>
            <?=
                $form->field($modelKunjungan, 'keterangan', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textArea([], [
                    'rows' => '6',
                ]);
            ?>
            <div class="penunjang-additional">
                <?php
                if($isPenunjang){
                    echo $this->render('_rplaboratorium', ['modelPemeriksaan'=>$modelPemeriksaan]);
                }
                ?>
            </div>
            <?php
            if($param != 'penunjang'){
                echo Html::activeHiddenInput($modelKunjungan, 'instalasi_id', ['value'=>$instalasi_id]);
            }
            ?>
            <?=Html::activeHiddenInput($modelKunjungan, 'rujukan_id', ['class'=>'rujukan-id'])?>
            <?=Html::activeHiddenInput($modelKunjungan, 'asuransipasien_id', ['class'=>'asuransipasien-id'])?>
            <?=Html::activeHiddenInput($modelKunjungan, 'bpjs_id', ['class'=>'bpjs-id'])?>
            <?=Html::hiddenInput('HiddenPenanggungJawab', 0, ['id' => 'HiddenPenanggungJawab']) ?>
            <?= Html::hiddenInput('hidden_no_bpjs', '', ['id' => 'hidden_no_bpjs', 'readonly' => 'readonly']); ?>
        </div>
    </div>
</div>

<div class='col-md-6'>
    <div class="panel panel-white panel-collapsed">
        <div class="panel-heading">
            <h5 class="panel-title"><?=Yii::t('fe','Penanggung jawab pasien')?></h5>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse" class="toggle-list-pjawab"></a></li>
                </ul>
            </div>
        </div>
        <div class="panel-body">
            <?=
                $form->field($modelKunjungan, 'pj_pengantar', [
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
                $form->field($modelKunjungan, 'pj_nama', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textInput();
            ?>
            <?=
                $form->field($modelKunjungan, 'pj_jk', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->radioList(
                    ArrayHelper::map($listResponses['jenis_kelamin'], 'lookup_id', 'lookup_value'),
                    ['inline'=>true]
                );
            ?>
            <?=
                $form->field($modelKunjungan, 'pj_jenis_identitas', [
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
                $form->field($modelKunjungan, 'pj_no_identitas', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textInput();
            ?>
            <?=
                $form->field($modelKunjungan, 'pj_hubungan', [
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
                $form->field($modelKunjungan, 'pj_tempat_lahir', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textInput();
            ?>
            <?=
                $form->field($modelKunjungan, 'pj_tanggal_lahir', [
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
                $form->field($modelKunjungan, 'pj_umur', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textInput(['class'=>'umurtext', 'readonly'=>true]);
            ?>
            <?=
                $form->field($modelKunjungan, 'pj_alamat', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-7'
                    ]
                ])->textArea();
            ?>
            <?=
                $form->field($modelKunjungan, 'pj_no_telepon', [
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
<?php
$this->registerJs($this->render('../js/rencanapemeriksaanlab.js'));
?>
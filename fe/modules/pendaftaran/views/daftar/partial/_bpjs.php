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
<div class="panel panel-white">
    <div class="panel-heading">
        <h5 class="panel-title"><?=Yii::t('fe','Rujukan bpjs')?></h5>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
    </div>
    <div class="panel-body">
        <?php
        $form = ActiveForm::begin([
            'id' => 'bpjs-form',
            // 'action' => '/pendaftaran/transaksi-pemesanan/save-cache',
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => [
                'labelSpan' => 3,
                'deviceSize' => ActiveForm::SIZE_SMALL
            ],
        ]);
        ?>
        <div class='bpjs-step-1'>
            <?= $form->field($modelBpjs, 'source_peserta')
                ->radioList(
                    [
                        '1'=> Yii::t('fe', 'No rujukan'),
                        '2'=> Yii::t('fe', 'No bpjs / ktp'),
                        '3'=> Yii::t('fe', 'No sep'),
                    ],
                    ['id'=>'source_peserta', 'name'=>'source_peserta', 'inline'=>true]
                );
            ?>
            <?= $form->field($modelBpjs, 'tglsep', [
                'inputOptions'=>['id'=>'pickadateSep'],
                'addon' => [
                    'append' => ['content'=>'<i class="fa fa-calendar"></i>'],
                ]
            ]); ?>

            <?= $form->field($modelBpjs, 'jnspelayanan')->widget(Select2::classname(), [
                'options' => [
                    'id' => 'jnspelayanan',
                ],
                'data' => [
                    '2'=>Yii::t('fe', 'Rawat jalan'),
                    '1'=>Yii::t('fe', 'Rawat inap'),
                ],
            ]); ?>

            <!-- unsaved -->
            <div class='asal_rujukan_form' style='display: none;'>
                <?= $form->field($modelBpjs, 'asal_rujukan_dummy')->widget(Select2::classname(), [
                    'options' => [
                        'id' => 'asal_rujukan_dummy',
                    ],
                    'data' => [
                        '1'=>Yii::t('fe', 'Faskes tingkat 1'),
                        '2'=>Yii::t('fe', 'Faskes tingkat 2 (RS)'),
                    ],
                ]); ?>
            </div>

            <!-- unsaved -->
            <?= $form->field($modelBpjs, 'nomor', [
                'inputOptions'=>['id'=>'nomor_cari'],
                // 'horizontalCssClasses' => [
                //     'label' => 'text-left control-label col-sm-3',
                //     'wrapper' => 'col-md-3'
                // ],
                // 'addon' => [
                //     'append' => [
                //         [
                //             'content' => Html::button('<i class="fa fa-search"></i>', ['class'=>'btn btn-igroup btn-default cariPeserta']),
                //             'asButton' => true
                //         ],
                //     ],
                // ]
            ])->hint('<div class="text-danger err_nomor_cari"></div>'); ?>

            <div class="form-group">
                <div class='col-md-offset-4 col-md-8'>
                    <?= Html::button('<i class="fa fa-search"> ' . Yii::t('fe', 'Cari') . '</i>', ['class'=>'btn btn-success cariPeserta']); ?>
                </div>
            </div>
        </div>

        <div class="panel panel-flat detail_peserta" style="display:none;">
            <div class="panel-heading">
                <h6 class="panel-title"><span id='bpjs_detail_nama'>Nama Peserta</span>
                    <small id='bpjs_detail_nik'>No NIK</small>
                    <a class="heading-elements-toggle"><i class="icon-more"></i></a>
                </h6>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><i class="fa fa-ban bpjs-back-step"></i></li>
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body" style="padding-top: 4px !important;">
                <div id='bpjs_detail_no_kartu'></div>
                <div id='bpjs_detail_tgl_lahir'></div>
                <div id='bpjs_detail_jenis_peserta'></div>
                <div id='bpjs_detail_hak_kelas'></div>
                <div id='bpjs_detail_tmt_tat'></div>
                <div id='bpjs_detail_ppk_rujukan'></div>
                <div id='bpjs_detail_status_peserta'></div>
            </div>
        </div>


        <div class='bpjs-step-2' style='display: none;'>
            <?php echo $form->field($modelBpjs, 'nokartuasuransi', [
                'inputOptions'=>['id'=>'nokartuasuransi',
                'readonly'=>true
            ],
            ]); ?>

            <?php
            echo $form->field($modelBpjs, 'politujuan', [
                'addon' => [
                    'prepend' => [
                        [
                            'content' => $form->field($modelBpjs, 'eksekutif', [
                                'template' => "<div class=\"col-md-2\">{input}</div>\n<div class=\"col-md-10\">{error}</div>",
                            ])->checkbox()->label(false),
                            'asButton' => true
                        ],
                    ],
                ],
            ])->widget(Select2::classname(), [
                'options' => [
                    'id' => 'poliTujuan',
                    'placeholder' => Yii::t('fe', 'Poli tujuan')
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 3,
                    'language' => [
                        'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                    ],
                    'ajax' => [
                        'url' => \yii\helpers\Url::to(['/api/bpjs/referensi-poli']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(politujuan) { return politujuan.text; }'),
                    'templateSelection' => new JsExpression('function (politujuan) { return politujuan.text; }'),
                ],
            ]);
            ?>

            <?= $form->field($modelBpjs, 'asal_rujukan')->widget(Select2::classname(), [
                'options' => [
                    'id' => 'asal_rujukan',
                ],
                'data' => [
                    '1'=>Yii::t('fe', 'Faskes tingkat 1'),
                    '2'=>Yii::t('fe', 'Faskes tingkat 2 (RS)'),
                ],
            ]); ?>

            <?= $form->field($modelBpjs, 'ppkrujukan')->widget(Select2::classname(), [
                'options' => [
                    'id' => 'ppkrujukan',
                    'placeholder' => Yii::t('fe', 'Ppk rujukan')
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 3,
                    'language' => [
                        'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                    ],
                    'ajax' => [
                        'url' => \yii\helpers\Url::to(['/api/bpjs/referensi-faskes']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(ppkrujukan) { return ppkrujukan.text; }'),
                    'templateSelection' => new JsExpression('function (ppkrujukan) { return ppkrujukan.text; }'),
                ],
            ]); ?>

            <?= $form->field($modelBpjs, 'norujukan', [
                'inputOptions'=>['id'=>'norujukan'],
            ])->hint('<div class="text-danger err-norujukan"></div>'); ?>

            <?= $form->field($modelBpjs, 'tglrujukan', [
                'inputOptions'=>['id'=>'pickadateRujukan'],
                'addon' => [
                    'append' => ['content'=>'<i class="fa fa-calendar"></i>'],
                ]
            ]); ?>

            <?php
            echo $form->field($modelBpjs, 'no_rekam_medik', [
                'inputOptions'=>['id'=>'nomr'],
                'addon' => [
                    'append' => [
                        [
                            'content' => $form->field($modelBpjs, 'cob', [
                                'template' => "<div class=\"col-md-2\">{input}</div>\n<div class=\"col-md-10\">{error}</div>",
                            ])->checkbox()->label(false),
                            'asButton' => true
                        ],
                    ],
                ],
            ]);
            ?>

            <?php
            echo $form->field($modelBpjs, 'diagnosaawal')->widget(Select2::classname(), [
                'options' => [
                    'id' => 'diagnosaAwal',
                    'placeholder' => Yii::t('fe', 'Diagnosa awal')
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 3,
                    'language' => [
                        'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                    ],
                    'ajax' => [
                        'url' => \yii\helpers\Url::to(['/api/bpjs/referensi-diagnosa']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(diagnosaawal) { return diagnosaawal.text; }'),
                    'templateSelection' => new JsExpression('function (diagnosaawal) { return diagnosaawal.text; }'),
                ],
            ]);
            ?>
            <?= $form->field($modelBpjs, 'notelp', [
                'inputOptions'=>['id'=>'notelp']
            ]); ?>

            <?=
            $form->field($modelBpjs, 'lakalantas')->checkbox(
                [
                    'label'=>Yii::t('fe', 'Kasus kecelakaan'),
                ]
            );
            ?>

            <div class='laka', style='display:none;'>
                <?php
                $list = [1=>'Jasa raharja PT', 2=>'BPJS Ketenagakerjaan', 3=>'TASPEN PT', 4=>'ASABRI PT'];
                echo $form->field($modelBpjs, 'penjamin')->checkboxList($list, ['inline'=>true]);
                ?>

                <?= $form->field($modelBpjs, 'lokasilaka', [
                    'inputOptions'=>['id'=>'lokasilaka']
                ]); ?>
            </div>

            <?= $form->field($modelBpjs, 'catatansep')->textArea([
                'id'=>'catatansep',
                'placeholder' => Yii::t('fe', 'Catatan sep'),
                'rows' => 4
            ]); ?>

            <hr>

            <div class='hidden'>
                <?= $form->field($modelBpjs, 'klsrawat', [
                    'inputOptions'=>['id'=>'hidden-klsrawat', 'readonly'=>true],
                ]); ?>
                <?= $form->field($modelBpjs, 'kdjenispeserta', [
                    'inputOptions'=>['readonly'=>true],
                ]); ?>
                <?= $form->field($modelBpjs, 'nmjenispeserta', [
                    'inputOptions'=>['readonly'=>true],
                ]); ?>
                <?= $form->field($modelBpjs, 'kdkelastanggungan', [
                    'inputOptions'=>['readonly'=>true],
                ]); ?>
                <?= $form->field($modelBpjs, 'nmkelastanggungan', [
                    'inputOptions'=>['readonly'=>true],
                ]); ?>
                <?= $form->field($modelBpjs, 'klsrawat', [
                    'inputOptions'=>['readonly'=>true],
                ]); ?>
            </div>

            <?= $form->field($modelBpjs, 'nosep', [
                'inputOptions'=>['id'=>'nosep', 'readonly'=>true],
                'addon' => [
                    'append' => [
                        [
                            'content' => Html::button(Yii::t('fe', 'Buat SEP'), ['class'=>'btn btn-info createSep']),
                            'asButton' => true
                        ],
                        [
                            'content' => Html::button(Yii::t('fe', 'Cetak SEP'), ['class'=>'btn btn-print printSep', 'style'=>'display:none;']),
                            'asButton' => true
                        ],
                    ],
                ]
            ])->hint('<div class="text-danger err-nosep"></div>'); ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>


<?php
$this->registerJs($this->render('../js/bpjs.js'));
?>
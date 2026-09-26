<?php

/**
 * @author Rizal
 * @description UI Update Pendaftaran Rajal
**/

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\web\JsExpression;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\select2\Select2;
use kartik\typeahead\Typeahead;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = $subtitle;

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
            <div class="panel-body" style="padding:10px;">

            <?php
                $form = ActiveForm::begin([
                    'id' => 'form-create-sep',
                    'type' => ActiveForm::TYPE_VERTICAL,
                    'enableClientValidation'=>false,
                    'enableAjaxValidation'=>false,
                ]);
            ?>

            <fieldset class="content-group">
                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-12">
                        <div class="panel panel-bordered">
                            <div class="panel-heading">
                                <h6 class="panel-title">Buat SEP</h6>
                                <div class="heading-elements">
                                    <ul class="icons-list">

                                    </ul>
                                </div>
                            </div>
                            <div class="panel-body" style="padding-top:20px !important;padding-bottom:20px !important;">
                                <div class="col-sm-4">
                                    <?= $form->field($model, 'jenis_rujukan')->radioList(['1' => Yii::t('fe', 'Rujukan'), '2'=> Yii::t('fe', 'Rujukan Manual / IGD')],
                                        [
                                            'id'=>'jenis_rujukan',
                                            'inline' => true,
                                            // 'item' => function($index, $label, $name, $checked, $value) {
                                            //     $return = '<label class="radio-inlineo">';
                                            //         $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" class="styled">';
                                            //         $return .= '<i></i>';
                                            //         $return .= '<span style="margin-left:5px;">' . $label . '</span>';
                                            //     $return .= '</label>';

                                            //     return $return;
                                            // }
                                        ]
                                    );
                                    ?>
                                    <?= $form->field($model, 'tanggal_sep', [
                                        'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-8'
                                        ],
                                        'inputOptions'=>['id'=>'tanggal_sep_1'],
                                        'addon' => [
                                            'append' => ['content'=>'<i class="fa fa-calendar"></i>'],
                                        ]
                                    ]); ?>
                                </div>
                                <div class="col-sm-4">
                                    <div id="base-rujukan">
                                        <?php $model->asal_rujukan = 1; ?>
                                        <?= $form->field($model, 'asal_rujukan',[
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-8'
                                                ]
                                            ])->dropDownList(
                                                [
                                                    '1'=>Yii::t('fe', 'Faskes tingkat 1'),
                                                    '2'=>Yii::t('fe', 'Faskes tingkat 2 (RS)'),
                                                ],
                                                [
                                                    'id'=>'asal_rujukan_1',
                                                    'class' => 'select2',
                                                    'prompt'=>'— PILIH —',
                                                ]
                                            );
                                        ?>

                                        <?php
                                        echo $form->field($model, 'no_rujukan_f', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ],
                                            'inputOptions'=>['id'=>'no_rujukan'],
                                        ])->hint('<div class="text-danger err-no-rujukan"></div>');
                                        ?>
                                    </div>
                                    <div id="base-rujukan-manual" style="display:none;">
                                        <?php
                                        echo $form->field($model, 'jenis_pelayanan',[
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-8'
                                                ]
                                            ])->dropDownList(
                                                [
                                                    '2'=>Yii::t('fe', 'Rawat Jalan'),
                                                    '1'=>Yii::t('fe', 'Rawat Inap'),
                                                ],
                                                [
                                                    'id'=>'jenis_pelayanan',
                                                    'class' => 'select2',
                                                ]
                                            );

                                        $model->jenis_kartu = 1;
                                        echo $form->field($model, 'jenis_kartu',[
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->radioList(
                                            [
                                                '1'=> Yii::t('fe', 'No BPJS'),
                                                '2'=> Yii::t('fe', 'NIK'),
                                            ],
                                            [
                                                'id' => 'jenis_kartu',
                                                'inline' => true,
                                                'item' => function($index, $label, $name, $checked, $value) {
                                                    $return = '<label class="radio-inlineo">';
                                                        $return .= '<input type="radio"
                                                                name="' . $name . '"
                                                                value="' . $value . '"
                                                                class="styled"'. ($checked ? 'checked' : '') .'>';
                                                        $return .= '<i></i>';
                                                        $return .= '<span style="margin-left:5px;">' . $label . '</span>';
                                                    $return .= '</label>';

                                                    return $return;
                                                }
                                            ]
                                        );

                                        echo $form->field($model, 'no_kartu', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-8'
                                                ],
                                            'inputOptions'=>['id'=>'no_kartu'],
                                        ])->hint('<div class="text-danger err-no-kartu"></div>');
                                        ?>
                                    </div>
                                    <?= Html::button('<b><i class="fa fa-search"></i></b>'.Yii::t('fe', 'Cari'), [
                                        'class' => 'btn btn-info btn-labeled btn-xs pull-right',
                                        'id' => 'btn-cari-rujukan',
                                        'style' => 'margin-top: 15px;'
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12" id="container-bpjs" style="display:none;">
                        <div class="panel panel-bordered">
                            <div class="panel-heading">
                                <h6 class="panel-title">Form Detail BPJS</h6>
                                <div class="heading-elements">
                                    <ul class="icons-list">

                                    </ul>
                                </div>
                            </div>
                            <div class="panel-body" style="padding-top:20px !important;">
                                <div class="col-md-3">
                                    <?php echo Yii::$app->controller->renderPartial('_informasi_pasien'); ?>
                                </div>
                                <div class="col-md-3">
                                    <!-- div poli tujuan -->
                                    <div class="form-poli_tujuan">
                                        <?php echo $form->field($model, 'poli_tujuan', [
                                                'addon' => [
                                                    'prepend' => [
                                                            'content' => '<label>
                                                                            <input type="checkbox" class="notUniform" id="bpjsnewform-poli_eksekutif" data-urutan=1 name="BpjsNewForm[poli_eksekutif]" value="1" autocomplete="off"> Eksekutif
                                                                            </label>',
                                                    ]
                                                ]
                                            ])
                                            ->dropDownList([],
                                                [
                                                    'id'=>'poli_tujuan',
                                                    'class'=>'select2Bpjs select2Poli',
                                                    'prompt'=>'— PILIH —'
                                                ]
                                            );
                                        ?>
                                    </div>

                                    <!-- div DPJP serve -->
                                    <div class="dpjp_form_melayani">
                                        <?php
                                        echo $form->field($model, 'kode_dpjp_melayani')
                                            ->dropDownList([],
                                                [
                                                    'id' => 'kode_dpjp_melayani',
                                                    'class'=>'select2DpjpServe',
                                                    'prompt'=>'— PILIH DPJP—',
                                                ]
                                            );
                                        ?>
                                    </div>

                                    <!-- div asal rujukan -->
                                    <div class="asal_rujukan">
                                        <?php
                                        echo Html::hiddenInput('asal_rujukan_hidden', '', ['id' => 'asal_rujukan_hidden']);
                                        echo $form->field($model, 'asal_rujukan')
                                            ->dropDownList(
                                                [
                                                    '1'=>Yii::t('fe', 'Faskes tingkat 1'),
                                                    '2'=>Yii::t('fe', 'Faskes tingkat 2 (RS)'),
                                                ],
                                                [
                                                    'id'=>'asal_rujukan',
                                                    'class'=>'select2Bpjs select2AsalRujukan',
                                                    'prompt'=>'— PILIH —',
                                                ]
                                            );
                                        ?>
                                    </div>

                                    <!-- div ppk rujukan -->
                                    <div class="ppk_rujukan">
                                        <?php
                                        echo Html::hiddenInput('ppk_rujukan_hidden', '', ['id' => 'ppk_rujukan_hidden']);
                                        echo $form->field($model, 'ppk_rujukan')
                                        ->dropDownList([],
                                            [
                                                'id'=>'ppk_rujukan',
                                                'class'=>'select2PpkRujukan',
                                                'prompt'=>'— PILIH —',
                                            ]
                                        )->label(Yii::t('fe', 'PPK Asal Rujukan'));
                                        ?>
                                    </div>

                                    <!-- div dpjp -->
                                    <div class="dpjp_form" style="display: none;">
                                        <?php
                                        echo $form->field($model, 'no_surat_kontrol', [
                                            'inputOptions'=>['id'=>'no_surat_kontrol'],
                                            'options'=>[ 'class'=>'form-group field-no_surat_kontrol']
                                        ]);

                                        echo $form->field($model, 'kode_dpjp')
                                            ->dropDownList([],
                                                [
                                                    'id' => 'kode_dpjp',
                                                    'class'=>'select2Dpjp',
                                                    'prompt'=>'— PILIH DPJP—',
                                                ]
                                            );
                                        ?>
                                    </div>

                                    <!-- div tanggal rujukan -->
                                    <div class="tanggal_rujukan">
                                        <?php
                                        echo $form->field($model, 'tanggal_rujukan', [
                                            'inputOptions'=>['id'=>'tanggal_rujukan', 'data-mask' => '99-99-9999'],
                                            'addon' => [
                                                'append' => ['content'=>'<i class="fa fa-calendar"></i>'],
                                            ]
                                        ]);
                                        ?>
                                    </div>

                                    <!-- div nomor rujukan -->
                                    <div class="no_rujukan">
                                        <?php
                                        echo $form->field($model, 'no_rujukan', [
                                            'inputOptions'=>['id'=>'no_rujukan_1'],
                                        ]);
                                        ?>
                                    </div>

                                    <!-- div tanggal sep -->
                                    <div class="tanggal_sep">
                                        <?php
                                        echo $form->field($model, 'tanggal_sep', [
                                            'inputOptions'=>['id'=>'tanggal_sep', 'data-mask' => '99-99-9999'],
                                            'addon' => [
                                                'append' => ['content'=>'<i class="fa fa-calendar"></i>'],
                                            ]
                                        ]);
                                        ?>
                                    </div>

                                    <!-- div nomor rekam medik -->
                                    <div class="no_rekam_medik">
                                        <?php
                                            echo $form->field($model, 'no_rekam_medik', [
                                            'inputOptions'=>['id'=>'nomr', 'readonly' => true],
                                            'addon' => [
                                                'append' => [
                                                    [
                                                        'content' => '<label>
                                                                        <input type="checkbox" class="notUniform" id="bpjsnewform-cob" name="BpjsNewForm[cob]" value="1" autocomplete="off">
                                                                        Peserta COB
                                                                    </label>'
                                                    ],
                                                ],
                                            ],
                                        ]);
                                        ?>
                                    </div>

                                    <!-- div kelas rawat -->
                                    <div class="kelas_rawat">
                                        <?php
                                        echo $form->field($model, 'kelas_rawat', [
                                          'addon' => [
                                              'append' => [
                                                  [
                                                      'content' => '<label>
                                                                      <input type="checkbox" class="notUniform" id="bpjsnewform-is_naikkelas_ranap" name="BpjsNewForm[is_naikkelas_ranap]" value="1" autocomplete="off">
                                                                       Naik Kelas Rawat Inap
                                                                    </label>'
                                                  ],
                                              ],
                                          ],
                                        ])
                                        ->dropDownList([
                                                1 => 'Kelas I',
                                                2 => 'Kelas II',
                                                3 => 'Kelas III',
                                            ],
                                            [
                                                'id' => 'kelas_rawat',
                                                'class'=>'select2Bpjs',
                                                'prompt'=>'— Pilih —',
                                            ]
                                        );
                                        ?>
                                    </div>

                                    <!--     div field naik kelas rawat inap    -->
                                    <div class="naik_kelas_rawat" style="display:none;">
                                      <?php
                                          echo $form->field($model, 'naik_kelas_rawat_inap')
                                              ->dropDownList([
                                                      1 => 'VVIP',
                                                      2 => 'VIP',
                                                      3 => 'Kelas 1',
                                                      4 => 'Kelas 2',
                                                      5 => 'Kelas 3',
                                                      6 => 'ICCU',
                                                      7 => 'ICU',
                                                      8 => 'Diatas Kelas 1',
                                                  ],
                                                  [
                                                      'id' => 'naik_kelas_rawat_inap',
                                                      'class'=>'select2Bpjs',
                                                      'prompt'=>'— PILIH —',
                                                  ]
                                              );

                                          echo $form->field($model, 'pembiayaan')
                                              ->dropDownList([
                                                      1 => 'Pribadi (Peserta/Pihak Lain)',
                                                      2 => 'Pemberi Kerja',
                                                      3 => 'Asuransi Kesehatan Tambahan',
                                                  ],
                                                  [
                                                      'id' => 'pembiayaan',
                                                      'class'=>'select2Bpjs',
                                                      'prompt'=>'— PILIH —',
                                                  ]
                                              );

                                        echo $form->field($model, 'nama_penganggung_jawab', [
                                                'inputOptions'=>['id'=>'nama_penganggung_jawab'],
                                            ]);
                                      ?>
                                    </div>
                                    <!-- end of div field naik kelas rawat inap -->

                                    <!-- div diagnosa awal -->
                                    <div class="diagnosa_awal">
                                        <?php
                                        echo $form->field($model, 'diagnosa_awal')
                                            ->dropDownList([],
                                                [
                                                    'id'=>'diagnosa_awal',
                                                    'class'=>'select2Diagnosa',
                                                    'prompt'=>'— PILIH —',
                                                ]
                                            )->label(Yii::t('fe', 'Diagnosa'));
                                        ?>
                                    </div>

                                    <!-- div no telp -->
                                    <div class="no_telp">
                                        <?php
                                        echo $form->field($model, 'no_telp', [
                                            'inputOptions'=>['id'=>'no_telp']
                                        ])->textInput(['class' => 'docoNumberOnly']);
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- div catatan -->
                                    <div class="catatan_sep">
                                        <?php
                                        echo $form->field($model, 'catatan_sep')->textArea([
                                            'id'=>'catatan_sep',
                                            'rows' => 2
                                        ]);
                                        ?>
                                    </div>

                                    <!-- div centang katarak -->
                                    <div class="katarak">
                                        <?php
                                            echo $form->field($model, 'katarak')->checkbox()->label('Centang Katarak, Jika Peserta Tersebut Mendapatkan Surat Perintah Operasi katarak');

                                        ?>
                                    </div>

                                    <!-- div kasus kecelakaan -->
                                    <div class="kasus_kecelakaan">
                                        <?php
                                        $model->kasus_kecelakaan = 0;
                                        echo $form->field($model, 'kasus_kecelakaan')
                                            ->dropDownList([
                                                    '0'=>Yii::t('fe', 'Bukan Kecelakaan'),
                                                    '1'=>Yii::t('fe', 'Kecelakaan Lalu Lintas dan Bukan Kecelakaan Kerja'),
                                                    '2'=>Yii::t('fe', 'Kecelakaan Lalu Lintas dan Kecelakaan Kerja'),
                                                    '3'=>Yii::t('fe', 'Kecelakaan Kerja'),
                                                ],
                                                [
                                                    'id'=>'kasus_kecelakaan',
                                                    'class'=>'select2KasusKecelakaan',
                                                    'prompt'=>'— PILIH —',
                                                ]
                                            );
                                        ?>
                                    </div>

                                    <div class="kasus_kecelakaan_form" style="display: none;">
                                        <?php
                                        echo $form->field($model, 'tanggal_kejadian', [
                                            'inputOptions'=>['id'=>'tanggal_kejadian', 'data-mask' => '99-99-9999'],
                                            'addon' => [
                                                'append' => ['content'=>'<i class="fa fa-calendar"></i>'],
                                            ]
                                        ]);

                                        echo $form->field($model, 'no_lp', [
                                            'inputOptions'=>['id'=>'no_lp']
                                        ]);

                                        echo $form->field($model, 'kode_provinsi')
                                        ->dropDownList([],
                                            [
                                                'id'=>'kode_provinsi',
                                                'class'=>'select2Provinsi',
                                                'prompt'=>'— PILIH —',
                                            ]
                                        );

                                        echo $form->field($model, 'kode_kabupaten')
                                        ->dropDownList([],
                                            [
                                                'id'=>'kode_kabupaten',
                                                'class'=>'kode_kabupaten',
                                                'prompt'=>'— PILIH —',
                                            ]
                                        );

                                        echo $form->field($model, 'kode_kecamatan')
                                        ->dropDownList([],
                                            [
                                                'id'=>'kode_kecamatan',
                                                'class'=>'kode_kecamatan',
                                                'prompt'=>'— PILIH —',
                                            ]
                                        );

                                        echo $form->field($model, 'keterangan')->textArea([
                                            'id'=>'keterangan',
                                            'rows' => 2
                                        ]);
                                        ?>
                                    </div>

                                    <div class="suplesi_form" style="display: none;">
                                        <?php
                                        echo Html::activeHiddenInput($model, 'status_suplesi', ['id'=>'status_suplesi', 'value'=>0]);
                                        echo $form->field($model, 'no_sep_suplesi',[
                                            'inputOptions'=>[
                                                'class'=>'form-control',
                                                'readOnly' => true
                                            ],
                                            'options'=>[ 'class'=>'form-group highlight-addon field-no_sep_suplesi required']
                                        ]);
                                        ?>
                                    </div>

                                    <?= Html::activeHiddenInput($model, 'tujuanKunj', ['id' => 'tujuan_kunjungan']); ?>
                                    <?= Html::activeHiddenInput($model, 'flagProcedure', ['id' => 'flag_procedure']); ?>
                                    <?= Html::activeHiddenInput($model, 'kdPenunjang', ['id' => 'kd_penunjang']); ?>
                                    <?= Html::activeHiddenInput($model, 'assesmentPel', ['id' => 'assesment_pel']); ?>
                                    <?= Html::activeHiddenInput($model, 'is_tujuan_kunj', ['id' => 'is_tujuan_kunj', 'value' => 0]); ?>

                                    <button type="button" class="btn btn-tujuan-kunjungan btn-info btn-labeled btn-xs" action="<?= Url::home() ?>pendaftaran/daftar/tujuan-prosedur" data-toggle="modal" data-target="#modal_backdrop" data-width="50%" style="display:none"><b><i class="fa fa-eye"></i></b>Tujuan Kunjungan</button>
                                </div>
                                <div class="col-md-9">
                                    <?= Html::submitButton('<b><i class="fa fa-floppy-o"></i></b>'. Yii::t('fe','Simpan'), [
                                        'class' => 'btn btn-info btn-labeled btn-xs pull-right',
                                        'style' => 'right:10px;bottom:10px;'
                                    ]); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </fieldset>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$_dataBpjs = json_encode($data_bpjs);
$_dataPasien = json_encode($data_pasien);

$this->registerJs("
    var jenisPendaftaran = '".$jenis_pendaftaran."';
    var dataBpjs = $_dataBpjs;
    var dataPasien = $_dataPasien;
", View::POS_END);
$this->registerJs($this->render('js/form_create_sep.js'), View::POS_END);
$this->registerJs($this->render('js/bpjs-helper.js'), View::POS_END);
?>

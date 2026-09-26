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
    'id' => 'bpjs-new-form',
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => [
        'labelSpan' => 3,
        'deviceSize' => ActiveForm::SIZE_SMALL
    ]
]);
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=Yii::t('fe','Rujukan BPJS')?></h5>
</div>

<div class="modal-body">
    <div class='bpjs-step-1'>
        <?php $modelBpjs->jenis_rujukan = 1; ?>
        <?php echo Html::activeHiddenInput($modelBpjs, 'pendaftaran_id', []); ?>
        <?php echo Html::activeHiddenInput($modelBpjs, 'pasienadmisi_id', []); ?>
        <?php echo Html::activeHiddenInput($modelBpjs, 'nama_pasien', ['id' => 'bpjs-nama-pasien']); ?>
        <?php echo Html::activeHiddenInput($modelBpjs, 'no_asuransi', ['id' => 'no_asuransi']); ?>
        <?= $form->field($modelBpjs, 'jenis_rujukan')
            ->radioList(
                [
                    '1'=> Yii::t('fe', 'Rujukan'),
                    '2'=> Yii::t('fe', 'Rujukan Manual / IGD'),
                ],
                ['id'=>'jenis_rujukan', 'name'=>'jenis_rujukan', 'inline'=>true]
            );
        ?>
        <?= $form->field($modelBpjs, 'tanggal_sep', [
            'inputOptions'=>['id'=>'tanggal_sep_1'],
            'addon' => [
                'append' => ['content'=>'<i class="fa fa-calendar"></i>'],
            ]
        ]); ?>
    
        <div id="base-rujukan">
            <?php $modelBpjs->asal_rujukan = 2; ?>
            <?= $form->field($modelBpjs, 'asal_rujukan')
                ->dropDownList(
                    [
                        '2'=>Yii::t('fe', 'Faskes tingkat 1'),
                        '1'=>Yii::t('fe', 'Faskes tingkat 2 (RS)'),
                    ],
                    [
                        'id'=>'asal_rujukan_1',
                        'class' => 'select2',
                        'prompt'=>'— PILIH —',
                    ]
                );
            ?>

            <?php
            echo $form->field($modelBpjs, 'no_rujukan_f', [
                'inputOptions'=>['id'=>'no_rujukan'],
                'addon' => [
                    'append' => [
                        [
                            'content' =>Html::button('Cari No Kartu', [
                                'class'=>'btn btn-default', 
                                'id' => 'modal-rujukan', 
                                'data-toggle' => 'modal', 
                                'href' => Url::to(['/pendaftaran/daftar/view-peserta-rujukan']), 
                                'data-target' => '#modal_view_rujukan', 
                                'width' => '75%'
                            ]),
                            'asButton' => true
                        ],
                    ],
                ],
            ])->hint('<div class="text-danger err-no-rujukan"></div>');

            echo Html::hiddenInput('pelayanan', 0, ['id' => 'hide-pelayanan']);

            echo "<div class='text-right'>";
            echo Html::button('<i class="fa fa-search"></i> ' . Yii::t('fe', 'Cari'), ['class'=>'btn btn-info cari_rujukan']);
            echo "</div>";
            ?>
        </div>

        <div id="base-rujukan-manual" style="display:none;">
            <?php 
            // $modelBpjs->jenis_pelayanan = 1;
            echo $form->field($modelBpjs, 'jenis_pelayanan')
                ->dropDownList(
                    [
                        '2'=>Yii::t('fe', 'Rawat Jalan'),
                        '1'=>Yii::t('fe', 'Rawat Inap'),
                    ],
                    [
                        'id'=>'jenis_pelayanan',
                        'class' => 'select2',
                        'prompt'=>'— PILIH —',
                    ]
                );
            
            $modelBpjs->jenis_kartu = 1;
            echo $form->field($modelBpjs, 'jenis_kartu')
            ->radioList(
                [
                    '1'=> Yii::t('fe', 'No BPJS'),
                    '2'=> Yii::t('fe', 'NIK'),
                ],
                ['id'=>'jenis_kartu', 'name'=>'jenis_kartu', 'inline'=>true]
            );

            echo $form->field($modelBpjs, 'no_kartu', [
                'inputOptions'=>['id'=>'no_kartu'],
            ])->hint('<div class="text-danger err-no-kartu"></div>');

            echo "<div class='text-right'>";
            echo Html::button('<i class="fa fa-search"></i> ' . Yii::t('fe', 'Cari'), ['class'=>'btn btn-info cari_rujukan_manual']);
            echo "</div>";
            ?>
        </div>
    </div>

    <div class="panel-group">
        <div class="panel panel-default detail_peserta" style="display:none;">
            <div class="panel-heading">
                <h4 class="panel-title"><span id='bpjsnew_detail_nama'>Nama Peserta</span>
                    <small id='bpjsnew_detail_no_kartu'>No Nartu</small>
                  <a data-toggle="collapse" href="#collapse1"><i class="glyphicon glyphicon-arrow-down"></i></a>
                </h4>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><i class="fa fa-ban bpjs-back-step"></i></li>
                        <li><a data-action="collapse info-peserta"></a></li>
                    </ul>
                </div>
            </div>
            <div id="collapse1" class="panel-collapse collapse">
                <div class="panel-body">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3">
                                <div id='bpjsnew_detail_nik'></div>
                            </div>
                            <div class="col-md-3">
                                <div id='bpjsnew_detail_tgl_lahir'></div>
                            </div>
                            <div class="col-md-3">
                                <div id='bpjsnew_detail_jenis_peserta'></div>
                            </div>
                            <div class="col-md-3">
                                <div id='bpjsnew_detail_hak_kelas'></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div id='bpjsnew_detail_tmt_tat'></div>
                            </div>
                            <div class="col-md-3">
                                <div id='bpjsnew_detail_ppk_rujukan'></div>
                            </div>
                            <div class="col-md-3">
                                <div id='bpjsnew_detail_status_peserta'></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel-body">
        <div class="bpjs-step-2" style="display: none;">
            <div class="col-md-6">
                <div class="form-poli_tujuan">
                    <?php echo $form->field($modelBpjs, 'poli_tujuan', [
                            'addon' => [
                                'prepend' => [
                                        'content' => $form->field($modelBpjs, 'poli_eksekutif', [
                                        'template' => "<div class=\"col-md-2\">{input}</div>\n<div class=\"col-md-10\">{error}</div>",
                                    ])->checkbox()->label(false),
                                    'asButton' => true
                                ]
                            ]
                        ])
                        ->dropDownList([],
                            [
                                'id'=>'poli_tujuan',
                                'class'=>'select2 select2Poli',
                                'prompt'=>'— PILIH —',
                            ]
                        );
                    ?>
                </div>
                <?php
                echo $form->field($modelBpjs, 'asal_rujukan')
                    ->dropDownList(
                        [
                            '1'=>Yii::t('fe', 'Faskes tingkat 1'),
                            '2'=>Yii::t('fe', 'Faskes tingkat 2 (RS)'),
                        ],
                        [
                            'id'=>'asal_rujukan',
                            'class'=>'select2 select2AsalRujukan',
                            'prompt'=>'— PILIH —',
                        ]
                    );
                ?>
                
                <div class="dpjp_form" style="display: none;">
                    <?php 
                    echo $form->field($modelBpjs, 'no_surat_kontrol', [
                        'inputOptions'=>['id'=>'no_surat_kontrol'],
                        'options'=>[ 'class'=>'form-group highlight-addon field-no_surat_kontrol required']
                    ]);

                    echo $form->field($modelBpjs, 'kode_dpjp')
                        ->dropDownList([],
                            [
                                'id' => 'kode_dpjp',
                                'class'=>'select2Dpjp',
                                'prompt'=>'— PILIH DPJP—',
                            ]
                        );
                    ?>
                </div>

                <?php 
                echo $form->field($modelBpjs, 'ppk_rujukan')
                    ->dropDownList([],
                        [
                            'id'=>'ppk_rujukan',
                            'class'=>'select2PpkRujukan',
                            'prompt'=>'— PILIH —',
                        ]
                    )->label(Yii::t('fe', 'PPK Asal Rujukan'));

                echo $form->field($modelBpjs, 'tanggal_rujukan', [
                    'inputOptions'=>['id'=>'tanggal_rujukan'],
                    'addon' => [
                        'append' => ['content'=>'<i class="fa fa-calendar"></i>'],
                    ]
                ]);

                echo $form->field($modelBpjs, 'no_rujukan', [
                    'inputOptions'=>['id'=>'no_rujukan_1'],
                ]);

                echo $form->field($modelBpjs, 'tanggal_sep', [
                    'inputOptions'=>['id'=>'tanggal_sep'],
                    'addon' => [
                        'append' => ['content'=>'<i class="fa fa-calendar"></i>'],
                    ]
                ]);

                echo $form->field($modelBpjs, 'no_rekam_medik', [
                    'inputOptions'=>['id'=>'nomr', 'readonly' => true],
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

                echo $form->field($modelBpjs, 'kelas_rawat')
                ->dropDownList([
                        1 => 'Kelas I',
                        2 => 'Kelas II',
                        3 => 'Kelas III',
                    ],
                    [
                        'id' => 'kelas_rawat',
                        'class'=>'select2',
                        'prompt'=>'— PILIH —',
                    ]
                );
                echo $form->field($modelBpjs, 'diagnosa_awal')
                    ->dropDownList([],
                        [
                            'id'=>'diagnosa_awal',
                            'class'=>'select2Diagnosa',
                            'prompt'=>'— PILIH —',
                        ]
                    )->label(Yii::t('fe', 'Diagnosa'));

                echo $form->field($modelBpjs, 'no_telp', [
                    'inputOptions'=>['id'=>'no_telp']
                ]); 
                ?>
            </div>

            <div class="col-md-6">
                <?php 
                echo $form->field($modelBpjs, 'catatan_sep')->textArea([
                    'id'=>'catatan_sep',
                    'rows' => 2
                ]);
                echo $form->field($modelBpjs, 'katarak')->checkbox()->label(false)
                    ->hint('Centang Katarak, Jika Peserta Tersebut Mendapatkan Surat Perintah Operasi katarak');

                $modelBpjs->kasus_kecelakaan = 0;
                echo $form->field($modelBpjs, 'kasus_kecelakaan')
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
                <div class="kasus_kecelakaan_form" style="display: none;">
                    <?php
                    echo $form->field($modelBpjs, 'tanggal_kejadian', [
                        'inputOptions'=>['id'=>'tanggal_kejadian'],
                        'addon' => [
                            'append' => ['content'=>'<i class="fa fa-calendar"></i>'],
                        ]
                    ]);
                    
                    echo $form->field($modelBpjs, 'kode_provinsi')
                    ->dropDownList([],
                        [
                            'id'=>'kode_provinsi',
                            'class'=>'select2Provinsi',
                            'prompt'=>'— PILIH —',
                        ]
                    );

                    echo $form->field($modelBpjs, 'kode_kabupaten', [
                        'options'=>[ 'class'=>'form-group highlight-addon field-kode_kabupaten required']
                    ])->widget(DepDrop::classname(), [
                        'data'=>[],
                        'options'=>['class'=>'select2 kode_kabupaten', 'id'=>'kode_kabupaten'],
                        'pluginOptions'=>[
                            'class'=>'select2',
                            'depends'=>['kode_provinsi'],
                            'placeholder'=>'--Pilih Kabupaten--',
                            'url'=>Url::to(['/api/bpjs/referensi-kabupaten'])
                        ]
                    ]);
                    
                    echo $form->field($modelBpjs, 'kode_kecamatan', [
                        'options'=>[ 'class'=>'form-group highlight-addon field-kode_kecamatan required']
                    ])->widget(DepDrop::classname(), [
                        'data'=>[],
                        'options'=>['class'=>'select2 kode_kecamatan', 'id'=>'kode_kecamatan'],
                        'pluginOptions'=>[
                            'class'=>'select2',
                            'depends'=>['kode_kabupaten'],
                            'placeholder'=>'--Pilih Kecamatan--',
                            'url'=>Url::to(['/api/bpjs/referensi-kecamatan'])
                        ]
                    ]);
                    
                    echo $form->field($modelBpjs, 'keterangan')->textArea([
                        'id'=>'keterangan',
                        'rows' => 2
                    ]);
                    ?>
                </div>

                <div class="suplesi_form" style="display: none;">
                    <?php
                    echo Html::activeHiddenInput($modelBpjs, 'status_suplesi', ['id'=>'status_suplesi', 'value'=>0]);
                    echo $form->field($modelBpjs, 'no_sep_suplesi',[
                        'inputOptions'=>[
                            'class'=>'form-control',
                            'readOnly' => true
                        ],
                        'options'=>[ 'class'=>'form-group highlight-addon field-no_sep_suplesi required']
                    ]);
                    ?>
                </div>

                <?php
                    echo "<div class='text-right'>";
                    echo Html::button('<i class="fa fa-save"></i> ' . Yii::t('fe', 'Simpan'), ['class'=>'btn btn-info create-sep']);

                    echo '&nbsp;&nbsp;&nbsp;&nbsp;';

                    echo Html::button('<i class="fa fa-arrow-left"></i> ' . Yii::t('fe', 'Kembali'), ['class'=>'btn bg-slate', 'data-dismiss' => 'modal']);
                    echo "</div>";
                ?>
            </div>
        </div>
    </div>
    <div class="bpjs-step-3" style="display: none;">
        <div class="panel panel-flat">
            <div class="panel-heading">
                <h6 class="panel-title">
                    <span class="text-semibold">Pembuatan SEP Berhasil.</span> 
                </h6>
                SEP : <strong> <span id="no_sep">00000000</span></strong>
            </div>
            
            <!-- <div class="panel-body">
                <div class="text-italic">
                    Lanjutkan input pendaftaran untuk mencetak SEP.
                </div>
            </div> -->
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php 
$this->registerJs("
var is_ranap = '".$is_ranap."';

");
$this->registerJs($this->render('../js/bpjs-new.js')); ?>

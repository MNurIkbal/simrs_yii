<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\DepDrop;

?>
<div class="col-md-3">
    <?php echo $form->field($model, 'kelas_rawat')->hiddenInput(['id' => 'kelas_rawat'])->label(false);
    echo Html::hiddenInput('cek_lokasi', '', ['id' => 'cek_lokasi']);?>
    <div class="form-poli_tujuan">
        <?php echo $form->field($model, 'poli_tujuan', [
                'addon' => [
                    'prepend' => [
                            'content' => '<label>
                            <input type="checkbox" class="notUniform" id="poli_eksekutif" data-urutan=1 name="ManajemenBpjsForm[poli_eksekutif]" value="1" autocomplete="off"> Eksekutif
                            </label>',
                    ]
                ]
            ])
            ->dropDownList([],
                [
                    'id'=>'poli_tujuan',
                    'class'=>'select2Bpjs select2Poli required',
                    'prompt'=>'— PILIH —'
                ]
            )->label(Yii::t('fe', 'Spesialis / SubSpesialis <span color="red">*</span>'));
        ?>
    </div>

    <div class="asal_rujukan">
        <?php
        // echo Html::hiddenInput('asal_rujukan_hidden', '', ['id' => 'asal_rujukan_hidden']);
        // echo $form->field($model, 'asal_rujukan')
        //     ->dropDownList(
        //         [
        //             '1'=>Yii::t('fe', 'Faskes tingkat 1'),
        //             '2'=>Yii::t('fe', 'Faskes tingkat 2 (RS)'),
        //         ],
        //         [
        //             'id'=>'asal_rujukan',
        //             'class'=>'select2Bpjs select2AsalRujukan required',
        //             'prompt'=>'— PILIH —',
        //             'disabled' => true
        //         ]
        //     );
        ?>
        <?php
        // echo $form->field($model, 'asal_rujukan', [
        //     'inputOptions'=>['id'=>'asal_rujukan']
        // ]);
        ?>
    </div>

    <!-- div DPJP serve -->
    <div class="dpjp_form_melayani">
        <?php 
        echo $form->field($model, 'kode_dpjp_melayani')
            ->dropDownList([],
                [
                    'id' => 'kode_dpjp_melayani',
                    'class'=>'select2Bpjs select2DpjpServe required',
                    'prompt'=>'— PILIH DPJP—',
                ]
            );
        ?>
    </div>

    <!-- div no telp -->
    <div class="no_telp">
        <?php
        echo $form->field($model, 'no_telp', [
            'inputOptions'=>['id'=>'no_telp' , 'class' => 'required']
        ]);
        ?>
    </div>

    <!-- div tanggal rujukan -->
    <div class="tanggal_rujukan">
        <?php
        echo $form->field($model, 'tanggal_rujukan', [
            'inputOptions'=>['id'=>'tanggal_rujukan', 'data-mask' => '99-99-9999', 'disabled' => true],
            'addon' => [
                'append' => ['content'=>'<i class="fa fa-calendar"></i>'],
            ]
        ]);
        ?>
    </div>

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
    
</div>
<div class="col-md-3">
    <!-- div ppk rujukan -->
    <div class="ppk_rujukan">
        <?php
            // echo $form->field($model, 'ppk_rujukan', [
            //     'inputOptions'=>['id'=>'ppk_rujukan', 'disabled' => true],
            // ])->label(Yii::t('fe', 'PPK Asal Rujukan'));
        ?>
        <?php
        // echo Html::hiddenInput('ppk_rujukan_hidden', '', ['id' => 'ppk_rujukan_hidden']);
        // echo $form->field($model, 'ppk_rujukan')
        // ->dropDownList([],
        //     [
        //         'id'=>'ppk_rujukan',
        //         'class'=>'select2PpkRujukan',
        //         'prompt'=>'— PILIH —',
        //     ]
        // )->label(Yii::t('fe', 'PPK Asal Rujukan'));
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

    <!-- div nomor rekam medik -->
    <div class="no_rekam_medik">
        <?php
            echo $form->field($model, 'no_rekam_medik', [
            'inputOptions'=>['id'=>'nomr', 'readonly' => true],
            'addon' => [
                'append' => [
                    [
                        'content' => '<label>
                                        <input type="checkbox" class="notUniform" id="bpjsnewform-cob" name="ManajemenBpjsForm[cob]" value="1" autocomplete="off">
                                        Peserta COB
                                    </label>'
                    ],
                ],
            ],
        ]);
        ?>
    </div>

    <!-- div tanggal sep -->
    <div class="tanggal_sep">
        <?php
        echo $form->field($model, 'tanggal_sep', [
            'inputOptions'=>['id'=>'tanggal_sep', 'data-mask' => '99-99-9999', 'disabled' => true],
            'addon' => [
                'append' => ['content'=>'<i class="fa fa-calendar"></i>'],
            ]
        ]);
        ?>
    </div>

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
</div>
<div class="col-md-3">
    <div class="jenis_peserta">
        <?php
        echo $form->field($model, 'jenis_peserta', [
            'inputOptions'=>['id'=>'jenis_peserta', 'disabled' => true],
        ]);
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
        ])->label(Yii::t('fe', 'Tanggal Kejadian <span color="red">*</span>'));
        
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

        // echo $form->field($model, 'kode_kabupaten', [
        //     'options'=>[ 'class'=>'form-group highlight-addon field-kode_kabupaten required']
        // ])->widget(DepDrop::classname(), [
        //     'data'=>[],
        //     'options'=>['class'=>'select2Bpjs kode_kabupaten', 'id'=>'kode_kabupaten'],
        //     'pluginOptions'=>[
        //         'class'=>'select2',
        //         'depends'=>['kode_provinsi'],
        //         'placeholder'=>'--Pilih Kabupaten--',
        //         'url'=>Url::to(['/pendaftaran/manajemen-bpjs/referensi-kabupaten'])
        //     ]
        // ]);
        
        // echo $form->field($model, 'kode_kecamatan', [
        //     'options'=>[ 'class'=>'form-group highlight-addon field-kode_kecamatan required']
        // ])->widget(DepDrop::classname(), [
        //     'data'=>[],
        //     'options'=>['class'=>'select2Bpjs kode_kecamatan', 'id'=>'kode_kecamatan'],
        //     'pluginOptions'=>[
        //         'class'=>'select2',
        //         'depends'=>['kode_kabupaten'],
        //         'placeholder'=>'--Pilih Kecamatan--',
        //         'url'=>Url::to(['/api/bpjs/referensi-kecamatan'])
        //     ]
        // ]);
        
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
                //'readOnly' => true
            ],
            'options'=>[ 'class'=>'form-group highlight-addon field-no_sep_suplesi required']
        ]);
        ?>
    </div>

</div>
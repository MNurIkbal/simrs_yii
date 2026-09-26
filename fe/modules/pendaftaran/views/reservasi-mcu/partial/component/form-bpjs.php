<?php
    use yii\helpers\Html;
    use yii\helpers\Url;
    use kartik\widgets\DepDrop;
?>
<div id="form-input-bpjs" style="display: none;">
    <div class="form-group" id="form-bpjs-content">
        <div class="col-sm-6">
            <!-- div poli tujuan -->
            <div class="form-poli_tujuan">
                <?php echo $form->field($modelBpjs, 'poli_tujuan', [
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
                echo $form->field($modelBpjs, 'kode_dpjp_melayani')
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
                echo $form->field($modelBpjs, 'asal_rujukan')
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
                echo $form->field($modelBpjs, 'ppk_rujukan')
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
                echo $form->field($modelBpjs, 'no_surat_kontrol', [
                    'inputOptions'=>['id'=>'no_surat_kontrol'],
                    'options'=>[ 'class'=>'form-group field-no_surat_kontrol']
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

            <!-- div tanggal rujukan -->
            <div class="tanggal_rujukan">
                <?php
                echo $form->field($modelBpjs, 'tanggal_rujukan', [
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
                echo $form->field($modelBpjs, 'no_rujukan', [
                    'inputOptions'=>['id'=>'no_rujukan_1'],
                ]);
                ?>
            </div>

            <!-- div tanggal sep -->
            <div class="tanggal_sep">
                <?php
                echo $form->field($modelBpjs, 'tanggal_sep', [
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
                    echo $form->field($modelBpjs, 'no_rekam_medik', [
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
                echo $form->field($modelBpjs, 'kelas_rawat')
                ->dropDownList([
                        1 => 'Kelas I',
                        2 => 'Kelas II',
                        3 => 'Kelas III',
                    ],
                    [
                        'id' => 'kelas_rawat',
                        'class'=>'select2Bpjs',
                        'prompt'=>'— PILIH —',
                    ]
                );
                ?>
            </div>

            <!-- div diagnosa awal -->
            <div class="diagnosa_awal">
                <?php
                echo $form->field($modelBpjs, 'diagnosa_awal')
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
                echo $form->field($modelBpjs, 'no_telp', [
                    'inputOptions'=>['id'=>'no_telp']
                ]);
                ?>
            </div>
        </div>
        <div class="col-sm-6">

            <!-- div catatan -->
            <div class="catatan_sep">
            <?php
            echo $form->field($modelBpjs, 'catatan_sep')->textArea([
                'id'=>'catatan_sep',
                'rows' => 2
            ]);
            ?>
            </div>

            <!-- div centang katarak -->
            <div class="katarak">
            <?php
                echo $form->field($modelBpjs, 'katarak')->checkbox()->label('Centang Katarak, Jika Peserta Tersebut Mendapatkan Surat Perintah Operasi katarak');
            
            ?>
            </div>

            <!-- div kasus kecelakaan -->
            <div class="kasus_kecelakaan">
            <?php
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
            </div>
            
                <div class="kasus_kecelakaan_form" style="display: none;">
                    <?php
                    echo $form->field($modelBpjs, 'tanggal_kejadian', [
                        'inputOptions'=>['id'=>'tanggal_kejadian', 'data-mask' => '99-99-9999'],
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
                        'options'=>['class'=>'select2Bpjs kode_kabupaten', 'id'=>'kode_kabupaten'],
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
                        'options'=>['class'=>'select2Bpjs kode_kecamatan', 'id'=>'kode_kecamatan'],
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
            </div>
    </div>
</div>
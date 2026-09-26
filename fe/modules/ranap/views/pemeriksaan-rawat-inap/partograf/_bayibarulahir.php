<?php

use yii\web\View;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\datetime\DateTimePicker;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use yii\helpers\Url;
use yii\web\JsExpression;
?>
<style>
    .datepicker>div{
        display:block;
    }
</style>
<div class="col-lg-12">
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title"><?=Yii::t('fe', 'Daftar Bayi Baru Lahir')?></h5>
            </div>
            <div class="panel-toolbars clearfix">
                <?=DocoHelpers::generateToolbar([
                    'tambah-bayibarulahir' => [
                        'type' => 'button',
                        'title' => Yii::t('fe', 'Tambah'),
                        'icon' => 'fa fa-plus',
                        'attributes' => [
                            'id' => 'btn-tambah-bayibarulahir',
                            'data-options' => 'click'
                        ],
                    ],
                    'ubah-bayibarulahir' => [
                        'type' => 'button',
                        'title' => Yii::t('fe', 'Ubah'),
                        'icon' => 'fa fa-pencil',
                        'attributes' => [
                            'id' => 'btn-ubah-bayibarulahir',
                            'data-options' => 'click'
                        ],
                    ],
                    'hapus-bayibarulahir' => [
                        'type' => 'button',
                        'title' => Yii::t('fe', 'Hapus'),
                        'icon' => 'fa fa-trash',
                        'attributes' => [
                            'id' => 'btn-hapus-bayibarulahir',
                            'data-options' => 'click'
                        ],
                    ]
                ], '#table-bayibarulahir');?>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <br>
                    <table class="table table-bordered datatable-basic dataTable"  id="table-bayibarulahir" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th></th>
                                <!-- <th>Detail</th> -->
                                <th>Bayi</th>
                                <th>No. Peneng</th>
                                <th>Berat Badan</th>
                                <th>Panjang Badan</th>
                                <th>Tanggal Lahir</th>
                                <th>Jenis Kelamin</th>
                                <th>Penilaian</th>
                                <th>Kondisi Bayi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <br>
                    <p style="color: red">- No. Peneng akan ter-generate secara otomatis setelah proses tambah data bayi sukses dilakukan.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"  data-toggle="collapse" >
                <h5 class="panel-title"><?=Yii::t('fe', 'Bayi Baru Lahir')?></h5>
            </div>
            <div id="collapse-bayibarulahir" class="panel-collapse collapse">
                    
                <?php 
                    $form = ActiveForm::begin([
                        'id' => 'form-bayibarulahir',
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'formConfig' => ['labelSpan' => 2, 'deviceSize' => ActiveForm::SIZE_SMALL],
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                    ]); 
                ?>
                <div class="panel-body">
                    <?=$form->field($mBayiBaruLahir, 'tgl_lahir', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-3'
                                ]
                            ])->widget(DatePicker::classname(), [
                                'name' => 'tgl_lahir',
                                'readonly' => true,
                                'language' => 'en',
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => 'dd-mm-yyyy',
                                ]
                        ]);
                    ?>
                    <div class="form-group highlight-addon field-kelahiranbayiform-jam_lahir required">
                        <div class="col-sm-2">&nbsp;</div>
                        <div class="col-sm-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input name="jam_lahir" type="input" class="form-control jam_lahir" >
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <p class="form-control-static">Jam Lahir</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br/>
                    <div class="form-group highlight-addon field-kelahiranbayiform-jenis_kelamin required">
                        <?= Html::label(Yii::t('fe', 'Jenis Kelamin'), null, ['class' => 'control-label col-sm-2']) ?>
                        <div class="col-sm-8">
                            <?= $form->field($mBayiBaruLahir, 'jenis_kelamin', ['template'=>"{input}\n{hint}\n{error}"])->radioList(
                                @$listData['jenis_kelamin'],
                                [
                                    'item' => function($index, $label, $name, $checked, $value) {
                                        $return = '<label class="modal-radio">';
                                        $return .= $checked ? '<input type="radio" name="'.$name.'" value="'.$value.'" tabindex="3" checked>' : '<input type="radio" name="'.$name.'" value="'.$value.'" tabindex="3" 
                                            id="jenis_kelamin-'.$index.'" class="jenis_kelamin">';
                                        $return .= '<i></i>';
                                        $return .= '<span>'.ucwords($label).'</span>';
                                        $return .= '</label>';
                                        return $return;
                                    }
                                ]
                            ) ?>
                            <div id="error_KelahiranBayiFormjenis_kelamin"></div>
                        </div>
                    </div>
                    <div class="select2-md">
                        <?=$form->field($mBayiBaruLahir,'pegawai_id',[
                            'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-3'
                                ]
                            ])->dropDownList([], [
                                'class' => 'form-control',
                                'prompt' => 'Pilih Dokter'
                        ])?>
                    </div>
                    <div class="select2-md">
                        <?=$form->field($mBayiBaruLahir,'kamartempattidur_id',[
                            'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-3'
                                ]
                            ])->dropDownList([], [
                                'class' => 'form-control',
                                'prompt' => 'Pilih Kamar Ruangan'
                            ])?>
                    </div>
                    <?=$form->field($mBayiBaruLahir,'berat_badan',[
                        'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-2',
                                'wrapper' => 'col-md-3'
                            ],
                        'addon' => [
                            'append' => [
                                'content' => 'Gram'
                            ]]
                        ])->textInput()?>
                    <?=$form->field($mBayiBaruLahir,'tinggi_badan',[
                        'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-2',
                                'wrapper' => 'col-md-3'
                            ],
                        'addon' => [
                            'append' => [
                                'content' => 'Cm'
                            ]]
                        ])->textInput()->label(Yii::t('fe', 'Panjang Badan'))?>
                    <?=$form->field($mBayiBaruLahir,'lingkar_kepala',[
                        'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-2',
                                'wrapper' => 'col-md-3'
                            ],
                        'addon' => [
                            'append' => [
                                'content' => 'Cm'
                            ]]
                        ])->textInput([
                            'class' => 'doco-decimal-wcomma'
                        ])?>
                    <?=$form->field($mBayiBaruLahir,'golongan_darah', [
                        'horizontalCssClasses' => [
                            'label' => 'text-left control-label col-sm-2',
                            'wrapper' => 'col-md-3'
                        ]
                    ])?>
                    <div class="form-group highlight-addon field-kelahiranbayiform-penilaian required">
                        <?= Html::label(Yii::t('fe', 'Penilaian'), null, ['class' => 'control-label col-sm-2']) ?>
                        <div class="col-sm-8">
                            <?= $form->field($mBayiBaruLahir, 'penilaian', ['template'=>"{input}\n{hint}\n{error}"])->radioList(
                                @$listData['penilaian'],
                                [
                                    'item' => function($index, $label, $name, $checked, $value) {
                                        $return = '<label class="modal-radio">';
                                        $return .= $checked ? '<input type="radio" name="'.$name.'" value="'.$value.'" tabindex="3" checked>' : '<input type="radio" name="'.$name.'" value="'.$value.'" tabindex="3" id="penilaian-'.$index.'" class="penilaian">';
                                        $return .= '<i></i>';
                                        $return .= '<span>'.ucwords($label).'</span>';
                                        $return .= '</label>';
                                        return $return;
                                    }
                                ]
                            ) ?>
                            <div id="error_KelahiranBayiFormpenilaian"></div>
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="control-label col-sm-2">Kondisi Bayi</label>
                        <div class="col-sm-8">
                            <div class="row">
                                <div class="col-sm-2">
                                    <label>
                                        <?=Html::radio(
                                        'KelahiranBayiForm[kondisi_bayi]',false,[
                                            'id'=>'kondisi_bayi_normal',
                                            'value'=>DocoConstants::KONDISI_BAYI_NORMAL,
                                            'disabled'=>true])?>
                                    Normal
                                    </label>
                                </div>
                                <br>
                                <div id="form_kondisi_normal" class="col-sm-10">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <?=$form->field($mBayiBaruLahir,'normal_tindakan')->checkboxList(@$listData['normal_tindakan'],['itemOptions'=>['class'=>'normal_options opt_normal_tindakan']])?>
                                        </div>
                                    </div>
                                    <div class="form-group highlight-addon field-kelahiranbayiform-asfiksia required">
                                        <?= Html::label(Yii::t('fe', 'Asfiksia'), null, ['class' => 'control-label col-sm-2']) ?>
                                        <div class="col-sm-8">
                                            <?= $form->field($mBayiBaruLahir, 'asfiksia', ['template'=>"{input}\n{hint}\n{error}"])->radioList(
                                                @$listData['asfiksia'],
                                                [
                                                    'item' => function($index, $label, $name, $checked, $value) {
                                                        $return = '<label class="modal-radio">';
                                                        $return .= $checked ? '<input type="radio" name="'.$name.'" value="'.$value.'" tabindex="3" checked>' : '<input type="radio" name="'.$name.'" value="'.$value.'" text="'.$label.'" tabindex="3" id="asfiksia-'.$index.'" class="asfiksia">';
                                                        $return .= '<i></i>';
                                                        $return .= '&nbsp;&nbsp;<span>'.ucwords($label).'</span>';
                                                        $return .= '</label>';
                                                        return $return;
                                                    }
                                                ]
                                            ) ?>
                                            <div id="error_KelahiranBayiFormasfiksia"></div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <?=$form->field($mBayiBaruLahir,'asfiksia_tindakan')->checkboxList(@$listData['asfiksia_tindakan'],['itemOptions'=>['class'=>'normal_options opt_asfiksia_tindakan']])?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-8 col-sm-offset-4">
                                            <?=$form->field($mBayiBaruLahir,'asfiksia_tindakan_lainnya')->textInput(['placeholder'=>'Lain-lain'])->label(false)?>
                                        </div>
                                    </div>
                                    <div class="row">&nbsp;</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2">
                                    <label>
                                        <?=Html::radio(
                                        'KelahiranBayiForm[kondisi_bayi]',false,[
                                            'id'=>'kondisi_bayi_cacat',
                                            'value'=>DocoConstants::KONDISI_BAYI_CACAT,
                                            'disabled'=>true])?>
                                    Cacat Bawaan
                                    </label>
                                </div>
                                <div class="col-sm-8">
                                    <?=$form->field($mBayiBaruLahir,'cacat_kondisi',['template'=>"{input}\n{hint}\n{error}"])->label(false)?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <label>
                                        <?=Html::radio(
                                        'KelahiranBayiForm[kondisi_bayi]',false,[
                                            'id'=>'kondisi_bayi_hipotermi',
                                            'value'=>DocoConstants::KONDISI_BAYI_HIPOTERMI,
                                            'disabled'=>true])?>
                                    Hipotermi
                                    </label>
                                </div>
                            </div>
                            <div class="row hipotermi last-hipotermi">
                                <div class="col-sm-10">
                                    <input type="text" class="form-control hipotermi_keterangan" name="KelahiranBayiForm[hipotermi_keterangan][]">
                                </div>
                                <div class="col-sm-1">
                                    <?= Html::button('<i class="fa fa-plus"></i>', [
                                        'class' => 'btn btn-sm btn-info btn-tambah-hipotermi',
                                    ]) ?>
                                </div>
                            </div>
                            <div id="error_KelahiranBayiFormkondisi_bayi"></div>
                        </div>
                    </div>
                    <br>
                    <div class="form-group highlight-addon field-kelahiranbayiform-is_asi required">
                        <label class="control-label col-sm-2">Pemberian ASI setelah jam pertama bayi lahir</label>
                        <div class="col-sm-6">
                            <div class="row">
                                <div class="col-sm-2">
                                    <label>
                                        <?=Html::radio(
                                        'KelahiranBayiForm[is_asi]',false,[
                                            'id'=>'is_asi_ya',
                                            'value'=>'1',
                                            'disabled'=>true])?>
                                    Ya
                                    </label>
                                </div>
                                <div class="col-sm-2">
                                    <?=$form->field($mBayiBaruLahir,'keterangan_asi_ya',['template'=>"{input}\n{hint}\n{error}"])->textInput(['class'=>'doco-decimal-wcomma'])->label(false)?>
                                </div>
                                <div class="col-sm-8">
                                    <p class="form-control-static">jam setelah bayi lahir</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2">
                                    <label>
                                        <?=Html::radio(
                                        'KelahiranBayiForm[is_asi]',false,[
                                            'id'=>'is_asi_tidak',
                                            'value'=>'0',
                                            'disabled'=>true])?>
                                    Tidak
                                    </label>
                                </div>
                                <div class="col-sm-6">
                                    <?=$form->field($mBayiBaruLahir,'keterangan_asi_tidak',['template'=>"{input}\n{hint}\n{error}"])->label(false)?>
                                </div>
                            </div>
                            <div id="error_KelahiranBayiFormis_asi"></div>
                        </div>
                    </div>
                    <?=$form->field($mBayiBaruLahir,'warna_kulit', [
                        'horizontalCssClasses' => [
                            'label' => 'text-left control-label col-sm-2',
                            'wrapper' => 'col-md-3'
                        ]
                    ])?>
                    <?=$form->field($mBayiBaruLahir,'masalah_lain', [
                        'horizontalCssClasses' => [
                            'label' => 'text-left control-label col-sm-2',
                            'wrapper' => 'col-md-3'
                        ]
                    ])?>
                    <br>
                    <div class="form-group">
                        <div class="col-sm-8 col-sm-offset-2">
                            <div class="row">
                                <label class="col-sm-2">
                                    Hasil
                                </label>
                                <div class="col-sm-10">
                                    <?=$form->field($mBayiBaruLahir,'hasil',['template'=>"{input}\n{hint}\n{error}"])?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-center">
                    <button type="button" id="simpan-bayi-baru-lahir" class="btn btn-labeled btn-info"><b><i class="fa fa-floppy-o"></i></b>Simpan</button>
                    <button type="button" id="batalsimpan-bayi-baru-lahir" class="btn btn-labeled btn-danger"><b><i class="fa fa-close"></i></b> Batal</button>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs('
    var _bayiId = 0;
    var listNormalTindakan = '.json_encode($listData['normal_tindakan']).';
    var listAsfiksiaTindakan = '.json_encode($listData['asfiksia_tindakan']).';

'.$this->render('js/_bayibarulahir.js'), View::POS_END) ?>
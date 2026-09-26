<?php

use yii\web\View;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use yii\helpers\Url;
use app\modules\components\helpers\DynamicFormHelpers;
use app\modules\ranap\components\widget\DynamicFormWidget;
use yii\web\JsExpression;
?>

<?php 
    $form = ActiveForm::begin([
        'id' => 'asesmenawalForm',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
    ]); 
?>
<style type="text/css">
    /*.stepy-header{
        display: none;
    }*/
</style>
    <?php 
    if ($isStopAkomodasi) {
        ?>
            <div class="row">
                <div class="col-xs-12">
                    <div class="alert alert-danger" role="alert"><strong>Pasien Sudah Melakukan Stop Akomodasi</strong></div>
                </div>
            </div>
        <?php
    }
    ?>
    <fieldset title="1" data-name="asmen_riwayat" onmouseover="this.title='';">
        <legend class="text-semibold"><?=Yii::t('fe', 'Riwayat Kesehatan')?></legend>
        
        <div class="row">
            <div class="col-xs-12 form-asesmenawal">
                <div class="form-group">
                    <div class="flex-container">
                        <div class="flex-50">

                            <!--Cara masuk-->
                            <?php 
                            // var_dump($tanggal_mulai);exit;
                                if(isset($data_pasien['caramasuk_nama'])){
                                    $modelAsesmen->asal_masuk = $data_pasien['caramasuk_nama'];
                                }
                                if(!isset($modelAsesmen->waktu_tiba)){
                                    $modelAsesmen->waktu_tiba = date('d/m/Y H:i:s');
                                }else{
                                    $modelAsesmen->waktu_tiba = date('d/m/Y H:i:s',strtotime($modelAsesmen->waktu_tiba));
                                }
                            ?>
                            <?=$form->field($modelAsesmen, 'asal_masuk')
                                ->textInput([
                                    'class' => 'form-control',
                                    'readonly'=> true , 
                                    ]); ?>

                            <!--waktu tiba-->
                            <?=$form->field($modelAsesmen, 'waktu_tiba')
                                ->widget(DateTimePicker::className(),[
                                    'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                                    'readonly' => true,
                                    'convertFormat' => true,
                                    'pluginOptions' => [
                                        'format' => 'dd/MM/yyyy HH:mm:ss',
                                        'autoclose' => true,
                                        'todayBtn' => true,
                                        'startDate' => $tanggal_mulai
                                    ]
                                ]); 
                            ?>
                        
                            <!--Tanggal assesmen-->
                            <?php 
                                if(!isset($modelAsesmen->tgl_asesmen)){
                                    $modelAsesmen->tgl_asesmen = date('d/m/Y H:i:s');
                                }else{
                                    $modelAsesmen->tgl_asesmen = date('d/m/Y H:i:s',strtotime($modelAsesmen->tgl_asesmen));
                                }
                            ?>
                            <?=$form->field($modelAsesmen, 'tgl_asesmen')
                                ->widget(DateTimePicker::className(),[
                                    'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                                    'readonly' => true,
                                    'convertFormat' => true,
                                    'pluginOptions' => [
                                        'format' => 'dd/MM/yyyy HH:mm:ss',
                                        // 'locale' => 'id',
                                        // 'language' => 'id',
                                        // 'showMeridian' => true,
                                        'autoclose' => true,
                                        'todayBtn' => true,
                                        'startDate' => date('d-m-Y H:i:s')
                                    ]
                                ]); 
                            ?>

                            <!--Assesmen di ambil dari -->
                            <?php 
                                if(!isset($modelAsesmen->asesmen_diambildari)){
                                    $modelAsesmen->asesmen_diambildari = key($list_asesmen_diambildari);
                                }
                            ?>
                            <?= $form->field($modelAsesmen, 'asesmen_diambildari')
                                ->radioList(
                                    $list_asesmen_diambildari,
                                    [
                                        'inline'=>true,
                                        'class'=>'bs-radio'
                                    ]
                                );
                            ?>
                            <div class="form-group fg_diambildari_nama">
                                <label class="control-label col-xs-4">&nbsp;</label>
                                <div class="col-xs-2">
                                    <div class="form-control-static">Nama</div>
                                </div>
                                <div class="col-xs-6">
                                    <?= Html::activeTextInput($modelAsesmen,'diambildari_nama',['class'=>'form-control']) ?>
                                </div>
                            </div>
                            <div class="form-group fg_diambildari_hubungan">
                                <label class="control-label col-sm-4">&nbsp;</label>
                                <div class="col-xs-2">
                                    <div class="form-control-static">Hubungan</div>
                                </div>
                                <div class="col-xs-6">
                                    <?= Html::activeTextInput($modelAsesmen,'diambildari_hub',['class'=>'form-control']) ?>
                                </div>
                            </div>

                            <!--Masuk Dengan -->
                            
                            <?= $form->field($modelAsesmen, 'masuk_dengan')
                                ->radioList(
                                    $list_masuk_dengan,
                                        ['inline'=>true]
                                );
                            ?>
                            
                            <div class="form-group fg_masuk_denganlain">
                                <label class="control-label col-xs-4">&nbsp;</label>
                                <div class="col-xs-6">
                                    <?= Html::activeTextInput($modelAsesmen,'masuk_denganlain',['class'=>'form-control','placeholder'=>'Lain-lain']) ?>
                                </div>
                            </div>

                            <!--Obat dari rumah-->
                            <?php 
                                if(!isset($modelAsesmen->obat_darirumah) || $modelAsesmen->obat_darirumah == false){
                                    $modelAsesmen->obat_darirumah = key($list_ada_tidak);
                                }
                            ?>
                            <?= $form->field($modelAsesmen, 'obat_darirumah')
                                ->radioList(
                                    $list_ada_tidak,
                                    ['inline'=>true]
                                );
                            ?>
                        
                        </div>

                        <div class="flex-50">
                            <!--Hasil Pemeriksaan-->
                            <?php 
                            if(!isset($modelAsesmen->hasil_pemeriksaan)){
                                $modelAsesmen->hasil_pemeriksaan = key($list_ada_tidak);
                            }
                            if($modelAsesmen->hasil_pemeriksaan == false){
                                $modelAsesmen->hasil_pemeriksaan = 0;
                            }
                            ?>
                            <?= $form->field($modelAsesmen, 'hasil_pemeriksaan')
                                ->radioList(
                                    $list_ada_tidak,
                                    ['inline'=>true]
                                );
                            ?>
                            <!--rad-->
                            <?=$form->field($modelAsesmen, 'hasil_rad',[
                                    'addon' => [
                                        'append' => [
                                            ['content'=>'<button type="button" class="btn btn-tambah-hasil-pemeriksaan btn-tambah-hasil-rad">+</button>','asButton'=>true]
                                        ]
                                    ]
                                ])
                                ->textInput([
                                    'class' => 'form-control tf-hasil-pemeriksaan', 
                                    'data-pemeriksaan' => 'rad' 
                                    ]); ?>
                            
                            <!--lab-->
                            <?=$form->field($modelAsesmen, 'hasil_lab',[
                                    'addon' => [
                                        'append' => [
                                            ['content'=>'<button type="button" class="btn btn-tambah-hasil-pemeriksaan btn-tambah-hasil-lab">+</button>','asButton'=>true]
                                        ]
                                    ]
                                ])
                                ->textInput([
                                    'class' => 'form-control tf-hasil-pemeriksaan', 
                                    'data-pemeriksaan' => 'lab' 
                                    ]); ?>
                            <!--lainnya-->
                            <?=$form->field($modelAsesmen, 'hasil_lainnya',[
                                    'addon' => [
                                        'append' => [
                                            ['content'=>'<button type="button" class="btn btn-tambah-hasil-pemeriksaan btn-tambah-hasil-lainnya">+</button>','asButton'=>true]
                                        ]
                                    ]
                                ])
                                ->textInput([
                                    'class' => 'form-control tf-hasil-pemeriksaan',
                                    'data-pemeriksaan' => 'lainnya' 
                                    ]);
                            ?>
                            <!--end-->
                        </div>
                    </div>

                </div>
                <hr>    
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading"  data-toggle="collapse" href="#collapse-riwayat-kesehatan">
                        <h5 class="panel-title"><?=Yii::t('fe', 'Riwayat Kesehatan')?></h5>
                        <div class="heading-elements">
                            <ul class="icons-list">
                                <li><a data-action="collapse" data-toggle="collapse" href="#collapse-riwayat-kesehatan"></a></li>
                            </ul>
                        </div>
                    </div>
                    <div id="collapse-riwayat-kesehatan" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div class="col-xs-6">
                                <?= $form->field($modelAsesmen, 'keluhan_utama')->textArea(); ?>
                                <?php $modelAsesmen->diagnosa_masuk = $init_diagnosa; ?>
                                <?php echo $form->field($modelAsesmen, 'diagnosa_masuk')->widget(Select2::classname(), [
                                    'initValueText' => $init_diagnosa != ''? $init_diagnosa:null,
                                    'data' => [
                                        $init_diagnosa=>$init_diagnosa
                                    ],
                                    'options' => [
                                        'placeholder' => '-- Pilih --',
                                        'class' => 'form-control input-sm select2'
                                    ],
                                    'pluginOptions' => [
                                        'tags' => true,
                                        'tokenSeparators' => [',', '_'],
                                        'minimumInputLength' => 3,
                                        'language' => [
                                            'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                                        ],
                                        'ajax' => [
                                            'url' => \yii\helpers\Url::to(['/ranap/end-point/get-new-diagnosa']),
                                            'dataType' => 'json',
                                            'data' => new JsExpression('
                                                function(params) {
                                                    return {
                                                        q: params.term,
                                                        type: "diagnosa_masuk",
                                                        all_text: 0,
                                                        id_with_text: 1,
                                                        is_perawat: '.$is_perawat.'
                                                    };
                                                }
                                            ')
                                        ],
                                        'escapeMarkup' => new JsExpression ('function(markup){ return markup;}'),
                                        'templateResult' => new JsExpression ('function(diagnosa){ return diagnosa.text;}'),
                                        'templateSelection' => new JsExpression ( 'function (subject) { return subject.text; }' ) ,
                                    ],
                                ]) ?>
                                <div class="form-group">
                                    <label class="control-label col-sm-4"><?=Yii::t('fe', 'Riwayat Kehamilan')?></label>
                                    <div class="col-sm-8">
                                        <div class="col-sm-4">
                                            <label class="control-label col-sm-2">G</label>
                                            <div class="col-sm-9">
                                                <?= Html::activeTextInput($modelAsesmen,'r_kehamilan_g',[
                                                    'type' => 'number',
                                                    'min' => 0,
                                                    'class'=>'form-control',
                                                    'placeholder'=>'G']) ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <label class="control-label col-sm-2">P</label>
                                            <div class="col-sm-9">
                                                <?= Html::activeTextInput($modelAsesmen,'r_kehamilan_p',[
                                                    'type' => 'number',
                                                    'min' => 0,
                                                    'class'=>'form-control',
                                                    'placeholder'=>'P']) ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <label class="control-label col-sm-2">A</label>
                                            <div class="col-sm-9">
                                                <?= Html::activeTextInput($modelAsesmen,'r_kehamilan_a',[
                                                    'type' => 'number',
                                                    'min' => 0,
                                                    'class'=>'form-control',
                                                    'placeholder'=>'A']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4 hide-xs">&nbsp;</label>
                                    <div class="col-sm-2">
                                        <div class="form-control-static">HPHT</div>
                                    </div>
                                    <div class="col-sm-6 no-margin">
                                    <?php
                                        $modelAsesmen->hpht = isset($modelAsesmen->hpht) ? date('d/m/Y H:i:s',strtotime($modelAsesmen->hpht)) : null;
                                        echo DateTimePicker::widget([
                                            'model' => $modelAsesmen,
                                            'attribute' => 'hpht',
                                            'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                                            'readonly' => true,
                                            'convertFormat' => true,
                                            'pluginOptions' => [
                                                'format' => 'dd/MM/yyyy HH:mm:ss',
                                                'autoclose' => true,
                                                'todayBtn' => true
                                            ]
                                        ]);
                                    ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-4 hide-xs">&nbsp;</label>
                                    <div class="col-sm-2">
                                        <div class="form-control-static">HAID</div>
                                    </div>
                                    <div class="col-sm-6 no-margin">
                                        <?php 
                                            if(isset($modelAsesmen->haid_teratur) && $modelAsesmen->haid_teratur == false){
                                                $modelAsesmen->haid_teratur = 0;
                                            }
                                        ?>
                                        <?=Html::activeRadioList($modelAsesmen,'haid_teratur',$list_haid)?>
                                    </div>
                                </div>
                                
                                <?= $form->field($modelAsesmen, 'ketergantungan')
                                    ->checkboxList(
                                        $list_ketergantungan
                                    );
                                ?>

                                <?= $form->field($modelAsesmen, 'r_penyakit_kel')
                                    ->checkboxList(
                                        $list_r_penyakit_kel
                                    );
                                ?>

                                <?= $form->field($modelAsesmen, 'penyakit_kel_lain');
                                ?>
                            </div>
                            <div class="col-xs-6">
                                <?= $form->field($modelAsesmen, 'r_kes_sekarang')->textArea(); ?>

                                <h5>Riwayat Kesehatan</h5>
                                
                                <?php 
                                    if($modelAsesmen->pernah_dirawat == false){
                                        $modelAsesmen->pernah_dirawat = 0;
                                    }
                                ?>
                                <?= $form->field($modelAsesmen, 'pernah_dirawat') 
                                    ->radioList(
                                        $list_ya_tidak,
                                        ['inline'=>true]
                                    );
                                ?>
                                <div class="form-group field-asesmenawalform-tgl_dirawat">
                                    <div class="col-xs-11 col-xs-offset-1">
                                        <label class="control-label col-xs-4" for="asesmenawalform-tgl_dirawat">Tanggal Dirawat</label>
                                        <div class="col-xs-8">
                                        <?php
                                            $modelAsesmen->tgl_dirawat = isset($modelAsesmen->tgl_dirawat) ? date('d/m/Y H:i:s',strtotime($modelAsesmen->tgl_dirawat)) :'';
                                            echo DateTimePicker::widget([
                                                'model' => $modelAsesmen,
                                                'attribute' => 'tgl_dirawat',
                                                'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
                                                'readonly' => true,
                                                'convertFormat' => true,
                                                'pluginOptions' => [
                                                    'format' => 'dd/MM/yyyy HH:mm:ss',
                                                    'autoclose' => true,
                                                    'todayBtn' => true
                                                ]
                                            ]);
                                        ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-xs-11 col-xs-offset-1">
                                        <?=$form->field($modelAsesmen, 'alasan_dirawat') ?>
                                    </div>
                                </div>
                                <?php 
                                    if($modelAsesmen->pernah_tindakan == false){
                                        $modelAsesmen->pernah_tindakan = 0;
                                    }
                                ?>
                                <?= $form->field($modelAsesmen, 'pernah_tindakan')
                                    ->radioList(
                                        $list_ya_tidak,
                                        ['inline'=>true]
                                    );
                                ?>
                                <div class="form-group field-asesmenawalform-tgl_tindakan">
                                    <div class="col-xs-11 col-xs-offset-1">
                                        <label class="control-label col-xs-4" for="asesmenawalform-tgl_tindakan">Tanggal Tindakan</label>
                                        <div class="col-xs-8">
                                        <?php
                                            $modelAsesmen->tgl_tindakan = isset($modelAsesmen->tgl_tindakan) ? date('d/m/Y H:i:s',strtotime($modelAsesmen->tgl_tindakan)) : '';
                                            echo DateTimePicker::widget([
                                                'model' => $modelAsesmen,
                                                'attribute' => 'tgl_tindakan',
                                                'type' => DateTimePicker::TYPE_COMPONENT_PREPEND,
                                                'readonly' => true,
                                                'convertFormat' => true,
                                                'pluginOptions' => [
                                                    'format' => 'dd/MM/yyyy HH:mm:ss',
                                                    'autoclose' => true,
                                                    'todayBtn' => true
                                                ]
                                            ]);
                                        ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-xs-11 col-xs-offset-1">
                                        <?=$form->field($modelAsesmen, 'jeniskegiatantindakan_id')
                                            ->dropDownList(
                                            $list_jenis_operasi,
                                            [
                                                'class'=>'select2  ddl_jenis_kegiatan',
                                                'prompt' => '--Pilih--'
                                            ]); ?>
                                    </div>
                                </div>

                                <?php 
                                    if($modelAsesmen->r_alergi == false){
                                        $modelAsesmen->r_alergi = 0;
                                    }
                                ?>
                                <?= $form->field($modelAsesmen, 'r_alergi')
                                    ->radioList(
                                        $list_ya_tidak,
                                        ['inline'=>true]
                                    );
                                ?>
                                
                                <div class="form-group">
                                    <div class="col-xs-11 col-xs-offset-1">
                                    <?=$form->field($modelAsesmen, 'nama_alergi')
                                    ->textInput([
                                        'class' => 'form-control input-sm', 
                                        ]); ?>
                                    </div>
                                </div>

                                <?php 
                                    if($modelAsesmen->transfusi == false){
                                        $modelAsesmen->transfusi = 0;
                                    }
                                ?>
                                <?= $form->field($modelAsesmen, 'transfusi')
                                    ->radioList(
                                        $list_ya_tidak,
                                        ['inline'=>true]
                                    );
                                ?>
                                <div class="form-group">
                                    <div class="col-xs-11 col-xs-offset-1">
                                    <?=$form->field($modelAsesmen, 'reaksi')
                                    ->textInput([
                                        'class' => 'form-control input-sm', 
                                        ]); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
            </div>
        </div>
    <button id="simpansementara-1" type="button" class="btn btn-info btn-labeled btn-xs "><b><i class="fa fa-floppy-o"></i></b>Simpan Sementara</button>
    <?php 
        $classCetakSementara = 'hidden';
        if($is_update_asesmen){
            $classCetakSementara = '';
        }
    ?>
    </fieldset>

    <fieldset title="2" data-name="skrining-gizi" onmouseover="this.title='';">
        <legend class="text-semibold"><?=Yii::t('fe', 'Skrining Gizi')?></legend>
        <div class="row">
            <div class="col-xs-12">
                <div class="form-group">
                    <label class="control-label col-sm-4">Penurunan Berat Badan yang tidak direncanakan</label>
                    <div class="col-sm-8">
                        <?= $form->field($modelSkrining, 'bb_ygdirencanakan',['template'=>'{input}'])
                            ->radioList(
                                $list_penurunan_bb,
                                [
                                    'inline'=>true,
                                    'class'=>'bs-radio'
                                ]
                            );
                        ?>
                    </div>
                </div>
                <div class="form-group field-group-bb-turun">
                    <div class="col-sm-8 col-xs-offset-4">
                        <?= $form->field($modelSkrining, 'bb_turun',['template'=>'{input}'])
                            ->radioList(
                                $list_gizi_bbturun,
                                [
                                    'inline'=>true,
                                    'class'=>'bs-radio'
                                ]
                            );
                        ?>
                    </div>
                </div>
                <?= $form->field($modelSkrining, 'porsi_makan')
                    ->radioList(
                        $list_gizi_porsi,
                        [
                            'inline'=>true,
                            'class'=>'bs-radio'
                        ]
                    );
                ?>
                <?= $form->field($modelSkrining, 'sakit_berat')
                    ->radioList(
                        $list_gizi_sakitberat,
                        [
                            'inline'=>true,
                            'class'=>'bs-radio'
                        ]
                    );
                ?>
                <div class="form-group">
                    <label class="control-label col-xs-4">Total Skor</label>
                    <div class="col-xs-2">
                        <p id="skor_skrining_gizi" class="form-control-static"></p>
                        <?= $form->field($modelSkrining, 'skor',['template'=>'{input}'])
                            ->hiddenInput();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    <button id="simpansementara-2" type="button" class="btn btn-info btn-labeled btn-xs data-save"><b><i class="fa fa-floppy-o"></i></b>Simpan Sementara</button>
    </fieldset>
    <!-- 
    <fieldset title="2" data-name="asmen_periksafisik" onmouseover="this.title='';">
        <legend class="text-semibold"><?=Yii::t('fe', 'Pemeriksaan Fisik')?></legend>

        <div class="row">
            <div class="col-lg-12">
            </div>
        </div>
    </fieldset>

    <fieldset title="3" onmouseover="this.title='';">
        <legend class="text-semibold"><?=Yii::t('fe', 'Kebutuhan Dasar')?></legend>
    </fieldset>

    <fieldset title="4" onmouseover="this.title='';">
        <legend class="text-semibold"><?=Yii::t('fe', 'Skoring Status Fungsional')?></legend>
    </fieldset>

    <fieldset title="6" onmouseover="this.title='';">
        <legend class="text-semibold"><?=Yii::t('fe', 'Kebutuhan Pendidikan Kesehatan')?></legend>
    </fieldset>

    <fieldset title="7" onmouseover="this.title='';">
        <legend class="text-semibold"><?=Yii::t('fe', 'Daftar Masalah Keperawatan')?></legend>
    </fieldset> -->

    <button type="submit" class="stepy-finish btn btn-xs btn-labeled btn-info"><b><i class="fa fa-floppy-o"></i></b> Submit</button>
    <button id="cetaksementara-1" type="button" class="stepy-finish btn btn-info btn-labeled btn-xs <?=$classCetakSementara?>"><b><i class="fa fa-print"></i></b>Cetak</button>
<?php ActiveForm::end(); ?>

<?php
$this->registerJs('
    var is_disabled = "'.$disabled.'";
    var is_perawatasesmenawal = "'.$is_perawat.'";
    var status_disabled = '.$status_disabled.';
    var skor_gizi_bbturun = '.$skor_gizi_bbturun.';
    var skor_gizi_porsi = '.$skor_gizi_porsi.';
    var skor_gizi_sakitberat = '.$skor_gizi_sakitberat.';
    var pasienStopAkomodasi = '.$isStopAkomodasi.'
    $(document).ready(function(){
        $("#asesmenawalForm :input").not("#cetaksementara-1").prop("disabled", '.$status_disabled.');
        $(".input-group-addon").'.$hide.';
    });

', View::POS_END) ?>
<?php
    
    $this->registerJs($this->render('_asesmenawal.js',['id'=>$pendaftaran_id ,'status_disabled'=>$status_disabled]), View::POS_END);
?>


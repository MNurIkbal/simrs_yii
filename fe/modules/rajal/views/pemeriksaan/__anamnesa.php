<?php

/**
 * @Author: afil
 * @Date:   2018-01-15 17:30:47
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2018-11-22 13:22:57
 * @Description:
 */

use yii\web\View;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

use yii\web\JsExpression;
use yii\helpers\Url;
    if(!empty($status_update)){
        $status_update = $status_update;
    }else{
        $status_update = false;
    }

?>

<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title"><?=Yii::t('fe', 'Asesmen')?></h5>
        <!--<div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>-->
    </div>
    <div class="panel-toolbar clearfix">
        <?=DocoHelpers::generateToolbar([
            'save' => [
                'attributes' => [
                    'form_id' => 'form-anamnesa', 
                    'id' => 'submit-anamnesa',
                    'onClick' => false
                ]
            ],
            // 'reset' => ['attributes' => ['data-parent' => '#form-anamnesa', 'id' => 'reset']],
            'cetak-anamnesa' => [
                'type' => 'button',
                'title' => \Yii::t('fe', 'Print'),
                'icon' => 'fa fa-print',
                'method' => '#',
                'attributes' => [
                    'class'=> ($modelAnamnesa->pendaftaran_id == '') ? 'disabled btn-cetak-anamnesa' : 'btn-cetak-anamnesa',
                    // 'data-change'=>'click',
                    'data-target' => Url::to(['cetak-anamnesa']).'?pendaftaran_id=',
                    'data-id'=>$modelAnamnesa->pendaftaran_id,
                    'data-options'=>'click'
                ]
            ],
        ],'');?>
    </div>
    
    <div class="panel-body">
        <div class="row">
            <div class="form-anamnesa">
                <?php
                $form = ActiveForm::begin([
                    'id' => 'form-anamnesa',
                    'enableAjaxValidation'=>false,
                    'enableClientValidation'=>false,
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                ]);
                ?>

                <div class="flex-container">

                <!--Column 1-->
                    <div class="flex-50">
                        <!--Dokter -->
                        <div class="form-group field-anamnesaform-pegawaidokter_id_view required">
                            <label class="control-label col-sm-4 text-right" style="text-align: right;" for="anamnesaform-pegawaidokter_id_view">Dokter</label>
                            <div class="col-sm-8">
                                <input type="text" id="anamnesaform-pegawaidokter_id_view" class="form-control input-sm" name="AnamnesaForm[pegawaidokter_id_view]" value="<?= $namaDokter ?>" readonly="readonly" aria-required="true">

                                <div class="help-block"></div>
                            </div>
                        </div>
                        <?= Html::hiddenInput('AnamnesaForm[pegawaidokter_id]', $modelAnamnesa->pegawaidokter_id );?>
                        
                        <!--perawat -->
                        <?php
                        if(Yii::$app->docoVars->user("kelompokpegawai_id") == DocoConstants::KELOMPOK_MEDIS ):
                        ?>
                            <?=$form->field($modelAnamnesa, 'pegawaiperawat_id', ['labelOptions' => ['class' => 'text-right']])
                            ->dropDownList(ArrayHelper::map($data_perawat, 'pegawai_id', 'nama_pegawai'), [
                                'class' => 'form-control input-sm select2',
                                'prompt' => Yii::t('fe', '— Pilih Perawat —'),
                            ]); ?>
                        <?php else: ?>
                            <div class="form-group highlight-addon field-anamnesaform-pegawaiperawat_id_view required">
                                <label class="control-label col-sm-4 text-right" style="text-align: right!important;" for="anamnesaform-pegawaiperawat_id_view">Perawat</label>
                                <div class="col-sm-8">
                                    <select id="anamnesaform-pegawaiperawat_id_view" class="form-control input-sm" name="AnamnesaForm[pegawaiperawat_id_view]" disabled="" aria-required="true">
                                    <option value="<?= Yii::$app->docoVars->user("id_pegawai"); ?>" selected=""><?= Yii::$app->session->get('user_identity')['nama_pegawai'] ?></option>
                                    </select>

                                    <div class="help-block"></div>
                                </div>
                            </div>
                            <?= Html::hiddenInput('AnamnesaForm[pegawaiperawat_id]', Yii::$app->docoVars->user("id_pegawai") );?>
                        <?php endif; ?>
                                
                        <!--keluhan utama -->
                        <?=$form->field($modelAnamnesa, 'keluhan_utama', ['labelOptions' => ['class' => 'text-right']])
                            ->textInput([
                                'class' => 'form-control input-sm input-tags',
                                'data-role' => 'tagsinput',
                            ]); ?>

                        <!--Keluhan Tambahan -->
                        <?=$form->field($modelAnamnesa, 'keluhan_tambahan', ['labelOptions' => ['class' => 'text-right']])
                            ->textInput([
                                'class' => 'form-control input-sm input-tags',
                                'data-role' => 'tagsinput',
                        ]); ?>

                        <!--Riwayat Perjalanan -->
                        <?=$form->field($modelAnamnesa, 'riwayat_perjalananpasien', ['labelOptions' => ['class' => 'text-right']])
                            ->textarea([
                                'class' => 'form-control input-sm',
                                // 'value' => '',
                                'placeholder' => Yii::t('fe', $modelAnamnesa->getAttributeLabel('riwayat_perjalananpasien'))
                            ]); ?>

                        <!--lama Sakit-->
                        <?=$form->field($modelAnamnesa, 'lama_sakit', [
                                'labelOptions' => ['class' => 'text-right'],
                                'addon' => ['append' => ['content' => Yii::t('fe', 'Hari')]],
                            ])->textInput([
                                'class' => 'form-control input-sm docoNumberOnly',
                                // 'value' => '',
                                'placeholder' => Yii::t('fe', $modelAnamnesa->getAttributeLabel('lama_sakit'))
                            ]); ?>

                        <!--riwayat penyakit terdahulu-->
                        <?php
                        echo $form->field($modelAnamnesa, 'riwayat_penyakitterdahulu')->widget(Select2::classname(), [
                            // 'data' => $modelAnamnesa->riwayat_penyakitterdahulu,
                            'showToggleAll' => false,
                            'options' => [
                                'multiple' => true,
                                'placeholder' => Yii::t('fe', '— Pilih —'),
                                'class' => 'form-control input-sm select2'
                            ],
                            'pluginOptions' => [
                                'tags' => true,
                                'tokenSeparators' => [',', '_'],
                                'maximumInputLength' => 50,
                                // 'allowClear' => true,
                                'minimumInputLength' => 3,
                                'language' => [
                                    'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                                ],
                                'ajax' => [
                                    'url' => \yii\helpers\Url::to(['/rajal/allow/get-all-diagnosa']),
                                    'dataType' => 'json',
                                    'data' => new JsExpression('
                                        function(params) {
                                            console.log(params);
                                            return {
                                                q:params.term,
                                                page:params.page || 1,
                                                is_valueWithText:0,
                                                id:0,
                                                set_id_as_text:1,
                                            };
                                        }
                                    ')
                                ],
                                'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
                                'templateResult' => new JsExpression('function(diagnosa){ return diagnosa.text;}'),
                                'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
                            ]
                        ])->label($modelAnamnesa->getAttributeLabel('riwayat_penyakitterdahulu'), [
                            'class' => 'text-right control-label col-sm-4'
                        ]);
                        ?>
                        <div class="form-group highlight-addon field-anamnesaform-riwayat_penyakitterdahulu_history">
                            <label class="control-label col-sm-4" for="anamnesaform-riwayat_penyakitterdahulu_history"></label>
                            <div class="col-sm-8">
                                <?php if (!empty($dataRiwayat['riwayat_penyakitDahulu'])): ?>
                                    <?php foreach ($dataRiwayat['riwayat_penyakitDahulu'] as $key => $value): ?>
                                        <li><?= $value ?></li>
                                    <?php endforeach ?>
                                <?php endif ?>
                                <div class="help-block"></div>
                            </div>
                        </div>

                        <!--Riwayat Penyakit Keluarga-->
                        <?php
                        /*echo $form->field($modelAnamnesa, 'riwayat_penyakitkeluarga', ['labelOptions' => ['class' => 'text-right']])
                            ->dropDownList(ArrayHelper::map($data_diagnosa, 'diagnosa_id', 'diagnosa_nama'), [
                                'class' => 'form-control input-sm select2 select2-ex',
                                'multiple'=>'multiple',
                            ]);*/

                            echo $form->field($modelAnamnesa, 'riwayat_penyakitkeluarga')->widget(Select2::classname(), [
                                // 'data' => $modelAnamnesa->riwayat_penyakitterdahulu,
                                'showToggleAll' => false,
                                'options' => [
                                    'riwayat_penyakitkeluarga' => 'anamnesaform-riwayat_penyakitkeluarga',
                                    'multiple' => true,
                                    'placeholder' => Yii::t('fe', '— Pilih —'),
                                    'class' => 'form-control input-sm select2'
                                ],
                                'pluginOptions' => [
                                    'tags' => true,
                                    'tokenSeparators' => [',', '_'],
                                    'maximumInputLength' => 50,
                                    // 'allowClear' => true,
                                    'minimumInputLength' => 3,
                                    'language' => [
                                        'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                                    ],
                                    'ajax' => [
                                        'url' => \yii\helpers\Url::to(['/rajal/allow/get-all-diagnosa']),
                                        'dataType' => 'json',
                                        'data' => new JsExpression('
                                            function(params) {
                                                console.log(params);
                                                return {
                                                    q:params.term,
                                                    page:params.page || 1,
                                                    is_valueWithText:0,
                                                    id:0,
                                                    set_id_as_text:1,
                                                };
                                            }
                                        ')
                                    ],
                                    'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
                                    'templateResult' => new JsExpression('function(diagnosa){ return diagnosa.text;}'),
                                    'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
                                ]
                            ])->label($modelAnamnesa->getAttributeLabel('riwayat_penyakitkeluarga'), [
                                'class' => 'text-right control-label col-sm-4'
                            ]);
                        ?>
                        <div class="form-group highlight-addon field-anamnesaform-riwayat_penyakitkeluarga_history">
                            <label class="control-label col-sm-4" for="anamnesaform-riwayat_penyakitkeluarga_history"></label>
                            <div class="col-sm-8">
                                <?php if (!empty($dataRiwayat['riwayat_penyakitKeluarga'])): ?>
                                    <?php foreach ($dataRiwayat['riwayat_penyakitKeluarga'] as $key => $value): ?>
                                        <li><?= $value ?></li>
                                    <?php endforeach ?>
                                <?php endif ?>
                                <div class="help-block"></div>
                            </div>
                        </div>

                        <!--Riwayat Imunisasi-->
                        <?= $form->field($modelAnamnesa, 'riwayat_imunisasi')->widget(Select2::classname(), [
                            'data' => $listDataDiagnosaImunisasi,
                            'showToggleAll' => false,
                            'options' => [
                                'prompt' => Yii::t('fe', '— Pilih —'),
                                'multiple' => true,
                                'class' =>'select2'
                                ],
                            'pluginOptions' => [
                                'tags' => true,
                                'tokenSeparators' => [',', '_'],
                                'maximumInputLength' => 50,
                                // 'allowClear' => true,
                                'minimumInputLength' => 3,
                            ],
                        ])->label($modelAnamnesa->getAttributeLabel('riwayat_imunisasi'), [
                            'class' => 'text-right control-label col-sm-4'
                        ]);
                        ?>
                        <?php //$form->field($modelAnamnesa, 'riwayat_imunisasi', ['labelOptions' => ['class' => 'text-right']])
                            // ->dropDownList(ArrayHelper::map($data_diagnosaimunisasi, 'diagnosa_id', 'diagnosa_nama'), [
                            // 'class' => 'form-control input-sm select2 select2-ex',
                            // 'multiple'=>'multiple',
                            // 'prompt' => Yii::t('fe', '— Pilih —')
                            //]);
                        ?>
                        <div class="form-group highlight-addon field-anamnesaform-riwayat_imunisasi_history">
                            <label class="control-label col-sm-4" for="anamnesaform-riwayat_imunisasi_history"></label>
                            <div class="col-sm-8">
                                <?php if (!empty($dataRiwayat['riwayat_imunisasi'])): ?>
                                    <?php foreach ($dataRiwayat['riwayat_imunisasi'] as $key => $value): ?>
                                        <li><?= $value ?></li>
                                    <?php endforeach ?>
                                <?php endif ?>
                                <div class="help-block"></div>
                            </div>
                        </div>
                    </div>
                
                <!-- Column 2 -->
                    <div class="flex-50">
                        <!--Tanggal -->
                        <?=$form->field($modelAnamnesa, 'tgl_anamnesis', ['labelOptions' => ['class' => 'text-right']])
                            ->textInput([
                                'class' => 'form-control', //input-sm date
                                'readonly'=>'readonly',
                                'value'=> date('d F Y')
                            ])->label(Yii::t('fe', 'Tanggal Asesmen')); ?>

                        <!--Status Merokok-->
                        <?=$form->field($modelAnamnesa, 'status_merokok', ['labelOptions' => ['class' => 'text-right']])
                            ->radioList([0 => Yii::t('fe', 'Tidak'), 1 => Yii::t('fe', 'Ya')], [
                                'class' => 'status-merokok',
                                'inline'=>true,
                                'value' => $modelAnamnesa->status_merokok ? : 0,
                                'separator' => ' ',
                            ]); ?>

                        <!--Jumlah Rokok -->
                        <?=$form->field($modelAnamnesa, 'jmlrokok_btgperhari', ['labelOptions' => ['class' => 'text-right']])
                            ->textInput([
                                'class' => 'form-control input-sm keterangan-merokok docoNumberOnly',
                            ]); ?>

                        <!--Riwayat Obat-->
                        <?=$form->field($modelAnamnesa, 'riwayat_obatygsering', ['labelOptions' => ['class' => 'text-right']])
                            ->textarea([
                                'class' => 'form-control input-sm',
                                // 'value' => '',
                                'placeholder' => Yii::t('fe', $modelAnamnesa->getAttributeLabel('riwayat_obatygsering'))
                            ]); ?>

                        <!--Riwayat Makan -->
                        <?=$form->field($modelAnamnesa, 'riwayat_makanan', ['labelOptions' => ['class' => 'text-right']])
                            ->textarea([
                                'class' => 'form-control input-sm',
                                // 'value' => '',
                                'placeholder' => Yii::t('fe', $modelAnamnesa->getAttributeLabel('riwayat_makanan'))
                            ]); ?>
                            
                        <div class="form-group highlight-addon field-anamnesaform-riwayat_riwayat_makanan">
                            <label class="control-label col-sm-4" for="anamnesaform-riwayat_riwayat_makanan"></label>
                            <div class="col-sm-8">
                                <?php if (!empty($dataRiwayat['riwayat_makanan'])): ?>
                                    <?php foreach ($dataRiwayat['riwayat_makanan'] as $key => $value): ?>
                                        <li><?= $value ?></li>
                                    <?php endforeach ?>
                                <?php endif ?>
                                <div class="help-block"></div>
                            </div>
                        </div>

                        <!--Riwayat kelahiran -->
                        <?=$form->field($modelAnamnesa, 'riwayat_kelahiran', ['labelOptions' => ['class' => 'text-right']])
                            ->textarea([
                            'class' => 'form-control input-sm',
                            // 'value' => '',
                            'placeholder' => Yii::t('fe', $modelAnamnesa->getAttributeLabel('riwayat_kelahiran'))
                        ]); ?>
                        <div class="form-group highlight-addon field-anamnesaform-riwayat_kelahiran_history">
                            <label class="control-label col-sm-4" for="anamnesaform-riwayat_kelahiran_history"></label>
                            <div class="col-sm-8">
                                <?php if (!empty($dataRiwayat['riwayat_kelahiran'])): ?>
                                    <?php foreach ($dataRiwayat['riwayat_kelahiran'] as $key => $value): ?>
                                        <li><?= $value ?></li>
                                    <?php endforeach ?>
                                <?php endif ?>
                                <div class="help-block"></div>
                            </div>
                        </div>

                        <!--Alergi obat-->
                        <!-- // untuk kebutuhan change field alergi menjadi free text - issue 1699 -->
                        <!-- <?=$form->field($modelAnamnesa, 'riwayat_alergiobat', ['labelOptions' => ['class' => 'text-right']])
                            ->textInput([
                                'class' => 'form-control input-sm input-tags',
                                'data-role' => 'tagsinput',
                            ]); ?>
                        <div class="form-group highlight-addon field-anamnesaform-riwayat_alergiobat_history">
                            <label class="control-label col-sm-4" for="anamnesaform-riwayat_alergiobat_history"></label>
                            <div class="col-sm-8">
                                <?php if (!empty($dataRiwayat['riwayat_alergiobat'])): ?>
                                    <?php foreach ($dataRiwayat['riwayat_alergiobat'] as $key => $value): ?>
                                        <li><?= $value ?></li>
                                    <?php endforeach ?>
                                <?php endif ?>
                                <div class="help-block"></div>
                            </div>
                        </div> -->
                        <?=$form->field($modelAnamnesa, 'riwayat_alergiobat', ['labelOptions' => ['class' => 'text-right']])
                            ->textarea([
                                'class' => 'form-control input-sm',
                                // 'value' => '',
                                'placeholder' => Yii::t('fe', 'Riwayat Alergi')
                            ])->label(Yii::t('fe', 'Riwayat Alergi')); ?>

                        <!--Keterangan -->
                        <?=$form->field($modelAnamnesa, 'keterangan_anamesa', ['labelOptions' => ['class' => 'text-right']])
                            ->textarea([
                                'class' => 'form-control input-sm',
                                // 'value' => '',
                                'placeholder' => Yii::t('fe', 'Keterangan Asesmen')
                            ])->label(Yii::t('fe', 'Keterangan Asesmen')); ?>

                        <!--Resiko Jatuh -->
                        <?= $form->field($modelAnamnesa, 'is_resikojatuh', ['labelOptions' => ['class' => 'text-right']])
                            ->radioList(
                                [0=>'Tidak',1=>'Ya'],
                                [
                                    'inline'=>true,
                                    'class'=>'bs-radio'
                                ]
                            );
                        ?>

                        <!--Nyeri-->
                        <div class="form-group">
                            <label class="control-label col-sm-4 text-right">Nyeri</label>
                            <div class="col-sm-6">
                                <?= $form->field($modelAnamnesa, 'is_nyeri',['template'=>'{input}'])
                                    ->radioList(
                                        [0=>'Tidak',1=>'Ya'],
                                        [
                                            'inline'=>true,
                                            'class'=>'bs-radio'
                                        ]
                                    );
                                ?>
                            </div>
                        </div>

                        <!--Lokasi Nyeri-->
                        <div class="form-group">
                            <div class="col-sm-12 field_nyeri_lokasi">
                                <label class="text-right control-label col-sm-4 field_nyeri_lokasi">Lokasi</label>
                                <?=$form->field($modelAnamnesa, 'lokasi_nyeri', [
                                    'labelOptions' => ['class' => 'text-right'],
                                    // 'addon' => ['append' => ['content' => Yii::t('fe', 'Hari')]],
                                ])->textInput([
                                    'class' => 'form-control input-sm',
                                    // 'value' => '',
                                    'placeholder' => Yii::t('fe', $modelAnamnesa->getAttributeLabel('lokasi_nyeri'))
                                ])->label(false); ?>
                                <?=$form->field($modelAnamnesa,'skala_nyeri', [
                                    'labelOptions' => [
                                        'class' => 'text-right'
                                        ]
                                    ])->textInput([
                                        'class'=>'doco-number skala_nyeri_val',
                                        'maxlength'=>2,
                                        'style'=> 'width:20% !important;',
                                    ])
                                ?>
                            </div>
                        </div>

                    </div>
                    <!--end of Column 2-->
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var update = '.$status_update. ';
    $(".select2-ex").select2();
    // $("#anamnesaform-riwayat_imunisasi").select2();
    $("#anamnesaform-pegawaiperawat_id").select2();
    $("#submit-anamnesa").on("click", function (event) {
        event.preventDefault();
        $().docoForm("click",{
            url : $("#form-anamnesa").attr("action"),
            data : $("#form-anamnesa").serializeArray(),
            success : function(data) {
                if(data.response.pendaftaran_id){
                    $(".btn-cetak-anamnesa").removeClass("disabled").attr("data-id", data.response.pendaftaran_id)
                }
            }
        });
    });
    $(document).ready(function(){
        $("div.field-anamnesaform-riwayat_penyakitterdahulu div.col-sm-8 span.select2 span.selection span.select2-selection--multiple").attr("id","select2_riwayat_penyakitterdahulu");

        $("div.field-anamnesaform-riwayat_penyakitkeluarga div.col-sm-8 span.select2 span.selection span.select2-selection--multiple").attr("id","select2_riwayat_penyakitkeluarga");

        $("div.field-anamnesaform-riwayat_imunisasi div.col-sm-8 span.select2 span.selection span.select2-selection--multiple").attr("id","select2_riwayat_imunisasi");

        $("div.field-anamnesaform-riwayat_alergiobat div.col-sm-8 span.select2 span.selection span.select2-selection--multiple").attr("id","select2_riwayat_alergiobat");

        $("#form-anamnesa :input").prop("disabled", '.$status_update.');
        $("#reset, #submit-anamnesa").prop("disabled", '.$status_update. ');

        $("#anamnesaform-skala_nyeri").on("change keyup",function() {
            var val_skala_nyeri = this.value;
            let sn = $("#anamnesaform-skala_nyeri");
            if(val_skala_nyeri == "0" || val_skala_nyeri == "00"){
                sn.val(1);
            }
        });

        if($("#anamnesaform-is_nyeri input[type=\'radio\']:checked").val() == 1){
            $(".field_nyeri_lokasi, .field-anamnesaform-skala_nyeri").show();
        }

        if($("#anamnesaform-status_merokok input[type=\'radio\']:checked").val() == 1){
            $(".field-anamnesaform-jmlrokok_btgperhari").show();
        }
    });

    $(".field-anamnesaform-jmlrokok_btgperhari").hide();
    $(".field_nyeri_lokasi, .field-anamnesaform-skala_nyeri").hide();

    $(".date").pickadate({
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        locale: {
            format: "DD-MMMM-YYYY"
        }
    });

    $(".input-tags").tagsinput();

    $(document).ready(function(){
        $("input[type=\'radio\'][name=\'AnamnesaForm[status_merokok]\']").change(function(event){
            $("#anamnesaform-jmlrokok_btgperhari").val("");
            if (this.value == 1){
                $(".field-anamnesaform-jmlrokok_btgperhari").show();
            }else{
                $(".field-anamnesaform-jmlrokok_btgperhari").hide();
            }
        });

        $("input[type=\'radio\'][name=\'AnamnesaForm[is_nyeri]\']").change(function(event){
            $("#anamnesaform-skala_nyeri").val(1);
            if (this.value == 1){
                $(".field_nyeri_lokasi, .field-anamnesaform-skala_nyeri").show();
            }else{
                $(".field_nyeri_lokasi, .field-anamnesaform-skala_nyeri").hide();
            }
        });
    });

    if (!update){
        $("#submit-anamnesa").prop("disabled", true);
    }

    $(".datetime").AnyTime_picker({
        format: "%d %M %Y %H:%i:%s",
        monthNames : ["January","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","Nopember","Desember"],
        monthAbbreviations : [ "Jan","Feb","Mar","Apr","Mei","Jun","Jul","Aug","Sep","Okt","Nop","Des" ],
        labelDayOfMonth : "Tanggal",
        labelYear : "Tahun",
        labelMonth : "Bulan",
        labelHour : "Jam",
        labelMinutes: "Menit",
        labelSecond: "Detik",
    });

    $(document).on("click", ".btn-cetak-anamnesa", function(){
        var url = window.location.origin;
        var _target = $(this).attr("data-target");
        var _id = $(this).attr("data-id");

        if(typeof _id != "undefined"){
            window.open(url+_target+_id);
        }
    })

    $("#reset").click(function () {
        docoResetForm($($(this).attr("data-parent")));
        var form = $("#form-anamnesa");
        form[0].reset();

        //$("#anamnesaform-keluhan_utama ,#anamnesaform-keluhan_tambahan, #anamnesaform-riwayat_alergiobat").tagsinput("removeAll");
    })
');
/*
 $(document).ready(function(){
    $("#anamnesaform-pegawaiperawat_id").val('.$modelAnamnesa->pegawaiperawat_id.').trigger("change");
    $("#anamnesaform-keluhan_utama").val('.$modelAnamnesa->keluhan_utama.');
});*/
?>



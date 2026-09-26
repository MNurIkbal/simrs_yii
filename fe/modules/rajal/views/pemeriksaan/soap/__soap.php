<?php

/**
 @Author: Ardi Pratama
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use yii\web\JsExpression;
?>

<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title"><?=Yii::t('fe', 'Reasesmen (SOAP)')?></h5>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
    </div>
    
    <div class="panel-toolbar clearfix">
        <?=DocoHelpers::generateToolbar([
            'custom-save' => [
                'type' => 'submit',
                'title' => Yii::t('fe', 'Simpan'),
                'icon' => 'fa fa-floppy-o',
                'attributes' => [
                    'id' => 'submit-soap',
                    'data-options'=>'click'
                ],
            ],
        ],'');?>
    </div>  
    <?php
        if( count($modelSoap) > 0 ):
    ?>     
    <div class="panel-body">
        <?php
            $ket_is_dokter = 0;
            if($is_dokter){
                $ket_is_dokter = 1;
            }
        ?>
        <?=Html::hiddenInput('ket_is_dokter',$ket_is_dokter,['id'=>'ket_is_dokter'])?>
        <div class="row form-input-ttv">
            <div class="col-md-12">
            <div class="panel panel-flat">
                <div class="panel-body">

                    <?php $form = ActiveForm::begin([
                        'id' => 'form-soap-ttv',
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                    ]);
                    ?>
                        <fieldset id="fieldset-input-ttv">
                            <div class="form-inline">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="form-group field-beratbadan_kg required">
                                            <label for="soaprjform-beratbadan_kg"><?=Yii::t('fe', 'Berat Badan')?><span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <?=Html::activeTextInput($modelSoap,'beratbadan_kg',['maxlength'=>6,'class'=>'form-control doco-decimal-wcomma','placeholder'=>'Berat Badan'])?>
                                                <span class="input-group-addon">Kg</span>
                                            </div>
                                            <div id="errorBeratBadan" class="has-error"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group field-tinggibadan_cm required">
                                            <label for="soaprjform-tinggibadan_cm"><?=Yii::t('fe', 'Tinggi Badan')?><span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <?=Html::activeTextInput($modelSoap,'tinggibadan_cm',['maxlength'=>6,'class'=>'form-control doco-decimal-wcomma','placeholder'=>'Tinggi Badan'])?>
                                                <span class="input-group-addon">cm</span>
                                            </div>
                                            <div id="errorTinggiBadan" class="error"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group field-imt required">
                                            <label for="soaprjform-imt"><?=Yii::t('fe', 'Indeks Masa Tubuh')?><span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <?=Html::activeTextInput($modelSoap,'imt',['maxlength'=>6,'class'=>'form-control doco-decimal-wcomma','readonly'=>true,'tabindex'=>"-1"])?>
                                                <span class="input-group-addon">Kg/m2</span>
                                            </div>
                                            <div id="errorImt" class="has-error"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group required">
                                            <label for="soaprjform-keterangan_imt"><?=Yii::t('fe', 'Kategori Indeks Masa Tubuh')?><span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <?=Html::activeTextInput($modelSoap,'keterangan_imt',['maxlength'=>6,'class'=>'form-control','readonly'=>true,'tabindex'=>"-1"])?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-inline">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="form-group field-tekanandarah required">
                                            <label><?=Yii::t('fe', 'Tekanan Darah')?><span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <?=Html::activeTextInput($modelSoap,'td_systolic',['maxlength'=>5,'class'=>'form-control doco-decimal-wcomma','placeholder'=>'Sistolik','col-index'=>1])?>
                                                <span class="input-group-addon">/</span>
                                                <?=Html::activeTextInput($modelSoap,'td_diastolic',['maxlength'=>5,'class'=>'form-control doco-decimal-wcomma','placeholder'=>'Diastolik','col-index'=>1])?>
                                                <span class="input-group-addon">mmHg</span>
                                            </div>
                                            <div id="errorSystolic" class="has-error"></div>
                                            <div id="errorDiastolic" class="has-error"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group field-pernapasan required">
                                            <label>Pernafasan<span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <?=Html::activeTextInput($modelSoap,'pernapasan',['maxlength'=>5,'class'=>'form-control doco-decimal-wcomma','placeholder'=>'Pernafasan','col-index'=>2])?>
                                                <span class="input-group-addon">x/m</span>
                                            </div>
                                            <div id="errorPernapasan" class="has-error"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group field-detaknadi required">
                                            <label>Detak Nadi<span class="text-danger">*</span> &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;</label>
                                            <div class="input-group">
                                                <?=Html::activeTextInput($modelSoap,'detaknadi',['maxlength'=>5,'class'=>'form-control doco-decimal-wcomma','placeholder'=>'Detak Nadi'])?>
                                                <span class="input-group-addon">x/m</span>
                                            </div>
                                            <div id="errorDetaknadi" class="has-error"></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group field-suhutubuh required">
                                            <label>Suhu Tubuh<span class="text-danger">*</span> &nbsp; &nbsp;</label>
                                            <div class="input-group">
                                                <?=Html::activeTextInput($modelSoap,'suhutubuh',['maxlength'=>5,'class'=>'form-control doco-decimal-wcomma','placeholder'=>'Suhu Tubuh'])?>
                                                <span class="input-group-addon">c</span>
                                            </div>
                                            <div id="errorSuhutubuh" class="has-error"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-3">
                                    <?=$form->field($modelSoap,'is_nyeri')
                                            ->radioList([0=>'Tidak',1=>'Ya'],[
                                                'inline'=>true,
                                                'class'=>'bs-radio'
                                                ])
                                    ?>
                                    <?=$form->field($modelSoap,'skala_nyeri')->textInput(['maxlength'=>'3','class'=>'docoNumberOnly'])?>
                                </div>
                                <div class="col-lg-3">
                                    <?=$form->field($modelSoap,'is_resikojatuh')
                                            ->radioList([0=>'Tidak',1=>'Ya'],[
                                                'inline'=>true,
                                                'class'=>'bs-radio'
                                                ])
                                    ?>
                                </div>
                            </div>
                        </fieldset>
                    <?php 
                        ActiveForm::end() 
                    ?>
                </div>
            </div>
            </div>
        </div>
        <div class="row form-input-soap">
            <div class="col-lg-9">

                <?php $form = ActiveForm::begin([
                    'id' => 'form-soap',
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
                ]) ?>
                <fieldset id="fieldset-input-soap" style="border:1px solid #ddd; padding:10px;">
                    <legend style="font-weight:bold;"><?= Yii::t('app', 'SOAP') ?></legend>

                    <!-- Subject -->
                    <?= $form->field($modelSoap, 'subject')->textarea([
                        'class' => 'form-control input-sm',
                        'rows' => '3',
                    ])->label('S <small class="text-muted">(subjektif)</small>') ?>

                    <!-- Object -->
                    <?= $form->field($modelSoap, 'object')->textarea([
                        'class' => 'form-control input-sm',
                        'rows' => '3',
                    ])->label('O <small class="text-muted">(objektif)</small>') ?>

                    <!-- Asesmen -->
                    <div class="form-group">
                        <?= Html::label('A  <small class="text-muted">(assesment)</small>', null, ['class' => 'control-label col-sm-3']) ?>
                        <div class="col-sm-9">
                            <!-- Diagnosa utama -->
                            
                        <?php 

                            echo $form->field($modelSoap, 'a_diag_utama')->widget(Select2::classname(), [
                                'initValueText' => $text_diag_utama != ''? $text_diag_utama:null,
                                'options' => [
                                    'placeholder' => '-- Pilih --',
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
                                        'url' => \yii\helpers\Url::to(['/rajal/end-point/get-new-diagnosa']),
                                        'dataType' => 'json',
                                        'data' => new JsExpression('
                                            function(params) {
                                                return {
                                                    q: params.term,
                                                    page:params.page || 1,
                                                    type: "diagnosa_masuk",
                                                    all_text: 0,
                                                    id_with_text: 1,
                                                }; 
                                            }
                                        ')
                                    ],
                                    'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
                                    'templateResult' => new JsExpression('function(diagnosa){ return diagnosa.text;}'),
                                    'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
                                ]
                            ]);
                            ?>
                            <style type="text/css">
                                .select2-container .select2-selection--multiple .select2-selection__choice {
                                      max-width: 100%;
                                      box-sizing: border-box;
                                      white-space: normal;
                                      word-wrap: break-word;
                                    }
                            </style>
                        <?= $form->field($modelSoap, 'a_diag_penyerta[]',[
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-3',
                                        'wrapper' => 'col-md-9'
                                    ]
                                ])->dropDownList($temp,[
                                        'class' => 'select2 word-wrapper',
                                        'id' => 'a_diag_penyerta_form',
                                        'multiple'=>'multiple',
                                        // 'value'=> $listValue
                                ]); 
                        ?>
                        </div>
                    </div>

                    <!-- Planning -->
                    <?= $form->field($modelSoap, 'planning')->textarea([
                        'class' => 'form-control input-sm',
                        'rows' => '3',
                    ])->label('P <small class="text-muted">(planning)</small>') ?>

                    <!-- Catatan Dokter -->
                    <?= $form->field($modelSoap, 'catatan_dokter')->textarea([
                        'class' => 'form-control input-sm',
                        'rows' => '3',
                    ]) ?>

                    <!-- Hidden inputs -->
                    <?= Html::activeHiddenInput($modelSoap, 'pendaftaran_id') ?>
                    <?= Html::activeHiddenInput($modelSoap, 'pegawai_id') ?>
                    <?= Html::activeHiddenInput($modelSoap, 'pasien_id') ?>
                    <?= Html::activeHiddenInput($modelSoap, 'ruangan_id') ?>
                </fieldset>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>      
    <?php
    $this->registerJs('
        $("#form-soap").yiiActiveForm("add", {
            id: "soaprjform-beratbadan_kg",
            name: "beratbadan_kg",
            container: ".field-beratbadan_kg",
            input: "#soaprjform-beratbadan_kg",
            error: "#errorBeratBadan",
            validate:  function (attribute, value, messages, deferred, $form) {
                yii.validation.number(value,messages,{pattern:/^(([1-9][0-9]*){1,3}(\,[0-9]+)?|0(\,[0-9]+)+)$/,message:"Format Salah","skipOnEmpty":1});
            }
        });
        $("#form-soap").yiiActiveForm("add", {
            id: "soaprjform-tinggibadan_cm",
            name: "tinggibadan_cm",
            container: ".field-tinggibadan_cm",
            input: "#soaprjform-tinggibadan_cm",
            error: "#errorTinggiBadan",
            validate:  function (attribute, value, messages, deferred, $form) {
                yii.validation.number(value,messages,{pattern:/^(([1-9][0-9]*){1,3}(\,[0-9]+)?|0(\,[0-9]+)+)$/,message:"Format Salah","skipOnEmpty":1});
            }
        });
        $("#form-soap").yiiActiveForm("add", {
            id: "soaprjform-imt",
            name: "imt",
            container: ".field-imt",
            input: "#soaprjform-imt",
            error: "#errorImt",
            validate:  function (attribute, value, messages, deferred, $form) {
                yii.validation.number(value,messages,{pattern:/^(([1-9][0-9]*){1,3}(\,[0-9]+)?|0(\,[0-9]+)+)$/,message:"Format Salah","skipOnEmpty":1});
            }
        });
        $("#form-soap").yiiActiveForm("add", {
            id: "soaprjform-td_systolic",
            name: "td_systolic",
            container: ".field-tekanandarah",
            input: "#soaprjform-td_systolic",
            error: "#errorSystolic",
            validate:  function (attribute, value, messages, deferred, $form) {
                yii.validation.number(value,messages,{pattern:/^(([1-9][0-9]*){1,3}(\,[0-9]+)?|0(\,[0-9]+)+)$/,message:"Format Systolic Salah","skipOnEmpty":1});
            }
        }); 
        $("#form-soap").yiiActiveForm("add", {
            id: "soaprjform-td_diastolyc",
            name: "td_diastolic",
            container: ".field-tekanandarah",
            input: "#soaprjform-td_diastolic",
            error: "#errorDiastolic",
            validate:  function (attribute, value, messages, deferred, $form) {
                yii.validation.number(value,messages,{pattern:/^(([1-9][0-9]*){1,3}(\,[0-9]+)?|0(\,[0-9]+)+)$/,message:"Format Diastolik Salah","skipOnEmpty":1});
            }
        });
        $("#form-soap").yiiActiveForm("add", {
            id: "soaprjform-pernapasan",
            name: "pernapasan",
            container: ".field-pernapasan",
            input: "#soaprjform-pernapasan",
            error: "#errorPernapasan",
            validate:  function (attribute, value, messages, deferred, $form) {
                yii.validation.number(value,messages,{pattern:/^(([1-9][0-9]*){1,3}(\,[0-9]+)?|0(\,[0-9]+)+)$/,message:"Format Salah","skipOnEmpty":1});
            }
        });
        $("#form-soap").yiiActiveForm("add", {
            id: "soaprjform-detaknadi",
            name: "detaknadi",
            container: ".field-detaknadi",
            input: "#soaprjform-detaknadi",
            error: "#errorDetaknadi",
            validate:  function (attribute, value, messages, deferred, $form) {
                yii.validation.number(value,messages,{pattern:/^(([1-9][0-9]*){1,3}(\,[0-9]+)?|0(\,[0-9]+)+)$/,message:"Format Salah","skipOnEmpty":1});
            }
        });
        $("#form-soap").yiiActiveForm("add", {
            id: "soaprjform-suhutubuh",
            name: "suhutubuh",
            container: ".field-suhutubuh",
            input: "#soaprjform-suhutubuh",
            error: "#errorSuhutubuh",
            validate:  function (attribute, value, messages, deferred, $form) {
                yii.validation.number(value,messages,{pattern:/^(([1-9][0-9]*){1,3}(\,[0-9]+)?|0(\,[0-9]+)+)$/,message:"Format Salah","skipOnEmpty":1});
            }
        });

        var data_kategori_imt = '.json_encode($data_kategori_imt).';
        function getImt(amount){
            var def = "IMT diluar range";
            $.each(data_kategori_imt,function(index){
                var data_bmi = data_kategori_imt[index];
                var bmi_maks =parseFloat(data_bmi.bmi_maksimum);
                var bmi_min = parseFloat(data_bmi.bmi_minimum);
                var regex_amount = /\,/g;
                amount = amount.replace(regex_amount,".");
                var angka_bmi = parseFloat(amount);
                if(angka_bmi >= bmi_min && angka_bmi <= bmi_maks){
                    def = data_bmi.bmi_defenisi;
                }
            });
            return def;
        }
        $(".field-soaprjform-skala_nyeri").hide();
        $("#submit-soap").on("click", function(event) {
            // Prevent default
            event.preventDefault();

            var ket_is_dokter = $("#ket_is_dokter").val();
            var form_data = [];
            form_data = $("#form-soap-ttv,#form-soap").serializeArray();

            $(this).docoForm("click", {
                url: "/rajal/pemeriksaan/create-soap?id='.$encryptedPendaftaranId.'&is_dokter="+ket_is_dokter,
                data: form_data,
                success : function(response) {
                    $("#content-soap").docoLoad({
                        url: "/rajal/pemeriksaan/soap?id='.$encryptedPendaftaranId.'",
                        dataType: "html",
                        success : function(data) {
                        }
                    });
                }
            });
        });

        $("#soaprjform-beratbadan_kg").on("keyup",function(){
            if($(this).val() !== "" && $("#soaprjform-tinggibadan_cm").val() !== ""){
                $("#soaprjform-imt").val("");
                var tinggi = $("#soaprjform-tinggibadan_cm").val();
                var regex1 = /\,/g;
                var berat = $(this).val();
                berat = berat.replace(regex1,".");
                var total_imt = 0;
                total_imt = parseFloat(berat) / ((parseFloat(tinggi)/100) * (parseFloat(tinggi)/100));
                total_imt = parseFloat(total_imt).toFixed(1);
                var regex = /\./g;
                total_imt = total_imt.replace(regex,",");
                $("#soaprjform-imt").val(total_imt);
                var kategori_imt = getImt(total_imt);
                $("#soaprjform-keterangan_imt").val(kategori_imt);
            }else{
                $("#soaprjform-imt").val(0);
                $("#soaprjform-keterangan_imt").val("");
            }
        });
        $("#soaprjform-tinggibadan_cm").on("keyup",function(){
            if($(this).val() !== "" && $("#soaprjform-beratbadan_kg").val() !== ""){
                $("#soaprjform-imt").val("");
                var tinggi = $(this).val();
                var regex1 = /\,/g;
                var berat = $("#soaprjform-beratbadan_kg").val();
                berat = berat.replace(regex1,".");
                var total_imt = 0;
                total_imt = parseFloat(berat) / ((parseFloat(tinggi)/100) * (parseFloat(tinggi)/100));
                total_imt = parseFloat(total_imt).toFixed(1);
                var regex = /\./g;
                total_imt = total_imt.replace(regex,",");
                $("#soaprjform-imt").val(total_imt);
                var kategori_imt = getImt(total_imt);
                $("#soaprjform-keterangan_imt").val(kategori_imt);
            }else{
                $("#soaprjform-imt").val(0);
                $("#soaprjform-keterangan_imt").val("");
            }
        });

        $("input.docoNumberFloat").on("keydown", function(e) {
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
                (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                (e.keyCode == 67 && (e.ctrlKey === true || e.metaKey === true)) ||
                (e.keyCode == 88 && (e.ctrlKey === true || e.metaKey === true)) ||
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                        return;
            }
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105) && (e.keyCode < 188 || e.keyCode > 188)) {
                e.preventDefault();
                return;
            }
        });

        $(document).ready(function(){
           $("#submit-soap").prop("disabled", '.$status_periksa.');
            if($("#soaprjform-imt").val() !== ""){
                var total_imt = $("#soaprjform-imt").val();
                total_imt = parseFloat(total_imt).toFixed(1);
                var regex = /\./g;
                total_imt = total_imt.replace(regex,",");
                var kategori_imt = getImt(total_imt);
                $("#soaprjform-keterangan_imt").val(kategori_imt);
            }
            if($("input:radio[name=\'SoapRjForm[is_nyeri]\']:checked").val() == "1"){
                $(".field-soaprjform-skala_nyeri").show();
            }
            $("#soaprjform-is_nyeri").change(function(){
                $("#soaprjform-skala_nyeri").val("");
                var sourceVal = $("input:radio[name=\'SoapRjForm[is_nyeri]\']:checked").val();
                if(sourceVal == "1"){
                    $(".field-soaprjform-skala_nyeri").show();
                }else{
                    $(".field-soaprjform-skala_nyeri").hide();
                }
            });
            
            var valDiagPenyerta = '.json_encode($valDiagPenyerta).';
             var data = {
            id: 4783,
            text: "L81.1 - Chloasma"
        };
            $("#a_diag_penyerta_form").select2({
                placeholder: "— Pilih —",
                minimumInputLength: 3, 
                multiple : true,
                tags : true,
                ajax : {
                    url: baseUrl+"rajal/end-point/get-data-diagnosa",
                    dataType: "json",
                    quietMillis: 250,
                    data: function (params) {
                        var query = {
                        search: params,
                    }
                    return {
                        q: params.term,
                        page:params.page || 1,
                        type: "diagnosa_penyerta",
                        all_text: 0,
                        id_with_text: 1,
                        }; 
                    },
                    processResults: function (data) {
                        return {
                            results: data.result
                        };
                    },
                    dropdownCssClass: "bigdrop",
                    escapeMarkup: function (m) { 
                        return m; 
                    },
                },
                createTag: function(params) {
                    var term = $.trim(params.term);
                    if(term === "") { return null; }

                    var optionsMatch = false;

                    this.$element.find("option").each(function() {
                    if(this.value.toLowerCase().indexOf(term.toLowerCase()) > -1) {
                        optionsMatch = true;
                    }
                    });

                    if(optionsMatch) {
                        return null;
                    }
                    return {id: term, text: term};
                },
                cache: true
            });
            var data = '.json_encode($callbackDiagPenyerta).';
            $.each(data, function(k,v){
                var option = new Option(v.text, v.id+"_"+v.text, true, true);
                $("#a_diag_penyerta_form").append(option).trigger("change");
            });
        });
    ');
    ?>
    <?php else:?>
        <div class="panel-body" style="text-align: center;"><?= Yii::t('fe', 'Tidak Ada Data')?> </div>
    <?php endif ?>
</div>

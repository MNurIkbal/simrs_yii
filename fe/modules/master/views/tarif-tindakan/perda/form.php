<?php
    use yii\web\View;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\Breadcrumbs;
    use yii\web\JsExpression;
    // use yii\widgets\ActiveForm;
    use app\components\DocoHelpers;
    use kartik\widgets\Select2;
    use kartik\widgets\DepDrop;
    use kartik\widgets\DatePicker;
    use kartik\widgets\ActiveForm;
    $this->title = $title;
?>
<style>
    .datepicker>div{
        display:block;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?= $this->title ;?></b></h3>
            </div>
            <div class="panel-toolbar clearfix">
                <?=
                    DocoHelpers::generateToolbar([
                        'save' =>[
                            'attributes' => [
                                'id' => 'btn-submit-perda'
                            ]
                        ],
                        'reset'=>[
                            'attributes'=>[
                                'id' => 'btn-reset-perda',
                                'data-parent'=>'#perda-form',
                            ]
                        ],
                        'kembali' => [
                            'title' => \Yii::t('fe', 'Kembali'),
                            'icon' => 'fa fa-arrow-left',
                            'attributes' => [
                                'id' => 'btn-back-perda',
                                'class' => 'spa',
                                'data-options' => 'click',
                                'data-content'=>'content-perda',
                                'data-url' => '/master/tarif-tindakan/perda',
                            ]
                        ],
                    ]);
                ?>
            </div>
            <div class="panel-body">
                <?php 
                $form = ActiveForm::begin([
                        'id' => 'perda-form', 
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        'validateOnSubmit' => false, 
                        'formConfig' => [
                                'labelSpan' => 4,
                                'deviceSize' => ActiveForm::SIZE_MEDIUM
                            ],
                        'options' => [
                                'class' => 'form-horizontal',
                                'role' => 'form',
                            ]
                        ]);  
                ?>
                <!-- start -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="col-md-6">
                            <?= $form->field($model, 'perda_no',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ]
                                ])->textInput([
                                        'class' => 'form-control'
                                ])->label(Yii::t('fe', 'Nomer Perda')); 
                            ?>
                            <?= $form->field($model, 'perdanama_sk',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ]
                                ])->textInput([
                                        'class' => 'form-control'
                                ])->label(Yii::t('fe', 'Nama Perda')); 
                            ?>
                            <?= $form->field($model, 'nama_lainnya',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ]
                                ])->textInput([
                                        'class' => 'form-control'
                                ])->label(Yii::t('fe', 'Nama Lainnya')); 
                            ?>
                            <?php
                             $model->perda_tgl = isset($model->perda_tgl) ? date('d-M-Y',strtotime($model->perda_tgl)) : date('d-M-Y');
                             ?>
                            <?= $form->field($model, 'perda_tgl', ['horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-md-4',
                                    'wrapper' => 'col-md-8'
                                ]])->widget(DatePicker::classname(), [
                                    'name' => 'date_12',
                                    'language' => 'en',
                                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                    'value' => date('dd-M-yyyy'),
                                        // 'readonly' => true,
                                    'pluginOptions' => [
                                        'autoclose' => true,
                                        'format' => 'dd-M-yyyy',
                                            // 'endDate' => "0d",
                                    ]
                                ]);
                            ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'ditetapkan_oleh',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ]
                                ])->textInput([
                                        'class' => 'form-control'
                                ])->label(Yii::t('fe', 'Ditetapkan Oleh')); 
                            ?>
                            <?= $form->field($model, 'perda_tentang',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ]
                                ])->textArea([
                                        'class' => 'form-control catatan',
                                ])->label(Yii::t('fe', 'Detail')); 
                            ?>

                            <?=
                            $form->field($model, 'is_active', [
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ]
                                ])->checkbox(['label' => 'Aktif'])->label(Yii::t('fe', 'Status'))
                            ?>


                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
                <!-- end -->
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    if($('#perdatarifform-perda_no').val() != ""){
        $('#perdatarifform-perda_no').attr('readonly','readonly');
    }
    if($('#perdatarifform-perda_no').val() == "" ){
        $('#perdatarifform-perda_no').removeAttr('readonly');
    }

    $('#btn-reset-perda').on('click', function (event) {
        var _form = $("#perda-form");
        _form[0].reset();
    });

    $("#perdatarifform-perdanama_sk").on("change", function(){
        var nama = $(this).val();
        $("#perdatarifform-nama_lainnya").val(nama);
        /*var nama_lainnya = $("#komponentarifform-komponentarif_namalainnya").val();
        if(nama_lainnya == '') {
            $("#komponentarifform-komponentarif_namalainnya").val(nama);
        }
        else {
            $("#komponentarifform-komponentarif_namalainnya").val(nama_lainnya);
        }*/
    });

    $(document).ready(function($) {
        $(".input-group-addon.kv-date-remove").remove();
    });

    $('#btn-submit-perda').on('click', function (event) {
        var _form = $('#perda-form');
        var submit_btn = $(this);
        $(this).docoForm("click", {
            url : _form.attr('action'),
            data : _form.serializeArray(),
            beforeSend: function () {
                ajaxLoading(submit_btn);
            },
            success : function(data) {
                console.log(data);
                var perdatarif_id_before = data.response.perdatarif_id_before;
                var perdatarif_id_now = data.response.perdatarif_id_now;
                var is_active = data.response.is_active;
                var perdanama_sk = data.response.perdanama_sk;

                if (perdatarif_id_before) {
                    (new PNotify({
                        title: data.response.title,
                        text: data.response.message,
                        addclass: "alert alert-success alert-arrow-right alert-styled-right",
                        type: "success",
                        buttons: {
                            closer: false,
                            sticker: false
                        },
                        hide: false,
                        confirm: {
                            confirm: true,
                            buttons: [
                                {
                                    text: 'Ya',
                                    addClass: 'btn btn-xs btn-success',
                                },
                                {
                                    text: 'Tidak',
                                    addClass: 'btn btn-xs btn-danger',
                                }
                            ]
                        },
                        history: {
                            history: false
                        }
                    })).get().on('pnotify.confirm', function() {

                        $.ajax({
                            url: '/master/tarif-tindakan/update-actived',
                            type: 'GET',
                            data: {
                                    perdatarif_id_before: perdatarif_id_before,
                                    perdatarif_id_now: perdatarif_id_now,
                                },
                            beforeSend: function () {
                                ajaxLoading(submit_btn);
                            },
                            success : function(res) {
                                docoNotification("success", i18next.t("Berhasil"), i18next.t("Data berhasil di simpan"));
                                $("#btn-back-perda").click();

                            },
                            error : function(res){
                            }
                        })
                        

                    }).on('pnotify.cancel', function() {
                        docoNotification("success", i18next.t("Berhasil"), i18next.t("Data berhasil di simpan"));
                        $("#btn-back-perda").click();
                        $("#perdatarifform-is_active").prop('checked', false);
                    });
                    
                }else {
                    $(" .select2 ").select2();
                    setTimeout(function () {
                        $("#btn-back-perda").click();
                    }, 1000);
                }
            },
            error : function(res){
                $(this).find('.error').show();
                $(document).ready(function () {
                    $("span.help-block,.error").show();
                });
                // return false;
            }
        });
    });
    $(document).on('keydown', null, 'alt+s', function (event) {
        $("##content-perda, #btn-submit-perda").click();
    });

    function backtoMenus(){
         $(location).attr('href', '/master/tarif-tindakan');
    }
    function goBack() {
        window.history.back();
    }

    function ajaxLoading(element) {
        $(element).attr("disabled", true);
        $(element).html("<i class=\"fa fa-spinner fa-pulse fa-1x fa-fw\"></i>");
    }
</script>
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
<style type="text/css">
    .radio-inline + .radio-inline, .checkbox-inline + .checkbox-inline {
         margin-left: 0px !important; 
        margin-right: 40px !important;
    }
    label.radio-inline {
        margin-right: 50px;
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
                                'id' => 'btn-submit-komponen'
                            ]
                        ],
                        'reset'=>[
                            'attributes'=>[
                                'id' => 'btn-reset-komponen',
                                'data-parent'=>'#komponen-form',
                            ]
                        ],
                        'kembali' => [
                            'title' => \Yii::t('fe', 'Kembali'),
                            'icon' => 'fa fa-arrow-left',
                            'attributes' => [
                                'id' => 'btn-back-komponen',
                                'class' => 'spa',
                                'data-options' => 'click',
                                'data-content'=>'content-komponen',
                                'data-url' => '/master/tarif-tindakan/komponen',
                            ]
                        ],
                    ]);
                ?>
            </div>
            <div class="panel-body">
                <?php 
                $form = ActiveForm::begin([
                        'id' => 'komponen-form', 
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
                <div class="row">
                    <div class="col-lg-12">
                        <div class="col-md-6">
                            <?= $form->field($model, 'komponentarif_kode',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ]
                                ])->textInput([
                                        'class' => 'form-control'
                                ])->label(Yii::t('fe', 'Kode Komponen')); 
                            ?>
                            <?= $form->field($model, 'komponentarif_nama',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ]
                                ])->textInput([
                                        'class' => 'form-control'
                                ])->label(Yii::t('fe', 'Nama Komponen')); 
                            ?>
                            <?= $form->field($model, 'komponentarif_namalainnya',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ]
                                ])->textInput([
                                        'class' => 'form-control'
                                ])->label(Yii::t('fe', 'Nama Lainnya')); 
                            ?>
                            <div class="form-group highlight-addon field-komponentarifform-jenis_komponen">
                                <label class="control-label text-left control-label col-sm-4" for="komponentarifform-jenis_komponen">
                                    <?= Yii::t("fe", "Jenis Komponen") ?>
                                </label>
                                <div class="col-sm-8">
                                    <?=Html::activeRadioList($model, 'jenis_komponen', $jenisKomponen, [
                                        'item'=>function($index, $label, $name, $checked, $value) use ($model){
                                                $check = "";
                                                if($model->jenis_komponen == $value){
                                                    $check = 'checked="checked"';
                                                }
                                                $return = '<label class="radio-inline">';
                                                $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" tabindex="3"'.$check.' class="radiocheck-'.$value.'">';
                                                $return .= ' <i></i>';
                                                $return .= '<span>' . ucwords($label) . '</span>';
                                                $return .= '</label>';
                                                return $return;
                                            }
                                    ])?>
                                </div>
                            </div>
                            <div class="col-sm-offset-4" id="error_komponentarifformjenis_komponen"></div>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, "persen_delegasi", [
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ],
                                'addon' => [
                                    'append' => [
                                        'content'=>'%'
                                    ]
                                ],
                            ])->textInput([
                                    "class" => "text-right form-control persen_delegasi",
                                    'id' => 'persen_delegasi',
                                    "placeholder" => '0,00'
                            ])->label(Yii::t('fe', 'Persentase Delegasi')); ?>

                            <?= $form->field($model, 'catatan',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ]
                                ])->textArea([
                                        'class' => 'form-control catatan',
                                ])->label(Yii::t('fe', 'Catatan')); 
                            ?>
                            <?=$form->field($model, 'is_active', [
                            'labelOptions' => [
                                    'class' => 'text-left control-label col-md-4'
                                ]
                            ])->checkbox()?>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        /*Menu Komponen start*/
        $(document).on("change","#persen_delegasi", function (e) {
            e.preventDefault();
            docoHelper.convertToDecimal(this, '.', ',' ,true );
        });

        if($('#komponentarifform-komponentarif_kode').val() != ""){
            $('#komponentarifform-komponentarif_kode').attr('readonly','readonly');
        }
        if($('#komponentarifform-komponentarif_kode').val() == "" ){
            $('#komponentarifform-komponentarif_kode').removeAttr('readonly');
        }

        $("#komponentarifform-komponentarif_nama").on("change", function(){
            var nama = $(this).val();
            $("#komponentarifform-komponentarif_namalainnya").val(nama);
            /*var nama_lainnya = $("#komponentarifform-komponentarif_namalainnya").val();
            if(nama_lainnya == '') {
                $("#komponentarifform-komponentarif_namalainnya").val(nama);
            }
            else {
                $("#komponentarifform-komponentarif_namalainnya").val(nama_lainnya);
            }*/
        });
    });
    $('#btn-reset-komponen').on('click', function (event) {
        var _form = $("#komponen-form");
        _form[0].reset();
    });

    $('#btn-submit-komponen').on('click', function (event) {
        var _form = $('#komponen-form');
        $(this).docoForm("click", {
            url : _form.attr('action'),
            data : _form.serializeArray(),
            success : function(data) {
                $(" .select2 ").select2();
                setTimeout(function () {
                    $("#btn-back-komponen").click();
                    // backtoMenus();
                 }, 1000);
            },
            error : function(data){
                return false;
            }
        });
    });
    $(document).on('keydown', null, 'alt+s', function (event) {
        $("#content-komponen, #btn-submit-komponen").click();
    });

    function backtoMenus(){
          $(location).attr('href', '/master/tarif-tindakan');
    }
</script>
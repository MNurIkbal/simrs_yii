<?php
    use yii\web\View;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\widgets\Breadcrumbs;
    use yii\web\JsExpression;
    // use yii\widgets\ActiveForm;
    use app\components\DocoHelpers;
    use app\components\DHtml;
    use kartik\widgets\Select2;
    use kartik\widgets\DepDrop;
    use kartik\widgets\DatePicker;
    use kartik\widgets\ActiveForm;
    use kartik\typeahead\Typeahead;
    $this->title = DHtml::getTitleMenu('Form Tarif Bedah');
?>
<style type="text/css">
    .radio label, .checkbox label {
        padding-left: 0px;
    }
    input[type='checkbox']:hover {
         box-shadow: 0 0 5px 0px #fff; 
    }
    input[type='checkbox']:focus {
        box-shadow: 0 0 5px 0px #fff;
    }
    /*label.required label.control-label:after {
      content: " *";
      color: red; 
    }
    .required:after {
        color: #e32;
        content: ' *';
        display:inline;
    }*/
    .checkbox label {
        padding-left: 30px !important;
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
                                'id' => 'btn-submit-tarif'
                            ]
                        ],
                        // 'reset'=>[
                        //     'attributes'=>[
                        //         'id' => 'btn-reset-tarif',
                        //         'data-parent'=>'#tarif-form',
                        //     ]
                        // ],
                        'kembali' => [
                            'title' => \Yii::t('fe', 'Kembali'),
                            'icon' => 'fa fa-arrow-left',
                            'attributes' => [
                                'id' => 'btn-back-tarif',
                                'class' => 'spa',
                                'data-options' => 'click',
                                'data-content'=>'content-tarif',
                                'data-url' => '/master/tarif-akomodasi-bedah/tarif',
                            ]
                        ],
                    ]);
                ?>
            </div>
            <div class="panel-body">
                <?php 
                $form = ActiveForm::begin([
                        'id' => 'tarif-form', 
                        // 'type' => ActiveForm::TYPE_HORIZONTAL,   
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
                        <div class="col-md-8">
                            <div class="panel panel-flat">
                                <div class="panel-heading">
                                    <h5 class="panel-title"><?=Yii::t('fe', 'Form Tarif Akomodasi Bedah')?></h5>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                        <?= $form->field($model, 'kegiatanoperasi_id')->dropDownList(@$additional_data['kegiatanoperasi'],[
                                            'class' => 'form-control select2 col-md-8',
                                            'prompt' => Yii::t('fe', '— Pilih Kegiatan Operasi —'),
                                        ])->label(Yii::t('fe', 'Kegiatan Operasi'),[
                                            'class'=>'text-left control-label col-md-4'
                                            ]
                                        ); ?>
                                    
                                        <?= $form->field($model, 'kelaspelayanan_id')->dropDownList(@$additional_data['kelaspelayanan'],[
                                            'class' => 'form-control select2 col-md-8',
                                            'prompt' => Yii::t('fe', '— Pilih Kelas Pelayanan —'),
                                        ])->label(Yii::t('fe', 'Kelas Pelayanan'),[
                                            'class'=>'text-left control-label col-md-4'
                                            ]
                                        ); ?>

                                        <?= $form->field($model, 'perdatarif_id')->dropDownList(@$additional_data['perdatarif'],[
                                            'class' => 'form-control select2 col-md-8',
                                            'prompt' => Yii::t('fe', '— Pilih Perda —'),
                                        ])->label(Yii::t('fe', 'Perda / SK'),[
                                            'class'=>'text-left control-label col-md-4'
                                            ]
                                        ); ?>
                                        <?= $form->field($model, 'tarif', [
                                            'labelOptions' => ['class' => 'text-left'],
                                            'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-qw',
                                                    'wrapper' => 'col-md-12'
                                            ],
                                            'addon'=>[
                                                'prepend' => [
                                                    'content'=>'Rp. '
                                                ]
                                            ],
                                            ])->textInput([
                                                'placeholder' => Yii::t('fe', 'Tarif'),
                                                'class' => 'form-control input-sm doco-number',
                                                'autocomplete' => "off",
                                                'id' => 'tarif',
                                            ])->label(false); ?>
                                            <?=
                                                $form->field($model, 'is_active', [
                                                    'horizontalCssClasses' => [
                                                        'label' => 'text-right control-label col-sm-4',
                                                        'wrapper' => 'col-md-7',
                                                    ],
                                                ])->checkbox([
                                                    'label' => 'Aktif',
                                                    'id' => 'is_active',
                                                    'class'=>'text-left control-label col-md-4'
                                                ])
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('#tarif-form').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) { 
            e.preventDefault();
            return false;
        }
    });
    $('#btn-submit-tarif').on('click', function (event) {
        var _form = $('#tarif-form').serializeArray();
        var _url = $('#tarif-form').attr('action');

        $(this).docoForm("click", {
            url : _url,
            data : _form,
            success : function(data) {
                // $(" .select2 ").select2();
                setTimeout(function () {
                    $("#btn-back-tarif").click();
                 }, 1000);
            },
            error : function(data){
                // return false;
            }
        });
    });

    $(document).on('keydown', null, 'alt+s', function (event) {
        $("##content-tarif, #btn-submit-tarif").click();
    });

    $(document).on("change","#persen_cyto", function (e) {
        e.preventDefault();
        docoHelper.convertToDecimal(this, '.', ',' ,true );
    });

    $('.select2-selection__clear').remove();
    

    $(document).ready(function($) {
    
        $('.select2-selection__clear').remove();
        $('#tarifakomodasibedahform-kelaspelayanan_id').select2();
        $('#tarifakomodasibedahform-kegiatanoperasi_id').select2();
        $('#tarifakomodasibedahform-perdatarif_id').select2();

        $(".select2").attr("style", "width:96% !important; margin-left:10px;" );
        $("#label_paket").attr("style", "width:96% !important; margin-left:10px;" );
        // $("#is_active").attr("style", "width:96% !important; margin-left:10px;" );
        //$("#komponentarif_id,.select2").attr("style", "width:100%; margin-left:10px;" );
        $(".input-group").attr("style", "padding: 0px 10px;" );
        $("#persen_cyto").attr("style", "padding: 0px 10px; !important" );
        
    });

</script>
<?php 

$this->registerJs("
    var status_edit = ".$status_edit."
    var opsiPerda = ".json_encode($opsiPerda)."
    var counter = 0", View::POS_END);
    $this->registerJs($this->render('../js/tarif.js'), View::POS_END);
?>
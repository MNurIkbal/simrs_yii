<?php

/**
 * @Author: Sigit
 * @Date:   2018-11-28 17:25:58
 */

use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
?>

<?php $form = ActiveForm::begin([   
        'id' => 'baseprice-form', 
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
    echo Html::hiddenInput('obatalkes_id', $model->obatalkes_id, [
        'class' => 'obatalkes_id'
    ]);
?>
<div class="modal-header bg-inverse">
    <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
    <h5 class="modal-title"><?= $title?></h5>
</div>

<div class="modal-body">
     <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'obatalkes_nama', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-5 text-bold',
                        'wrapper' => 'col-md-6'
                    ]
                ])->staticInput([
                    'class' => 'obatalkes_nama text-right',
                ])->label(Yii::t('fe', 'Nama Obat Alkes')); ?>
            
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'hn_last', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-5 text-bold',
                        'wrapper' => 'col-md-6'
                    ]
                ])->staticInput([
                    'class' => 'hn_last text-right',
                ])->label(Yii::t('fe', 'Harga Netto Terakhir'). ' (Rp.)'); ?>
            
        </div>
    </div>
    <!-- <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'harga_sugesstion', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-5 text-bold',
                        'wrapper' => 'col-md-6'
                    ]
                ])->staticInput([
                    'class' => 'harga_sugesstion text-right',
                ])->label(Yii::t('fe', 'Suggestion System'). ' (Rp.)'); ?>
            
        </div>
    </div> -->
    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'harganetto', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-5 text-bold',
                        'wrapper' => 'col-md-6'
                    ]
                ])->textInput([
                    'class' => 'hn_last text-right doco-number-decimal',
                    'id'=> 'harganetto'
                ])->label(Yii::t('fe', 'Harga Dasar Yang Digunakan'). ' (Rp.)'); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'catatan', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-5 text-bold',
                        'wrapper' => 'col-md-6'
                    ]
                ])->textArea([
                    'class' => 'hn_last text-left',
                    'id'=> 'catatan'
                ])->label(Yii::t('fe', 'catatan')); ?>
        </div>
    </div>
</div>

<div class="modal-footer">
    <?= Html::button("<b><i class='fa fa-floppy-o'></i></b>&nbsp;Simpan", [
        'class' => 'btn btn-info btn-labeled btn-xs',
        'id' => 'btn-submit'
    ]) ?>
    <?= Html::button("<b><i class='fa fa-arrow-left'></i></b>&nbsp;Kembali",[
        'class' => 'btn btn-info btn-labeled btn-xs',
        'data-dismiss' => 'modal'
    ]); ?>
</div>
<?php ActiveForm::end(); ?>

<script type="text/javascript">
    $(document).on('keydown', null, function (event) {
        if (event.key == 'Enter') {
            return false;
        }
    });
    
    var _count = 0;
    $("#button-back").on("click", function(event){
        event.preventDefault();
        var harganetto = $("#harganetto").val();
        if (harganetto != '') {
            _count ++;
            var _countNotif = $('.alert-warning').length;
            if (_countNotif  == 0) {
                (new PNotify({
                title: "Peringatan",
                text: "Data belum tersimpan, apakah anda yakin kembali ?",
                addclass: "alert alert-warning alert-arrow-right alert-styled-right",
                type: "warning",
                buttons: {
                    closer: false,
                    sticker: false
                },
                hide: false,
                confirm: {
                    confirm: true,
                    buttons: [{
                            text: 'Ya',
                            addClass: 'btn btn-xs btn-warning',
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
                })).get().on('pnotify.confirm', function () {
                    $("#modal_backdrop").modal('toggle');
                }).on('pnotify.cancel', function () {
                    return false;
                });
            } else {
                return false;
            }
        } else {
            $("#modal_backdrop").modal('toggle');
        }
    });

    $('#btn-submit').on('click', function (event) {
        var _form = $('#baseprice-form');
        var payload = _form.serializeArray();

        var harganetto = payload[2].value.replace(/[\.]+/g, "");
        payload[2].value = parseFloat(harganetto.replace(",", "."));

        $(this).docoForm("click", {
            url : _form.attr('action'),
            data : payload,
            success : function(data) {
                var form = $("#baseprice-form");
                form[0].reset();
                table.draw();
                $("#modal_backdrop").modal('toggle');
            },
            error : function(data){
                $(this).find('.error').hide();
                $(document).ready(function () {
                    $("div.help-block").remove();
                });
            }
        });
    });

    $(document).on("keyup", ".doco-number-decimal", function(e){
        match        = (/(\d{0,9})[^,]*((?:\,\d{0,2})?)/g).exec(this.value.replace(/[^\d,]/g, ""));
        valDisDesimal = match[1] + match[2];
        this.value = valDisDesimal;
    });


    $(document).on("keyup", ".doco-number-decimal", function(e) {
        var angka = $(this).val();
        var number_string = angka.toString().toString().replace(/\./g, ","),
            split = number_string.split(","),
            absvalue = split[0];
        var _split = split[0].replace(/\-/g, "");
        var sisa = _split.length % 3,
            rupiah = _split.substr(0, sisa),
            ribuan = _split.substr(sisa).match(/\d{1,3}/gi);
        var simbol = absvalue.match(/\-/gm);
        simbol = simbol == null ? "" : simbol;

        if (ribuan) {
            separator = sisa ? "." : "";
            rupiah += separator + ribuan.join(".");
        }

        var _value = simbol + (split[1] != undefined ? rupiah + "," + split[1] : rupiah);

        if (_value == "NaN") {
            _value = 0;
        }
        $(this).val(_value);
    });

</script>

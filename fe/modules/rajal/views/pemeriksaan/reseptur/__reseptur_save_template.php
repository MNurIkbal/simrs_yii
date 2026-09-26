<?php

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><b><?=Yii::t('fe', 'Konfirmasi Template');?></b></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <?php $form = ActiveForm::begin([
                'id' => 'form',
                'enableAjaxValidation' => false,
                'enableClientValidation' => false,
                'type' => ActiveForm::TYPE_HORIZONTAL,
                'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
            ]) ?>
                <p>Apakah anda ingin menambahkannya ke template ?</p>
                <p>Jika ya, masukan nama template</p>
                <?= 
                    $form->field($model, 'nama_template', ['labelOptions' => ['class' => 'text-left']])
                        ->textInput([
                            'class' => 'form-control input-sm',
                            'placeholder' => 'Masukan nama template resep',
                        ])->label(false);
                ?>
                <span>contoh : resep flu dewasa</span>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="pull-left">
        <?= Html::button("<i class='fa fa-floppy-o'></i> ". Yii::t('fe', 'Simpan'), ['class' => 'btn bg-teal btn-sm', 'id' => 'btn-add']) ?>
        <?= Html::button("<i class='fa fa-arrow-left'></i> ". Yii::t('fe', 'Kembali'),[
            'class' => 'btn bg-slate btn-sm',
            'data-dismiss' => 'modal'
        ]); ?>
    </div>
</div>

<?php
$this->registerJs('
    // $(document).on("keypress",function(e) {
    //     if(e.which == 13) {
    //         $(#btn-add).trigger("click");
    //     }
    // });

    $(document).on("click", "#btn-add", function(e) {
        e.preventDefault();
        let data = new FormData();
        let dataPost = $("#form").serializeArray();
        let signa = $(".signa_edit").get();
        let qty = $(".qty_add").get();
        for (let i = 0; i < signa.length; i++) {
            data.append(signa[i].name, signa[i].value);
        }
        for (let i = 0; i < qty.length; i++) {
            data.append(qty[i].name, qty[i].value);
        }
        $.each(dataPost, function (key, value) {
            data.append(value.name, value.value);
        });
        $(this).docoForm("click", {
            url: "/rajal/pemeriksaan/save-template?id='.$id.'",
            data: data,
            dataType: false, // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            method: "post",
            isUpload: true,
            success: function(res) {
                var options = $("<select/>");
                if(res.response !== null) {
                    var data_template = res.response.data;
                    if(typeof data_template !== "undefined") {
                        options.append("<option value>--Pilih template--</option>");
                        $.each(data_template, function (key, val) {
                            options.append("<option value="+val.reseptemp_id+">"+val.reseptemp_nama+"</option>");
                        });
                        $("#select_template").html(options.html());
                    }
                    else {
                        options.append("<option value>--Pilih template--</option>");
                        $("#select_template").html(options.html());
                    }
                }
                else {
                    options.append("<option value>--Pilih template--</option>");
                    $("#select_template").html(options.html());
                }
                
                $("#form")[0].reset();
                $("#modal_backdrop").modal("toggle");
            }
        });
    });
');
?>
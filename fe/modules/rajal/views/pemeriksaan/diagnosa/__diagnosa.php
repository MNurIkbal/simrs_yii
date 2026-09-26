<?php

/**
 * @Author: afil
 * @Date:   2018-01-19 09:23:10
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-03-22 10:31:21
 * @Description: 
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DHtml;
use kartik\widgets\DepDrop;
use yii\web\JsExpression;
$isdokter = DHtml::cekHakAkses('is-dokter') ? true : false;
?>
<style>
    .my-legend .legend-title {
    text-align: left;
    margin-bottom: 8px;
    font-weight: bold;
    font-size: 90%;
    }
  .my-legend .legend-scale ul {
    margin: 0;
    padding: 0;
    float: left;
    list-style: none;
    }
  .my-legend .legend-scale ul li {
    display: block;
    float: left;
    width: 50px;
    margin-bottom: 6px;
    margin-right: 5px;
    text-align: center;
    font-size: 80%;
    list-style: none;
    }
  .my-legend ul.legend-labels li span {
    display: block;
    float: left;
    height: 15px;
    width: 50px;
    border: solid 0.2px;
    }
  .my-legend .legend-source {
    font-size: 70%;
    color: #999;
    clear: both;
    }
  .my-legend a {
    color: #777;
    }
</style>
<div class="row">
    <!-- form bmhp start -->
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h5 class="panel-title"><?=Yii::t('fe', 'Diagnosa')?></h5>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                </ul>
            </div>
        </div>
        <div class="panel-toolbar clearfix">
            <?=DocoHelpers::generateToolbar([
                'custom-save' => [
                    'type'=>'button',
                    'title' => Yii::t('fe', 'Simpan'),
                    'icon' => 'fa fa-floppy-o',
                    'attributes' => [
                        'class'=>'save-diagnosa',
                        'action' => '/rajal/pemeriksaan/save-session-diagnosa?pendaftaran_id='. $pendaftaran_id .'&pasien_id='. $pasien_id,
                        'method'=>'json',
                        'data-options'=>'click',
                    ],
                ],
                'reset' => [
                    'attributes' => [
                        'id' => 'reset-diagnosa',
                        'data-options' => 'click'
                    ],
                ],
                'pdf' => [
                    'attributes' => [
                        'url' => '/rajal/pemeriksaan/export-pdf-diagnosa?pendaftaran_id='. $pendaftaran_id .'&pasien_id='. $pasien_id,
                    ],
                ],
            ], '#tabel-diagnosa');
            ?>
        </div>
        <div class="panel-body">
            <?php 
            $form = ActiveForm::begin([
                'id' => 'form-diagnosa',
                'enableAjaxValidation'=>false, 
                'enableClientValidation'=>false,
                'type' => ActiveForm::TYPE_HORIZONTAL,
                'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
            ]); 
            ?>
           
            <div class="row" id="section-diagnosa">
                <div class="col-md-5 kelompok-diagnosa-field">
                    <?=$form->field($modelMorbiditas, 'kelompokdiagnosa_id', ['labelOptions' => ['class' => 'text-right']])
                        ->dropDownList(ArrayHelper::map($data_kelompokdiagnosa, 'kelompokdiagnosa_id', 'kelompokdiagnosa_nama'), [
                            'id' => 'kelompokdiagnosa_id',
                            'class' => 'form-control input-sm select2',
                            'prompt' => Yii::t('fe', '-- Pilih kelompok diagnosa --'), 
                        ]);?>
                </div>
                <div class="col-md-6">
                    <?php /* $form->field($modelMorbiditas, 'diagnosa_id', ['labelOptions' => ['class' => 'text-right']])->widget(DepDrop::classname(), [
                            'options'=>[
                                'id' => 'selectDiagnosa',
                                'class' => 'form-control select2 select-diagnosa'
                            ],
                            'pluginOptions'=>[
                                'depends' => ['kelompokdiagnosa_id'],
                                'placeholder' => \Yii::t('fe', '-- Pilih diagnosa --'),
                                'url' => \yii\helpers\Url::to(['/rajal/end-point/get-new-diagnosa']),
                                // 'url' => Url::to(['/rajal/pemeriksaan/get-list-diagnosa-by-versi-tabular']),
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
                            ]
                        ]); 
                        */ ?>
                    <?= $form->field($modelMorbiditas, 'diagnosa_id', ['labelOptions' => ['class' => 'text-right']])->dropDownlist([], [
                            'id' => 'selectDiagnosa',
                            'class' => 'form-control input-sm',
                            'prompt' => Yii::t('fe', '-- Pilih Diagnosa --'), 
                    ]);
                    ?>
                    <?=Html::activeHiddenInput($modelMorbiditas, 'diagnosa_nama', ['id' => 'morb_diagnosanama'])?>
                    <?=Html::activeHiddenInput($modelMorbiditas, 'diagnosa_kode', ['id' => 'morb_diagnosakode'])?>
                    <?=Html::activeHiddenInput($modelMorbiditas, 'kelompokdiagnosa_nama', ['id' => 'kelompokdiagnosa_nama'])?>
                </div>
                <div class="col-md-1">
                    <?= Html::submitButton('<i class="fa fa-plus"></i>', ['class' => 'btn bg-success save-form']); ?>
                </div>
            </div>
     
            <div class="row">
                <div class="col-md-12">
                    <hr>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
            <div class="row">
                <div class="col-md-12">
                    <div class='my-legend'>
                        <div class='legend-title'>Keterangan</div>
                        <div class='legend-scale'>
                            <ul class='legend-labels'>
                                <li><span style='background:#ffec8b;'></span>Data Belum Disimpan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row text-center">
                <div class="col-md-12" style="width: 100%;">
                    <table id="tabel-diagnosa" class="table datatable-basic table-striped table-hover dataTable no-footer table-framed table-diagnosa" width="100%" style="overflow-x: scroll;">
                        <thead>
                            <tr class="bg-inverse">
                                <th><?=Yii::t('fe', 'kelompokdiagnosa_nama')?></th>
                                <th><?=Yii::t('fe', 'diagnosa_kode')?></th>
                                <th><?=Yii::t('fe', 'diagnosa_nama')?></th>
                                <th><?=Yii::t('fe', 'Aksi')?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- form bmhp end -->
</div>

<script>
    $(document).ready(function(){
       /* $('#auto-paket').select2({
            minimumInputLength: 2,
            ajax: {
                url: "<?= Url::home() . (Yii::$app->controller->module->id) . '/tindakan/auto-paket' ?>",
                data: function (params) {
                var query = {
                    search: params.term,
                    type: 'public'
                }
                return query;
                }
            }
        });*/
    });
</script>

<?php
$this->registerJs('
    var status_update = "'.$status_update.'";
    var isdokter = "'.$isdokter.'";
    var jenisdiagnosa = "diagnosa_masuk";
    var tabel_diagnosa = $("#tabel-diagnosa").docoTabel({
        scrollX: true,
        filter: false,
        sorting: [[1, "asc"]], 
        processing: true,
        serverSide: true,
        paging: false,
        ajax: baseUrl+"rajal/pemeriksaan/get-data-diagnosa-session?id=' . $pendaftaran_id . '&pasien_id='.$pasien_id.'",
        rowCallback: function(row, data, index, fullindex){
            if(typeof data.color != "undefined"){
                $("td", row).css("background-color", data.color);
            }
        },
        columns: [
            {title: "' . (\Yii::t("fe", "kelompokdiagnosa_nama")) . '", data: "kelompokdiagnosa_nama"},
            
            {title: "' . (\Yii::t("fe", "diagnosa_kode")) . '", data: "diagnosa_kode"},
            {title: "' . (\Yii::t("fe", "diagnosa_nama")) . '", data: "diagnosa_nama"},
            {
                title: "' . (\Yii::t("fe", "Aksi")) . '",
                data: "aksi",
                searchable: false,
                orderable: false,
            },
        ],
    });

    $(document).ready(function(){
        $("#form-diagnosa :input").prop("disabled", '.$status_update.');
        $("#reset-diagnosa").prop("disabled", '.$status_update.');
        $(".save-diagnosa").prop("disabled", '.$status_update.');
        $(".delete-diagnosa").prop("disabled", '.$status_update.');
        if(isdokter != "1"){
            $(".save-diagnosa").prop("disabled", true);
            $(".save-form").prop("disabled", true);
        }
        $("#selectDiagnosa").attr("disabled", true);
        $("#selectDiagnosa").select2({
            maximumInputLength: 50,
            minimumInputLength:3,
            tags: true,
            ajax: {
                url: "'.\yii\helpers\Url::to(['/rajal/end-point/get-new-diagnosa']).'",
                dataType: "json",
                data: function(params) {
                    return {
                        q: params.term,
                        page:params.page || 1,
                        type: jenisdiagnosa,
                    }; 
                }
            },
            escapeMarkup:function(markup){ return markup;},
            templateResult: function(diagnosa){ return diagnosa.text;},
            templateSelection: function (subject) { return subject.text; },
        });
    });
    $(document).on("click", ".save-form", function(e){
        e.preventDefault();
        if($("#kelompokdiagnosa_id").val() == ""){
            docoNotification("warning", "Terjadi Kesalahan", "Kelompok Diagnosa Tidak Boleh Kosong!");
            return false;
        }else if($("#selectDiagnosa").val() == ""){
            docoNotification("warning", "Terjadi Kesalahan", "Diagnosa Tidak Boleh Kosong!");
            return false;
        }
        $("#form-diagnosa").trigger("submit");
    })
    $("#form-diagnosa").docoForm("submit",{
        skipConfirm: true,
        success : function(data) {
            $("#kelompokdiagnosa_id").val(null).trigger("change");
            $("#selectDiagnosa").val(null).trigger("change");
            $("#kelompokdiagnosa_nama").val("");
            $("#morb_diagnosanama").val("");
            $("#morb_diagnosakode").val("");
            tabel_diagnosa.draw();
        }
    });
    // function deprecated
    // dipindah ke js/_pemeriksaan.js
    // $(document).on("click", ".delete-diagnosa", function (e) {
    //     e.preventDefault();
    //     $(this).docoForm("delete", {
    //         skipConfirm: true,
    //         success : function(data) {
    //             tabel_diagnosa.draw();
    //             let countdialog = $(document).find("#confirm-dialog").length;
    //             let i;
    //             if(countdialog > 0){
    //                 for (i = 0; i < countdialog; i++) { 
    //                     $(document).find("#confirm-dialog").remove();
    //                 }
    //             }
    //         }
    //     });
    // });

    $(".save-diagnosa").on("click", function (e) {
        $(this).docoForm("click", {
            success : function(data) {
                $("#tab-diagnosa").trigger("click");
            }
        });
    });
    $(document).on("change", "#kelompokdiagnosa_id", function(){
        var values = $(this).val();
        var textval = $("#kelompokdiagnosa_id option:selected").text();
        $("#selectDiagnosa").val(null).trigger("change");
        if(values == ""){
            $("#selectDiagnosa").attr("disabled", true);
            $("#morb_diagnosanama").val("");
            $("#morb_diagnosakode").val("");
        }else{
            if(values == 6){
                jenisdiagnosa = "diagnosa_terapi";
            }else{
                jenisdiagnosa = "diagnosa_masuk";
            }
            $("#selectDiagnosa").attr("disabled", false);
            $("#kelompokdiagnosa_nama").val(textval);
        }
    })
    $("#reset-diagnosa").on("click", function (e) {
        $("#selectDiagnosa").val(null).trigger("change");
        $("#pasienmorbiditasform-kelompokdiagnosa_id").val(null);
        $.ajax({
            url: baseUrl+"rajal/pemeriksaan/reset-session?pendaftaran_id=' . $pendaftaran_id . '",
            success: function(data){
                tabel_diagnosa.draw();
            }
        });
    });
    $(document).on("change", "#selectDiagnosa", function(){
        var splitter = [];
        if($(this).val() != ""){
            var text = $("#selectDiagnosa option:selected").text();
            splitter = text.split(" - ");
            if(splitter.length > 1){
                $("#morb_diagnosanama").val(typeof splitter[1] != "undefined" ? splitter[1] : "")
                $("#morb_diagnosakode").val(typeof splitter[0] != "undefined" ? splitter[0] : "")
            }else{
                $("#morb_diagnosanama").val(typeof splitter[0] != "undefined" ? splitter[0] : "")
            }
        }else{
            $("#morb_diagnosanama").val("")
            $("#morb_diagnosakode").val("")
        }
    })
');
?>

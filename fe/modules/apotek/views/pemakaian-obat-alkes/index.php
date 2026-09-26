<?php

/**
 * @author Yaya
 * @copyright 3 January 2018 aweutist
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\typeahead\Typeahead;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")), 'url' => ['']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style>
    .obatalkes-out-of-stock{
        background-color: #fdb7b7 !important;
        color: #606060 !important;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'simpan' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Simpan'),
                        'icon' => 'fa fa-save',
                        'attributes' => [
                            'id' => 'simpan-pemakaian-alkes',
                            'data-options'=>'click',
                        ]
                    ],
                    'reset'=>[
                        'attributes'=>[
                            'id' => 'btn-reset-pemakaian',
                            'data-target'=>'ajax-form'
                        ]
                    ],
                    'print'=>[
                        'attributes'=>[
                            'id'=>'btn-cetak',
                            'data-lasturl'=>'/apotek/pemakaian-obat-alkes/cetak?id=',
                            'data-target'=>'/apotek/pemakaian-obat-alkes/cetak?id=',
                            'data-options'=>'click'
                        ]
                    ]
                ]);?>
            </div>
            <div class="panel-body">
                <div class="col-md-5">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b>Data pemakaian obat alkes</b></h6>
                        </div>

                        <div class="panel-body">
                            <?php 
                                $form = ActiveForm::begin([
                                    'id' => 'ajax-form', 
                                    'action' => '/apotek/pemakaian-obat-alkes/set-list-item',
                                    'enableAjaxValidation'=>false, 
                                    'enableClientValidation'=>false,
                                    'type' => ActiveForm::TYPE_HORIZONTAL,
                                    'formConfig' => [
                                        'labelSpan' => 3, 
                                        'deviceSize' => ActiveForm::SIZE_SMALL
                                    ],
                                    'options' => [
                                        'skip-confirm' => "true"
                                    ]
                                ]); 
                            ?>
                            <?= $form->field($model, 'tanggal_pemakaian', [
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-5'
                                    ],
                                'addon' => ['append' => [
                                        'content' => '<i class="fa fa-calendar"></i>']]
                                ])->textInput([
                                        'placeholder' => $model->getAttributeLabel('tanggal_pemakaian'),
                                        'class' => 'form-control input-sm',
                                        'id' => 'tanggal-pemakaian',
                                        'readonly' => true,
                                        'value' => date('d-M-Y H:i:s')
                                ]);
                            ?>
                            <hr>
                            <?= $form->field($model, 'obatalkes_id',[
                                        'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-8'
                                        ]
                                    ])->dropDownList([],[
                                            'class' => 'select2',
                                            'id' => 'obatalkes_id'
                                            ])->label(Yii::t('fe', 'Obat alkes')); 
                            ?>
                            <?= $form->field($model, 'satuan',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ],
                                ])->dropDownList([],[
                                    'class' => 'select2',
                                    'id' => 'list-satuan'
                                ]); ?>

                            <?= $form->field($model, 'stok', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-4'
                                            ]
                                        ])->textInput([
                                            'placeholder' => $model->getAttributeLabel('stok'),
                                            'class' => 'form-control input-sm typeahead',
                                            'autocomplete' => "off",
                                            'id' => 'pemakaian-obat-stok',
                                            'type' => 'number',
                                            'readonly' => true
                                        ]); ?>
                            <?= $form->field($model, 'qty', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-4'
                                            ]
                                        ])->textInput([
                                            'placeholder' => $model->getAttributeLabel('qty'),
                                            'class' => 'form-control input-sm typeahead',
                                            'autocomplete' => "off",
                                            'type' => 'number'
                                        ]); ?>
                            <?= $form->field($model, 'ket_obatpakai',[
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textArea(['row'=>7])->label(Yii::t('fe', 'Keterangan'))?>
                            <?=Html::activeHiddenInput($model, 'satuan_text', ['class'=>'satuan-text'])?>
                        <hr>
                        <div class="btn-group pull-right">
                            <?=Html::resetButton('reset', ['class'=>'hidden'])?>
                            <?= Html::submitButton(
                                '<b><i class="fa fa-plus"></i></b>' . Yii::t('fe','Tambah'), 
                                    [
                                        'class' => 'btn btn-success btn-labeled btn-xs',
                                        'id' => 'simpan-pemakain-obat'
                            ]) ?>
                        </div>
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="legend-index">
                        <div class="legend-header">Keterangan</div>
                        <div class="legend-wrapper">
                            <div class="legend-information">
                                <div class="legend-information__color unavailable-stock"></div>
                                <div class="legend-information__text">Stok tidak tersedia</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b>Tabel pemakaian obat alkes</b></h6>
                        </div>

                        <div class="panel-body">
                            <table id="pemakaian-obat-alkes" 
                            class="table table-striped table-condensed table-hover" style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?=\Yii::t("fe", "Kode obat alkes");?></th>
                                        <th><?=\Yii::t("fe", "Nama obat alkes");?></th>
                                        <th><?=\Yii::t("fe", "Qty");?></th>
                                        <th><?=\Yii::t("fe", "Satuan Besar");?></th>
                                        <th><?=\Yii::t("fe", "Qty");?></th>
                                        <th><?=\Yii::t("fe", "Satuan Kecil");?></th>
                                        <th><?=\Yii::t("fe", "Keterangan");?></th>
                                        <th width="12"><?=\Yii::t("fe", "Aksi");?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="8">
                                            <?=\Yii::t("fe", "Data tidak ditemukan.");?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php

$this->registerJs('

    var table;
    var _detailObat = {
        stok : {},
        satuankecil : {},
        satuan : {},
        currentStok : 0,
        currentSatuan : 0,
        item : {}
    };
    var attributes = {};
    
    $("#simpan-pemakaian-alkes").on("click",function (event) {
        event.preventDefault();
        $(this).docoForm("click",{
            url : "/apotek/pemakaian-obat-alkes/save",
            method : "POST",
            type : "json",
            data : {tanggal_pemakaian : $("#tanggal-pemakaian").val()},
            success : function (data) {
                $("#obatalkes_id").val(\'\').trigger(\'change\');
                $("#list-satuan").val(\'\').trigger(\'change\');
                $("#pemakaian-obat-stok, #pemakaianobatalkesform-qty, #pemakaianobatalkesform-ket_obatpakai").val("");
                $(".data-print").prop("disabled", false).attr("data-target", $(".data-print").attr("data-lasturl") + data.response.id);
                table.draw();
            },
            error: function (data) {
                let response = data.responseJSON;
                if (typeof response?.metadata?.status != "undefined" && response?.metadata?.status == "422") {
                    if (typeof response?.response != "undefined" && typeof response?.response?.data?.obatalkes_ids_not_in_stock != "undefined") {
                        let obatalkes_ids_not_in_stock = response.response.data.obatalkes_ids_not_in_stock;
                        obatalkes_ids_not_in_stock.forEach(function(item, index){
                            let row = $("#pemakaian-obat-alkes").DataTable().row((idx, data) => data.id == item);
                            if (row) {
                                $(row.node()).addClass("obatalkes-out-of-stock");
                            }
                        });
                    }
                }
            }
        });
    });

    $("#ajax-form").submit(function(event){
        event.preventDefault();
        var _value = $(this).serializeArray();
        if (Object.keys(attributes).length) {
            $.each(attributes, function (key, val) {
                _value.push({
                    name : key,
                    value : val
                });
            });
        }
        console.log(_value);
        $(this).docoForm("submit",{
            data : _value,
            success : function (data) {
                $("#obatalkes_id").val(\'\').trigger(\'change\');
                $("#list-satuan").val(\'\').trigger(\'change\');
                $("#pemakaian-obat-stok, #pemakaianobatalkesform-qty, #pemakaianobatalkesform-ket_obatpakai").val("");
                table.draw();
            }
        });
    });

    $(document).on(\'click\',\'.delete\', function(event) {
        event.preventDefault();
        $(this).docoForm(\'delete\',{
            success : function (data) {
                table.draw();
            }
        });
    });
    $("#btn-reset-pemakaian").on("click", function(){
        $(this).docoForm("click",{
            skipConfirm: true,
            skipSuccessNotif: true,
            url : "/apotek/pemakaian-obat-alkes/clear-data",
            method : "POST",
            type : "json",
            success : function (data) {
                $("#obatalkes_id").val(\'\').trigger(\'change\');
                $("#list-satuan").val(\'\').trigger(\'change\');
                $("#pemakaian-obat-stok, #pemakaianobatalkesform-qty, #pemakaianobatalkesform-ket_obatpakai").val("");
                table.draw();
            }
        });
    })
    $("#btn-cetak").on("click", function(){
        window.open($("#btn-cetak").attr("data-target"));
    })
    $(document).ready(function() {
        $("#btn-cetak").prop("disabled", true)
        $("#obatalkes_id").select2({
            placeholder: "'. \Yii::t("fe", "Pilih") .'",
            minimumInputLength: 3, 
            ajax : {
                url: baseUrl+"apotek/pemakaian-obat-alkes/search-obat-alkes",
                dataType: \'json\',
                quietMillis: 250,
                data: function (params) {
                  var query = {
                    search: params,
                  }
                  // Query parameters will be ?search=[term]&type=public
                  return params;
                },
                processResults: function (data) {
                    $.each(data.result, function (key,val) {
                        _detailObat.item[val.id] = val;
                        _detailObat.satuan[val.id] = {};
                        _detailObat.stok[val.id] = val.stok;
                        _detailObat.satuankecil[val.id] = val.satuankecil_id;
                        $.each(val.satuan, function (id, item) {
                            _detailObat.satuan[val.id][id] = item;
                        });
                    });
                  return {
                    results: data.result
                  };
                },
                dropdownCssClass: \'bigdrop\',
                escapeMarkup: function (m) { return m; },
            },
            cache: true
        }).on("change", function (e) {
            var value = $(this).val();
            var list_html = "";
            list_html += " <option value=\"\"></option>";
            data = [];
            if (typeof _detailObat.satuan[value] !== "undefined") {
                data = _detailObat.satuan[value];
            }

            if (typeof _detailObat.stok[value] !== "undefined") {
                $("#pemakaian-obat-stok").val(_detailObat.stok[value]);
                _detailObat.currentStok = _detailObat.stok[value];
            }

            var defaultValue = null;

            if (typeof _detailObat.satuankecil[value] !== "undefined") {
                _detailObat.currentSatuan = _detailObat.satuankecil[value];
                defaultValue = _detailObat.satuankecil[value];
            }

            if (typeof _detailObat.item[value] !== "undefined") {
                attributes = _detailObat.item[value];
            }

            $.each(data, function(i, item) {
                if (defaultValue == item.satuanbesar_id) {
                    list_html += "<option data-nilai=\'"+ item.nilai_konversi +"\' value=\'"+ item.satuanbesar_id +"\' selected>"+item.satuan_besar+"</option>";
                } else {
                    list_html += "<option data-nilai=\'"+ item.nilai_konversi +"\' value=\'"+ item.satuanbesar_id +"\' >"+item.satuan_besar+"</option>";
                }
            });

            $("#list-satuan").html(list_html);
            var count = Object.keys(data).length;
            if (count > 1) {
                $("#list-satuan").select2({placeholder: "'.Yii::t('fe','--Pilih--') .'"});
            } else {
            }

            $("#list-satuan").select2({placeholder: "'.Yii::t('fe','--Pilih--') .'"}).trigger("change");
        });

        $("#list-satuan").on("change", function (event) {
            event.preventDefault();
            var oaId = $("#obatalkes_id").val();
            var _value = $(this).val();
            var _current = _detailObat.currentSatuan;
            var _stok = _detailObat.currentStok;
            var hasil = _stok;
            var _konv = $(this).find("option:selected").attr("data-nilai");
            var satuanbesar_id = $(this).find("option:selected").val();
            hasil = _konv != 0 ? _stok/_konv : 0;
            attributes.stok = hasil;
            attributes.nilai_konversi = parseInt(_konv);
            attributes.satuanbesar_id = satuanbesar_id;

            var _fixed = hasil%1 == 0 ? 0 : 2;
            $("#pemakaian-obat-stok").val(hasil.toFixed(_fixed));
            $(".satuan-text").val( $(this).find("option:selected").text() )
            console.log(attributes);
        });

        $(".pickadate").pickadate({
            format: "dd-mm-yyyy",
            formatSubmit: "yyyy-mm-dd",
            onStart: function() {
                var date = new Date();
                this.set("select", [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
            }
        });

        table = $("#pemakaian-obat-alkes").docoTabel({
            filter: false,
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: true,
            ajax: baseUrl+"apotek/pemakaian-obat-alkes/get-list-item",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Kode obat alkes")).'", 
                    data: "kode_obat",
                    orderable: false
                },                
                {
                    title: "'.(\Yii::t("fe", "Nama obat alkes")).'", 
                    data: "nama_obat",
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Qty")).'", 
                    data: "qty_besar",
                    name: "qty_besar",
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Satuan Besar")).'",
                    data: "satuan_besar",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                },
               {
                    title: "'.(\Yii::t("fe", "Qty")).'", 
                    data: "qty_kecil",
                    name: "qty_kecil",
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Satuan Kecil")).'",
                    data: "satuan_kecil",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                },
                {
                    title: "'.(\Yii::t("fe", "Keterangan")).'",
                    data: "ket_obatpakai",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                },
                {
                    title: "'.(\Yii::t("fe", "Aksi")).'",
                    data: "aksi",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                },
                {
                    title: "Obat Alkes Id",
                    data: "id",
                    searchable: false,
                    orderable: false,
                    visible: false
                },
            ],
        });
    });

',View::POS_END,'pemakaian-obat-alkes');
<?php

/**
 * @author Yaya
 * @copyright 23 March 2018
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
$this->params['breadcrumbs'][] = ['label' => 'Gudang', 'url' => []];
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', $title)];

?>

<style type="text/css">
    .table-condensed > tbody > tr > td {
        padding: 8px!important;
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?= Html::button('<b><i class="fa fa-floppy-o"></i></b>'.Yii::t('fe', ' Simpan'),
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'simpan-pemakaian-alkes'
                    ]);
                ?>
                <?= Html::button('<b><i class="fa fa fa-refresh"></i></b>'.Yii::t('fe', ' Ulang'),
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                    ]);
                ?>

            </div>
            <div class="panel-body">
                <div class="col-md-5">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b>Data pemakaian barang</b></h6>
                        </div>

                        <div class="panel-body">
                            <?php
                                $form = ActiveForm::begin([
                                    'id' => 'ajax-form',
                                    'action' => '/gudang/pemakaian-barang/set-list-item',
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
                                        ]); ?>
                            <hr>
                            <?= $form->field($model, 'barang_id',[
                                        'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-8'
                                        ]
                                    ])->dropDownList([],[
                                            'class' => 'select2',
                                            'id' => 'barang_id'
                                            ]);
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
                                            'id' => 'pemakaian-barang-stok',
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
                            <?= $form->field($model, 'keterangan', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textarea([
                                            'placeholder' => $model->getAttributeLabel('keterangan'),
                                            'class' => 'form-control input-xl',
                                            'autocomplete' => "off",
                                            'cols' => '20'
                                        ]); ?>
                        <hr>
                        <div class="btn-group pull-right">
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
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b>Tabel Pemakaian Barang</b></h6>
                        </div>

                        <div class="panel-body">
                            <table id="pemakaian-obat-alkes"
                            class="table table-striped table-condensed table-hover" style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?=\Yii::t("fe", "Nama barang");?></th>
                                        <th><?=\Yii::t("fe", "Qty Input");?></th>
                                        <th><?=\Yii::t("fe", "Satuan Input");?></th>
                                        <th><?=\Yii::t("fe", "Qty Konversi");?></th>
                                        <th><?=\Yii::t("fe", "Satuan Kecil");?></th>
                                        <th><?=\Yii::t("fe", "Keterangan");?></th>
                                        <th width="12"><?=\Yii::t("fe", "Aksi");?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="9">
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

<?= Html::button('<b><i class="fa fa fa-refresh"></i></b>'.Yii::t('fe', ' cetak'),
    [
        'class' => 'btn btn-info btn-labeled btn-xs',
        'action' => '/gudang/pemakaian-barang/before-print?id_pemakaian=',
        'id' => 'show-cetak',
        'data-width' => '800px',
        'data-target' => '#modal_backdrop',
        'data-toggle' => 'modal',
        'style' => 'display:none',
    ]);
?>

<?php

$this->registerJs('

    var table;
    var _detailObat = {
        stok : {},
        satuankecil : {},
        satuan : {},
        currentStok : 0,
        currentSatuan : 0,
        item : {},
        cacheSatuan : ' . $cache . '
    };
    var attributes = {};

    $("#simpan-pemakaian-alkes").on("click",function (event) {
        event.preventDefault();
        $(this).docoForm("click",{
            url : "/gudang/pemakaian-barang/save",
            method : "POST",
            type : "json",
            data : {tanggal_pemakaian : $("#tanggal-pemakaian").val()},
            success : function (data) {
                var _modal = $("#show-cetak");
                var _action = _modal.attr("action");
                _modal.attr("action",_action  + data.response.id_parent);
                _modal.trigger("click");
                _modal.attr("action",_action);
                $("#barang_id").val(\'\').trigger(\'change\');
                $("#list-satuan").val(\'\').trigger(\'change\');
                $("#pemakaian-barang-stok, #pemakaianbarangform-qty").val("");
                $("#pemakaianbarangform-keterangan").val("");
                table.draw();
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
        $(this).docoForm("submit",{
            data : _value,
            success : function (data) {
                $("#barang_id").val(\'\').trigger(\'change\');
                $("#list-satuan").val(\'\').trigger(\'change\');
                $("#pemakaian-barang-stok, #pemakaianbarangform-qty").val("");
                $("#pemakaianbarangform-keterangan").val("");
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

    $(document).ready(function() {
        $("#barang_id").select2({
            placeholder: "'. \Yii::t("fe", "Pilih") .'",
            minimumInputLength: 3,
            ajax : {
                url: baseUrl+"gudang/pemakaian-barang/search-barang",
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
                $("#pemakaian-barang-stok").val(_detailObat.stok[value]);
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
                if (defaultValue == i) {
                    list_html += "<option value=\'"+ i +"\' selected>"+item+"</option>";
                } else {
                    list_html += "<option value=\'"+ i +"\'>"+item+"</option>";
                }
            });

            $("#list-satuan").html(list_html);
            var count = Object.keys(data).length;
            // if (count > 1) {
            //     $("#list-satuan").removeAttr("disabled");
            //     $("#list-satuan").select2({placeholder: "'.Yii::t('fe','--Pilih--') .'"});
            // } else {
            //     $("#list-satuan").select2("enable", false);
            // }

            $("#list-satuan").select2({placeholder: "'.Yii::t('fe','--Pilih--') .'"});
        });

        $("#list-satuan").on("change", function (event) {
            event.preventDefault();
            var _value = $(this).val();
            var _current = _detailObat.currentSatuan;
            var _stok = _detailObat.currentStok;
            var hasil = _stok;
            if (typeof _detailObat.cacheSatuan[_value] !== "undefined") {
                var _object = _detailObat.cacheSatuan[_value];
                if (typeof _object[_current] !== "undefined") {
                    var _konv = _object[_current];
                    hasil = _konv != 0 ? _stok/_konv : 0;
                }
            }
            var _fixed = hasil%1 == 0 ? 0 : 2;
            $("#pemakaian-barang-stok").val(_fixed);
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
            displayLength: 20,
            lengthChange : false,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"gudang/pemakaian-barang/get-list-item",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                },
                {
                    title: "'.(\Yii::t("fe", "Nama barang")).'",
                    data: "nama_barang",
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Qty Input")).'",
                    data: "qty_besar",
                    name: "qty_besar",
                    orderable: false,
                    class: "text-right"
                },
                {
                    title: "'.(\Yii::t("fe", "Satuan Input")).'",
                    data: "satuan_besar",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                },
               {
                    title: "'.(\Yii::t("fe", "Qty Konversi")).'",
                    data: "qty_kecil",
                    name: "qty_kecil",
                    orderable: false,
                    class: "text-right"
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
                    data: "keterangan",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Aksi")).'",
                    data: "aksi",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                }
            ],
        });
    });

',View::POS_END,'pemakaian-barang');
<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
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

?>
<style>
    .datepicker>div{
        display:block;
    }
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<div class="modal-body">
    <?php $form = ActiveForm::begin([
            'id' => 'form',
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
        <div class="col-sm-12">
            <?= $form->field($model, 'obatalkes', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-8'
                    ]
                    ])->dropDownList([], [
                        'class' => 'select2',
                        'id' => 'obatalkes',
                        'tab-index' => 2
                    ]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'satuan', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-4'
                    ],
                ])->dropDownList([], [
                    'class' => 'select2',
                    'id' => 'list-satuan',
                    'tab-index' => 3,
                    'disabled' => true
                ]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'stok', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-4'
                    ]
                ])->textInput([
                    'placeholder' => $model->getAttributeLabel('stok'),
                    'class' => 'form-control input-sm',
                    'autocomplete' => "off",
                    'id' => 'pemesanan-obat-stok',
                    'type' => 'number',
                    'readonly' => true,
                ]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'qty', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-4'
                    ]
                ])->textInput([
                    'placeholder' => $model->getAttributeLabel('qty'),
                    'class' => 'form-control input-sm doco-number',
                    'autocomplete' => "off",
                    'id' => 'pemesanan-obat-qty',
                    'type' => 'text',
                    'tab-index' => 4
                ]);
            ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    <hr>
    <div class="modal-footer">
        <?= Html::button("<b><i class='fa fa-floppy-o'></i></b>&nbsp;Simpan", [
            'class' => 'btn btn-info btn-labeled btn-xs',
            'id' => 'btn-submit-obat',
            'data-dismiss' => 'modal'
        ]) ?>
        <?= Html::button("<b><i class='fa fa-arrow-left'></i></b>&nbsp;Kembali",[
            'class' => 'btn btn-info btn-labeled btn-xs',
            'data-dismiss' => 'modal'
        ]); ?>
    </div>

</div>

<?php
$this->registerJs('
    // Event Ready
    $(document).ready(function() {
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

        $("#btn-submit-obat").on("click", function(){
            var tableData = tableEditPemesanan.data().toArray();
            var obatalkes_id = parseInt($("#obatalkes").val() != null ? $("#obatalkes").val() : 0);
            var qty = $("#pemesanan-obat-qty").val();
            var stok = parseInt($("#pemesanan-obat-stok").val());
            rowNum = tableData.length + 1;

            if(obatalkes_id == "") {
                docoNotification("warning", i18next.t("Perhatian"), i18next.t("Nama Obat Alkes harus diisi"));
                return false;
            }

            if(qty == "" || qty <= 0) {
                docoNotification("warning", i18next.t("Perhatian"), i18next.t("Qty Pesan harus diisi lebih dari 0"));
                return false;
            }

            if(qty > stok) {
                docoNotification("warning", i18next.t("Perhatian"), i18next.t("Stok tidak mencukupi"));
                return false;
            }

            if (tableData.some(el => el.obatalkes_id == obatalkes_id)) {
                docoNotification("warning", i18next.t("Perhatian"), i18next.t("Obat sudah ada"));
                return false;
            } else {
                var obatalkes_nama = $("#obatalkes").children("option:selected").text();
                var satuan = $("#list-satuan").children("option:selected").text();
                var satuan_id = parseInt($("#list-satuan").val());
                var index_selected_satuan = $("#list-satuan").prop("selectedIndex")-1;

                var identifier = obatalkes_id +"-"+_detailObat.satuankecil[obatalkes_id];
                var obatBaru = {
                    "identifier": identifier,
                    "to_delete": false,
                    "rowNum": rowNum,
                    "pesanobatdetail_id": "",
                    "obatalkes_id": obatalkes_id,
                    "obatalkes_nama": obatalkes_nama,
                    "satuankecil_id": _detailObat.satuankecil[obatalkes_id],
                    "satuanbesar_id": satuan_id,
                    "qty_besar": qty,
                    "qty_form": "<div class=\"col-md-6\"><input type=\"text\" name=\"QtyPesan["+obatalkes_id+"]\" value=\""+qty+"\" class=\"form-control qty-form text-right doco-number\" data-id=\"\"/></div><div class=\"col-md-6\"><p class=\"form-control-static\">"+satuan+"</p></div>",
                    "stok": stok,
                    "action_column": `<button class="btn btn-xs btn-danger btn-delete-row" data-key="` + identifier +`" ><i class="fa fa-trash"></i></button>`
                };

                if(obatBaru.satuanbesar_id != obatBaru.satuankecil_id) {
                    // menggunakan satuan besar
                    obatBaru.nilai_konversi = _detailObat.satuan[obatalkes_id][index_selected_satuan].nilai_konversi;
                    obatBaru.qty_kecil = qty*obatBaru.nilai_konversi;
                    obatBaru.qty_konversi = "<span class=\"qty-konversi-"+obatalkes_id+"\">"+qty*obatBaru.nilai_konversi+"</span> "+ _detailObat.item[obatalkes_id].satuankecil_nama;
                } else {
                    // menggunakan satuan terkecil
                    obatBaru.nilai_konversi = 1;
                    obatBaru.qty_kecil = qty;
                    obatBaru.qty_konversi = "<span class=\"qty-konversi-"+obatalkes_id+"\">"+qty+"</span> "+ _detailObat.item[obatalkes_id].satuankecil_nama;
                }
                tableEditPemesanan.row.add(obatBaru).draw();
                listObat.push(obatBaru);
                onChangeQtyPemesanan();
                resetFormTambah();
            }
        });

        function resetFormTambah() {
            $("#obatalkes").val(null).trigger("change");
            $("#list-satuan").val(null).trigger("change");
            $("#pemesanan-obat-stok").val(null);
            $("#pemesanan-obat-qty").val(null);
        }

        $("#pemesanan-obat-qty").on("input", function() {
            match        = (/(\d{0,9})[^.]*((?:\.\d{0,2})?)/g).exec(this.value.replace(/[^\d.]/g, ""));
            this.value   = match[1] + match[2];
        });

        $("#obatalkes").select2({
            language: {
                errorLoading: function () { return "Searching..." }
            },
            placeholder: "-- Pilih --",
            minimumInputLength: 3,
            ajax: {
                url: baseUrl + "apotek/informasi-obat-alkes-keluar/search-obat-alkes?ruangan_id=" + ruangan_id,
                dataType: "json",
                quietMillis: 250,
                data: function (params) {
                    var query = {
                        search: params,
                    }
                    return params;
                },
                processResults: function (data) {
                    $.each(data.result, function (key, val) {
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
                dropdownCssClass: "bigdrop",
                escapeMarkup: function(m) { return m; },
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
                $("#pemesanan-obat-stok").val(_detailObat.stok[value]);
                _detailObat.currentStok = _detailObat.stok[value];
                current_limit = _detailObat.stok[value];
            }

            var defaultValue = null;

            if (typeof _detailObat.satuankecil[value] !== "undefined") {
                _detailObat.currentSatuan = _detailObat.satuankecil[value];
                defaultValue = _detailObat.satuankecil[value];
            }

            if (typeof _detailObat.item[value] !== "undefined") {
                attributes = _detailObat.item[value];
            }

            $.each(data, function (i, item) {
                if (defaultValue == item.satuanbesar_id) {
                    list_html += "<option data-nilai=\'"+ item.nilai_konversi +"\' value=\'" + item.satuanbesar_id + "\' selected>" + item.satuan_besar + "</option>";
                } else {
                    list_html += "<option data-nilai=\'"+ item.nilai_konversi +"\' value=\'" + item.satuanbesar_id + "\'>" + item.satuan_besar + "</option>";
                }
            });

            $("#list-satuan").html(list_html);
            var count = Object.keys(data).length;
            if (count > 0) {
                $("#list-satuan").removeAttr("disabled");
                $("#list-satuan").select2({ placeholder: "--Pilih--" });
            } else {
                $("#list-satuan").select2("enable", false);
            }

            $("#list-satuan").select2({ placeholder: "--Pilih--" });
            $("#pemesanan-obat-qty").val(null);
        });

        $("#list-satuan").on("change", function(event){
            event.preventDefault();
            var oaId = $("#obatalkes_id").val();
            var _value = $(this).val();
            var _current = _detailObat.currentSatuan;
            var _stok = _detailObat.currentStok;
            var hasil = _stok;
            var _konv = $(this).find("option:selected").attr("data-nilai");
            hasil = _konv != 0 ? Math.floor(_stok/_konv) : 0;
            var _fixed = hasil%1 == 0 ? 0 : 2;
            $("#pemesanan-obat-stok").val(hasil.toFixed(_fixed));
            current_limit = hasil;
            $(".satuan-text").val( $(this).find("option:selected").text() )
            $("#pemesanan-obat-qty").val(0);
        });
    });

',View::POS_END,'b-index');
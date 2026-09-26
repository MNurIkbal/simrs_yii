<?php

use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;

$this->title = $title;

$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"),
'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<style type="text/css">
.table-condensed > tbody > tr > td,
.table-condensed > thead > tr > th {
    padding: 8px 12px;
}

</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><strong><?= $this->title; ?></strong></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'back',
                    'save' => [
                        'type'=>'button',
                        'title' => Yii::t('fe', 'Simpan'),
                        'icon' => 'fa fa-save',
                        'attributes' => [
                            'data-options' => "click",
                            'id' => "btn-retur",
                        ]
                    ],
                    // 'reset'=> [
                    //     'title' => "Ulang",
                    //     'attributes'=>[
                    //         'data-parent'=>'.filter-form',
                    //         "id" => "btn-reset"
                    //     ]
                    // ],
                ]);?>
            </div>

            <div class="panel-body">
                <?php
                    $form = ActiveForm::begin([
                        'id' => 'retur-form',
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        // 'type' => ActiveForm::TYPE_HORIZONTAL,
                        'action' => "/gudang/informasi-penerimaan-obat/retur?id={$id}",
                        'options' => [
                            'skip-confirm' => "true"
                        ]
                    ]);
                ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title">Informasi Data Retur</h6>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <br>
                                    <div class="col-md-3">
                                        <p><strong>Nama Supplier</strong></p>
                                        <p><?= $header["supplier_nama"] ?></p>
                                    </div>
                                    <div class="col-md-3">
                                        <p><strong>Tarif Pajak</strong></p>
                                        <p><?= $header["pajak_persen"] ?> %</p>
                                    </div>
                                    <div class="col-md-3">
                                        <p><strong>Tanggal Retur</strong></p>
                                        <p><?= date("d-M-Y") ?></p>
                                        <?= $form
                                        ->field($form_model, 'tgl_retur')->hiddenInput([
                                            "value" => date("d-M-Y")
                                        ])->label(false); ?>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="form-group highlight-addon field-returformobat-pegawairetur_id">
                                                <label class="control-label" for="returformobat-pegawairetur_id">
                                                    <strong>Pegawai Retur</strong>
                                                </label>
                                                <input type="text" id="returformobat-pegawairetur_id" class="form-control" value="<?= Yii::$app->docoVars->user('nama_pegawai') ?>" readonly="">
                                            </div>
                                            <?= $form
                                            ->field($form_model, 'pegawairetur_id')->hiddenInput([
                                                "value" => Yii::$app->docoVars->user('id_pegawai'),
                                                "readonly" => true
                                            ])->label(false); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <?= $form->field($form_model, 'alasan_retur', [
                                                    'labelOptions' => [
                                                        'style'=>'font-weight:bold'
                                                    ]
                                                ])->textarea([
                                                    'class' => 'form-control',
                                                    'tabindex' => 1,
                                                    'style' => 'resize: none'
                                                ]); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title">Detail Barang</h6>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-3" style="margin: 15px 0">
                                   <button class="btn btn-success" id="btn-tambah-obat"><i class="fa fa-search"></i> Tambah Obat</button>
                                </div>
                            </div>

                            <div style="width: auto; overflow: auto;">
                                <table
                                    class="table table-striped table-condensed table-hover dataTable"
                                    id="table-retur">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th><?= \Yii::t("fe", "No. PO") ?></th>
                                            <th><?= \Yii::t("fe", "No. Penerimaan") ?></th>
                                            <th><?= \Yii::t("fe", "No. Faktur") ?></th>
                                            <th><?= \Yii::t("fe", "Nama Obat") ?></th>
                                            <th><?= \Yii::t("fe", "Harga Satuan (Rp.)") ?></th>
                                            <th><?= \Yii::t("fe", "Qty Diterima") ?></th>
                                            <th><?= \Yii::t("fe", "On Retur") ?></th>
                                            <th><?= \Yii::t("fe", "Tanggal Kadaluarsa") ?></th>
                                            <th><?= \Yii::t("fe", "No. Batch") ?></th>
                                            <th><?= \Yii::t("fe", "Sub Total (Rp.)") ?></th>
                                            <th><?= \Yii::t("fe", "Qty Retur") ?> <sup style="color: red">*</sup></th>
                                            <th><?= \Yii::t("fe", "Total Retur (Rp.)") ?></th>
                                            <th><?= \Yii::t("fe", "Aksi") ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($model->list_data)): ?>
                                            <?php
                                            foreach ($model->list_data as $index => $detail):
                                                $no_po = $detail["nomor_po"];
                                                $no_penerimaan = $detail["no_penerimaan"];
                                                $penerimaanobatdetail_id = $detail["penerimaanobatdetail_id"];
                                                
                                                $harga = ArrayHelper::getValue($detail, 'harga', 0);
                                                $qty_diterima = ArrayHelper::getValue($detail, 'qty_diterima', 0);
                                                $on_retur = ArrayHelper::getValue($detail, 'on_retur', 0);
                                                $nilai_konversi = ArrayHelper::getValue($detail, 'nilai_konversi', 1);
                                                $disabled_input = false;

                                                $subtotal = $harga * ($qty_diterima - ($on_retur / $nilai_konversi));

                                                if($qty_diterima == $on_retur) {
                                                    $disabled_input = true;
                                                }

                                                $_detailretur[$penerimaanobatdetail_id] = [
                                                    "no_po" => $no_po,
                                                    "no_penerimaan" => $no_penerimaan,
                                                    "obatalkes_id" => ArrayHelper::getValue($detail, 'obatalkes_id', null),
                                                    "no_faktur" => ArrayHelper::getValue($detail, 'no_faktur', '-'),
                                                    "obatalkes_nama" => ArrayHelper::getValue($detail, 'obatalkes_nama', '-'),
                                                    "harga" => $harga,
                                                    "satuan_besar" => ArrayHelper::getValue($detail, 'satuan_besar', '-'),
                                                    "satuanbesar_id" => ArrayHelper::getValue($detail, 'satuanbesar_id', '-'),
                                                    "tgl_kadaluarsa" => isset($detail["tgl_kadaluarsa"]) ? date("d-M-Y", strtotime($detail["tgl_kadaluarsa"])) : "" ,
                                                    "no_batch" => ArrayHelper::getValue($detail, 'no_batch', '-'),
                                                    "subtotal" => $subtotal,
                                                    "qty_diterima" => $qty_diterima,
                                                    "penerimaanobat_id" => ArrayHelper::getValue($detail, 'penerimaanobat_id', '-'),
                                                    "penerimaanobatdetail_id" => $penerimaanobatdetail_id ,
                                                    "qty_yg_diterima" => $qty_diterima . " " . ArrayHelper::getValue($detail, 'satuan_besar', '-'),
                                                    "nilai_konversi" => $nilai_konversi,
                                                    "on_retur" => $on_retur,
                                                    "qty_input" => ArrayHelper::getValue($detail, 'qty_input', 0),
                                                    "qty_retur" => 0,
                                                ];
                                            ?>
                                                <tr
                                                    data-nopo = "<?= $no_po ?>"
                                                    data-nopenerimaan = "<?= $no_penerimaan ?>"
                                                    data-key = "<?= $penerimaanobatdetail_id ?>"
                                                >
                                                    <td><?= $detail["nomor_po"] ?></td>
                                                    <td><?= $detail["no_penerimaan"] ?></td>
                                                    <td><?= $detail["no_faktur"] ?></td>
                                                    <td><?= $detail["obatalkes_nama"] ?></td>
                                                    <td width="8%" class="text-right"><?= DocoHelpers::formatNumber($detail["harga"]) ?></td>
                                                    <td><?= $detail["qty_diterima"] ?> <?= $detail["satuan_besar"] ?></td>
                                                    <td width="5%"><?= isset($detail["on_retur"]) ? ($detail["on_retur"]/$detail["nilai_konversi"]) : 0 ?> <?= $detail["satuan_besar"] ?></td>
                                                    <td><?= isset($detail["tgl_kadaluarsa"]) ? date("d-M-Y", strtotime($detail["tgl_kadaluarsa"])) : "" ?></td>
                                                    <td><?= $detail["no_batch"] ?></td>
                                                    <td width="7%" class="text-right"><?= DocoHelpers::formatNumber($subtotal) ?></td>
                                                    <td width="7%">
                                                        <?=
                                                            Html::textInput("ReturFormObat[qty_retur_$penerimaanobatdetail_id]",0,[
                                                                'class' => 'form-control doco-number text-right qty-retur',
                                                                'maxlength' => 6,
                                                                'tabindex' => $index + 2,
                                                                'disabled' => $disabled_input
                                                            ])
                                                        ?>
                                                        <div id="error_ReturFormObatqty_retur<?= $penerimaanobatdetail_id ?>" class="error-parent"></div>
                                                    </td>
                                                    <!-- <td><input type="number" class="form-control qty-retur doco-number"></td> -->
                                                    <td width="7%" class="text-right" id="subtotal_retur_<?= $penerimaanobatdetail_id ?>">
                                                        0
                                                    </td>
                                                    <td width="1%">
                                                        <button class="btn btn-danger delretur"><i class="fa fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                                <!-- <tr>
                                                    <td colspan="11" class="text-right"><strong>Total Retur (Rp.)</strong></td>
                                                    <td id="total_retur">0</td>
                                                    <td></td>
                                                </tr> -->
                                        <?php
                                        endforeach ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="10" class="text-center"><?= \Yii::t("fe", "Data tidak tersedia") ?></td>
                                            </tr>
                                        <?php endif ?>
                                    </tbody>
                                </table>

                                <div style="margin:2% 0%">
                                    <div class="col-md-10 text-right" style="">
                                        <h6><strong>Total Retur (Rp.)</strong></h6>
                                    </div>
                                    <div class="col-md-2 text-right" style="padding-right: 6%">
                                        <h6 id="total_retur">0</h6>
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

<div class="modal fade" id="modalRetur">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Detail Retur</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table">
                            <tr>
                                <td>Nama Supplier</td>
                                <td>:</td>
                                <td><?= $header["supplier_nama"] ?></td>
                            </tr>
                            <tr>
                                <td>Tarif Pajak</td>
                                <td>:</td>
                                <td><?= $header["pajak_persen"] ?> %</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                    <div class="col-md-6">
                        <button type="button" class="btn btn-info btn-labeled btn-xs data-filter" data-parent><i class="fa fa-search"></i> Cari</button>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-borderded table-hover table-condensed" id="tblTambahObat">
                            <thead>
                                <tr class="bg-inverse">
                                    <th></th>
                                    <th><?= \Yii::t("fe", "No. Transaksi Penerimaan") ?></th>
                                    <th><?= \Yii::t("fe", "No. Faktur") ?></th>
                                    <th><?= \Yii::t("fe", "Tanggal Penerimaan") ?></th>
                                    <th><?= \Yii::t("fe", "No. PO") ?></th>
                                    <th><?= \Yii::t("fe", "Nama Obat") ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada obat yang dipilih</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="btnSimpan">Simpan</button>
            </div>
        </div>
    </div>
</div>

<?php

$_detailretur = json_encode($_detailretur);

$this->registerJs('
    var detail_retur_cache = '.$_detailretur.';
    var _supplier_id = "'.$supplier_id.'";
    var _pajak_id = "'.$pajak_id. '";

    var _temp_detail_retur = {};

    var _table;

    function drawTblTambahObat(){
        _table = $("#tblTambahObat").docoTabel({
            filter: true,
            columnDefs: [
                {
                    orderable: false,
                    className: "select-checkbox",
                    targets:   0
                },
                {
                    searchable: false,
                    targets:   4
                }
            ],
            select: {
                style:    "multiple",
                selector: "tr"
            },
            displayLength: 10,
            sorting: [[1, "desc"]],
            processing: true,
            serverSide: true,
            ajax : baseUrl+"gudang/informasi-penerimaan-obat/get-obat-supplier?supplier_id="+_supplier_id+"&pajak_id="+_pajak_id,
            columns : [
                {
                    title: "",
                    data: null,
                    defaultContent: "",
                    searchable: false,
                    orderable: false,
                    width: "10%"
                },
                {
                    "title" : "No. PO",
                    "data" : "nomor_po"
                },
                {
                    "title" : "No. Transaksi Penerimaan",
                    "data" : "no_penerimaan"
                },
                {
                    "title" : "No. Faktur",
                    "data" : "no_faktur"
                },
                {
                    "title" : "Tanggal Penerimaan",
                    "data" : "tgl_penerimaan"
                },
                {
                    "title" : "Nama Obat",
                    "data" : "obatalkes_nama"
                },
            ],
            fnRowCallback : function (nRow, aData, iDisplayIndex, iDisplayIndexFull)
            {
                var _key = aData.penerimaanobatdetail_id;
                var _on_cache = detail_retur_cache[_key];
                var _on_temp = _temp_detail_retur[_key];
                var _tr = $(nRow);

                if(typeof _on_cache != "undefined")
                {
                        _tr.css("background-color", "rgb(250, 235, 220)");
                        _tr.find("td.select-checkbox").removeClass("select-checkbox");
                }
            },
            drawCallback : function (settings) {
                var api = this.api();
                var dataRows = api.rows( {page:"current"} ).data();
                $.each(dataRows, function (key, val) {
                    var _key = val.penerimaanobatdetail_id;
                    var _on_temp = _temp_detail_retur[_key];
                    if (typeof _on_temp != "undefined") {
                        _table.row(":eq("+key+")").select();
                    }
                })
            }
        });

        $(".dataTables_filter").hide();

        $(".filter-form").datatableBootstrapFilter(_table);
    }

    function drawMainTable()
    {
        var _main_table = $("#table-retur");
        var _tbody = _main_table.find("tbody");

        _tbody.find("tr").remove();

        var _all_tr = "";
        $.each(detail_retur_cache, function(i,el){
            var new_tr = $("<tr></tr>");
            new_tr.attr("data-nopo", el.no_po);
            new_tr.attr("data-nopenerimaan", el.no_penerimaan);
            new_tr.attr("data-key", el.penerimaanobatdetail_id);

            var input_val = el.qty_retur;
            var new_input = $("<input>");
            new_input.attr("type", "text");
            new_input.attr("class", "form-control doco-number text-right qty-retur");
            new_input.attr("name", "qty_retur");
            new_input.attr("maxlength", 10);
            new_input.attr("value", input_val);
            new_input.attr("tabindex", parseInt(i) + 2);

            var new_err_div = $("<div></div>");
            new_err_div.attr("id", "error_ReturFormObatqty_retur"+el.penerimaanobatdetail_id);
            new_err_div.attr("class", "error-parent");

            var new_btn = $("<button>").append($("<i></i>").attr("class", "fa fa-trash"));
            new_btn.attr("class", "btn btn-danger delretur");

            var new_onretur = (el.on_retur/el.nilai_konversi);
            new_onretur = new_onretur + " " + el.satuan_besar;
            var subtotal = el.harga * (el.qty_diterima - el.on_retur);
            var total_retur = el.harga * input_val;
            
            if(el.qty_yg_diterima == new_onretur) {
                new_input.attr("disabled", true);
            }

            new_tr.append($("<td></td>").text(el.no_po));
            new_tr.append($("<td></td>").text(el.no_penerimaan));
            new_tr.append($("<td></td>").text(el.no_faktur));
            new_tr.append($("<td></td>").text(el.obatalkes_nama));
            new_tr.append($("<td class=\"text-right\"></td>").text(docoHelper.convertToRupiah(el.harga)));
            new_tr.append($("<td></td>").text(el.qty_yg_diterima));
            new_tr.append($("<td></td>").text(new_onretur));
            new_tr.append($("<td></td>").text(el.tgl_kadaluarsa));
            new_tr.append($("<td></td>").text(el.no_batch));
            new_tr.append($("<td class=\"text-right\"></td>").text(docoHelper.convertToRupiah(subtotal)));
            new_tr.append($("<td></td>").append(new_input).append(new_err_div));
            new_tr.append($("<td class=\"text-right\" id=\"subtotal_retur_"+el.penerimaanobatdetail_id+"\"></td>").append(docoHelper.convertToRupiah(total_retur)));
            new_tr.append($("<td></td>").append(new_btn));

            _tbody.append(new_tr);
        });
    }

    $(document).ready(function(){
        $("#btn-reset").click(function(e){
            $("input").val("");
            $("textarea").val("");

            $.each(detail_retur_cache, function(i, el){
                el.qty_retur = 0;
            });
        });
    });

    $(document).on("click", "#btn-tambah-obat", function(e){
        e.preventDefault();
        $("#modalRetur").modal();
    });

    $(document).on("click", ".delretur", function(e){
        e.preventDefault();
        var _row = $(this).closest("tr");
        var _key = _row.attr("data-key");

        if(typeof detail_retur_cache[_key] != "undefined")
        {
            delete detail_retur_cache[_key];
            _row.remove();
        }
    });

    $(document).on("input", ".qty-retur", function(e){
        e.preventDefault();
        var _row = $(this).closest("tr");
        var _key = _row.attr("data-key");
        var _val = parseInt($(this).val());
        var subtotal_retur_text = $("#subtotal_retur_"+_key);
        var total_retur_text = $("#total_retur");
        var subtotal_retur = 0;
        var total_retur = 0;

        if(typeof detail_retur_cache[_key] != "undefined")
        {
            var max_qty = parseInt(detail_retur_cache[_key].qty_diterima -
                (detail_retur_cache[_key].on_retur / detail_retur_cache[_key].nilai_konversi)
            );
            
            if(_val >= max_qty)
            {
                $(this).val(parseInt(max_qty));
                _val = max_qty;
                detail_retur_cache[_key].qty_retur = max_qty * detail_retur_cache[_key].nilai_konversi;
                detail_retur_cache[_key].qty_input = max_qty;
            } else {
                detail_retur_cache[_key].qty_retur = _val * detail_retur_cache[_key].nilai_konversi;
                detail_retur_cache[_key].qty_input = _val;
            }

            $.each(detail_retur_cache, function(key, val){
                // next update this line is usefull
                // total_retur = total_retur + (val.harga * val.qty_retur);

                // use qty_input because unit cannot be changed by user.
                total_retur = total_retur + (val.harga * val.qty_input);
            });
            subtotal_retur = detail_retur_cache[_key].harga * _val;
        }

        subtotal_retur_text.text(docoHelper.convertToRupiah(subtotal_retur));
        total_retur_text.text(docoHelper.convertToRupiah(total_retur));
    });

    $(document).on("click", "#btn-retur", function(e){
        e.preventDefault();
        var urlPost = $("#retur-form").attr("action");
        var dataPost = $("#retur-form").serializeArray();

        dataPost.push(
            {
                name: "detail_retur",
                value: JSON.stringify(detail_retur_cache)
            }
        );

        $().docoForm("click",{
            url : urlPost,
            data : dataPost,
            success : function (data){
                $("#btn-retur").attr("disabled", true);
                var no_retur = data.response.no_retur;
                (new PNotify({
                    title: "Proses Berhasil !",
                    text: "Informasi Retur dengan Nomor <strong>" + no_retur + "</strong> berhasil disimpan, apakah Anda ingin mencetak dokumen ?",
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
                                text: \'Ya\',
                                addClass: \'btn btn-xs btn-success\',
                            },
                            {
                                text: \'Tidak\',
                                addClass: \'btn btn-xs btn-danger\',
                            }
                        ]
                    },
                    history: {
                        history: false
                    }
                })).get().on(\'pnotify.confirm\', function() {
                    window.open("/gudang/informasi-penerimaan-obat/retur-pdf?no_retur="+no_retur, "_blank");
                    window.location = "/gudang/informasi-retur-obat-supplier";
                }).on(\'pnotify.cancel\', function() {
                    window.location = "/gudang/informasi-retur-obat-supplier"
                });
            }
        })
    });

    $(document).on("click", "#btnSimpan", function(e){
        e.preventDefault();
        if(!$.isEmptyObject(_temp_detail_retur))
        {
            $.extend(detail_retur_cache, _temp_detail_retur);
        }
        $("#modalRetur").modal("toggle");
    });


    $("#modalRetur").on("shown.bs.modal", function(e){
        _temp_detail_retur = {};
        drawTblTambahObat();
    });

    $("#modalRetur").on("hidden.bs.modal", function(e){
        _table.destroy();
        drawMainTable();
    });

    $(document).on("click", "#tblTambahObat tr", function(e){
        var _row = _table.row($(this));
        var _row_data = _row.data();

        if(typeof _row_data != "undefined")
        {
            if(!$(this).closest("tr").hasClass("selected"))
            {
                $(this).closest("tr").removeClass("selected");
                delete _temp_detail_retur[_row_data.penerimaanobatdetail_id];
            }else
            {
                if(typeof _temp_detail_retur[_row_data.penerimaanobatdetail_id] == "undefined")
                {
                     var data_torow = {
                        no_po : _row_data.nomor_po,
                        no_penerimaan : _row_data.no_penerimaan,
                        obatalkes_id : _row_data.obatalkes_id,
                        no_faktur : _row_data.no_faktur,
                        obatalkes_nama : _row_data.obatalkes_nama,
                        satuan_besar : _row_data.satuan_besar,
                        satuanbesar_id : _row_data.satuanbesar_id,
                        tgl_kadaluarsa : _row_data.tgl_kadaluarsa,
                        no_batch : _row_data.no_batch,
                        qty_retur : 0,
                        qty_input : 0,
                        qty_diterima : _row_data.qty_diterima,
                        penerimaanobatdetail_id : _row_data.penerimaanobatdetail_id,
                        penerimaanobat_id : _row_data.penerimaanobat_id,
                        qty_yg_diterima : _row_data.qty_yg_diterima,
                        nilai_konversi : _row_data.nilai_konversi,
                        on_retur : _row_data.on_retur,
                        harga : _row_data.harga
                    };
                    _temp_detail_retur[_row_data.penerimaanobatdetail_id] = data_torow;
                }
            }
        }
    });

', View::POS_END);
?>

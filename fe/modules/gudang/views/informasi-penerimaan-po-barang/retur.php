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
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
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
                        'action' => "/gudang/informasi-penerimaan-po-barang/retur?id={$id}",
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
                                <div class="row">
                                    <div class="col-md-3">
                                        <p><b>Nama Supplier</b></p>
                                        <p><?= $header["supplier_nama"] ?></p>
                                    </div>
                                    <div class="col-md-3">
                                        <p><b>Tarif Pajak</b></p>
                                        <p><?= isset($header["pajak_persen"]) ? $header["pajak_persen"] : "0" ?> %</p>
                                    </div>
                                    <div class="col-md-3">
                                        <p><b>Tanggal Retur</b></p>
                                        <p><?= date("d-M-Y") ?></p>
                                        <?= $form
                                        ->field($form_model, 'tgl_retur')->hiddenInput([
                                            "value" => date("d-M-Y")
                                        ])->label(false); ?>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="form-group highlight-addon field-returformbarang-pegawairetur_id">
                                                <label class="control-label" for="returformobat-pegawairetur_id">Pegawai Retur</label>
                                                <input type="text" class="form-control" value="<?= Yii::$app->docoVars->user('nama_pegawai') ?>" readonly="">
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
                                            <?= $form
                                            ->field($form_model, 'alasan_retur')->textarea([
                                                'class' => 'form-control',
                                                'tabindex' => 1
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
                                   <button type="button" class="btn btn-success" id="btn-tambah-barang"><i class="fa fa-search"></i> Tambah Barang</button>
                                </div>
                            </div>

                            <table class="table table-striped table-condensed table-hover" id="table-retur">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th><?= \Yii::t("fe", "No. PO") ?></th>
                                        <th><?= \Yii::t("fe", "No. Penerimaan") ?></th>
                                        <th><?= \Yii::t("fe", "No. Faktur") ?></th>
                                        <th><?= \Yii::t("fe", "Nama Barang") ?></th>
                                        <th><?= \Yii::t("fe", "Qty Diterima") ?></th>
                                        <th><?= \Yii::t("fe", "On Retur") ?></th>
                                        <th><?= \Yii::t("fe", "Tanggal Kadaluarsa") ?></th>
                                        <th><?= \Yii::t("fe", "No. Batch") ?></th>
                                        <th><?= \Yii::t("fe", "Qty Retur") ?> <sup style="color: red">*</sup></th>
                                        <th><?= \Yii::t("fe", "Aksi") ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($model->list_data)): ?>
                                        <?php
                                        foreach ($model->list_data as $index => $detail):
                                            $no_po = $detail["nomor_po"];
                                            $no_penerimaan = $detail["no_penerimaan"];
                                            $penerimaanbarangdetail_id = $detail["penerimaanbarangdetail_id"];

                                            $_detailretur[$penerimaanbarangdetail_id] = [
                                                "no_po" => $no_po,
                                                "no_penerimaan" => $no_penerimaan,
                                                "barang_id" => $detail["barang_id"],
                                                "no_faktur" => $detail["no_faktur"],
                                                "barang_nama" => $detail["barang_nama"],
                                                "satuan_besar" => $detail["satuan_besar"],
                                                "satuanbesar_id" => $detail["satuanbesar_id"] ,
                                                "tgl_kadaluarsa" => isset($detail["tgl_kadaluarsa"]) ? date("d-M-Y", strtotime($detail["tgl_kadaluarsa"])) : "" ,
                                                "no_batch" => $detail["no_batch"] ,
                                                "qty_diterima" => $detail["qty_diterima"] ,
                                                "penerimaanbarang_id" => $detail["penerimaanbarang_id"] ,
                                                "penerimaanbarangdetail_id" => $penerimaanbarangdetail_id ,
                                                "qty_yg_diterima" => $detail["qty_diterima"]." ".$detail["satuan_besar"],
                                                "nilai_konversi" => isset($detail["nilai_konversi"]) ? $detail["nilai_konversi"] : 0,
                                                "on_retur" => isset($detail["on_retur"]) ? $detail["on_retur"] / $detail['nilai_konversi'] : 0,
                                                "qty_input" => isset($detail["qty_input"]) ? $detail["qty_input"] : 0,
                                                "qty_retur" => 0 ,
                                            ];
                                        ?>
                                            <tr
                                                data-nopo = "<?= $no_po ?>"
                                                data-nopenerimaan = "<?= $no_penerimaan ?>"
                                                data-key = "<?= $penerimaanbarangdetail_id ?>"
                                            >
                                                <td><?= $detail["nomor_po"] ?></td>
                                                <td><?= $detail["no_penerimaan"] ?></td>
                                                <td><?= $detail["no_faktur"] ?></td>
                                                <td><?= $detail["barang_nama"] ?></td>
                                                <td><?= $detail["qty_diterima"] ?> <?= $detail["satuan_besar"] ?></td>
                                                <td><?= isset($detail["on_retur"]) ? $detail["on_retur"] / $detail['nilai_konversi'] : 0 ?></td>
                                                <td><?= isset($detail["tgl_kadaluarsa"]) ? date("d-M-Y", strtotime($detail["tgl_kadaluarsa"])) : "" ?></td>
                                                <td><?= $detail["no_batch"] ?></td>
                                                <td>
                                                    <?=
                                                        Html::textInput("ReturFormObat[qty_retur_$penerimaanbarangdetail_id]","0",[
                                                            'class' => 'form-control doco-number text-right qty-retur',
                                                            'maxlength' => 10,
                                                            'tabindex' => $index + 2,
                                                        ])
                                                    ?>
                                                    <div id="error_ReturFormBarangtqty_retur<?= $penerimaanbarangdetail_id ?>" class="error-parent"></div>
                                                </td>
                                                <!-- <td><input type="number" class="form-control qty-retur doco-number"></td> -->
                                                <td><button class="btn btn-danger btn-xs delretur"><i class="fa fa-trash"></i></button></td>
                                            </tr>
                                    <?php
                                    endforeach ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="10" class="text-center"><?= \Yii::t("fe", "Data tidak tersedia") ?></td>
                                        </tr>
                                    <?php endif ?>
                                </tbody>
                            </table>
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
                                <td><?= isset($header["pajak_persen"]) ? $header["pajak_persen"] : "undefined" ?> %</td>
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
                                    <th><?= \Yii::t("fe", "Nama Barang") ?></th>
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
    var _pajak_id = "'.$pajak_id.'";

    var _temp_detail_retur = {};

    var _table;

    function drawTblTambahObat()
    {
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
            ajax : baseUrl+"gudang/informasi-penerimaan-po-barang/get-barang-supplier?supplier_id="+_supplier_id+"&pajak_id="+_pajak_id,
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
                    "title" : "Nama Barang",
                    "data" : "barang_nama"
                },
            ],
            fnRowCallback : function (nRow, aData, iDisplayIndex, iDisplayIndexFull)
            {
                var _key = aData.penerimaanbarangdetail_id;
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
                    var _key = val.penerimaanbarangdetail_id;
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
            new_tr.attr("data-key", el.penerimaanbarangdetail_id);

            var input_val = el.qty_retur;
            var new_input = $("<input>");
            new_input.attr("type", "text");
            new_input.attr("class", "form-control doco-number text-right qty-retur");
            new_input.attr("name", "qty_retur");
            new_input.attr("maxlength", 10);
            new_input.attr("value", input_val);
            new_input.attr("tabindex", parseInt(i) + 2);

            var new_err_div = $("<div></div>");
            new_err_div.attr("id", "error_ReturFormBarangtqty_retur"+el.penerimaanbarangdetail_id);
            new_err_div.attr("class", "error-parent");

            var new_btn = $("<button>").append($("<i></i>").attr("class", "fa fa-trash"));
            new_btn.attr("class", "btn btn-danger btn-xs delretur");

            new_tr.append($("<td></td>").text(el.no_po));
            new_tr.append($("<td></td>").text(el.no_penerimaan));
            new_tr.append($("<td></td>").text(el.no_faktur));
            new_tr.append($("<td></td>").text(el.barang_nama));
            new_tr.append($("<td></td>").text(el.qty_yg_diterima));
            new_tr.append($("<td></td>").text(el.on_retur));
            new_tr.append($("<td></td>").text(el.tgl_kadaluarsa));
            new_tr.append($("<td></td>").text(el.no_batch));
            new_tr.append($("<td></td>").append(new_input).append(new_err_div));
            new_tr.append($("<td></td>").append(new_btn));

            _tbody.append(new_tr);
        });
    }

    $(document).ready(function()
    {
        $("#btn-reset").click(function(e){
            $("input").val("");
            $("textarea").val("");

            $.each(detail_retur_cache, function(i, el){
                el.qty_retur = 0;
            });
        });
    });

    $(document).on("click", "#btn-tambah-barang", function(e)
    {
        e.preventDefault();
        $("#modalRetur").modal();
    });

    $(document).on("click", ".delretur", function(e)
    {
        e.preventDefault();
        var _row = $(this).closest("tr");
        var _key = _row.attr("data-key");

        if(typeof detail_retur_cache[_key] != "undefined")
        {
            delete detail_retur_cache[_key];
            _row.remove();
        }
    });

    $(document).on("keyup", ".qty-retur", function(e)
    {
        e.preventDefault();
        console.log($(this).val());
        var _row = $(this).closest("tr");
        var _key = _row.attr("data-key");
        var _val = docoHelper.convertToAngka($(this).val());



        if(typeof detail_retur_cache[_key] != "undefined")
        {
            var max_qty = parseInt(detail_retur_cache[_key].qty_diterima - detail_retur_cache[_key].on_retur);
            if(_val >= max_qty)
            {
                $(this).val(parseInt(max_qty));
                detail_retur_cache[_key].qty_retur = max_qty * detail_retur_cache[_key].nilai_konversi;
                detail_retur_cache[_key].qty_input = max_qty;
            }else
            {
                detail_retur_cache[_key].qty_retur = _val * detail_retur_cache[_key].nilai_konversi;
                detail_retur_cache[_key].qty_input = _val;
            }
        }
    });

    $(document).on("click", "#btn-retur", function(e)
    {
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
                var no_retur = data.response.no_retur;
                var id_retur = data.response.id_retur;
                $("#btn-retur").attr("disabled", true);
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
                    window.open("/gudang/informasi-penerimaan-po-barang/retur-pdf?id="+id_retur, "_blank");
                    window.location = "'.Url::to(['index']).'";
                }).on(\'pnotify.cancel\', function() {
                    window.location = "'.Url::to(['index']).'";
                });
            }
        })
    });

    $(document).on("click", "#btnSimpan", function(e)
    {
        e.preventDefault();
        if(!$.isEmptyObject(_temp_detail_retur))
        {
            $.extend(detail_retur_cache, _temp_detail_retur);
        }
        $("#modalRetur").modal("toggle");
    });


    $("#modalRetur").on("shown.bs.modal", function(e)
    {
        _temp_detail_retur = {};
        drawTblTambahObat();
    });

    $("#modalRetur").on("hidden.bs.modal", function(e)
    {
        _table.destroy();
        drawMainTable();
    });

    $(document).on("click", "#tblTambahObat tr", function(e)
    {
        var _row = _table.row($(this));
        var _row_data = _row.data();

        if(typeof _row_data != "undefined")
        {
            if(!$(this).closest("tr").hasClass("selected"))
            {
                $(this).closest("tr").removeClass("selected");
                delete _temp_detail_retur[_row_data.penerimaanbarangdetail_id];
            }else
            {
                if(typeof _temp_detail_retur[_row_data.penerimaanbarangdetail_id] == "undefined")
                {
                     var data_torow = {
                        no_po : _row_data.nomor_po,
                        no_penerimaan : _row_data.no_penerimaan,
                        barang_id : _row_data.barang_id,
                        no_faktur : _row_data.no_faktur,
                        barang_nama : _row_data.barang_nama,
                        satuan_besar : _row_data.satuan_besar,
                        satuanbesar_id : _row_data.satuanbesar_id,
                        tgl_kadaluarsa : _row_data.tgl_kadaluarsa,
                        no_batch : _row_data.no_batch,
                        qty_retur : 0,
                        qty_input : 0,
                        qty_diterima : _row_data.qty_diterima,
                        penerimaanbarangdetail_id : _row_data.penerimaanbarangdetail_id,
                        penerimaanbarang_id : _row_data.penerimaanbarang_id,
                        qty_yg_diterima : _row_data.qty_yg_diterima,
                        nilai_konversi : _row_data.nilai_konversi,
                        on_retur : _row_data.on_retur
                    };
                    _temp_detail_retur[_row_data.penerimaanbarangdetail_id] = data_torow;
                }
            }
        }
    });

', View::POS_END);
?>
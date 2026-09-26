<?php
use app\components\DHtml;
use app\components\DocoHelpers;

use kartik\widgets\DatePicker;
use kartik\widgets\ActiveForm;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"),
'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$_cache = [];
?>

<style type="text/css">
    .datepicker>div{
        display:block;
    }

    .static-value{
        padding-top: 10px;
        padding-bottom: 15px;
    }
</style>

<?php # dump([$header, $detail]) ?>

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
                    'custom-save' => [
                        'type' => 'button',
                        'title' => Yii::t('fe', 'Simpan'),
                        'icon' => 'fa fa-floppy-o',
                        'attributes' => [
                            'data-options' => 'click',
                            'id' => 'btn-simpan',
                        ],
                    ],
                ]);?>
            </div>

            <div class="panel-body">
                <?php
                    $form = ActiveForm::begin([
                        'id' => 'mutasi-form',
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'action' => "/gudang/inf-pemesanan-barang/proses-mutasi?id=$id",
                        'formConfig' => [
                            'labelSpan' => 3,
                            'deviceSize' => ActiveForm::SIZE_SMALL
                        ],
                        'options' => [
                            'skip-confirm' => "true"
                        ]
                ]);
                ?>
                <?= $form->field($model, 'pesanbarang_id')->hiddenInput(["value" => $header["pesanbarang_id"]])->label(false); ?>
                <?= $form->field($model, 'ruangantujuan_id')->hiddenInput(["value" => $header["ruanganpemesan_id"]])->label(false); ?>

                <div class="row" style="padding-bottom: 15px">
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="col-md-12">
                                <?= $form->field($model, 'tgl_mutasibarang',[
                                      'horizontalCssClasses' => [
                                              'label' => 'text-left control-label col-sm-4',
                                              'wrapper' => 'col-md-8'
                                          ],
                                      ])->textInput([
                                        "value" => date("d-M-Y"),
                                        "readonly" => true
                                      ]); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <label class="control-label col-sm-4">No. Pemesanan</label>
                                <div class="col-md-8 static-value">
                                    <?= isset($header["no_pemesanan"]) ? $header["no_pemesanan"] : "" ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="col-md-12">
                                <label class="control-label col-sm-4">Instalasi Tujuan</label>
                                <div class="col-md-8 static-value">
                                    <?= isset($header["instalasi_pemesan"]) ? $header["instalasi_pemesan"] : "" ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <label class="control-label col-sm-4">Ruangan Tujuan</label>
                                <div class="col-md-8 static-value">
                                    <?= isset($header["ruangan_pemesan"]) ? $header["ruangan_pemesan"] : "" ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <div class="col-md-12">
                                <?= $form->field($model, 'pegawaimengetahui_id',[
                                      'horizontalCssClasses' => [
                                              'label' => 'text-left control-label col-sm-4',
                                              'wrapper' => 'col-md-8'
                                          ],
                                      ])->dropDownList([],
                                        [
                                        'class' => 'form-control search-pegawai',
                                        'tabindex' => 2
                                        ]
                                );?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-body">
                <h4>Data Barang</h4>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?= Yii::t("fe", "No.") ?></th>
                            <th><?= Yii::t("fe", "Nama Barang") ?></th>
                            <th><?= Yii::t("fe", "Qty Pemesanan") ?></th>
                            <th><?= Yii::t("fe", "Stock Saat Ini") ?></th>
                            <th><?= Yii::t("fe", "Stock Pemesan") ?></th>
                            <th><?= Yii::t("fe", "Qty Kirim") ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($detail)):
                            $no = 1;
                         ?>
                            <?php foreach ($detail as $index => $row):
                                $_cache[$row["pesanbarangdetail_id"]] = [
                                    "barang_nama" => $row["barang_nama"],
                                    "barang_id" => $row["barang_id"],
                                    "harga_netto" => isset($row["harga_netto"]) ? $row["harga_netto"] : 0,
                                    "jumlah_input" => $row["jumlah_input"] , #yg diisi user saat memesan
                                    "nilai_konversi" => isset($row["nilai_konversi"]) ? $row["nilai_konversi"] : 1,
                                    "pesanbarangdetail_id" => $row["pesanbarangdetail_id"],
                                    "qty_dipesan" => $row["qty_pesan"], #yg dipesan dalam bentuk satuan terkecil
                                    "qty_mutasi" => 0, # satuan terkecil yang mau dimutasikan
                                    "qty_mutasi_input" => 0, # input user yang mau dimutasikan
                                    "qty_tersedia" => floor($row["qty_tersedia"]),
                                    "qty_tersedia_konversi" => floor($row["qty_tersedia"] / $row["nilai_konversi"]),
                                    "qty_pemesan_konversi" =>  floor($row["stok_pemesan"] / $row["nilai_konversi"]),
                                    "satuanbesar_id" => $row["satuanbesar_id"],
                                    "satuankecil_id" => $row["satuankecil_id"],
                                ];

                                $data = $_cache[$row["pesanbarangdetail_id"]];
                                ?>
                                <tr data-id="<?= $row["pesanbarangdetail_id"] ?>">
                                    <td><?= $no ?></td>
                                    <td><?= isset($row["barang_nama"]) ? $row["barang_nama"] : "-" ?></td>
                                    <td><?= isset($row["jumlah_input"]) ? $row["jumlah_input"] : "-" ?> <?= isset($row["satuan_besar"]) ? $row["satuan_besar"] : "-" ?></td>
                                    <td><?= isset($data["qty_tersedia_konversi"]) ? $data["qty_tersedia_konversi"] : "-" ?> <?= isset($row["satuan_besar"]) ? $row["satuan_besar"] : "-" ?></td>
                                    <td><?= isset($data["qty_pemesan_konversi"]) ? $data["qty_pemesan_konversi"] : "-" ?> <?= isset($row["satuan_besar"]) ? $row["satuan_besar"] : "-" ?></td>
                                    <td>
                                        <input type="text" tabindex="<?= $no+2 ?>" class="text-right form-control qty-input doco-number" maxlength="10" style="width: 100px">
                                        <div class="error-class"></div>
                                    </td>
                                </tr>
                            <?php
                            $no++;
                             endforeach ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Data tidak tersedia</td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('

    var _cache = '.json_encode($_cache).';

    function calcTotalNetto(data)
    {
        var total = 0;
        $.each(data, function(index, el){
            sum = el.harga_netto * (el.qty_mutasi_input * el.nilai_konversi);
            total += sum;
        });
        return total;
    }

    $(document).ready(function(){
        $(".search-pegawai").select2({
            placeholder: "Pilih Pegawai",
            minimumInputLength: 3,
            ajax : {
                url: baseUrl+"gudang/inf-pemesanan-barang/search-pegawai",
                dataType: "json",
                quietMillis: 250,
                data: function (params) {
                  var query = {
                    search: params,
                  }
                  return params;
                },
                processResults: function (data) {
                    $.each(data.result, function (key,val) {
                    });
                  return {
                    results: data.result
                  };
                },
                dropdownCssClass: "bigdrop",
                escapeMarkup: function (m) { return m; },
            },
        });
        $(".dataTables_filter").hide();
    });

    $(document).on("keyup keydown change", ".qty-input", function(e){
        var tr = $(this).closest("tr");
        var id = tr.attr("data-id");
        var val = docoHelper.convertToAngka($(this).val() ? $(this).val() : 0);

        var data = _cache[id];

        if(typeof data != "undefined")
        {
            var max = data.qty_dipesan / data.nilai_konversi;
            var stock = data.qty_tersedia / data.nilai_konversi;

            if(val > max)
            {
                val = max;
            }

            if(val > stock)
            {
                val = Math.floor(stock);
            }

            var real_qty = val * data.nilai_konversi;
            _cache[id].qty_mutasi = real_qty;
            _cache[id].qty_mutasi_input = val;
            $(this).val(val);
        }else{
            $(this).val(-1);
        }
    });

    $(document).on("click", "#btn-simpan", function(e)
    {
        e.preventDefault();
        var urlPost = $("#mutasi-form").attr("action");
        var dataPost = $("#mutasi-form").serializeArray();

        $(".qty-input").trigger("change");

        var totalhargamutasi = calcTotalNetto(_cache);

        dataPost.push(
            {
            name: "mutasi_detail",
            value: JSON.stringify(_cache),
            },
            {
            name: "FormMutasiBarang[totalhargamutasi]",
            value: parseInt(totalhargamutasi)
            },
        );

        $().docoForm("click",{
            url : urlPost,
            data : dataPost,
            success : function (data){
                var no_transaksi = data.response.no_transaksi;
                var id_transkasi = data.response.id_transkasi;
                if(id_transkasi != null){
                    $("#btn-simpan").prop("disabled", true);
                    (new PNotify({
                        title: "Proses Berhasil !",
                        text: "Informasi Mutasi dengan Nomor <strong>" + no_transaksi + "</strong> berhasil disimpan, apakah Anda ingin mencetak dokumen ?",
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
                        window.open("/gudang/inf-pemesanan-barang/cetak-mutasi?id="+id_transkasi, "_blank");
                        window.location = "'.Url::to(['index']).'";
                    }).on(\'pnotify.cancel\', function() {
                        window.location = "'.Url::to(['index']).'";
                    });
                }else{
                    console.log("data transaksi gagal dibuat");
                }
            },
            error : function(data) {
                var response = data.responseJSON.response;
                let message = [];

                if(response.data != undefined) {
                    var data = response.data
                    for (const [key, value] of Object.entries(data)) {
                        message.push(value);
                    }
                    docoNotification("error", "Error", message.join( "<br />" ))
                } else {
                    docoNotification("error", response.title, response.message)
                }
            }
        })
    });


', View::POS_END);
?>

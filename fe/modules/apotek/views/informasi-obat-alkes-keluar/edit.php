<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', 'Detail Pemesanan Obat Alkes');
$this->params['breadcrumbs'][] = ['label' => 'Informasi Pemesanan Obat Alkes', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Detail Pemesanan';
$idParent = DocoHelpers::encrypt($id);
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar($btn_toolbar);?>
            </div>
            <div class="panel-body">
                <div class="col-md-12 text-center">
                    <?php $ruangan = !empty($data->ruangan_pemesan) ? ucfirst($data->ruangan_pemesan) : '-' ?>
                    <h4><b><?= Yii::t('fe','PEMESANAN OBAT ALKES') ?></b></h4>

                    <hr>
                </div>

                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe','Detail Pemesanan Obat Alkes') ?></b></h6>
                        </div>
                        <div class="panel-body">
                        	<table width="100%" class="tabel">
                                <tbody>
                                    <tr>
                                        <td class="bold w-10">
                                            <?= Yii::t('fe','Tanggal Pemesanan') ?>
                                        </td>
                                        <td>
                                            <?= !empty($data->tglpemesanan) ?  date('d M Y',strtotime($data->tglpemesanan)) : '' ?>
                                        </td>
                                        <td class="bold w-10">
                                            <?= Yii::t('fe','Nomer Pemesanan') ?>
                                        </td>
                                        <td>
                                            <?= !empty($data->nopemesanan) ?  $data->nopemesanan : '' ?>
                                        </td>
                                        <td class="bold w-15">
                                            <?= Yii::t('fe','Ruangan Tujuan Pemesanan') ?>
                                        </td>
                                        <td>
                                            <?= !empty($data->ruangan_tujuan) ? $data->ruangan_tujuan : '' ?>
                                        </td>
                                        <td class="bold w-10">
                                            <?= Yii::t('fe','Ruangan Pemesan') ?>
                                        </td>
                                        <td>
                                            <?= $ruangan ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <br>

                            <button type="button"
                                class="addrow btn btn-info btn-labeled btn-xs"
                                data-toggle="modal"
                                data-target="#modal_backdrop"
                                data-width="50%"
                                action=<?= "/apotek/informasi-obat-alkes-keluar/tambah-obat" ?>>
                                <b><i class="fa fa-plus"></i></b>Tambah Obat
                            </button>

                            <table id="detail-edit-pemesanan" class="table datatable-basic table-striped table-hover dataTable no-footer" style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?= \Yii::t("fe", "Nama Obat Alkes") ?></th>
                                        <th></th>
                                        <th><?= \Yii::t("fe", "Qty Pemesanan") ?></th>
                                        <th></th>
                                        <th><?= \Yii::t("fe", "Qty Konversi") ?></th>
                                        <th></th>
                                        <th><?= \Yii::t("fe", "Aksi") ?></th>
                                    </tr>
                                </thead>
                                    <tr class="loading">
                                        <th colspan="6" class="text-center">Loading...</th>
                                    </tr>
                                <tbody>
                                </tbody>
                            </table>
                            <div class="col-md-5">
                                Catatan:
                                <textarea name="catatan" id="catatan" cols="5" rows="5" class="form-control"><?= !empty($data->keterangan_pesan) ? $data->keterangan_pesan : '' ?></textarea>
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
    // Global Var
    var tableEdit, tableEditPemesanan;
    var rowNum = 1;
    var listObat = [];
    var ruangan_id = '.$data->ruangan_id.';
    $("#cetak-pdf").on("click",function (event) {
        event.preventDefault();
        var url = window.location.origin;
        var target = $(this).attr(\'data-target\');
        window.open(url+target);
    });

    // Event Ready
    $(document).ready(function() {
        $.ajax({
            url: baseUrl+"apotek/informasi-obat-alkes-keluar/get-data-pemesanan-form?id=\''.$idParent .'\'",
            contentType: "application/json",
            dataType: "json",
            success: function(result){
                $(".loading").remove();
                listObat = result.data;
                rowNum = listObat.length;
                tableEditPemesanan = $("#detail-edit-pemesanan").DataTable({
                    filter: false,
                    data: listObat,
                    columns: [
                        { title: "No", data: "rowNum" },
                        { title: "Nama Obat Alkes", data: "obatalkes_nama" },
                        { title: "ID Obat Alkes", data: "obatalkes_id", visible: false },
                        { title: "Qty Pemesanan", data: "qty_form" },
                        { title: "ID Satuan Kecil", data: "satuankecil_id", visible: false },
                        { title: "Qty Konversi", data: "qty_konversi" },
                        { title: "Stok", data: "stok", visible: false },
                        { title: "Aksi", data: "action_column", orderable: false, searchable: false }
                    ]
                });

                onChangeQtyPemesanan();
            }
        });

        $("#save-edit-pesan").on("click",function(){
            var not_deleted = $.grep(listObat, function(e){
                return e.to_delete == false;
            });

            if(not_deleted.length < 1) {
                docoNotification("warning", i18next.t("Perhatian"), i18next.t("Minimal terdapat 1 obat"));
                return false;
            }

            var url = $(this).data("url");
            var formData = {
                "listObat": listObat,
                "listQtyObat": tableEditPemesanan.$("input").serializeArray(),
                "catatan": $("#catatan").val()
            };

    		$(this).docoForm("click", {
    			url: url,
		        data: formData,
		        method: "POST",
		        success: function (data) {
		        	setTimeout(() => {
		                window.location.href = "/apotek/informasi-obat-alkes-keluar";
		            }, 150);
		        }
		    });
	    });
    });

    function onChangeQtyPemesanan() {
        $.each(listObat, function(key, value){
            var obatalkes_id = value.obatalkes_id;
            var nilai_konversi = value.nilai_konversi;
            var stok = value.stok;
            $("input[name=\'QtyPesan["+obatalkes_id+"]\']").on("input", function() {
                match        = (/(\d{0,9})[^.]*((?:\.\d{0,2})?)/g).exec(this.value.replace(/[^\d.]/g, ""));
                this.value   = match[1] + match[2];

                var qty = $("input[name=\'QtyPesan["+obatalkes_id+"]\']").val();
                // if(qty > stok) {
                //     qty = stok;
                //     $("input[name=\'QtyPesan["+obatalkes_id+"]\']").val(stok);
                // }

                var qty_konversi = qty * nilai_konversi;
                $(".qty-konversi-"+obatalkes_id).html(qty_konversi);
            });
        });
    }

    $(document).on("click", ".btn-delete-row", function(e){
        var btn = $(this);
        var key = btn.data("key");
        var pos = listObat.findIndex(obat => obat.identifier == key);
        var data_obat = listObat[pos];
        data_obat.to_delete = true;
        listObat[pos] = data_obat;

        if(listObat[pos].pesanobatdetail_id == "" && listObat[pos].to_delete) {
            listObat.splice(pos, 1);
        }

        tableEditPemesanan.rows(function(idx, data, node){
            return data.identifier == key ? true : false;
        }).remove().draw();
    });

',View::POS_END,'b-index');
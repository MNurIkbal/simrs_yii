<?php

use yii\web\View;
use yii\helpers\Html;

?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Tambah Obat</h5>
</div>
<div class="modal-body">
    <form id="modal-before-leaving" class="form-horizontal" autocomplete="off">
        <div class="form-group">
            <label class="control-label col-sm-3">Drug</label>
            <div class="col-sm-9">
                <select class="form-control" name="obatalkes_id">
                    <option value="">-- Pilih --</option>
                </select>
                <div class="help-block"></div>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Dose</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" name="dose">
                <div class="help-block"></div>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-3">Time Delivery</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" name="time_delivery">
                <div class="help-block"></div>
            </div>
        </div>
        <div class="row" style=" margin-top: 20px;">
            <div class="col-sm-12" style="text-align: right;">
                <input type="hidden" name="obatalkes_nama">
                <button type="button" id="save-button-before-leaving" class="btn btn-info btn-labeled btn-xs btn-custom-save">
                    <b><i class="fa fa-floppy-o"></i></b> Simpan
                </button>
            </div>
        </div>
    </form>
</div>


<?php
$this->registerJs('
    $("#modal-before-leaving").ready(function() {

        $("#modal-before-leaving [name=time_delivery]").timepicker({
            showMeridian: false
        });

        $("#modal-before-leaving [name=obatalkes_id").docoPaginationSelec2(
            config = {
                placeholder : "Pilih ... ",
                _api : "/apotek/transaksi-resep/list-obat-alkes-depo",
                dropdownParent: $("#modal-before-leaving"),
                ajax: {
                    data: function(params) {
                        return {
                            q: params.term,
                            page: params.page || 1,
                            ruangan_id: 29,
                        }
                    },
                    results: function (data, params) {
                        var more = (params.page * 30) < data.total_count;
                        return { results: data.items, more: more };
                    },
                    processResults: function(res, params) {
                        params.page = params.page || 1;
                        var arr = [];
                        $.each(res.data_stok, function(index, value) {
                            if (index < 10) {
                                var _disabled = value.qty_tersedia <= 0 ? true : false;
                                arr.push({
                                    id: value.obatalkes_id,
                                    text: value.obatalkes_nama,
                                    disabled: _disabled
                                })

                                let data = [];
                                let response = res.data_stok;
                                for (var i in response) {
                                    data.push({ id: response[i].obatalkes_id, text: response[i].obatalkes_nama });
                                }
                            }
                        });
                        return {
                            results: arr,
                            pagination: {
                                more: res.data_stok.length > 10
                            }
                        };
                    }
                },
            }
        );

        $("#modal-before-leaving [name=obatalkes_id").on("select2:select", function(e) {
            $("#modal-before-leaving [name=obatalkes_nama").val(e.params.data.text);
        })
    });

', View::POS_END, 'b-index');
?>
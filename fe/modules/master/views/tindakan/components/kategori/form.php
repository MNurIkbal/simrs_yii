<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-04-27 16:06:09
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-03-27 11:16:48
 */


use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\ActiveForm;

?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <?=
                    DocoHelpers::generateToolbar([
                        'simpan' => [
                            'title' => \Yii::t('fe', 'Simpan'),
                            'icon' => 'fa fa-save',
                            'attributes' => [
                                'class' => 'spa',
                                'action' => 'tindakan/save-kategori',
                                'data-options' => 'click',
                                'form-id' => 'kategori-form',
                                'data-render' => 'kategori',
                                'data-tab' => 'tab-kategori',
                                'data-target' => '#view-kategori',
                                'id' => 'btn-save'
                            ]
                        ],
                        'kembali' => [
                            'title' => \Yii::t('fe', 'Kembali'),
                            'icon' => 'fa fa-arrow-left',
                            'attributes' => [
                                'class' => 'spa',
                                'data-options' => 'click',
                                'data-render' => 'kategori',
                                'data-tab' => 'tab-kategori',
                                'data-target' => '#view-kategori',
                            ]
                        ],
                    ],'#table-kategori');
                ?>
            </div>
            <div class="panel-body">
                <?php
                    $form = ActiveForm::begin([
                        'id' => 'kategori-form',
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'formConfig' => [
                            'labelSpan' => 3,
                            'deviceSize' => ActiveForm::SIZE_SMALL
                        ],
                        'options' => [
                            'role' => 'form',
                        ]
                    ]);
                    ?>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label text-left control-label col-sm-3">
                                <h3><strong><?= $title ?></strong></h3>
                            </label>
                            <div class="col-md-5"></div>
                        </div>
                        <?=
                            $form->field($model, 'kategori_kode', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-4'
                                ]
                            ])->textInput()->label(Yii::t('fe', 'Kode Kategori'))
                        ?>
                        <?=
                            $form->field($model, 'kategoritindakan_nama', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-4'
                                ]
                            ])->textInput()->label(Yii::t('fe', 'Nama Kategori'))
                        ?>
                        <?=
                            $form->field($model, 'kategoritindakan_namalainnya', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-4'
                                ]
                            ])->textInput()->label(Yii::t('fe', 'Nama Lainnya'))
                        ?>
	                    <?=
	                        $form->field($model, 'catatan', [
	                            'horizontalCssClasses' => [
	                                'label' => 'text-left control-label col-sm-2',
	                                'wrapper' => 'col-md-4'
	                            ]
	                        ])->textArea()
	                    ?>
                        <?=
                            $form->field($model, 'is_active', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-4'
                                ] 
                            ])->checkbox(['label' => 'Aktif'])->label(Yii::t('fe', 'Status'))
                        ?>
                        <br><hr>
                        <div class="form-group">
                            <div class="col-sm-2">
                                <label><?= Yii::t('fe', "Tambah Tindakan") ?></label>
                            </div>
                            <div class="col-sm-4">
                                <select id="auto-tindakan"></select>
                                <input type="hidden" name="tampung_tindakan_kategori" id="tampung_tindakan_kategori">
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group">
                            <table id="tabel-tampung-tindakan-kategori" class="table table-striped table-condensed table-hover" style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th>No</th>
                                        <th>Tindakan</th>
                                        <th>Hapus</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<script>
var tabel;
var url = '/master/tindakan/get-tindakan-maping?column_id=kategoritindakan_id&';

var hapusTindakan = function(id){
    $.getJSON('/master/tindakan/hapus-cache-tindakan-kategori?id='+id,{},function(data){
        tabel.draw();
    });
}

$("#kategoritindakanform-kategoritindakan_nama").on("change", function(){
    var nama = $(this).val();
    var nama_lainnya = $("#kategoritindakanform-kategoritindakan_namalainnya").val();
    if(nama_lainnya == '') {
        $("#kategoritindakanform-kategoritindakan_namalainnya").val(nama);
    }
    else {
        $("#kategoritindakanform-kategoritindakan_namalainnya").val(nama_lainnya);
    }
});

$(document).ready(function() {
    $('#auto-tindakan').select2({
        minimumInputLength: 3,
        placeholder: 'Tambah Tindakan',
        ajax: {
            url: url,
            data: function (params) {
            var query = {
                search: params.term,
                type: 'public'
            }
            return query;
            }
        }
    });

    var tampung_tindakan = {"data":[{id: 6, text: "Adm. Surat Keterangan Kematian", selected: true}],"draw":"2","recordsTotal":1,"recordsFiltered":0};
    var emptyTable = '<?= (\Yii::t("fe", "Tidak ada data yang tersedia"))?>';
    var info = '<?= (\Yii::t("fe", "Menampilkan _START_ sampai _END_ dari _TOTAL_ data"))?>';
    var infoEmpty = '<?= (\Yii::t("fe", "Menampilkan 0 sampai 0 dari 0 data"))?>';
    var infoFiltered = '<?= (\Yii::t("fe", "(disaring dari _MAX_ total data)"))?>';
    var lengthMenu = '<?= (\Yii::t("fe", "Menampilkan _MENU_ data"))?>';
    var loadingRecords = '<?= (\Yii::t("fe", "Memuat..."))?>';
    var processing = '<?= (\Yii::t("fe", "Memproses..."))?>';
    var search = '<?= (\Yii::t("fe", "Cari:"))?>';
    var zeroRecords = '<?= (\Yii::t("fe", "Tidak ada data yang ditemukan"))?>';
    var first = '<?= (\Yii::t("fe", "Pertama"))?>';
    var last = '<?= (\Yii::t("fe", "Terakhir"))?>';
    var next = '<?= (\Yii::t("fe", "Selanjutnya"))?>';
    var previous = '<?= (\Yii::t("fe", "Sebelumnya"))?>';
    var sortAscending = '<?= (\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terkecil ke yang terbesar"))?>';
    var sortDescending = '<?= (\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terbesar ke yang terkecil"))?>';

    tabel = $("#tabel-tampung-tindakan-kategori").docoTabel({
        filter: false,
        paging: false,
        sorting: [[2, "asc"]],
        drawCallback: function() {
            $('.dataTables_scrollBody').scrollTop($('.dataTables_scrollBody')[0].scrollHeight);
        },
        displayLength: false,
        serverSide: true,
        processing: true,
        scrollX: true,
        scrollCollapse: true,
        ajax:'tindakan/get-cache-tindakan-kategori',
        columns: [
            {title: "No", data:"no", searchable: false, orderable: false},
            {title: "Tindakan", data: "text", searchable: false},
            {title: "Hapus", data:"hapus"}, 
        ],
        language: {
            emptyTable: emptyTable,
            info: info,
            infoEmpty: infoEmpty,
            infoFiltered: infoFiltered,
            lengthMenu: lengthMenu,
            loadingRecords: loadingRecords,
            processing: processing,
            search: search,
            zeroRecords: zeroRecords,
            paginate: {
                first: first,
                last: last,
                next: next,
                previous: previous
            },
            aria: {
                sortAscending: sortAscending,
                sortDescending: sortDescending
            }
        }
    });

     $('#auto-tindakan').on('select2:select', function (e) {
        var data = e.params.data;
        tampung_tindakan.data.push(data);
        var recordTotal = tampung_tindakan.recordsTotal;
        tampung_tindakan.recordsTotal = parseInt(recordTotal) + 1;
        $.post("/master/tindakan/cache-tindakan-kategori", {
            tampung_tindakan:data,
            status_chache:"insert",
        },function(data){
            var $remote = $('#auto-tindakan');
            $remote.html('').select2('data',null);
            tabel.draw();
            if(data.status == 200){
                 docoNotification('success', data.title, data.text);
            }
            if(data.status == 500){
                 docoNotification('error', data.title, data.text);
            }
            $('#auto-tindakan').select2('focus');
        });
        
    });
});

</script>
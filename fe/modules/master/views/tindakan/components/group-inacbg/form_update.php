<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-04-27 16:06:09
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-03-27 14:00:17
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
                                'action' => 'tindakan/save-inacbg?groupinacbg_id='.$id_encrypt,
                                'data-options' => 'click',
                                'form-id' => 'group-inacbg-form',
                                'data-render' => 'group-inacbg',
                                'data-tab' => 'tab-group-inacbg',
                                'data-target' => '#view-group-inacbg',
                                'id' => 'btn-save'
                            ]
                        ],
                        'kembali' => [
                            'title' => \Yii::t('fe', 'Kembali'),
                            'icon' => 'fa fa-arrow-left',
                            'attributes' => [
                                'class' => 'spa',
                                'data-options' => 'click',
                                'data-render' => 'group-inacbg',
                                'data-tab' => 'tab-group-inacbg',
                                'data-target' => '#view-group-inacbg',
                            ]
                        ],
                    ],'#table-group-inacbg');
                ?>
            </div>
            <div class="panel-body">
                <?php
                    $form = ActiveForm::begin([
                        'id' => 'group-inacbg-form',
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
                            $form->field($model, 'groupinacbg_kode', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-4'
                                ]
                            ])->textInput()->label(Yii::t('fe', 'Kode INA CBGS'))
                        ?>
                        <?=
                            $form->field($model, 'groupinacbg_nama', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-4'
                                ]
                            ])->textInput()->label(Yii::t('fe', 'Nama Group INA CBGS'))
                        ?>
                        <?=
                            $form->field($model, 'groupinacbg_namalainnya', [
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
                            $form->field($model, 'is_obat', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-4'
                                ],
                            ])->radioList([ 
                                '0' => Yii::t('fe', 'Tindakan'), 
                                '1' => Yii::t('fe', 'Obat')
                            ], [
                                'itemOptions' => 
                                [
                                    'disabled' => true
                                ]
                            ])
                        ?>
                        <?=
                            $form->field($model, 'is_active', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-4'
                                ]
                            ])->checkbox(['label' => 'Aktif'])->label(Yii::t('fe', 'Status'))
                        ?>
                        <?=Html::activeHiddenInput($model, 'is_obat')?>
                        <br><hr>
                        <div class="form-group tindakan-form">
                            <div class="col-sm-2">
                                <label><?= Yii::t('fe', "Tambah Tindakan") ?></label>
                            </div>
                            <div class="col-sm-4">
                                <select id="auto-tindakan-inacbg" class="auto-list"></select>
                                <input type="hidden" name="tampung_tindakan" id="tampung_tindakan">
                            </div>
                        </div>
                        <div class="form-group obat-form hidden">
                            <div class="col-sm-2">
                                <label><?= Yii::t('fe', "Tambah Obat") ?></label>
                            </div>
                            <div class="col-sm-4">
                                <select id="auto-obat-inacbg" class="auto-list"></select>
                                <input type="hidden" name="tampung_obat" id="tampung_obat">
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group">
                            <table id="tabel-tampung-tindakan-inacbg" class="table table-striped table-condensed table-hover" style="width:100%">
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

<script type="text/javascript">
var tabel;
var id_encrypt = "<?=$id_encrypt?>";
    type = "<?=(empty($model->is_obat) ? 0 : $model->is_obat)?>";
var url = '/master/tindakan/get-tindakan-maping?column_id=groupinacbg_id&';
    checkvalue = $("input[name='GroupInaCbgForm[is_obat]']:checked").val();

$("#groupinacbgform-groupinacbg_nama").on("change", function(){
    var nama = $(this).val();
    var nama_lainnya = $("#groupinacbgform-groupinacbg_namalainnya").val();
    if(nama_lainnya == '') {
        $("#groupinacbgform-groupinacbg_namalainnya").val(nama);
    }
    else {
        $("#groupinacbgform-groupinacbg_namalainnya").val(nama_lainnya);
    }
});

$(document).ready(function() {
    if(checkvalue == 0){
        $('.tindakan-form').removeClass('hidden');
        $('.obat-form').addClass('hidden');
    }else{
        $('.tindakan-form').addClass('hidden');
        $('.obat-form').removeClass('hidden');
    }
    $('#auto-tindakan-inacbg').select2({
        minimumInputLength: 3,
        placeholder: 'Tambah Tindakan',
        ajax: {
            url: '/master/tindakan/get-tindakan-maping?column_id=groupinacbg_id&',
            data: function (params) {
            var query = {
                search: params.term,
                type: 'public'
            }
            return query;
            }
        }
    });
    $('#auto-obat-inacbg').select2({
        minimumInputLength: 3,
        placeholder: 'Tambah Obat',
        ajax: {
            url: '/master/tindakan/get-obat-mapping?column_id=groupinacbg_id&',
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

    tabel = $("#tabel-tampung-tindakan-inacbg").docoTabel({
        filter: false,
        paging: false,
        sorting: [[1, "asc"]],
        drawCallback: function() {
            $('.dataTables_scrollBody').scrollTop($('.dataTables_scrollBody')[0].scrollHeight);
        },
        displayLength: false,
        serverSide: true,
        processing: true,
        scrollY: "200px",
        scrollCollapse: true,
        ajax:'/master/tindakan/get-cache-tindakan-inacbg?groupinacbg_id=' + id_encrypt + '&type='+type,
        columns: [
            {title: "No", data:"no", searchable: false, orderable: false},
            {title: (type == 0) ? "Tindakan" : "Obat", data: "text",name:(type == 0) ? "daftartindakan_nama" : "obatalkes_nama",  searchable: false},
            {title: "Hapus", data:"hapus", searchable: false, orderable: false}, 
        ],
        rowCallback: function(row, data, index, fullindex){
            if(typeof data.color != "undefined"){
                $("td", row).css("background-color", data.color);
            }
        },
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

    $('.auto-list').on('select2:select', function (e) {
        var data = e.params.data;
        $.post("/master/tindakan/cache-tindakan-inacbg",{tampung_data:data,status_chache:"insert"},function(data){
            var $remote = $('.auto-list');
            $remote.html('').select2('data',null);
            tabel.ajax.url('/master/tindakan/get-cache-tindakan-inacbg?groupinacbg_id=' + id_encrypt + '&type='+type).load();
            if(data.status == 200){
                 docoNotification('success', data.title, data.text);
            }
            if(data.status == 500){
                 docoNotification('error', data.title, data.text);
            }
            if(checkvalue == 0){
                $('#auto-tindakan-inacbg').select2('focus');
            }else{
                $('#auto-obat-inacbg').select2('focus');
            }
        });
    });
    $(document).on('keydown', null, 'alt+s', function (event) {
        $('#btn-save').click();
    });
});

var hapusTindakan = function(id){
        $.getJSON('/master/tindakan/hapus-cache-tindakan-inacbg?id='+id,{},function(data){
            tabel.draw();
        });
    }

var hapusTindakanInacbg = function(daftartindakan_id, groupinacbg_id, type){
    $.post("tindakan/cache-tindakan-inacbg",{daftartindakan_id:daftartindakan_id,status_chache:"delete", type: type},function(data, xhr, ind){
        tabel.draw();
        if(ind.status == 200){
             docoNotification('success', data.response.title, data.response.text);
        }
        if(data.status == 500){
             docoNotification('error', data.response.title, data.response.text);
        }
    });
}
</script>


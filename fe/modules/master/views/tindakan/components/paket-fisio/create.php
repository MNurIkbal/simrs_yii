<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Tindakan Ruangan
 * @copyright 26 April 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\modules\master\models\CaraBayarForm;
use Doco\master\controllers\CaraBayarController;
use kartik\widgets\ActiveForm;


// $this->title = $title;

?>

<style>
    .divider-vertical {
        height: 100px;                   /* any height */
        border-left: 1px solid gray;     /* right or left is the same */
        float: left;                     /* so BS grid doesn't break */
        opacity: 0.5;                    /* optional */
        margin: 0 15px;                  /* optional */
    }
</style>

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
                            'action' => '/master/tindakan/simpan-paket',
                            'data-options' => 'click',
                            'form-id' => 'tindakan-paket-form',
                            'data-render' => 'paket',
                            'data-tab' => 'tab-paket',
                            'data-target' => '#view-paket',
                            'id' => 'btn-save'
                        ]
                    ],
                    'kembali' => [
                        'title' => \Yii::t('fe', 'Kembali'),
                        'icon' => 'fa fa-arrow-left',
                        'attributes' => [
                            'class' => 'spa',
                            'data-options' => 'click',
                            'data-render' => 'paket',
                            'data-tab' => 'tab-paket',
                            'data-target' => '#view-paket',
                        ]
                    ],
                ], '#table-tindakan-ruangan');
                ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <h3><strong><?= Yii::t('fe', "Tambah Paket Tindakan") ?></strong></h3>
                    </div>
                </div>
                <?php
                $form = ActiveForm::begin([
                    'id' => 'tindakan-paket-form',
                    // 'action' => 'tindakan/simpan-paket',
                    'enableAjaxValidation' => false,
                    'enableClientValidation' => false,
                        // 'type' => ActiveForm::TYPE_INLINE,
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => [
                        'labelSpan' => 3,
                        'deviceSize' => ActiveForm::SIZE_SMALL
                    ],
                    'options' => [
                        'role' => 'form',
                        'enctype' => 'multipart/form-data'
                    ]
                ]);
                ?>
                   
                <?= $form->field($model, 'tipepaket_kode', ['labelOptions' => ['class' => 'text-left']])->textInput([ 'class' => 'form-control input-sm kode_unique'])->label(Yii::t('fe', "Kode Paket")); ?>
                <?= $form->field($model, 'tipepaket_nama', ['labelOptions' => ['class' => 'text-left']])->textInput([ 'class' => 'form-control input-sm'])->label(Yii::t('fe', "Nama Paket")); ?>
                <?= $form->field($model, 'tipepaket_namalainnya', ['labelOptions' => ['class' => 'text-left']])->textInput([ 'class' => 'form-control input-sm'])->label(Yii::t('fe', "Nama Lainnya")); ?>
                <?php $model->is_active = true; ?>
                <?= $form->field($model, 'is_active', ['labelOptions' => ['class' => 'text-left']])->checkbox(['class'=>'pull-left','label'=> Yii::t('fe', "Aktif")])->label(Yii::t('fe', "Status")); ?>
                
                <?= $form->field($model, 'keterangan_tipepaket', ['labelOptions' => ['class' => 'text-left']])->textArea([ 'class' => 'form-control input-sm'])->label(Yii::t('fe', "Catatan")); ?>

                <?= $form->field($model, 'is_mcu', ['labelOptions' => ['class' => 'text-left']])
                    ->checkBox()->label(Yii::t('fe', "Paket MCU ?")); ?>
                <hr/>

                <div class="form-group" style="margin-bottom:30px;">
                    <label class="text-left col-sm-3">Tambah Tindakan</label>
                    <div class="col-sm-4 auto-ruangan">
                        <select id="auto-ruangan"></select>
                    </div>
                    <div class="col-sm-4">
                        <select id="auto-tindakan"></select>
                    </div>
                </div>

                 <div class="row">
                    <div class="col-md-12 tabel-tindakan" style="margin-top:30px;">
                        <table id="tabel-tampung-tindakan" class="table table-striped table-condensed table-hover" style="width:100%;">
                            <thead>
                                <tr class="bg-inverse">
                                    <th>No</th>
                                    <th>Tindakan</th>
                                    <th>Kelompok</th>
                                    <th>Hapus</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="col-md-12 tabel-tindakan-ruangan" style="margin-top:30px;">
                        <table id="tabel-tampung-tindakan-ruangan" class="table table-striped table-condensed table-hover" style="width:100%;">
                            <thead>
                                <tr class="bg-inverse">
                                    <th>No</th>
                                    <th>Tindakan</th>
                                    <th>Kelompok</th>
                                    <th>Instalasi/Ruangan</th>
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
var tabel_ruangan;

$(document).ready(function(){
    $(".auto-ruangan").hide();
    $(".tabel-tindakan-ruangan").hide();
    $('#auto-ruangan').docoPaginationSelec2(
        config = {
            placeholder : '-- Pilih Ruangan --',
            _api : '/master/tindakan/instalasi-ruangan'
        }
    );

    $('#auto-tindakan').select2({
        minimumInputLength: 3,
        placeholder: 'Tambah Tindakan',
        ajax: {
            url: "<?= Url::home().(Yii::$app->controller->module->id).'/tindakan/get-tindakan' ?>",
            data: function (params) {
            var query = {
                search: params.term,
                ruangan_id: $('#auto-ruangan').val(),
                is_mcu: ($('#tipepaketform-is_mcu').is(':checked')) ? 1 : 0,
                type: 'public'
            }
            return query;
            }
        }
    });
    $("#tipepaketform-tipepaket_nama").on("change", function(){
        var nama = $(this).val();
        var nama_lainnya = $("#tipepaketform-tipepaket_namalainnya").val();
        if(nama_lainnya == '') {
            $("#tipepaketform-tipepaket_namalainnya").val(nama);
        }
        else {
            $("#tipepaketform-tipepaket_namalainnya").val(nama_lainnya);
        }
    });

    $("#tipepaketform-is_mcu").on("change", function(){
        if($(this).is(":checked")) {
            $(".auto-ruangan").show();
            $(".tabel-tindakan-ruangan").show();
            $(".tabel-tindakan").hide();
            tabel_ruangan.draw();
        }
        else {
            $(".auto-ruangan").hide();
            $(".tabel-tindakan").show();
            $(".tabel-tindakan-ruangan").hide();
            tabel.draw();
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

    tabel = $("#tabel-tampung-tindakan").docoTabel({
        filter: false,
        sorting: [[2, "asc"]],
        drawCallback: function() {
            $('.dataTables_scrollBody').scrollTop($('.dataTables_scrollBody')[0].scrollHeight);
        },
        paging:false,
        serverSide: true,
        processing: true,
        scrollY: "200px",
        scrollCollapse: true,
        ajax:'tindakan/get-cache-tindakan',
        columns: [
            {title: "No", data:"no", searchable: false, orderable: false},
            {title: "Tindakan", data: "text", searchable: false},
            {title: "Kelompok", data: "kelompoktindakan_nama", searchable: false},
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

    tabel_ruangan = $("#tabel-tampung-tindakan-ruangan").docoTabel({
        filter: false,
        sorting: [[2, "asc"]],
        // drawCallback: function() {
        //     $('.dataTables_scrollBody').scrollTop($('.dataTables_scrollBody')[0].scrollHeight);
        // },
        paging:false,
        serverSide: true,
        processing: true,
        // scrollY: "200px",
        // scrollCollapse: true,
        ajax:'tindakan/get-cache-tindakan',
        columns: [
            {title: "No", data:"no", searchable: false, orderable: false},
            {title: "Tindakan", data: "text", searchable: false},
            {title: "Kelompok", data: "kelompoktindakan_nama", searchable: false},
            {title: "Instalasi/Ruangan", data: "instalasi_ruangan", searchable: false},
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
        $.post("tindakan/cache-tindakan",{tampung_tindakan:data,status_chache:"insert"},function(data){
            var $remote = $('#auto-tindakan');
            $remote.html('').select2('data',null);
            $('#auto-ruangan').html('').select2('data',null);
            tabel.draw();
            tabel_ruangan.draw();
            if(data.status == 200){
                 docoNotification('success', data.title, data.text);
            }
            if(data.status == 500){
                 docoNotification('error', data.title, data.text);
            }
        });
    });
});

var hapusTindakan = function(id){
    $.getJSON('tindakan/hapus-cache-tindakan?id='+id,{},function(data){
        if(data.status == 200){
             docoNotification('success', data.title, data.text);
         }
         if(data.status == 500){
             docoNotification('error', data.title, data.text);
         }
        tabel.draw();
        tabel_ruangan.draw();
    });
}
</script>
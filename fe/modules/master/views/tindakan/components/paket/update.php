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
                            'action' => '/master/tindakan/update-paket?tipepaket_id='. $id_encrypt,
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
                        <h3><strong><?= Yii::t('fe', "Ubah Paket Tindakan") ?></strong></h3>
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
                   
                <?= $form->field($model, 'tipepaket_kode', ['labelOptions' => ['class' => 'text-left']])->textInput([ 'class' => 'form-control input-sm kode_unique','readonly'=>true])->label(Yii::t('fe', "Kode Paket")); ?>
                <?= $form->field($model, 'tipepaket_nama', ['labelOptions' => ['class' => 'text-left']])->textInput([ 'class' => 'form-control input-sm'])->label(Yii::t('fe', "Nama Paket")); ?>
                <?= $form->field($model, 'tipepaket_namalainnya', ['labelOptions' => ['class' => 'text-left']])->textInput([ 'class' => 'form-control input-sm'])->label(Yii::t('fe', "Nama Lainnya")); ?>
                <?php $model->is_active = true; ?>
                <?= $form->field($model, 'is_active', ['labelOptions' => ['class' => 'text-left']])->checkbox(['class'=>'pull-left','label'=> Yii::t('fe', "Aktif")])->label(Yii::t('fe', "Status Aktif")); ?>
                
                <?= $form->field($model, 'keterangan_tipepaket', ['labelOptions' => ['class' => 'text-left']])->textArea([ 'class' => 'form-control input-sm'])->label(Yii::t('fe', "Catatan")); ?>
                            <hr/>
                
                 <div class="form-group" style="margin-bottom:30px;">
                        <div class="col-sm-3">
                            <label><?= Yii::t('fe', "Tambah Tindakan") ?></label>
                        </div>
                        <div class="col-sm-9">
                            <select id="auto-tindakan"></select>
                        </div>
                </div>
                
                
                 <div class="row">
                    <div class="col-md-12" style="margin-top:30px;">
                           
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
                 </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<script>
var tabel;
var id_enkrip = "<?=$id_encrypt?>";

$(document).ready(function(){

    $('#auto-tindakan').select2({
        minimumInputLength: 3,
        placeholder: 'Tambah Tindakan',
        ajax: {
            url: "<?= Url::home().(Yii::$app->controller->module->id).'/tindakan/get-tindakan' ?>",
            data: function (params) {
            var query = {
                search: params.term,
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
        ajax:'tindakan/get-cache-tindakan?tipepaket_id='+id_enkrip,
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

     $('#auto-tindakan').on('select2:select', function (e) {
        var data = e.params.data;
        console.log(data);
        tampung_tindakan.data.push(data);
        
        var recordTotal = tampung_tindakan.recordsTotal;
        tampung_tindakan.recordsTotal = parseInt(recordTotal) + 1;
    
        $.post("tindakan/cache-tindakan",{tampung_tindakan:data,status_chache:"insert"},function(data){
            var $remote = $('#auto-tindakan');
            $remote.html('').select2('data',null);
            tabel.draw();
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
            // console.log($('#tabel-tampung-tindakan')[0].scrollHeight);
             if(data.status == 200){
                 docoNotification('success', data.title, data.text);
             }
             if(data.status == 500){
                 docoNotification('error', data.title, data.text);
             }
            tabel.draw();
        });
    }

var hapusTindakanPaket = function(daftartindakan_id,tipepaket_id){
    // alert(daftartindakan_id+" "+tipepaket_id);
     $.post("tindakan/cache-tindakan",{daftartindakan_id:daftartindakan_id,tipepaket_id:tipepaket_id,status_chache:"delete"},function(data){
            // console.log(data);
            tabel.draw();
            // console.log($('#tabel-tampung-tindakan')[0].scrollHeight);
             if(data.status == 200){
                 docoNotification('success', data.title, data.text);
             }
             if(data.status == 500){
                 docoNotification('error', data.title, data.text);
             }
        });
    // console.log(daftartindakan_id+" "+tipepaket_id);
}


</script>
<?php
// $this->registerJs($this->render('js/tindakan-ruangan.js'), View::POS_END);
?>
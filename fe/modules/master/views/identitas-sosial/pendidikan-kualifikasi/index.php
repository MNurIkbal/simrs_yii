<?php
// Author : Ramdhan Nurrachman
// Modify : Naufal Ziyad L - 11/01/2018 ::: 14:53

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', 'Pendidikankualifikasi');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form-pendidikan-kualifikasi'
                            ]
                        ],
                        'add' => [
                            'attributes' => [
                                'id' => 'btn-add-pendidikan-kualifikasi',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => Url::home().'master/identitas-sosial/create-pendidikan-kualifikasi',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-url' => Url::home().'master/identitas-sosial/edit-pendidikan-kualifikasi?id=',
                                'id' => 'btn-edit-pendidikan-kualifikasi',
                                'data-options' => 'modal',
                                'data-target' => '#modal_backdrop',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'data-additional' => 'data-rm',
                                'id' => 'btn-delete-pendidikan-kualifikasi',
                                'data-target' => Url::home().'master/identitas-sosial/delete-pendidikan-kualifikasi?id=',
                            ]
                        ],
                        'pdf' => [
                            'attributes' => [
                                'data-target' => Url::home().'master/identitas-sosial/export-pdf-pendidikan-kualifikasi?',
                                'id' => 'btn-pdf-pendidikan-kualifikasi',
                            ]
                        ],
                        'excel' => [
                            'attributes' => [
                                'data-target' => Url::home().'master/identitas-sosial/export-excel-pendidikan-kualifikasi?',
                                'id' => 'btn-excel-pendidikan-kualifikasi',
                            ]
                        ],
                    ],'#table-pendidikan-kualifikasi');?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form-pendidikan-kualifikasi"></div>
                </div>
                <div class="form-group">
                </div>
                <table id="table-pendidikan-kualifikasi" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="5%"></th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Pendidikan");?></th>
                            <th><?=\Yii::t("fe", "Kelompok Pegawai");?></th>
                            <th><?=\Yii::t("fe", "Kode Pendidikan Kualifikasi");?></th>
                            <th><?=\Yii::t("fe", "Nama Pendidikan Kualifikasi");?></th>
                            <th><?=\Yii::t("fe", "Nama Lainnya");?></th>
                            <th><?=\Yii::t("fe", "Jumlah Kebutuhan Laki-laki");?></th>
                            <th><?=\Yii::t("fe", "Jumlah Kebutuhan Perempuan");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="8">Data tidak ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
    </div>
</div>
<div id="modal_pend_kualifikasi" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        </div>
    </div>
</div>

<script>
    var tablePendidikanKualifikasi = $('#table-pendidikan-kualifikasi').docoTabel({
        filter: true,
        columnDefs: [ {
            orderable: false,
            className: 'select-checkbox',
            targets: 0,
            checkboxes: {
                selectRow: true
            }
        }],
        select: {
            style: 'os',
            selector: 'tr'
        },
        sorting: [[3, 'asc']],
        displayLength: 10,
        processing: true,
        serverSide: true,
        // scrollX: true,
        ajax: baseUrl+"master/identitas-sosial/get-data-pendidikan-kualifikasi",
        columns: [
            {
                title: '',
                data: null,
                defaultContent: '',
                searchable: false,
                orderable: false
            },
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },            
            {title: "<?=Yii::t('fe', 'Pendidikan')?>",  data: "pendidikan_nama"},
            {title: "<?=Yii::t('fe', 'Kelompok Pegawai')?>", data: "kelompokpegawai_id"},
            {title: "<?=Yii::t('fe', 'Kode Pendidikan Kualifikasi')?>", data: "pendkualifikasi_kode"},
            {title: "<?=Yii::t('fe', 'Nama Pendidikan Kualifikasi')?>", data: "pendkualifikasi_nama"},
            {title: "<?=Yii::t('fe', 'Nama Lainnya')?>", data: "pendkualifikasi_namalainnya"},
            {title: "<?=Yii::t('fe', 'Jumlah Kebutuhan Laki-laki')?>", data: "jmlkeblaki"},
            {title: "<?=Yii::t('fe', 'Jumlah Kebutuhan Perempuan')?>", data: "jmlkebperempuan"},
            {title: "Status",  data: "status"},
        ]
        });
     $(".dataTables_filter").hide();
     $(".filter-form-pendidikan-kualifikasi").datatableBootstrapFilter(tablePendidikanKualifikasi, 
            [
                [2, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('pendidikan_nama', '', $ddl_pendidikan, ['class' => 'select2', 'prompt' => \Yii::t('fe', 'Pilih')])));?>'],
                [3, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('kelompokpegawai_id', '', $ddl_pegawai, ['class' => 'select2', 'prompt' => \Yii::t('fe', 'Pilih')])));?>'],
                [4, '<?=(preg_replace("/[\n\t\r]/i", '', Html::textInput('pendkualifikasi_kode', '', ['class' => 'form-control','placeholder'=>'Kode Pendidikan Kualifikasi'])));?>'],
                [5, '<?=(preg_replace("/[\n\t\r]/i", '', Html::textInput('pendkualifikasi_nama', '', ['class' => 'form-control','placeholder'=>'Nama Pendidikan Kualifikasi'])));?>'],
                [6, '<?=(preg_replace("/[\n\t\r]/i", '', Html::textInput('pendkualifikasi_namalainnya', '', ['class' => 'form-control','placeholder'=>'Nama Lainnya'])));?>'],
                [7, '<?=(preg_replace("/[\n\t\r]/i", '', Html::textInput('jmlkeblaki', '', ['class' => 'form-control','placeholder'=>'Jumlah Kebutuhan Laki-laki'])));?>'],
                [8, '<?=(preg_replace("/[\n\t\r]/i", '', Html::textInput('jmlkebperempuan', '', ['class' => 'form-control','placeholder'=>'Jumlah Kebutuhan Perempuan'])));?>'],
                [9, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'select2', 'prompt' => \Yii::t('fe', 'Pilih')])));?>']
            ]
        );

    $("#table-pendidikan-kualifikasi tbody").on("click", "tr", function(){
        try {
            primaryKey = tablePendidikanKualifikasi.row(".selected").data().primary ? tablePendidikanKualifikasi.row(".selected").data().primary : null;
        } catch (e) {
            primaryKey = false;
        }
        // alert(primaryKey);
        if (primaryKey) {
            $("#btn-edit-pendidikan-kualifikasi").attr("action",$("#btn-edit-pendidikan-kualifikasi").data("url")+primaryKey);
            $("#btn-delete-pendidikan-kualifikasi").attr("action",$("#btn-delete-pendidikan-kualifikasi").data("target")+primaryKey);
        } else {
            $("#btn-edit-pendidikan-kualifikasi").removeAttr("action");
            $("#btn-delete-pendidikan-kualifikasi").removeAttr("action");
        }
    });

    // Event Delete
    $(document).on("click", ".data-delete-pendidikan-kualifikasi", function(e) {
        e.preventDefault();
        $(this).docoForm("delete",{
            success : function (data) {
                tablePendidikanKualifikasi.draw()
            }
        });
        return false;
    });

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        tablePendidikanKualifikasi.draw();
    });
</script>
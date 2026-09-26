<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 *
 * Modal Form Edit Tanggal Rujukan
 */


use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;
use kartik\widgets\DatePicker;

?>
<style>
    .datepicker>div{
        display:block;
    }
    .datepicker>div{
        display:block;
    }
</style>


<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><strong><?= $title ?></strong></h5>
</div>

<?php
    $id = DocoHelpers::encrypt($id);
    $form = ActiveForm::begin([
        'id' => 'edit-tanggal-form',
        'type' => ActiveForm::TYPE_VERTICAL,
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
        'formConfig' => [
            'labelSpan' => 4,
            'deviceSize' => ActiveForm::SIZE_SMALL
        ],
        'options' => [
            'role' => 'form',
            'data-id' => $id
        ]
    ]);
?>

<div class="modal-body">
    <div class="col-md-12">
        <div class="panel panel-default panel-bordered">
            <div class="panel-heading">
                <h6 class="panel-title">Detail Informasi Pasien</h6>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-4">
                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Nama Pasien") ?></b>
                        <br>
                        <p><b><?= $body['response']['nama_pasien'].' - '.$body['response']['no_rekam_medik'] ?></b></p>
                    </div>
                    <div class="col-xs-4">
                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Kelas Pelayanan") ?></b>
                        <br>
                        <p><b><?= $body['response']['kelaspelayanan_nama'].' - '.$body['response']['carabayar_nama'].' - '.$body['response']['penjamin_nama'] ?></b></p>
                    </div>
                    <div class="col-xs-4">
                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Pendaftaran") ?></b>
                        <br>
                        <p><b><?= $body['response']['no_pendaftaran'].' - '.DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($body['response']['tgl_pendaftaran'])),false,false) ?></b></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="panel panel-default panel-bordered">
            <div class="panel-heading">
                <h6 class="panel-title"><?= $subTitle ?></h6>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-3">
                        <?php $modelOrder->tgl_rujukan = date('d-M-Y'); ?>
                        <?= $form->field($modelOrder, 'tgl_rujukan', [
                        'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-4 text-bold',
                                'wrapper' => 'col-md-6'
                            ]
                        ])->widget(DatePicker::classname(), [
                            'name' => 'date_12',
                            'value' => date('Y-m-d'),
                            'readonly' => true,
                            'language' => 'en',
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'dd-M-yyyy',
                                'startDate' => $modelOrder->tgl_rujukan,
                            ]
                        ]); ?>
                    </div>
                    <div class="col-md-12">
                        <table id="tb-rencana-pemeriksaan-rad" class="table table-striped table-condensed table-hover" style="width:100%">
                            <thead>
                                <tr class="bg-inverse">
                                    <th><?= Yii::t('fe', 'No') ?></th>
                                    <th><?= Yii::t("fe", "Jenis Pemeriksaan") ?></th>
                                    <th><?= Yii::t("fe", "Nama Pemeriksaan") ?></th>
                                    <th><?= Yii::t("fe", "Qty") ?></th>
                                    <th><?= Yii::t("fe", "Cyto") ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- body table -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <?= Html::submitButton("<i class='fa fa-pencil-square-o'></i> ". Yii::t('fe', 'Update'), ['class' => 'btn bg-teal btn-sm']) ?>
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
</div>

<?php ActiveForm::end(); ?>

<script type="text/javascript">
$(function () {
    let id = $('#edit-tanggal-form').data('id');
    console.log(id)
    tablePenunajang = $("#tb-rencana-pemeriksaan-rad").docoTabel({
        select: {
            style: "os",
            selector: "tr"
        },
        filter: true,
        sorting: [[1, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl + "radiologi/inf-pasien-rujukan-rad/get-data-pemeriksaan?id="+id,
        columns: [
            {
                title: "No", 
                data: "rowNum", 
                searchable: false, 
                orderable: false
            },
            {
                title: "Jenis Pemeriksaan", 
                data: "jenispemeriksaanrad_nama", 
                searchable: false 
            },
            {
                title: "Nama Pemeriksaan", 
                data: "daftartindakan_nama", 
                searchable: false 
            },
            {
                title: "Qty", 
                data: "qtypermintaan", 
                searchable: false
            },
            {
                title: "    Cyto", 
                data: "is_checkbox", 
                searchable: false 
            },
        ],
        scrollCollapse: true,
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

    // Hide datatables filter form
    $(".dataTables_filter").hide();
})
$("#edit-tanggal-form").docoForm("submit", {
    success : function(data) {
        table.draw()
    }
});
</script>


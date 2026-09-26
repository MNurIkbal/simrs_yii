<?php
/**
 * @author Budi
 * Powered by Sirs
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\components\DocoHelpers;
use yii\helpers\Url;
use kartik\widgets\DatePicker;
use kartik\widgets\ActiveForm;
?>

<style>
    .datepicker>div{
        display:block;
    }
</style>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>

<div class="modal-body">
    <div class="col-md-12">
        <div class="panel panel-default panel-bordered">
            <div class="panel-heading">
                <h6 class="panel-title"><?= $title ?></h6>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                            $form = ActiveForm::begin([
                                'id' => 'invoice-form',
                                'enableAjaxValidation'=>false,
                                'enableClientValidation'=>false,
                                'type' => ActiveForm::TYPE_VERTICAL,
                                'formConfig' => [
                                    'labelSpan' => 3,
                                    'deviceSize' => ActiveForm::SIZE_SMALL
                                ],
                            ]);
                        ?>
                        <div class="row">
                            <div class="col-md-4">
                                <?= $form->field($modelHeader, 'tgl_invoicegabung')->widget(DatePicker::classname(), [
                                    'options' => ['placeholder' => 'Tanggal Invoice '],
                                    'language' => 'en',
                                    'pluginOptions' => [
                                        'autoclose' => true,
                                        'format' => 'dd-M-yyyy',
                                        'todayHighlight' => true,
                                        'endDate' => "0d"
                                    ]
                                ]); ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'pasien_id')
                                    ->dropDownList([], ['id' => 'pasien_id', 'class' => 'select2'])
                                ->label(Yii::t('fe', 'Nama Pasien / No. RM')); ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'pendaftaran_id')
                                    ->dropDownList([], ['id' => 'pendaftaran_id', 'class' => 'select2', 'multiple' => 'multiple']); ?>
                            </div>
                        </div>
                        <div class="row" style="margin-top:15px;">
                            <div class="col-md-4">
                                <?= $form->field($modelHeader, 'tgl_invoicegabung_cetak')->widget(DatePicker::classname(), [
                                    'options' => ['placeholder' => 'Tanggal Invoice'],
                                    'language' => 'en',
                                    'pluginOptions' => [
                                        'autoclose' => true,
                                        'format' => 'dd-M-yyyy',
                                        'todayHighlight' => true,
                                        'endDate' => "0d"
                                    ]
                                ]); ?>
                            </div>
                            <div class="col-md-4">
                                    <?= $form->field($modelHeader, 'penjamin_id_cetak')
                                        ->dropDownList([], ['id' => 'penjamin_id_cetak', 'class' => 'select2']); ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($modelHeader, 'pendaftaran_id_cetak')
                                    ->dropDownList([], ['id' => 'pendaftaran_id_cetak', 'class' => 'select2']); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4" style="margin-top:25px;">
                            <b>TOTAL INVOICE</b> <span id="total_invoice" style="font-weight:bold;font-size:14px;"> 0</span>
                            </div>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <table 
                            class="table datatable-basic table-hover dataTable no-footer"
                            id="table-invoice"
                            style="width: 100%;"
                        >
                            <thead>
                                <tr class="bg-inverse">
                                    <th><?=Yii::t('fe', 'No'); ?></th>
                                    <th><?=Yii::t('fe', 'No Pendaftaran'); ?></th>
                                    <th><?=Yii::t('fe', 'No Invoice'); ?></th>
                                    <th><?=Yii::t('fe', 'Pasien'); ?></th>
                                    <th><?=Yii::t('fe', 'Penjamin'); ?></th>
                                    <th><?=Yii::t('fe', 'Tanggal Invoice'); ?></th>
                                    <th><?=Yii::t('fe', 'Nilai Invoice'); ?></th>
                                    <th><?=Yii::t('fe', 'Pilih'); ?></th>
                                </tr>    
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center" colspan="7"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <?= Html::button("<i class='fa fa-floppy-o'></i> " . Yii::t('fe', 'Simpan'), ['class' => 'btn bg-teal', 'id' => 'btn-submit-invoice']) ?>
    <?= Html::button("<i class='fa fa-arrow-left'></i> ". Yii::t('fe', 'Kembali'),[
        'class' => 'btn bg-slate btn-sm',
        'data-dismiss' => 'modal'
    ]) ?>
</div>

<script type="text/javascript">
$(document).ready(function(){

   $("#pendaftaran_id").attr("disabled",true) //init on load
   $("#pendaftaran_id_cetak").attr("disabled",true) //init on load
   $("#penjamin_id_cetak").attr("disabled",true) //init on load

    var tableInvoice;   
    tableInvoice = $("#table-invoice").docoTabel({
        filter: false,
        // sorting: [[4, "desc"]],
        paging: false,
        info: false,
        processing: true,
        serverSide: true,
        scrollX: true,
        scrollCollapse: true,
        select: {
            // style: 'single',
            info: false,
            // selector: 'td:not(:last-child)'
        },
        ajax: baseUrl + "kasir/inf-gabung-invoice/get-data-invoice?",
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                render: (data, rowElement, rowData, rowAdditionalData) => {
                    var tableInfo = tableInvoice.page.info()
                    return tableInfo.start + rowAdditionalData.row + 1
                }
            },
            {
                data: "no_pendaftaran", 
                searchable: false,
                orderable: false,
            },
            {
                data: "no_invoice", 
                searchable: false,
                orderable: false,
            },
            {
                data: "nama_pasien", 
                searchable: false,
                orderable: false,
            },
            {
                data: "penjamin_nama", 
                searchable: false,
                orderable: false,
            },
            {
                data: "tgl_invoicegabung", 
                searchable: false,
                orderable: false,
            },
            {
                data: "total_invoice",
                searchable: false,
                orderable: false,
                className: "text-right",
            },
            {
                title : "<input type='checkbox' id='checkAll'></input>",
                data: "aksi", 
                searchable: false,
                orderable: false,
                className: "text-center",
            },
        ],
        footerCallback: function(row, data, start, end, display) {
         var api = this.api(), data;
         var intVal = function ( i ) {
            return typeof i === "string" ?
            i.replace(/[\$,]/g, "")*1 :
            typeof i === "number" ?
               i : 0;
         };

         var subTotal = api
            .column( 5, { page: "current"} )
            .data()
            .reduce( function (a, b) {
               return intVal(a) + intVal(b);
            }, 0 );
         
         $("#total_invoice").html("Rp. " + docoHelper.convertToRupiah(Math.ceil(subTotal)));
        },
    });
    $(".dataTables_filter").hide();
    $("#pasien_id").select2InfinityScroll({
        url: "/kasir/inf-gabung-invoice/filters?type=no_rekam_medik",
        callbackData: (param) => {
            return {
                payload: {
                    ...param,
                }
            }
        }
    });
    $("#pendaftaran_id").select2InfinityScroll({
        url: "/kasir/inf-gabung-invoice/filters?type=no_pendaftaran",
        callbackData: (param) => {
            return {
                payload: {
                    ...param,
                    pasien_id: $("#pasien_id").val()
                }
            }
        }
    })
    $("#pendaftaran_id").on('change', function(){
        var pasien_id = '';
        var no_rekam_medik = '';
        var nama_pasien = '';
        var no_pendaftaran = '';
        var dataPasien = $("#pasien_id").select2("data");
        var dataPendaftaran = $("#pendaftaran_id").select2("data")
        if(dataPasien.length != 0) {
            no_rekam_medik = (dataPasien[0] != 'undefined') ?  dataPasien[0].no_rekam_medik : '';
            nama_pasien = (dataPasien[0] != 'undefined') ?  dataPasien[0].nama_pasien : '';
            pasien_id = (dataPasien[0] != 'undefined') ?  dataPasien[0].id : '';
        }
        if(dataPendaftaran.length != 0) {
            no_pendaftaran = (dataPendaftaran[0] != 'undefined') ?  dataPendaftaran[0].no_pendaftaran : '';
            no_rekam_medik = (dataPendaftaran[0] != 'undefined') ?  dataPendaftaran[0].no_rekam_medik : '';
            nama_pasien = (dataPendaftaran[0] != 'undefined') ?  dataPendaftaran[0].nama_pasien : '';
            pasien_id = (dataPendaftaran[0] != 'undefined') ?  dataPendaftaran[0].pasien_id : '';
        }
        var _data = {
            InvoiceGabungDetailForm: {
                pasien_id: pasien_id,
                pendaftaran_id: $("#pendaftaran_id").val(),
                no_rekam_medik: no_rekam_medik,
                nama_pasien: nama_pasien,
                no_pendaftaran: no_pendaftaran,
            }
        }
        $().docoForm("click",{
            url : "/kasir/inf-gabung-invoice/tambah-invoice",
            method : "POST",
            type : "json",
            data : _data,
            skipConfirm: true,
            skipSuccessNotif: true,
            success : function (data) {
                tableInvoice.draw();
            }
        });
        
        $("#pendaftaran_id_cetak").attr("disabled",false) 
        $("#penjamin_id_cetak").attr("disabled",false) 
        
        $("#pendaftaran_id_cetak").empty();
        $("#penjamin_id_cetak").empty();
    });
    $("#no_invoicegabung").select2InfinityScroll({
        url: "/kasir/inf-gabung-invoice/filters?type=no_invoice",
        callbackData: (param) => {
            return {
                payload: {
                    ...param,
                    pendaftaran_id: $("#no_pendaftaran").val()
                }
            }
        }
    })
    $('#btn-submit-invoice').unbind();
    $('#btn-submit-invoice').bind('click', function(e){
        e.preventDefault();
        var _form = $("#invoice-form").serializeArray();
        const detailInvoice = []
        let valid = 1
        var arrInvoice = [];
        $(".check-invoice:checked").each(function () {
            var pembayaranId = $(this).attr("data-value");
            arrInvoice.push(pembayaranId);
        })
        var countChecked = arrInvoice.length;
        if (countChecked == 0) {
            valid = 0
            docoNotification('error', 'Proses Gagal.', 'Belum ada data yang di Pilih!')
        }
        else if(countChecked == 1) {
            valid = 0
            docoNotification('error', 'Proses Gagal.', 'Gabung Invoice harus lebih dari satu Invoice')
        }
        _form.push({
            name: "detail",
            value: arrInvoice,
        });
        if (valid == 1) {
            $().docoForm("click",{
                url : "/kasir/inf-gabung-invoice/simpan-invoice",
                method : "POST",
                type : "json",
                data: _form,
                success : function (data) {
                    table.draw();
                    $('#modal_backdrop').modal('toggle');
                }
            });
        }
    });

    $('#table-invoice').on('click', 'tbody td, thead th:first-child', function(e){
        $(this).parent().find('input[type="checkbox"]').trigger('click');
    });

    $(document).on('click', '.check-invoice', function(){
        var _total = 0;
        var intVal = function ( i ) {
            return typeof i === "string" ?
            i.replace(/[\$,]/g, "")*1 :
            typeof i === "number" ?
               i : 0;
        };
        $(".check-invoice:checked").each(function () {
            var harga = $(this).attr("data-harga");
            _total += intVal(harga);

        })
        $("#total_invoice").html("Rp. " + docoHelper.convertToRupiah(_total));

        if(!$(this).is(":checked")){
            $("#checkAll").prop('checked', false);
        }

        if($(".check-invoice").length == $(".check-invoice:checked").length){
            $("#checkAll").prop('checked', true);
        }

    });

    $("#pasien_id").on("change", function(){
        $("#pendaftaran_id").attr("disabled",false)

        $("#pendaftaran_id").empty();
        $("#pendaftaran_id_cetak").empty();
        $("#penjamin_id_cetak").empty();
        $(".has-error" ).removeClass()
        $(".help-block" ).remove()
        $(".help-block error" ).remove()
        $("#checkAll").prop('checked', false);
        
        $().docoForm("click",{
            url : "/kasir/inf-gabung-invoice/clear-invoice-cache",
            method : "POST",
            type : "json",
            data : {},
            skipConfirm: true,
            skipSuccessNotif: true,
            success : function (data) {
                tableInvoice.draw();
            }
        });
    })
   
    $("#pendaftaran_id_cetak").select2InfinityScroll({
        url: "/kasir/inf-gabung-invoice/filters?type=no_pendaftaran",
        callbackData: (param) => {
            return {
                payload: {
                    ...param,
                    pasien_id: $("#pasien_id").val(),
                    pendaftaran_id: $("#pendaftaran_id").val()
                }
            }
        }
    })
   
    $("#penjamin_id_cetak").select2InfinityScroll({
        url: "/kasir/inf-gabung-invoice/filters?type=penjamin",
        callbackData: (param) => {
            return {
                payload: {
                    ...param,
                    pendaftaran_id: $("#pendaftaran_id").val()
                }
            }
        }
    })

    $("#checkAll").on("click", function(){
        if($("#checkAll").is(":checked")){
            $(".check-invoice").prop('checked', false);
        }else{
            $(".check-invoice").prop('checked', true);
        }
        $(".check-invoice").trigger('click')
    })

})
</script>

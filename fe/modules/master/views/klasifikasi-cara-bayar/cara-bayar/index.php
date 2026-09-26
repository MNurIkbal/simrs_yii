<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = \Yii::t('fe', 'Cara bayar');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel-heading">
           <h3 class="panel-title"><b><?=$this->title;?></b></h3>
        </div>
        <div class="panel-toolbar clearfix">
            <?=DocoHelpers::generateToolbar([
                'search',
                'reset'=> [
                    'attributes'=>[
                        'data-parent' => '.filter-form'
                    ]
                ],
                'add' => [
                    'attributes' => [
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'action' => 'cara-bayar/create',
                    ]
                ],
                'edit' => [
                    'attributes' => [
                        'data-options' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'data-url' => 'cara-bayar/update?id=',
                    ]
                ],
                'delete' => [
                    'attributes' => [
                        'data-additional' => 'data-rm',
                        'id' => 'btn-delete',
                        'data-target' => Url::home().'master/cara-bayar/delete?id=',
                    ]
                ],
                'pdf' => [
                    'title' => Yii::t('fe', 'Export Pdf'),
                    'attributes'=>[
                        'id' => 'cetak-detail-pdf',
                        'data-target'=> Url::home().'master/cara-bayar/export-pdf?',
                    ]
                ],
                'excel' => [
                    'attributes' => [
                        'data-target' => Url::home().'master/cara-bayar/export-excel?',
                        'id' => 'btn-excel',
                    ]
                ],
            ],'#table-carabayar');?>    
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12 filter-form"></div>
            </div>
            <table id="table-carabayar" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th width="5%"></th>
                        <th><?= Yii::t("fe", "No") ?></th>
                        <th><?=\Yii::t("fe", "Nama");?></th>
                        <th><?=\Yii::t("fe", "Nama lainnya");?></th>
                        <th><?=\Yii::t("fe", "Metode pembayaran");?></th>
                        <th><?=\Yii::t("fe", "Subsidi asuransi");?></th>
                        <th><?=\Yii::t("fe", "Subsidi pemerintah");?></th>
                        <th><?=\Yii::t("fe", "Subsidi rs");?></th>
                        <th><?= Yii::t("fe", "Status") ?></th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modal-cara-bayar" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content"></div>
    </div>
</div>

<script>
    var table = $('#table-carabayar').docoTabel({
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
            sorting: [[2, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            // scrollX: true,
            ajax: baseUrl+"master/klasifikasi-cara-bayar/get-data-cara-bayar",
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
                {title: "<?=Yii::t('fe', 'Nama')?>", data: "carabayar_nama"},
                {title: "<?=Yii::t('fe', 'Nama Lainnya')?>", data: "carabayar_namalainnya"},
                {title: "<?=Yii::t('fe', 'Metode Pembayaran')?>",  data: "metode_pembayaran_nama",name:"metode_pembayaran"},
                {title: "<?=Yii::t('fe', 'Subsidi Asuransi')?>", data: "is_subsidiasuransi", searchable: false},
                {title: "<?=Yii::t('fe', 'Subsidi Pemerintah')?>", data: "is_subsidipemerintah", searchable: false},
                {title: "<?=Yii::t('fe', 'Subsidi Rumah Sakit')?>", data: "is_subsidirs", searchable: false},
                {title: "Status", data: "is_active"}
            ],
            scrollCollapse: true,
        });

     $(".dataTables_filter").hide();
     $(".filter-form").datatableBootstrapFilter(table,[
                [2, '<?=(preg_replace("/[\n\t\r]/i", '', Html::textInput('carabayar_nama', '', ['class' => 'form-control','placeholder'=>'Nama'])));?>'],
                [3, '<?=(preg_replace("/[\n\t\r]/i", '', Html::textInput('carabayar_namalainnya', '', ['class' => 'form-control','placeholder'=>'Nama Lainnya'])));?>'],
                [4, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('metode_pembayaran_nama', '', $metode_bayar, ['class' => 'select2', 'prompt' => \Yii::t('fe', '--Pilih--')])));?>'],
                [8, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'select2', 'prompt' => \Yii::t('fe', '--Pilih--')])));?>']
            ]
        );

    // On click tr
    $("#table-carabayar tbody").on("click", "tr", function(){
        try {
             var primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
        } catch (e) {
            var primaryKey = false;
        }

        // Check primary
        if (primaryKey) {
            $("#btn-delete").attr("action", $("#btn-delete").data("target") + primaryKey);
        }
    });



      // Event Reload
    $(document).on("switchChange.bootstrapSwitch", ".change-status", function (e, state) {

        var dataStatus = "0";
        var dataId = $(this).attr("data-id");

        if (e.target.checked == true)
            dataStatus = "1";
        
        $(this).docoForm("delete",{
            url: baseUrl+"master/cara-bayar/change-status?id="+dataId+"&status="+dataStatus,
            confirmTitle : "<?=Yii::t('fe', 'Konfirmasi')?>",
            confirmMessage : "<?=Yii::t('fe', 'Apa anda yakin ingin mengubah status data ini ?')?>",
            success : function (data) {
                table.draw();
            }
        });
        table.draw();
    });

    // Print pdf
    // $(document).on("click", "#print-cara-bayar", function() {
    //     window.open("/master/cara-bayar/export-pdf");
    // });

    // // Excel
    // $('#excel-cara-bayar').click(function () {
    //     window.open('/master/cara-bayar/export-excel');
    // });

</script>
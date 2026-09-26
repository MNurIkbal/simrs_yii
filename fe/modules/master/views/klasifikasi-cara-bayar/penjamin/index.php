<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = \Yii::t('fe', 'Penjamin Pasien');
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
                        'data-parent' => '.filter-form-penjamin'
                    ]
                ],
                'add' => [
                    'attributes' => [
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'action' => 'penjamin/create',
                    ]
                ],
                'edit' => [
                    'attributes' => [
                        'data-options' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'data-url' => 'penjamin/update?id=',
                    ]
                ],
                'delete' => [
                    'attributes' => [
                        'data-additional' => 'data-rm',
                        'id' => 'btn-delete',
                        'data-target' => Url::home().'master/penjamin/delete?id=',
                    ]
                ],
                'pdf' => [
                    'attributes' => [
                        'data-target' => Url::home().'master/penjamin/export-pdf?',
                        'id' => 'btn-pdf',
                    ]
                ],
                'excel' => [
                    'attributes' => [
                        'data-target' => Url::home().'master/penjamin/export-excel?',
                        'id' => 'btn-excel',
                    ]
                ],
            ], '#example') ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12 filter-form-penjamin"></div>
            </div>
            <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th width="5%"></th>
                        <th><?= Yii::t("fe", "No") ?></th>
                        <th><?=\Yii::t("fe", "Cara Bayar");?></th>
                        <th><?=\Yii::t("fe", "Kode Penjamin");?></th>
                        <th><?=\Yii::t("fe", "Nama Penjamin");?></th>
                        <th><?=\Yii::t("fe", "Nama lainnya");?></th>
                        <th><?=\Yii::t("fe", "Group Margin");?></th>
                        <th><?= Yii::t("fe", "Status") ?></th>
                        <th><?= Yii::t("fe", "Tampilkan Di Mobile") ?></th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    var table2 = $('#example').docoTabel({
        columnDefs: [ {
            orderable: false,
            className: 'select-checkbox',
            targets:   0,
            width: "10%"
        }],
        select: {
            style:    'os',
            selector: 'tr'
        },
        filter: true,
        sorting: [[2, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        // rowsGroup: [2],
        // stateSave: true,
        // scrollX: true,
        ajax: baseUrl+"master/klasifikasi-cara-bayar/get-data-penjamin",
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
            {title: "<?=Yii::t('fe', 'Cara Bayar')?>", data: "carabayar_nama"},
            {title: "<?=Yii::t('fe', 'Kode Penjamin')?>", data: "penjamin_kode"},
            {title: "<?=Yii::t('fe', 'Nama Penjamin')?>", data: "penjamin_nama"},
            {title: "<?=Yii::t('fe', 'Nama Lainnya')?>",  data: "penjamin_namalainnya"},
            {title: "<?=Yii::t('fe', 'Group Margin')?>",  data: "groupmargin_nama"},
            {title: "<?=Yii::t('fe', 'Status')?>", data: "is_active"},
            {title: "<?=Yii::t('fe', 'Tampilkan Di Mobile')?>", data: "is_online"},
        ],
        scrollCollapse: true,
    });

    $(".dataTables_filter").hide();
    $(".filter-form-penjamin").datatableBootstrapFilter(table2,
        [
            [2, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('carabayar_id', '', $carabayar, ['class' => 'select2', 'prompt' => \Yii::t('fe', '--Pilih--')])));?>'],
            [3, '<?=(preg_replace("/[\n\t\r]/i", '', Html::textInput('penjamin_nama', '', ['class' => 'form-control','placeholder'=>'Nama'])));?>'],
            [4, '<?=(preg_replace("/[\n\t\r]/i", '', Html::textInput('penjamin_namalainnya', '', ['class' => 'form-control','placeholder'=>'Nama Lainnya'])));?>'],
            [5, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('groupmargin_nama', '', $margingroup, ['class' => 'select2','prompt' => \Yii::t('fe', '--Pilih--')])));?>'],
            [6, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'select2', 'prompt' => \Yii::t('fe', '--Pilih--')])));?>'],
            [7, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_online', '', $options, ['class' => 'select2', 'prompt' => \Yii::t('fe', '--Pilih--')])));?>']
        ]
    );

    // On click tr
    $("#example tbody").on("click", "tr", function(){
        try {
             var primaryKey = table2.row(".selected").data().primary ? table2.row(".selected").data().primary : null;
        } catch (e) {
            var primaryKey = false;
        }

        // Check primary
        if (primaryKey) {
            $("#btn-delete").attr("action", $("#btn-delete").data("target") + primaryKey);
        }
    });

      // Event Reload
    $(document).on("switchChange.bootstrapSwitch", ".change-status-penjamin", function (e, state) {

        var dataStatus = "0";
        var dataId = $(this).attr("data-id");

        console.log(dataId);

        if (e.target.checked == true)
            dataStatus = "1";

        $(this).docoForm("delete",{
            url: baseUrl+"master/penjamin/change-status-penjamin?id="+dataId+"&status="+dataStatus,
            confirmTitle : "<?=Yii::t('fe', 'Konfirmasi')?>",
            confirmMessage : "<?=Yii::t('fe', 'Apa anda yakin ingin mengubah status data ini ?')?>",
            success : function (data) {
                table2.draw();
            }
        });
        table2.draw();
    });

    // Print pdf
    $(document).on("click", "#print-penjamin", function() {
        window.open("/master/penjamin/export-pdf");
    });

    // Excel
    $('#excel-penjamin').click(function () {
        window.open('/master/penjamin/export-excel');
    });
</script>

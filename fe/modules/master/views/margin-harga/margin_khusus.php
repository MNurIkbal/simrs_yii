<?php

/**
 * @author : Ardi (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
$this->title = Yii::t('fe', 'Margin Khusus Rumah Sakit');
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    "search"=> [
                        'attributes'=>[
                            'id' => 'btn-find-margin-khusus',
                        ]
                    ],
                    'reset'=> [
                        'attributes'=>[
                            'id' => 'btn-reset-margin-khusus',
                            'data-parent' => '.filter-form-margin-khusus'
                        ]
                    ],
                    'add' => [
                        'attributes' => [
                            'id' => 'btn-tambah-margin-khusus',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '75%',
                            'action' => '/master/margin-harga/margin-khusus-tambah',
                        ]
                    ],
                    'edit' => [
                        'title' => Yii::t('fe', 'Ubah'),
                        'icon' => 'fa fa-pencil',
                        'attributes' => [
                            'disabled' => true,
                            'id' => 'btn-edit-margin-khusus',
                            'data-options'=>'modal',
                            'data-target'=>'#modal_backdrop',
                            'data-width' => '75%',
                            'data-url' => '/master/margin-harga/margin-khusus-update?id=',
                        ]
                    ],
                    'delete' => [
                        'attributes' => [
                            'disabled' => true,
                            'id' => 'btn-hapus-margin-khusus',
                            'data-additional' => 'data-rm',
                        ]
                    ],
                ],'#table-margin-khusus') ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form-margin-khusus"></div>
                </div>
                <table id="table-margin-khusus" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th><?= Yii::t('fe', 'No') ?></th>
                            <th><?=\Yii::t("fe", "Detail");?></th>
                            <th><?= Yii::t("fe", "Nama") ?></th>
                            <th><?= Yii::t("fe", "Tanggal Berlaku") ?></th>
                            <th><?= Yii::t("fe", "Perda / SK") ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs('
    $(document).on("keydown", null, "enter", function (event) {
        $("#btn-find-margin-khusus").click();
    });

    $(document).on("keydown", null, "alt+t", function (event) {
        $("#btn-tambah-margin-khusus").click();
    });

    $(document).on("keydown", null, "alt+u", function (event) {
        $("#btn-edit-margin-khusus").click();
    });

    $(document).on("keydown", null, "alt+u", function (event) {
        $("#btn-hapus-margin-khusus").click();
    });

    $(document).on("keydown", null, function (e) {
        if (e.key == "Enter") {
            $("#btn-find-margin-khusus").click();
        }

        if (e.key == "F7") {
            $("#btn-reset-margin-khusus").click();
            $("#btn-edit-margin-khusus").prop("disabled",false);
            $("#btn-hapus-margin-khusus").prop("disabled",false);
        }

        if (e.key == "F8") {
            $("#btn-pdf-margin-khusus").click();
        }

        if (e.key == "F9") {
            $("#btn-excel-margin-khusus").click();
        }
    });

    $(document).on("click", ".data-reload", function() {
        table.draw();
    });
    var table;
    $(document).ready(function() {
        // Generate Table
        table = $("#table-margin-khusus").docoTabel({
            createdRow: function(row, data, index) {
                if(data.active_margin) {
                    $("td", row).addClass("active-margin");
                }
            },
            filter: true,
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            sorting: [[3, "asc"], [5, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"master/margin-harga/margin-khusus-get-data",
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: "",
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false,
                    width:"1%"
                },
                {
                    title: "'.(\Yii::t("fe", "Detail")).'",
                    data: "detail",
                    searchable: false,
                    orderable: false,
                    width:"1%"
                },
                {
                    title: "'.(\Yii::t("fe", "Nama")).'",
                    data: "nama"
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Berlaku")).'",
                    data: "mulai_berlaku",
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Perda /SK")).'",
                    data: "perda"
                },
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form-margin-khusus").datatableBootstrapFilter(table, [
            [
                3, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('perda', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Nama')]))).'\'
            ]
        ]);

        $(".switch").bootstrapSwitch();
        $(document).on("switchChange.bootstrapSwitch", ".change-status", function (e, state) {
            var dataStatus = "0";
            var dataId = $(this).attr("data-id");
            if (e.target.checked == true)
                dataStatus = "1";

            $(this).docoForm("delete",{
                url: baseUrl+"master/margin-harga/change-status?id="+dataId+"&status="+dataStatus,
                confirmTitle : "'.(\Yii::t("fe", "Konfirmasi")).'",
                confirmMessage : "'.(\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")).'",
                success : function (data) {
                    table .draw();
                }
            });
            table .draw();
        });

        $("#table-margin-khusus tbody").on("click", "tr", function(){
            try {
                primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
            } catch (e) {
                primaryKey = false;
            }
            tgl_berlaku = table.row(".selected").data().tgl_berlaku ? table.row(".selected").data().tgl_berlaku : null;
            var dateNow = new Date();
            var dateBerlaku = new Date(tgl_berlaku);
           if (primaryKey) {
                $.ajax({
                    url: "/master/margin-harga/check-transaction?id=" + primaryKey,
                    type: "get",
                    beforeSend: function () {
                    },
                    success: function (res) {
                        btnEditHapus(res);
                    },
                    error: function (res) {
                        btnEditHapus(res);
                    },
                    complete: function() {
                    }
                });
            } else {
                $("#btn-edit-margin-khusus").prop("disabled",true);
                $("#btn-hapus-margin-khusus").prop("disabled",true);
                return false;
            }
        });

        $(".heading-elements").remove();
    });

    function btnEditHapus(val){
        if(val != 200){
            $("#btn-edit-margin-khusus").prop("disabled",true);
            $("#btn-hapus-margin-khusus").prop("disabled",true);
        } else {
            $("#btn-edit-margin-khusus").prop("disabled",false);
            $("#btn-hapus-margin-khusus").prop("disabled",false);
        }
    }
', View::POS_END, 'b-index');
?>

<?php

/**
 * @Author: Sigit
 * @Date:   2018-06-06 09:24:42
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2020-01-21 16:24:36
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
$this->title = Yii::t('fe', 'Group Margin');
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <div class="btn-group pull-left">
                    <?= DocoHelpers::generateToolbar([
                        "search"=> [
                            'attributes'=>[
                                'id' => 'btn-find-group-margin',
                            ]
                        ],
                        'reset'=> [
                            'attributes'=>[
                                'id' => 'btn-reset-group-margin',
                                'data-parent' => '.filter-form-group-margin'
                            ]
                        ],
                        'add' => [
                            'attributes' => [
                                'id' => 'btn-tambah-group-margin',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-width' => '50%',
                                'action' => '/master/margin-harga/group-margin-create',
                            ]
                        ],
                        'edit' => [
                            'title' => Yii::t('fe', 'Ubah'),
                            'icon' => 'fa fa-pencil',
                            'attributes' => [
                                'disabled' => false,
                                'id' => 'btn-edit-group-margin',
                                'data-options'=>'modal',
                                'data-target'=>'#modal_backdrop',
                                'data-width' => '75%',
                                'data-url' => '/master/margin-harga/update?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'disabled' => false,
                                'id' => 'btn-hapus-group-margin',
                                'data-additional' => 'data-rm',
                            ]
                        ],
                    ],'#table-group-margin') ?>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form-group-margin"></div>
                </div>
                <table id="table-group-margin" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th><?=Yii::t('fe', 'No')?></th>
                            <th><?= Yii::t("fe", "Group Margin") ?></th>
                            <th><?= Yii::t("fe", "Kode") ?></th>
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
        $("#btn-find-group-margin").click();
    });

    $(document).on("keydown", null, "alt+t", function (event) {
        $("#btn-tambah-group-margin").click();
    });

    $(document).on("keydown", null, "alt+u", function (event) {
        $("#btn-edit-group-margin").click();
    });

    $(document).on("keydown", null, "alt+u", function (event) {
        $("#btn-hapus-group-margin").click();
    });

    $(document).on("keydown", null, function (e) {
        if (e.key == "Enter") {
            $("#btn-find-group-margin").click();
        }

        if (e.key == "F7") {
            $("#btn-reset-group-margin").click();
            $("#btn-edit-group-margin").prop("disabled",false);
            $("#btn-hapus-group-margin").prop("disabled",false);
        }

        if (e.key == "F8") {
            $("#btn-pdf-group-margin").click();
        }

        if (e.key == "F9") {
            $("#btn-excel-group-margin").click();
        }
    });

    $(document).on("click", ".data-reload", function() {
        table.draw();
    });
    var table;
    $(document).ready(function() {
        // Generate Table
        table = $("#table-group-margin").docoTabel({
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
            sorting: [[2, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"master/margin-harga/group-margin-get-data",
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
                    width:"5%"
                },
                {
                    title: "'.(\Yii::t("fe", "Group Margin")).'",
                    data: "groupmargin_nama"
                },
                {
                    title: "'.(\Yii::t("fe", "Kode")).'",
                    data: "groupmargin_kode",
                    searchable: false,
                },
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form-group-margin").datatableBootstrapFilter(table, [
            [
                3, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('groupmargin_nama', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Group Margin')]))).'\'
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

        $("#table-group-margin tbody").on("click", "tr", function(){
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
                $("#btn-edit").prop("disabled",true);
                $("#btn-hapus").prop("disabled",true);
                return false;
            }
        });

        $(".heading-elements").remove();
    });

    function btnEditHapus(val){
        if(val != 200){
            $("#btn-edit").prop("disabled",true);
            $("#btn-hapus").prop("disabled",true);
        } else {
            $("#btn-edit").prop("disabled",false);
            $("#btn-hapus").prop("disabled",false);
        }
    }
    $(document).on("click",".addrow",function(event) {
        event.preventDefault();
        var _save = true;
        $.each(_jsonData, function(key, val) {
            if (val.harga_min > val.harga_max || val.harga_max == 0) {
                docoNotification("error", "Proses Gagal !", "Harga Max tidak boleh kecil dari Nilai Min.");
                $("button[data-id="+ key +"]").closest("tr").css("background","#ff000047");
                _save = false;
            }
        });
        if (_save) {
            _jsonData["row-"+counter] = {
                harga_min : 0,
                harga_max : 0,
                margin : 0,
                is_deleted : true
            };
            _generateRow(_jsonData);
            $(".harga_max").trigger("keyup");
            counter++;
        }
    });
', View::POS_END, 'b-index');
?>

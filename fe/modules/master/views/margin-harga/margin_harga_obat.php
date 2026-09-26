<?php

/**
 * @Author: Sigit
 * @Date:   2018-06-06 09:24:42
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2020-01-21 16:23:07
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-03-06 16:18:32
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use app\components\DHtml;
$this->title = Yii::t('fe', 'Margin Harga Obat');
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <?php
                    $listButton = [
                        "search"=> [
                            'attributes'=>[
                                'id' => 'btn-find-margin-harga-obat',
                            ]
                        ],
                        'reset'=> [
                            'attributes'=>[
                                'id' => 'btn-reset-margin-harga-obat',
                                'data-parent' => '.filter-form-margin-harga-obat'
                            ]
                        ],
                    ];

                    if (DHtml::cekHakAkses('create')) {
                        $listButton['add'] = [
                            'attributes' => [
                                'id' => 'btn-tambah-margin-harga-obat',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-width' => '75%',
                                'action' => '/master/margin-harga/create',
                            ]
                        ];
                    }

                    if (DHtml::cekHakAkses('update')) {
                        $listButton['edit'] = [
                            'title' => Yii::t('fe', 'Ubah'),
                            'icon' => 'fa fa-pencil',
                            'attributes' => [
                                'disabled' => true,
                                'id' => 'btn-edit-margin-harga-obat',
                                'data-options'=>'modal',
                                'data-target'=>'#modal_backdrop',
                                'data-width' => '75%',
                                'data-url' => '/master/margin-harga/update?id=',
                            ]
                        ];
                    }

                    if (DHtml::cekHakAkses('delete')) {
                        $listButton['delete'] = [
                            'attributes' => [
                                'disabled' => true,
                                'id' => 'btn-hapus-margin-harga-obat',
                                'data-additional' => 'data-rm',
                            ]
                        ];
                    }
                ?>
                <?= DocoHelpers::generateToolbar($listButton,'#table-margin-harga-obat') ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form-margin-harga-obat"></div>
                </div>
                <table id="table-margin-harga-obat" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th><?= Yii::t('fe', 'No') ?></th>
                            <th><?=\Yii::t("fe", "Detail");?></th>
                            <th><?= Yii::t("fe", "Group Margin") ?></th>
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
        $("#btn-find-margin-harga-obat").click();
    });

    $(document).on("keydown", null, "alt+t", function (event) {
        $("#btn-tambah-margin-harga-obat").click();
    });

    $(document).on("keydown", null, "alt+u", function (event) {
        $("#btn-edit-margin-harga-obat").click();
    });

    $(document).on("keydown", null, "alt+u", function (event) {
        $("#btn-hapus-margin-harga-obat").click();
    });

    $(document).on("keydown", null, function (e) {
        if (e.key == "Enter") {
            $("#btn-find-margin-harga-obat").click();
        }

        if (e.key == "F7") {
            $("#btn-reset-margin-harga-obat").click();
            $("#btn-edit-margin-harga-obat").prop("disabled",false);
            $("#btn-hapus-margin-harga-obat").prop("disabled",false);
        }

        if (e.key == "F8") {
            $("#btn-pdf-margin-harga-obat").click();
        }

        if (e.key == "F9") {
            $("#btn-excel-margin-harga-obat").click();
        }
    });

    $(document).on("click", ".data-reload", function() {
        table.draw();
    });
    var table;
    $(document).ready(function() {
        // Generate Table
        table = $("#table-margin-harga-obat").docoTabel({
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
            ajax: baseUrl+"master/margin-harga/margin-harga-obat-get-data",
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
                    title: "'.(\Yii::t("fe", "Group Margin")).'",
                    data: "groupmargin_m.groupmargin_nama"
                },
                {
                    title: "'.(\Yii::t("fe", "Nama")).'",
                    data: "nama_margin"
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Berlaku")).'",
                    data: "tgl_berlaku",
                    searchable: false,
                },
                {
                    title: "'.(\Yii::t("fe", "Perda /SK")).'",
                    data: "perda_margin"
                },
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form-margin-harga-obat").datatableBootstrapFilter(table, [
            [
                3, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('perda_margin', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Nama')]))).'\'
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

        $("#table-margin-harga-obat tbody").on("click", "tr", function(){
            try {
                primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
                $("#btn-edit-margin-harga-obat, #btn-hapus-margin-harga-obat").prop("disabled",false);

            } catch (e) {
                $("#btn-edit-margin-harga-obat, #btn-hapus-margin-harga-obat").prop("disabled",true);
                primaryKey = false;
            }
        });

        $(".heading-elements").remove();
    });

    function btnEditHapus(val){
        if(val != 200){
            $("#btn-edit-margin-harga-obat").prop("disabled",true);
            $("#btn-hapus-margin-harga-obat").prop("disabled",true);
        } else {
            $("#btn-edit-margin-harga-obat").prop("disabled",false);
            $("#btn-hapus-margin-harga-obat").prop("disabled",false);
        }
    }
    $(document).on("click",".addrow",function(event) {
        event.preventDefault();
        var _save = true;
        $.each(_jsonData, function(key, val) {
            if (val.harga_min > val.harga_max || val.harga_max == 0) {
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

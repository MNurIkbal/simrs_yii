<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = \Yii::t('fe', 'Peran Pengguna');
$this->params['breadcrumbs'][] = ['label' => 'Dcms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
              <!-- breadcrumbs replace with this -->
              <div class="row">
                  <div class="column-1">
                      <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                  </div>
                  <div class="column-2">
                      <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                      <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
              </div>
              <!-- end -->
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
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        'tambah' => [
                            'type' => 'link',
                            'title' => \Yii::t('fe', 'Tambah'),
                            'icon' => 'fa fa-plus',
                            'method' => 'not exist',
                            'attributes' => [
                                'class' => 'data-tambah',
                                'id' => 'tambah',
                                'data-target' => '/dcms/peran-pengguna/create',
                                'data-options' => 'link'

                            ]
                        ],
                        'ubah' => [
                            'type' => 'link',
                            'title' => \Yii::t('fe', 'Ubah'),
                            'icon' => 'fa fa-pencil',
                            'method' => 'not exist',
                            'attributes' => [
                                'class' => 'data-ubah',
                                'id' => 'edit',
                                'data-target' => '/dcms/peran-pengguna/update?id=',

                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                            ]
                        ],
                        'pdf',
                        'excel',
                    ],'#table-peranpengguna');?>

            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="table-peranpengguna" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Peran Pengguna Nama");?></th>
                            <th><?=\Yii::t("fe", "Nama lainnya");?></th>
                            <th><?=\Yii::t("fe", "Modul");?></th>
                            <th width="1">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs('
    // Global Var
    var tablePeranPengguna;

    // Event Reload
    $(document).on("switchChange.bootstrapSwitch", ".change-status", function (e, state) {

        var dataStatus = "0";
        var dataId = $(this).attr("data-id");

        if (e.target.checked == true)
            dataStatus = "1";

        $(this).docoForm("delete",{
            url: baseUrl+"master/cara-bayar/change-status?id="+dataId+"&status="+dataStatus,
            confirmTitle : "'.(\Yii::t("fe", "Konfirmasi")).'",
            confirmMessage : "'.(\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")).'",
            success : function (data) {
                tablePeranPengguna.draw();
            }
        });
        tablePeranPengguna.draw();
    });

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        tablePeranPengguna.draw();
    });

    // Event Delete
    // $(document).on("click", ".data-delete", function(e) {
    //     e.preventDefault();
    //     $(this).docoForm("delete",{
    //         success : function (data) {
    //             tablePeranPengguna.draw()
    //         }
    //     });
    //     return false;
    // });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        tablePeranPengguna = $("#table-peranpengguna").docoTabel({
            filter: true,
            //add for handle checkbox
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
            scrollX: true,
            ajax: baseUrl+"dcms/peran-pengguna/get-data",
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    render : function () {
                        return null;
                    }
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Peran pengguna nama")).'", data: "peranpenggunanama"},
                {title: "'.(\Yii::t("fe", "Nama lainnya")).'", data: "peranpenggunanamalain"},
                {title: "'.(\Yii::t("fe", "Modul")).'",  data: "module_nama", name : "modul_k.modul_namalainnya"},
                {
                    title: "Status", 
                    data: "peranpengguna_aktif",
                    render: function (data, type, row, meta) {
                        return row.is_active
                    },
                },
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(tablePeranPengguna, [
            [
                5, 
                \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('peranpengguna_aktif', '', $status, ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Pilih')]))).'\'
            ],
        ]);
    });


    $("#table-peranpengguna tbody").on("click", "tr", function(){
            try {
                primaryKey = tablePeranPengguna.row(".selected").data().primary ? tablePeranPengguna.row(".selected").data().primary : null;
            } catch (e) {
                primaryKey = false;
            }
            console.log(primaryKey);

            if (primaryKey) {
                 $("#edit").attr("href","/dcms/peran-pengguna/update?id="+primaryKey);
            } else {
                $("#edit").attr("href","javascript:void(0)");
            }

        });
', View::POS_END, 'b-index');
?>

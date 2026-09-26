<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = \Yii::t('fe', 'Klasifikasi Kamar');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
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
                        'add' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => '/master/klasifikasi-kamar/create',
                                'data-width' => '30%',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'id' => 'btn-edit',
                                'data-options' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-url' => '/master/klasifikasi-kamar/update?id=',
                                'data-width' => '30%',
                            ]
                        ],
                        'hapus' => [
                            'type' => 'button',	
                            'title' => \Yii::t('fe', 'Hapus'),	
                            'icon' => 'fa fa-trash',	
                            'method' => '#',
                            'attributes' => [	
                                'id' => 'btn-hapus',	
                                'data-options'=>'click',
                                'data-conditions' => 'id'	
                            ]
                        ],
                        // 'pdf',
                        'excel-bgprocess' => [
                            'type' => 'button',
                            'title' => 'Unduh Excel',
                            'icon' => 'fa fa-file-excel-o',
                            'attributes' => [
                                'id'=>'excel-bgprocess',
                                'data-options' => 'excel-serconn',
                                'data-target' => '#modal_backdrop',
                                'data-width' => '50%',
                                'data-url' =>'/master/klasifikasi-kamar/show-popup-excel?',
                            ]
                        ],
                    ],'#table-klasifikasi-kamar');?>
            </div>

            <div class="panel-body">
                <div class="advanced-filter"></div>
                <table id="table-klasifikasi-kamar" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Nama Klasifikasi");?></th>
                            <th><?=\Yii::t("fe", "Sirs Online");?></th>
                            <th><?=\Yii::t("fe", "EIS Covid");?></th>
                            <th><?=\Yii::t("fe", "Aplicare");?></th>
                            <th><?=\Yii::t("fe", "SPGDT");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="17"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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
    var tableKlasifikasiKamar;
    var data;

    // Event Reload
    $(document).on("switchChange.bootstrapSwitch", ".change-status", function (e, state) {

        var dataStatus = "0";
        var dataId = $(this).attr("data-id");

        if (e.target.checked == true)
            dataStatus = "1";

        $(this).docoForm("delete",{
            url: baseUrl+"master/klasifikasi-kamar/change-status?id="+dataId+"&status="+dataStatus,
            confirmTitle : "'.(\Yii::t("fe", "Konfirmasi")).'",
            confirmMessage : "'.(\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")).'",
            success : function (data) {
                tableKlasifikasiKamar.draw();
            }
        });
        tableKlasifikasiKamar.draw();
    });

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {
        jQuery("#btn-delete").removeClass("btn-toolbar");

        // Generate Table
        tableKlasifikasiKamar = $("#table-klasifikasi-kamar").docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style: "os",
                selector: "tr"
            },
            sorting: [[2, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"master/klasifikasi-kamar/get-data",
            columns: [
                {
                    title: "",
                    data: null,
                    defaultContent: "",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Nama Klasifikasi")).'",  data: "klasifikasikamar_nama"},
                {title: "'.(\Yii::t("fe", "SIRS Online")).'",  data: "sirsonline_nama"},
                {title: "'.(\Yii::t("fe", "EIS Covid")).'",  data: "eiscovid_nama"},
                {title: "'.(\Yii::t("fe", "Applicare")).'",  data: "namakelas_aplicare"},
                {title: "'.(\Yii::t("fe", "SPGDT")).'",  data: "spgdt_nama"},
                {title: "'.(\Yii::t('fe', 'Status')).'", data: "is_active"},
            ],
        });

        tableKlasifikasiKamar.on( \'xhr\', function () {
            data = tableKlasifikasiKamar.ajax.params();
            // alert( \'Search term was: \'+data.search.value );
        });

        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(tableKlasifikasiKamar, [
            [2, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('klasifikasikamar_nama', '', ['class' => 'form-control','placeholder'=>'Nama Klasifikasi']))).'\'],
            [
                3,
                \'<div class="">'.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList(
                    'sirsonline_nama',
                    null,
                    $sirs, [
                        'class' => 'form-control select2',
                        'id' => 'sirsonline_nama',
                        'prompt' => \Yii::t('fe', '-- Pilih --'),
                        'col-index' => '3'
                    ])
                )).'</div>\'
            ],
            [
                4,
                \'<div class="">'.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList(
                    'eiscovid_nama',
                    null,
                    $eis, [
                        'class' => 'form-control select2',
                        'id' => 'eiscovid_nama',
                        'prompt' => \Yii::t('fe', '-- Pilih --'),
                        'col-index' => '3'
                    ])
                )).'</div>\'
            ],
            [
                5,
                \'<div class="">'.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList(
                    'namakelas_aplicare',
                    null,
                    $listReferensiAplicare, [
                        'class' => 'form-control select2',
                        'id' => 'applicare_nama',
                        'prompt' => \Yii::t('fe', '-- Pilih --'),
                        'col-index' => '3'
                    ])
                )).'</div>\'
            ],
            [
                6,
                \'<div class="">'.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList(
                    'spgdt_nama',
                    null,
                    $spdgt, [
                        'class' => 'form-control select2',
                        'id' => 'spgdt_nama',
                        'prompt' => \Yii::t('fe', '-- Pilih --'),
                        'col-index' => '3'
                    ])
                )).'</div>\'
            ],
            [7, \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $options['status'], ['class' => 'form-control select2']))) . '\']
        ], {
            2:0,
            3:1,
            4:2,
            5:3,
            6:4,
            7:5
        }, true);
        
        $("#table-ruangan tbody").on("click", "tr", function(){
            try {
                primaryKey = tableKlasifikasiKamar.row(".selected").data().primary ? tableKlasifikasiKamar.row(".selected").data().primary : null;
            } catch (e) {
                primaryKey = false;
            }

            if (primaryKey) {
                $("#btn-edit").attr("action",$("#btn-edit").data("url")+primaryKey);
            } else {
                $("#btn-edit").removeAttr("action");
                $(".data-delete").removeAttr("action");
            }
            
        });

        $(document).on("click","#btn-hapus", function(event) {
            event.preventDefault();
            var id = tableKlasifikasiKamar.row(".selected").data() != null ? tableKlasifikasiKamar.row(".selected").data().primary : null;
            if (id == null) {
                docoNotification("warning", i18next.t("Terjadi Kesalahan"), i18next.t("Belum ada data yang dipilih!"));
                return false;
            }

            $(this).docoForm("click",{
                url: "/master/klasifikasi-kamar/delete?id="+id,
                confirmTitle: i18next.t("Konfirmasi"),
                confirmMessage: i18next.t("Apakah anda yakin untuk menghapus data ini ?"),
                data: {},
                method: "POST",
                before: function () {
                    
                },
                success: function () {
                    tableKlasifikasiKamar.draw();
                }
            });
        });

    });
', View::POS_END, 'b-index');
?>

<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
// use app\components\DocoHelpers;
// use app\components\DocoController;

$this->title = Yii::t('fe', 'Jenis Kasus Penyakit');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white" style="margin-top: 0px !important">
            <div class="panel-heading">
              <!-- breadcrumbs replace with this -->
              <div class="row">
                  <div class="column-1">
                      <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                  </div>
                  <div class="column-2">
                      <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                  </div>
              </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form-jeniskasuspenyakit'
                            ]
                        ],
                        'add' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => '/master/jenis-kasus-penyakit/create',
                            ]
                        ],
                         'edit' => [
                            'attributes' => [
                                'data-url' => Url::home().'master/jenis-kasus-penyakit/update?id=',
                                'id' => 'btn-edit-suku',
                                'data-options' => 'modal',
                                'data-target' => '#modal_backdrop',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'data-additional' => 'data-rm',
                                'id' => 'btn-delete-suku',
                                'data-target' => Url::home().'master/jenis-kasus-penyakit/delete?id=',
                            ]
                        ],
                        'pdf' => [
                            'attributes' => [
                                'data-target' => '/master/jenis-kasus-penyakit/export-pdf?',
                            ]
                        ],
                        'excel' => ['attributes' => ['data-target'=>'/master/jenis-kasus-penyakit/export-excel?']],
                    ], '#table-jeniskasuspenyakit');?>
            </div>
            <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12 filter-form-jeniskasuspenyakit"></div>
                    </div>
                        <div class="form-group">
                    </div>
                    <table class="table datatable-basic table-striped table-hover dataTable no-footer"
                            id="table-jeniskasuspenyakit" style="width: 100%;" >
                        <thead>
                            <tr class="bg-inverse">
                                <th></th>
                                <th width="1"><?= Yii::t('fe', 'No'); ?></th>
                                <th><?= Yii::t('fe', 'Nama'); ?></th>
                                <th><?= Yii::t('fe', 'Nama lainnya'); ?></th>
                                <th><?= Yii::t('fe', 'Status'); ?></th>
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
    </div>
</div>

<script>
    var tableJenisKasusPenyakit;
    $(document).ready(function() {
        tableJenisKasusPenyakit = $('#table-jeniskasuspenyakit').docoTabel({
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
                ajax: baseUrl+"master/jenis-kasus-penyakit/get-data",
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
                    {title: "<?=Yii::t('fe', 'Nama')?>",  data: "jeniskasuspenyakit_nama"},
                    {title: "<?=Yii::t('fe', 'Nama Lainnya')?>",  data: "jeniskasuspenyakit_namalainnya"},
                    {title: "Status",  data: "status"}
                ]
            });
        $(".dataTables_filter").hide();

        $(".filter-form-jeniskasuspenyakit").datatableBootstrapFilter(tableJenisKasusPenyakit,
            [
                [2, '<?=(preg_replace("/[\n\t\r]/i", '', Html::textInput('jeniskasuspenyakit_nama', '', ['class' => 'form-control','placeholder'=>'Nama'])));?>'],
                [3, '<?=(preg_replace("/[\n\t\r]/i", '', Html::textInput('jeniskasuspenyakit_namalainnya', '', ['class' => 'form-control','placeholder'=>'Nama Lainnya'])));?>'],
                [4, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'select2', 'prompt' => \Yii::t('fe', '--Pilih--')])));?>']
            ]
        );

        var primaryKey;

        $("#table-jeniskasuspenyakit tbody").on("click", "tr", function(){
            try {
                primaryKey = tableJenisKasusPenyakit.row(".selected").data().primary ? tableJenisKasusPenyakit.row(".selected").data().primary : null;
            } catch (e) {
                primaryKey = false;
            }

            // Check class selected
            /*if ($('#table-jeniskasuspenyakit tr.selected').length == 0) {
                // Disable edit button
                $("#'btn-edit-jns-penyakit'").prop("disabled", true);
                $("#'btn-delete-jns-penyakit'").prop("disabled", true);
            }
            else {
                // Disable edit button
                $("#'btn-edit-jns-penyakit'").prop("disabled", false);
                $("#'btn-delete-jns-penyakit'").prop("disabled", false);
            }*/

            if (primaryKey) {
                // alert("test");
                // console.log(primaryKey);
                $("#btn-edit-jns-penyakit").prop("disabled", false);
                $("#btn-delete-jns-penyakit").prop("disabled", false);
                 
                
                $(".data-edit").attr("action",$(".data-edit").data("url")+primaryKey);
                $(".data-delete").attr("action",$(".data-delete").data("target")+primaryKey);
            } else {
                $("#btn-edit-jns-penyakit").prop("disabled", true);
                $("#btn-delete-jns-penyakit").prop("disabled", true);

                $(".data-edit").removeAttr("action");
                $(".data-delete").removeAttr("action");
            }
        });
    });

    // Event Delete
    $(document).on("click", "#data-delete-penyakit", function(e) {
        e.preventDefault();
        if($(this).attr("action") == "null_id"){
            new PNotify({
                title: "Terjadi Kesalahan",
                text: "Tidak ada data yang dipilih",
                addclass: 'alert alert-warning alert-arrow-right alert-styled-right',
                type: 'error'
            });
        }else{
            $(this).docoForm("delete",{
                success : function (data) {
                    tableJenisKasusPenyakit.draw()
                }
            });
        }
        return false;
    });

    // Event Reload
    $(document).on("click", ".data-reset", function() {
        tableJenisKasusPenyakit.draw();
    });
</script>

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

$this->title = Yii::t('fe', 'Jenis Kasus Penyakit Ruangan');
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
                                'data-parent'=>'.filter-form-ruangan'
                            ]
                        ],
                        'add' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_kasuspenyakitruangan',
                                'action' => '/master/kasus-penyakit-ruangan/create',
                                'disabled' => false
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'id' => 'data-edit',
                                'data-options' => 'modal',
                                'data-target' => '#modal_kasuspenyakitruangan',
                                'data-url' => '/master/kasus-penyakit-ruangan/update?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'id' => 'btn-delete-penyakit-ruangan',
                                'data-additional' => 'data-rm'
                            ]
                        ],
                        'pdf' => [
                            'attributes' => [
                                'data-target' => '/master/kasus-penyakit-ruangan/export-pdf?',
                                'disabled' => false
                            ]
                        ],
                        'excel' => [
                            'attributes' => [
                                'data-target'=>'/master/kasus-penyakit-ruangan/export-excel?',
                                'disabled' => false
                            ]
                        ],
                    ],'#kasus-penyakit-ruangan');?>
            </div>
            <div class="panel-body">
                <div class="row">
                        <div class="col-md-12 filter-form-ruangan"></div>
                </div>
                <table class="table datatable-basic table-striped table-hover dataTable no-footer"
                        id="kasus-penyakit-ruangan" style="width: 100%;">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th><?= Yii::t('fe', 'No'); ?></th>
                            <th><?= Yii::t('fe', 'Nama Ruangan'); ?></th>
                            <th><?= Yii::t('fe', 'Jenis kasus penyakit'); ?></th>
                            <th><?= Yii::t('fe', 'Status'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="modal_kasuspenyakitruangan" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        </div>
    </div>
</div>

<script>
    var tableRuangan;
    $(document).ready(function() {
        tableRuangan = $('#kasus-penyakit-ruangan').docoTabel({
                filter: true,
                //add for handle checkbox
                columnDefs: [ {
                    orderable: false,
                    className: 'select-checkbox',
                    targets:   0
                }],
                select: {
                    style:    'os',
                    selector: 'tr'
                },
                sorting: [[2, 'asc']],
                displayLength: 10,
                processing: true,
                serverSide: true,
                scrollX: true,
                // rowsGroup: [2],
                ajax: baseUrl+"master/kasus-penyakit-ruangan/get-data",
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
                    {title: "<?=Yii::t('fe', 'Nama Ruangan')?>",  data: "ruangan_nama",name:"ruangan_id"},
                    {title: "<?=Yii::t('fe', 'Jenis Kasus Penyakit')?>",  data: "jeniskasuspenyakit_nama",name: "jeniskasuspenyakit_id"},
                    {title: "Status",  data: "is_active"}
                ]
            });
        $(".dataTables_filter").hide();
        $(".filter-form-ruangan").datatableBootstrapFilter(tableRuangan,[
                    [2, 
                        '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('ruangan_nama', '', $listRuangan, ['id' => 'filter_ruangan_nama','class' => 'select2', 'prompt' => \Yii::t('fe', '--Pilih--')])));?>'
                    ],
                    [3, 
                        '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('jeniskasuspenyakit_nama', '',$listKasusPenyakit, ['id' => 'filter_jeniskasuspenyakit_nama','class' => 'select2', 'prompt' => \Yii::t('fe', '--Pilih--')])));?>'
                    ],
                    [4, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'select2', 'prompt' => \Yii::t('fe', '--Pilih--')])));?>']

                ]
        );


        /*$('#filter_ruangan_nama').select2({
            ajax: {
                url: baseUrl+"master/end-point/get-all-ruangan",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                        is_valueWithText: 2
                    };
                },
                cache: true
            },
            placeholder: '-- Pilih Nama Ruangan--',
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 3,
        });*/

        /*$('#filter_jeniskasuspenyakit_nama').select2({
            ajax: {
                url: baseUrl+"master/end-point/get-all-jenis-penyakit",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                        is_valueWithText: 2
                    };
                },
                cache: true
            },
            placeholder: '-- Pilih Jenis Kasus Penyakit--',
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 3,
        });*/

        $('#kasus-penyakit-ruangan tbody').on('click', 'tr', function(){
            try {
                primaryKey = tableRuangan.row('.selected').data().primary ? tableRuangan.row('.selected').data().primary : null;
            } catch (e) {
                primaryKey = false;
            }

            if (primaryKey) {
                $('#data-edit').attr('action',$('#data-edit').data('url')+primaryKey);
                $('#data-delete').attr('action',$('#data-delete').data('url')+primaryKey);
            } else {
                $('#data-edit').removeAttr('action');
                $('#data-delete').removeAttr('action');
            }
            
        });

    });

    // Event Delete
    $(document).on("click", "#data-delete", function(e) {
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
                    tableRuangan.draw()
                }
            });
        }
        return false;
    });

    $(document).on("click", ".data-reset", function() {
        tableRuangan.draw();
    });
</script>

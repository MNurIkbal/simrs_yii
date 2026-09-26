<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-21 11:52:58
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-03-27 14:06:18
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")), 'url' => ['index']];
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
                      <h3 class="panel-title"><b><?= $this->title ?></b></h3>
                      <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
              </div>
              <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset'=> [
                        'attributes'=>[
                            'data-parent'=>'.filter-form'
                        ]
                    ],
                    'add' => [
                        'attributes' => [
                            'data-options' => 'link',
                            'data-target' => '/master/pegawai/create',
                        ]
                    ],
                    'edit' => [
                        'attributes' => [
                            'id' => 'data-edit',
                            'data-target' => '/master/pegawai/update?id=',
                        ]
                    ],
                    'delete' => [
                        'attributes' => [
                        'id' => 'data-delete',
                        'data-additional' => 'data-rm'
                        ]
                    ],
                    'pdf',
                    'excel',
                    'esign-reg' => [
                        'title' => 'Daftar Esign',
                        'icon' => 'fa fa-registered',
                        'attributes' => [
                            'data-target' => '/master/pegawai/form-esign-regis?id=',
                        ]
                    ],
                    'esign-revoke' => [
                        'title' => 'Revoke Esign',
                        'icon' => 'fa fa-ban',
                        'attributes' => [
                            'data-href' => '/master/pegawai/form-esign-revoke?id=',
                            'data-width' => '50%',
                            'data-wrapper' => '#modal-esign .modal-content',
                        ]
                    ],
                    'esign-reenroll' => [
                        'title' => 'Re-Enroll',
                        'icon' => 'fa fa-registered',
                        'attributes' => [
                            'data-target' => '/master/pegawai/form-re-enroll?id=',
                        ]
                    ],
                ]);?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form">
                    </div>
                </div>
                <table width="100%" id="example" class="table datatable-basic table-striped table-hover dataTable no-footer">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="80"><?= Yii::t('fe', 'No') ?></th>
                            <th><?= Yii::t('fe', 'Nama Pegawai') ?></th>
                            <th><?= Yii::t('fe', 'NIP') ?></th>
                            <th><?= Yii::t('fe', 'Jabatan') ?></th>
                            <th width="10%"><?= Yii::t('fe', 'Pangkat') ?></th>
                            <th width="10%"><?= Yii::t('fe', 'Aktif') ?></th>
                            <th width="10%"><?= Yii::t('fe', 'Satu Sehat Practitioner ID') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="modal-esign" class="modal fade" style="z-index: 1041 !important; overflow-y:auto !important" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
        </div>
    </div>
</div>

<?php

$this->registerJs("
        // Global Var
        var table;

        // Event Reload
        $(document).on('click', '.data-reload', function() {
            table.draw();
        });

        $(document).ready(function(){
            table = $('#example').docoTabel({
                filter: true,
                //add for handle checkbox
                columnDefs: [ {
                    orderable: false,
                    className: 'select-checkbox',
                    targets:   0
                }],
                select: {
                    style: 'os',
                    selector: 'tr'
                },
                sorting: [[2, 'asc']],
                displayLength: 10,
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: baseUrl+'master/pegawai/get-data-pegawai-tabel',
                columns: [
                    {
                        data: 'null',
                        searchable: false,
                        orderable: false,
                        defaultContent:'',
                        width: '7%',
                    },
                    {
                        title: 'No',
                        data: 'rowNum',
                        searchable: false,
                        orderable: false
                    },
                    {title: '".(\Yii::t('fe', 'Nama Pegawai')). "', data: 'nama_pegawai'},
                    {title: '".(\Yii::t('fe', 'NIP')). "', data: 'nomorindukpegawai'},
                    {title: '".(\Yii::t('fe', 'Jabatan')). "',  data:'jabatan_nama'},
                    {title: '".(\Yii::t('fe', 'Pangkat'))."',  data:'pangkat_nama'},
                    {title: '".(\Yii::t('fe', 'Status'))."',  data:'aktif', orderable: false},
                    {title: '".(\Yii::t('fe', 'Satu Sehat Practitioner ID'))."',  data:'satusehat_pegawai_id', orderable: false},
                ],
            });
            $('.dataTables_filter').hide();
            $('.filter-form').datatableBootstrapFilter(table, [
                [
                    2,
                    \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'', 
                        Html::textInput('nama_pegawai', '', 
                                [
                                    'class' => 'form-control', 
                                    'col-index'=>3,
                                    'placeholder'=> 'Nama Pegawai'
                                ]
                            )
                        )
                    )."<div>\"
                ],
                [
                    3,
                    \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'', 
                        Html::textInput('nomorindukpegawai', '', 
                                [
                                    'class' => 'form-control', 
                                    'col-index'=>3,
                                    'placeholder' => 'NIP', 
                                ]
                            )
                        )
                    )."<div>\"
                ],
                [
                    4,
                    \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'', 
                        Html::dropDownList('jabatan', '', array(), 
                                [
                                    'class' => 'form-control select2 jabatan', 
                                    'col-index'=>4
                                ]
                            )
                        )
                    )."<div>\"
                ],
                [
                    5,
                    \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'', 
                        Html::dropDownList('pangkat', '', array(), 
                                [
                                    'class' => 'form-control select2 pangkat', 
                                    // 'prompt' => '— Pilih Pangkat —', 
                                    'col-index'=>5
                                ]
                            )
                        )
                    )."<div>\"
                ],
                [
                    6,
                    \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'', 
                        Html::dropDownList('pegawai_aktif', 3, $status_arr, 
                                [
                                    'class' => 'form-control select2 pegawai_aktif',
                                    'prompt' => '— Pilih Status Pegawai —', 
                                    'col-index' => 6
                                ]
                            )
                        )
                    )."<div>\"
                ],
            ]);

            $('.nama_pegawai').select2({
                placeholder: '&nbsp;',
                minimumInputLength: 3,  
                ajax: {
                    url: '/master/pegawai/get-nama-pegawai',
                    dataType: 'json',
                    quietMillis: 250,
                    data: function(term, page){
                        return{
                            q: term,
                            page: page
                        }
                    },
                    processResults: function (data) {
                    return {
                        results: data.result
                    };
                    }
                },
                dropdownCssClass: 'bigdrop',
                escapeMarkup: function (m) { return m; },
            });
            $('.nip').select2({
                placeholder: '&nbsp;',
                minimumInputLength: 3,  
                ajax: {
                    url: '/master/pegawai/get-nip',
                    dataType: 'json',
                    quietMillis: 250,
                    data: function(term, page){
                        return{
                            q: term,
                            page: page
                        }
                    },
                    processResults: function (data) {
                    return {
                        results: data.result
                    };
                    }
                },
                dropdownCssClass: 'bigdrop',
                escapeMarkup: function (m) { return m; },
            });

            $('.jabatan').select2({
                placeholder: '— Pilih Jabatan —',
                ajax: {
                    url: '/master/pegawai/list-jabatan',
                    dataType: 'json',
                    quietMillis: 250,
                    data: function(term, page){
                        return{
                            q: term,
                            page: page
                        }
                    },
                    'processResults' : function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.result,
                                pagination: {
                                    more: data.pagination
                            }
                        };
                    } ,
                },
                dropdownCssClass: 'bigdrop',
                escapeMarkup: function (m) { return m; },
            });

            $('.pangkat').select2({
                placeholder: '— Pilih Pangkat —',
                ajax: {
                    url: '/master/pegawai/list-pangkat',
                    dataType: 'json',
                    quietMillis: 250,
                    data: function(term, page){
                        return{
                            q: term,
                            page: page
                        }
                    },
                    'processResults' : function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.result,
                                pagination: {
                                    more: data.pagination
                            }
                        };
                    } ,
                },
                dropdownCssClass: 'bigdrop',
                escapeMarkup: function (m) { return m; },
            });

            // $('.pangkat').select2({
            //     placeholder: '— Pilih Pangkat —',
            //     allowClear: true
            // });
            
        });
	");
    $this->registerJs($this->render('js/toolbar-esign.js'));
?>

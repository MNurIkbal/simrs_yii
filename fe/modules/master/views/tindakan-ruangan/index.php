<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;

$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;
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
												<h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$title); ?></b></h3>
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
                        'reset',
                        // 'print',
                        // 'pdf',
                        // 'excel',
                        'add',
                        'detail'=>[
                            'type'=>'link',
                            'title' => \Yii::t('fe', 'Detail'),
                            'icon' => 'fa fa-list-ul',
                            'method' => 'not-exist',
                            'attributes' => [
                                'data-target'=>Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/detail?id='
                            ]
                        ],
                    ],'#tindakan_ruangan');?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form">
                    </div>
                </div>

            	<table id="tindakan_ruangan" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th width="20">No</th>
                            <th><?=\Yii::t("fe", "Kode Tindakan");?></th>
                            <th><?=\Yii::t("fe", "Nama Tindakan");?></th>
                            <th><?=\Yii::t("fe", "Kelompok Tindakan");?></th>
                            <th><?=\Yii::t("fe", "Kategori Tindakan");?></th>
                            <th><?=\Yii::t("fe", "Jenis Tindakan");?></th>
                        </tr>
                    </thead>
                    <tbody>
                    	<?php /* list data */ ?>
                    </tbody>
                </table>
            </div>
		</div>
	</div>
</div>

<?php
$this->registerJs("
	var table;

    // Event Reload
    $(document).on('click', '.data-reload', function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $('#tindakan_ruangan').docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   0
            }],
            select: {
                style:    'os',
                selector: 'td:first-child'
            },
            sorting: [[2, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            oLanguage: {
                sLengthMenu: '".(\Yii::t('fe', 'dt_length_menu'))."',
                sZeroRecords: '".(\Yii::t('fe', 'dt_zero_records'))."',
                sEmptyTable: '".(\Yii::t('fe', 'dt_empty_table'))."',
                sInfoFiltered: '".(\Yii::t('fe', 'dt_info_filtered'))."',
                sInfoEmpty: '".(\Yii::t('fe', 'dt_info_empty'))."',
                sInfo: '".(\Yii::t('fe', 'dt_info'))."',
                oPaginate: {
                    sFirst: '".(\Yii::t('fe', 'dt_first_page'))."',
                    sPrevious: '".(\Yii::t('fe', 'dt_previous_page'))."',
                    sNext: '".(\Yii::t('fe', 'dt_next_page'))."',
                    sLast: '".(\Yii::t('fe', 'dt_last_page'))."'
                }
            },
            ajax: {
                url:baseUrl+'master/tindakan-ruangan/get-data',
                type:'POST'
            },
            columns: [
                {
                    title: '',
                    data: null,
                    defaultContent: '',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(\Yii::t('fe', 'Kode Tindakan'))."', data: 'daftartindakan_kode'},
                {title: '".(\Yii::t('fe', 'Nama Tindakan'))."', data: 'daftartindakan_nama'},
                {title: '".(\Yii::t('fe', 'Kelompok Tindakan'))."', data: 'kelompoktindakan_nama'},
                {title: '".(\Yii::t('fe', 'Kategori Tindakan'))."',  data: 'kategoritindakan_nama'},
                {title: '".(\Yii::t('fe', 'Jenis Tindakan'))."', data: 'jeniskegiatantindakan_nama'},
            ],
        });

        $('.dataTables_filter').hide();

        $('.filter-form').datatableBootstrapFilter(table, [
            [
                2,
                 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('daftartindakan_kode', '', array(),
                            [
                                'class' => 'form-control select2 daftarKodeTindakanNama',
                                'prompt' => \Yii::t('fe', ''),
                                'col-index'=>1,
                            ]
                        )
                    )))."\"
            ],
            [
                4,
                 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('kelompoktindakan_id', '', array(),
                            [
                                'class' => 'form-control select2 KelompokTindakan',
                                'prompt' => \Yii::t('fe', ''),
                                'col-index'=>1,
                            ]
                        )
                    )))."\"
            ],
            [
                5,
                 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('kategoritindakan_id', '', array(),
                            [
                                'class' => 'form-control select2 kategoriTindakan',
                                'prompt' => \Yii::t('fe', ''),
                                'col-index'=>1,
                            ]
                        )
                    )))."\"
            ],
            [
                6,
                 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('jeniskegiatantindakan_id', '', array(),
                            [
                                'class' => 'form-control select2 jenisKegiatanTindakan',
                                'prompt' => \Yii::t('fe', ''),
                                'col-index'=>1,
                            ]
                        )
                    )))."\"
            ],

        ]);

        $('.daftarKodeTindakanNama').select2({
                    minimumInputLength: 2,
                    ajax: {
                        url: '/master/tindakan-ruangan/get-daftar-tindakan',
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

        $('.KelompokTindakan').select2({
                    minimumInputLength: 3,
                    ajax: {
                        url: '/master/tindakan-ruangan/get-kelompok-tindakan',
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

        $('.kategoriTindakan').select2({
                    minimumInputLength: 3,
                    ajax: {
                        url: '/master/tindakan-ruangan/get-kategori-tindakan',
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

        $('.jenisKegiatanTindakan').select2({
                    minimumInputLength: 3,
                    ajax: {
                        url: '/master/tindakan-ruangan/get-jenis-kegiatan-tindakan',
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

        var primaryKey;

        // $('#tindakan_ruangan tbody').on('click', 'tr', function(){

        //         primaryKey = table.row('.selected').data().primary ? table.row('.selected').data().primary : null;
        //         if(primaryKey){
        //             $('.data-edit').attr('action',$('.data-edit').data('target')+primaryKey);
        //             $('.data-detail').attr('href',$('.data-detail').data('target')+primaryKey);
        //         }else{
        //             $('.data-edit').removeAttr('action');
        //             $('.data-detail').removeAttr('href');
        //         }

        //     });

        // $(document).on('click', '.data-detail', function(){
        //     window.location = $(this).attr('href');
        // })
        $(document).on('click', '.data-add', function(){
            window.location = $(this).data('target');
        })

    });
");
?>

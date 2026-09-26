<?php

/**
 * @Author: ayip
 * @Date:   2018-01-12 15:47:03
 * @Last Modified by: ayip
 * @Last Modified time: 2018-01-26 16:33:35
 * @Description:
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['/master/inf-tarif-pelayanan']];
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
                      <h3 class="panel-title"><b>
                        <?= Yii::t('fe', 'Informasi tarif') ?>
                        <?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
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
                        'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                        'komponen_tarif' => [
                            'type' => 'link',
				            'title' => \Yii::t('fe', 'Komponen Tarif'),
				            'icon' => 'fa fa-reorder',
				            'method' => 'detail?id=not_exist',
				            'attributes' => [
				                'class' => 'btn bg-teal data-detail',
				                'data-toggle' => 'modal',
                                'id' => 'komponen-tarif',
				                'data-target' => '#detail',
				                'data-href' => Url::home().('master/inf-tarif-pelayanan/detail?id=')
				            ],
				        ]
                    ]);?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form">
                    </div>
                </div>

                <table id="inf-tarif-pelayanan" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th width="20">No</th>
                            <th><?=\Yii::t("fe", "Nama penjamin");?></th>
                            <th><?=\Yii::t("fe", "Kelompok Tindakan");?></th>
                            <th><?=\Yii::t("fe", "Kategori Tindakan");?></th>
                            <th><?=\Yii::t("fe", "Nama Tindakan");?></th>
                            <th><?=\Yii::t("fe", "Kelas Pelayanan");?></th>
                            <th><?=\Yii::t("fe", "Tarif Total");?></th>
                            <th><?=\Yii::t("fe", "Cyto Tindakan");?> (%)</th>
                            <th><?=\Yii::t("fe", "Diskon Tindakan");?> (%)</th>
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

<div id="detail" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			...
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
        table = $('#inf-tarif-pelayanan').docoTabel({
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
            ajax: baseUrl+'master/inf-tarif-pelayanan/get-data',
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
                {title: '".(\Yii::t('fe', 'Nama penjamin'))."', data: 'penjamin_nama'},
                {title: '".(\Yii::t('fe', 'Kelompok Tindakan'))."',  data: 'kelompoktindakan_nama',searchable: false},
                {title: '".(\Yii::t('fe', 'Nama Tindakan'))."', data: 'daftartindakan_nama'},
                {title: '".(\Yii::t('fe', 'Kategori Tindakan'))."', data: 'kategoritindakan_nama'},
                {title: '".(\Yii::t('fe', 'Kelas Pelayanan'))."', data: 'kelaspelayanan_nama'},
                {title: '".(\Yii::t('fe', 'Tarif Total'))."', data: 'harga_tariftindakan',searchable: false},
                {title: '".(\Yii::t('fe', 'Cyto Tindakan (%)'))."', data: 'persencyto_tindakan',searchable: false},
                {title: '".(\Yii::t('fe', 'Diskon Tindakan (%)'))."', data: 'persendiskon_tindakan',searchable: false},
            ],
        });

        $('.dataTables_filter').hide();

        $('.filter-form').datatableBootstrapFilter(table,[
            [
                2,
                 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('penjamin_id', '', $listfilter['penjamin'],
                            [
                                'class' => 'form-control select2 ddl_penjamin',
                                'prompt' => \Yii::t('fe', 'Pilih'),
                                'col-index'=>2,
                            ]
                        )
                    )))."\"
            ],
            [
                4,
                 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('daftartindakan_id', '', $listfilter['daftarTindakan'],
                            [
                                'class' => 'form-control select2 ddl_daftartindakan',
                                'prompt' => \Yii::t('fe', 'Pilih'),
                                'col-index'=>2,
                            ]
                        )
                    )))."\"
            ],
            [
                5,
                 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('kategoritindakan_id', '', $listfilter['kategoriTindakan'],
                            [
                                'class' => 'form-control select2 ddl_kategoritindakan',
                                'prompt' => \Yii::t('fe', 'Pilih'),
                                'col-index'=>2,
                            ]
                        )
                    )))."\"
            ],
            [
                6,
                 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('kelaspelayanan_id', '', $listfilter['kelasPelayanan'],
                            [
                                'class' => 'form-control select2 ddl_kelas_pelayanan',
                                'prompt' => \Yii::t('fe', 'Pilih'),
                                'col-index'=>2,
                            ]
                        )
                    )))."\"
            ],
    	]);

        dateRangeHelper('.startDate','.endDate','.targetDate');

        $('.ddl_kelas_pelayanan').select2({
	        minimumInputLength: 3,
	        // ajax: {
	        //     url: '/master/kelas/get-kelas-pelayanan',
	        //     dataType: 'json',
	        //     quietMillis: 250,
	        //     data: function(term, page){
	        //         return{
	        //             q: term,
	        //             page: page
	        //         }
	        //     },
	        //     processResults: function (data) {
	        //       return {
	        //         results: data.result
	        //       };
	        //     }
	        // },
	        dropdownCssClass: 'bigdrop',
	        escapeMarkup: function (m) { return m; },
	    });

        var primaryKey;

        $('#inf-tarif-pelayanan tbody').on('click', 'tr', function(){            
                if(table.row('.selected').data() !=  undefined ||table.row('.selected').data() != null){
                    primaryKey = table.row('.selected').data().primary ? table.row('.selected').data().primary : 0;
                    if(primaryKey){
                        $('.data-detail').attr('href',$('.data-detail').data('href')+primaryKey);
                    }else{
                        $('.data-detail').removeAttr('href');
                        $('#komponen-tarif').removeAttr('href');
                    }                   
                }else{
                    $('.data-detail').removeAttr('href');
                    $('#komponen-tarif').attr('href','/master/inf-tarif-pelayanan/detail?id=not_exist');
                }           
            });

        // $(document).on('click', '.data-detail', function(){
        //     window.location = $(this).attr('href');
        // })

        // $(document).on('click', '.data-add', function(){
        //     window.location = $(this).data('target');
        // })

    });", View::POS_END, 'js-kuning');
?>

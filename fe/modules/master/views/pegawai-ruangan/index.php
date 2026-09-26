<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-21 11:52:58
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2019-04-12 11:52:43
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
                      <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
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
                    <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                        'pdf',
                        'excel',
                        'add',
                        'detail',
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
                            <th ></th>
                            <th width="5%"><?= Yii::t('fe', 'No') ?></th>
                            <th><?= Yii::t('fe', 'Ruangan') ?></th>
                            <th><?= Yii::t('fe', 'Nama Pegawai') ?></th>
                            <th><?= Yii::t('fe', 'Kelompok Pegawai') ?></th>
                            <th width="10%"><?= Yii::t('fe', 'Status') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
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
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   0
            }],
            select: {
                style:    'os',
                selector: 'tr'
            },
            sorting: [[2, 'asc'], [3, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            rowsGroup: [2],
            ajax: baseUrl+'master/pegawai-ruangan/get-data-pegawai-ruangan',
            columns: [
                {
                    title: '',
                    data: null,
                    defaultContent: '',
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
                {title: '".(\Yii::t('fe', 'Ruangan'))."', data: 'ruangan_nama', name: 'ruangan_id'},
                {title: '".(\Yii::t('fe', 'Nama Pegawai'))."', data: 'nama_pegawai'},
                {title: '".(\Yii::t('fe', 'Kelompok Pegawai'))."',  data: 'kelompokpegawai_nama', name: 'kelompokpegawai_id'},
                {title: '".(\Yii::t('fe', 'Status'))."',  data: 'is_active'},
            ],
        });
        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table, [
        	[
        		2,
        		 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('ruangan_nama', '',
                            $ruangan,
                            [
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', '— Pilih Ruangan —'),
                                'col-index'=>1,
                            ]
                        )
                    )))."\"
        	],
        	[
        		4,
        		 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('kelompokpegawai_nama', '',
                            $kelompok_pegawai,
                            [
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', '— Pilih Kelompok Pegawai —'),
                                'col-index'=>3,
                            ]
                        )
                    )))."\"
        	],
        	[
        		5,
        		 \"".(preg_replace('/[\n\t\r]/i', '', preg_replace("/[\"]/i", '\'',
                        Html::dropDownList('status', '',
                            $status_arr,
                            [
                                'class' => 'form-control select2',
                                'prompt' => \Yii::t('fe', '— Pilih Status —'),
                                'col-index'=>4,
                            ]
                        )
                    )))."\"
        	],

        ]);

        $('.pegawai_nama').select2({
                placeholder: '".\Yii::t("fe", "— Pilih Nama Pegawai — ")."',
                minimumInputLength: 3,
                ajax: {
                    url: '/master/pegawai-ruangan/get-data-pegawai',
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



    });

	");

?>

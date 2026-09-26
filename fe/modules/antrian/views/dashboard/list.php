<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\modules\master\models\DisplayAntrianForm;
use Doco\master\controllers\DisplayAntrianController;
use yii\widgets\Breadcrumbs;
use app\components\DocoTableHelper;


$this->title = \yii::t('fe', 'Data List Antrian');
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
                        'generate-link'=>[
                            'type'=>'button',
                            'title' => \Yii::t('fe', 'Generate Link'),
                            'icon' => 'fa fa-link',
                            'method' => 'not-exist',
                            'attributes'=>[
                                'id'=>'btn-generate-link',
                                'data-target'=>Url::home().'antrian/dashboard/generate-link?id=',
                                'data-pages'=>'_blank',
                            ],
                        ],
                    ],'#table-jenisantrian');?>
            </div>


                <div class="panel-body">
                <div class="row">
                <div class="col-md-12 filter-form"></div>
                </div>
                <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="table-jenisantrian" data-source="<?=Url::home();?>antrian/dashboard/get-data" data-filter=".form-filter" data-test="true">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="5%"></th>
                            <th width="5%">No</th>
                            <th width="5%">Status</th>
                            <th></th>
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
    var table;
    // Event Reload
    $(document).on('click', '.data-reload', function() {
        table.draw();
    });

    $(document).ready(function(){
        table = $('#table-jenisantrian').docoTabel({
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
            fixedColumns: {
                leftColumns: 1
            },
            ajax: baseUrl+'antrian/dashboard/get-data',
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: '',
                },
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {title: '".(\Yii::t('fe', 'Jenis Layar Antrian'))."', data: 'lookup_name', searchable: false},
                {title: '".(\Yii::t('fe', 'Status'))."',  data: 'is_active', searchable: false},
            ],
        });
        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table,
            []
        );

    });


    ", VIEW::POS_END, 'js-kunings');
?>

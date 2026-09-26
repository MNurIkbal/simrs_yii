<?php

/**
 * @author Randy Vianda Putra
 * @todo View Master dokter
 * @copyright 21 November 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\modules\master\models\DisplayAntrianForm;
use Doco\master\controllers\DisplayAntrianController;
use yii\widgets\Breadcrumbs;
use app\components\DocoTableHelper;


$this->title = \Yii::t('fe', 'Master Dokter');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['/master']];
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
                <?= DocoHelpers::generateToolbar([
                        'search',
                        'detail' => [
                            'attributes' => [
                                'id' => 'data-edit',
                                'data-target' => '/master/dokter/update?id=',
                            ]
                        ],
                    ],'#table-dokter');
                ?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table 
                    class="table datatable-basic table-striped table-hover dataTable no-footer"
                    id="table-dokter"
                    style="width:100%"
                    data-filter=".form-filter"
                    data-test="true"
                >
                    <thead>
                        <tr class="bg-inverse">
                            <th width="5%"></th>
                            <th width="5%">No</th>
                            <th><?=Yii::t('fe', 'Nik'); ?></th>
                            <th><?=Yii::t('fe', 'Nama Dokter'); ?></th>
                            <th><?=Yii::t('fe', 'Nama Spesialis'); ?></th>
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
        table = $('#table-dokter').docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets: 0
            }],
            select: {
                style: 'os',
                selector: 'tr'
            },
            sorting: [[2, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            // scrollX: true,
            // fixedColumns: {
            //     leftColumns: 1
            // },
            ajax: baseUrl+'master/dokter/get-data',
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
                {title: '".(\Yii::t('fe', 'NIK'))."', data: 'nomorindukpegawai',  searchable:false},
                {title: '".(\Yii::t('fe', 'Nama Dokter'))."', data: 'nama_pegawai'},
                {title: '".(\Yii::t('fe', 'Nama Spesialis'))."', data: 'spesialis_nama', searchable: false},
            ]
        });
        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table,
            [
                [
                    3,
                    \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                        Html::textInput('nama_pegawai', '',
                                [
                                    'class' => 'form-control',
                                    'col-index'=>3,
                                    'placeholder'=> 'Nama Dokter',
                                ]
                            )
                        )
                    )."<div>\"
                ]
            ]
        );

        table.on( 'select', function ( e, dt, type, indexes ) {
            if ( type === 'row' ) {
                var data = table.rows( indexes ).data()[0].status_isi;
                var data_kamar = table.rows( indexes ).data()[0].kamarruangan_nokamar;
                
                if(data){
                    // console.log(data);
                    // console.log(data_kamar);
                    $('#data-delete').attr('disabled', true);
                    $('#data-edit').attr('disabled', true);
                }else{
                    $.ajax({
                        type: 'GET',
                        url: '/master/kamar/cek-data-kamar?kamarruangan_nokamar='+data_kamar,
                        success: function(response){
                            // console.log(response);
                            if (response=='kosong') {
                                $('#data-delete').attr('disabled', false);
                                $('#data-edit').attr('disabled', false);
                            }else{
                                $('#data-delete').attr('disabled', true);
                                $('#data-edit').attr('disabled', true);
                            }
                        }
                    });
                }
            }
        });

    });


    ", VIEW::POS_END, 'js-kunings');
?>

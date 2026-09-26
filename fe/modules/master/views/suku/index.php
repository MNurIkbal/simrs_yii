<?php
// Author : Ramdhan Nurrachman
// Modify : Naufal Ziyad L

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', 'Suku');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <?=Breadcrumbs::widget([
                    'homeLink' => [ 
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>
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
                                'action' => '/master/#/create',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-url' => '/master/#/update?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                            ]
                        ],
                        'pdf',
                        'excel',
                    ],'#table-suku');?>    
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form-suku"></div>
                </div>
                <table id="table-suku" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
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
 
<div id="modal_suku" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        </div>
    </div>
</div>

<script>
    var table = $('#table-suku').docoTabel({
        filter: true,
            sorting: [[1, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            // stateSave: true,
            //scrollX: true,
            ajax: baseUrl+"master/suku/get-data-suku",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "<?=Yii::t('fe', 'Nama Suku')?>",  data: "suku_nama"},
                {title: "<?=Yii::t('fe', 'Nama Lainnya')?>", data: "suku_namalainnya"},
                {title: "Status",  data: "is_active"}, 
                {
                    title: "<?=Yii::t('fe', 'Aksi')?>", 
                    data: "aksi",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                }
            ]
        });
     $(".dataTables_filter").hide();
     $(".filter-form-suku").datatableBootstrapFilter(table, 
            [
                [3, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'select2', 'prompt' => \Yii::t('fe', '--Pilih--')])));?>']
            ]
        );

    

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });
</script>
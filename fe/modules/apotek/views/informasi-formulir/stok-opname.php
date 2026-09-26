<?php
/**
 * @author Randy Vianda Putra
 * @copyright 18 January 2018 aweutist
 */
 
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("instalasi_name"), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss('
.pickadate{
    top:187px !important;
}
');
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
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset',
                    'detail' => [
                        'title'=>\Yii::t('fe', 'Lihat'),
                        'attributes' => [
                            'url' => $default_url .'/view?id=',
                        ]
                    ],
                    'custom-stokopname' => [
                        'type' => 'button',
                        'title' => Yii::t('fe', 'Stok opname'),
                        'icon' => 'fa fa-shopping-cart',
                        'attributes' => [
                            'data-target' => $default_url .'/detail?id=',
                        ]
                    ],
                    'delete' => [
                        'attributes' => [
                            'url' => $default_url .'/delete?id=',
                            'data-additional' => 'data-rm',
                        ]
                    ],
                ]);?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1"><?=\Yii::t("fe", "Rownum");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Formulir");?></th>
                            <th><?=\Yii::t("fe", "No Formulir");?></th>
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

$this->registerCss($this->render('../assets/css/apotek.css'));
$this->registerJs($this->render("js/informasi_formulir.js"));
$this->registerJs("
        //global variable
        var table;

        $(document).ready(function(){
            table = $('#example').docoTabel({
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
                filter: true,
                sorting: [[2,'desc']],
                displayLength: 10,
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: baseUrl+'apotek/informasi-formulir/get-data-stok',
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
                        orderable: false,
                    },
                    {
                        title: '".\Yii::t("fe", "Tanggal Formulir")."',
                        data: 'tglformulir',
                    },
                    {
                        title: '".\Yii::t("fe", "No Formulir")."',
                        data: 'noformulir',
                    },
                ],
            });

            $('.dataTables_filter').hide();
            
            $('.filter-form').datatableBootstrapFilter(table, [
                [
                    2, 
                    \"<div class='input-group'><input type='text' id='rangeDemoStart' readonly='readonly' value=".date('d-M-Y')." class='form-control startDate' /><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' value=".date('d-M-Y')." class='form-control endDate' readonly='readonly' /><input type='text' style='display:none' class='targetDate' col-index=2 readonly='true'></div>\"
                ],
            ]);
            dateRangeHelper('.startDate','.endDate','.targetDate');
        });


        ");

?>
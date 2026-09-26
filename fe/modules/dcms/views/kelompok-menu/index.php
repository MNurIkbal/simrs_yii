<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\modules\master\models\EsselonForm;
use Doco\master\controllers\EsselonController;

$this->params['breadcrumbs'][] = ['label' => 'DCMS', 'url' => ['index']];
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
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        'add' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => '/dcms/kelompok-menu/create',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-options'=>'modal',
                                'data-target'=>'#modal_backdrop',
                                'data-url' => '/dcms/kelompok-menu/update?id=',
                            ]
                        ],
                        'delete',
                        'pdf',
                        'excel',
                    ], '#data-pendidikan');?>

            </div>

            <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12 filter-form"></div>
                    </div>
                    <table class="table datatable-basic table-striped table-hover dataTable no-footer"
                    id="data-pendidikan"  style="width: 100%"
                        >
                        <thead>
                            <tr class="bg-inverse">
                                <th width="1"></th>
                                <th width="1"><?= Yii::t('fe','No') ?></th>
                                <th><?= Yii::t('fe','Nama') ?></th>
                                <th><?= Yii::t('fe','Status') ?></th>
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
$this->registerJs('
    // Event Ready
    $(document).ready(function() {
        // Generate Table
        tableJenisKertas = $("#data-pendidikan").docoTabel({
            filter: true,
            columnDefs: [
                {
                    orderable: false,
                    className: "select-checkbox",
                    targets:   0
                },
                {
                    orderable: false,
                }
            ],
            select: {
                style:    "os",
                selector: "tr"
            },
            sorting: [[2, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"dcms/kelompok-menu/get-data",
            columns: [
                {
                    data : null,
                    render : function ( data, type, full, meta ) {
                        return null;
                    },
                    searchable: false,
                    orderable: false
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Nama")).'",
                    data: "kelmenu_nama"
                },
                {
                    title: "'.(\Yii::t("fe", "Status")).'",
                    data: "is_active"
                },
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(tableJenisKertas, [[3, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '--Semua--')]))).'\']]);
    })
',View::POS_END,'kelompok-menu');
?>

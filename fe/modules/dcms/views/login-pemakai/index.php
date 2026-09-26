<?php
// Author : Ramdhan Nurrachman

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
$this->title = $title;
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
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        'tambah' => [
                            'type' => 'link',
                            'title' => \Yii::t('fe', 'Tambah'),
                            'icon' => 'fa fa-plus',
                            'method' => 'not exist',
                            'attributes' => [
                                'class' => 'data-tambah',
                                'id' => 'tambah',
                                'data-target' => '/dcms/login-pemakai/create',
                                'data-options' => 'link',

                            ]
                        ],
                        'ubah' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Ubah'),
                            'icon' => 'fa fa-pencil',
                            'method' => 'not exist',
                            'attributes' => [
                                'class' => 'data-ubah',
                                'data-target' => '/dcms/login-pemakai/update?id=',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                            ]
                        ],
                    ],"#table-loginpemakai");?>

            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="table-loginpemakai" class="table table-bordered table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?= \Yii::t("fe", "Nama Pemakai") ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="3">Data tidak ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="modal_backdrop" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        </div>
    </div>
</div>
<?php
$this->registerJs('
    // Global Var
    var tableLoginPemakai;
    // Event Ready
    $(document).ready(function() {
        // Generate Table
        tableLoginPemakai = $("#table-loginpemakai").docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            sorting: [[1, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            stateSave: true,
            ajax: baseUrl+"dcms/login-pemakai/get-data",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Nama Pemakai")).'",  data: "nama_pemakai"},
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(tableLoginPemakai);
    });

', View::POS_END, 'b-index');
?>

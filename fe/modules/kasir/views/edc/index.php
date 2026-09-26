<?php
/**
 * @author: [Budi][budi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use kartik\widgets\DepDrop;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
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
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset'=> [
                        'attributes'=>[
                            'data-parent' => '.filter-form'
                        ]
                    ],
                    'add' => [
                        'attributes' => [
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => '/kasir/edc/create',
                        ]
                    ],
                    'edit' => [
                        'attributes' => [
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-url' => '/kasir/edc/update?id=',
                        ]
                    ],
                ]) ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th><?= Yii::t('fe', 'No') ?></th>
                            <th><?= Yii::t("fe", "Kode EDC") ?></th>
                            <th><?= Yii::t("fe", "Nama Mesin EDC") ?></th>
                            <th><?= Yii::t("fe", "Bank") ?></th>
                            <th><?= Yii::t("fe", "Status") ?></th>
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
var table;
$(document).ready(function() {
    table = $("#example").docoTabel({
        filter: true,
        columnDefs: [ {
            orderable: false,
            className: "select-checkbox",
            targets:   0
        }],
        select: {
            style:    "os",
            selector: "tr"
        },
        sorting: [[2, "asc"]], 
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: baseUrl+"kasir/edc/get-data",
        columns: [
            {
                title: "", 
                data: null, 
                defaultContent: "", 
                searchable: false, 
                orderable: false,
                width: "10%"
            },
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "'.(\Yii::t("fe", "Kode EDC")).'",  
                data: "edclist_kode",
            },
            {
                title: "'.(\Yii::t("fe", "Nama Mesin EDC")).'",  
                data: "edclist_namamesin",
            },
            {
                title: "'.(\Yii::t("fe", "Bank")).'",  
                data: "nama_bank",
            },
            {title: "Status", data: "is_active", class: "text-center"},
        ],
    });

    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
        [
            4, 
            \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('edclist_bank', '', ArrayHelper::map($bank, 'bank_id', 'nama_bank'), ['class' => 'form-control select2', 
            'prompt' => \Yii::t('fe', '— Pilih Bank — ')]))).'\'
        ],
        [
            5, 
            \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'form-control select2', 
            'prompt' => \Yii::t('fe', '— Pilih Status — ')]))).'\'
        ],
    ]);
});
', View::POS_END, 'b-index');
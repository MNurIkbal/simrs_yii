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
                            'action' => '/'.$module.'create',
                        ]
                    ],
                    'edit' => [
                        'attributes' => [
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-url' => '/'.$module.'update?id=',
                        ]
                    ],
                    'delete'=>[
                        'attributes'=>[
                            'data-additional'=>'data-rm',
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
                            <th><?= Yii::t("fe", "Kode") ?></th>
                            <th><?= Yii::t("fe", "Jenis Pembayaran") ?></th>
                            <th><?= Yii::t("fe", "Bank") ?></th>
                            <th><?= Yii::t("fe", "Status") ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="8"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
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
        ajax: baseUrl+"kasir/jenis-pembayaran-non-tunai/get-data",
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
                title: "'.(\Yii::t("fe", "Kode")).'",  
                data: "kode",
            },
            {
                title: "'.(\Yii::t("fe", "Jenis Pembayaran")).'",  
                data: "nama",
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
            \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('nama_bank', '', [], [
                'class' => 'form-control select2', 
                'id' => 'selectBank',
                'prompt' => \Yii::t('fe', '— Pilih Bank — ')]))).'\'
        ],
        [
            5, 
            \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'form-control select2', 
            'prompt' => \Yii::t('fe', '— Pilih Status — ')]))).'\'
        ],
    ]);
    $("#selectBank").docoPaginationSelec2(
        config = {
            placeholder : "-- Cari Bank --",  
            _api : "/kasir/jenis-pembayaran-non-tunai/get-data-bank",
        }
    )
});
', View::POS_END, 'b-index');
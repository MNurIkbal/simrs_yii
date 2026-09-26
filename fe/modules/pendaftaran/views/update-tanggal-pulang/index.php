<?php
/**
 * @Author: Ikhwanu Arriyadh T
 * @Date:   2022-02-14
 */

use app\components\DocoHelpers;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['/']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title">
                            <b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title) ?></b>
                        </h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params["breadcrumbs"])) ?>
                    </div>
                </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset',
                    'print-rujukan' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Print Rujukan'),
                        'icon' => 'fa fa-print',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id' => 'btn-print-rujukan',
                            'class' => 'btn-print-rujukan',
                            // 'data-options' => 'click',
                            'data-target' => Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-rujukan?rujukankontrol_id=',
                            'disabled' => true,
                            'data-pages' => '_blank',
                        ]
                    ],
                    'edit' => [
                        'title' => \Yii::t('fe', 'Update'),
                        'icon' => 'fa fa-plus',
                        'attributes' => [
                            'id' => 'data-edit',
                            'data-options' => false,
                            'disabled' => true,
                            'data-target' => '/pendaftaran/update-tanggal-pulang/update?id=',
                            'class' => 'btn btn-info btn-labeled btn-xs data-edit btn-toolbar'
                        ]
                    ],
                ], '#tb-update-tanggal-pulang') ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="advanced-filter"></div>
                    </div>
                </div>
                <table id="tb-update-tanggal-pulang" class="table table-striped" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th>&nbsp;</th>
                            <th><?= Yii::t("fe", "No") ?></th>
                            <th><?= Yii::t("fe", "No. SEP") ?></th>
                            <th><?= Yii::t("fe", "No. Rekam Medik") ?></th>
                            <th><?= Yii::t("fe", "No. Kartu") ?></th>
                            <th><?= Yii::t("fe", "Nama Pasien") ?></th>
                            <th><?= Yii::t("fe", "Tanggal Pulang") ?></th>
                            <th><?= Yii::t("fe", "Status Pulang") ?></th>
                            <th><?= Yii::t("fe", "No. Surat Kematian") ?></th>
                            <th><?= Yii::t("fe", "Tanggal Kematian") ?></th>
                            <th><?= Yii::t("fe", "Nomor Up Manual") ?></th>
                            <th><?= Yii::t("fe", "Bpjs ID") ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs('
    var date = "'.date('d-M-Y', strtotime('NOW')).'";
    var table;

    $(document).ready(function() {
        table = $("#tb-update-tanggal-pulang").docoTabel({
            filter: true,
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollCollapse: true,
            ajax: baseUrl + "pendaftaran/update-tanggal-pulang/get-data",
            sorting: [[6, "desc"]],
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets: 0,
                checkboxes: {
                    selectRow: true
                }
            }],
            select: {
                style: "os",
                selector: "tr"
            },
            columns: [
                {data: null, searchable: false, orderable: false, defaultContent: ""}, //0
                {title: "'.(\Yii::t("fe", "No")).'", data: "no", searchable: false, orderable: false}, //1
                {title: "'.(\Yii::t("fe", "No. SEP")).'", data: "nosep", searchable: true, orderable: false}, //2
                {title: "'.(\Yii::t("fe", "No. Rekam Medik")).'", data: "no_rekam_medik", orderable: false}, //3
                {title: "'.(\Yii::t("fe", "No. Kartu")).'", data: "nokartuasuransi", searchable: true, orderable: false}, //4
                {title: "'.(\Yii::t("fe", "Nama Pasien")).'", data: "nama_pasien", searchable: true, orderable: false}, //5
                {title: "'.(\Yii::t("fe", "Tanggal Pulang")).'", data: "tglpasienpulang", searchable: true, orderable: false}, //6
                {title: "'.(\Yii::t("fe", "Status Pulang")).'", data: "status_pulang_nama", searchable: false, orderable: false}, //7
                {title: "'.(\Yii::t("fe", "No. Surat Kematian")).'", data: "no_surat_kematian", searchable: false, orderable: false}, //8
                {title: "'.(\Yii::t("fe", "Tanggal Kematian")).'", data: "tgl_meninggal", searchable: false, orderable: false}, //9
                {title: "' . (\Yii::t("fe", "Nomor Up Manual")) . '", data: "no_up_manual", searchable: false, orderable: false}, //10
                {title: "' . (\Yii::t("fe", "BPJS ID")) . '", data: "bpjs_id", visible: false, searchable: false }, //11
            ],
        });

        $(".dataTables_filter").hide();

        $(".filter-form").datatableBootstrapFilter(table , [
            [6, 
                \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate dateStart1" value="' . date('d-M-Y') . '" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" readonly class="form-control endDate dateEnd1" value="' . date('d-M-Y') . '" /><input type="text" style="display:none" class="targetDate dateTarget1" col-index=2></div>\'
            ],
        ], {
            0:6,
            1:5,
            2:2,
            3:4
        });

        dateRangeHelper(".dateStart1",".dateEnd1",".dateTarget1");
    });

    $(document).on("click", "#tb-update-tanggal-pulang tbody tr", function () {
        var bpjs_id = null;
        $("#data-edit").attr("disabled", false);
        try {
            bpjs_id = table.row(".selected").data().bpjs_id ? table.row(".selected").data().bpjs_id : null;
            getInfoPeserta(bpjs_id);
        } catch (e) {
            bpjs_id = null;
        }
        
        if (bpjs_id != null ) {
            $("#btn-print-rencana").attr("disabled", false);
        } else {
            $("#btn-print-rencana").attr("disabled", true);
        }
    });

    function getInfoPeserta(bpjs_id) {  
        $.ajax({
            url: "/pendaftaran/update-tanggal-pulang/get-info-peserta",
            type: "GET",
            data: {
                bpjs_id: bpjs_id,
            },
            dataType: "JSON",
            success: function(response) {
                var can_update = response.can_update
                var error_message_1 = response.Error
                if (can_update == false) {
                    $("#data-edit").attr("disabled", true);
                    docoNotification("error", "Tidak bisa update", error_message_1);
                } else {
                    $("#data-edit").attr("disabled", false);
                }
            }
        })
    }
', View::POS_END, 'index');
?>

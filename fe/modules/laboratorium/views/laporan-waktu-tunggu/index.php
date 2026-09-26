<?php 

/**
 * @author Randy Vianda Putra
 * @todo Laporan Waktu Tunggu Lab
 * @copyright 01 Agustus 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
// use kartik\widgets\DepDrop;
// use kartik\widgets\Select2;
// use yii\web\JsExpression;
use yii\widgets\Breadcrumbs;
use app\components\DocoTableHelper;
// use yii\helpers\ArrayHelper;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Laboratorium', 'url' => ['/laboratorium']];
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
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        'pdf',
                        'excel'
                    ],'#table-waktutunggu-lab');
                ?>
            </div>

            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <div class="col-md-12">
                    <table 
                        class="table table-striped table-condensed table-hover" 
                        id="table-waktutunggu-lab" 
                        style="width:100%;"
                    >
                        <thead>
                            <tr class="bg-inverse">
                                <th width="1"><?=Yii::t('fe', 'No'); ?></th>
                                <th><?=Yii::t('fe', 'No pendaftaran'); ?></th>
                                <th><?=Yii::t('fe', 'No rekam medik'); ?></th>
                                <th><?=Yii::t('fe', 'Nama pasien'); ?></th>
                                <th><?=Yii::t('fe', 'Dokter'); ?></th>
                                <th><?=Yii::t('fe', 'Specimen'); ?></th>
                                <th><?=Yii::t('fe', 'Pemeriksaan'); ?></th>
                                <th><?=Yii::t('fe', 'Tanggal pendaftaran'); ?></th>
                                <th><?=Yii::t('fe', 'Tanggal specimen'); ?></th>
                                <th><?=Yii::t('fe', 'Tanggal hasil pemeriksaan'); ?></th>
                                <th><?=Yii::t('fe', 'Tanggal expertise'); ?></th>
                                <th><?=Yii::t('fe', 'Waktu tunggu'); ?> <br>(Specimen - Expertise)</th>
                                <th><?=Yii::t('fe', 'Waktu tunggu'); ?> <br>(Pendaftaran - Expertise)</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $("#table-waktutunggu-lab").docoTabel({
            filter: true,
            scrollX: true,
            sorting: [[1, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"laboratorium/laporan-waktu-tunggu/get-data",
            scrollCollapse: true,
            // fixedColumns: {
            //     leftColumns: 3,
            // },
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "No pendaftaran")).'", data: "no_pendaftaran"},
                {title: "'.(\Yii::t("fe", "No rekam medik")).'", data: "no_rekam_medik"},
                {title: "'.(\Yii::t("fe", "Nama pasien")).'", data: "nama_pasien"},
                {title: "'.(\Yii::t("fe", "Dokter")).'", data: "dokter", name: "dokter"},
                {title: "'.(\Yii::t("fe", "Specimen")).'", data: "nama_sample", name: "nama_sample"},
                {title: "'.(\Yii::t("fe", "Pemeriksaan")).'", data: "pemeriksaanlab_nama", name: "pemeriksaanlab_nama"},
                {title: "'.(\Yii::t("fe", "Tanggal pendaftaran")).'", data: "tglmasukpenunjang"},
                {title: "'.(\Yii::t("fe", "Tanggal specimen")).'", data: "tgl_ambilsample", searchable: false},
                {title: "'.(\Yii::t("fe", "Tanggal hasil pemeriksaan")).'", data: "tgl_hasilpemeriksaanlab", searchable: false},
                {title: "'.(\Yii::t("fe", "Tanggal expertise")).'", data: "tgl_expertise", searchable: false},
                {title: "'.(\Yii::t("fe", "Waktu tunggu")).' <br>(Specimen - Expertise)", data: "waktu_tunggu_sample", searchable: false},
                {title: "'.(\Yii::t("fe", "Waktu tunggu")).' <br>(Pendaftaran - Expertise)", data: "waktu_tunggu_daftar", searchable: false},
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                7,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" value="'. date('d-M-Y') .'"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" readonly class="form-control endDate" value="'. date('d-M-Y') .'"/><input type="text" style="display:none" class="targetDate" col-index=2></div>\'
            ], 
            [   4,
            \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('dokter', '',[], ['class' => 'form-control select2 selectDokter', 'prompt' => "" ]))).'\' 
            ],
            [   5,
            \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('specimen', '',[], ['class' => 'form-control select2 selectSpecimen','id'=>'select-specimen', 'prompt' => "" ]))).'\' 
            ],
            [   6,
            \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('pemeriksaan', '',[], ['class' => 'form-control select2 selectPemeriksaan','id'=>'select-pemeriksaan', 'prompt' => "" ]))).'\' 
            ],
        ], {7:0}, true);
        dateRangeHelper(".startDate",".endDate",".targetDate");
        $(".selectSpecimen").select2({
            placeholder: "",
            minimumInputLength: 3,
            ajax: {
                url: "'.Url::to(['get-specimen']).'",
                dataType: "json",
                quietMillis: 250,
                processResults: function (data) {
                    return {
                        results: data.result
                    };
                }
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        });
        $(".selectPemeriksaan").select2({
            placeholder: "",
            minimumInputLength: 3,
            ajax: {
                url: "'.Url::to(['get-pemeriksaan']).'",
                dataType: "json",
                quietMillis: 250,
                processResults: function (data) {
                    return {
                        results: data.result
                    };
                }
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        });
        $(".selectDokter").select2({
            placeholder: "",
            minimumInputLength: 3,
            ajax: {
                url: "'.Url::to(['get-dokter']).'",
                dataType: "json",
                quietMillis: 250,
                processResults: function (data) {
                    return {
                        results: data.result
                    };
                }
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        });
    });
    ', View::POS_END, 'js');
?>
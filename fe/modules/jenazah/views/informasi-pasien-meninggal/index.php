<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-09-06 10:35:25
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-12-18 10:35:23
 */
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 
'url' => ['index']];
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
                      <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
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
                    'str' => [
                        'title' => \Yii::t('fe', 'Serah Terima Ruangan'),
                        'icon' => 'fa fa-folder-open',
                        'attributes' => [
                            'id'=>'btn-str',
                            'data-target' => '/jenazah/informasi-pasien-meninggal/serah-terima-ruangan?id=',
                        ]
                    ],
                    'stk' => [
                        'title' => \Yii::t('fe', 'Serah Terima Keluarga'),
                        'icon' => 'fa fa-folder-open',
                        'attributes' => [
                            'id'=>'btn-stk',
                            'data-target' => '/jenazah/informasi-pasien-meninggal/serah-terima-keluarga?id=',
                        ]
                    ],
                    'proses' => [
                        'title' => \Yii::t('fe', 'Proses'),
                        'icon' => 'fa fa-cog',
                        'attributes' => [
                            'id'=>'btn-proses',
                            'data-target' => '/jenazah/informasi-pasien-meninggal/proses?id=',
                        ]
                    ],
                    'lihat' => [
                        'title' => \Yii::t('fe', 'Detail'),
                        'icon' => 'fa fa-eye',
                        'attributes' => [
                            'id'=>'btn-lihat',
                            'data-pages' => '_blank',
                            // 'data-target' => '/jenazah/informasi-pasien-meninggal/detail?id=',
                        ]
                    ],
                    'excel' => [
                        'attributes' => [
                            'data-target' => '/jenazah/informasi-pasien-meninggal/export-excel?'
                        ]
                    ],
                ],'#example');?>
            </div>
            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th>No</th>
                            <th><?=\Yii::t("fe", "Tanggal Meninggal");?></th>
                            <th><?=\Yii::t("fe", "Nama Jenazah");?></th>
                            <th><?=\Yii::t("fe", "No Rekam Medik");?></th>
                            <th><?=\Yii::t("fe", "Jenis Kelamin");?></th>
                            <th><?=\Yii::t("fe", "Ruangan Asal");?></th>
                            <th><?=\Yii::t("fe", "Penyebab Meninggal Dunia");?></th>
                            <th><?=\Yii::t("fe", "Nama Penanggung Jawab");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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
            sorting: [[2, "desc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"jenazah/informasi-pasien-meninggal/get-data",
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
                {title: "'.(\Yii::t("fe", "Tanggal Meninggal")).'", data: "tgl_meninggal"},
                {title: "'.(\Yii::t("fe", "Nama Jenazah")).'", data: "nama_pasien"},
                {title: "'.(\Yii::t("fe", "No Rekam Medik")).'", data: "no_rekam_medik"},
                {title: "'.(\Yii::t("fe", "Jenis Kelamin")).'", data: "jenis_kelamin"},
                {title: "'.(\Yii::t("fe", "Ruangan Asal")).'", data: "ruangan_nama"},
                {title: "'.(\Yii::t("fe", "Penyebab Meninggal Dunia")).'", data: "diagnosa_nama", searchable:false},
                {title: "'.(\Yii::t("fe", "Nama Penanggung Jawab")).'", data: "penanggungjawab_nama"},
                {title: "'.(\Yii::t("fe", "Status")).'", data: "status_periksa_nama"},
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
			[
                2, 
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date('d-M-Y').'" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" readonly="" id="rangeDemoFinish" value="'.date('d-M-Y').'" class="form-control endDate" /><input type="text" style="display:none" class="targetDate" col-index=4 readonly="true"></div>\'
            ],
            [6, \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('ruangan_nama', '', 
                ArrayHelper::map($response['ruangan'], 'ruangan_id', 'ruangan_nama'), ['class' => 'form-control select2', 'id' => 'ruangan_nama', 'prompt' => Yii::t('fe', '--Pilih Ruangan Asal--') ]))).'\' 
            ],
            [9, \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('status_periksa_nama', '', 
                ArrayHelper::map($response['status_jenazah'], 'lookup_id', 'lookup_name'), ['class' => 'form-control select2', 'id' => 'status_periksa_nama', 'prompt' => Yii::t('fe', '--Pilih Status Jenazah--') ]))).'\' 
            ],
            [5, \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('jenis_kelamin', '', 
                ArrayHelper::map($response['jenis_kelamin'], 'lookup_id', 'lookup_name'), ['class' => 'form-control select2', 'id' => 'jenis_kelamin', 'prompt' => Yii::t('fe', '--Pilih Jenis Kelamin--') ]))).'\' 
            ],
        ], {
		2:0,
		3:1,
		4:2,
		6:3,
		9:4,
		5:5,
		8:6,
        });
        
        dateRangeHelper(".startDate",".endDate",".targetDate");
    });

    $(document).on("click", "#example tr", function(){
        var tbl = table.row(".selected").data();
        if(tbl.status_periksa == 581){
            $("#btn-lihat").attr("data-target", "/jenazah/informasi-pasien-meninggal/cetak-belum-diterima?pendaftaran_id=");
            $("#btn-str").prop("disabled", false);
            $("#btn-stk").prop("disabled", true);
            $("#btn-proses").prop("disabled", true);
        }else if(tbl.status_periksa == 577){
            $("#btn-lihat").attr("data-target", "/jenazah/informasi-pasien-meninggal/cetak-detail-serah-terima?pendaftaran_id=");
            $("#btn-str").prop("disabled", true);
            $("#btn-stk").prop("disabled", true);
            $("#btn-proses").prop("disabled", false);
        }else if(tbl.status_periksa == 578){
            $("#btn-lihat").attr("data-target", "/jenazah/informasi-pasien-meninggal/cetak-proses?pendaftaran_id=");
            $("#btn-str").prop("disabled", true);
            $("#btn-stk").prop("disabled", false);
            $("#btn-proses").prop("disabled", true);
        }else {
            $("#btn-lihat").attr("data-target", "/jenazah/informasi-pasien-meninggal/cetak-serah-terima-keluarga?pendaftaran_id=");
            $("#btn-str").prop("disabled", true);
            $("#btn-stk").prop("disabled", true);
            $("#btn-proses").prop("disabled", true);
        }
    });
', View::POS_END, 'b-index');
?>
<?php
/*
 * @Author: metafiliana 
 * @Date: 2018-01-26 10:50:44 
 * @Last Modified by:   afil
 * @Last Modified time: 2018-03-02 17:15:46
 * @Description: 
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', 'Laporan');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rawat jalan'), 'url' => ['/rajal/dashboard']];
$this->params['breadcrumbs'][] = $sub_title;
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
                        <h3 class="panel-title"><b><?= $this->title ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>

            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset' => ['attributes' => ['data-parent' => '.filter-form']],
                    'custom-print' => [
                        'title' => Yii::t('fe', 'Cetak Per Pasien'),
                        'icon' => 'fa fa-file-pdf-o',
                        'attributes' => [
                            // 'data-options'=>'pdf',
                            // 'target'=>'_blank',
                            'class'=>'spa',
                            'id' => 'btn-diangnosa-pasien',
                            'data-target' => '/rajal/laporan-diagnosa-pasien/export-pdf-pasien?pendaftaran_id=',
                        ],
                    ],
                     /*'rincian' => [
                        'title' => Yii::t('fe', 'Cetak Rincian'),
                        'icon' => 'fa fa-file-pdf-o',
                        'attributes' => [
                            'id'=>'cetak-rincian-tagihan',
                            'data-options' => 'link',
                            'class'=>'spa',
                            'data-target' => '/rajal/pemeriksaan/export-pdf-rincian-tagihan?pendaftaran_id=',
                            'disabled'=>'true',
                            'target'=>'_blank'
                        ]
                    ],*/
                    'pdf',
                    'excel',
                ], '#data-laporan');?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <hr>
                    </div>
                </div>
                <table class=table table-striped table-condensed table-hover" style="width:100%" id="data-laporan" >
                    <thead>
                        <tr class="bg-inverse">
                            <th width="5%"></th>
                            <th >No</th>
                            <th width="15%"><?=Yii::t('fe', 'Tanggal Diagnosa')?></th>
                            <th width="20%"><?=Yii::t('fe', 'No. pendaftaran / No. Rekam Medik')?></th>
                            <th></th>
                            <th></th>
                            <th ><?=Yii::t('fe', 'Nama pasien')?></th>
                            <th ><?=Yii::t('fe', 'Klasifikasi diagnosa')?></th>
                            <th><?=Yii::t('fe', 'Kode diagnosa')?></th>
                            <th><?=Yii::t('fe', 'Nama diagnosa')?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="11"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
$this->registerJs('
    // Event Ready
    // Generate Table
    var tabel = $("#data-laporan").docoTabel({
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
        sorting: [[2, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        // scrollX: true,
        autoWidth : true, 
        ajax: baseUrl+"rajal/laporan-diagnosa-pasien/get-data",
        columns: [
            {
                title: "",
                data: null,
                defaultContent: "",
                searchable: false,
                orderable: false
            },
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {title: "'.(\Yii::t("fe", "Tanggal Diagnosa")).'", data: "tgl_diagnosa"},
            {title: "'.(\Yii::t("fe", "No. Pendaftaran / No. Rekam Medik")).'", data: "no_pendaftaran_rm",searchable: false},
            {title: "'.(\Yii::t("fe", "No pendaftaran")).'", data: "no_pendaftaran",visible: false},
            {title: "'.(\Yii::t("fe", "No rekam medik")).'", data: "no_rekam_medik",visible: false},
            {title: "'.(\Yii::t("fe", "Nama Pasien")).'", data: "nama_pasien"},
            {title: "'.(\Yii::t("fe", "Klasifikasi Diagnosa")).'", data: "klasifikasidiagnosa_nama"},
            {title: "'.(\Yii::t("fe", "Kode")).'", data: "diagnosa_kode"},
            {title: "'.(\Yii::t("fe", "Nama diagnosa")).'", data: "exp_nama_diagnosa", name: "diagnosa_nama"},
        ],
    });
    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(tabel, [
        [
            2,
            \'<div class="input-group"><input value='.date("d-M-Y").' type="text" id="rangeDemoStart" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input value='.date("d-M-Y").' type="text" id="rangeDemoFinish" class="form-control endDate" /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
        ],
        [4, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('no_pendaftaran', '', ['class' => 'form-control','placeholder'=>'No Pendaftaran']))).'\'],
        [5, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('no_rekam_medik', '', ['class' => 'form-control','placeholder'=>'No Rekam Medik']))).'\'],
        [6, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('nama_pasien', '', ['class' => 'form-control','placeholder'=>'Nama Pasien']))).'\'],
        [7, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('klasifikasidiagnosa_nama', '', ['class' => 'form-control','placeholder'=>'Klasifikasi Diagnosa']))).'\'],
        [8, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('diagnosa_kode', '', ['class' => 'form-control','placeholder'=>'Kode']))).'\'],
        [9, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('exp_nama_diagnosa', '', ['class' => 'form-control','placeholder'=>'Nama diagnosa']))).'\'],
    ], {
        2:0,
        4:1,
        5:2,
        6:3,
        7:4,
        8:5,
        9:6,
    });

    dateRangeHelper(".startDate",".endDate",".targetDate");

    $(document).ready(function() {

        $("#data-laporan tbody").on("click", "tr", function(){
            try {
                primaryKey = tabel.row(".selected").data().primary ? tabel.row(".selected").data().primary : null;
            } catch (e) {
                primaryKey = false;
            }


            if (primaryKey) {
                var and = "&";
                $("#btn-diangnosa-pasien").attr("action",$("#btn-diangnosa-pasien").data("target")+primaryKey);
                $("#btn-diangnosa-pasien").attr("data-options", "pdf");
                $("#btn-diangnosa-pasien").attr("value",primaryKey);
                $("#btn-diangnosa-pasien").attr("data-target",$("#btn-diangnosa-pasien").data("target")+primaryKey+and);
            } else {
                $("#btn-diangnosa-pasien").removeAttr("action");
                $("#btn-diangnosa-pasien").removeAttr("data-options");
                $("#btn-diangnosa-pasien").removeAttr("value");
            }
            
        });

    });

', View::POS_END, 'b-index');
?>
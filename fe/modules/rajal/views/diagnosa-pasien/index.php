<?php
/*
 * @Author: metafiliana 
 * @Date: 2018-01-26 10:50:44 
 * @Last Modified by: metafiliana
 * @Last Modified time: 2018-01-26 15:08:43
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
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b> - <?=$sub_title;?></h3>
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

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <hr>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <?=Html::a('<i class="fa fa-file-excel-o"></i> '.Yii::t('fe', 'Ekspor'), '#', [
                            'class' => 'btn btn-green btn-sm data-export-all',
                            'action' =>  Url::home().'rajal/diagnosa-pasien/export-all'
                        ]);?>
                        <?=Html::a('<i class="fa fa-file-pdf-o"></i> '.Yii::t('fe', 'Cetak'), '#', [
                            'class' => 'btn btn-crimson btn-sm data-export-all',
                            'action' =>  Url::home(). 'rajal/diagnosa-pasien/print-all'
                        ]);?>
                    </div>
                </div>
                <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed" 
                    id="data-laporan" 
                    style="width:100%"
                    >
                    <thead>
                        <tr class="bg-inverse">
                            <th>No</th>
                            <th><?=Yii::t('fe', 'tglmorbiditas')?></th>
                            <th><?=Yii::t('fe', 'no_pendaftaran')?></th>
                            <th><?=Yii::t('fe', 'no_rekam_medik')?></th>
                            <th><?=Yii::t('fe', 'nama_pasien')?></th>
                            <th><?=Yii::t('fe', 'kelompokdiagnosa_nama')?></th>
                            <th><?=Yii::t('fe', 'klasifikasidiagnosa_nama')?></th>
                            <th><?=Yii::t('fe', 'diagnosa_kode')?></th>
                            <th><?=Yii::t('fe', 'diagnosa_nama')?></th>
                            <th><?=Yii::t('fe', 'diagnosa_namalainnya')?></th>
                            <th><?=Yii::t('fe', 'diagnosa_katakunci')?></th>
                            <th><?=Yii::t('fe', 'Aksi')?></th>
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
    // Generate Table
    var tabel = $("#data-laporan").docoTabel({
        filter: true,
        sorting: [[0, "asc"]], 
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl+"rajal/diagnosa-pasien/get-data",
        columns: [
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {title: "'.(\Yii::t("fe", "tglmorbiditas")).'", data: "tglmorbiditas"},
            {title: "'.(\Yii::t("fe", "no_pendaftaran")).'", data: "no_pendaftaran"},
            {title: "'.(\Yii::t("fe", "no_rekam_medik")).'", data: "no_rekam_medik"},
            {title: "'.(\Yii::t("fe", "nama_pasien")).'", data: "nama_pasien"},
            {title: "'.(\Yii::t("fe", "kelompokdiagnosa_nama")).'", data: "kelompokdiagnosa_nama"},
            {title: "'.(\Yii::t("fe", "klasifikasidiagnosa_nama")).'", data: "klasifikasidiagnosa_nama"},
            {title: "'.(\Yii::t("fe", "diagnosa_kode")).'", data: "diagnosa_kode"},
            {title: "'.(\Yii::t("fe", "diagnosa_nama")).'", data: "diagnosa_nama"},
            {title: "'.(\Yii::t("fe", "diagnosa_namalainnya")).'", data: "diagnosa_namalainnya"},
            {title: "'.(\Yii::t("fe", "diagnosa_katakunci")). '", data: "diagnosa_katakunci"},
            {
                title: "' . (\Yii::t("fe", "Aksi")) . '",
                data: "aksi",
                searchable: false,
                orderable: false,
                class: "text-center"
            }
        ],
    });
    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(tabel, [
        [1, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('tglmorbiditas', '', ['class' => 'form-control daterange', 'placeholder' => \Yii::t('fe', 'tglmorbiditas')]))).'\'],
        [5, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('kelompokdiagnosa_nama', '', ArrayHelper::map($data_kelompokdiagnosa, 'kelompokdiagnosa_nama', 'kelompokdiagnosa_nama'), ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '--pilih kelompok diagnosa--')]))). '\'],
    ]);

    $(".reset-filter").on("click", function (e) {
        e.preventDefault();
        tabel.reset();
    });

    // Datepicker
    $(".daterange").daterangepicker({
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        locale: {
            format: "DD-MMMM-YYYY"
        }
    });
', View::POS_END, 'b-index');
?>

<?php
// Author : Budi
 
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
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
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"><?=\Yii::t("fe", "Rownum");?></th>
                            <th><?=\Yii::t("fe", "Tanggal pemeriksaan");?></th>
                            <th><?=\Yii::t("fe", "Kode diagnosa");?></th>
                            <th><?=\Yii::t("fe", "Nama diagnosa");?></th>
                            <th><?=\Yii::t("fe", "Klasifikasi diagnosa");?></th>
                            <th><?=\Yii::t("fe", "Jumlah kasus");?></th>
                            <th><?=\Yii::t("fe", "Instalasi");?></th>
                            <th><?=\Yii::t("fe", "Ruangan");?></th>
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
        <div class="form-group">
            <?= Html::button(Yii::t('fe', ' Print'), [
                'class' => 'btn btn-dodger-blue fa fa-print',
            ]);?>
            <?= Html::a(Yii::t('fe', ' Cetak PDF'), 'javascript:void(0);',
                [
                    'class' => 'btn btn-crimson fa fa-file-pdf-o', 
                    'onclick' => "_export_pdf(this.id,'.filter-form')",
                    'id' => 'pdf',
                    'data-sources' => $module."export-pdf"
                ]);
            ?>
            <?= Html::a(Yii::t('fe', ' Export Excel'), 'javascript:void(0);',
                [
                    'class' => 'btn btn-green fa fa-file-excel-o',
                    'onclick' => "_export_excel(this.id,'.filter-form')",
                    'id' => 'excel',
                    'data-sources' => $module."export-excel"
                ]);
            ?>
        </div>
    </div>
</div>

<!-- Untuk Kebutuhan Modal Global -->
<div id="modal_backdrop_search" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- End -->

<?php 
$this->registerCss('
.daterangepicker{
    // top:187px !important;
}
');
$this->registerJs('
    // Global Var
    var table;
    var data;

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        table = $("#example").docoTabel({
            filter: true,
            sorting: [[1, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"rm/lap-sepuluh-besar-penyakit/get-data",
            columns: [
                {
                data: "rowNum",
                name : "rowNum",
                searchable: false,
                orderable: false
            },
            {title: "'.(\Yii::t("fe", "Tanggal")).'", data: "tglmorbiditas", visible: false},
            {title: "'.(\Yii::t("fe", "Kode diagnosa")).'", data: "diagnosa_kode", searchable: false},
            {title: "'.(\Yii::t("fe", "Nama diagnosa")).'", data: "diagnosa_nama", searchable: false},
            {title: "'.(\Yii::t("fe", "Klasifikasi diagnosa")).'", data: "klasifikasidiagnosa_nama", searchable: false},
            {title: "'.(\Yii::t("fe", "Jumlah kasus")).'", data: "total", searchable: false},
            {title: "'.(\Yii::t("fe", "Jumlah kasus")).'", data: "instalasi_nama", visible: false},
            {title: "'.(\Yii::t("fe", "Jumlah kasus")).'", data: "ruangan_nama", visible: false},
            ],
        });

        table.on( \'xhr\', function () {
            data = table.ajax.params();
        });

        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, 
            [
                [
                    1, 
                    \'<div class="input-group"><span class="input-group-addon"><i class="icon-calendar22"></i></span><input type="text" class="form-control daterange-basic" value="" placeholder="'.(\Yii::t('fe', 'Tanggal pemeriksaan')).'" col-index="1"></div>\'
                ],
                [
                    6, 
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('instalasi_nama', '', 
                        ArrayHelper::map($api['response']['instalasi'], 'instalasi_nama', 'instalasi_nama'), [
                            'class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Instalasi')]))).'\'
                ],
                [
                    7, 
                    \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('ruangan_nama', '', 
                        ArrayHelper::map($api['response']['ruangan'], 'ruangan_nama', 'ruangan_nama'), [
                            'class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Ruangan')]))).'\'
                ],
            ], {
                1:1,
                6:2,
                7:3
            }, true
        );

        $(".daterange-basic").daterangepicker({
            // autoUpdateInput: false,
            startDate: "'.(date("01-m-Y")).'",
            endDate: "'.(date("d-m-Y")).'",
            applyClass: "bg-slate-600",
            cancelClass: "btn-default",
            locale: {
                format: "DD-MMMM-YYYY"
            }
        });
    });
', View::POS_END, 'b-index');
?>

<script>
var _export_excel = function (id, filler) {
    var form = $(filler);
    var target = $("#" + id).attr("data-sources");
    query = $.param(data);
    form.attr("action", target);
    form.attr("target", "_blank");
    window.open(target + "?" + query, "_blank");

    form.submit();
    form.attr("action", "");
};

var _export_pdf = function (id, filler) {
    var form = $(filler);
    var target = $("#" + id).attr("data-sources");
    query = $.param(data);
    form.attr("action", target);
    form.attr("target", "_blank");
    window.open(target + "?" + query, "_blank");

    form.submit();
    form.attr("action", "");
};

</script>
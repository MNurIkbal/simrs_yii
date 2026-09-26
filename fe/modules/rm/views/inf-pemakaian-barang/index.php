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
                            <th><?=\Yii::t("fe", "Tanggal Pemakaian");?></th>
                            <th><?=\Yii::t("fe", "Nama Penginput");?></th>
                            <th><?=\Yii::t("fe", "Nama Barang");?></th>
                            <th><?=\Yii::t("fe", "Qty");?></th>
                            <th><?=\Yii::t("fe", "Satuan");?></th>
                            <th><?=\Yii::t("fe", "Aksi");?></th>
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
            ajax: baseUrl+"rm/inf-pemakaian-barang/get-data",
            columns: [
                {
                data: "rowNum",
                name : "rowNum",
                searchable: false,
                orderable: false
            },
            {title: "'.(\Yii::t("fe", "Tanggal pemakaian")).'", data: "tgl_pemakaianbarang"},
            {title: "'.(\Yii::t("fe", "Nama penginput")).'", data: "nama_pegawai", searchable: false},
            {title: "'.(\Yii::t("fe", "Nama barang")).'", data: "barang_nama"},
            {title: "'.(\Yii::t("fe", "Qty")).'", data: "jumlah_pakai", searchable: false},
            {title: "'.(\Yii::t("fe", "Satuan")).'", data: "satuan_pakai", searchable: false},
            {
                data: "aksi",
                searchable: false,
                orderable: false,
                class: "text-center"
            }
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, 
            [
                [
                    1, 
                    \'<div class="input-group"><span class="input-group-addon"><i class="icon-calendar22"></i></span><input type="text" class="form-control daterange-basic" value="" placeholder="'.(\Yii::t('fe', 'Tanggal pemakaian')).'" col-index="1"></div>\'
                ],
                [
                    2, 
                    \'<div class="input-group">'.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('barang_nama', NULL, ArrayHelper::map($barang['response']['data'], 'barang_nama', 'barang_nama'), ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Nama barang'), "col-index" => "2"]))).'<span class="input-group-addon"><span class="cursor-pointer" action="'.Url::home().'rm/inf-pemakaian-barang/search" data-toggle="modal" data-target="#modal_backdrop_search"><i class="fa fa-list"></i> <i class="fa fa-search"></i></span></span></div>\'
                ],
            ], {
                1:0,
                2:1,
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
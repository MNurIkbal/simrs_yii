<?php
// Author : Budi
// Date : 15 Januari 2018
 
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss('
.pickadate{
    top:187px !important;
}
');
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
            <div class="panel-toolbar clearfix">
                <?= Html::button('<b><i class="fa fa-search"></i></b>'.Yii::t('fe', 'Cari'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                    ]);
                ?>
                <?= Html::button('<b><i class="fa fa-refresh"></i></b>'.Yii::t('fe', ' Muat Ulang'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                    ]);
                ?>
                <?= Html::button('<b><i class="fa fa-print"></i></b>'.Yii::t('fe', ' Print'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                    ]);
                ?>
                <?= Html::a('<b><i class="fa fa-file-pdf-o"></i></b>'.Yii::t('fe', ' Cetak PDF'), 'javascript:void(0);',
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'onclick' => "_export_pdf(this.id,'.filter-form')",
                        'id' => 'pdf',
                        'data-sources' => "/rm/lap-kunjungan/export-pdf"
                    ]);
                ?>
                <?= Html::a('<b><i class="fa fa-file-excel-o"></i></b>'.Yii::t('fe', ' Unduh Excel'), 'javascript:void(0);',
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'onclick' => "_export_excel(this.id,'.filter-form')",
                        'id' => 'excel',
                        'data-sources' => "/rm/lap-kunjungan/export-excel"
                    ]);
                ?>
            
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form">
                        <?= Yii::$app->controller->renderPartial('_search', array(
                            'instalasi' => $instalasi,
                            'ruangan' => $ruangan,
                        )) ?>
                    </div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"><?=\Yii::t("fe", "Rownum");?></th>
                            <th><?=\Yii::t("fe", "Nama Obat Alkes");?></th>
                            <th><?=\Yii::t("fe", "Stok Sistem");?></th>
                            <th><?=\Yii::t("fe", "Stok Fisik");?></th>
                            <th><?=\Yii::t("fe", "Kondisi");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Kadaluarsa");?></th>
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
        <div class="btn-group pull-left">
                <?= Html::submitButton('<i class="fa fa-floppy-o"></i> '.\Yii::t('fe', 'Simpan').'', [
                    'class' => 'btn bg-teal',
                ]);?>
                <!-- <?//= Html::button('<i class="fa fa-file-pdf-o"></i> '.\Yii::t('fe', 'Cetak').'', [
                    //'class' => 'btn btn-crimson',
                //]); ?>
                 --><?= Html::a('<i class="fa fa-info"></i> '.\Yii::t('fe', 'Petunjuk').'', '#', [
                    'class' => 'btn btn-green',
                    // 'action' =>  Url::home().'master/cara-bayar/export-all'
                ]);?>
            </div>
    </div>
</div>
<?php 
$this->registerJs('
    // Global Var
    var table;

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    // Event Delete
    $(document).on("click", ".data-delete", function(e) {
        e.preventDefault();
        $(this).docoForm("delete",{
            success : function (data) {
                table.draw()
            }
        });
        return false;
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
            stateSave: true,
            scrollX: false,
            ajax: baseUrl+"rm/stok-opname/get-data",
            columns: [
                {
                    data: "rowNum",
                    name : "rowNum",
                    searchable: false,
                    orderable: false
                },
                {data: "nama_obat", name: "nama_obat"},
                {data: "stok_sistem", name: "stok_sistem"},
                {data: "stok_fisik", name: "stok_fisik"},
                {data: "kondisi", name: "kondisi"},
                {data: "tanggal_kadaluarsa", name: "tanggal_kadaluarsa"},
            ],
            scrollCollapse: false,
            // fixedColumns: {
            //     leftColumns: 2,
            // },

            "initComplete": function(settings, json) {
                $(".pickadate").pickadate();
            }
        });
        $(".dataTables_filter").hide();

        
    });
', View::POS_END, 'b-index');
?>
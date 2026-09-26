<?php
// Author : Ardi Pratama

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

$this->title = \Yii::t('fe', 'Laporan Kunjungan Rawat Jalan');
$this->params['breadcrumbs'][] = ['label' => 'Rm', 'url' => ['index']];
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
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Tanggal Pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "No Pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "No Rekam Medik");?></th>
                            <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th><?=\Yii::t("fe", "Jenis Kelamin");?></th>
                            <th><?=\Yii::t("fe", "Golongan Umur");?></th>
                            <th><?=\Yii::t("fe", "Agama");?></th>
                            <th><?=\Yii::t("fe", "Status Perkawinan");?></th>
                            <th><?=\Yii::t("fe", "Pekerjaan");?></th>
                            <th><?=\Yii::t("fe", "Alamat");?></th>
                            <th><?=\Yii::t("fe", "Kota/Kab");?></th>
                            <th><?=\Yii::t("fe", "Kunjungan");?></th>
                            <th><?=\Yii::t("fe", "Jenis kasus penyakit");?></th>
                            <th><?=\Yii::t("fe", "Cara Bayar");?></th>
                            <th><?=\Yii::t("fe", "Poliklinik");?></th>
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
    // Global Var
    var table;

    // Event Reload
    $(document).on("switchChange.bootstrapSwitch", ".change-status", function (e, state) {

        var dataStatus = "0";
        var dataId = $(this).attr("data-id");

        if (e.target.checked == true)
            dataStatus = "1";
        
        $(this).docoForm("delete",{
            url: baseUrl+"master/cara-bayar/change-status?id="+dataId+"&status="+dataStatus,
            confirmTitle : "'.(\Yii::t("fe", "Konfirmasi")).'",
            confirmMessage : "'.(\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")).'",
            success : function (data) {
                table.draw();
            }
        });
        table.draw();
    });

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    // Event Delete
    $(document).on("click", ".data-delete", function(e) {
        e.preventDefault();
        $(this).docoForm("delete",{
            additional: "data-rm",
            success : function (data) {
                table.draw()
            }
        });
        return false;
    });

    // Event Ready
    $(document).ready(function() {
        $(function(){
            $(".pickadate").pickadate({
                formatSubmit: "yyyy-mm-dd",
            });
        })

        // Generate Table
        table = $("#example").docoTabel({
            filter: true,
            sorting: [[1, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: true,
            scrollX: true,
            ajax: baseUrl+"laporan/kunjungan-rawat-jalan/get-data",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Tanggal Pendaftaran")).'", data: "dummy_tanggal"},
                {title: "'.(\Yii::t("fe", "No Pendaftaran")).'", data: "dummy_nopendaftaran"},
                {title: "'.(\Yii::t("fe", "No Rekam Medik")).'", data: "dummy_norm"},
                {title: "'.(\Yii::t("fe", "Nama Pasien")).'", data: "dummy_nama",searchable: false},
                {title: "'.(\Yii::t("fe", "Jenis Kelamin")).'", data: "dummy_kelamin",searchable: false},
                {title: "'.(\Yii::t("fe", " Golongan Umur")).'", data: "dummy_golumur",searchable: false},
                {title: "'.(\Yii::t("fe", "Agama")).'", data: "dummy_agama",searchable: false},
                {title: "'.(\Yii::t("fe", "Status Perkawinan")).'", data: "dummy_statuskawin",searchable: false},
                {title: "'.(\Yii::t("fe", "Pekerjaan")).'", data: "dummy_pekerjaan",searchable: false},
                {title: "'.(\Yii::t("fe", "Alamat")).'", data: "dummy_alamat",searchable: false},
                {title: "'.(\Yii::t("fe", "Kota/Kab")).'", data: "dummy_kota",searchable: false},
                {title: "'.(\Yii::t("fe", "Kunjungan")).'", data: "dummy_kunjungan",searchable: false},
                {title: "'.(\Yii::t("fe", "Jenis kasus penyakit")).'", data: "dummy_jeniskasus",searchable: false},
                {title: "'.(\Yii::t("fe", "Cara Bayar")).'", data: "dummy_carabayar",searchable: false},
                {title: "'.(\Yii::t("fe", "Poliklinik")).'", data: "dummy_poly",searchable: false},
                
            ],
            scrollCollapse: true,
            fixedColumns: {
                leftColumns: 2,
            }
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, 
            [
                [1, \''.(preg_replace("/[\n\t\r]/i", '', 
                    Html::textInput('dummy_ruangan', '', ['class' => 'form-control pickadate','placeholder'=>\Yii::t('fe', 'Tanggal Pendaftaran')])
                    )).'\'],
                [2, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('dummy_ruangan', '', $carabayar, ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', 'Pilih Cara Bayar')]))).'\'],
                [3, \''.(preg_replace("/[\n\t\r]/i", '', Html::checkboxList('dummy_ruangan', '', $ruangan, []))).'\'],
            ]
        );
    });
', View::POS_END, 'b-index');
?>

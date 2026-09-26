<?php
// Author : Ardi Pratama
 
use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = $title;
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
                    <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-options' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'data-url' => '/rajal/inf-pembebasan-tarif/update?id=',
                            ]
                        ],
                        'cancel' => [
                            'attributes' => [
                                'data-url' => '/rajal/inf-pembebasan-tarif/delete?id='
                            ]
                        ],
                        'custom-pdf' => [
                            'title' => Yii::t('fe', 'Cetak pdf'),
                            'icon' => 'fa fa-file-pdf-o',
                            'type'=>'button',
                            'attributes' => [
                                'data-target' => '/rajal/inf-pembebasan-tarif/export-pdf?id='
                            ]
                        ],
                    ], '#table_pembebasan_tarif');
                ?>           

            </div>
            <div class="panel-body">
                <div class="row">
                <div class="col-md-12 filter-form">
                </div>
                </div>
                <table id="table_pembebasan_tarif" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Tanggal Pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "No. RM");?></th>
                            <th><?=\Yii::t("fe", "No. Pendaftaran");?></th>
                            <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                            <th><?=\Yii::t("fe", "Kelas Pelayanan");?></th>
                            <th><?=\Yii::t("fe", "Jenis Kasus Penyakit");?></th>
                            <th><?=\Yii::t("fe", "Dokter");?></th>
                            <th><?=\Yii::t("fe", "No Transaksi");?></th>
                            <th><?=\Yii::t("fe", "Total Tagihan");?></th>
                            <th><?=\Yii::t("fe", "Total Tarif Yang Dibebaskan");?></th>
                            <th><?=\Yii::t("fe", "Catatan");?></th>
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
<div id="modal_backdrop" class="modal fade" style="z-index: 1064" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
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

    // Event Ready
    $(document).ready(function() {



    	$(function(){
            $(".daterange").daterangepicker({
                applyClass: "bg-slate-600",
                cancelClass: "btn-default",
                locale: {
                    format: "DD MMM YYYY"
                }
            });


        })   
        // Generate Table
        table = $("#table_pembebasan_tarif").docoTabel({
            filter: true,
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "td:first-child"
            },
            order: [[ 1, "asc" ]],
            sorting: [[1, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"rajal/inf-pembebasan-tarif/get-data",
            columns: [
                {
                    title: "Nomor",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Tanggal Pendaftaran")).'", data: "tgl_pendaftaran"},
                {title: "'.(\Yii::t("fe", "No. RM")).'", data: "no_rekam_medik"},
                {title: "'.(\Yii::t("fe", "No. Pendaftaran")).'", data: "no_pendaftaran"},
                {title: "'.(\Yii::t("fe", "Nama Pasien")).'", data: "nama_pasien"},
                {title: "'.(\Yii::t("fe", "Kelas Pelayanan")).'", data: "kelaspelayanan_nama"},
                {title: "'.(\Yii::t("fe", "Jenis Kasus Penyakit")).'",  data: "jeniskasuspenyakit_nama"},
                {title: "'.(\Yii::t("fe", "Dokter")).'", data: "nama_dokter"},
                {title: "'.(\Yii::t("fe", "No Transaksi")).'", data: "no_pembebasantarif"},
                {title: "'.(\Yii::t("fe", "Total Tagihan")).'", data: "total_tagihan"},
                {title: "'.(\Yii::t("fe", "Total Tarif Yang Dibebaskan")).'", data: "total_pembebasantarif"},
                {title: "'.(\Yii::t("fe", "Catatan")).'", data: "catatan"},
                {title: "'.(\Yii::t("fe", "Status")).'", data: "status_pembebasantarif"}
            ],
            // scrollCollapse: true,
            fixedColumns: {
                leftColumns: 3,
            }
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
                [1, \''.(preg_replace("/[\n\t\r]/i", '', 
                  Html::textInput('tgl_pendaftaran', '', ['class' => 'form-control daterange','placeholder'=>\Yii::t('fe', 'Tanggal Pendaftaran')])
                  )).'\'],
                [5, \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('kelaspelayanan', '', $ddlKelasPelayanan, ['class' => 'form-control select2', 'id' => 'kelaspelayanan', 'prompt' => Yii::t('fe', '--Pilih Kelas Pelayanan--') ]))).'\' ],
                [6, \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('jeniskasuspenyakit', '', $ddlJenisKasusPenyakit, ['class' => 'form-control select2', 'id' => 'jeniskasuspenyakit', 'prompt' => Yii::t('fe', '--Pilih Jenis Kasus Penyakit--') ]))).'\' ],
                [7, \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('dokter', '', $ddlDokter, ['class' => 'form-control select2', 'id' => 'dokter', 'prompt' => Yii::t('fe', '--Pilih Dokter--') ]))).'\' ],
                [12, \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('status_pembebasantarif', '', $ddlStatusbayar, ['class' => 'form-control select2', 'id' => 'status_pembebasantarif', 'prompt' => Yii::t('fe', '--Pilih status--') ]))).'\' ],
             ]);

        // Options
	    var oneDay = 24*60*60*1000;
	    var rangeDemoFormat = "%e-%b-%Y";
	    var rangeDemoConv = new AnyTime.Converter({format:rangeDemoFormat});

	    var primaryKey;

        $("#table_pembebasan_tarif tbody").on("click", "tr", function(){
            try {
                primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
            } catch (e) {
                primaryKey = false;
            }


            if (primaryKey) {
                $(".data-edit").attr("action",$(".data-edit").data("url")+primaryKey);
                $(".data-delete").attr("action",$(".data-delete").data("target")+primaryKey);
            } else {
                $(".data-edit").removeAttr("action");
                $(".data-delete").removeAttr("action");
            }
            
        });
    });
    $(document).on("click",".data-delete", function(event) {
        event.preventDefault();
        $(this).docoForm("delete",{
            success : function (data) {
                table.draw()
            }
        });
    });
', View::POS_END, 'b-index');
?>
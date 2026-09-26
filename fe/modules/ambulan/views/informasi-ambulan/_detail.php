<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\web\View;
    use app\components\DocoConstants;
    use app\components\DocoHelpers;
    $this->title = Yii::t('fe', 'Detail Informasi Ambulan');
?>
<style type="text/css">
    .modal-dialog {
        /*width: 65% !important;*/
        margin: 30px auto;
    }
    button#button-back {
        height: 30px;
        padding-top: 5px;
    }
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $this->title;?></h5>
</div>
<div class="modal-body">
    <div class="panel">
        <div class="panel-toolbar clearfix">
             <?=
             DocoHelpers::generateToolbar([
                'excel' => [
                    'title' => Yii::t('fe', 'Export Excel'),
                    'attributes'=>[
                        'id' => 'cetak-detail-excel',
                        'data-target'=>Url::home().'ambulan/informasi-ambulan/export-excel-detail?ambulan_id='.$encryptAmbulan_id.'&',
                        // 'data-options' => 'click',
                        // 'data-tooltip' => 'tooltip',
                        // 'target' => '_blank',
                    ]
                ],
                'pdf' => [
                    'title' => Yii::t('fe', 'Expor Pdf'),
                    'attributes'=>[
                        'id' => 'cetak-detail-pdf',
                        'data-target'=>Url::home().'ambulan/informasi-ambulan/export-pdf-detail?ambulan_id='.$encryptAmbulan_id.'&',
                        // 'data-options' => 'click',
                        // 'data-tooltip' => 'tooltip',
                        // 'target' => '_blank',
                    ]
                ],
            ], "#history-obat");
           /* echo  Html::a('<b><i class="fa fa-file-pdf-o"></i></b>'.\Yii::t('fe', 'Cetak Detail Obat'),
                Url::to(['cetak-detail-obat', 'obatalkes_id'=>$encryptAmbulan_id]),
                ['class'=>'btn btn-info btn-labeled btn-xs',
                'id'=>'cetak-detail',
                'target'=>'_blank'
                ]);*/
            ?>
        </div>
    </div>
    <div class="clear"><br></div>
    <div class="row">
        <div class="form-group">
            <div class="col-md-2">
                <label class="control-label text-black l-label" style="padding-left:0px;"><?= Yii::t('fe', 'Nomor Polisi'); ?></label>
            </div>
            <div class="col-md-10">
                <label class="container-label"><?= !empty($getData['no_polisi']) ? $getData['no_polisi'] : '-'; ?></label>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-2">
                <label class="control-label text-black l-label" style="padding-left:0px;"><?= Yii::t('fe', 'Jenis Ambulan'); ?></label>
            </div>
            <div class="col-md-10">
                <label class="container-label"><?= !empty($getData['jenis_ambulan']) ? $getData['jenis_ambulan'] : '-'; ?></label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="row">
            <div class="col-md-12 detal-filter-form"></div>
        </div>
        <table id="history-obat" class="table table-striped table-condensed table-hover" style="width:100%">
            <thead>
                <tr class="bg-inverse">
                    <th width="1">No</th>
                    <th><?=\Yii::t("fe", "Tanggal Pemakaian");?></th>
                    <th><?=\Yii::t("fe", "Tanggal Kembali");?></th>
                    <th><?=\Yii::t("fe", "Nomor Pemesanan");?></th>
                    <th><?=\Yii::t("fe", "Nama Pemesan");?></th>
                    <th><?=\Yii::t("fe", "Supir");?></th>
                    <th align="right"><?=\Yii::t("fe", "Jarak Pemakaian").' (Km) ';?></th>
                    <th align="right"><?=\Yii::t("fe", "Nominal Tagihan").' (Rp.) ';?></th>
                    <th align="right"><?=\Yii::t("fe", "Total").' (Rp.) ';?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center" colspan="5"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <hr>
    <div class="modal-footer">
        <?= Html::button("<i class='fa fa-arrow-left'></i> ". Yii::t('fe', 'Kembali'),[
            'class' => 'btn bg-slate btn-sm',
            'id'=>'button-back',
            'data-dismiss' => 'modal'
        ]) ?>
    </div>
</div>
<?php 
$this->registerJs('
    // Global Var
    var table;
    var no_urut = 0;
    $(document).ready(function() {
        table = $("#history-obat").docoTabel({
            filter: true,
            // columnDefs: [ {
            //     orderable: false,
            //     className: "select-checkbox text-center",
            //     targets:   0
            // }],
            select: {
                style:    "os",
                selector: "tr"
            },
            // sorting: [[2, "desc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"ambulan/informasi-ambulan/get-data-ambulan-detail?ambulan_id='.$encryptAmbulan_id.'",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "Tanggal Pemakaian", 
                    data: "tgl_pemakaiandari",
                    searchable: false
                },
                {
                    title: "Tanggal Kembali", 
                    data: "tgl_realisasikembali",
                    searchable: false
                },
                {
                    title: "Nomor Pemesanan", 
                    data: "no_pesanambulan",
                    searchable: false
                },
                {
                    title: "Nama Pemesan", 
                    data: "nama_pemesan",
                    searchable: false
                },
                {
                    title: "Supir", 
                    data: "supir",
                    searchable: false
                },
                {
                    title: "Jarak Pemakaian (Km)", 
                    data: "jarak_pemakaian",
                    class : "text-right",
                    searchable: false
                },
                {
                    title: "Nominal Tagihan (Rp.)", 
                    data: "nominal_tagihan",
                    class : "text-right",
                    searchable: false
                },
                {
                    title: "Total (Rp.)", 
                    data: "total",
                    class : "text-right",
                    searchable: false
                }
            ]
        });
        $(".dataTables_filter").hide();
        $(".detal-filter-form").datatableBootstrapFilter(table, [
            [
                2, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('obatalkes_nama', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Nama Obat')]))).'\'
            ]
        ]);
    });

    $("#cetak-detail").on("click", function(event) {
        var table = $("#history-obat").dataTable();
        var tableLength = table.fnGetData().length;

        if(tableLength > 0 ){
            $("#cetak-detail").submit();
        }else{
            docoNotification("error","Proses Gagal !","Tidak ada data Obat.");
            return false;
        }
    });

', View::POS_END, 'b-index');
?>

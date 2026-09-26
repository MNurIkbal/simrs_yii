<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\web\View;
    use app\components\DocoConstants;
    use app\components\DocoHelpers;
    $this->title = Yii::t('fe', 'Detail Base Price Obat');
?>
<style type="text/css">
.modal-dialog {
width: 65% !important;
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
             <?php
             /*DocoHelpers::generateToolbar([
                'excel' => [
                    'title' => Yii::t('fe', 'Cetak Detail Obat'),
                    'attributes'=>[
                        'id' => 'cetak-detail',
                        'data-target'=>Url::home().'master/base-price/cetak-detail-obat?obatalkes_id=',
                        'data-options' => 'click',
                        'data-tooltip' => 'tooltip',
                        'target' => '_blank',
                    ]
                ],
            ], "#history-obat");*/
            echo Html::button("<b><i class='fa fa-arrow-left'></i></b>&nbsp;Kembali",[
                    'class' => 'btn btn-info btn-labeled btn-xs',
                    'data-dismiss' => 'modal'
                ]);
            echo "&nbsp";
            echo  Html::a('<b><i class="fa fa-file-pdf-o"></i></b>'.\Yii::t('fe', 'Cetak Detail Obat'),
                Url::to(['cetak-detail-obat', 'obatalkes_id'=>$obatalkes_id]),
                ['class'=>'btn btn-info btn-labeled btn-xs',
                'id'=>'cetak-detail',
                'target'=>'_blank'
                ]);
            ?>
        </div>
    </div>
    <div class="clear"><br></div>
    <div class="row">
        <div class="form-group">
            <div class="col-md-4">
                <label class="control-label text-black l-label" style="padding-left:0px;"><?= Yii::t('fe', 'Nama Obat'); ?></label>
            </div>
            <div class="col-md-8">
                <label class="container-label"><?php echo $obatalkes['obatalkes_nama'] ?></label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12 detal-filter-form"></div>
            </div>
            <table id="history-obat" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?=\Yii::t("fe", "Tanggal");?></th>
                        <th align="right"><?=\Yii::t("fe", "Harga Dasar Yang Digunakan").' (Rp.) ';?></th>
                        <th><?=\Yii::t("fe", "Dibuat Oleh");?></th>
                        <th><?=\Yii::t("fe", "Keterangan");?></th>
                        <th><?=\Yii::t("fe", "Catatan");?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center" colspan="5"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php 
$this->registerJs('
    // Global Var
    var table_detail;
    var no_urut = 0;
    $(document).ready(function() {
        table_detail = $("#history-obat").docoTabel({
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
            sorting: [[1, "desc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"master/base-price/get-data-obat?obatalkes_id='.$obatalkes_id.'",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "Tanggal", 
                    data: "tgl_obathistory",
                    searchable: false
                },
                {
                    title: "Harga Dasar Yang Digunakan (Rp.)", 
                    data: "harga_dasar",
                    class : "text-right",
                    searchable: false
                },
                {
                    title: "Dibuat Oleh", 
                    data: "nama_pegawai",
                    searchable: false
                },
                {
                    title: "Keterangan", 
                    data: "keterangan", 
                    searchable: false
                },
                {
                    title: "Catatan", 
                    data: "catatan", 
                    searchable: false
                },
            ]
        });
        $(".dataTables_filter").hide();
        $(".detal-filter-form").datatableBootstrapFilter(table_detail, [
            [
                1, \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('tgl_obathistory', '', ['class' => 'form-control', 'placeholder' => \Yii::t('fe', 'Nama Obat')]))).'\'
            ]
        ]);
    });

    $("#cetak-detail").on("click", function(event) {
        var table_detail_obat = $("#history-obat").dataTable();
        var tableLength = table_detail_obat.fnGetData().length;

        if(tableLength > 0 ){
            $("#cetak-detail").submit();
        }else{
            docoNotification("error","Proses Gagal !","Tidak ada data Obat.");
            return false;
        }
    });

', View::POS_END, 'b-index');
?>

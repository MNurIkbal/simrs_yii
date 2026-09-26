<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-15 16:34:43
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-01-31 12:13:28
 * desc: modal list obat
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use app\components\DocoHelpers;

$title = "Ketersediaan Ambulan";

?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">
    <div class="panel-toolbar clearfix">
    <?=DocoHelpers::generateToolbar([
        'search',
        'reset'=> [
            'attributes'=>[
                'data-parent'=>'.filter-form'
            ]
        ],
    ],'#ambulan-list');?>
    <?= Html::button(
        '<b><i class="fa fa-folder-open"></i></b>' . Yii::t('fe','Pesan'), 
        [
            'class' => 'btn btn-info btn-labeled btn-xs btn-pesan',
        ]) 
    ?>
            </div>
    <div class="row">
        <div class="col-md-12 filter-form"></div>
    </div>
    <table id="ambulan-list" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th width="1">No</th>
                <th><?=\Yii::t("fe", "Tanggal");?></th>
                <th><?=\Yii::t("fe", "No Polisi");?></th>
                <th><?=\Yii::t("fe", "Jenis Ambulan");?></th>
                <th><?=\Yii::t("fe", "Status");?></th>
                <th>Pilih</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" colspan="5"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
            </tr>
        </tbody>
    </table>
</div>

<?php 
$this->registerJs('
    // Global Var
    var table;
    var no_urut = 0;
    $(document).ready(function() {
        table = $("#ambulan-list").docoTabel({
            filter: true,
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox text-center",
                targets:   5
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            sorting: [[1, "desc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"ambulan/permintaan-ambulan/get-data-ambulan",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "Tanggal",
                    data: "tgl_pesanambulan"
                },
                {
                    title: "No Polisi",
                    data: "no_polisi"
                },
                {
                    title: "Jenis Ambulan",
                    data: "jenis_ambulan"
                },
                {
                    title: "Status", 
                    data: "status_ambulan", 
                    searchable: false
                },
                {
                    title: "Pilih", 
                    data: null, 
                    defaultContent: "", 
                    searchable: false, 
                    orderable: false,
                    width: "10%"
                },
            ],
            drawCallback : function (settings) {
                var api = this.api();
                var dataRows = api.rows( {page:"current"} ).data();
                var tr = $(this);
                $.each(dataRows, function (key, val) {
                    var _primary = val.ambulan_id;
                    if (parseInt(_primary) == parseInt(ambulanId)) {
                        table.row(":eq("+key+")").select();
                    }
                })
            }
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, [
            [
                1, 
                \'<div class=""><input type="text" value="'.date('d-M-Y').'" class="form-control pickadate" /></div>\'
            ],
            [3, \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('jenis_ambulan', '', 
                [1 => 'EMERGENCY', 0 => 'NON EMERGENCY'], ['class' => 'form-control select2', 'id' => 'jenis_ambulan', 'prompt' => Yii::t('fe', '--Pilih Jenis Ambulan--') ]))).'\' 
            ],
        ]);
        
        $(".pickadate").pickadate({
            format: "dd mmm yyyy",
        });
    });
    
    $(".btn-pesan").on("click", function(){
        var tableData = table.row(".selected").data();
        try {
            var ambulan_id = tableData.ambulan_id;
            var jenis_ambulan = tableData.jenis_ambulan;
            var tgl_pesanambulan = tableData.tgl_pesanambulan;
            var noPolisi = tableData.no_polisi;
        } catch (e) {
            docoNotification("error","Proses Gagal !","Tidak ada ambulan yang dipilih");
            return false;
        }

        ambulanId = ambulan_id;
        $("#modal_backdrop").modal("toggle");
        $(".ambulan_id").val(ambulan_id);
        $(".is_emergency").text(jenis_ambulan);
        $(".tgl_pesanambulan").val(tgl_pesanambulan);
        $(".plat-nomor").html(noPolisi);

        $.ajax({
            url: "/ambulan/informasi-permintaan-ambulan/tambah-tarif-ambulan?ambulan_id=" + ambulan_id + "&parent_id=" + _idParent,
            success: function(data) {
                _table.draw();
            }
        })
    });
', View::POS_END, 'b-index');
?>


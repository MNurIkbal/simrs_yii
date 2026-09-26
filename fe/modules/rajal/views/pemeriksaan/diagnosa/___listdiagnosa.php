<?php
/*
 * @Author: metafiliana 
 * @Date: 2018-01-31 10:40:58 
 * @Last Modified by: metafiliana
 * @Last Modified time: 2018-01-31 13:50:06
 * @Description: 
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12 filter-form"></div>
    </div>
    <div class="form-group">
        <div class="col-md-12">
            <hr>
        </div>
    </div>
    <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="data-list-diagnosa">
        <thead>
            <tr class="bg-inverse">
                <th><?=Yii::t('fe', 'diagnosa_kode');?></th>
                <th><?=Yii::t('fe', 'diagnosa_nama');?></th>
                <th><?=Yii::t('fe', 'diagnosa_namalainnya');?></th>
                <th><?=Yii::t('fe', 'diagnosa_katakunci');?></th>
                <th width="1"><?=Yii::t('fe', 'Aksi');?></th>
            </tr>
        </thead>
        <tbody> 
        </tbody>
    </table>
</div>

<?php
$this->registerJs('
    var tabel_listdiagnosa = $("#data-list-diagnosa").docoTabel({
        filter: true,
        sorting: [[1, "asc"]], 
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl+"rajal/pemeriksaan/get-data-diagnosa",
        columns: [
            {title: "' . (\Yii::t("fe", "diagnosa_kode")) . '", data: "diagnosa_kode"},
            {title: "' . (\Yii::t("fe", "diagnosa_nama")) . '", data: "diagnosa_nama"},
            {title: "' . (\Yii::t("fe", "diagnosa_namalainnya")) . '", data: "diagnosa_namalainnya"},
            {title: "' . (\Yii::t("fe", "diagnosa_katakunci")) . '", data: "diagnosa_katakunci"},
            {
                title: "' . (\Yii::t("fe", "Aksi")) . '",
                data: "aksi",
                searchable: false,
                orderable: false
            },
        ],
    });
    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(tabel_listdiagnosa);
    $(".reset-filter").on("click", function (e) {
        e.preventDefault();
        tabel_listdiagnosa.reset();
    });

    $(document).on("click", " .pilih-diagnosa ", function(e) {
        e.preventDefault();
        var _object = $(this).data("diagnosa");
        console.log(_object);
        $("#selectDiagnosa").val(_object).trigger("change");
        $(" #modal_backdrop").modal("hide");
        return false;
    });
');
?>

<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = \Yii::t('fe', 'Pasien');
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $this->title; ?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-13 filter-form-modal"></div>
    </div>
    <table id="exampleFilter" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th width="1">No</th>
                <th><?= \Yii::t("fe", "No rekam medis"); ?></th>
                <th><?= \Yii::t("fe", "Nama pasien"); ?></th>
                <th><?= \Yii::t("fe", "Tanggal lahir"); ?></th>
                <th width="1"><?= \Yii::t("fe", ""); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" colspan="6"><?= \Yii::t("fe", "Data tidak ditemukan."); ?></td>
            </tr>
        </tbody>
    </table>
</div>
<script>
    var tableSearch;

    $(document).on('click', '.select-pasien', function(e) {
        e.preventDefault();
        var _object = $(this).data('pasien');
        $(".nama_pasien").val(_object).trigger('change')
        $('#modal_backdrop-lg').modal('hide')
        return false;
    })

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        tableSearch = $("#exampleFilter").docoTabel({
            filter: true,
            sorting: [[1, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"apotek/transaksi-resep/get-data-pasien",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "<?= (\Yii::t("fe", "No rekam medis")); ?>", data: "no_rekam_medik"},
                {title: "<?= (\Yii::t("fe", "Nama pasien")); ?>", data: "nama_pasien"},
                {title: "<?= (\Yii::t("fe", "Tanggal lahir")); ?>", data: "tanggal_lahir"},
                {
                    title: "<?= (\Yii::t("fe", "")); ?>",
                    data: "check",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                }
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form-modal").datatableBootstrapFilter(tableSearch, 
            [
                [
                    3, 
                    '<div class="input-group"><input type="text" class="form-control pickadate" value="" placeholder="<?= \Yii::t('fe', 'Tanggal lahir') ?>" col-index="3"><span class="input-group-addon"><i class="icon-calendar"></i></span></div>'
                ],
                
            ], {
                3:3
            }, true
        );

        $('.pickadate').pickadate({
            format: 'dd mmm yyyy',
        });
    });
</script>

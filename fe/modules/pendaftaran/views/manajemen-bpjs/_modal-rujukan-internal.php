<?php
use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\View;
use kartik\widgets\ActiveForm;
?>

<style>
.modal {
  overflow-y:auto;
}
</style>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <table id="table-rujukan-internal" class="table datatable-basic table-striped table-hover dataTable no-footer" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th><?=Yii::t("fe", "No. SEP");?></th>
                        <th><?=Yii::t("fe", "No. Rujuk Internal");?></th>
                        <th><?=Yii::t("fe", "Tanggal"); ?></th>
                        <th><?=Yii::t('fe', 'Tujuan')?></th>
                        <th><?=Yii::t('fe', 'Nama DPJP')?></th>
                        <th><?=Yii::t('fe', 'Diagnosa')?></th>
                        <th><?=Yii::t('fe', 'Aksi')?></th>
                    </tr>
                </thead>
                <tbody> 
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    var table_rujukan_internal;
    var draw = 0;
    generateTable();

    function generateTable() {
        table_rujukan_internal = $('#table-rujukan-internal').DataTable({
            filter: true,
            displayLength: 10,
            columnDefs: [],
            sorting: false, 
            processing: true,
            serverSide: false,
            data: rujukan_internal.list,
            columns:[
                {
                    title: '" . (\Yii::t("fe", "No. SEP")) . "',
                    data: 'nosep',
                    searchable: false,
                },
                {
                    title: '" . (\Yii::t("fe", "No. Rujuk Internal")) . "',
                    data: 'nosurat',
                    searchable: false,
                },
                {
                    title: '" . (\Yii::t("fe", "Tanggal")) . "',
                    data: 'tglrujukinternal',
                    searchable: false,
                },
                {
                    title: '" . (\Yii::t("fe", "Tujuan")) . "', 
                    data: 'kdpolituj',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: '" . (\Yii::t("fe", "Nama DPJP")) . "', 
                    data: 'nmdokter', 
                    searchable: false,
                    orderable: false,
                },
                {
                    title: '" . (\Yii::t("fe", "Diagnosa")) . "', 
                    data: 'diagppk', 
                    searchable: false,
                    orderable: false,
                },
                {
                    title: '" . (\Yii::t("fe", "Aksi")) . "', 
                    data: 'aksi', 
                    searchable: false,
                    orderable: false,
                },
            ]
        });
        
        $('.dataTables_filter').hide();
    }

", View::POS_END, 'pencarian_spesialis');
?>
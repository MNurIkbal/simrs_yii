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
    <h5 class="modal-title"><?=  Yii::t('fe', 'Pencarian Spesialis/Sub Spesialis') ?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <table id="table-spesialis" class="table datatable-basic table-striped table-hover dataTable no-footer" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th><?=Yii::t('fe', 'No')?></th>
                        <th><?=Yii::t("fe", "Nama Spesialis/Sub Spesialis"); ?></th>
                        <th><?=Yii::t("fe", "Detail"); ?></th>
                        <th><?=Yii::t("fe", "Kapasitas"); ?></th>
                        <th><?=Yii::t('fe', 'Jumlah Rencana Kontrol & Rujukan')?></th>
                        <th><?=Yii::t('fe', 'Persentase')?></th>
                        <th><?=Yii::t('fe', 'Kode Poli')?></th>
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
    var table_spesialis;
    var draw = 0;
    var params = 'jenis_kontrol=' +rencanaKontrol.jnsKontrol+ '&no_kartu='+noKartu+'&tgl='+$('#tgl_rencanakontrol').val();
    generateTable();

    function generateTable() {
        table_spesialis = $('#table-spesialis').DataTable({
            filter: true,
            displayLength: 10,
            columnDefs: [],
            sorting: [[0, 'asc']], 
            processing: true,
            serverSide: false,
            ajax: baseUrl+'pendaftaran/rencana-kontrol-inap/get-data-spesialis?'+params,
            columns:[
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {
                    title: '" . (\Yii::t("fe", "Nama Spesialis/Sub")) . "',
                    data: 'namaPoli',
                    searchable: false,
                },
                {
                    title: '" . (\Yii::t("fe", "Detai")) . "',
                    data: 'detail',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: '" . (\Yii::t("fe", "Kapasitas")) . "',
                    data: 'kapasitas',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: '" . (\Yii::t("fe", "Jml.Rencana Kontrol & Rujukan")) . "', 
                    data: 'jmlRencanaKontroldanRujukan',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: '" . (\Yii::t("fe", "Presentase")) . "', 
                    data: 'persentase', 
                    searchable: false,
                    orderable: false,
                },
                {
                    title: '" . (\Yii::t("fe", "Kode Poli")) . "', 
                    data: 'kodePoli', 
                    searchable: false,
                    visible: false,
                },
            ]
        });
        
        $('.dataTables_filter').hide();
    }

", View::POS_END, 'pencarian_spesialis');
?>
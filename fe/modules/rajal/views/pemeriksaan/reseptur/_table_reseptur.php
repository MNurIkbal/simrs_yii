<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use yii\web\View;

?>

<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title"><?=Yii::t('fe', 'Tabel Reseptur')?></h5>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                </ul>
            </div>
        </div>
        <div class="panel-body">
            <div class="row">
				<div class="col-md-12 table-container">
					<table width="100%" id="tabel-reseptur" class="table table-striped table-hover datatable-basic dataTable">
	                    <thead>
	                        <tr class="bg-inverse">
                                <th>No</th>
	                            <th><?=Yii::t('fe', 'Racikan / non racikan')?></th>
	                            <th><?=Yii::t('fe', 'R ke-')?></th>
	                            <th><?=Yii::t('fe', 'Nama obat')?></th>
	                            <th><?=Yii::t('fe', 'Satuan')?></th>
	                            <th><?=Yii::t('fe', 'Signa')?></th>
	                            <th><?=Yii::t('fe', 'Qty')?></th>
	                            <th><?=Yii::t('fe', 'Harga satuan')?></th>
	                            <th><?=Yii::t('fe', 'Jumlah harga')?></th>
                                <th><?=Yii::t('fe', 'Catatan')?></th>
	                            <th><?=Yii::t('fe', 'Aksi')?></th>
	                        </tr>
	                    </thead>
	                    <tbody>
	                        <!-- <tr class="default-value text-center">
	                            <td colspan="9">Data tidak di temukan</td>
	                        </tr> -->
	                    </tbody>
	                </table>
				</div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="pull-left" style="margin-left:5px">
                <?= Html::button('<i class="fa fa-floppy-o"></i> '. Yii::t('fe', 'Simpan Template'), [
                    'class' => 'btn bg-teal',
                    'id' => 'save-template',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_backdrop',
                    'action' => "/rajal/pemeriksaan/add-template?id=". $pendaftaran_id
                ]); ?>
            </div>
            <div class="pull-right" style="margin-right:5px">
                <?= Html::button('<i class="fa fa-floppy-o"></i> '. Yii::t('fe', 'Simpan'), [
                    'class' => 'btn bg-teal',
                    'id' => 'saveReseptur',
                    'action' => "/rajal/pemeriksaan/save-session-reseptur?pendaftaran_id=". $pendaftaran_id,
                    'onclick' => 'simpanReseptur(this)'
                ]); ?>
                <?= Html::button('<i class="fa fa-refresh"></i> '. Yii::t('fe', 'Muat ulang'), [
                    'class' => 'btn bg-teal',
                    'id'=>'btn-resetAll',
                    'data-url'=> Url::to(['reset-reseptur-session?pendaftaran_id='.$pendaftaran_id]),
                ]); ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var pendaftaran_id = "'.$pendaftaran_id.'";
var tabel_reseptur = "";
var _tmpStok = [];
var _iter = 0;

var tabel_reseptur = $("#tabel-reseptur").docoTabel({
    destroy: true,
    filter: false,
    sorting: [[1, "asc"]], 
    processing: true,
    serverSide: true,
    lengthChange: false,
    paging: false,
    info: false,
    scrollX: false,
    // scrollapse: false,
    ajax: baseUrl+"rajal/pemeriksaan/get-data-reseptur-session?pendaftaran_id="+pendaftaran_id,
    columns: [
        {
            title: "No",
            data: "no",
            searchable: false,
            orderable: false, 
        },
        {
            title: "Racikan/ non racikan", 
            data: "nama_racikan"
        },
        {
            title: "R ke-", 
            data: "rke"
        },
        {
            title: "Nama obat", 
            data: "obatalkes_nama"
        },
        {
            title: "Satuan", 
            data: "satuankecil_nama"
        },
        {
            title: "Signa", 
            data: "signa_edit"
        },
        {
            title: "Qty", 
            data: "qty_edit"
        },
        {
            title: "Harga satuan", 
            data: "hargasatuan_reseptur", 
            className: "text-right"
        },
        {
            title: "Jumlah harga", 
            data: "jumlah_harga", 
            className: "text-right"
        },
        {
            title: "Catatan", 
            data: "etiket"
        },
        {
            title: "Aksi",
            data: "aksi",
            searchable: false,
            orderable: false, 
        },
    ],
    drawCallback: function (settings) {
        _tmpStok = [];
        var rows = tabel_reseptur.rows();
        data = rows.data();
        $.each(data, function (k,v) {
            _iter = v.iter;
            if (typeof _tmpStok[v.obatalkes_id] == "undefined") {
                    _tmpStok[v.obatalkes_id] = 0;
                }
            _tmpStok[v.obatalkes_id] += parseFloat(v.total_konversi);
        });
        if (_iter) {
            $("#reseptur_iter").val(_iter);
        }
    }
});    
', View::POS_END) ?>
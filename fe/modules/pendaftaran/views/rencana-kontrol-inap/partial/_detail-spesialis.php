<?php

/**
 * @Author: Fajar Supriadi
 * @Date:   2022-02-02
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\modules\master\models\CaraBayarForm;
use Doco\master\controllers\CaraBayarController;

$this->title = $title;

?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-paket"></div>
                </div>
                
                <table id="tabel-detail-spesialis-<?= $kode_poli ?>" class="table table-striped table-condensed table-hover" style="width:100%;">
                    <thead>
                        <tr class="bg-inverse">
                            <th>No</th>
                            <th><?=Yii::t('fe', 'Nama Dokter')?></th>
                            <th><?=Yii::t('fe', 'Jadwal Praktek')?></th>
                            <th><?=Yii::t('fe', 'Kapasitas')?></th>
                            <th><?=Yii::t('fe', 'Aksi')?></th>
                        </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    var table_dpjp;
    var draw = 0;
    var params = 'jenis_kontrol=' +rencanaKontrol.jnsKontrol+ '&kode_poli=$kode_poli&tgl=' +$('#tgl_rencanakontrol').val()+ '&nama_spesialis=$nama_spesialis';
    generateTableDpjp();

    function generateTableDpjp() {
        table_dpjp = $('#tabel-detail-spesialis-$kode_poli').DataTable({
            filter: true,
            displayLength: 10,
            columnDefs:[],
            sorting: [[0, 'asc']], 
            processing: true,
            serverSide: false,
            ajax: baseUrl+'pendaftaran/rencana-kontrol-inap/get-dokter-dpjp?'+params,
            columns:[
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {
                    title: '" . (\Yii::t("fe", "Nama Dokter")) . "',
                    data: 'namaDokter',
                    searchable: false,
                },
                {
                    title: '" . (\Yii::t("fe", "Jadwal Praktek")) . "',
                    data: 'jadwalPraktek',
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
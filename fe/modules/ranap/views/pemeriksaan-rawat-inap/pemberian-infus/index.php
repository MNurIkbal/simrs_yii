<?php
use app\components\DocoHelpers;

use kartik\widgets\ActiveForm;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

?>
<style type="text/css">
    .table-title {
        text-align: left;
        margin-bottom: 8px;
        font-weight: bold;
        font-size: 90%;
    }
    .dataTables_scroll {
        height: auto !important;
    }
</style>
<div class="row body">
    <div class="col-md-12">
        <div class="row">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h5 class="panel-title">Pemberian Infus</h5>
            </div>
            <div class="panel-body">
                <div class="row" id="div-form">
                    <?= $this->render('_form', compact('model', 'id', 'now', 'listTindakan', 'listObat', 'is_nurse')); ?>
                </div>
                    <hr>
                <div class="row">
                    <div class="col-md-12">
                        <h5 class="panel-title">Tabel Monitoring Infus</h5>
                        <table class="table datatable-basic table-striped table-hover dataTable" id="table-monitoring-infus" style="width:100%">
                            <thead>
                                <tr class="bg-inverse">
                                    <th>No</th>
                                    <th>Tanggal Pemasangan/Profesi</th>
                                    <th>Tindakan/Obat</th>
                                    <th>Volume Infus</th>
                                    <th>Durasi</th>
                                    <th>Jumlah Tetesan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="first-class">
                                    <td colspan="7" class="text-center">Tidak ada data</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs($this->render('index.js'), View::POS_END);
?>
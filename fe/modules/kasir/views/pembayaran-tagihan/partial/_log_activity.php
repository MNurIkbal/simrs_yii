<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;

?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><b><?=$title;?></b></h5>
</div>
<div class="modal-body">
    <div class="panel-body">
            <table id="table-log-activity" class="table table-striped table-hover" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th><?=\Yii::t("fe", "Tanggal");?></th>
                        <th><?=\Yii::t("fe", "Keterangan");?></th>
                        <th><?=\Yii::t("fe", "Tipe");?></th>
                        <th><?=\Yii::t("fe", "Oleh");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($datalog as $key => $value) : ?>
                        <tr>
                            <td><?= date('d M Y H:i:s', strtotime($value['created_date'])) ?></td>
                            <td><?= $value['keterangan'] ?></td>
                            <td><?= $value['kelompoktindakan_nama'] ?></td>
                            <td><?= $value['nama_pegawai'] ?></td>
                        </tr>
                    <?php endforeach;?>
                </tbody>
            </table>
    </div>
    <div class="modal-footer" style="padding:0px !important;">
        <!-- <hr> -->
        <?= Html::button("<b><i class='fa fa-arrow-left'></i></b>&nbsp;Kembali",[
                            'class' => 'btn btn-info btn-labeled btn-xs',
                            'data-dismiss' => 'modal'
            ]); ?>

    </div>
</div>
<?php 
$this->registerJs("
    $('#table-log-activity').ready(function () {
        $('#table-log-activity').DataTable({
            filter: false,
            scrollCollapse: true,
            processing: false,
            serverSide: false,
            order: [[2, 'desc']],
            language: {
                emptyTable: 'Belum Ada Data Edit Tagihan.'
            }
        }).columns.adjust().draw(false)
    });

", View::POS_END);
?>
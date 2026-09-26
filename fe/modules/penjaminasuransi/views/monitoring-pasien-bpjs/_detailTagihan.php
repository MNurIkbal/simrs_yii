<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-09-06 10:35:25
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-09-13 16:02:38
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 
'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title;?></h5>
</div>
<div class="modal-body">
    <?php 
    $lastGroup = 0;
    foreach($groupHeader as $key => $value) : 
        $total = 0;
        $no = 1; 
    ?>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h6 class="panel-title"><b><?= $key ?></b></h6>
            </div>
            <div class="panel-body">
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th>No</th>
                            <th><?=\Yii::t("fe", "Tanggal Tindakan");?></th>
                            <th><?=\Yii::t("fe", "Nama Tindakan");?></th>
                            <th><?=\Yii::t("fe", "Qty");?></th>
                            <th><?=\Yii::t("fe", "Tarif Satuan");?></th>
                            <th><?=\Yii::t("fe", "Tarif Cyto");?></th>
                            <th><?=\Yii::t("fe", "Jumlah Tarif");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($value as $k => $v) : $total += $v['jml_tarif']; ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= date('d M Y', strtotime($v['tgl_tindakan'])) ?></td>
                            <td><?= $v['daftartindakan_nama'] ?></td>
                            <td style="text-align: right;"><?= $v['qty_tindakan'] ?></td>
                            <td style="text-align: right;"><?= DocoHelpers::formatNumber($v['tarif_satuan']) ?>
                            </td>
                            <td style="text-align: right;"><?= DocoHelpers::formatNumber($v['tarifcyto_tindakan']) ?></td>
                            <td style="text-align: right;"><?= DocoHelpers::formatNumber($v['jml_tarif']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="6" style="text-align:right">Total:</th>
                            <th style="text-align: right;"><?= DocoHelpers::formatNumber($total); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<div class="modal-footer">
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
</div>



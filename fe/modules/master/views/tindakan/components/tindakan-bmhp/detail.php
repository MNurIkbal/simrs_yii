<?php

/**
 * @Author: Wahyu Saepuloh
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
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
                    <div class="col-md-12 filter-tindakan-bmhp"></div>
                </div>
                
                <table id="tabel-detail-tindakan-bmhp-<?= $id_encrypt ?>" class="table table-striped table-condensed table-hover" style="width:100%;">
                    <thead>
                        <tr class="bg-inverse">
                            <th>No</th>
                            <th>Group</th>
                            <th>Obat/Alkes</th>
                            <th>Satuan Input</th>
                            <th>QTY</th>
                            <th>QTY Konversi</th>
                        </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var tabel;
    var id_enkrip = '. $id_encrypt .';
' . $this->render('js/detail.js'), View::POS_END);
?>
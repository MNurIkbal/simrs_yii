<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
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
                    <div class="col-md-12 filter-paket-mcu"></div>
                </div>
                
                <table id="tabel-detail-paket-mcu-<?= $id_encrypt ?>" class="table table-striped table-condensed table-hover" style="width:100%;">
                    <thead>
                        <tr class="bg-inverse">
                            <th>No</th>
                            <th>Paket/Tindakan</th>
                            <th>Instalasi</th>
                            <th>Ruangan</th>
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
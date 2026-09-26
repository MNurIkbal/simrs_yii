<?php

/**
 * @Author: Ripan
 */

use yii\web\View;

$this->title = $title;

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-tindakan-luar-bedah"></div>
                </div>
                
                <table id="tabel-detail-tindakan-luar-bedah-<?= $decId ?>" class="table table-striped table-condensed table-hover" style="width:100%;">
                    <thead>
                        <tr class="bg-inverse">
                            <th>No</th>
                            <th>Tindakan Di Luar Bedah</th>
                            <th>Qty</th>
                            <th>Ditagihkan</th>
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
    var dec_id = '. $decId .';
' . $this->render('js/detail.js'), View::POS_END);
?>
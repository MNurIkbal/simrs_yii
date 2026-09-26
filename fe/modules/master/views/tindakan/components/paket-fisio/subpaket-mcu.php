<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $title;

?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-paket"></div>
                </div>
                <table id="table-sub-mcu-<?= $idEnc ?>" class="table table-striped table-condensed table-hover" style="width:100%;">
                    <thead>
                        <tr class="bg-inverse">
                            <th>No</th>
                            <th>Tindakan</th>
                            <th>Kelompok</th>
                            <th>Instalasi/Ruangan</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$phpVars = [
    'id_enc' => $idEnc,
    'is_mcu' => $isMcu
];

$this->registerJsVar('phpVars', $phpVars);
$this->registerJs($this->render('js/subpaket-mcu.js'), View::POS_END);
?>
<?php
use yii\web\View;

$this->title = $title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-paket"></div>
                </div>
                <table id="table-sub-<?= $idEnc ?>" class="table table-striped table-condensed table-hover" style="width:100%;">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1%"></th>
                            <th></th>
                            <th></th>
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
$this->registerJs($this->render('js/subpaket.js'), View::POS_END);
?>
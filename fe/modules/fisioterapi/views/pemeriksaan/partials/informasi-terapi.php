<?php

use yii\helpers\ArrayHelper;

$daftarTindakanArr = ArrayHelper::getValue($dataView, 'resultDaftarTindakan');

?>
<div class="panel panel-default" style="margin-top: 10px; margin-bottom: 15px;">
    <div class="panel-heading flex-container ">
        <h6 class="panel-title text-bold">Terapi</h6>
        <a data-toggle="collapse" href="#patient-history-tabs" role="button" aria-expanded="false" aria-controls="patient-history-tabs">
            <ul class="icons-list">
                <li>
                    <i id="chevron" class="fa fa-chevron-down" style="margin-top: 5px;"></i>
                </li>
            </ul>
        </a>
    </div>
    <div style="overflow-y: auto;" class="panel-body collapse in multi-collapse label-information h-115" id="patient-history-tabs">
        <div class="row" style="margin-top: 5px;">
            <?php foreach ($daftarTindakanArr as $key => $value) { ?>
                <div class="col-md-6">
                    <div class="row" style="border-bottom: 1px solid #ddd;margin-left: 1px; margin-right: 1px">
                        <div class="col-md-10">
                            <p title="<?= ArrayHelper::getValue($value, 'daftartindakan_nama'); ?>" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= ArrayHelper::getValue($value, 'daftartindakan_nama'); ?></p>
                        </div>
                        <div class="col-md-2">
                            <button style="float: right;" data-target="#modalProgramTerapi" title="Detail" type="button" data-btntrigger="modal" action="/fisioterapi/informasi-program-fisioterapi-rajal/detail?id=<?= ArrayHelper::getValue($value, 'programterapi_id') ?>&readonly=true" data-toggle="modal" data-options="modal" class="btn-transparent">
                                <i class="fa fa-calendar" style="margin-top: 3px;"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
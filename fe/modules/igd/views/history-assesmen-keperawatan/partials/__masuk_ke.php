<?php

use app\components\DHtml;
?>
<div class="row form-row" id="__masuk_ke">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">Masuk Ke</h5>
        </div>
        <div class="panel-body">
            <div class="row form-row">
                <div class="col-sm-12">
                    <?=
                        DHtml::multipleRadio([
                            'model' => $model,
                            'fieldName' => 'masuk_ke',
                            'data' => $arrayConfig['masuk_ke'],
                            'colSize' => 2
                        ])
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
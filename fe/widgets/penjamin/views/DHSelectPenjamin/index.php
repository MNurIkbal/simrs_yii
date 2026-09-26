<?php

use yii\web\View;
use yii\helpers\Html;
?>

<div class="form-group">
    <select class="form-control" id="<?= $id ?>"></select>
</div>

<?php
$this->registerJs("

var configPenjamin = {
    url: `/api/master/get-penjamin`,
    additionalOption: {
      placeholder: `-- Penjamin --`
    },
    callbackProccess : (data) => {
        var results = []; 
        $.each(data.results.result, function (index, penjamin) {
            results.push({
                id: penjamin.penjamin_id,
                text: penjamin.penjamin_nama
            });
        });
        return {
            results: results,
            pagination: {
                more: data.results.pagination.more
            },
            incomplete_results: false,
        };    
    },
    callbackData: (params) => {
        return {
            term: params.term,
            page: params.page || 1,
            limit: params.limit,
        }
    }
}
$(`#$id`).select2InfinityScroll(configPenjamin);

", View::POS_READY);
?>



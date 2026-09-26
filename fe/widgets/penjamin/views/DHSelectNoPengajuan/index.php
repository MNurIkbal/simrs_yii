<?php

use yii\web\View;
use yii\helpers\Html;
?>

<div class="form-group">
    <select class="form-control" data-start="" data-end="" id="<?= $id ?>" name="<?= $name ?>"></select>
</div>

<?php
$this->registerJs("

var configInfinity = {
    url: `/api/penjamin/list-pengajuan`,
    additionalOption: {
      placeholder: `-- No Pengajuan --`
    },
    callbackProccess : (data) => {
        var results = []; 
        $.each(data.results.result, function (index, row) {
            results.push({
                id: row.pengajuanklaim_id,
                text: row.no_pengajuanklaim
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
        const startDate = $(`#$id`).attr(`data-start`);
        const endDate = $(`#$id`).attr(`data-end`);
        return {
            term: params.term,
            page: params.page || 1,
            limit: params.limit,
            additionalPayload : {
                start_date: startDate,
                end_date: endDate,
            }
        }
    }
}
$(`#$id`).select2InfinityScroll(configInfinity);

", View::POS_READY);
?>
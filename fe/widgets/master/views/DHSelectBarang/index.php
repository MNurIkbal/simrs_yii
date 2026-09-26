<?php

use yii\web\View;
use yii\helpers\Html;
?>

<div class="form-group">
    <select class="form-control" id="<?= $id ?>"></select>
</div>

<?php
$this->registerJs("

var configBarang = {
    url: `/api/master/get-barang`,
    additionalOption: {
        placeholder: `-- Barang --`,
        templateSelection: function(container) {
            $(container.element).attr(`data-kelompok`, container.kelompok_nama);
            $(container.element).attr(`data-subkelompok`, container.subkelompok_nama);
            return container.text;
        }
    },
    callbackProccess : (data) => {
        var results = []; 
        $.each(data.results.result, function (index, barang) {
            results.push({
                id: barang.barang_id,
                text: barang.barang_nama,
                barang_nama: barang.barang_nama,
                kelompok_id: barang.kelompokbarang_id,
                kelompok_nama: barang.kelompokbarang_nama,
                subkelompok_id: barang.subkelompokbarang_id,
                subkelompok_nama: barang.subkelompok_nama
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
            additionalPayload : {
            }
        }
    }
}

$(`#$id`).select2InfinityScroll(configBarang);

", View::POS_READY);
?>



<?php

use yii\web\View;
use yii\helpers\Html;
?>

<div class="form-group" style="max-width: 200px;">
    <select class="form-control <?= $className ?>" id="<?= $id ?>"></select>
</div>

<?php
$this->registerJs("

var configObat = {
    url: '".$api."',
    additionalOption: {
        placeholder: `-- Obat --`,
        templateSelection: function(container) {
            $(container.element).attr(`data-kelompok`, container.kelompok_nama);
            $(container.element).attr(`data-subkelompok`, container.subkelompok_nama);
            return container.text;
        }
    },
    callbackProccess : (data) => {
        var results = []; 
        $.each(data.results.result, function (index, obat) {
            results.push({
                id: obat.obatalkes_id,
                text: obat.obatalkes_kode+` / `+obat.obatalkes_nama,
                obatalkes_nama: obat.obatalkes_nama,
                obatalkes_kode: obat.obatalkes_kode,
                jenisobatalkes_nama: obat.jenisobatalkes_nama,
                servicecategory_nama: obat.servicecategory_nama,
                satuan_kecil: obat.satuan_kecil,
                uom: obat.uom,
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

$(`#$id`).select2InfinityScroll(configObat);

", View::POS_READY);
?>



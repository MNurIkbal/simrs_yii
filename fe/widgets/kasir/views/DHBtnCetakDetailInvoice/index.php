<?php

use yii\web\View;
use yii\helpers\Html;
?>

<button type="button" 
    id="<?= $id ?>" 
    disabled="<?= $disabled ?>" 
    class="btn btn-info btn-labeled btn-xs" 
    data-pembayaran-id=""
    data-pendaftaran-id=""
    >
    <b><i class="fa fa-print"></i></b>
    Cetak Detail Invoice
</button>

<?php
$this->registerJs("

$(document).on(`click`, `#$id`, function(e){
    e.preventDefault();
    let pendaftaranId = $(`#$id`).attr(`data-pendaftaran-id`);
    let pembayaranId = $(`#$id`).attr(`data-pembayaran-id`);
    if(!pendaftaranId || !pembayaranId) {
        return false;
    }
    let urlLocal = `/api/kasir/pembayaran-tagihan/cetak-detail-invoice?id=` + pendaftaranId + `&invoice_id=` + pembayaranId;
    window.open(urlLocal);
})

", View::POS_END);
?>
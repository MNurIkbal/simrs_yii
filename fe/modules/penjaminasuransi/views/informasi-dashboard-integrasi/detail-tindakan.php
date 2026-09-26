<?php

use yii\web\View;

?>
<table class="table table-striped table-condensed table-hover" id="table-expand-tindakan-<?= $kelompoktindakanId ?>" style="width: 98%;">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Item</th>
            <th>Kode Item</th>
            <th>No Pendaftaran</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Sub Total</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
<?php
    $this->registerJs("
        var pendaftaranId = '".$pendaftaranId."';
        var kelompoktindakanId = '".$kelompoktindakanId."';
        var penjaminId = '".$penjaminId."';
        var noKlaim = '".$noKlaim."';
        var isobat = '".$isobat."';
    ", View::POS_END);
    $this->registerJs($this->render('js/_detail_tindakan.js'), View::POS_END);
?>

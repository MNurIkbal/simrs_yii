<?php

use yii\web\View;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\DateTimePicker;
?>
<style>
    .table-left td {
        border-left: none !important;
        border-right: none !important;
    }

    .table-left th {
        border: none !important;
    }

    .table-right td {
        border-left: none !important;
        border-right: none !important;
    }

    .table-right th {
        border: none !important;
    }

    .modal-body {
        margin-top: -10px;
    }

    .content-right {
        margin-bottom: 20px;
    }

    .content-right .dataTables_wrapper .dataTables_scroll {
        overflow-x: hidden;
    }

    .content-right .dataTables_wrapper .dataTables_scroll {
        border: 0.1px solid #bbb;
    }

    .kv-datetime-remove {
        display: none !important;
    }
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Tambah Tindakan Program Terapi</h5>
</div>
<div class="modal-body">
    <div class="row">
        Tambah Tindakan nya gan
    </div>
</div>

<?php
$this->registerJs($this->render('js/tambah-tindakan.js'), View::POS_END);
?>
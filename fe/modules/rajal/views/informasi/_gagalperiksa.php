<?php

/**
 * @Author: afil
 * @Date:   2018-01-15 14:40:50
 * @Last Modified by:   Iqbal
 * @Last Modified time: 2018-08-13 16:36:11
 * @Description:
 */

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Proses Gagal</h5>
</div>
<div class="modal-body">
   <div style="text-align: center; display: flex; justify-content: center;">
        <h4>Status periksa bukan antrian poliklinik</h4>
   </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        setTimeout(() => {
            location.reload()
        }, 2000);
    });
</script>


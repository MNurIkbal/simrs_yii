<?php

/**
 * @Author: Ilham pramono
 * @Date:   2021-08-05 15:24:43
 */

use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

?>

<style type="text/css">

.strikethrough {
    position: relative;
}
.strikethrough:before {
    position: absolute;
    content: "";
    left: 0;
    top: 50%;
    right: 0;
    border-top: 2px solid #666666!important;
    border-color: inherit;
}

</style>
<div class="row">
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h5 class="panel-title"><?=Yii::t('fe', 'Rujuk Balik')?></h5>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                </ul>
            </div>
        </div>
        <div class="panel-body">
             <table class="table table-bordered datatable-basic dataTable" style="width:100%" id="tabel-rujuk-balik">
                   <thead>
                        <tr class="bg-inverse">
                            <th><?=Yii::t('fe', 'No')?></th>
                            <th><?=Yii::t('fe', 'No. SRB')?></th>
                            <th><?=Yii::t('fe', 'No. SEP')?></th>
                            <th><?=Yii::t('fe', 'No. RM')?></th>
                            <th><?=Yii::t('fe', 'Tanggal Pembuatan')?></th>
                            <th><?=Yii::t('fe', 'Aksi')?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <br>
                <br>
 
        </div>
    </div>
</div>

<?php 

$this->registerJs("
    var pendaftaran_id = '".$pendaftaran_id."';
    var url = '/rajal/pemeriksaan';
  
  ".$this->render('index.js'), View::POS_END, 'js');

?>
<?php

/**
 * @author Budi
 */

use yii\web\View;
use yii\helpers\Html;
?>
<div class="row">
    <div class='tabbable'>
        <div id="div-tab">
            <ul class="nav nav-tabs nav-tabs-highlight nav-justified">
                <li class="active" id="tab-rujukan"><a href="#view-rujukan" data-toggle="tab" aria-expanded="true"><strong><?=Yii::t('fe', 'Pasien Rujukan')?></strong></a></li>
                <li class="" id="tab-non-rujukan"><a href="#view-non-rujukan" data-toggle="tab" aria-expanded="true"><strong><?=Yii::t('fe', 'Pasien Laboratorium')?></strong></a></li>
                <li class="" id="tab-riwayat"><a href="#view-riwayat" data-toggle="tab" aria-expanded="true"><strong><?=Yii::t('fe', 'Riwayat Kunjungan')?></strong></a></li>
                <li class="" id="tab-batal"><a href="#view-batal" data-toggle="tab" aria-expanded="true"><strong><?=Yii::t('fe', 'Batal')?></strong></a></li>
            </ul>
        </div>
        <div class="tab-content col-lg-12">
            <div class="tab-pane has-padding active" id="view-rujukan">
                <div id="content-rujukan"></div>
            </div>
            <div class="tab-pane has-padding" id="view-non-rujukan">
                <div id="content-non-rujukan"></div>
            </div>
            <div class="tab-pane has-padding" id="view-riwayat">
                <div id="content-riwayat"></div>
            </div>
            <div class="tab-pane has-padding" id="view-batal">
                <div id="content-batal"></div>
            </div>
        </div>
    </div>
</div>

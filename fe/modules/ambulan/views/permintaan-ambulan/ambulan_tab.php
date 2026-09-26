<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-08-10 11:22:09
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-01-08 16:49:13
 */
use yii\web\View;
use yii\helpers\Html;

?>

<style lang="">
    .tab-header > a {
        height: 70px;
    }
</style>
<div class="row">
    <div class='tabbable'>
        <ul class="nav nav-tabs nav-tabs-highlight nav-justified">
            <li class="tab-header active" id="tab-luar">
                <a data-target="#view-luar" href="#view-luar" data-toggle="tab" aria-expanded="true">
                    <b><?=Yii::t('fe', 'Pasien Luar / Umum')?></b>
                </a>
            </li>
            <li class="tab-header " id="tab-rs">
                <a data-target="#view-rs" href="#view-rs" data-toggle="tab" aria-expanded="true">
                    <b><?=Yii::t('fe', 'Pasien Rumah Sakit')?></b>
                </a>
            </li>
        </ul>
        <div class="tab-content col-lg-12">
            <div class="tab-pane has-padding active" id="view-luar">
                <div id="content-luar"></div>
            </div>
            <div class="tab-pane has-padding" id="view-rs">
                <div id="content-rs"></div>
            </div>
        </div>
    </div>
</div>
<!-- Untuk Kebutuhan Modal Global -->
<div id="modal_backdrop" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- End -->
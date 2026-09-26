<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-08-10 11:22:09
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-08-10 17:19:37
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
            <li class="tab-header active" id="tab-masuk">
                <a data-target="#view-masuk" href="#view-masuk" data-toggle="tab" aria-expanded="true">
                    <?=Yii::t('fe', 'Adjustment Masuk')?>
                </a>
            </li>
            <li class="tab-header " id="tab-keluar">
                <a data-target="#view-keluar" href="#view-keluar" data-toggle="tab" aria-expanded="true">
                    <?=Yii::t('fe', 'Adjustment Keluar')?>
                </a>
            </li>
        </ul>
        <div class="tab-content col-lg-12">
            <div class="tab-pane has-padding active" id="view-masuk">
                <div id="content-masuk"></div>
            </div>
            <div class="tab-pane has-padding" id="view-keluar">
                <div id="content-keluar"></div>
            </div>
        </div>
    </div>
</div>
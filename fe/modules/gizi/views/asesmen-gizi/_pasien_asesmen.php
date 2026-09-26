<?php

/**
 * @Author: rizal
 * @Date:   2018-11-27 16:31:06
 * @Description: 
 */

use yii\web\View;
use yii\helpers\Html;

use app\components\DocoConstants;
?>


<div class='tabbable'>
    <ul id="tab-asesmen" class="nav nav-tabs nav-justified nav-tabs-top">
        <li class="<?= $data_pasien['skor'] >= 2 ? "disabled" : "" ?>" id="tab-sga" <?= $style_button_pulang ?> >
            <a href="#view-sga" <?= $data_pasien['skor'] >= 2 ? "" : "data-toggle=\"tab\"" ?> aria-expanded="true">
                <?= Yii::t('fe', 'Subjective Global Assesment (SGA)') ?>
            </a>
        </li>
        <li class="" id="tab-pagt" <?= $style_button_pulang ?>>
            <a href="#view-pagt" data-toggle="tab" aria-expanded="true">
                <?= Yii::t('fe', 'Proses Asuhan Gizi Terstandar (PAGT)') ?>
            </a>
        </li>
        <li class="<?= $data_pasien['stat_asesmen_gizi'] != DocoConstants::SUDAH_ASESMEN ? 'hidden' : ''; ?>" id="tab-asuhan-gizi" <?= $style_button ?>>
            <a href="#view-asuhan-gizi" data-toggle="tab" aria-expanded="true">
                <?= Yii::t('fe', 'Asuhan Gizi') ?>
            </a>
        </li>
        <li id="tab-nrs" <?= $style_button_pulang ?>>
            <a href="#view-nrs" data-toggle="tab" aria-expanded="true">
                <?= Yii::t('fe', 'Asesmen Gizi NRS') ?>
            </a>
        </li>
        <li id="tab-cppt-gizi" <?= $style_button_pulang ?>>
            <a href="#view-cppt-gizi" data-toggle="tab" aria-expanded="true">
                <?= Yii::t('fe', 'CPPT') ?>
            </a>
        </li>
        <li id="tab-permintaan-makan" <?= $style_button_pulang ?>>
            <a href="#view-permintaan-makan" data-toggle="tab" aria-expanded="true">
                <?= Yii::t('fe', 'Permintaan Makan') ?>
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane has-padding" id="view-sga">
            <div id="content-sga"> </div>
        </div>
        <div class="tab-pane has-padding" id="view-pagt">
            <div id="content-pagt"> </div>
        </div>
        <div class="tab-pane has-padding" id="view-asuhan-gizi">
            <div id="content-asuhan-gizi"> </div>
        </div>
        <div class="tab-pane has-padding" id="view-nrs">
            <div id="content-nrs"> </div>
        </div>
        <div class="tab-pane has-padding" id="view-cppt-gizi">
            <div id="content-cppt-gizi"> </div>
        </div>
        <div class="tab-pane has-padding" id="view-permintaan-makan">
            <div id="content-permintaan-makan"> </div>
        </div>
    </div>
</div>

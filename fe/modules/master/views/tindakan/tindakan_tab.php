<?php

/**
 * @author Randy Vianda Putra
 * @todo Master kegiatan Tabular
 * @copyright 26 April 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Html;
?>
<style lang="">
    .tab-header>a {
        height: 100px;
    }
</style>
<div class="row">
    <div class='tabbable'>
        <div id="div-tab">
            <ul class="nav nav-tabs nav-tabs-highlight nav-justified">
                <li class="tab-header active" id="tab-kategori">
                    <a style="font-size: 12px;" data-target="#view-kategori" href="#view-kategori" data-toggle="tab" aria-expanded="true">
                        <?= Yii::t('fe', 'Master Kategori') ?>
                    </a>
                </li>
                <li class="tab-header " id="tab-kelompok">
                    <a style="font-size: 12px;" data-target="#view-kelompok" href="#view-kelompok" data-toggle="tab" aria-expanded="true">
                        <?= Yii::t('fe', 'Master Kelompok') ?>
                    </a>
                </li>
                <li class="tab-header " id="tab-kegiatan">
                    <a style="font-size: 12px;" data-target="#view-kegiatan" href="#view-kegiatan" data-toggle="tab" aria-expanded="true">
                        <?= Yii::t('fe', 'Master Kegiatan') ?>
                    </a>
                </li>
                <li class="tab-header " id="tab-group-inacbg">
                    <a style="font-size: 12px;" data-target="#view-group-inacbg" href="#view-group-inacbg" data-toggle="tab" aria-expanded="true">
                        <?= Yii::t('fe', 'Master Group INA CBG') ?>
                    </a>
                </li>
                <li class="tab-header " id="tab-tindakan">
                    <a style="font-size: 12px;" data-target="#view-tindakan" href="#view-tindakan" data-toggle="tab" aria-expanded="true">
                        <?= Yii::t('fe', 'Master Tindakan') ?>
                    </a>
                </li>
                <li class="tab-header " id="tab-paket">
                    <a style="font-size: 12px;" data-target="#view-paket" href="#view-paket" data-toggle="tab" aria-expanded="true">
                        <?= Yii::t('fe', 'Master Paket') ?>
                    </a>
                </li>
                <li class="tab-header " id="tab-paket-fisio">
                    <a data-target="#view-paket-fisio" href="#view-paket-fisio" data-toggle="tab" aria-expanded="true">
                        Master Paket Fisioterapi
                    </a>
                </li>
                <li class="tab-header" id="tab-tindakan-ruangan">
                    <a style="font-size: 12px;" data-target="#view-tindakan-ruangan" href="#view-tindakan-ruangan" data-toggle="tab" aria-expanded="true">
                        <?= Yii::t('fe', 'Master Tindakan Ruangan') ?>
                    </a>
                </li>
                <li class="tab-header" id="tab-paket-ruangan">
                    <a style="font-size: 12px;" data-target="#view-paket-ruangan" href="#view-paket-ruangan" data-toggle="tab" aria-expanded="true">
                        <?= Yii::t('fe', 'Master Paket Ruangan') ?>
                    </a>
                </li>
                <li class="tab-header" id="tab-tindakan-bmhp">
                    <a style="font-size: 12px;" data-target="#view-tindakan-bmhp" href="#view-tindakan-bmhp" data-toggle="tab" aria-expanded="true">
                        <?= Yii::t('fe', 'Master Mapping Tindakan dan BMHP / Alkes') ?>
                    </a>
                </li>
                <li class="tab-header" id="tab-tindakan-luar-bedah">
                    <a style="font-size: 12px;" data-target="#view-tindakan-luar-bedah" href="#view-tindakan-luar-bedah" data-toggle="tab" aria-expanded="true">
                        <?= Yii::t('fe', 'Master Mapping Tindakan Di Luar Bedah') ?>
                    </a>
                </li>
                <li class="tab-header" id="tab-tindakan-spesialis">
                    <a style="font-size: 12px;" data-target="#view-tindakan-spesialis" href="#view-tindakan-spesialis" data-toggle="tab" aria-expanded="true">
                        <?= Yii::t('fe', 'Master Tindakan Spesialis') ?>
                    </a>
                </li>
            </ul>
        </div>
        <div class="tab-content col-lg-12">
            <div class="tab-pane has-padding active" id="view-kategori">
                <div id="content-kategori"></div>
            </div>
            <div class="tab-pane has-padding" id="view-kelompok">
                <div id="content-kelompok"></div>
            </div>
            <div class="tab-pane has-padding" id="view-kegiatan">
                <div id="content-kegiatan"></div>
            </div>
            <div class="tab-pane has-padding" id="view-group-inacbg">
                <div id="content-group-inacbg"></div>
            </div>
            <div class="tab-pane has-padding" id="view-tindakan">
                <div id="content-tindakan"></div>
            </div>
            <div class="tab-pane has-padding" id="view-paket">
                <div id="content-paket"></div>
            </div>
            <div class="tab-pane has-padding" id="view-paket-fisio">
                <div id="content-paket-fisio"></div>
            </div>
            <div class="tab-pane has-padding" id="view-tindakan-ruangan">
                <div id="content-tindakan-ruangan"></div>
            </div>
            <div class="tab-pane has-padding" id="view-paket-ruangan">
                <div id="content-paket-ruangan"></div>
            </div>
            <div class="tab-pane has-padding" id="view-tindakan-bmhp">
                <div id="content-tindakan-bmhp"></div>
            </div>
            <div class="tab-pane has-padding" id="view-tindakan-luar-bedah">
                <div id="content-tindakan-luar-bedah"></div>
            </div>
            <div class="tab-pane has-padding" id="view-tindakan-spesialis">
                <div id="content-tindakan-spesialis"></div>
            </div>
        </div>
    </div>
</div>

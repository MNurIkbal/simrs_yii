<?php

/**
 * @Author: afil
 * @Date:   2018-01-15 16:31:06
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-02-26 11:10:19
 * @Description: 
 */

use yii\web\View;
use yii\helpers\Html;
?>

    <div class="col-md-3">
        <ul class="nav nav-tabs nav-tabs-highlight border-bar">
            <li class="active" id="tab-riwayat-penyakit">
                <a href="#view-riwayat-penyakit" data-toggle="tab" aria-expanded="true">
                    <?=Yii::t('fe', 'Riwayat Penyakit')?>
                </a>
            </li>
            <li class="" id="tab-pemeriksaan-fisik">
                <a href="#view-pemeriksaan-fisik" data-toggle="tab" aria-expanded="true">
                    <?=Yii::t('fe', 'Pemeriksaan Fisik')?>
                </a>
            </li>
            <li class="" id="tab-penunjang">
                <a href="#view-penunjang" data-toggle="tab" aria-expanded="true">
                    <?=Yii::t('fe', 'Pemeriksaan MCU')?>
                </a>
            </li>

            <?php if(empty($format_mcu) || $format_mcu == 'default'): ?>
            <li class="" id="tab-kesimpulan">
                <a href="#view-kesimpulan" data-toggle="tab" aria-expanded="true">
                    <?=Yii::t('fe', 'Hasil MCU')?>
                </a>
            </li>
            <?php endif; ?>

            <?php if(! empty($format_mcu) && $format_mcu == 'prima'): ?>
            <li class="" id="tab-resume-pemeriksaan">
                <a href="#view-resume-pemeriksaan" data-toggle="tab" aria-expanded="true">
                    <?=Yii::t('fe', 'Resume Hasil Pemeriksaan')?>
                </a>
            </li>
            <?php endif; ?>

            <?php if(! empty($format_mcu) && $format_mcu == 'prima'): ?>
            <li class="" id="tab-status-kesehatan">
                <a href="#view-status-kesehatan" data-toggle="tab" aria-expanded="true">
                    <?=Yii::t('fe', 'Status Kesehatan')?>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if(! empty($format_mcu) && $format_mcu == 'prima'): ?>
            <li class="" id="tab-hasil-pemeriksaan-kesehatan">
                <a href="#view-status-kesehatan" data-toggle="tab" aria-expanded="true">
                    <?=Yii::t('fe', 'Hasil Pemeriksaan Kesehatan (Khusus PHR)')?>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="col-md-9">
        <div class="tab-content">
            <div class="tab-pane" id="view-penunjang">
                <div id="content-penunjang">  </div>
            </div>
            <div class="tab-pane active" id="view-riwayat-penyakit">
                <div id="content-riwayat-penyakit">  </div>
            </div>
            <div class="tab-pane" id="view-pemeriksaan-fisik">
                <div id="content-pemeriksaan-fisik">  </div>
            </div>
            
            <?php if(empty($format_mcu) || $format_mcu == 'default'): ?>
            <div class="tab-pane" id="view-kesimpulan">
                <div id="content-kesimpulan">  </div>
            </div>
            <?php endif; ?>

            <?php if(! empty($format_mcu) && $format_mcu == 'prima'): ?>
            <div class="tab-pane" id="view-resume-pemeriksaan">
                <div id="content-resume-pemeriksaan">  </div>
            </div>
            <?php endif; ?>
            
            <?php if(! empty($format_mcu) && $format_mcu == 'prima'): ?>
            <div class="tab-pane" id="view-status-kesehatan">
                <div id="content-status-kesehatan">  </div>
            </div>
            <?php endif; ?>

            <?php if(! empty($format_mcu) && $format_mcu == 'prima'): ?>
            <div class="tab-pane" id="view-hasil-pemeriksaan-kesehatan">
                <div id="content-hasil-pemeriksaan-kesehatan">  </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

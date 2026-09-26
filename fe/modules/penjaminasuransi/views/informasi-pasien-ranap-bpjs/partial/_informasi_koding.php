<?php

use app\components\DocoConstants;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DateTimePicker;
use kartik\widgets\DepDrop;
?>

<!-- Diagnosa Pasien -->
<div class="row p-5 ml-5">
    <div class="col-md-12">
        <div class="tabbable" style="position: relative">
            <div class="nav-sticky-wrapper nav-sticky-cppt" id="nav-sticky">
                <div class="nav nav-tabs nav-tab-cppt nav-tab-periksa" id="nav-tab" role="tablist">
                    <button id="tab-unu" style="min-width: 250px;" class="nav-item nav-tab-type nav-link" data-toggle="tab" href="#unu-grouper" role="tab" aria-controls="#nav-tab" aria-expanded="true" aria-selected="true">Coding UNU Grouper</button>
                    <button id="tab-ina" style="min-width: 250px;" class="nav-item nav-tab-type nav-link" data-toggle="tab" href="#ina-grouper" role="tab" aria-controls="#nav-tab" aria-expanded="true" aria-selected="true">Coding INA Grouper</button>
                </div>
            </div>
        </div>
        <div class="tab-content">
            <div class="tab-pane active" id="unu-grouper">
                <div class="row p-5">
                    <div class="col-md-2"><br><label><b>Diagnosa (ICD 10)</b></label></div>
                    <div class="col-md-9"">
                        <table class=" table table-bordered tbl-icd-10" style="border-collapse: collapse;">
                        <tbody>
                            <?php
                            $dupliDiagnosa = [];
                            $icdPrimer = '';
                            $i = 0;
                            foreach ($unuGrouper as $key => $value) {
                                if ($value['is_icdprimer'] == 'true') {
                                    $icdPrimer = $value['diagnosa_kode'];
                                }
                                if (!in_array($value['diagnosa_kode'], $dupliDiagnosa)) {
                                    $dupliDiagnosa[] = $value['diagnosa_kode'];
                                    if ($value['kelompokdiagnosa_id'] == DocoConstants::MAP_DIAGNOSA_TAMBAHAN || $value['kelompokdiagnosa_id'] == DocoConstants::VAR_KELOMPOK_DIAGNOSA_PENYERTA) {
                                        $primarybadge = '';
                                        $secondarybadge = '';
                                        if ($value['is_icdprimer'] == 'true' || $icdPrimer == $value['diagnosa_kode']) {
                                            $primarybadge = '<span id="label-primary" class="badge badge-warning font-14" type="10" dig-id="' . $value['diagnosa_id'] . '" dig-kode="' . $value['diagnosa_kode'] . '" style="float:right">ICD Primer</span>';
                                        }
                                        if ($value['kelompokdiagnosa_id'] == DocoConstants::VAR_KELOMPOK_DIAGNOSA_PENYERTA) {
                                            $secondarybadge = '<span id="label-primary" class="badge badge-info font-14" type="10" dig-id="' . $value['diagnosa_id'] . '" dig-kode="' . $value['diagnosa_kode'] . '" style="float:right">ICD Sekunder</span>';
                                        }
                            ?>
                                        <tr>
                                            <th class="list-diagnosa" style="width: 60%; border-right: 0px;"><?= $value['diagnosa_nama'] ?>
                                            <th class="dig-aksi" style="border-right: 0px;border-left: 0px;">
                                                <?php
                                                if ($value['is_icdprimer'] != 'true' || $icdPrimer != $value['diagnosa_kode']) {
                                                ?>
                                                    <button id="set-<?= $value['diagnosa_id'] ?>" style="float:right; margin-top: -3px;" data-target="dig-icd-10" data-key="<?= 'a' . $i ?>" class="btn btn-warning btn-md set-primer hidden hide-me font-13" dig-id=<?= $value['diagnosa_id'] ?> dig-kode=<?= $value['diagnosa_kode'] ?>>Set Primer</button>
                                                <?php
                                                } else {
                                                ?>
                                                    <button id="set-<?= $value['diagnosa_id'] ?>" style="float:right; margin-top: -3px;" data-target="dig-icd-10" data-key="<?= 'a' . $i ?>" class="btn btn-warning btn-md set-primer hide-me hidden font-13" dig-id=<?= $value['diagnosa_id'] ?> dig-kode=<?= $value['diagnosa_kode'] ?>>Set Primer</button>
                                                <?php
                                                }
                                                ?>
                                            </th>
                                            </th>
                                            <th class="dig-info" style="width: 190px; border-left: 0px; border-right: 0px;">
                                                <?= $primarybadge ?> <span class="badge badge-primary font-14" style="float:left;margin-right: 3px"><?= $value['diagnosa_kode'] ?> </span>
                                                <?php
                                                if ($value['is_icdprimer'] != 'true' || $icdPrimer != $value['diagnosa_kode']) {
                                                ?>
                                                    <?= $secondarybadge ?>
                                                    <button id="del-<?= $value['diagnosa_id'] ?>" style="float:right; margin-top: -3px;" data-target="tbl-icd-10" data-key="<?= 'p' . $i ?>" class="btn btn-danger btn-lg hidden btn-remove-diagnosa hide-me" style="float:left" type="10" dig-id=<?= $value['diagnosa_id'] ?> dig-kode=<?= $value['diagnosa_kode'] ?>><i class="fa fa-trash"></i></button>
                                                <?php
                                                } else {
                                                ?>
                                                    <button id="del-<?= $value['diagnosa_id'] ?>" style="float:right; margin-top: -3px;" data-target="tbl-icd-10" data-key="<?= 'p' . $i ?>" class="btn btn-danger btn-lg hidden btn-remove-diagnosa hide-me hidden" style="float:left" type="10" dig-id=<?= $value['diagnosa_id'] ?> dig-kode=<?= $value['diagnosa_kode'] ?>><i class="fa fa-trash"></i></button>
                                                <?php
                                                }
                                                ?>
                                            </th>
                                        </tr>
                                        <?php $i++ ?>
                            <?php
                                    }
                                }
                            }
                            ?>
                            <?php if ($i == 0) : ?>
                                <tr class="row-null empty-icd9">
                                    <th colspan="2" style="color:red">Tidak Ada diagnosa dengan ICD 10 yang dipilih</th>
                                </tr>
                            <?php endif; ?> 
                        </tbody>
                        </table>
                        <br>
                    </div>
                </div>
                <div class="row p-5">
                    <div class="col-md-2"><br><label><b>Diagnosa (ICD 9)</b></label></div>
                    <div class="col-md-9">
                        <table class="table table-bordered tbl-icd-9" style="border-collapse: collapse;">
                            <?php
                            $dupliProc = [];
                            $x = 0;
                            foreach ($unuGrouper as $key => $value) {
                                if (!in_array($value['diagnosa_kode'], $dupliProc)) {
                                    $dupliProc[] = $value['diagnosa_kode'];
                                    if ($value['kelompokdiagnosa_id'] == DocoConstants::DIAGNOSA_TERAPI) {
                                        $primarybadge = '';
                            ?>
                                        <tr>
                                            <th style="width: 60%; border-right: 0px;"><?= $value['diagnosa_nama'] ?> </th>
                                            <th class="dig-aksi" style="border-right: 0px;border-left: 0px;">
                                            </th>
                                            <th class="dig-info text-right" style="border-left: 0px; width: 190px;">
                                                <span class="badge badge-primary font-14" style="margin-right: 106px"><?= $value['diagnosa_kode'] ?></span>
                                                <button style="float:right; margin-top: -3px" data-target="tbl-icd-9" data-key="<?= 's' . $x ?>" class="btn btn-danger btn-lg hidden btn-remove-diagnosa hide-me" style="float:left" type="9" dig-id=<?= $value['diagnosa_id'] ?> dig-kode=<?= $value['diagnosa_kode'] ?>><i class="fa fa-trash"></i></button>
                                            </th>
                                        </tr>
                                <?php
                                        $x++;
                                    }
                                }
                            }
                            if ($x == 0) {
                                ?>
                                <tr class="row-null empty-icd9">
                                    <th colspan="2" style="color:red">Tidak Ada diagnosa dengan ICD 9 yang dipilih</th>
                                </tr>
                            <?php
                            }
                            ?>
                        </table>
                        <br>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="ina-grouper">
                <div class="row p-5">
                    <div class="col-md-2"><br><label><b>Diagnosa (ICD 10)</b></label></div>
                    <div class="col-md-9"">
                        <table class=" table table-bordered tbl-icd-10" style="border-collapse: collapse;">
                        <tbody>
                            <?php
                            $dupliDiagnosa = [];
                            $icdPrimer = '';
                            $i = 0;
                            foreach ($inaGrouper as $key => $value) {
                                if ($value['is_icdprimer'] == 'true') {
                                    $icdPrimer = $value['diagnosa_kode'];
                                }
                                if (!in_array($value['diagnosa_kode'], $dupliDiagnosa)) {
                                    $dupliDiagnosa[] = $value['diagnosa_kode'];
                                    if ($value['kelompokdiagnosa_id'] == DocoConstants::MAP_DIAGNOSA_TAMBAHAN || $value['kelompokdiagnosa_id'] == DocoConstants::VAR_KELOMPOK_DIAGNOSA_PENYERTA) {
                                        $primarybadge = '';
                                        $secondarybadge = '';
                                        if ($value['is_icdprimer'] == 'true' || $icdPrimer == $value['diagnosa_kode']) {
                                            $primarybadge = '<span id="label-primary" class="badge badge-warning font-14" type="10" dig-id="' . $value['diagnosa_id'] . '" dig-kode="' . $value['diagnosa_kode'] . '" style="float:right">ICD Primer</span>';
                                        }
                                        if ($value['kelompokdiagnosa_id'] == DocoConstants::VAR_KELOMPOK_DIAGNOSA_PENYERTA) {
                                            $secondarybadge = '<span id="label-primary" class="badge badge-info font-14" type="10" dig-id="' . $value['diagnosa_id'] . '" dig-kode="' . $value['diagnosa_kode'] . '" style="float:right">ICD Sekunder</span>';
                                        }
                            ?>
                                        <tr>
                                            <th class="list-diagnosa" style="width: 60%; border-right: 0px;"><?= $value['diagnosa_nama'] ?>
                                            <th class="dig-aksi" style="border-right: 0px;border-left: 0px;">
                                                <?php
                                                if ($value['is_icdprimer'] != 'true' || $icdPrimer != $value['diagnosa_kode']) {
                                                ?>
                                                    <button id="set-<?= $value['diagnosa_id'] ?>" style="float:right; margin-top: -3px;" data-target="dig-icd-10" data-key="<?= 'a' . $i ?>" class="btn btn-warning btn-md set-primer hidden hide-me font-13" dig-id=<?= $value['diagnosa_id'] ?> dig-kode=<?= $value['diagnosa_kode'] ?>>Set Primer</button>
                                                <?php
                                                } else {
                                                ?>
                                                    <button id="set-<?= $value['diagnosa_id'] ?>" style="float:right; margin-top: -3px;" data-target="dig-icd-10" data-key="<?= 'a' . $i ?>" class="btn btn-warning btn-md set-primer hide-me hidden font-13" dig-id=<?= $value['diagnosa_id'] ?> dig-kode=<?= $value['diagnosa_kode'] ?>>Set Primer</button>
                                                <?php
                                                }
                                                ?>
                                            </th>
                                            </th>
                                            <th class="dig-info" style="width: 190px; border-left: 0px; border-right: 0px;">
                                                <?= $primarybadge ?> <span class="badge badge-primary font-14" style="float:left;margin-right: 3px"><?= $value['diagnosa_kode'] ?> </span>
                                                <?php
                                                if ($value['is_icdprimer'] != 'true' || $icdPrimer != $value['diagnosa_kode']) {
                                                ?>
                                                    <?= $secondarybadge ?>
                                                    <button id="del-<?= $value['diagnosa_id'] ?>" style="float:right; margin-top: -3px;" data-target="tbl-icd-10" data-key="<?= 'p' . $i ?>" class="btn btn-danger btn-lg hidden btn-remove-diagnosa hide-me" style="float:left" type="10" dig-id=<?= $value['diagnosa_id'] ?> dig-kode=<?= $value['diagnosa_kode'] ?>><i class="fa fa-trash"></i></button>
                                                <?php
                                                } else {
                                                ?>
                                                    <button id="del-<?= $value['diagnosa_id'] ?>" style="float:right; margin-top: -3px;" data-target="tbl-icd-10" data-key="<?= 'p' . $i ?>" class="btn btn-danger btn-lg hidden btn-remove-diagnosa hide-me hidden" style="float:left" type="10" dig-id=<?= $value['diagnosa_id'] ?> dig-kode=<?= $value['diagnosa_kode'] ?>><i class="fa fa-trash"></i></button>
                                                <?php
                                                }
                                                ?>
                                            </th>
                                        </tr>
                                        <?php $i++ ?>
                            <?php
                                    }
                                }
                            }
                            ?>
                            <?php if ($i == 0) : ?>
                                <tr class="row-null empty-icd9">
                                    <th colspan="2" style="color:red">Tidak Ada diagnosa dengan ICD 10 yang dipilih</th>
                                </tr>
                            <?php endif; ?> 
                        </tbody>
                        </table>
                        <br>
                    </div>
                </div>
                <div class="row p-5">
                    <div class="col-md-2"><br><label><b>Diagnosa (ICD 9)</b></label></div>
                    <div class="col-md-9">
                        <table class="table table-bordered tbl-icd-9" style="border-collapse: collapse;">
                            <?php
                            $dupliProc = [];
                            $x = 0;
                            foreach ($inaGrouper as $key => $value) {
                                if (!in_array($value['diagnosa_kode'], $dupliProc)) {
                                    $dupliProc[] = $value['diagnosa_kode'];
                                    if ($value['kelompokdiagnosa_id'] == DocoConstants::DIAGNOSA_TERAPI) {
                                        $primarybadge = '';
                            ?>
                                        <tr>
                                            <th style="width: 60%; border-right: 0px;"><?= $value['diagnosa_nama'] ?> </th>
                                            <th class="dig-aksi" style="border-right: 0px;border-left: 0px;">
                                            </th>
                                            <th class="dig-info text-right" style="border-left: 0px; width: 190px;">
                                                <span class="badge badge-primary font-14" style="margin-right: 106px"><?= $value['diagnosa_kode'] ?></span>
                                                <button style="float:right; margin-top: -3px" data-target="tbl-icd-9" data-key="<?= 's' . $x ?>" class="btn btn-danger btn-lg hidden btn-remove-diagnosa hide-me" style="float:left" type="9" dig-id=<?= $value['diagnosa_id'] ?> dig-kode=<?= $value['diagnosa_kode'] ?>><i class="fa fa-trash"></i></button>
                                            </th>
                                        </tr>
                                <?php
                                        $x++;
                                    }
                                }
                            }
                            if ($x == 0) {
                                ?>
                                <tr class="row-null empty-icd9">
                                    <th colspan="2" style="color:red">Tidak Ada diagnosa dengan ICD 9 yang dipilih</th>
                                </tr>
                            <?php
                            }
                            ?>
                        </table>
                        <br>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
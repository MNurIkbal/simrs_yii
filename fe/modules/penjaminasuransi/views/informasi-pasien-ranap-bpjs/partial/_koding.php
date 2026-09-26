<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;

?>

<div class="row">
    <div class="col-md-12">
        <div class="tabbable" style="position: relative">
            <div class="nav-sticky-wrapper nav-sticky-cppt" id="nav-sticky">
                <div class="nav nav-tabs nav-tab-cppt nav-tab-periksa" id="nav-tab" role="tablist">
                    <button id="tab-unu" style="min-width: 250px;" class="nav-item nav-tab-type nav-link" data-toggle="tab" href="#view-anamnesa" role="tab" aria-controls="#nav-tab" aria-expanded="true" aria-selected="true">Coding UNU Grouper</button>
                    <button id="tab-ina" style="min-width: 250px;" class="nav-item nav-tab-type nav-link" data-toggle="tab" href="#test-anamnesa" role="tab" aria-controls="#nav-tab" aria-expanded="true" aria-selected="true">Coding INA Grouper</button>
                </div>
            </div>
            <?php
            $action = !empty($action) ? $action : null;
            $form = ActiveForm::begin([
                'id' => 'form-koreksi-diagnosa',
                'enableAjaxValidation' => false,
                'enableClientValidation' => false,
                'type' => ActiveForm::TYPE_VERTICAL,
                'formConfig' => [
                    'labelSpan' => 3,
                    'deviceSize' => ActiveForm::SIZE_SMALL
                ],
                'options' => [],
                'action' => $action
            ]);
            ?>
            <div class="tab-content">
                <div class="tab-pane active" id="view-anamnesa">
                    <div id="content-anamnesa">
                        <h6>UNU Grouper</h6>
                        <div class="row">
                            <div class="col-md-12">
                                <table id="tbl-klaim" class="table table-striped table-condensed table-hover" style="width: 100%">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th width="1">No</th>
                                            <th width="8%"><?= \Yii::t("fe", "Jenis diagnosa"); ?></th>
                                            <th><?= \Yii::t("fe", "Nama diagnosa"); ?></th>
                                            <th><?= \Yii::t("fe", "Koreksi Diagnosa"); ?></th>
                                            <th width="5%"></th>
                                            <th class="hidden"><?= \Yii::t("fe", "ICD Primary"); ?></th>
                                            <th class="hidden"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        $idTr = 1;
                                        foreach ($diagnosa['detail'] as $key => $value) {
                                            $textIcd = isset($value['text']) ? $value['text'] : null;
                                            $kelompok = isset($diagnosa['mapping'][$key]) ? $diagnosa['mapping'][$key] : null;

                                            $idIcd = isset($value['id']) ? $value['id'] : null;
                                            $default = $idIcd ? [$idIcd => $textIcd] : [];

                                            if (isset($diagnosa['hasil_diagnosa'][$kelompok . '-' . $textIcd])) {
                                                $default = $diagnosa['hasil_diagnosa'][$kelompok . '-' . $textIcd];
                                            }



                                            $dataType = ($key == 'Tindakan/Operasi') ? 'ICD IX' : 'ICD X';
                                            if ($key != 'Utama') {
                                                $penyerta = isset($diagnosa['mapping']['Terapi']) ? $diagnosa['mapping']['Terapi'] : null;
                                                $diagnosaNewPenyerta = isset($diagnosa['mapping']['Penyerta']) ? $diagnosa['mapping']['Penyerta'] : null;
                                                $diagnosaMasuk = isset($diagnosa['mapping']['Masuk']) ? $diagnosa['mapping']['Masuk'] : null;
                                                $kelompok = ($key == 'Tindakan/Operasi') ? $penyerta : null;
                                                if ($key == 'Tindakan/Operasi') {
                                                    $kelompok = $penyerta;
                                                }

                                                if ($key == 'Tambahan') {
                                                    $kelompok = $diagnosaNewPenyerta;
                                                }

                                                if ($key == 'Sebab Luar') {
                                                    $kelompok = $diagnosaMasuk;
                                                }

                                                if ($key == 'Morfologi') {
                                                    $kelompok = 9;
                                                }

                                                if (is_array($value)) {
                                                    foreach ($value as $k => $v) {

                                                        $textIcd = isset($v['text']) ? $v['text'] : null;
                                                        $diagnosa_lama = isset($v['diagnosa_lama']) ? $v['diagnosa_lama'] : null;
                                                        $idIcd = isset($v['id']) ? $v['id'] : null;
                                                        $default = isset($v['id']) ? [$idIcd => $textIcd] : [null => '--Pilih--'];

                                                        $checkbox = (isset($v['is_inacbg']) && $v['is_inacbg']) ? 'checked' : '';
                                                        if ($checkbox == 'checked') {
                                                            if ($v['is_icdprimer']) {
                                                                $radio = 'checked';
                                                            } else {
                                                                $radio = '';
                                                            }
                                                        } else {
                                                            $radio = 'disabled';
                                                        }

                                                        if (isset($diagnosa['hasil_diagnosa'][$kelompok . '-' . $textIcd])) {
                                                            $default = $diagnosa['hasil_diagnosa'][$kelompok . '-' . $textIcd];
                                                        } else if (isset($diagnosa['hasil_diagnosa'][$kelompok . '-' . $k . ' - ' . $textIcd])) {
                                                            $default = $diagnosa['hasil_diagnosa'][$kelompok . '-' . $k . ' - ' . $textIcd];
                                                        }

                                                        if (!$v) {
                                                            $checkbox = 'disabled';
                                                            $radio = 'disabled';
                                                        }
                                                        if ($k == 0) { ?>
                                                            <tr class="tr-diagnosa-<?= isset($kelompok) ? $kelompok : 3 ?>">
                                                                <td width="1%" rowspan="<?= count($value) ?>" class="diagnosa-<?= $kelompok ?>"><?= $no++ ?></td>
                                                                <td width="1%" class="text-center diagnosa-<?= $kelompok ?>" rowspan="<?= count($value) ?>"><?= $labelINACBS[$key] ?></td>
                                                                <td width="20%" class="diagnosa-nama"><?= $diagnosa_lama; ?></td>
                                                                <td width="30%">
                                                                    <?= Html::dropDownList('koreksi_diagnosa[]', $idIcd, $default, [
                                                                        'class' => 'koreksi-diagnosa',
                                                                        'data-type' => $dataType,
                                                                        'data-kelompok' => isset($kelompok) ? $kelompok : 3,
                                                                        'data-icd' => $idIcd,
                                                                        'data-asal' => ($idIcd ? $textIcd : $k . ' - ' . $textIcd)
                                                                    ]) ?>
                                                                </td>
                                                                <td width="4%" class="padding-0">
                                                                    <?php if ($key != 'Masuk') { ?>
                                                                        <button type="button" id="add-diagnosa-<?= $kelompok ?>" class="btn btn-success btn-xsm"><i class="fa fa-plus"></i></button>
                                                                        <button type="button" class="btn btn-danger btn-xsm diagnosa-reset"><i class="fa fa-trash"></i></button>
                                                                    <?php } ?>
                                                                </td>
                                                                <td class="hidden">
                                                                    <input type='checkbox' class='check-inacbg validate-update' name='FormKoreksi[is_inacbg][<?= $idIcd ?>]' <?= $checkbox; ?>>
                                                                </td>
                                                                <td class="hidden">

                                                                </td>
                                                            </tr>
                                                        <?php } else { ?>
                                                            <tr class="tr-diagnosa-<?= $kelompok ?>">
                                                                <td width="20%" class="diagnosa-nama"><?= $diagnosa_lama; ?></td>
                                                                <td width="30%">
                                                                    <?= Html::dropDownList('koreksi_diagnosa[]', $idIcd, $default, [
                                                                        'class' => 'koreksi-diagnosa',
                                                                        'data-type' => $dataType,
                                                                        'data-kelompok' => $kelompok,
                                                                        'data-icd' => $idIcd,
                                                                        'data-asal' => ($idIcd ? $textIcd : $k . ' - ' . $textIcd)
                                                                    ]) ?>
                                                                </td>
                                                                <td width="1%">
                                                                    <button type="button" class="btn btn-danger btn-xsm remove-diagnosa-<?= $kelompok ?>"><i class="fa fa-trash"></i></button>
                                                                </td>
                                                                <td class="hidden">
                                                                    <input type='checkbox' class='check-inacbg validate-update' name='FormKoreksi[is_inacbg][<?= $idIcd ?>]' <?= $checkbox; ?>>
                                                                </td>
                                                                <td class="hidden">

                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } else {
                                                $checkbox = (isset($value['is_inacbg']) && $value['is_inacbg']) ? 'checked' : '';
                                                if ($checkbox == 'checked') {
                                                    $radio = 'checked';
                                                } else {
                                                    $radio = 'disabled';
                                                }
                                            ?>
                                                <tr>
                                                    <td width="1%"><?= $no++ ?></td>
                                                    <td width="1%" class="text-center"><?= $labelINACBS[$key] ?></td>
                                                    <td width="20%" class="diagnosa-nama"><?= isset($value['diagnosa_lama']) ? $value['diagnosa_lama'] : ''; ?></td>
                                                    <td width="30%">
                                                        <?= Html::dropDownList('koreksi_diagnosa[]', $idIcd, $default, [
                                                            'class' => 'koreksi-diagnosa',
                                                            'data-type' => $dataType,
                                                            'data-kelompok' => $kelompok,
                                                            'data-icd' => $idIcd,
                                                            'data-asal' => $textIcd
                                                        ]) ?>
                                                    </td>
                                                    <td width="1%">

                                                    </td>
                                                    <td width="1%" class="hidden">
                                                        <input type='checkbox' class='check-inacbg validate-update' name='FormKoreksi[is_inacbg][<?= $idIcd ?>]' <?= $checkbox; ?>>
                                                    </td>
                                                    <td width="1%" class="hidden">
                                                        <input type='radio' value='<?= $idIcd ?>' class='radio-icdprimer validate-update' name='FormKoreksi[is_icdprimer]' data-type='<?= $dataType; ?>' data-kelompok='<?= $kelompok; ?>' <?= $radio; ?>>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="test-anamnesa">
                    <div id="content-anamnesa">
                        <h6>INA Grouper</h6>
                        <div class="row mv-5">
                            <div class="col-md-12">
                                <div style="display: flex; justify-content: flex-end;">
                                    <a style="font-size: 20px; cursor: pointer;" id="import_koding"><u>[import koding]</u></a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <table id="tbl-klaim-ina-grouper" class="table table-striped table-condensed table-hover" style="width: 100%">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th width="1">No</th>
                                            <th width="8%"><?= \Yii::t("fe", "Jenis diagnosa"); ?></th>
                                            <th><?= \Yii::t("fe", "Nama diagnosa"); ?></th>
                                            <th><?= \Yii::t("fe", "Koreksi Diagnosa"); ?></th>
                                            <th width="5%"></th>
                                            <th class="hidden"><?= \Yii::t("fe", "ICD Primary"); ?></th>
                                            <th class="hidden"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        $idTr = 1;
                                        $default = [];
                                        foreach ($diagnosa['detail_ina'] as $key => $value) :
                                            $textIcd = isset($value['text']) ? $value['text'] : null;
                                            $kelompok = isset($diagnosa['mapping'][$key]) ? $diagnosa['mapping'][$key] : null;

                                            $idIcd = isset($value['id']) ? $value['id'] : null;
                                            $default = $idIcd ? [$idIcd => $textIcd] : [];

                                            if (isset($diagnosa['hasil_diagnosa'][$kelompok . '-' . $textIcd])) {
                                                $default = $diagnosa['hasil_diagnosa'][$kelompok . '-' . $textIcd];
                                            }

                                            $dataType = ($key == 'Tindakan/Operasi') ? 'ICD IX' : 'ICD X';
                                            if ($key != 'Utama') {
                                                $penyerta = isset($diagnosa['mapping']['Terapi']) ? $diagnosa['mapping']['Terapi'] : null;
                                                $diagnosaNewPenyerta = isset($diagnosa['mapping']['Penyerta']) ? $diagnosa['mapping']['Penyerta'] : null;
                                                $diagnosaMasuk = isset($diagnosa['mapping']['Masuk']) ? $diagnosa['mapping']['Masuk'] : null;
                                                $kelompok = ($key == 'Tindakan/Operasi') ? $penyerta : null;
                                                if ($key == 'Tindakan/Operasi') {
                                                    $kelompok = $penyerta;
                                                }

                                                if ($key == 'Tambahan') {
                                                    $kelompok = $diagnosaNewPenyerta;
                                                }

                                                if ($key == 'Sebab Luar') {
                                                    $kelompok = $diagnosaMasuk;
                                                }

                                                if ($key == 'Morfologi') {
                                                    $kelompok = 9;
                                                }

                                                if (is_array($value)) {
                                                    foreach ($value as $k => $v) {
                                                        $textIcd = isset($v['text']) ? $v['text'] : null;
                                                        $diagnosa_lama = isset($v['diagnosa_lama']) ? $v['diagnosa_lama'] : null;;
                                                        $idIcd = isset($v['id']) ? $v['id'] : null;
                                                        $default = isset($v['id']) ? [$idIcd => $textIcd] : [null => '--Pilih--'];

                                                        $checkbox = (isset($v['is_inacbg']) && $v['is_inacbg']) ? 'checked' : '';
                                                        if ($checkbox == 'checked') {
                                                            if ($v['is_icdprimer']) {
                                                                $radio = 'checked';
                                                            } else {
                                                                $radio = '';
                                                            }
                                                        } else {
                                                            $radio = 'disabled';
                                                        }

                                                        if (isset($diagnosa['hasil_diagnosa'][$kelompok . '-' . $textIcd])) {
                                                            $default = $diagnosa['hasil_diagnosa'][$kelompok . '-' . $textIcd];
                                                        } else if (isset($diagnosa['hasil_diagnosa'][$kelompok . '-' . $k . ' - ' . $textIcd])) {
                                                            $default = $diagnosa['hasil_diagnosa'][$kelompok . '-' . $k . ' - ' . $textIcd];
                                                        }

                                                        if (!$v) {
                                                            $checkbox = 'disabled';
                                                            $radio = 'disabled';
                                                        }
                                                        if ($k == 0) { ?>
                                                            <tr class="tr-diagnosa-ina-<?= isset($kelompok) ? $kelompok : 3 ?>">
                                                                <td width="1%" rowspan="<?= count($value) ?>" class="diagnosa-ina-<?= $kelompok ?>" data-ina="1"><?= $no++ ?></td>
                                                                <td width="1%" class="text-center diagnosa-ina-<?= $kelompok ?>" rowspan="<?= count($value) ?>"><?= $labelINACBS[$key] ?></td>
                                                                <td width="20%" class="diagnosa-nama"><?= $diagnosa_lama; ?></td>
                                                                <td width="30%">
                                                                    <?= Html::dropDownList('koreksi_diagnosa_ina[]', $idIcd, $default, [
                                                                        'class' => 'koreksi-diagnosa',
                                                                        'data-type' => $dataType,
                                                                        'data-kelompok' => isset($kelompok) ? $kelompok : 3,
                                                                        'data-icd' => $idIcd,
                                                                        'data-asal' => ($idIcd ? $textIcd : $k . ' - ' . $textIcd),
                                                                        'data-ina' => 1
                                                                    ]) ?>
                                                                </td>
                                                                <td width="4%" class="padding-0">
                                                                    <?php if ($key != 'Masuk') { ?>
                                                                        <button type="button" id="add-diagnosa-ina-<?= $kelompok ?>" data-ina="1" class="btn btn-success btn-xsm"><i class="fa fa-plus"></i></button>
                                                                        <button type="button" data-ina="1" class="btn btn-danger btn-xsm diagnosa-reset"><i class="fa fa-trash"></i></button>
                                                                    <?php } ?>
                                                                </td>
                                                                <td class="hidden">
                                                                    <input type='checkbox' class='check-inacbg validate-update' name='FormKoreksi[is_inacbg][<?= $idIcd ?>]' <?= $checkbox; ?>>
                                                                </td>
                                                                <td class="hidden">

                                                                </td>
                                                            </tr>
                                                        <?php } else { ?>
                                                            <tr class="tr-diagnosa-ina-<?= $kelompok ?>">
                                                                <td width="20%" class="diagnosa-nama"><?= $diagnosa_lama; ?></td>
                                                                <td width="30%">
                                                                    <?= Html::dropDownList('koreksi_diagnosa_ina[]', $idIcd, $default, [
                                                                        'class' => 'koreksi-diagnosa',
                                                                        'data-type' => $dataType,
                                                                        'data-kelompok' => $kelompok,
                                                                        'data-icd' => $idIcd,
                                                                        'data-asal' => ($idIcd ? $textIcd : $k . ' - ' . $textIcd),
                                                                        'data-ina' => 1
                                                                    ]) ?>
                                                                </td>
                                                                <td width="1%">
                                                                    <button type="button" class="btn btn-danger btn-xsm remove-diagnosa-ina-<?= $kelompok ?>"><i class="fa fa-trash"></i></button>
                                                                </td>
                                                                <td class="hidden">
                                                                    <input type='checkbox' class='check-inacbg validate-update' name='FormKoreksi[is_inacbg][<?= $idIcd ?>]' <?= $checkbox; ?>>
                                                                </td>
                                                                <td class="hidden">

                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } else {
                                                $checkbox = (isset($value['is_inacbg']) && $value['is_inacbg']) ? 'checked' : '';
                                                if ($checkbox == 'checked') {
                                                    $radio = 'checked';
                                                } else {
                                                    $radio = 'disabled';
                                                }
                                            ?>
                                                <tr>
                                                    <td width="1%"><?= $no++ ?></td>
                                                    <td width="1%" class="text-center"><?= $labelINACBS[$key] ?></td>
                                                    <td width="20%" class="diagnosa-nama"><?= isset($value['diagnosa_lama']) ? $value['diagnosa_lama'] : ''; ?></td>
                                                    <td width="30%">
                                                        <?= Html::dropDownList('koreksi_diagnosa_ina[]', $idIcd, $default, [
                                                            'class' => 'koreksi-diagnosa',
                                                            'data-type' => $dataType,
                                                            'data-kelompok' => $kelompok,
                                                            'data-icd' => $idIcd,
                                                            'data-asal' => $textIcd,
                                                            'data-ina' => 1
                                                        ]) ?>
                                                    </td>
                                                    <td width="1%">

                                                    </td>
                                                    <td width="1%" class="hidden">
                                                        <input type='checkbox' class='check-inacbg validate-update' name='FormKoreksi[is_inacbg][<?= $idIcd ?>]' <?= $checkbox; ?>>
                                                    </td>
                                                    <td width="1%" class="hidden">
                                                        <input type='radio' value='<?= $idIcd ?>' class='radio-icdprimer validate-update' name='FormKoreksi[is_icdprimer_ina]' data-type='<?= $dataType; ?>' data-kelompok='<?= $kelompok; ?>' <?= $radio; ?>>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            ActiveForm::end();
            ?>
        </div>
    </div>
</div>
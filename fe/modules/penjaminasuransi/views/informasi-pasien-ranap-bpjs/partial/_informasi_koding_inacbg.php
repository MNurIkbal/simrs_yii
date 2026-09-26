<?php

use yii\helpers\Html;
?>

<!-- Section INACBGS -->
<div class="row m-3 hidden" id="inacbgs-section">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h6 class="panel-title"><b><?= Yii::t('fe', 'INACBGS'); ?></b></h6>
            </div>
            <div class="panel-body">
                <div class="row mb-3 p-5" style="margin-top: 20px;">
                    <div class="col-md-12" style="margin-bottom: 20px;">
                        <button class="hidden" type="button" id="btn-validate-diagnosa"></button>
                        <div class='my-legend'>
                            <div class='legend-title'>Keterangan</div>
                            <div class='legend-scale'>
                                <ul class='legend-labels'>
                                    <li><span class="row-error">Diagnosa / Procedure Tidak Berlaku</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div>
                            <div style="max-width: 350px; display: flex;" class="mb-3">
                                <div style="width: 100%;">
                                    <label for="inacbgsDiagnosa">
                                        <b>Diagnosa ICD - 10</b>
                                    </label>
                                    <select name="inacbgsDiagnosa" id="inacbgsDiagnosa" class="form-control koreksi-diagnosa" data-type="ICD X" data-inacbg="1" data-type-ina="0" data-button="btn-add-diagnosa-inacbgs"></select>
                                </div>
                                <div>
                                    <label></label>
                                    <button type="button" class="btn btn-success hidden btn-sm ml-2 mt-1" id="btn-add-diagnosa-inacbgs">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <table id="inacbgsDiagnosaTable" class="table table-striped table-hover table-bordered " style="width: 100%;">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th>No</th>
                                        <th>Nama Diagnosa</th>
                                        <th>Primer</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="inacbgsDiagnosaBody"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div>
                            <div style="max-width: 350px; display: flex;" class="mb-3">
                                <div style="width: 100%;">
                                    <label for="inacbgsProcedure">
                                        <b>Prosedur ICD - 9</b>
                                    </label>
                                    <select name="inacbgsProcedure" id="inacbgsProcedure" class="form-control koreksi-procedure" data-type="ICD IX" data-button="btn-add-procedure-inacbgs"data-unik="1"></select>
                                </div>
                                <div>
                                    <label for=""></label>
                                    <button type="button" class="btn btn-success hidden btn-sm ml-2 mt-1" id="btn-add-procedure-inacbgs">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <table id="inacbgsProcedureTable" class="table table-striped table-hover table-bordered no-footer" style="width: 100%;">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th>No</th>
                                        <th>Nama Procedure</th>
                                        <th>Multiplicity</th>
                                        <th>Primer</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div id="proses-final-klaim" class="hidden">
                        <hr>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Hasil Grouping INACBGS'); ?></b></h6>
                                        </div>
                                        <div class="panel-body">
                                            <table class="table" style="margin-top: 20px;">
                                                <tr>
                                                    <th style="width: 150px"><?= Yii::t('fe', 'Info') ?></th>
                                                    <td colspan="4" class="info-txt"></td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 150px"><?= Yii::t('fe', 'Jenis Rawat') ?></th>
                                                    <td colspan="4" class="jenisrawat-txt"></td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 150px"><?= Yii::t('fe', 'Group') ?></th>
                                                    <td class="text-center penyakit-nama"></td>
                                                    <td class="text-center kode-penyakit"></td>
                                                    <td class="text-center kolom-nosep"></td>
                                                    <td class="text-right harga-klaim"></td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 150px"><?= Yii::t('fe', 'Sub Acute') ?></th>
                                                    <td class="text-center subacute-detail">-</td>
                                                    <td class="text-center subacute-kode">-</td>
                                                    <td class="text-center"></td>
                                                    <td class="text-right subacute-harga">Rp. 0</td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 150px"><?= Yii::t('fe', 'Chronic') ?></th>
                                                    <td class="text-center cronic-detail">-</td>
                                                    <td class="text-center cronic-kode">-</td>
                                                    <td class="text-center"></td>
                                                    <td class="text-right cronic-harga">Rp. 0</td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 150px"><?= Yii::t('fe', 'Special Procedure') ?></th>
                                                    <td class="text-center">
                                                        <?= Html::dropDownList('sproc_combo', '', [], ['class' => 'form-control sproc-combo spesial-prosedur', 'id' => 'sproc-combo', 'data-placeholder' => 'none']) ?>
                                                    </td>
                                                    <td id="sproc-kode" class="text-center">-</td>
                                                    <td class="text-center"></td>
                                                    <td id="sproc-val" class="text-right">Rp. 0</td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 150px"><?= Yii::t('fe', 'Special Prosthesis') ?></th>
                                                    <td class="text-center">
                                                        <?= Html::dropDownList('spros_combo', '', [], ['class' => 'form-control spros-combo spesial-prosedur', 'id' => 'spros-combo', 'data-placeholder' => 'none']) ?>
                                                    </td>
                                                    <td id="spros-kode" class="text-center">-</td>
                                                    <td class="text-center"></td>
                                                    <td id="spros-val" class="text-right">Rp. 0</td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 150px"><?= Yii::t('fe', 'Special Investigation') ?></th>
                                                    <td class="text-center">
                                                        <?= Html::dropDownList('inv_combo', '', [], ['class' => 'form-control inv-combo spesial-prosedur', 'id' => 'inv-combo', 'data-placeholder' => 'none']) ?>
                                                    </td>
                                                    <td id="inv-kode" class="text-center">-</td>
                                                    <td class="text-center"></td>
                                                    <td id="inv-val" class="text-right">Rp. 0</td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 150px"><?= Yii::t('fe', 'Special Drug') ?></th>
                                                    <td class="text-center">
                                                        <?= Html::dropDownList('drug_combo', '', [], ['class' => 'form-control drug-combo spesial-prosedur', 'id' => 'drug-combo', 'data-placeholder' => 'none']) ?>
                                                    </td>
                                                    <td id="drug-kode" class="text-center">-</td>
                                                    <td class="text-center"></td>
                                                    <td id="drug-val" class="text-right">Rp. 0</td>
                                                </tr>
                                                <tr class="kategoriHemodialysis">
                                                    <th style="width: 150px"><?= Yii::t('fe', 'Penggunaan Dializer') ?></th>
                                                    <td>
                                                        <?= Html::activeRadioList($model, 'dializer', [
                                                            '0' => 'Multiple Use (reuse)',
                                                            '1' => 'Single Use',
                                                        ], [
                                                            'item' => function ($index, $label, $name, $checked, $value) use ($info, $model) {
                                                                $check = "";
                                                                if ($model->dializer == $value) {
                                                                    $check = 'checked="checked"';
                                                                }
                                                                $return = '<label class="radio-' . $value . '">';
                                                                $return .= '<input class="klaiminacbgranapform-dializer" type="radio" name="' . $name . '" value="' . $value . '" tabindex="3"' . $check . ' id="dializer-' . $value . '">';
                                                                $return .= ' <i></i>';
                                                                $return .= '<span>' . ucwords($label) . '</span>';
                                                                $return .= '</label>';
                                                                return $return;
                                                            }
                                                        ]);
                                                        ?>
                                                    </td>
                                                    <td class="text-center persentase-darah">-</td>
                                                    <td class="text-center"></td>
                                                    <td class="text-right nominal-darah">Rp. 0</td>
                                                </tr>
                                                <tr class="kategoriHemodialysis">
                                                    <th style="width: 150px"><?= Yii::t('fe', 'Transfusi Darah') ?></th>
                                                    <td colspan="4">
                                                        <p> Jumlah Kantong darah <?= Html::activeTextInput($model, 'transfusi_darah', ['class' => 'input-sm doco-number', 'style' => 'width:60px; border-radius: 10px;']) ?> Kantong</p>
                                                    </td>
                                                </tr>
                                                <tr class="kemenkes_status_klaim">
                                                    <th style="width: 150px"><?= Yii::t('fe', 'Status Klaim') ?></th>
                                                    <td colspan="4" class="text-left status-klaim">
                                                        <?= Yii::t('fe', '') ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 150px"><?= Yii::t('fe', 'Total') ?></th>
                                                    <td class="text-center"></td>
                                                    <td class="text-center"></td>
                                                    <td class="text-center"></td>
                                                    <td id="total-harga" data="0" class="text-right total-harga"></td>
                                                </tr>

                                                <tr>
                                                    <td colspan="5" class="tbl-tambahan-biaya naik-turun-kelas-hide" style="padding-left: 0; padding-right: 0;">
                                                        <table style="width: 100%; background-color: #fff0; margin-bottom: 30px;" class="table">
                                                            <tr>
                                                                <th colspan="5" class="text-center keterangan-naik-kelas" style="border-top: none;"></th>
                                                            </tr>
                                                            <tr>
                                                                <th style="width: 150px"><?= Yii::t('fe', 'Tambahan Biaya') ?></th>
                                                                <td colspan="3" class="str-tambahan" style="font-size: 11pt;"></td>
                                                                <td class="text-right" style="font-size: 11pt;">= <span class="tambahanbiaya-txt"></span></td>
                                                            </tr>
                                                            <tr>
                                                                <th style="width: 150px; border-bottom: 2px solid #ddd; padding-bottom: 15px !important;"><?= Yii::t('fe', 'Pembayar Selisih Biaya') ?></th>
                                                                <td colspan="4" style="border-bottom: 2px solid #ddd; padding-bottom: 15px !important;">
                                                                    <?= Html::activeRadioList($model, 'pembayar_selisih_biaya', [
                                                                        'peserta' => 'Peserta',
                                                                        'pemberi_kerja' => 'Pemberi Kerja',
                                                                        'asuransi_tambahan' => 'Asuransi Tambahan'
                                                                    ], [
                                                                        'item' => function ($index, $label, $name, $checked, $value) use ($info, $model) {
                                                                            $check = "";
                                                                            if ($model->naik_kelas == $value) {
                                                                                $check = 'checked="checked"';
                                                                            }
                                                                            $return = '<label class="radio-' . $value . '">';
                                                                            $return .= '<input class="" type="radio" name="' . $name . '" value="' . $value . '" tabindex="3"' . $check . ' id="pembayarselisih-' . $value . '">';
                                                                            $return .= ' <i></i>';
                                                                            $return .= '<span>' . ucwords($label) . '</span>';
                                                                            $return .= '</label><br>';
                                                                            return $return;
                                                                        }
                                                                    ]);
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div style="display: flex; justify-content: end">
                            <button type="button" class="btn btn-success m-2 btn-import-koding" id="btn-import-koding">
                                Import INA-CBG
                            </button>
                            <button type="button" class="btn btn-success m-2 btn-grouping-inacbgs" id="btn-grouping-inacbgs">
                                Grouping INA-CBG
                            </button>
                            <button type="button" class="btn btn-success m-2 btn-final-inacbgs" id="btn-final-inacbgs" disabled>
                                Final INA-CBG
                            </button>
                            <button type="button" class="btn btn-success m-2 btn-edit-inacbgs" id="btn-edit-inacbgs" disabled>
                                Update INA-CBG
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 hidden" id="status-klaim-section">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h6 class="panel-title"><b><?= Yii::t('fe', 'Status Final Klaim') ?></b></h6>
            </div>
            <div class="panel-body">
                <table class="table table-striped table-hover">
                    <tr>
                        <th style="width: 150px"><?= Yii::t('fe', 'Status Klaim	') ?></th>
                        <td colspan="4" class="status-klaim text-capitalize">
                        </td>
                    </tr>
                    <tr>
                        <th style="width: 150px"><?= Yii::t('fe', 'Status DC Kemkes') ?></th>
                        <td colspan="4" class="text-left text-danger kemenkes_status">
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div style="display: flex; justify-content: end">
            <button type="button" class="btn btn-success m-2 btn-cetak-klaim hidden" id="formfinal-btn-cetak-klaim">
                Cetak Klaim
            </button>
            <button type="button" class="btn btn-success m-2 btn-kirim-klaim hidden"  id="formfinal-btn-kirim-klaim">
                Kirim Klaim Online
            </button>
            <button type="button" class="btn btn-success m-2 btn-edit-klaim hidden" id="btn-edit-klaim">
                Edit Ulang Klaim
            </button>
            <button type="button" class="btn btn-success m-2 btn-final-klaim hidden" id="btn-final-klaim">
                Final Klaim
            </button>
        </div>
    </div>
</div>
<!-- End Section INACBGS -->
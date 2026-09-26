<?php 

use yii\helpers\Html;

?>

<div class="col-md-12 col-md-offset-1">
    <div class="row">
        <div class="col-md-10">
            <center>
                <h4 id="judul-grouper"><?= Yii::t('fe', 'Hasil Groupper') ?></h4>
            </center>
        </div>
    </div>
    <div class="row">
        <input type="hidden" id="total-naikkelas">
        <input type="hidden" id="total-kelaspelayanan">
        <input type="hidden" id="total-tambahan">
        <div class="col-md-10">
            <table class="table">
                <tr>
                    <th style="width: 150px" class="label-grouper"><?= Yii::t('fe', 'Info') ?></th>
                    <td colspan="4" class="info-txt"></td>
                </tr>
                <tr>
                    <th style="width: 150px" class="label-grouper"><?= Yii::t('fe', 'Jenis Rawat') ?></th>
                    <td colspan="4" class="jenisrawat-txt"></td>
                </tr>
                <tr>
                    <th style="width: 150px" class="label-grouper"><?= Yii::t('fe', 'Group') ?></th>
                    <td class="text-center penyakit-nama"></td>
                    <td class="text-center kode-penyakit"></td>
                    <td class="text-center kolom-nosep"></td>
                    <td class="text-right harga-klaim"></td>
                </tr>
                <tr>
                    <th style="width: 150px" class="label-grouper"><?= Yii::t('fe', 'Sub Acute') ?></th>
                    <td class="text-center subacute-detail accute-grouper">-</td>
                    <td class="text-center subacute-kode accute-grouper">-</td>
                    <td class="text-center"></td>
                    <td class="text-right subacute-harga accute-grouper">Rp. 0</td>
                </tr>
                <tr>
                    <th style="width: 150px" class="label-grouper"><?= Yii::t('fe', 'Chronic') ?></th>
                    <td class="text-center cronic-detail chronic-grouper">-</td>
                    <td class="text-center cronic-kode chronic-grouper">-</td>
                    <td class="text-center"></td>
                    <td class="text-right cronic-harga chronic-grouper">Rp. 0</td>
                </tr>
                <tr>
                    <th style="width: 150px" class="label-grouper"><?= Yii::t('fe', 'Special Procedure') ?></th>
                    <td class="text-center dig-info">
                        <?= Html::dropDownList('sproc_combo', '', [], ['class' => 'form-control sproc-combo spesial-prosedur procedure-grouper', 'id' => 'sproc-combo', 'data-placeholder' => 'None']) ?>
                    </td>
                    <td id="sproc-kode" class="text-center procedure-grouper">-</td>
                    <td class="text-center"></td>
                    <td id="sproc-val" class="text-right spec-val procedure-grouper">Rp. 0</td>
                </tr>
                <tr>
                    <th style="width: 150px" class="label-grouper"><?= Yii::t('fe', 'Special Prosthesis') ?></th>
                    <td class="text-center">
                        <?= Html::dropDownList('spros_combo', '', [], ['class' => 'form-control spros-combo spesial-prosedur prosthesis-grouper', 'id' => 'spros-combo', 'data-placeholder' => 'None']) ?>
                    </td>
                    <td id="spros-kode" class="text-center prosthesis-grouper">-</td>
                    <td class="text-center"></td>
                    <td id="spros-val" class="text-right spec-val prosthesis-grouper">Rp. 0</td>
                </tr>
                <tr>
                    <th style="width: 150px" class="label-grouper"><?= Yii::t('fe', 'Special Investigation') ?></th>
                    <td class="text-center">
                        <?= Html::dropDownList('inv_combo', '', [], ['class' => 'form-control inv-combo spesial-prosedur investigation-grouper', 'id' => 'inv-combo', 'data-placeholder' => 'None']) ?>
                    </td>
                    <td id="inv-kode" class="text-center investigation-grouper">-</td>
                    <td class="text-center"></td>
                    <td id="inv-val" class="text-right spec-val investigation-grouper">Rp. 0</td>
                </tr>
                <tr style="height: 67px">
                    <th style="width: 150px" class="label-grouper"><?= Yii::t('fe', 'Special Drug') ?></th>
                    <td class="text-center drug-grouper">
                        <?= Html::dropDownList('drug_combo', '', [], ['class' => 'form-control drug-combo spesial-prosedur drug-grouper', 'id' => 'drug-combo', 'data-placeholder' => 'None']) ?>
                    </td>
                    <td id="drug-kode" class="text-center drug-grouper">-</td>
                    <td class="text-center"></td>
                    <td id="drug-val" class="text-right spec-val drug-grouper">Rp. 0</td>
                </tr>
                <tr class="kemenkes_status_klaim">
                    <th style="width: 150px" class="label-grouper"><?= Yii::t('fe', 'Status Data Klaim') ?></th>
                    <td colspan="4" class="text-left text-danger kemenkes_status kemenkes-grouper">
                    </td>
                </tr>
                <tr class="kemenkes_status_klaim">
                    <th style="width: 150px" class="label-grouper"><?= Yii::t('fe', 'Status Klaim') ?></th>
                    <td colspan="4" class="text-left kemenkes-grouper">
                        <?= Yii::t('fe', '') ?>
                    </td>
                </tr>
                <tr>
                    <th style="width: 150px" class="label-grouper"><?= Yii::t('fe', 'Total') ?></th>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td id="total-harga" data="0" class="text-right total-harga"></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10">
            <center class="keterangan-naik-kelas">
                <h4><?= Yii::t('fe', 'Tambahan Biaya yang Dibayar Pasien') ?></h4>
            </center>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10">
            <table class="table">
                <tr>
                    <th style="width: 150px" class="label-grouper"><?= Yii::t('fe', 'Tambahan Biaya') ?></th>
                    <td></td>
                    <td colspan="2" class="text-right str-tambahan tambahan-grouper"></td>
                    <td class="text-right tambahanbiaya-txt tambahan-grouper">Rp. 0</td>
                </tr>
            </table>
        </div>
    </div><br>
    <div class="row">
        <div class="button-proc col-sm-4">
            <button type="button" id="formfinal-btn-cetak-klaim" class="btn btn-primary btn-cetak-klaim btn-xs btn-labeled <?= $cetak ?>"><b><i class="fa fa-print"></i></b> <?= Yii::t('fe', 'Cetak Klaim') ?></button>
            <button type="button" id="formfinal-btn-kirim-klaim" class="btn btn-primary btn-xs btn-labeled btn-kirim-klaim <?= $cetak ?>"><b><i class="fa fa-print"></i></b> <?= Yii::t('fe', 'Kirim Klaim Online') ?></button>
        </div>
        <div class="button-proc col-sm-6 text-right">
            <button type="button" id="formfinal-btn-final-klaim" class="btn btn-success btn-final-klaim btn-xs btn-labeled <?= $final ?> pull-left"><b><i class="fa fa-send"></i></b> <?= Yii::t('fe', 'Final Klaim') ?></button>
            <button type="button" id="formfinal-btn-edit-klaim" class="<?= $cetak ?> btn btn-warning btn-edit-klaim btn-xs btn-labeled"><b><i class="fa fa-pencil"></i></b> <?= Yii::t('fe', 'Edit Klaim') ?></button>
        </div>
    </div><br>
</div>
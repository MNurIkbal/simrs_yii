<?php

use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;

?>
<style>
    .width-td {
        width: 30px;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="modal-header bg-inverse">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h5 class="modal-title">Detail Tagihan</h5>
        </div>
        <div class="modal-body">
            <div>
                <table class=" table table-hover " border="0" style="font-size: 15px; border: 0;">
                    <tr>
                        <td style="width: 200px;"><b>Tanggal Transaksi</b></td>
                        <td class="width-td">:</td>
                        <td><span class="nomor_tagihan"><?= $tglPembayaran ?></span></td>
                    </tr>
                    <tr>
                        <td><b>Nama Pasien</b></td>
                        <td class="width-td">:</td>
                        <td><span class="nomor_tagihan"><?= $namaPasien ?></span></td>
                    </tr>
                    <tr>
                        <td><b>No. MR</b></td>
                        <td class="width-td">:</td>
                        <td><span class="nomor_tagihan"><?= $noRekamMedik ?></span></td>
                    </tr>
                    <tr>
                        <td><b>No. Pendaftaran</b></td>
                        <td class="width-td">:</td>
                        <td><span class="nomor_tagihan"><?= $noPendaftaran ?></span></td>
                    </tr>
                </table>
            </div>
            <div style="margin-top: 30px; font-size: 15px; border: 0;">
                <?php if (!empty($subsidiAsuransi)) : ?>
                    <table class="table table-hover">
                        <tr>
                            <td colspan="6"><b>Penjamin : <?= ArrayHelper::getValue($penjaminAsuransi, 'penjamin_nama', $defaultPenjamin) ?></b></td>
                        </tr>
                        <tr>
                            <td style="width: 200px;">Limit Diskon</td>
                            <td class="width-td">:</td>
                            <td><?= $limitMasterAsuransi ?>%</td>
                            <td style="width: 150px;">Nominal Limit</td>
                            <td class="width-td">:</td>
                            <td><?= DocoHelpers::rupiahDisplay($limitDiscAsuransi) ?></td>
                        </tr>
                        <tr>
                            <td style="width: 200px;">Diskon yang diberikan</td>
                            <td class="width-td">:</td>
                            <td><?= $diskonPersenAsuransi ?>%</td>
                            <td style="width: 150px;">Nominal</td>
                            <td class="width-td">:</td>
                            <td><?= DocoHelpers::rupiahDisplay($diskonDiberikanAsuransi) ?></td>
                        </tr>
                    </table>
                <?php endif; ?>
                <br>
                <?php if (!empty($totalDibayar)) : ?>
                    <table class="table table-hover">
                        <tr>
                            <td colspan="6"><b>Penjamin : <?= ArrayHelper::getValue($penjaminUmum, 'penjamin_nama') ?></b></td>
                        </tr>
                        <tr>
                            <td style="width: 200px;">Limit Diskon</td>
                            <td class="width-td">:</td>
                            <td><?= $limitMasterUmum ?>%</td>
                            <td style="width: 150px;">Nominal Limit</td>
                            <td class="width-td">:</td>
                            <td><?= DocoHelpers::rupiahDisplay($limitDiscUmum) ?></td>
                        </tr>
                        <tr>
                            <td style="width: 200px;">Diskon yang diberikan</td>
                            <td class="width-td">:</td>
                            <td><?= $diskonPersenUmum ?>%</td>
                            <td style="width: 150px;">Nominal</td>
                            <td class="width-td">:</td>
                            <td><?= DocoHelpers::rupiahDisplay($diskonDiberikanUmum) ?></td>
                        </tr>
                    </table>
                <?php endif; ?>
            </div>
            <div style="margin-top: 30px; font-size: 15px; border: 0;">
                <?php if (!empty($subsidiAsuransi)) : ?>
                    <table class="table table-hover">
                        <tr>
                            <td colspan="6"><b>Penjamin : <?= ArrayHelper::getValue($penjaminAsuransi, 'penjamin_nama', $defaultPenjamin) ?></b></td>
                        </tr>
                        <tr>
                            <td style="width: 250px;">Total tagihan sebelum diskon</td>
                            <td class="width-td">:</td>
                            <td><?= DocoHelpers::rupiahDisplay($totalTagihanAsuransi) ?></td>
                        </tr>
                        <tr>
                            <td style="width: 250px;">Total tagihan setelah diskon</td>
                            <td class="width-td">:</td>
                            <td><?= DocoHelpers::rupiahDisplay($totalAsuransi) ?></td>
                        </tr>
                    </table>
                <?php endif; ?>
                <br>
                <?php if (!empty($totalDibayar)) : ?>
                    <table class="table table-hover">
                        <tr>
                            <th colspan="6"><b>Penjamin : <?= ArrayHelper::getValue($penjaminUmum, 'penjamin_nama') ?></b></th>
                        </tr>
                        <tr>
                            <td style="width: 250px;">Total tagihan sebelum diskon</td>
                            <td class="width-td">:</td>
                            <td><?= DocoHelpers::rupiahDisplay($totalTagihanUmum) ?></td>
                        </tr>
                        <tr>
                            <td style="width: 250px;">Total tagihan setelah diskon</td>
                            <td class="width-td">:</td>
                            <td><?= DocoHelpers::rupiahDisplay($totalUmum) ?></td>
                        </tr>
                    </table>
                <?php endif; ?>
            </div>

            <div style="margin-top: 20px; font-size: 15px; border: 0;">
                <table class="table table-hover">
                    <th style="width: 200px;">Nama Kasir</th>
                    <th class="width-td">:</th>
                    <th><?= $pegawaiKasir ?></th>
                </table>
            </div>
        </div>
    </div>
</div>
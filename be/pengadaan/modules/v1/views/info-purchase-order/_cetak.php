<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use Doco\components\DocoHelpers;
    use Doco\components\DocoConstants;
use yii\helpers\ArrayHelper;

?>

<style type="text/css">
    .tbl-bordered {
        border-collapse: collapse;
        font-family: Tahoma;
        font-size: 10px;
    }

    .tbl-bordered th {
        border: 1px solid black;
        /*border-bottom: 1px solid black;*/
        padding: 5px;
    }

    .tbl-bordered tr td.border-bottom {
        border-bottom: 1px solid black;
    }

    .body-border td {
        border: 1px solid black;
    }

    .tbl-bordered tr.border-top td {
        border-top: 1px solid black;
    }

    .bold {
        font-weight: bold;
    }

    .header-right {
        padding-right: 15px;
    }

    .heading-bottom {
      background: linear-gradient(to bottom, transparent 3px, black 3px, black 6px, transparent 6px) no-repeat;
      border-bottom: 1px solid black;
      display: inline-block;
      vertical-align: bottom;
      padding-bottom: 10px;
    }

    .table-padding-top {
        padding-top: -50px;
    }
</style>

<div class="modal-body">
    <div class="form-group">
        <div class="col-lg-12">

            <table width="100%" class="tbl-bordered">
                <thead  style="font-size: 12px">
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th>Tanggal PO</th>
                        <th>Tanggal Validasi PO</th>
                        <th>No Transaksi</th>
                        <th>Asal Transaksi</th>
                        <th>Nomor PO</th>
                        <th>Supplier</th>
                        <th>Payment Term</th>
                        <th>Total Harga PO</th>
                        <th>Ruangan</th>
                        <th>Status Penerimaan</th>
                        <th>Pegawai Validasi</th>
                        <th>Tanggal Cetak PO</th>
                        <th>Cito</th>
                        <th>Admin</th>
                        <th>Consigment</th>
                    </tr>
                </thead>

                <tbody class="body-border">
                    <?php
                        $no = 1;
                        $subTotal = 0;
                        foreach ($data as $value) :
                            if($value['is_validasi'] == false){
                                $color = '#ffcc66';
                            }else{
                                $color = '#ffffff';
                            }
                    ?>
                        <tr>
                            <td style="background-color: <?=  $color ?> ;" align="center"><?= $no ?></td>
                            <td style="background-color: <?=  $color ?> ;" align="center"><?= date('d M Y',strtotime($value['tanggal_buat_po'])) ?></td>
                            <td style="background-color: <?=  $color ?> ;" align="center"><?= !empty($value['tanggal_po']) ? date('d M Y',strtotime($value['tanggal_po'])) : "-" ?></td>
                            <td style="background-color: <?=  $color ?> ;" align="center"><?= $value['nomor'] ?></td>
                            <td style="background-color: <?=  $color ?> ;" align="center">
                                <?= isset(DocoConstants::$statusAsal[$value['asal_transaksi']])
                                        ? DocoConstants::$statusAsal[$value['asal_transaksi']] : null ?>
                            </td>
                            <td style="background-color: <?=  $color ?> ;" align="center"><?= $value['no_transaksi'] ?></td>
                            <td style="background-color: <?=  $color ?> ;" align="center"><?= $value['supplier_nama'] ?></td>
                            <td style="background-color: <?=  $color ?> ;" align="center"><?= $value['payment_term'] ?></td>
                            <td style="background-color: <?=  $color ?> ;" align="center"><?= DocoHelpers::formatNumber($value['total_harga_po']) ?></td>
                            <td style="background-color: <?=  $color ?> ;" align="center"><?= $value['ruangan_nama'] ?></td>
                            <td style="background-color: <?=  $color ?> ;" align="center"><?= $value['stat_penerimaan'] ?></td>
                            <td style="background-color: <?=  $color ?> ;" align="center"><?= $value['pegawai_validasi'] ?></td>
                            <td style="background-color: <?=  $color ?> ;" align="center"><?= isset($value['tgl_cetak_po']) ? date('d M Y',strtotime($value['tgl_cetak_po'])) : '' ?></td>
                            <td style="background-color: <?=  $color ?> ;" align="center"><?= ArrayHelper::getValue($value, 'po_cito') ?></td>
                            <td style="background-color: <?=  $color ?> ;" align="center"><?= ArrayHelper::getValue($value, 'po_admin') ?></td>
                            <td style="background-color: <?=  $color ?> ;" align="center"><?= ArrayHelper::getValue($value, 'po_consigment') ?></td>
                        </tr>
                    <?php
                        $no++;
                        $subTotal += $value['total_harga_po'];
                        endforeach;
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7" align="right"><?=\Yii::t("app", "Grand Total : ");?></td>
                        <td class="heading-bottom" colspan="2" align="right"><b><?= DocoHelpers::formatNumber($subTotal, 0) ?></b></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<style type="text/css">
    body { 
        font-family: Courier New,Courier,monospace;
        font-size: 14px;
        letter-spacing: 1px;
    }
    .tbl {
        border-collapse: collapse;
        font-size: 12px;
        letter-spacing: 1px;
    }

    .tbl th {
        border: 1px solid black;
        padding: 5px;
    }

</style>

<table width="100%" cellpadding="1" cellspacing="0" class="tbl">
    <thead>
        <tr>
            <td colspan="4" style="border-top: 1px solid black"></td>
        </tr>
        <tr class="border-top">
            <td style="text-align: center" width="5%">No</td>
            <td width="61%">Nama Obat Alkes</td>
            <td width="17%" style="text-align: center">Qty Pesan</td>
            <!-- <td>Satuan</td> -->
            <td width="17%" style="text-align: center">Qty Kirim</td>
            <!-- <td>Satuan</td> -->
        </tr>
        <tr>
            <td colspan="4" style="border-bottom: 1px solid black"></td>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; ?>
        <?php foreach ($detail as $row): ?>
            <?php
                $qty_konversi = $row['jumlah_pesan'] /  ($row['qty_besar'] > 0 ? $row['qty_besar'] : 1);
                $qty_konversi = $qty_konversi > 0 ? $qty_konversi : 1;
             ?>
            <tr>
                <td style="height:25px;text-align: center"><?= $no ?></td>
                <td style="height:25px;padding-left: 5px"><?= $row['obatalkes_nama'] ?></td>
                <td style="height:25px;text-align: center"><?= $row['qty_besar'] ?> <?= $row['satuan_besar'] ?></td>
                <!-- <td style="height:25px;padding-left: 10px"><?= $row['satuan_besar'] ?></td> -->
                <td style="height:25px;text-align: center"><?= empty($row['jumlah_mutasi']) ? "-" : $row['jumlah_mutasi'] / $qty_konversi ?> <?= empty($row['satuan_kirim']) ? "-" : $row['satuan_besar'] ?></td>
                <!-- <td style="height:25px;padding-left: 10px"><?= empty($row['satuan_kirim']) ? "-" : $row['satuan_besar'] ?></td> -->
            </tr>
            <?php $no++ ?>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" style="border-bottom: 1px solid black"></td>
        </tr>
    </tfoot>

</table>
<style type="text/css">
    .tbl-bordered {
        border-collapse: collapse;
        font-size: 8pt;
        font-family: 'Arial';
    }

    .tbl-bordered th {
        padding: 2px;
    }

    .tbl-bordered td {
        font-family: 'Arial';
        padding: 2px;
    }

    thead td {
        text-align: center;
        font-weight: bold;
    }

    .rak-name {
        font-weight: bold;
        font-size: 10pt;
        font-family: 'Arial';
    }

    tr.border-head th {
        border-top: 1px solid black;
        border-bottom: 1px solid black;
    }

    tr.border-bottom td {
        border-bottom: 1px solid black;
    }
</style>

<table class="tbl-bordered" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr class="border-head">
            <th>No</th>
            <th>Kode Obat</th>
            <th>Nama obat alkes</th>
            <th>UoM</th>
            <th>In</th>
            <th>Out</th>
            <th>Stok Fisik 1</th>
            <th>Stok Fisik 2</th>
            <th>Satuan</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        if (count($detail)) :
            foreach ($detail as $key => $value) : ?>
                <tr class="<?= $no == count($detail) ? "border-bottom" : "" ?>">
                    <td style="text-align:center"><?= $no ?></td>
                    <td><?= $value['obatalkes_kode'] ?></td>
                    <td><?= $value['obatalkes_nama'] ?></td>
                    <td width="15%"><?= $value['uom'] ?></td>
                    <td width="6%" style="text-align: center;">. . . . .</td>
                    <td width="6%" style="text-align: center;">. . . . .</td>
                    <td width="8%" style="text-align: center;">. . . . .</td>
                    <td width="8%" style="text-align: center;">. . . . .</td>
                    <td style="max-width: 8%"><?= $value['satuanunit_nama'] ?></td>
                </tr>
            <?php
                $no++;
            endforeach;
        else :
            ?>
            <tr>
                <td colspan="4" style="text-align: center;">Data kosong</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<br>

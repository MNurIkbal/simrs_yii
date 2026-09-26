<table width="100%" border="1" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th>No.</th>
            <th>Nama Ruangan</th>
            <th>Jan</th>
            <th>Feb</th>
            <th>Mar</th>
            <th>Apr</th>
            <th>May</th>
            <th>Jun</th>
            <th>Jul</th>
            <th>Aug</th>
            <th>Sep</th>
            <th>Oct</th>
            <th>Nov</th>
            <th>Dec</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $number = 1;
        foreach ($data as $key_data => $rekap): ?>
            <tr>
                <td style='text-align: center'><?= $number ?></td>
                <td><?= $rekap["ruangan_nama"] ?></td>
                <?php for ($i=1; $i <= 12; $i++) {
                    $month_num = ($i <= 9) ? (string) "0".$i : $i;
                    $value_rekap = $rekap[$month_num];
                    echo "<td style='text-align: center'>{$value_rekap}</td>";
                } ?>
            </tr>
        <?php
        $number++;
        endforeach ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2">Total :</th>
            <?php foreach ($total as $key_total => $total_tahun): ?>
                <th><?= $total_tahun ?></th>
            <?php endforeach ?>
        </tr>
    </tfoot>
</table>
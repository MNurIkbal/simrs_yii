<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <td>No</td>
            <td>Nama Obat Alkes</td>
            <td>Diagnosa Pasien</td>
        </tr>
    </thead>
    <tbody>
        <?php 
            $no = 1;
            foreach ($detail as $value) :
        ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= isset($value['obatalkes']['obatalkes_nama']) 
                        ? $value['obatalkes']['obatalkes_nama'] : '' ?></td>
                <td><?= isset($value['diagnosa']['diagnosa_nama']) 
                        ? $value['diagnosa']['diagnosa_nama'] : 'xxxxxxxx' ?></td>
            </tr>
        <?php
            $no++;
            endforeach;

        ?>
    </tbody>
</table>

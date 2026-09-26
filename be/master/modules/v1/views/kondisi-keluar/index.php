
<!--
@Author: Sunarko / Master Cara Keluar
@Date:   2018-07-30 09:26:15
@Last Modified by:  
@Last Modified time: 
-->

<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode</th>
            <th>Cara Keluar</th>
            <th>Kondisi Keluar</th>
            <th>Nama Lainnya</th>
            <th>Status</th>
            <th>Catatan</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1;
        foreach ($detail as $id => $value) : ?>
                <tr>
                    <td><?= $no ?></td>
                    <td><?= !empty($value['kondisikeluar_kode']) ? $value['kondisikeluar_kode'] : '' ?></td>
                    <td><?= !empty($value['carakeluar_nama']) ? $value['carakeluar_nama'] : '' ?></td>
                    <td><?= !empty($value['kondisikeluar_nama']) ? $value['kondisikeluar_nama'] : '' ?></td>
                    <td><?= !empty($value['kondisikeluar_namalain']) ? $value['kondisikeluar_namalain'] : '' ?></td>
                    <td><?= !empty($value['is_active']) ? 'Aktif' : 'Tidak Aktif' ?></td>
                    <td><?= !empty($value['catatan']) ? $value['catatan'] : '' ?></td>
                </tr>
            <?php $no++; ?>
        <?php endforeach; ?>
    </tbody>
</table>
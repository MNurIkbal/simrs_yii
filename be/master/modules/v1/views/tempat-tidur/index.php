
<!--
@Author: Sunarko / Master Tempat Tidur
@Date:   2018-07-24 10:00:15
@Last Modified by:  
@Last Modified time: 
-->
<style type="text/css">
    .tbl-bordered {
    border-collapse: collapse;
    }

    .tbl-bordered th {
        border: 1px solid black;
        padding: 5px;
    }
    .tbl-bordered td {
        border: 1px solid black;
        padding: 5px;
    }
</style>
<table class="tbl-bordered" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Ruangan</th>
            <th>Nama Kamar</th>
            <th style="width:30%">No Tempat Tidur</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1;
        foreach ($detail as $id => $value) : ?>
                <tr>
                    <td><?= $no ?></td>
                    <td><?= !empty($value['ruangan_nama']) ? $value['ruangan_nama'] : '' ?></td>
                    <td><?= !empty($value['kamarruangan_nokamar']) ? $value['kamarruangan_nokamar'] : '' ?></td>
                    <td><?= !empty($value['no_tempattidur']) ? $value['no_tempattidur'] : '' ?></td>
                    <!-- <td><?= !empty($value['kettempattidur_nama']) ? $value['kettempattidur_nama'] : '' ?></td> -->
                    <td><?= !empty($value['is_active']) ? 'Aktif' : 'Tidak Aktif' ?></td>
                </tr>
            <?php $no++; ?>
            
        <?php endforeach; ?>
    </tbody>
</table>
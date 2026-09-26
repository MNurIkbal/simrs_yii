
<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Kelompok Tindakan</th>
            <th>Kelompok BPJS</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1;
        foreach ($detail as $id => $value) : 
            $newdata = "";
            $tmp_detail = $value['monitorbpjsdetail'];
            if (is_array($tmp_detail)) {
                $detail = [];
                foreach ($tmp_detail as $value1) {
                    $detail[] = $value1['groupinacbg_nama'];
                }
                $newdata = $value['monitorbpjsdetail']=implode(', ', $detail);
            }
            ?>
                <tr>
                    <td><?= $no ?></td>
                    <td><?= !empty($value['kelompoktindakan_nama']) ? $value['kelompoktindakan_nama'] : '' ?></td>
                    <td><?= $newdata ?></td>
                    <td><?= !empty($value['is_active']) ? 'Aktif' : 'Tidak Aktif' ?></td>
                </tr>
            <?php $no++; ?>
        <?php endforeach; ?>
    </tbody>
</table>
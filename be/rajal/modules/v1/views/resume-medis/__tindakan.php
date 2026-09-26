<style>
    ul.dash {
        list-style: none;
        margin-left: 0;
        padding-left: 1em;
    }

    ul.dash>li:before {
        display: inline-block;
        content: "-";
        width: 1em;
        margin-left: -1em;
    }
</style>
<table class="tbl tbl-bordered" width="100%">
    <tr>
        <th width='8px'>No</th>
        <th>Nama Tindakan / Paket</th>
        <th>Jumlah</th>
    </tr>
    <?php $counter = 1;
    if (count($data['tindakan']) > 0) :
        foreach ($data['tindakan'] as $value) :
            $detailtindakan = !empty($value['daftartindakan_namapaket']) ? explode(',', $value['daftartindakan_namapaket']) : [];
            $detailtindakantxt = '';
            if (isset($detailtindakan[0])) {
                $detailtindakantxt .= "<ul>";
                foreach ($detailtindakan as $val) {
                    $detailtindakantxt .= "<li>" . $val . "</li>";
                }
                $detailtindakantxt .= "</ul>";
            }
    ?>
            <tr>
                <td width='8px'><?= $counter++ ?></td>
                <td><?= $value['tindakan_obat'] . ' ' . $detailtindakantxt ?></td>
                <td class="text-center" width='8px'><?= $value['qty'] ?></td>
            </tr>
    <?php
        endforeach;
    else:
        ?>
        <tr>
            <td class="text-center" colspan="3">Data Tidak Tersedia</td>
        </tr>
    <?php
    endif;
    ?>
</table>
<style>
    table,
    li,
    p {
        font-size: 9px !important;
        margin: 0px;
        line-height: 0px;
    }

    hr {
        margin: 8px 0px !important;
    }

    .text-center {
        text-align: center;
    }

    table {
        border-collapse: collapse;
    }

    .tbl-no-border td,
    .tbl-no-border th {
        border: 0px;
        padding: 5px;
    }

    .tbl-bordered td,
    .tbl-bordered th {
        border: 1px solid black;
        padding: 5px;
    }
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
    if (count($data) > 0) :
        foreach ($data as $value) :
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
                <td><?= $value['tindakan_paket_obat'] . ' ' . $detailtindakantxt ?></td>
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
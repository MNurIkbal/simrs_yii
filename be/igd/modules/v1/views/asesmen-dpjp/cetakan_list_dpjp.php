
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
    ul {
        margin: 0;
    }
    ul.dashed {
        list-style-type: none;
    }
    ul.dashed > li {
        text-indent: -5px;
    }
    ul.dashed > li:before {
        content: "-";
        text-indent: -5px;
    }
    .strike-text{
        text-decoration: line-through;
    }
</style>
<table class="tbl-bordered" style="width:100%;">
    <thead>
        <tr>
            <th class="text-center" width="10%" style="font-size: 12px">Tanggal & Jam</th>
            <th class="text-center" width="20%" style="font-size: 12px">Ruang/ Profesi</th>
            <th class="text-center" style="font-size: 12px">Hasil Asesmen Pasien dan Pemberi Pelayanan</th>
            <th class="text-center" style="font-size: 12px">Instruksi</th>
            <th class="text-center" style="font-size: 12px">Verifikasi Dpjp</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        foreach ($data as $key => $value) : 
            $strike_text = $value['is_deleted'] ? '' : '';
            $tgl_edit = isset($value['created_date']) ? date('d/m/Y / H:i:s', strtotime($value['created_date'])) : '';
            $ket_edit = '<p> Data sudah di ubah oleh <br>'.$value['pegawai_update_nama'].' - <br>'.$tgl_edit.'</p>';
        ?>
        <tr>
            <td style="vertical-align: top; font-size: 12px;"  class="<?= $strike_text?>"><?= nl2br(@$value['tgl_cppt'] ."\r\n". $value['jam_cppt']) ?></td>
            <td style="vertical-align: top; font-size: 12px;" class="<?= $strike_text?>"><?= nl2br(@$value['ruangan'] ."\r\n". $value['profesi']) ?></td>
            <td style="vertical-align: top; font-size: 12px;width: 25%;" class="<?= $strike_text?>"><?= nl2br(@$value['penatalaksanaan']) ?></td>
            <td style="vertical-align: top; font-size: 12px;width: 30%;" class="<?= $strike_text?>"> <?= nl2br(@$value['instruksi_dpjp']) ?></td>
            <td style="vertical-align: center; font-size: 12px; padding-top: 10px"> <?= $value['is_deleted'] ? $ket_edit : nl2br(@$value['verifikasi'])?> </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

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
<table class="tbl-bordered" style="width:100%;">
    <thead>
        <tr>
            <th class="text-center">Tanggal / Jam</th>
            <th class="text-center">Ruang / Profesi</th>
            <th class="text-center">Pengkajian Pasien <br> S.O.A.P</th>
            <th class="text-center">Terapi</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        foreach ($data as $key => $value) : 
            $batas = 0;
            foreach ($value as $k => $v) : 
                $batas++;
                $count = count($value);
        ?>
        <tr>
            <?php if($k == 0) : ?>
            <td style="vertical-align: top; font-size: 13px;" ><?= $v['tgl_cppt']; ?></td>
            <td style="vertical-align: top; font-size: 13px;" ><?= $v['ruangan'] ?></td>
            <td style="vertical-align: top; font-size: 13px;width: 40%;" ><?= $v['penatalaksanaan']; ?></td>
            <?php elseif($batas == $count) : ?>
            <td style="vertical-align: top; font-size: 13px;border-top: 0px"></td>
            <td style="vertical-align: top; font-size: 13px;border-top: 0px"></td>
            <td style="vertical-align: top; font-size: 13px;width: 40%;border-top: 0px" ></td>
            <?php else : ?>
            <td style="vertical-align: top; font-size: 13px;border-bottom: 0px;border-top: 0px"></td>
            <td style="vertical-align: top; font-size: 13px;border-bottom: 0px;border-top: 0px"></td>
            <td style="vertical-align: top; font-size: 13px;width: 40%;border-bottom: 0px;border-top: 0px" ></td>
            <?php endif ?>
            <td style="vertical-align: top; font-size: 13px;"><?= $v['instruksi_dpjp'] ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endforeach; ?>
    </tbody>
</table>
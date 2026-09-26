<style type="text/css">
    .heading{
        font-size: 14px;
        font-weight: bold;
    }

    .riwayat-pasien{
        margin-top: 10px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .riwayat-pasien__anak{
        padding-left: 20px;
    }

    .riwayat-pasien__anak tr td:nth-child(3){
        padding-right: 15px;
    }

    .riwayat-pasien__anak tr td:nth-child(4){
        padding-left: 15px;
    }

    .sub-value{
        padding-left: 64px;
    }
</style>

<!-- Riwayat pasien -->
<table class="riwayat-pasien">
    <tbody>
        <tr>
            <td style="max-width: 270px; width:270px;">Tanggal asesmen medis</td>
            <td>: </td>
            <td><?= isset($asesmen['tgl_asesmenmedis']) ? date('Y-m-d h:i:s', strtotime($asesmen['tgl_asesmenmedis'])) : '' ?></td>
        </tr>
        <tr>
            <td>Sumber informasi</td>
            <td>: </td>
            <td><?= isset($asesmen['sumber_info']) ? ($asesmen['sumber_info'] == 1) ? 'Pasien' : $asesmen['sumber_hubungan'] : '' ?></td>
        </tr>
        <tr>
            <td>Kategori Asesmen Awal Medis</td>
            <td>: </td>
            <td><?= isset($asesmen['kategori_asmed']) && $asesmen['kategori_asmed'] == 2 ? 'Anak' : 'Dewasa' ?></td>
        </tr>
        <tr>
            <td>Keluhan utama</td>
            <td>: </td>
            <td><?= isset($asesmen['keluhan_utama']) ? $asesmen['keluhan_utama'] : '' ?></td>
        </tr>
        <tr>
            <td>Keluhan tambahan</td>
            <td>: </td>
            <td><?= isset($asesmen['keluhan_tambahan']) ? $asesmen['keluhan_tambahan'] : '' ?></td>
        </tr>
        <tr>
            <td>Riwayat penyakit sekarang</td>
            <td>: </td>
            <td><?= isset($asesmen['r_penyakitsekarang']) ? $asesmen['r_penyakitsekarang'] : '' ?></td>
        </tr>
        <tr>
            <td>Lama sakit</td>
            <td>: </td>
            <td><?= isset($asesmen['lama_sakit']) ? $asesmen['lama_sakit'] . ' hari' : '0' . ' hari' ?></td>
        </tr>
        <tr>
            <td>Riwayat penyakit terdahulu</td>
            <td>: </td>
            <td><?=$riwayat_terdahulu?></td>
        </tr>
        <tr>
            <td>Riwayat penyakit keluarga</td>
            <td>: </td>
            <td><?= !empty($r_penyakitkeluarga) ? implode(', ', $r_penyakitkeluarga) : '-' ?></td>
        </tr>
        <tr>
            <td>Riwayat imunisasi</td>
            <td>: </td>
            <td><?= !empty($penyakitImunisasi) ? implode(', ', $penyakitImunisasi) : '-' ?></td>
        </tr>
        <?php if ($asesmen['kategori_asmed'] != 2):?>
            <tr>
                <td>Riwayat pekerjaan sosial, Ekonomi, Kejiwaan dan Kebiasaan</td>
                <td>: </td>
                <td><?= isset($asesmen['r_peskk']) ? $asesmen['r_peskk'] : '' ?></td>
            </tr>
            <tr>
                <td>Status merokok</td>
                <td>: </td>
                <td><?= isset($asesmen['is_merokok']) ? ($asesmen['is_merokok'] == 0) ? 'Tidak Merokok' : 'Merokok' : '' ?></td>
            </tr>
            <tr>
                <td>Jumlah batang rokok</td>
                <td>: </td>
                <td><?= isset($asesmen['jumlah_rokok']) ? $asesmen['jumlah_rokok'] : 0 ?></td>
            </tr>
        <?php endif;?>
        <tr>
            <td>Obat yang sudah diberikan</td>
            <td>: </td>
            <td><?= isset($asesmen['obat_diberikan']) ? $asesmen['obat_diberikan'] : '' ?></td>
        </tr>
        <tr>
            <td>Riwayat makanan</td>
            <td>: </td>
            <td><?= isset($asesmen['r_makanan']) ? $asesmen['r_makanan'] : '' ?></td>
        </tr>
        <?php if ($asesmen['kategori_asmed'] != 2):?>
            <tr>
                <td>Riwayat kelahiran</td>
                <td>: </td>
                <td><?= $penyakitKelahiran ?></td>
            </tr>
        <?php endif;?>
        <tr>
            <td>Riwayat alergi obat</td>
            <td>: </td>
            <td><?= isset($asesmen['r_alergiobat']) ? $asesmen['r_alergiobat'] : '' ?></td>
        </tr>
        <tr>
            <td>Keterangan anamnesa</td>
            <td>: </td>
            <td><?=  isset($asesmen['keterangan']) ? $asesmen['keterangan'] : '' ?></td>
        </tr>
    </tbody>
</table>

<?php if($asesmen['kategori_asmed'] == 2): ?>
<!-- Riwayat Persalinan ibu -->
<div class="heading">Riwayat Persalinan Ibu</div>
<table class="riwayat-pasien riwayat-pasien__anak">
    <tbody>

        <?php if($asesmen['paritas']) : ?>
            <tr>
                <td>Paritas</td>
                <td>: </td>
                <td><?= $asesmen['paritas_lainnya'] ?></td>
            </tr>
        <?php endif; ?>

        <?php if($asesmen['abortus']) : ?>
            <tr>
                <td>Abortus</td>
                <td>: </td>
                <td><?= $asesmen['abortus_lainnya'] ?></td>
            </tr>
        <?php endif; ?>

        <?php if($asesmen['meninggal']) : ?>
            <tr>
                <td>Meninggal</td>
                <td>: </td>
                <td><?= $asesmen['meninggal_lainnya'] ?></td>
            </tr>
        <?php endif; ?>

        <?php if($asesmen['meninggal']) : ?>
            <tr>
                <td>Meninggal</td>
                <td>: </td>
                <td><?= $asesmen['meninggal_lainnya'] ?></td>
            </tr>
        <?php endif; ?>

        <?php if($asesmen['meninggal']) : ?>
            <tr>
                <td>Meninggal</td>
                <td>: </td>
                <td><?= $asesmen['meninggal_lainnya'] ?></td>
            </tr>
        <?php endif; ?>

        <tr>
            <td>Riwayat partus ditolong oleh</td>
            <td>:</td>
            <td></td>
        </tr>
        <?php if($asesmen['partus_dokter']) : ?>
            <tr>
                <td class="sub-value" colspan="3">- Dokter</td>
            </tr>
        <?php endif; ?>
        <?php if($asesmen['partus_bidan']) : ?>
            <tr>
                <td class="sub-value" colspan="3">- Bidan</td>
            </tr>
        <?php endif; ?></td>
        </tr>
        <?php if($asesmen['partus']) : ?>
            <tr>
                <td class="sub-value" colspan="3">- Lainnya, <?= $asesmen['partus_lainnya'] ?></td>
            </tr>
        <?php endif; ?>

        <tr>
            <td>Lama kehamilan</td>
            <td>:</td>
            <td><?= isset($asesmen['lama_kehamilan'])?  $asesmen['lama_kehamilan'].' Minggu' : ' - ';?></td>
        </tr>

        <tr>
            <td>Komplikasi</td>
            <td>:</td>
            <td><?= isset($asesmen['komplikasi']) && $asesmen['komplikasi']  == 0 ?  'Ya, '. $asesmen['komplikasi_lainnya'] : 'Tidak';?></td>
        </tr>

        <tr>
            <td>Masalah neotanus</td>
            <td>:</td>
            <td><?= isset($asesmen['neotanus']) && $asesmen['neotanus']  == 0 ? 'Ya, '. $asesmen['neotanus_lainnya'] : 'Tidak' ?></td>
        </tr>

        <tr>
            <td>Masalah maternal</td>
            <td>:</td>
            <td><?= isset($asesmen['maternal']) && $asesmen['maternal']  == 0 ? 'Ya, '. $asesmen['maternal_lainnya'] : 'Tidak' ?></td>
        </tr>
    </tbody>
</table>

<!-- Riwayat Persalinan ibu -->
<div class="heading">Riwayat tumbuh kembang</div>
<table class="riwayat-pasien riwayat-pasien__anak">
    <tbody>
        <tr>
            <td>BB anak saat lahir</td>
            <td>:</td>
            <td><?= !empty($r_tumbuh_kembang['berat_badan_anak'])?  $r_tumbuh_kembang['berat_badan_anak'].' Gram' : ' - ';?></td>
            <td>Makanan tambahan dimulai dari usia</td>
            <td>:</td>
            <td><?= !empty($r_tumbuh_kembang['makanan_tambahan'])?  $r_tumbuh_kembang['makanan_tambahan'].' '.$r_tumbuh_kembang['makanan_tambahan_addon'] : ' - ';?></td>
        </tr>

        <tr>
            <td>TB anak saat lahir</td>
            <td>:</td>
            <td><?= !empty($r_tumbuh_kembang['tinggi_badan_anak'])?  $r_tumbuh_kembang['tinggi_badan_anak'].' Cm' : ' - ';?></td>
            <td>Tengkurap</td>
            <td>:</td>
            <td><?= !empty($r_tumbuh_kembang['tengkurap'])?  $r_tumbuh_kembang['tengkurap'].' '.$r_tumbuh_kembang['tengkurap_addon'] : ' - ';?></td>
        </tr>

        <tr>
            <td>Kelainan bawaan</td>
            <td>:</td>
            <td><?= !empty($r_tumbuh_kembang['kelainan_anak'])?  $r_tumbuh_kembang['kelainan_anak'] : ' - ';?></td>
            <td>Duduk</td>
            <td>:</td>
            <td><?= !empty($r_tumbuh_kembang['duduk'])?  $r_tumbuh_kembang['duduk'].' '.$r_tumbuh_kembang['duduk_addon'] : ' - ';?></td>
        </tr>

        <tr>
            <td>Asi sampai</td>
            <td>:</td>
            <td><?= !empty($r_tumbuh_kembang['asi'])?  $r_tumbuh_kembang['asi'].' '.$r_tumbuh_kembang['asi_addon'] : ' - ';?></td>
            <td>Merangkak</td>
            <td>:</td>
            <td><?= !empty($r_tumbuh_kembang['merangkak'])?  $r_tumbuh_kembang['merangkak'].' '.$r_tumbuh_kembang['merangkak_addon'] : ' - ';?></td>
        </tr>

        <tr>
            <td>Susu formula dimulai dari usia</td>
            <td>:</td>
            <td><?= !empty($r_tumbuh_kembang['susu_formula'])?  $r_tumbuh_kembang['susu_formula'].' '.$r_tumbuh_kembang['susu_formula_addon'] : ' - ';?></td>
            <td>Berdiri</td>
            <td>:</td>
            <td><?= !empty($r_tumbuh_kembang['berdiri'])?  $r_tumbuh_kembang['berdiri'].' '.$r_tumbuh_kembang['berdiri_addon'] : ' - ';?></td>
        </tr>

        <tr>
            <td>Makanan padat dimulai dari usia</td>
            <td>:</td>
            <td><?= !empty($r_tumbuh_kembang['makanan_padat'])?  $r_tumbuh_kembang['makanan_padat'].' '.$r_tumbuh_kembang['makanan_padat_addon'] : ' - ';?></td>
            <td>Berjalan</td>
            <td>:</td>
            <td><?= !empty($r_tumbuh_kembang['berjalan'])?  $r_tumbuh_kembang['berjalan'].' '.$r_tumbuh_kembang['berjalan_addon'] : ' - ';?></td>
        </tr>

    </tbody>
</table>

<?php endif; ?>

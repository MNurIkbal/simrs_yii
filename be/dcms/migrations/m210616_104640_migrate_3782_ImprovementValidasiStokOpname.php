<?php

use yii\db\Migration;

/**
 * Class m210616_104640_migrate_3782_ImprovementValidasiStokOpname
 */
class m210616_104640_migrate_3782_ImprovementValidasiStokOpname extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."stokopname_t" 
  ADD COLUMN if not exists "total_weighted_avg_fisik" float8,
  ADD COLUMN if not exists "total_weighted_avg_sistem" float8;');

        $this->execute('DROP VIEW if exists "public"."infostokopnamedetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infostokopnamedetail_v\" AS  SELECT instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    formulirstokopname_t.formulirstokopname_id,
    formulirstokopname_t.tglformulir,
    formulirstokopname_t.noformulir,
    stokopname_t.stokopname_id,
    stokopname_t.tglstokopname,
    stokopname_t.nostokopname,
    stokopnamedetail_t.obatalkes_id,
    obatalkes_m.obatalkes_namalain,
    stokopnamedetail_t.tglkadaluarsa,
    COALESCE(stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) AS volume_fisik,
    stokopnamedetail_t.volume_sistem,
    COALESCE(stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) * stokopnamedetail_t.harganetto AS harga_netto_fisik,
    stokopnamedetail_t.volume_sistem * stokopnamedetail_t.harganetto AS harga_netto_sistem,
    COALESCE(stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) - stokopnamedetail_t.volume_sistem AS selisih_jumlah,
    stokopnamedetail_t.jmlselisihstok * stokopnamedetail_t.harganetto AS selisih_harganetto,
    petugas1.pegawai_id AS petugas1_id,
    petugas1.nomorindukpegawai AS petugas1_nip,
    petugas1.noidentitas AS petugas1_noidentitas,
    petugas1.gelardepan AS petugas1_gelardepan,
    petugas1.nama_pegawai AS petugas1_nama,
    gelarbelakangpetugas1.gelarbelakang_nama AS petugas1_gelarbelakang,
    petugas2.pegawai_id AS petugas2_id,
    petugas2.nomorindukpegawai AS petugas2_nip,
    petugas2.noidentitas AS petugas2_noidentitas,
    petugas2.gelardepan AS petugas2_gelardepan,
    petugas2.nama_pegawai AS petugas2_nama,
    gelarbelakangpetugas2.gelarbelakang_nama AS petugas2_gelarbelakang,
    pegawaimengetahui.pegawai_id AS pegawaimengetahui_id,
    pegawaimengetahui.nomorindukpegawai AS pegawaimengetahui_nip,
    pegawaimengetahui.noidentitas AS pegawaimengetahui_noidentitas,
    pegawaimengetahui.gelardepan AS pegawaimengetahui_gelardepan,
    pegawaimengetahui.nama_pegawai AS pegawaimengetahui_nama,
    gelarbelakangpegawaimengetahui.gelarbelakang_nama AS pegawaimengetahui_gelarbelakang,
    obatalkes_m.obatalkes_nama,
    fgetnamalookup(stokopnamedetail_t.kondisibarang::integer) AS kondisibarang_nama,
    periodestokobat_m.tglperiodestok_awal,
    periodestokobat_m.tglperiodestok_akhir,
    rakobat_m.rakobat_nama,
    stok.qty_sisa AS stok_sistem,
    COALESCE(stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) - stok.qty_sisa AS stok_selisih,
    stokopnamedetail_t.stokopnamedetail_id,
    stokopnamedetail_t.harganetto,
    stokopnamedetail_t.stokobatalkes_id,
    COALESCE(stokopnamedetail_t.weighted_avg, logasetobat.weighted_avg::double precision, nulllogasetobat.harga_weighted_avg) AS weighted_avg,
    COALESCE(stokopnamedetail_t.weighted_avg, logasetobat.weighted_avg::double precision, nulllogasetobat.harga_weighted_avg) * (COALESCE(stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) - stokopnamedetail_t.volume_sistem) AS selisih_weighted_avg,
        CASE
            WHEN rakobat_m.parentrakobat_id IS NOT NULL THEN rakobat_m.parentrakobat_id::bigint
            ELSE rakobat_m.rakobat_id
        END AS rakobat_id,
    rakobat_m.rakobat_id AS laciobat_id
   FROM stokopnamedetail_t
     JOIN stokopname_t ON stokopnamedetail_t.stokopname_id = stokopname_t.stokopname_id
     LEFT JOIN formulirstokopname_t ON stokopname_t.formulirstokopname_id = formulirstokopname_t.formulirstokopname_id
     JOIN ruangan_m ON stokopname_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN obatalkes_m ON stokopnamedetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN pegawai_m petugas1 ON stokopname_t.petugas1_id = petugas1.pegawai_id
     LEFT JOIN pegawai_m petugas2 ON stokopname_t.mengetahui_id = petugas2.pegawai_id
     LEFT JOIN gelarbelakang_m gelarbelakangpetugas1 ON petugas1.gelarbelakang::integer = gelarbelakangpetugas1.gelarbelakang_id
     LEFT JOIN gelarbelakang_m gelarbelakangpetugas2 ON petugas2.gelarbelakang::integer = gelarbelakangpetugas2.gelarbelakang_id
     LEFT JOIN pegawai_m pegawaimengetahui ON stokopname_t.mengetahui_id = pegawaimengetahui.pegawai_id
     LEFT JOIN gelarbelakang_m gelarbelakangpegawaimengetahui ON pegawaimengetahui.gelarbelakang::integer = gelarbelakangpegawaimengetahui.gelarbelakang_id
     JOIN formstokopname_r ON stokopnamedetail_t.stokopnamedetail_id = formstokopname_r.stokopnamedetail_id
     LEFT JOIN periodestokobat_m ON formstokopname_r.periodestok_id = periodestokobat_m.periodestokobat_id
     LEFT JOIN konfigrak_m ON stokopnamedetail_t.obatalkes_id = konfigrak_m.obatalkes_id AND stokopname_t.ruangan_id = konfigrak_m.ruangan_id
     LEFT JOIN rakobat_m ON konfigrak_m.rakobat_id = rakobat_m.rakobat_id
     LEFT JOIN ( SELECT stokobatalkes_r.ruangan_id,
            stokobatalkes_r.obatalkes_id,
            stokobatalkes_r.qty_sisa
           FROM stokobatalkes_r) stok ON stokopnamedetail_t.obatalkes_id = stok.obatalkes_id AND stokopname_t.ruangan_id = stok.ruangan_id
     LEFT JOIN ( SELECT logasetobat_r.logasetobat_id,
            logasetobat_r.obatalkes_id,
            COALESCE(logasetobat_r.weighted_avg, 0::numeric) AS weighted_avg
           FROM logasetobat_r
             JOIN ( SELECT max(pk.logasetobat_id) AS logasetobat_id,
                    pk.obatalkes_id
                   FROM logasetobat_r pk
                  GROUP BY pk.obatalkes_id) max_pk ON logasetobat_r.logasetobat_id = max_pk.logasetobat_id AND logasetobat_r.obatalkes_id = max_pk.obatalkes_id) logasetobat ON stokopnamedetail_t.obatalkes_id = logasetobat.obatalkes_id
     LEFT JOIN ( SELECT stokobatalkes_t.obatalkes_id,
            sum(b.harga_asset / b.qty_asset) AS harga_weighted_avg
           FROM stokobatalkes_t
             LEFT JOIN ( SELECT t.harga * s.qty_asset AS harga_asset,
                    t.obatalkes_id,
                    s.qty_asset
                   FROM ( SELECT penerimaanobatdetail_t.obatalkes_id,
                            penerimaanobatdetail_t.harga /
                                CASE
                                    WHEN penerimaanobatdetail_t.qty_diterima = 0 THEN 1
                                    ELSE NULL::integer
                                END::double precision AS harga
                           FROM penerimaanobatdetail_t
                        UNION ALL
                         SELECT penerimaansuppdetail_t.obatalkes_id,
                            penerimaansuppdetail_t.harga_netto /
                                CASE
                                    WHEN penerimaansuppdetail_t.qty_kecil = 0 THEN 1
                                    ELSE NULL::integer
                                END::double precision AS harga
                           FROM penerimaansuppdetail_t) t
                     LEFT JOIN ( SELECT sum(stokobatalkes_t_1.qtystok_in - stokobatalkes_t_1.qtystok_out) AS qty_asset,
                            stokobatalkes_t_1.obatalkes_id
                           FROM stokobatalkes_t stokobatalkes_t_1
                          GROUP BY stokobatalkes_t_1.obatalkes_id) s ON t.obatalkes_id = s.obatalkes_id) b ON stokobatalkes_t.obatalkes_id = b.obatalkes_id
          GROUP BY stokobatalkes_t.obatalkes_id) nulllogasetobat ON stokopnamedetail_t.obatalkes_id = nulllogasetobat.obatalkes_id
  WHERE stokopname_t.is_active = true AND stokopname_t.is_deleted = false;");

        $this->execute('DROP VIEW if exists "public"."infostokopname_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infostokopname_v\" AS  SELECT instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    formulirstokopname_t.formulirstokopname_id,
    formulirstokopname_t.tglformulir,
    formulirstokopname_t.noformulir,
    stokopname_t.stokopname_id,
    stokopname_t.tglstokopname,
    stokopname_t.nostokopname,
    stokopname_t.is_stokawal AS isstokawal,
    stokopname_t.jenisstokopname,
    stokopname_t.keterangan_opname,
    stokopname_t.totalharga_fisik,
    stokopname_t.totalharga_sistem,
    petugas1.pegawai_id AS petugas1_id,
    petugas1.nomorindukpegawai AS petugas1_nip,
    petugas1.noidentitas AS petugas1_noidentitas,
    petugas1.gelardepan AS petugas1_gelardepan,
    petugas1.nama_pegawai AS petugas1_nama,
    gelarbelakangpetugas1.gelarbelakang_nama AS petugas1_gelarbelakang,
    petugas2.pegawai_id AS petugas2_id,
    petugas2.nomorindukpegawai AS petugas2_nip,
    petugas2.noidentitas AS petugas2_noidentitas,
    petugas2.gelardepan AS petugas2_gelardepan,
    petugas2.nama_pegawai AS petugas2_nama,
    gelarbelakangpetugas2.gelarbelakang_nama AS petugas2_gelarbelakang,
    pegawaimengetahui.pegawai_id AS pegawaimengetahui_id,
    pegawaimengetahui.nomorindukpegawai AS pegawaimengetahui_nip,
    pegawaimengetahui.noidentitas AS pegawaimengetahui_noidentitas,
    pegawaimengetahui.gelardepan AS pegawaimengetahui_gelardepan,
    pegawaimengetahui.nama_pegawai AS pegawaimengetahui_nama,
    gelarbelakangpegawaimengetahui.gelarbelakang_nama AS pegawaimengetahui_gelarbelakang,
    stokopname_t.is_verifikasi,
    stokopname_t.tglverifikasi,
    pegawaiverifikasi.pegawai_id AS pegawaiverifikasi_id,
    pegawaiverifikasi.nomorindukpegawai AS pegawaiverifikasi_nip,
    pegawaiverifikasi.noidentitas AS pegawaiverifikasi_noidentitas,
    pegawaiverifikasi.nama_pegawai AS pegawaiverifikasi_nama,
    stokopname_t.total_weighted_avg_fisik,
    stokopname_t.total_weighted_avg_sistem,
    stokopname_t.total_weighted_avg_fisik - stokopname_t.total_weighted_avg_sistem AS selisih_weighted_avg
   FROM stokopname_t
     LEFT JOIN formulirstokopname_t ON stokopname_t.formulirstokopname_id = formulirstokopname_t.formulirstokopname_id
     JOIN ruangan_m ON stokopname_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN pegawai_m petugas1 ON stokopname_t.petugas1_id = petugas1.pegawai_id
     LEFT JOIN pegawai_m petugas2 ON stokopname_t.mengetahui_id = petugas2.pegawai_id
     LEFT JOIN gelarbelakang_m gelarbelakangpetugas1 ON petugas1.gelarbelakang::integer = gelarbelakangpetugas1.gelarbelakang_id
     LEFT JOIN gelarbelakang_m gelarbelakangpetugas2 ON petugas2.gelarbelakang::integer = gelarbelakangpetugas2.gelarbelakang_id
     LEFT JOIN pegawai_m pegawaimengetahui ON stokopname_t.mengetahui_id = pegawaimengetahui.pegawai_id
     LEFT JOIN gelarbelakang_m gelarbelakangpegawaimengetahui ON pegawaimengetahui.gelarbelakang::integer = gelarbelakangpegawaimengetahui.gelarbelakang_id
     LEFT JOIN pegawai_m pegawaiverifikasi ON stokopname_t.pegawaiverifikasi_id = pegawaiverifikasi.pegawai_id
  WHERE stokopname_t.is_active = true AND stokopname_t.is_deleted = false;");


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210616_104640_migrate_3782_ImprovementValidasiStokOpname cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210616_104640_migrate_3782_ImprovementValidasiStokOpname cannot be reverted.\n";

        return false;
    }
    */
}

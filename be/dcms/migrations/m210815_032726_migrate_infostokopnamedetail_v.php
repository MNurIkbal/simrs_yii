<?php

use yii\db\Migration;

/**
 * Class m210815_032726_migrate_infostokopnamedetail_v
 */
class m210815_032726_migrate_infostokopnamedetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if  exists "public"."infostokopnamedetail_v";');
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
    petugas1.gelarbelakang_nama AS petugas1_gelarbelakang,
    petugas2.pegawai_id AS petugas2_id,
    petugas2.nomorindukpegawai AS petugas2_nip,
    petugas2.noidentitas AS petugas2_noidentitas,
    petugas2.gelardepan AS petugas2_gelardepan,
    petugas2.nama_pegawai AS petugas2_nama,
    petugas2.gelarbelakang_nama AS petugas2_gelarbelakang,
    pegawaimengetahui.pegawai_id AS pegawaimengetahui_id,
    pegawaimengetahui.nomorindukpegawai AS pegawaimengetahui_nip,
    pegawaimengetahui.noidentitas AS pegawaimengetahui_noidentitas,
    pegawaimengetahui.gelardepan AS pegawaimengetahui_gelardepan,
    pegawaimengetahui.nama_pegawai AS pegawaimengetahui_nama,
    pegawaimengetahui.gelarbelakang_nama AS pegawaimengetahui_gelarbelakang,
    obatalkes_m.obatalkes_nama,
    fgetnamalookup(stokopnamedetail_t.kondisibarang::integer) AS kondisibarang_nama,
    NULL::text AS tglperiodestok_awal,
    NULL::text AS tglperiodestok_akhir,
        CASE
            WHEN stokopname_t.is_verifikasi = true THEN stokopnamedetail_t.stok_akhir
            ELSE stok.qty_sisa
        END AS stok_sistem,
        CASE
            WHEN stokopname_t.is_verifikasi = true THEN stokopnamedetail_t.selisih_akhir
            ELSE COALESCE(stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) - stok.qty_sisa
        END AS stok_selisih,
    stokopnamedetail_t.stokopnamedetail_id,
    stokopnamedetail_t.harganetto,
    stokopnamedetail_t.stokobatalkes_id,
    COALESCE(stokopnamedetail_t.weighted_avg, obatalkes_m.harganetto, logasetobat.weighted_avg::double precision, nulllogasetobat.harga_weighted_avg) AS weighted_avg,
    COALESCE(stokopnamedetail_t.weighted_avg, obatalkes_m.harganetto, logasetobat.weighted_avg::double precision, nulllogasetobat.harga_weighted_avg) * (COALESCE(stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) - stokopnamedetail_t.volume_sistem) AS selisih_weighted_avg,
        CASE
            WHEN rakobat_m.parentrakobat_id IS NOT NULL THEN rakobat_m.parentrakobat_id::bigint
            ELSE rakobat_m.rakobat_id
        END AS rakobat_id,
    rakobat_m.rak,
    rakobat_m.rakobat_nama,
    rakobat_m.rakobat_id AS laciobat_id,
    rakobat_m.rakobat_nama AS laci
   FROM stokopnamedetail_t
     JOIN ( SELECT a.stokopname_id,
            a.formulirstokopname_id,
            a.petugas1_id,
            a.mengetahui_id,
            a.ruangan_id,
            a.tglstokopname,
            a.nostokopname,
            a.is_verifikasi
           FROM stokopname_t a
          WHERE a.is_active = true AND a.is_deleted = false) stokopname_t ON stokopnamedetail_t.stokopname_id = stokopname_t.stokopname_id
     LEFT JOIN ( SELECT b.formulirstokopname_id,
            b.tglformulir,
            b.noformulir
           FROM formulirstokopname_t b) formulirstokopname_t ON stokopname_t.formulirstokopname_id = formulirstokopname_t.formulirstokopname_id
     JOIN ( SELECT c.ruangan_id,
            c.instalasi_id,
            c.ruangan_nama
           FROM ruangan_m c) ruangan_m ON stokopname_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT d.instalasi_id,
            d.instalasi_nama
           FROM instalasi_m d) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ( SELECT e.obatalkes_id,
            e.obatalkes_nama,
            e.obatalkes_namalain,
            e.harganetto
           FROM obatalkes_m e) obatalkes_m ON stokopnamedetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT d.pegawai_id,
            d.nama_pegawai,
            d.gelarbelakang,
            d.nomorindukpegawai,
            d.noidentitas,
            d.gelardepan,
            gelar_belakang.gelarbelakang_nama
           FROM pegawai_m d
             LEFT JOIN ( SELECT gelarbelakang_m.gelarbelakang_id,
                    gelarbelakang_m.gelarbelakang_nama
                   FROM gelarbelakang_m) gelar_belakang ON d.gelarbelakang::integer = gelar_belakang.gelarbelakang_id) petugas1 ON stokopname_t.petugas1_id = petugas1.pegawai_id
     LEFT JOIN ( SELECT e.pegawai_id,
            e.nama_pegawai,
            e.gelarbelakang,
            e.nomorindukpegawai,
            e.noidentitas,
            e.gelardepan,
            gelar_belakang.gelarbelakang_nama
           FROM pegawai_m e
             LEFT JOIN ( SELECT gelarbelakang_m.gelarbelakang_id,
                    gelarbelakang_m.gelarbelakang_nama
                   FROM gelarbelakang_m) gelar_belakang ON e.gelarbelakang::integer = gelar_belakang.gelarbelakang_id) petugas2 ON stokopname_t.mengetahui_id = petugas2.pegawai_id
     LEFT JOIN ( SELECT f.pegawai_id,
            f.nama_pegawai,
            f.gelarbelakang,
            f.nomorindukpegawai,
            f.noidentitas,
            f.gelardepan,
            gelar_belakang.gelarbelakang_nama
           FROM pegawai_m f
             LEFT JOIN ( SELECT gelarbelakang_m.gelarbelakang_id,
                    gelarbelakang_m.gelarbelakang_nama
                   FROM gelarbelakang_m) gelar_belakang ON f.gelarbelakang::integer = gelar_belakang.gelarbelakang_id) pegawaimengetahui ON stokopname_t.mengetahui_id = pegawaimengetahui.pegawai_id
     LEFT JOIN ( SELECT g.rakobat_id,
            g.obatalkes_id,
            g.ruangan_id
           FROM konfigrak_m g) konfigrak_m ON stokopnamedetail_t.obatalkes_id = konfigrak_m.obatalkes_id AND stokopname_t.ruangan_id = konfigrak_m.ruangan_id
     LEFT JOIN ( SELECT h.rakobat_id,
            h.rakobat_nama,
            h.parentrakobat_id,
            rak.rakobat_nama AS rak
           FROM rakobat_m h
             LEFT JOIN ( SELECT h1.rakobat_id,
                    h1.rakobat_nama
                   FROM rakobat_m h1) rak ON h.parentrakobat_id = rak.rakobat_id) rakobat_m ON konfigrak_m.rakobat_id = rakobat_m.rakobat_id
     LEFT JOIN ( SELECT i.ruangan_id,
            i.obatalkes_id,
            i.qty_sisa
           FROM stokobatalkes_r i) stok ON stokopnamedetail_t.obatalkes_id = stok.obatalkes_id AND stokopname_t.ruangan_id = stok.ruangan_id
     LEFT JOIN ( SELECT DISTINCT ON (j.obatalkes_id) j.logasetobat_id,
            j.obatalkes_id,
            COALESCE(j.weighted_avg, 0::numeric) AS weighted_avg
           FROM logasetobat_r j) logasetobat ON stokopnamedetail_t.obatalkes_id = logasetobat.obatalkes_id
     LEFT JOIN ( SELECT stokobatalkes_t.obatalkes_id,
            sum(stok_1.harga_asset / stok_1.qty_asset) AS harga_weighted_avg
           FROM stokobatalkes_t
             LEFT JOIN ( SELECT penerimaan_obat.harga * stok_obat.qty_asset AS harga_asset,
                    penerimaan_obat.obatalkes_id,
                    stok_obat.qty_asset
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
                           FROM penerimaansuppdetail_t) penerimaan_obat
                     LEFT JOIN ( SELECT sum(st_ob.qtystok_in - st_ob.qtystok_out) AS qty_asset,
                            st_ob.obatalkes_id
                           FROM stokobatalkes_t st_ob
                          GROUP BY st_ob.obatalkes_id) stok_obat ON penerimaan_obat.obatalkes_id = stok_obat.obatalkes_id) stok_1 ON stokobatalkes_t.obatalkes_id = stok_1.obatalkes_id
          GROUP BY stokobatalkes_t.obatalkes_id) nulllogasetobat ON stokopnamedetail_t.obatalkes_id = nulllogasetobat.obatalkes_id;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210815_032726_migrate_infostokopnamedetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210815_032726_migrate_infostokopnamedetail_v cannot be reverted.\n";

        return false;
    }
    */
}

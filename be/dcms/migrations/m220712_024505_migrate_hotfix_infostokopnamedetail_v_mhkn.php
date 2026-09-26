<?php

use yii\db\Migration;

/**
 * Class m220712_024505_migrate_hotfix_infostokopnamedetail_v_mhkn
 */
class m220712_024505_migrate_hotfix_infostokopnamedetail_v_mhkn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infostokopnamedetail_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infostokopnamedetail_v" AS SELECT a.instalasi_id,
    a.instalasi_nama,
    a.ruangan_id,
    a.ruangan_nama,
    a.formulirstokopname_id,
    a.tglformulir,
    a.noformulir,
    a.stokopname_id,
    a.tglstokopname,
    a.nostokopname,
    a.obatalkes_id,
    a.obatalkes_namalain,
    a.tglkadaluarsa,
    a.volume_fisik,
    a.volume_sistem,
    a.harga_netto_fisik,
    a.harga_netto_sistem,
    a.selisih_jumlah,
    a.selisih_harganetto,
    a.petugas1_id,
    a.petugas1_nip,
    a.petugas1_noidentitas,
    a.petugas1_gelardepan,
    a.petugas1_nama,
    a.petugas1_gelarbelakang,
    a.petugas2_id,
    a.petugas2_nip,
    a.petugas2_noidentitas,
    a.petugas2_gelardepan,
    a.petugas2_nama,
    a.petugas2_gelarbelakang,
    a.pegawaimengetahui_id,
    a.pegawaimengetahui_nip,
    a.pegawaimengetahui_noidentitas,
    a.pegawaimengetahui_gelardepan,
    a.pegawaimengetahui_nama,
    a.pegawaimengetahui_gelarbelakang,
    a.obatalkes_nama,
    a.kondisibarang_nama,
    a.tglperiodestok_awal,
    a.tglperiodestok_akhir,
    a.stok_sistem,
    a.stok_selisih,
    a.stokopnamedetail_id,
    a.harganetto,
    a.stokobatalkes_id,
    a.weighted_avg,
    a.stok_selisih * a.weighted_avg AS selisih_weighted_avg,
    a.rakobat_id,
    a.rak,
    a.rakobat_nama,
    a.laciobat_id,
    a.laci,
    a.jenisobatalkes_nama,
    a.base_price,
    a.selisih_base_price
   FROM ( SELECT instalasi_m.instalasi_id,
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
            look_kondisibarang.lookup_name AS kondisibarang_nama,
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
            COALESCE(stokopnamedetail_t.weighted_avg, logasetobat.weighted_avg::double precision, obatalkes_m.harganetto, nulllogasetobat.harga_weighted_avg) AS weighted_avg,
            COALESCE(stokopnamedetail_t.weighted_avg, logasetobat.weighted_avg::double precision, obatalkes_m.harganetto, nulllogasetobat.harga_weighted_avg) * (COALESCE(stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) - stokopnamedetail_t.volume_sistem) AS selisih_weighted_avg,
            COALESCE(laci.rak_id::bigint, rak.rakobat_id) AS rakobat_id,
            COALESCE(laci.rak, rak.rakobat_nama) AS rak,
            COALESCE(laci.rak, rak.rakobat_nama) AS rakobat_nama,
            COALESCE(laci.rakobat_id, rak.rakobat_id) AS laciobat_id,
            COALESCE(laci.rakobat_nama, rak.rakobat_nama) AS laci,
            jenisobatalkes_m.jenisobatalkes_nama,
            COALESCE(obatalkes_m.harganetto, 0::double precision) AS base_price,
            COALESCE(obatalkes_m.harganetto, 0::double precision) * (COALESCE(stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) - stokopnamedetail_t.volume_sistem) AS selisih_base_price
           FROM stokopnamedetail_t
             JOIN ( SELECT a_1.stokopname_id,
                    a_1.formulirstokopname_id,
                    a_1.petugas1_id,
                    a_1.mengetahui_id,
                    a_1.ruangan_id,
                    a_1.tglstokopname,
                    a_1.nostokopname,
                    a_1.is_verifikasi
                   FROM stokopname_t a_1
                  WHERE a_1.is_active = true AND a_1.is_deleted = false) stokopname_t ON stokopnamedetail_t.stokopname_id = stokopname_t.stokopname_id
             LEFT JOIN ( SELECT a_1.formulirstokopname_id,
                    a_1.tglformulir,
                    a_1.noformulir
                   FROM formulirstokopname_t a_1) formulirstokopname_t ON stokopname_t.formulirstokopname_id = formulirstokopname_t.formulirstokopname_id
             JOIN ( SELECT a_1.ruangan_id,
                    a_1.instalasi_id,
                    a_1.ruangan_nama
                   FROM ruangan_m a_1) ruangan_m ON stokopname_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a_1.instalasi_id,
                    a_1.instalasi_nama
                   FROM instalasi_m a_1) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN ( SELECT a_1.obatalkes_id,
                    a_1.obatalkes_nama,
                    a_1.obatalkes_namalain,
                    a_1.harganetto,
                    a_1.jenisobatalkes_id
                   FROM obatalkes_m a_1) obatalkes_m ON stokopnamedetail_t.obatalkes_id = obatalkes_m.obatalkes_id
             LEFT JOIN ( SELECT a_1.jenisobatalkes_id,
                    a_1.jenisobatalkes_nama
                   FROM jenisobatalkes_m a_1) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
             LEFT JOIN ( SELECT a_1.pegawai_id,
                    a_1.nama_pegawai,
                    a_1.gelarbelakang,
                    a_1.nomorindukpegawai,
                    a_1.noidentitas,
                    a_1.gelardepan,
                    gelar_belakang.gelarbelakang_nama
                   FROM pegawai_m a_1
                     LEFT JOIN ( SELECT gelarbelakang_m.gelarbelakang_id,
                            gelarbelakang_m.gelarbelakang_nama
                           FROM gelarbelakang_m) gelar_belakang ON a_1.gelarbelakang::integer = gelar_belakang.gelarbelakang_id) petugas1 ON stokopname_t.petugas1_id = petugas1.pegawai_id
             LEFT JOIN ( SELECT a_1.pegawai_id,
                    a_1.nama_pegawai,
                    a_1.gelarbelakang,
                    a_1.nomorindukpegawai,
                    a_1.noidentitas,
                    a_1.gelardepan,
                    gelar_belakang.gelarbelakang_nama
                   FROM pegawai_m a_1
                     LEFT JOIN ( SELECT gelarbelakang_m.gelarbelakang_id,
                            gelarbelakang_m.gelarbelakang_nama
                           FROM gelarbelakang_m) gelar_belakang ON a_1.gelarbelakang::integer = gelar_belakang.gelarbelakang_id) petugas2 ON stokopname_t.mengetahui_id = petugas2.pegawai_id
             LEFT JOIN ( SELECT a_1.pegawai_id,
                    a_1.nama_pegawai,
                    a_1.gelarbelakang,
                    a_1.nomorindukpegawai,
                    a_1.noidentitas,
                    a_1.gelardepan,
                    gelar_belakang.gelarbelakang_nama
                   FROM pegawai_m a_1
                     LEFT JOIN ( SELECT gelarbelakang_m.gelarbelakang_id,
                            gelarbelakang_m.gelarbelakang_nama
                           FROM gelarbelakang_m) gelar_belakang ON a_1.gelarbelakang::integer = gelar_belakang.gelarbelakang_id) pegawaimengetahui ON stokopname_t.mengetahui_id = pegawaimengetahui.pegawai_id
             LEFT JOIN ( SELECT a_1.rakobat_id,
                    a_1.obatalkes_id,
                    a_1.ruangan_id
                   FROM konfigrak_m a_1) konfigrak_m ON stokopnamedetail_t.obatalkes_id = konfigrak_m.obatalkes_id AND stokopname_t.ruangan_id = konfigrak_m.ruangan_id
             LEFT JOIN ( SELECT a_1.rakobat_id,
                    a_1.rakobat_nama
                   FROM rakobat_m a_1) rak ON konfigrak_m.rakobat_id = rak.rakobat_id
             LEFT JOIN ( SELECT a_1.rakobat_id,
                    a_1.parentrakobat_id AS rak_id,
                    rak_1.rakobat_nama AS rak,
                    a_1.rakobat_nama
                   FROM rakobat_m a_1
                     JOIN rakobat_m rak_1 ON a_1.parentrakobat_id = rak_1.rakobat_id
                  WHERE a_1.parentrakobat_id IS NOT NULL) laci ON konfigrak_m.rakobat_id = laci.rakobat_id
             LEFT JOIN ( SELECT a_1.rakobat_id,
                    a_1.rakobat_nama,
                    a_1.parentrakobat_id,
                    rak_1.rakobat_nama AS rak
                   FROM rakobat_m a_1
                     LEFT JOIN ( SELECT a1.rakobat_id,
                            a1.rakobat_nama
                           FROM rakobat_m a1) rak_1 ON a_1.parentrakobat_id = rak_1.rakobat_id) rakobat_m ON konfigrak_m.rakobat_id = rakobat_m.rakobat_id
             LEFT JOIN ( SELECT hit.ruangan_id,
                    hit.obatalkes_id,
                    hit.total_stok AS qty_sisa
                   FROM ( SELECT stokobatalkes_r.ruangan_id,
                            stokobatalkes_r.obatalkes_id,
                            kartustok.total AS total_stok
                           FROM stokobatalkes_r
                             JOIN ( SELECT a_1.ruangan_id
                                   FROM ruangan_m a_1) ruangan_m_1 ON stokobatalkes_r.ruangan_id = ruangan_m_1.ruangan_id
                             JOIN ( SELECT a_1.obatalkes_id,
                                    a_1.is_active,
                                    a_1.is_deleted
                                   FROM obatalkes_m a_1) obatalkes_m_1 ON stokobatalkes_r.obatalkes_id = obatalkes_m_1.obatalkes_id
                             LEFT JOIN ( SELECT a_1.stokobatr_id,
                                    a_1.obatalkes_id,
                                    a_1.is_deleted
                                   FROM konfigrak_m a_1) konfigrak_m_1 ON stokobatalkes_r.stokobatr_id = konfigrak_m_1.stokobatr_id AND stokobatalkes_r.obatalkes_id = konfigrak_m_1.obatalkes_id AND konfigrak_m_1.is_deleted = false
                             LEFT JOIN ( SELECT st.ruangan_id,
                                    st.obatalkes_id,
                                    sum(st.qtystok_in - st.qtystok_out) AS total
                                   FROM stokobatalkes_t st
                                  GROUP BY st.ruangan_id, st.obatalkes_id) kartustok ON kartustok.ruangan_id = stokobatalkes_r.ruangan_id AND kartustok.obatalkes_id = stokobatalkes_r.obatalkes_id
                          WHERE obatalkes_m_1.is_active = true AND obatalkes_m_1.is_deleted = false) hit) stok ON stokopnamedetail_t.obatalkes_id = stok.obatalkes_id AND stokopname_t.ruangan_id = stok.ruangan_id
             LEFT JOIN ( SELECT DISTINCT ON (lr.ruangan_id, lr.obatalkes_id) lr.logasetobat_id,
                    lr.obatalkes_id,
                    lr.ruangan_id,
                    COALESCE(lr.weighted_avg, 0::numeric) AS weighted_avg,
                    lr.stokobatalkes_id
                   FROM logasetobat_r lr
                  ORDER BY lr.ruangan_id, lr.obatalkes_id, lr.stokobatalkes_id DESC) logasetobat ON stokopnamedetail_t.obatalkes_id = logasetobat.obatalkes_id AND stokopname_t.ruangan_id = logasetobat.ruangan_id
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
                  GROUP BY stokobatalkes_t.obatalkes_id) nulllogasetobat ON stokopnamedetail_t.obatalkes_id = nulllogasetobat.obatalkes_id
             JOIN ( SELECT a_1.lookup_id,
                    a_1.lookup_name
                   FROM lookup_m a_1) look_kondisibarang ON stokopnamedetail_t.kondisibarang::integer = look_kondisibarang.lookup_id) a;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220712_024505_migrate_hotfix_infostokopnamedetail_v_mhkn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220712_024505_migrate_hotfix_infostokopnamedetail_v_mhkn cannot be reverted.\n";

        return false;
    }
    */
}

<?php

use yii\db\Migration;

/**
 * Class m221217_035959_migrate_gb_330_infostokopname_v
 */
class m221217_035959_migrate_gb_330_infostokopname_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infostokopname_v";
        ');

        $this->execute("
            CREATE VIEW \"public\".\"infostokopname_v\" AS  SELECT a.instalasi_id,
    a.instalasi_nama,
    a.ruangan_id,
    a.ruangan_nama,
    a.formulirstokopname_id,
    a.tglformulir,
    a.noformulir,
    a.stokopname_id,
    a.tglstokopname,
    a.nostokopname,
    a.isstokawal,
    a.jenisstokopname,
    a.keterangan_opname,
    a.totalharga_fisik,
    a.totalharga_sistem,
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
    a.is_verifikasi,
    a.tglverifikasi,
    a.pegawaiverifikasi_id,
    a.pegawaiverifikasi_nip,
    a.pegawaiverifikasi_noidentitas,
    a.pegawaiverifikasi_nama,
    sum(a.total_weighted_avg_fisik) AS total_weighted_avg_fisik,
    sum(a.total_weighted_avg_sistem) AS total_weighted_avg_sistem,
    sum(a.selisih_weighted_avg) AS selisih_weighted_avg
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
            COALESCE(stokopnamedetail_t.weighted_avg, logasetobat.weighted_avg::double precision, obatalkes_m.harganetto, nulllogasetobat.harga_weighted_avg) AS weighted_avg,
            COALESCE(stokopnamedetail_t.weighted_avg, logasetobat.weighted_avg::double precision, obatalkes_m.harganetto, nulllogasetobat.harga_weighted_avg) * COALESCE(round(stokopnamedetail_t.revisi_stok::numeric, 3), round(stokopnamedetail_t.volume_fisik::numeric, 3), round(stokopnamedetail_t.volume_sistem::numeric, 3))::double precision AS total_weighted_avg_fisik,
            COALESCE(stokopnamedetail_t.weighted_avg, logasetobat.weighted_avg::double precision, obatalkes_m.harganetto, nulllogasetobat.harga_weighted_avg) * round(stokopnamedetail_t.volume_sistem::numeric, 3)::double precision AS total_weighted_avg_sistem,
            COALESCE(stokopnamedetail_t.weighted_avg, logasetobat.weighted_avg::double precision, obatalkes_m.harganetto, nulllogasetobat.harga_weighted_avg) * COALESCE(round(stokopnamedetail_t.revisi_stok::numeric, 3), round(stokopnamedetail_t.volume_fisik::numeric, 3), round(stokopnamedetail_t.volume_sistem::numeric, 3))::double precision - COALESCE(stokopnamedetail_t.weighted_avg, logasetobat.weighted_avg::double precision, obatalkes_m.harganetto, nulllogasetobat.harga_weighted_avg) * round(stokopnamedetail_t.volume_sistem::numeric, 3)::double precision AS selisih_weighted_avg
           FROM stokopname_t
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
             LEFT JOIN ( SELECT gelarbelakang_m.gelarbelakang_id,
                    gelarbelakang_m.gelarbelakang_nama
                   FROM gelarbelakang_m) gelarbelakangpetugas1 ON petugas1.gelarbelakang::integer = gelarbelakangpetugas1.gelarbelakang_id
             LEFT JOIN ( SELECT gelarbelakang_m.gelarbelakang_id,
                    gelarbelakang_m.gelarbelakang_nama
                   FROM gelarbelakang_m) gelarbelakangpetugas2 ON petugas2.gelarbelakang::integer = gelarbelakangpetugas2.gelarbelakang_id
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
             LEFT JOIN ( SELECT gelarbelakang_m.gelarbelakang_id,
                    gelarbelakang_m.gelarbelakang_nama
                   FROM gelarbelakang_m) gelarbelakangpegawaimengetahui ON pegawaimengetahui.gelarbelakang::integer = gelarbelakangpegawaimengetahui.gelarbelakang_id
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
                           FROM gelarbelakang_m) gelar_belakang ON a_1.gelarbelakang::integer = gelar_belakang.gelarbelakang_id) pegawaiverifikasi ON stokopname_t.pegawaiverifikasi_id = pegawaiverifikasi.pegawai_id
             LEFT JOIN stokopnamedetail_t ON stokopnamedetail_t.stokopname_id = stokopname_t.stokopname_id
             JOIN ( SELECT a_1.obatalkes_id,
                    a_1.harganetto
                   FROM obatalkes_m a_1
                  WHERE a_1.is_deleted = false AND a_1.is_active = true) obatalkes_m ON obatalkes_m.obatalkes_id = stokopnamedetail_t.obatalkes_id
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
                  WHERE (stok_1.harga_asset IS NOT NULL OR stok_1.qty_asset IS NOT NULL) AND (stok_1.harga_asset <> 0::double precision OR stok_1.qty_asset <> 0::double precision)
                  GROUP BY stokobatalkes_t.obatalkes_id) nulllogasetobat ON stokopnamedetail_t.obatalkes_id = nulllogasetobat.obatalkes_id
          WHERE stokopname_t.is_active = true AND stokopname_t.is_deleted = false) a
  GROUP BY a.instalasi_id, a.instalasi_nama, a.ruangan_id, a.ruangan_nama, a.formulirstokopname_id, a.tglformulir, a.noformulir, a.stokopname_id, a.tglstokopname, a.nostokopname, a.isstokawal, a.jenisstokopname, a.keterangan_opname, a.totalharga_fisik, a.totalharga_sistem, a.petugas1_id, a.petugas1_nip, a.petugas1_noidentitas, a.petugas1_gelardepan, a.petugas1_nama, a.petugas1_gelarbelakang, a.petugas2_id, a.petugas2_nip, a.petugas2_noidentitas, a.petugas2_gelardepan, a.petugas2_nama, a.petugas2_gelarbelakang, a.pegawaimengetahui_id, a.pegawaimengetahui_nip, a.pegawaimengetahui_noidentitas, a.pegawaimengetahui_gelardepan, a.pegawaimengetahui_nama, a.pegawaimengetahui_gelarbelakang, a.is_verifikasi, a.tglverifikasi, a.pegawaiverifikasi_id, a.pegawaiverifikasi_nip, a.pegawaiverifikasi_noidentitas, a.pegawaiverifikasi_nama; 
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221217_035959_migrate_gb_330_infostokopname_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221217_035959_migrate_gb_330_infostokopname_v cannot be reverted.\n";

        return false;
    }
    */
}

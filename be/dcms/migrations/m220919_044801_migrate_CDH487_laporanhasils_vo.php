<?php

use yii\db\Migration;

/**
 * Class m220919_044801_migrate_CDH487_laporanhasils_vo
 */
class m220919_044801_migrate_CDH487_laporanhasils_vo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."laporanhasilso_v";
        ');

        $this->execute("
            CREATE VIEW \"public\".\"laporanhasilso_v\" AS  SELECT DISTINCT a.stokopnamedetail_id,
    a.kondisi,
    a.tgl_form_so,
    a.no_form_so,
    a.tgl_validasi_so,
    a.validasi_by,
    a.ruangan_id,
    a.instalasi_id,
    a.nostokopname,
    a.instalasi_ruangan,
    a.jenis_obatalkes,
    a.obatalkes_id,
    a.kode_obat,
    a.nama_obat,
    a.satuan_kecil,
    a.weighted_avg_backup,
        CASE
            WHEN a.base_price_so::text = '0'::text THEN a.weighted_avg
            ELSE a.harganetto
        END AS weighted_avg,
    a.wa,
    a.stok_sistem,
    a.stok_fisik,
    a.selisih,
        CASE
            WHEN a.base_price_so::text = '0'::text THEN a.selisih * a.weighted_avg
            ELSE a.selisih * a.harganetto
        END AS total_harga_selisi,
    a.tgl_implementasi,
    a.selisih_weighted_avg,
        CASE
            WHEN a.base_price_so::text = '0'::text THEN a.stok_sistem * a.weighted_avg
            ELSE a.stok_sistem * a.harganetto
        END AS total_harga_sistem
   FROM ( SELECT stokopnamedetail_t.stokopnamedetail_id,
                CASE
                    WHEN stokobatalkes_t.tglstok_in IS NOT NULL THEN 'IN'::text
                    ELSE 'OUT'::text
                END AS kondisi,
                CASE
                    WHEN stokobatalkes_t.tglstok_in IS NULL THEN max(stokobatalkes_t.tglstok_out)
                    WHEN stokobatalkes_t.tglstok_in IS NOT NULL THEN max(stokobatalkes_t.tglstok_in)
                    ELSE NULL::timestamp without time zone
                END AS tgl_stokopname,
            formulirstokopname_t.created_date AS tgl_form_so,
            formulirstokopname_t.noformulir AS no_form_so,
            stokopname_t.tglverifikasi AS tgl_validasi_so,
            peg_verif_so.nama_pegawai AS validasi_by,
            ruangan_m.ruangan_id,
            instalasi_m.instalasi_id,
            stokopname_t.nostokopname,
            concat(instalasi_m.instalasi_nama, ' - ', ruangan_m.ruangan_nama) AS instalasi_ruangan,
            jenisobatalkes_m.jenisobatalkes_nama AS jenis_obatalkes,
            stokopnamedetail_t.obatalkes_id,
            obatalkes_m.obatalkes_kode AS kode_obat,
            obatalkes_m.obatalkes_nama AS nama_obat,
            sat_kecil.satuanunit_nama AS satuan_kecil,
                CASE
                    WHEN lr.weighted_avg IS NULL AND (COALESCE(stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) - stokopnamedetail_t.volume_sistem) = 0::double precision THEN stokopnamedetail_t.weighted_avg
                    WHEN lr.weighted_avg IS NOT NULL THEN lr.weighted_avg::double precision
                    WHEN lr.weighted_avg IS NULL AND (COALESCE(stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) - stokopnamedetail_t.volume_sistem) <> 0::double precision THEN obatalkes_m.harganetto
                    ELSE NULL::double precision
                END AS weighted_avg_backup,
            COALESCE(lr.weighted_avg, 0::numeric) AS wa,
            formstokopname_t.volume_stok AS stok_sistem,
            COALESCE(stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) AS stok_fisik,
            COALESCE(stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) - COALESCE(stokopnamedetail_t.volume_sistem, 0::double precision) AS selisih,
                CASE
                    WHEN lr.weighted_avg IS NOT NULL THEN lr.weighted_avg::double precision * (stokopnamedetail_t.volume_fisik - stokopnamedetail_t.volume_sistem)
                    ELSE obatalkes_m.harganetto * (stokopnamedetail_t.volume_fisik - stokopnamedetail_t.volume_sistem)
                END AS total_harga_selisih,
            stokopname_t.tgl_implementasi,
            COALESCE(stokopnamedetail_t.weighted_avg, logasetobat.weighted_avg::double precision, obatalkes_m.harganetto, nulllogasetobat.harga_weighted_avg) AS weighted_avg,
            COALESCE(stokopnamedetail_t.weighted_avg, logasetobat.weighted_avg::double precision, obatalkes_m.harganetto, nulllogasetobat.harga_weighted_avg) * (COALESCE(stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) - COALESCE(stokopnamedetail_t.volume_sistem, 0::double precision)) AS selisih_weighted_avg,
            obatalkes_m.harganetto,
            konfigfarmasi_k.base_price_so
           FROM stokopname_t
             JOIN ( SELECT a_1.stokopnamedetail_id,
                    a_1.stokopname_id,
                    a_1.obatalkes_id,
                    a_1.is_deleted,
                    a_1.revisi_stok,
                    a_1.volume_fisik,
                    a_1.volume_sistem,
                    a_1.weighted_avg
                   FROM stokopnamedetail_t a_1) stokopnamedetail_t ON stokopname_t.stokopname_id = stokopnamedetail_t.stokopname_id AND stokopnamedetail_t.is_deleted = false
             LEFT JOIN ( SELECT a_1.created_date,
                    a_1.noformulir,
                    a_1.formulirstokopname_id
                   FROM formulirstokopname_t a_1) formulirstokopname_t ON stokopname_t.formulirstokopname_id = formulirstokopname_t.formulirstokopname_id
             LEFT JOIN ( SELECT a_1.pegawai_id,
                    a_1.nama_pegawai
                   FROM pegawai_m a_1) peg_verif_so ON stokopname_t.pegawaiverifikasi_id = peg_verif_so.pegawai_id
             LEFT JOIN ( SELECT a_1.ruangan_id,
                    a_1.ruangan_nama,
                    a_1.instalasi_id
                   FROM ruangan_m a_1) ruangan_m ON stokopname_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN ( SELECT a_1.instalasi_id,
                    a_1.instalasi_nama
                   FROM instalasi_m a_1) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN ( SELECT a_1.obatalkes_id,
                    a_1.obatalkes_kode,
                    a_1.obatalkes_nama,
                    a_1.jenisobatalkes_id,
                    a_1.harganetto,
                    a_1.satuankecil_id
                   FROM obatalkes_m a_1) obatalkes_m ON stokopnamedetail_t.obatalkes_id = obatalkes_m.obatalkes_id
             LEFT JOIN ( SELECT a_1.jenisobatalkes_id,
                    a_1.jenisobatalkes_nama
                   FROM jenisobatalkes_m a_1) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
             LEFT JOIN ( SELECT a_1.satuanunit_id,
                    a_1.satuanunit_nama
                   FROM satuanunit_m a_1) sat_kecil ON obatalkes_m.satuankecil_id = sat_kecil.satuanunit_id
             LEFT JOIN ( SELECT a_1.formstokopname_id,
                    a_1.volume_stok,
                    a_1.stokopnamedetail_id
                   FROM formstokopname_t a_1) formstokopname_t ON stokopnamedetail_t.stokopnamedetail_id = formstokopname_t.stokopnamedetail_id
             LEFT JOIN ( SELECT DISTINCT ON (a_1.stokopnamedetail_id) a_1.stokobatalkes_id,
                    a_1.stokopnamedetail_id,
                    a_1.tglstok_in,
                    a_1.tglstok_out
                   FROM stokobatalkes_t a_1
                  WHERE a_1.is_deleted = false) stokobatalkes_t ON stokopnamedetail_t.stokopnamedetail_id = stokobatalkes_t.stokopnamedetail_id
             LEFT JOIN ( SELECT a_1.stokobatalkes_id,
                    a_1.weighted_avg,
                    a_1.obatalkes_id,
                    a_1.ruangan_id
                   FROM logasetobat_r a_1) lr ON stokobatalkes_t.stokobatalkes_id = lr.stokobatalkes_id
             LEFT JOIN ( SELECT DISTINCT ON (lr_1.ruangan_id, lr_1.obatalkes_id) lr_1.logasetobat_id,
                    lr_1.obatalkes_id,
                    lr_1.ruangan_id,
                    COALESCE(lr_1.weighted_avg, 0::numeric) AS weighted_avg,
                    lr_1.stokobatalkes_id
                   FROM logasetobat_r lr_1
                  ORDER BY lr_1.ruangan_id, lr_1.obatalkes_id, lr_1.stokobatalkes_id DESC) logasetobat ON stokopnamedetail_t.obatalkes_id = logasetobat.obatalkes_id AND stokopname_t.ruangan_id = logasetobat.ruangan_id
             LEFT JOIN ( SELECT stokobatalkes_t_1.obatalkes_id,
                    sum(stok_1.harga_asset / stok_1.qty_asset) AS harga_weighted_avg
                   FROM stokobatalkes_t stokobatalkes_t_1
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
                                  GROUP BY st_ob.obatalkes_id) stok_obat ON penerimaan_obat.obatalkes_id = stok_obat.obatalkes_id) stok_1 ON stokobatalkes_t_1.obatalkes_id = stok_1.obatalkes_id
                  GROUP BY stokobatalkes_t_1.obatalkes_id) nulllogasetobat ON stokopnamedetail_t.obatalkes_id = nulllogasetobat.obatalkes_id
             LEFT JOIN ( SELECT a_1.base_price_so,
                    a_1.is_deleted
                   FROM konfigfarmasi_k a_1
                  WHERE a_1.konfigfarmasi_id = 1) konfigfarmasi_k ON konfigfarmasi_k.is_deleted IS FALSE
          GROUP BY stokopnamedetail_t.stokopnamedetail_id, stokobatalkes_t.tglstok_in, stokobatalkes_t.tglstok_out, formulirstokopname_t.created_date, formulirstokopname_t.noformulir, stokopname_t.tglverifikasi, peg_verif_so.nama_pegawai, ruangan_m.ruangan_id, instalasi_m.instalasi_id, stokopname_t.nostokopname, instalasi_m.instalasi_nama, ruangan_m.ruangan_nama, jenisobatalkes_m.jenisobatalkes_nama, stokopnamedetail_t.obatalkes_id, obatalkes_m.obatalkes_kode, obatalkes_m.obatalkes_nama, nulllogasetobat.harga_weighted_avg, sat_kecil.satuanunit_nama, lr.weighted_avg, obatalkes_m.harganetto, formstokopname_t.volume_stok, logasetobat.weighted_avg, stokopnamedetail_t.volume_fisik, stokopname_t.tgl_implementasi, stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_sistem, stokopnamedetail_t.weighted_avg, konfigfarmasi_k.base_price_so) a; ");
  
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220919_044801_migrate_CDH487_laporanhasils_vo cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220919_044801_migrate_CDH487_laporanhasils_vo cannot be reverted.\n";

        return false;
    }
    */
}

<?php

use yii\db\Migration;

/**
 * Class m221217_031540_migrate_gb_330_laporansumso_v
 */
class m221217_031540_migrate_gb_330_laporansumso_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."laporansumso_v";
        ');

        $this->execute("
            CREATE VIEW \"public\".\"laporansumso_v\" AS  SELECT stokopnamedetail_t.stokopnamedetail_id,
    ruangan_m.ruangan_nama AS store,
    stokopname_t.nostokopname AS no_so,
    stokopname_t.tglstokopname AS tgl_so,
    stokopname_t.tglverifikasi AS tgl_validasi,
    peg_validasi.nama_pegawai AS validasi_oleh,
    jenisobatalkes_m.jenisobatalkes_nama AS jenis_obat,
    obatalkes_m.obatalkes_id,
    obatalkes_m.obatalkes_kode AS kode_obat,
    obatalkes_m.obatalkes_nama AS nama_obat,
    kecil.satuanunit_nama AS satuan_kecil,
    concat('1 ', besar.satuanunit_nama, ' - ', satuankonversi_m.nilai_konversi, ' ', kecil.satuanunit_nama) AS konversi,
    COALESCE(stokopnamedetail_t.weighted_avg, lr.weighted_avg::double precision, nullweighted_avg.weighted_avg::double precision, obatalkes_m.harganetto, nulllogasetobat.harga_weighted_avg) AS weighted_average,
    stokopnamedetail_t.volume_sistem AS system_stock_qty,
        CASE
            WHEN stokopnamedetail_t.revisi_stok IS NOT NULL THEN stokopnamedetail_t.revisi_stok
            ELSE COALESCE(stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem)
        END AS physical_stock_qty,
        CASE
            WHEN stokopnamedetail_t.selisih_akhir IS NOT NULL THEN stokopnamedetail_t.selisih_akhir
            ELSE COALESCE(stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) - stokopnamedetail_t.volume_sistem
        END AS variance_qty,
        CASE
            WHEN lr.weighted_avg IS NOT NULL THEN stokopnamedetail_t.volume_sistem * lr.weighted_avg::double precision
            ELSE stokopnamedetail_t.volume_sistem * obatalkes_m.harganetto
        END AS opening_total_batch_cost,
        CASE
            WHEN lr.weighted_avg IS NOT NULL THEN COALESCE(stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) * lr.weighted_avg::double precision
            ELSE COALESCE(stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) * obatalkes_m.harganetto
        END AS ending_total_batch_cost,
        CASE
            WHEN stokopnamedetail_t.selisih_akhir IS NOT NULL THEN stokopnamedetail_t.selisih_akhir * COALESCE(lr.weighted_avg::double precision, stokopnamedetail_t.weighted_avg)
            ELSE COALESCE(stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) - stokopnamedetail_t.volume_sistem * COALESCE(lr.weighted_avg::double precision, stokopnamedetail_t.weighted_avg)
        END AS selisih_batch_cost,
    formulirstokopname_t.tglformulir,
    formulirstokopname_t.noformulir,
    ruangan_m.ruangan_id,
    jenisobatalkes_m.jenisobatalkes_id,
    stokopname_t.tgl_implementasi,
    COALESCE(lr.weighted_avg::double precision, stokopnamedetail_t.weighted_avg) AS \"coalesce\",
    COALESCE(stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) - stokopnamedetail_t.volume_sistem AS selisih
   FROM stokopname_t
     JOIN ( SELECT a.stokopname_id,
            a.obatalkes_id,
            a.volume_fisik,
            a.volume_sistem,
            a.satuankecil_id,
            a.stokopnamedetail_id,
            a.is_deleted,
            a.revisi_stok,
            a.stok_akhir,
            a.selisih_akhir,
            a.weighted_avg
           FROM stokopnamedetail_t a
          WHERE a.is_deleted = false) stokopnamedetail_t ON stokopname_t.stokopname_id = stokopnamedetail_t.stokopname_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON stokopname_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.obatalkes_id,
            a.jenisobatalkes_id,
            a.satuankecil_id,
            a.satuanbesar_id,
            a.obatalkes_kode,
            a.obatalkes_nama,
            a.harganetto
           FROM obatalkes_m a
          WHERE a.is_deleted = false AND a.is_active = true) obatalkes_m ON stokopnamedetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT a.jenisobatalkes_id,
            a.jenisobatalkes_nama
           FROM jenisobatalkes_m a) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) peg_validasi ON stokopname_t.pegawaiverifikasi_id = peg_validasi.pegawai_id
     LEFT JOIN ( SELECT a.stokopname_id,
            a.tglformulir,
            a.noformulir
           FROM formulirstokopname_t a
          WHERE a.is_deleted = false) formulirstokopname_t ON stokopname_t.stokopname_id = formulirstokopname_t.stokopname_id
     LEFT JOIN ( SELECT a.satuanbesar_id,
            a.satuankecil_id,
            a.nilai_konversi,
            a.obatalkes_id
           FROM satuankonversi_m a
          WHERE a.is_deleted = false AND a.is_active = true) satuankonversi_m ON obatalkes_m.obatalkes_id = satuankonversi_m.obatalkes_id AND obatalkes_m.satuankecil_id = satuankonversi_m.satuankecil_id AND obatalkes_m.satuankecil_id = satuankonversi_m.satuanbesar_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) kecil ON satuankonversi_m.satuankecil_id = kecil.satuanunit_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) besar ON satuankonversi_m.satuanbesar_id = besar.satuanunit_id
     LEFT JOIN ( SELECT stokobatalkes_t.stokopnamedetail_id,
            COALESCE(max(logasetobat_r.weighted_avg), 0::numeric) AS weighted_avg
           FROM stokobatalkes_t
             LEFT JOIN ( SELECT a.logasetobat_id,
                    a.stokobatalkes_id,
                    a.obatalkes_id,
                    a.ruangan_id,
                    a.weighted_avg,
                    obatalkes_m_1.harganetto
                   FROM logasetobat_r a
                     LEFT JOIN ( SELECT a_1.obatalkes_id,
                            a_1.obatalkes_kode,
                            a_1.obatalkes_nama,
                            a_1.harganetto
                           FROM obatalkes_m a_1) obatalkes_m_1 ON obatalkes_m_1.obatalkes_id = a.obatalkes_id
                  WHERE a.ruangan_id = (( SELECT lookuptransaksi_m.kode_id
                           FROM lookuptransaksi_m
                          WHERE lookuptransaksi_m.kode_transaksi::text = 'gudang_farmasi'::text))) logasetobat_r ON logasetobat_r.stokobatalkes_id = stokobatalkes_t.stokobatalkes_id
          WHERE stokobatalkes_t.stokopnamedetail_id IS NOT NULL AND stokobatalkes_t.is_deleted = false
          GROUP BY stokobatalkes_t.stokopnamedetail_id) lr ON lr.stokopnamedetail_id = stokopnamedetail_t.stokopnamedetail_id
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
     LEFT JOIN ( SELECT a.obatalkes_id,
            a.weighted_avg
           FROM logasetobat_r a
          WHERE (a.logasetobat_id IN ( SELECT max(logasetobat_r.logasetobat_id) AS max_id
                   FROM logasetobat_r
                  WHERE logasetobat_r.ruangan_id = (( SELECT lookuptransaksi_m.kode_id
                           FROM lookuptransaksi_m
                          WHERE lookuptransaksi_m.kode_transaksi::text = 'gudang_farmasi'::text))
                  GROUP BY logasetobat_r.obatalkes_id, logasetobat_r.ruangan_id))) nullweighted_avg ON nullweighted_avg.obatalkes_id = obatalkes_m.obatalkes_id
  WHERE stokopname_t.is_deleted = false; 
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221217_031540_migrate_gb_330_laporansumso_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221217_031540_migrate_gb_330_laporansumso_v cannot be reverted.\n";

        return false;
    }
    */
}

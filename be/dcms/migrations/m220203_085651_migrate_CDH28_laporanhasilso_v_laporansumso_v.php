<?php

use yii\db\Migration;

/**
 * Class m220203_085651_migrate_CDH28_laporanhasilso_v_laporansumso_v
 */
class m220203_085651_migrate_CDH28_laporanhasilso_v_laporansumso_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporanhasilso_v;');

        $this->execute("
            CREATE VIEW \"public\".\"laporanhasilso_v\" AS  SELECT a.stokopnamedetail_id,
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
                       a.weighted_avg,
                       a.stok_sistem,
                       a.stok_fisik,
                       a.selisih,
                       a.selisih * a.weighted_avg AS total_harga_selisi,
                       a.tgl_implementasi
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
                                       WHEN lr.weighted_avg IS NOT NULL THEN lr.weighted_avg::double precision
                                       ELSE obatalkes_m.harganetto
                                   END AS weighted_avg,
                               formstokopname_t.volume_stok AS stok_sistem,
                               COALESCE(stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) AS stok_fisik,
                               COALESCE(stokopnamedetail_t.revisi_stok, stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) - stokopnamedetail_t.volume_sistem AS selisih,
                                   CASE
                                       WHEN lr.weighted_avg IS NOT NULL THEN lr.weighted_avg::double precision * (stokopnamedetail_t.volume_fisik - stokopnamedetail_t.volume_sistem)
                                       ELSE obatalkes_m.harganetto * (stokopnamedetail_t.volume_fisik - stokopnamedetail_t.volume_sistem)
                                   END AS total_harga_selisi,
                               stokopname_t.tgl_implementasi
                              FROM stokopname_t
                                JOIN stokopnamedetail_t ON stokopname_t.stokopname_id = stokopnamedetail_t.stokopname_id AND stokopnamedetail_t.is_deleted = false
                                LEFT JOIN formulirstokopname_t ON stokopname_t.formulirstokopname_id = formulirstokopname_t.formulirstokopname_id
                                LEFT JOIN pegawai_m peg_verif_so ON stokopname_t.pegawaiverifikasi_id = peg_verif_so.pegawai_id
                                LEFT JOIN ruangan_m ON stokopname_t.ruangan_id = ruangan_m.ruangan_id
                                LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                                LEFT JOIN obatalkes_m ON stokopnamedetail_t.obatalkes_id = obatalkes_m.obatalkes_id
                                LEFT JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                                LEFT JOIN satuanunit_m sat_kecil ON obatalkes_m.satuankecil_id = sat_kecil.satuanunit_id
                                LEFT JOIN formstokopname_t ON stokopnamedetail_t.stokopnamedetail_id = formstokopname_t.stokopnamedetail_id
                                LEFT JOIN stokobatalkes_t ON stokopnamedetail_t.stokopnamedetail_id = stokobatalkes_t.stokopnamedetail_id
                                LEFT JOIN logasetobat_r lr ON stokobatalkes_t.stokobatalkes_id = lr.stokobatalkes_id
                             WHERE stokobatalkes_t.stokopnamedetail_id IS NOT NULL
                             GROUP BY stokopnamedetail_t.stokopnamedetail_id, stokobatalkes_t.tglstok_in, stokobatalkes_t.tglstok_out, formulirstokopname_t.created_date, formulirstokopname_t.noformulir, stokopname_t.tglverifikasi, peg_verif_so.nama_pegawai, ruangan_m.ruangan_id, instalasi_m.instalasi_id, stokopname_t.nostokopname, instalasi_m.instalasi_nama, ruangan_m.ruangan_nama, jenisobatalkes_m.jenisobatalkes_nama, stokopnamedetail_t.obatalkes_id, obatalkes_m.obatalkes_kode, obatalkes_m.obatalkes_nama, sat_kecil.satuanunit_nama, lr.weighted_avg, obatalkes_m.harganetto, formstokopname_t.volume_stok, stokopnamedetail_t.volume_fisik, stokopname_t.tgl_implementasi) a;");

 							$this->execute('DROP VIEW if exists public.laporansumso_v;');
 
        					$this->execute("
            CREATE VIEW \"public\".\"laporansumso_v\" AS   SELECT ruangan_m.ruangan_nama AS store,
    stokopname_t.nostokopname AS no_so,
    stokopname_t.tglstokopname AS tgl_so,
    stokopname_t.tglverifikasi AS tgl_validasi,
    peg_validasi.nama_pegawai AS validasi_oleh,
    jenisobatalkes_m.jenisobatalkes_nama AS jenis_obat,
    obatalkes_m.obatalkes_kode AS kode_obat,
    obatalkes_m.obatalkes_nama AS nama_obat,
    kecil.satuanunit_nama AS satuan_kecil,
    concat('1 ', besar.satuanunit_nama, ' - ', satuankonversi_m.nilai_konversi, ' ', kecil.satuanunit_nama) AS konversi,
        CASE
            WHEN lr.weighted_avg IS NOT NULL THEN lr.weighted_avg::double precision
            ELSE obatalkes_m.harganetto
        END AS weighted_average,
    stokopnamedetail_t.volume_sistem AS system_stock_qty,
    COALESCE(stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) AS physical_stock_qty,
    COALESCE(stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) - stokopnamedetail_t.volume_sistem AS variance_qty,
        CASE
            WHEN lr.weighted_avg IS NOT NULL THEN stokopnamedetail_t.volume_sistem * lr.weighted_avg::double precision
            ELSE stokopnamedetail_t.volume_sistem * obatalkes_m.harganetto
        END AS opening_total_batch_cost,
        CASE
            WHEN lr.weighted_avg IS NOT NULL THEN COALESCE(stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) * lr.weighted_avg::double precision
            ELSE COALESCE(stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) * obatalkes_m.harganetto
        END AS ending_total_batch_cost,
        CASE
            WHEN lr.weighted_avg IS NOT NULL THEN (COALESCE(stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) - stokopnamedetail_t.volume_sistem) * lr.weighted_avg::double precision
            ELSE (COALESCE(stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) - stokopnamedetail_t.volume_sistem) * obatalkes_m.harganetto
        END AS selisih_batch_cost,
    formulirstokopname_t.tglformulir,
    formulirstokopname_t.noformulir,
    ruangan_m.ruangan_id,
    jenisobatalkes_m.jenisobatalkes_id,
    stokopname_t.tgl_implementasi
   FROM stokopname_t
     JOIN ( SELECT a.stokopname_id,
            a.obatalkes_id,
            a.volume_fisik,
            a.volume_sistem,
            a.satuankecil_id,
            a.stokopnamedetail_id
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
           FROM obatalkes_m a) obatalkes_m ON stokopnamedetail_t.obatalkes_id = obatalkes_m.obatalkes_id
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
          WHERE a.is_deleted = false) satuankonversi_m ON obatalkes_m.obatalkes_id = satuankonversi_m.obatalkes_id AND obatalkes_m.satuankecil_id = satuankonversi_m.satuankecil_id AND obatalkes_m.satuankecil_id = satuankonversi_m.satuanbesar_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) kecil ON satuankonversi_m.satuankecil_id = kecil.satuanunit_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) besar ON satuankonversi_m.satuanbesar_id = besar.satuanunit_id
     LEFT JOIN ( SELECT stokobatalkes_t.stokobatalkes_id,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.stokopnamedetail_id
           FROM stokobatalkes_t
          WHERE stokobatalkes_t.stokopnamedetail_id IS NOT NULL) st ON stokopnamedetail_t.stokopnamedetail_id = st.stokopnamedetail_id
     LEFT JOIN logasetobat_r lr ON lr.stokobatalkes_id = st.stokobatalkes_id
  WHERE stokopname_t.is_deleted = false;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220203_085651_migrate_CDH28_laporanhasilso_v_laporansumso_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220203_085651_migrate_CDH28_laporanhasilso_v_laporansumso_v cannot be reverted.\n";

        return false;
    }
    */
}

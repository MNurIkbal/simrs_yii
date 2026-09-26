<?php

use yii\db\Migration;

/**
 * Class m211019_125052_improve_laporansumso
 */
class m211019_125052_improve_laporansumso extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."laporansumso_v";');
        
        $this->execute("
            CREATE VIEW \"public\".\"laporansumso_v\" AS  SELECT ruangan_m.ruangan_nama AS store,
    stokopname_t.nostokopname AS no_so,
    stokopname_t.tglstokopname AS tgl_so,
    stokopname_t.tglverifikasi AS tgl_validasi,
    peg_validasi.nama_pegawai AS validasi_oleh,
    jenisobatalkes_m.jenisobatalkes_nama AS jenis_obat,
    obatalkes_m.obatalkes_kode AS kode_obat,
    obatalkes_m.obatalkes_nama AS nama_obat,
    kecil.satuanunit_nama AS satuan_kecil,
    concat('1 ', besar.satuanunit_nama, ' - ', satuankonversi_m.nilai_konversi, ' ', kecil.satuanunit_nama) AS konversi,
    obatalkes_m.harganetto AS weighted_average,
    stokopnamedetail_t.volume_sistem AS system_stock_qty,
    COALESCE(stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) AS physical_stock_qty,
    COALESCE(stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) - stokopnamedetail_t.volume_sistem AS variance_qty,
    stokopnamedetail_t.volume_sistem * obatalkes_m.harganetto AS opening_total_batch_cost,
    COALESCE(stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) * obatalkes_m.harganetto AS ending_total_batch_cost,
    (COALESCE(stokopnamedetail_t.volume_fisik, stokopnamedetail_t.volume_sistem) - stokopnamedetail_t.volume_sistem) * obatalkes_m.harganetto AS selisih_batch_cost,
    formulirstokopname_t.tglformulir,
    formulirstokopname_t.noformulir,
    ruangan_m.ruangan_id,
    jenisobatalkes_m.jenisobatalkes_id
   FROM stokopname_t
     JOIN ( SELECT a.stokopname_id,
            a.obatalkes_id,
            a.volume_fisik,
            a.volume_sistem,
            a.satuankecil_id
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
  WHERE stokopname_t.is_deleted = false;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211019_125052_improve_laporansumso cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211019_125052_improve_laporansumso cannot be reverted.\n";

        return false;
    }
    */
}

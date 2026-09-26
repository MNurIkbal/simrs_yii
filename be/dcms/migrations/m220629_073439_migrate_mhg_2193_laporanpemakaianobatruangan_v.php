<?php

use yii\db\Migration;

/**
 * Class m220629_073439_migrate_mhg_2193_laporanpemakaianobatruangan_v
 */
class m220629_073439_migrate_mhg_2193_laporanpemakaianobatruangan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."laporanpemakaianobatruangan_v";');

        $this->execute("
           CREATE VIEW \"public\".\"laporanpemakaianobatruangan_v\" AS   SELECT pemakaianobat_t.tglpemakaianobat AS tgl_transaksi,
    pemakaianobat_t.nopemakaian_obat AS no_transaksi,
    obatalkes_m.obatalkes_kode AS kode_obat,
    obatalkes_m.obatalkes_nama AS nama_obat,
    pemakaianobatdetail_t.jumlah_input AS qty_input,
    pemakaianobatdetail_t.qty_satuanpakai AS qty_konversi,
    s_besar.satuanunit_nama AS satuan_besar,
    s_kecil.satuanunit_nama AS satuan_kecil,
    obatalkes_m.harganetto AS harga_netto,
    obatalkes_m.harganetto * s_konversi.nilai_konversi AS harga_netto_konversi,
    obatalkes_m.harganetto * s_konversi.nilai_konversi * pemakaianobatdetail_t.jumlah_input AS total_harga,
    pegawai_m.nama_pegawai AS \"user\",
    pemakaianobatdetail_t.ket_obatpakai AS catatan,
    pemakaianobat_t.ruangan_id,
    ruangan_m.ruangan_nama,
    jenisobatalkes_m.jenisobatalkes_nama
   FROM pemakaianobatdetail_t
     JOIN ( SELECT a.pemakaianobat_id,
            a.tglpemakaianobat,
            a.nopemakaian_obat,
            a.ruangan_id,
            a.pegawai_id
           FROM pemakaianobat_t a) pemakaianobat_t ON pemakaianobatdetail_t.pemakaianobat_id = pemakaianobat_t.pemakaianobat_id
     JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_kode,
            a.obatalkes_nama,
            a.harganetto,
            a.jenisobatalkes_id
           FROM obatalkes_m a) obatalkes_m ON pemakaianobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) s_besar ON pemakaianobatdetail_t.satuanbesar_id = s_besar.satuanunit_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) s_kecil ON pemakaianobatdetail_t.satuankecil_id = s_kecil.satuanunit_id
     LEFT JOIN ( SELECT satuankonversi_m.obatalkes_id,
            satuankonversi_m.satuanbesar_id,
            satuankonversi_m.satuankecil_id,
            satuankonversi_m.nilai_konversi
           FROM satuankonversi_m
          WHERE satuankonversi_m.is_deleted = false) s_konversi ON pemakaianobatdetail_t.obatalkes_id = s_konversi.obatalkes_id AND pemakaianobatdetail_t.satuanbesar_id = s_konversi.satuanbesar_id AND pemakaianobatdetail_t.satuankecil_id = s_konversi.satuankecil_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON pemakaianobat_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.jenisobatalkes_id,
            a.jenisobatalkes_nama
           FROM jenisobatalkes_m a) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON pemakaianobat_t.ruangan_id = ruangan_m.ruangan_id; ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220629_073439_migrate_mhg_2193_laporanpemakaianobatruangan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220629_073439_migrate_mhg_2193_laporanpemakaianobatruangan_v cannot be reverted.\n";

        return false;
    }
    */
}

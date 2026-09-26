<?php

use yii\db\Migration;

/**
 * Class m201013_031238_migrate_20201013_laporanpemakaianobatruangan
 */
class m201013_031238_migrate_20201013_laporanpemakaianobatruangan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."laporanpemakaianobatruangan_v";');

        $this->execute("
            CREATE VIEW \"public\".\"laporanpemakaianobatruangan_v\" AS  SELECT pemakaianobat_t.tglpemakaianobat AS tgl_transaksi,
    pemakaianobat_t.nopemakaian_obat AS no_transaksi,
    obatalkes_m.obatalkes_kode AS kode_obat,
    obatalkes_m.obatalkes_nama AS nama_obat,
    pemakaianobatdetail_t.jumlah_input AS qty_input,
    pemakaianobatdetail_t.qty_satuanpakai AS qty_konversi,
    s_besar.satuanunit_nama AS satuan_besar,
    s_kecil.satuanunit_nama AS satuan_kecil,
    obatalkes_m.harganetto AS harga_netto,
    (obatalkes_m.harganetto * s_konversi.nilai_konversi) AS harga_netto_konversi,
    ((obatalkes_m.harganetto * s_konversi.nilai_konversi) * pemakaianobatdetail_t.jumlah_input) AS total_harga,
    pegawai_m.nama_pegawai AS \"user\",
    pemakaianobatdetail_t.ket_obatpakai AS catatan,
    pemakaianobat_t.ruangan_id
   FROM ((((((pemakaianobatdetail_t
     JOIN pemakaianobat_t ON ((pemakaianobatdetail_t.pemakaianobat_id = pemakaianobat_t.pemakaianobat_id)))
     JOIN obatalkes_m ON ((pemakaianobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN satuanunit_m s_besar ON ((pemakaianobatdetail_t.satuanbesar_id = s_besar.satuanunit_id)))
     LEFT JOIN satuanunit_m s_kecil ON ((pemakaianobatdetail_t.satuankecil_id = s_kecil.satuanunit_id)))
     LEFT JOIN ( SELECT satuankonversi_m.obatalkes_id,
            satuankonversi_m.satuanbesar_id,
            satuankonversi_m.satuankecil_id,
            satuankonversi_m.nilai_konversi
           FROM satuankonversi_m
          WHERE (satuankonversi_m.is_deleted = false)) s_konversi ON (((pemakaianobatdetail_t.obatalkes_id = s_konversi.obatalkes_id) AND (pemakaianobatdetail_t.satuanbesar_id = s_konversi.satuanbesar_id) AND (pemakaianobatdetail_t.satuankecil_id = s_konversi.satuankecil_id))))
     LEFT JOIN pegawai_m ON ((pemakaianobat_t.pegawai_id = pegawai_m.pegawai_id)));");

        $this->execute('ALTER TABLE "public"."laporanpemakaianobatruangan_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201013_031238_migrate_20201013_laporanpemakaianobatruangan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201013_031238_migrate_20201013_laporanpemakaianobatruangan cannot be reverted.\n";

        return false;
    }
    */
}

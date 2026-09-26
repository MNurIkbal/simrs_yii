<?php

use yii\db\Migration;

/**
 * Class m201129_035001_migrate_20201129_laporanpenerimaanpopr_v
 */
class m201129_035001_migrate_20201129_laporanpenerimaanpopr_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute("
            CREATE VIEW \"public\".\"laporanpenerimaanpopr_v\" AS  SELECT btrim(validasipoobat_t.no_poobat::text) AS no_po,
    btrim(supplier_m.supplier_nama::text) AS vendor_obat,
    btrim(obatalkes_m.obatalkes_nama::text) AS nama_obat,
    validasipoobatdetail_t.qty_po::double precision / satuankonversi_m.nilai_konversi AS qty_po,
    btrim(sat_besar.satuanunit_nama::text) AS satuan_po,
    satuankonversi_m.nilai_konversi,
    validasipoobatdetail_t.qty_penerimaan::double precision * satuankonversi_m.nilai_konversi AS qty_terima,
    btrim(sat_kecil.satuanunit_nama::text) AS satuan_terima,
    validasipoobatdetail_t.harga AS hna,
    validasipoobatdetail_t.discount AS disc,
    pajak_m.pajak_persen AS ppn,
    validasipoobatdetail_t.jumlah AS harga_akhir,
    btrim(fgetnamalookup(validasipoobat_t.status_penerimaan)::text) AS status_po,
    btrim(validasipoobat_t.catatan1) AS catatan_1,
    btrim(validasipoobat_t.catatan2) AS catatan_2,
    btrim(validasipoobat_t.catatan) AS alasan_batal,
    btrim(penerimaanobat_t.no_penerimaan::text) AS no_penerimaan,
    penerimaanobat_t.tgl_penerimaan AS tanggal_penerimaan,
    purchasereq_t.no_pr,
    purchasereq_t.tgl_pr AS tanggal_pr,
    purchasereq_t.is_prcyto AS jenis_pr,
    fgetnamalookup(purchasereq_t.status::integer) AS status_pr,
    validasipoobat_t.tgl_validasi AS tgl_verifikasi
   FROM validasipoobat_t
     JOIN validasipoobatdetail_t ON validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id
     JOIN obatalkes_m ON validasipoobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN supplier_m ON validasipoobat_t.supplier_id = supplier_m.supplier_id
     JOIN pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
     LEFT JOIN penerimaanobatdetail_t ON validasipoobatdetail_t.validasipoobatdetail_id = penerimaanobatdetail_t.validasipoobatdetail_id
     LEFT JOIN penerimaanobat_t ON penerimaanobatdetail_t.penerimaanobat_id = penerimaanobat_t.penerimaanobat_id
     LEFT JOIN purchasereqdetail_t ON validasipoobatdetail_t.purchasereqdetail_id = purchasereqdetail_t.purchasereqdetail_id
     LEFT JOIN purchasereq_t ON purchasereqdetail_t.purchasereq_id = purchasereq_t.purchasereq_id
     LEFT JOIN satuankonversi_m ON penerimaanobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id
     LEFT JOIN satuanunit_m sat_kecil ON satuankonversi_m.satuankecil_id = sat_kecil.satuanunit_id
     LEFT JOIN satuanunit_m sat_besar ON satuankonversi_m.satuanbesar_id = sat_besar.satuanunit_id
  WHERE validasipoobat_t.is_deleted = false AND penerimaanobatdetail_t.is_deleted = false
  ORDER BY (btrim(validasipoobat_t.no_poobat::text)), (btrim(obatalkes_m.obatalkes_nama::text));");
        
         $this->execute('ALTER TABLE "public"."laporanpenerimaanpopr_v" OWNER TO "postgres";');

         
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201129_035001_migrate_20201129_laporanpenerimaanpopr_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201129_035001_migrate_20201129_laporanpenerimaanpopr_v cannot be reverted.\n";

        return false;
    }
    */
}

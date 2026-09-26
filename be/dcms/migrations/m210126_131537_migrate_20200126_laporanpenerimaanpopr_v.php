<?php

use yii\db\Migration;

/**
 * Class m210126_131537_migrate_20200126_laporanpenerimaanpopr_v
 */
class m210126_131537_migrate_20200126_laporanpenerimaanpopr_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."laporanpenerimaanpopr_v";');

        $this->execute("
            CREATE VIEW \"public\".\"laporanpenerimaanpopr_v\" AS  SELECT purchasereq_t.no_pr,
    btrim(validasipoobat_t.no_poobat::text) AS no_po,
    btrim(supplier_m.supplier_nama::text) AS vendor_obat,
    btrim(obatalkes_m.obatalkes_nama::text) AS nama_obat,
        CASE
            WHEN validasipoobat_t.is_validasi IS TRUE THEN validasipoobatdetail_t.qty_po::double precision / satuankonversi_m.nilai_konversi
            ELSE validasipoobatdetail_t.qty_po::double precision
        END AS qty_po,
    btrim(sat_besar.satuanunit_nama::text) AS satuan_po,
    satuankonversi_m.nilai_konversi,
    COALESCE(penerimaanobatdetail_t.qty_diterima::double precision, 0::double precision) * satuankonversi_m.nilai_konversi AS qty_terima,
    btrim(sat_kecil.satuanunit_nama::text) AS satuan_terima,
    validasipoobatdetail_t.harga AS hna,
    validasipoobatdetail_t.discount AS disc,
    pajak_m.pajak_persen AS ppn,
    COALESCE(penerimaanobatdetail_t.jumlah - penerimaanobatdetail_t.discount_rp +
        CASE
            WHEN COALESCE(pajak_m.pajak_persen::integer, 0) = 0 THEN 0::double precision
            ELSE (penerimaanobatdetail_t.jumlah - penerimaanobatdetail_t.discount_rp) / (100 / pajak_m.pajak_persen)::double precision
        END, 0::double precision) AS harga_akhir,
    btrim(fgetnamalookup(validasipoobat_t.status_penerimaan)::text) AS status_po,
    btrim(validasipoobat_t.catatan1) AS catatan_1,
    btrim(validasipoobat_t.catatan2) AS catatan_2,
    btrim(validasipoobat_t.catatan) AS alasan_batal_po,
    btrim(penerimaanobat_t.no_penerimaan::text) AS no_penerimaan,
    penerimaanobat_t.tgl_penerimaan AS tanggal_penerimaan,
    purchasereq_t.tgl_pr AS tanggal_pr,
    purchasereq_t.is_prcyto AS jenis_pr,
    fgetnamalookup(purchasereqdetail_t.status::integer) AS status_pr,
        CASE validasipoobat_t.is_validasi
            WHEN true THEN validasipoobat_t.tgl_validasi
            ELSE NULL::timestamp without time zone
        END AS tgl_verifikasi,
    btrim(purchasereqdetail_t.alasan) AS alasan_batal_pr,
    validasipoobat_t.tgl_validasi AS tgl_po,
    validasipoobat_t.created_date AS tgl_create_po,
    purchasereqdetail_t.purchasereqdetail_id
   FROM purchasereqdetail_t
     JOIN purchasereq_t ON purchasereq_t.purchasereq_id = purchasereqdetail_t.purchasereq_id
     LEFT JOIN validasipoobatdetail_t ON validasipoobatdetail_t.purchasereqdetail_id = purchasereqdetail_t.purchasereqdetail_id
     LEFT JOIN validasipoobat_t ON validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id AND validasipoobat_t.is_deleted = false
     JOIN obatalkes_m ON purchasereqdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN supplier_m ON validasipoobat_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
     LEFT JOIN penerimaanobatdetail_t ON validasipoobatdetail_t.validasipoobatdetail_id = penerimaanobatdetail_t.validasipoobatdetail_id AND penerimaanobatdetail_t.is_deleted = false
     LEFT JOIN penerimaanobat_t ON penerimaanobatdetail_t.penerimaanobat_id = penerimaanobat_t.penerimaanobat_id
     LEFT JOIN satuankonversi_m ON validasipoobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id
     LEFT JOIN satuanunit_m sat_kecil ON satuankonversi_m.satuankecil_id = sat_kecil.satuanunit_id
     LEFT JOIN satuanunit_m sat_besar ON satuankonversi_m.satuanbesar_id = sat_besar.satuanunit_id
  WHERE purchasereq_t.is_deleted = false AND purchasereqdetail_t.is_deleted = false
  ORDER BY (btrim(purchasereq_t.no_pr::text)), (btrim(validasipoobat_t.no_poobat::text)), (btrim(obatalkes_m.obatalkes_nama::text));");
        
        $this->execute('ALTER TABLE "public"."laporanpenerimaanpopr_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210126_131537_migrate_20200126_laporanpenerimaanpopr_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210126_131537_migrate_20200126_laporanpenerimaanpopr_v cannot be reverted.\n";

        return false;
    }
    */
}

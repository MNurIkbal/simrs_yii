<?php

use yii\db\Migration;

/**
 * Class m210225_100422_migrate_20210225_laporanpenerimaanpopr_v
 */
class m210225_100422_migrate_20210225_laporanpenerimaanpopr_v extends Migration
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
    validasipoobatdetail_t.qty_penerimaan::double precision AS qty_terima,
    btrim(sat_kecil.satuanunit_nama::text) AS satuan_terima,
    validasipoobatdetail_t.harga AS hna,
    validasipoobatdetail_t.discount AS disc,
    pajak_m.pajak_persen AS ppn,
    validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp AS sub_total,
    validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp + (validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp) * validasipoobat_t.ppn_persen::double precision / 100::double precision AS harga_akhir,
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
    purchasereqdetail_t.purchasereqdetail_id,
    obatalkes_m.obatalkes_kode AS kode_obat,
    manufaktur_m.nama AS manufaktur_nama,
    penerimaanobatdetail_t.po_balance AS qty_outstanding
   FROM purchasereqdetail_t
     JOIN purchasereq_t ON purchasereq_t.purchasereq_id = purchasereqdetail_t.purchasereq_id
     LEFT JOIN validasipoobatdetail_t ON validasipoobatdetail_t.purchasereqdetail_id = purchasereqdetail_t.purchasereqdetail_id
     LEFT JOIN validasipoobat_t ON validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id AND validasipoobat_t.is_deleted = false
     JOIN obatalkes_m ON purchasereqdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN supplier_m ON validasipoobat_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
     LEFT JOIN ( SELECT pen_det.validasipoobatdetail_id,
            pen_det.obatalkes_id,
            pen_det.s_konversiobt_id,
            pen_det.qty_diterima,
            pen_det.jumlah,
            pen_det.discount_rp,
            pen_det.po_balance
           FROM penerimaanobatdetail_t pen_det
             JOIN ( SELECT penerimaanobatdetail_t_1.validasipoobatdetail_id,
                    max(penerimaanobatdetail_t_1.penerimaanobatdetail_id) AS penerimaanobatdetail_id
                   FROM penerimaanobatdetail_t penerimaanobatdetail_t_1
                  GROUP BY penerimaanobatdetail_t_1.validasipoobatdetail_id) max_det ON pen_det.penerimaanobatdetail_id = max_det.penerimaanobatdetail_id
          WHERE pen_det.is_deleted = false) penerimaanobatdetail_t ON validasipoobatdetail_t.validasipoobatdetail_id = penerimaanobatdetail_t.validasipoobatdetail_id
     LEFT JOIN ( SELECT penerimaanobat_t_1.penerimaanobat_id,
            penerimaanobat_t_1.validasipoobat_id,
            penerimaanobat_t_1.tgl_penerimaan,
            penerimaanobat_t_1.no_penerimaan
           FROM penerimaanobat_t penerimaanobat_t_1
             JOIN ( SELECT penerimaanobat_t_2.validasipoobat_id,
                    max(penerimaanobat_t_2.penerimaanobat_id) AS penerimaanobat_id
                   FROM penerimaanobat_t penerimaanobat_t_2
                  GROUP BY penerimaanobat_t_2.validasipoobat_id) max_pen ON penerimaanobat_t_1.penerimaanobat_id = max_pen.penerimaanobat_id) penerimaanobat_t ON validasipoobat_t.validasipoobat_id = penerimaanobat_t.validasipoobat_id
     LEFT JOIN satuankonversi_m ON validasipoobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id
     LEFT JOIN satuanunit_m sat_kecil ON satuankonversi_m.satuankecil_id = sat_kecil.satuanunit_id
     LEFT JOIN satuanunit_m sat_besar ON satuankonversi_m.satuanbesar_id = sat_besar.satuanunit_id
     LEFT JOIN manufaktur_m ON obatalkes_m.manufaktur_id = manufaktur_m.manufaktur_id
  WHERE purchasereq_t.is_deleted = false AND purchasereqdetail_t.is_deleted = false
  ORDER BY (btrim(purchasereq_t.no_pr::text)), (btrim(validasipoobat_t.no_poobat::text)), (btrim(obatalkes_m.obatalkes_nama::text));");

        $this->execute('ALTER TABLE "public"."laporanpenerimaanpopr_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210225_100422_migrate_20210225_laporanpenerimaanpopr_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210225_100422_migrate_20210225_laporanpenerimaanpopr_v cannot be reverted.\n";

        return false;
    }
    */
}

<?php

use yii\db\Migration;

/**
 * Class m210202_035931_migrate_20200202_laporananalisapo_v
 */
class m210202_035931_migrate_20200202_laporananalisapo_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('DROP VIEW if exists "public"."laporananalisapo_v";');
         
         $this->execute("
            CREATE VIEW \"public\".\"laporananalisapo_v\" AS  SELECT validasipoobat_t.validasipoobat_id AS no,
    obatalkes_m.obatalkes_kode AS kode_obat,
    obatalkes_m.obatalkes_nama AS nama_obat,
    manufaktur_m.nama AS manufaktur,
    jenisobatalkes_m.jenisobatalkes_nama AS jenis_obat,
    purchasereq_t.no_pr,
    purchasereq_t.tgl_pr,
    purchasereqdetail_t.qty_input AS qty_pr,
        CASE
            WHEN uom_pr.satuankonversi_id IS NULL THEN NULL::text
            ELSE concat('1 ', uom_pr.uom_besar, ' = ', uom_pr.nilai_konversi, ' ', uom_pr.uom_kecil)
        END AS uom_pr,
    purchasereqdetail_t.catatan,
    validasipoobat_t.no_poobat AS no_po,
    validasipoobat_t.created_date AS tgl_po,
    validasipoobat_t.tgl_validasi AS tgl_po_validasi,
    validasipoobatdetail_t.qty_input AS qty_po,
        CASE
            WHEN uom_po.satuankonversi_id IS NULL THEN NULL::text
            ELSE concat('1 ', uom_po.uom_besar, ' = ', uom_po.nilai_konversi, ' ', uom_po.uom_kecil)
        END AS uom_po,
    validasipoobatdetail_t.harga,
    validasipoobatdetail_t.discount AS disc_persen,
    pajak_m.pajak_persen AS ppn_persen,
    validasipoobatdetail_t.jumlah - validasipoobatdetail_t.discount_rp AS sub_total,
    COALESCE(validasipoobatdetail_t.jumlah - validasipoobatdetail_t.discount_rp +
        CASE
            WHEN COALESCE(validasipoobat_t.ppn_persen::integer, 0) = 0 THEN 0::double precision
            ELSE (validasipoobatdetail_t.jumlah - validasipoobatdetail_t.discount_rp) / (100 / validasipoobat_t.ppn_persen)::double precision
        END, 0::double precision) AS total,
    fgetnamalookup(validasipoobat_t.status_penerimaan) AS status_po,
        CASE
            WHEN validasipoobat_t.status_penerimaan = 575 THEN validasipoobat_t.last_modified_date
            ELSE NULL::timestamp without time zone
        END AS tgl_po_batal,
    validasipoobat_t.catatan AS catatan_batal,
    supplier_m.supplier_kode AS kode_supplier,
    supplier_m.supplier_nama AS nama_supplier,
    penerimaan.tgl AS tgl_penerimaan,
    validasipoobatdetail_t.qty_penerimaan,
        CASE
            WHEN uom_terima.satuankonversi_id IS NULL THEN NULL::text
            ELSE concat('1 ', uom_terima.uom_besar, ' = ', uom_terima.nilai_konversi, ' ', uom_terima.uom_kecil)
        END AS uom_penerimaan,
    validasipoobatdetail_t.qty_sisa AS sisa_penerimaan,
    validasipoobat_t.is_validasi,
    date_part('day'::text, validasipoobat_t.created_date::date) - date_part('day'::text, purchasereq_t.tgl_pr) AS pr_to_po,
        CASE
            WHEN validasipoobat_t.is_validasi = true THEN date_part('day'::text, validasipoobat_t.tgl_validasi::date) - date_part('day'::text, purchasereq_t.tgl_pr)
            ELSE date_part('day'::text, validasipoobat_t.created_date::date) - date_part('day'::text, purchasereq_t.tgl_pr)
        END AS pr_to_povalidasi,
        CASE
            WHEN validasipoobat_t.is_validasi = true THEN date_part('day'::text, validasipoobat_t.tgl_validasi::date) - date_part('day'::text, validasipoobat_t.created_date)
            ELSE date_part('day'::text, validasipoobat_t.created_date::date) - date_part('day'::text, validasipoobat_t.created_date)
        END AS po_to_povalidasi
   FROM validasipoobat_t
     JOIN validasipoobatdetail_t ON validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id
     JOIN purchasereqdetail_t ON validasipoobatdetail_t.purchasereqdetail_id = purchasereqdetail_t.purchasereqdetail_id
     JOIN purchasereq_t ON purchasereqdetail_t.purchasereq_id = purchasereq_t.purchasereq_id
     LEFT JOIN ( SELECT satuankonversi_m_1.satuankonversi_id,
            satuankonversi_m_1.obatalkes_id,
            satuankonversi_m_1.satuankecil_id,
            satuankonversi_m_1.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            satuankonversi_m_1.nilai_konversi
           FROM satuankonversi_m satuankonversi_m_1
             LEFT JOIN satuanunit_m uom_besar ON satuankonversi_m_1.satuanbesar_id = uom_besar.satuanunit_id
             LEFT JOIN satuanunit_m uom_kecil ON satuankonversi_m_1.satuankecil_id = uom_kecil.satuanunit_id
          WHERE satuankonversi_m_1.is_deleted = false AND satuankonversi_m_1.is_active = true
          GROUP BY satuankonversi_m_1.satuankonversi_id, satuankonversi_m_1.obatalkes_id, satuankonversi_m_1.satuankecil_id, satuankonversi_m_1.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, satuankonversi_m_1.nilai_konversi) uom_pr ON purchasereqdetail_t.obatalkes_id = uom_pr.obatalkes_id AND purchasereqdetail_t.satuan_id = uom_pr.satuanbesar_id AND purchasereqdetail_t.satuankonversi_id = uom_pr.satuankecil_id
     LEFT JOIN supplier_m ON validasipoobat_t.supplier_id = supplier_m.supplier_id
     JOIN obatalkes_m ON validasipoobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN satuankonversi_m ON validasipoobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id
     LEFT JOIN satuanunit_m kecil ON satuankonversi_m.satuankecil_id = kecil.satuanunit_id
     LEFT JOIN satuanunit_m besar ON satuankonversi_m.satuanbesar_id = besar.satuanunit_id
     LEFT JOIN manufaktur_m ON obatalkes_m.manufaktur_id = manufaktur_m.manufaktur_id
     LEFT JOIN pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
     LEFT JOIN ( SELECT satuankonversi_m_1.satuankonversi_id,
            satuankonversi_m_1.obatalkes_id,
            satuankonversi_m_1.satuankecil_id,
            satuankonversi_m_1.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            satuankonversi_m_1.nilai_konversi
           FROM satuankonversi_m satuankonversi_m_1
             LEFT JOIN satuanunit_m uom_besar ON satuankonversi_m_1.satuanbesar_id = uom_besar.satuanunit_id
             LEFT JOIN satuanunit_m uom_kecil ON satuankonversi_m_1.satuankecil_id = uom_kecil.satuanunit_id
          WHERE satuankonversi_m_1.is_deleted = false AND satuankonversi_m_1.is_active = true) uom_po ON validasipoobatdetail_t.obatalkes_id = uom_po.obatalkes_id AND validasipoobatdetail_t.s_konversiobt_id = uom_po.satuankonversi_id
     LEFT JOIN ( SELECT penerimaanobatdetail_t.validasipoobatdetail_id,
            max(penerimaanobat_t.tgl_penerimaan) AS tgl,
            penerimaanobatdetail_t.obatalkes_id,
            penerimaanobatdetail_t.s_konversiobt_id
           FROM penerimaanobatdetail_t
             JOIN penerimaanobat_t ON penerimaanobatdetail_t.penerimaanobat_id = penerimaanobat_t.penerimaanobat_id
          GROUP BY penerimaanobatdetail_t.validasipoobatdetail_id, penerimaanobatdetail_t.obatalkes_id, penerimaanobatdetail_t.s_konversiobt_id) penerimaan ON validasipoobatdetail_t.validasipoobatdetail_id = penerimaan.validasipoobatdetail_id
     LEFT JOIN ( SELECT satuankonversi_m_1.satuankonversi_id,
            satuankonversi_m_1.obatalkes_id,
            satuankonversi_m_1.satuankecil_id,
            satuankonversi_m_1.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            satuankonversi_m_1.nilai_konversi
           FROM satuankonversi_m satuankonversi_m_1
             LEFT JOIN satuanunit_m uom_besar ON satuankonversi_m_1.satuanbesar_id = uom_besar.satuanunit_id
             LEFT JOIN satuanunit_m uom_kecil ON satuankonversi_m_1.satuankecil_id = uom_kecil.satuanunit_id
          WHERE satuankonversi_m_1.is_deleted = false AND satuankonversi_m_1.is_active = true) uom_terima ON penerimaan.obatalkes_id = uom_terima.obatalkes_id AND penerimaan.s_konversiobt_id = uom_terima.satuankonversi_id;");
         
         $this->execute('ALTER TABLE "public"."laporananalisapo_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210202_035931_migrate_20200202_laporananalisapo_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210202_035931_migrate_20200202_laporananalisapo_v cannot be reverted.\n";

        return false;
    }
    */
}

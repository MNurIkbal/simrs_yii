<?php

use yii\db\Migration;

/**
 * Class m220423_214141_migrate_MHG775_laporananalisapononmedis_v
 */
class m220423_214141_migrate_MHG775_laporananalisapononmedis_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."lapanalisapononmedis_v";
        ');

        $this->execute("
            CREATE VIEW public.lapanalisapononmedis_v AS   SELECT a.kode_barang,
    a.nama_barang,
    a.no_pr,
    a.tgl_pr,
    a.qty_pr,
    a.uom_pr,
    a.catatan,
    a.no_po,
    a.tgl_po,
    a.tgl_validasi_po,
    a.tgl_batal_po,
    a.catatan_batal_po,
    a.catatan_po,
    a.qty_po,
    a.uom_po,
    a.harga,
    a.diskon,
    a.ppn,
    a.subtotal,
    a.total,
    a.harga_total,
    a.tgl_penerimaan,
    a.qty_penerimaan,
    a.uom_penerimaan,
    a.uom_sisa_penerimaan,
    a.sisa_penerimaan,
    a.kode_supplier,
    a.nama_supplier,
    a.pr_jarak_po,
    a.pr_jarak_tgl_penerimaan,
    a.po_jarak_validasi_po,
    a.po_jarak_tgl_penerimaan,
    a.po_validasi_tgl_penerimaan,
    a.no_penerimaan,
    a.nofaktur_penerimaan,
    a.tgl_verifikasi_penerimaan,
    a.satuan_pr,
    a.satuan_po,
    a.penerimaan,
    a.status_po
   FROM ( SELECT barang_m.barang_kode AS kode_barang,
            barang_m.barang_nama AS nama_barang,
            purchasereqbrg_t.no_pr,
            purchasereqbrg_t.tgl_pr,
            purchasereqbrgdetail_t.qty_input AS qty_pr,
                CASE
                    WHEN uom_pr.satuankonversibrg_id IS NULL THEN NULL::text
                    ELSE concat('1 ', uom_pr.uom_besar, ' = ', uom_pr.nilai_konversi, ' ', uom_pr.uom_kecil)
                END AS uom_pr,
            purchasereqbrgdetail_t.catatan,
            validasipobarang_t.no_pobarang AS no_po,
            validasipobarang_t.created_date AS tgl_po,
            validasipobarang_t.tgl_validasi AS tgl_validasi_po,
                CASE
                    WHEN validasipobarang_t.status_penerimaan = 575 THEN validasipobarang_t.last_modified_date
                    ELSE NULL::timestamp without time zone
                END AS tgl_batal_po,
            validasipobarang_t.catatan1 AS catatan_batal_po,
            validasipobarang_t.catatan2 AS catatan_po,
            validasipobarangdetail_t.qty_input AS qty_po,
                CASE
                    WHEN uom_po.satuankonversibrg_id IS NULL THEN NULL::text
                    ELSE concat('1 ', uom_po.uom_besar, ' = ', uom_po.nilai_konversi, ' ', uom_po.uom_kecil)
                END AS uom_po,
                CASE
                    WHEN validasipobarang_t.status_penerimaan = 575 THEN 0::double precision
                    WHEN barang_m.is_deleted = true OR validasipobarangdetail_t.is_deleted = true THEN 0::double precision
                    ELSE validasipobarangdetail_t.harga
                END AS harga,
                CASE
                    WHEN validasipobarang_t.status_penerimaan = 575 THEN 0::double precision
                    WHEN barang_m.is_deleted = true OR validasipobarangdetail_t.is_deleted = true THEN 0::double precision
                    ELSE validasipobarangdetail_t.discount::double precision
                END AS diskon,
                CASE
                    WHEN validasipobarang_t.status_penerimaan = 575 THEN 0::double precision
                    WHEN barang_m.is_deleted = true OR validasipobarangdetail_t.is_deleted = true THEN 0::double precision
                    ELSE pajak_m.pajak_persen::double precision
                END AS ppn,
                CASE
                    WHEN validasipobarang_t.status_penerimaan = 575 THEN 0::double precision
                    WHEN barang_m.is_deleted = true OR validasipobarangdetail_t.is_deleted = true THEN 0::double precision
                    ELSE validasipobarangdetail_t.harga * validasipobarangdetail_t.qty_input::double precision
                END AS subtotal,
                CASE
                    WHEN validasipobarang_t.status_penerimaan = 575 THEN 0::double precision
                    WHEN barang_m.is_deleted = true OR validasipobarangdetail_t.is_deleted = true THEN 0::double precision
                    ELSE validasipobarangdetail_t.harga * validasipobarangdetail_t.qty_input::double precision - validasipobarangdetail_t.discount_rp + (validasipobarangdetail_t.harga * validasipobarangdetail_t.qty_input::double precision - validasipobarangdetail_t.discount_rp) * pajak_m.pajak_persen::double precision / 100::double precision
                END AS total,
                CASE
                    WHEN validasipobarang_t.status_penerimaan = 575 THEN 0::double precision
                    WHEN barang_m.is_deleted = true OR validasipobarangdetail_t.is_deleted = true THEN 0::double precision
                    ELSE COALESCE(validasipobarangdetail_t.jumlah - validasipobarangdetail_t.discount_rp +
                    CASE
                        WHEN COALESCE(validasipobarang_t.ppn_persen::integer, 0) = 0 THEN 0::double precision
                        ELSE (validasipobarangdetail_t.jumlah - validasipobarangdetail_t.discount_rp) / (100 / validasipobarang_t.ppn_persen)::double precision
                    END, 0::double precision)
                END AS harga_total,
            penerimaan.tgl AS tgl_penerimaan,
            validasipobarangdetail_t.qty_penerimaan,
                CASE
                    WHEN uom_terima.satuankonversibrg_id IS NULL THEN NULL::text
                    ELSE concat('1 ', uom_terima.uom_besar, ' = ', uom_terima.nilai_konversi, ' ', uom_terima.uom_kecil)
                END AS uom_penerimaan,
                CASE
                    WHEN uom_terima.satuankonversibrg_id IS NULL THEN NULL::text
                    ELSE concat('1 ', uom_terima.uom_besar, ' = ', uom_terima.nilai_konversi, ' ', uom_terima.uom_kecil)
                END AS uom_sisa_penerimaan,
            validasipobarangdetail_t.qty_sisa AS sisa_penerimaan,
            supplier_m.supplier_kode AS kode_supplier,
            supplier_m.supplier_nama AS nama_supplier,
            COALESCE(date_part('day'::text, validasipobarang_t.created_date::date) - date_part('day'::text, purchasereqbrg_t.tgl_pr), 0::double precision) AS pr_jarak_po,
                CASE
                    WHEN validasipobarang_t.is_validasi = true THEN COALESCE(date_part('day'::text, validasipobarang_t.tgl_validasi::date) - date_part('day'::text, purchasereqbrg_t.tgl_pr), 0::double precision)
                    ELSE COALESCE(date_part('day'::text, validasipobarang_t.created_date::date) - date_part('day'::text, purchasereqbrg_t.tgl_pr), 0::double precision)
                END AS pr_jarak_tgl_penerimaan,
                CASE
                    WHEN validasipobarang_t.is_validasi = true THEN date_part('day'::text, validasipobarang_t.tgl_validasi::date) - date_part('day'::text, validasipobarang_t.created_date)
                    ELSE date_part('day'::text, validasipobarang_t.created_date::date) - date_part('day'::text, validasipobarang_t.created_date)
                END AS po_jarak_validasi_po,
            0 AS po_jarak_tgl_penerimaan,
            0 AS po_validasi_tgl_penerimaan,
            penerimaan.no_penerimaan,
            penerimaan.no_faktur AS nofaktur_penerimaan,
            penerimaan.tgl AS tgl_verifikasi_penerimaan,
            uom_pr.uom_besar AS satuan_pr,
            uom_po.uom_besar AS satuan_po,
                CASE
                    WHEN uom_terima.satuankonversibrg_id IS NULL THEN NULL::text
                    ELSE concat(uom_terima.uom_besar)
                END AS penerimaan,
                CASE
                    WHEN validasipobarang_t.status_penerimaan = 575 THEN 'Dibatalkan'::text
                    WHEN validasipobarang_t.status_penerimaan = 572 THEN 'Belum Diterima'::text
                    WHEN validasipobarang_t.status_penerimaan = 573 THEN 'Belum Semua Diterima'::text
                    WHEN validasipobarang_t.status_penerimaan = 686 THEN 'Expired'::text
                    WHEN barang_m.is_deleted = true OR validasipobarangdetail_t.is_deleted = true THEN 'Dibatalkan'::text
                    WHEN validasipobarang_t.status_penerimaan = 574 THEN 'Sudah Semua Diterima'::text
                    WHEN validasipobarang_t.status_penerimaan = 580 THEN 'Closing Supplier'::text
                    ELSE NULL::text
                END AS status_po,
                CASE
                    WHEN purchasereqbrg_t.status = 713 THEN 'Sudah PO'::text
                    ELSE NULL::text
                END AS status_pr
           FROM validasipobarang_t
             JOIN validasipobarangdetail_t ON validasipobarang_t.validasipobarang_id = validasipobarangdetail_t.validasipobarang_id
             LEFT JOIN purchasereqbrgdetail_t ON validasipobarangdetail_t.purchasereqbrgdetail_id = purchasereqbrgdetail_t.purchasereqbrgdetail_id
             LEFT JOIN purchasereqbrg_t ON purchasereqbrgdetail_t.purchasereqbrg_id = purchasereqbrg_t.purchasereqbrg_id
             LEFT JOIN ( SELECT satuankonversibrg_m.satuankonversibrg_id,
                    satuankonversibrg_m.barang_id,
                    satuankonversibrg_m.satuankecil_id,
                    satuankonversibrg_m.satuanbesar_id,
                    uom_besar.satuanunit_nama AS uom_besar,
                    uom_kecil.satuanunit_nama AS uom_kecil,
                    satuankonversibrg_m.nilai_konversi
                   FROM satuankonversibrg_m
                     LEFT JOIN satuanunit_m uom_besar ON satuankonversibrg_m.satuanbesar_id = uom_besar.satuanunit_id
                     LEFT JOIN satuanunit_m uom_kecil ON satuankonversibrg_m.satuankecil_id = uom_kecil.satuanunit_id
                  WHERE satuankonversibrg_m.is_deleted = false AND satuankonversibrg_m.is_active = true
                  GROUP BY satuankonversibrg_m.satuankonversibrg_id, satuankonversibrg_m.barang_id, satuankonversibrg_m.satuankecil_id, satuankonversibrg_m.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, satuankonversibrg_m.nilai_konversi) uom_pr ON purchasereqbrgdetail_t.barang_id = uom_pr.barang_id AND purchasereqbrgdetail_t.satuan_id = uom_pr.satuanbesar_id AND purchasereqbrgdetail_t.satuankonversi_id = uom_pr.satuankecil_id
             LEFT JOIN supplier_m ON validasipobarang_t.supplier_id = supplier_m.supplier_id
             JOIN barang_m ON validasipobarangdetail_t.barang_id = barang_m.barang_id
             LEFT JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
             LEFT JOIN satuankonversi_m ON validasipobarangdetail_t.s_konversibrg_id = satuankonversi_m.satuankonversi_id
             LEFT JOIN satuanunit_m kecil ON satuankonversi_m.satuankecil_id = kecil.satuanunit_id
             LEFT JOIN satuanunit_m besar ON satuankonversi_m.satuanbesar_id = besar.satuanunit_id
             LEFT JOIN pajak_m ON validasipobarang_t.pajak_id = pajak_m.pajak_id
             LEFT JOIN ( SELECT satuankonversi_po.satuankonversibrg_id,
                    satuankonversi_po.barang_id,
                    satuankonversi_po.satuankecil_id,
                    satuankonversi_po.satuanbesar_id,
                    uom_besar.satuanunit_nama AS uom_besar,
                    uom_kecil.satuanunit_nama AS uom_kecil,
                    satuankonversi_po.nilai_konversi
                   FROM satuankonversibrg_m satuankonversi_po
                     LEFT JOIN satuanunit_m uom_besar ON satuankonversi_po.satuanbesar_id = uom_besar.satuanunit_id
                     LEFT JOIN satuanunit_m uom_kecil ON satuankonversi_po.satuankecil_id = uom_kecil.satuanunit_id
                  WHERE satuankonversi_po.is_deleted = false AND satuankonversi_po.is_active = true) uom_po ON validasipobarangdetail_t.barang_id = uom_po.barang_id AND validasipobarangdetail_t.s_konversibrg_id = uom_po.satuankonversibrg_id
             LEFT JOIN ( SELECT max_penerimaan.validasipobarang_id,
                    penerimaanbarangdetail_t.validasipobarangdetail_id,
                    penerimaanbarang_t.no_penerimaan,
                    penerimaanbarang_t.no_faktur,
                    penerimaanbarang_t.tgl_penerimaan AS tgl,
                    penerimaanbarangdetail_t.barang_id,
                    penerimaanbarangdetail_t.s_konversibrg_id
                   FROM penerimaanbarang_t
                     JOIN ( SELECT max(penerimaanbarang_t_1.penerimaanbarang_id) AS max_id,
                            penerimaanbarang_t_1.validasipobarang_id
                           FROM penerimaanbarang_t penerimaanbarang_t_1
                          GROUP BY penerimaanbarang_t_1.validasipobarang_id) max_penerimaan ON penerimaanbarang_t.penerimaanbarang_id = max_penerimaan.max_id AND penerimaanbarang_t.validasipobarang_id = max_penerimaan.validasipobarang_id
                     JOIN penerimaanbarangdetail_t ON penerimaanbarang_t.penerimaanbarang_id = penerimaanbarangdetail_t.penerimaanbarang_id) penerimaan ON validasipobarang_t.validasipobarang_id = penerimaan.validasipobarang_id AND validasipobarangdetail_t.barang_id = penerimaan.barang_id
             LEFT JOIN ( SELECT sk_terima.satuankonversibrg_id,
                    sk_terima.barang_id,
                    sk_terima.satuankecil_id,
                    sk_terima.satuanbesar_id,
                    uom_besar.satuanunit_nama AS uom_besar,
                    uom_kecil.satuanunit_nama AS uom_kecil,
                    sk_terima.nilai_konversi
                   FROM satuankonversibrg_m sk_terima
                     LEFT JOIN satuanunit_m uom_besar ON sk_terima.satuanbesar_id = uom_besar.satuanunit_id
                     LEFT JOIN satuanunit_m uom_kecil ON sk_terima.satuankecil_id = uom_kecil.satuanunit_id
                  WHERE sk_terima.is_deleted = false AND sk_terima.is_active = true) uom_terima ON penerimaan.barang_id = uom_terima.barang_id AND penerimaan.s_konversibrg_id = uom_terima.satuankonversibrg_id
          WHERE validasipobarangdetail_t.is_deleted = false AND purchasereqbrgdetail_t.status <> 720
        UNION ALL
         SELECT barang_m.barang_kode AS kode_barang,
            barang_m.barang_nama AS nama_barang,
            purchasereqbrg_t.no_pr,
            purchasereqbrg_t.tgl_pr,
            purchasereqbrgdetail_t.qty_input AS qty_pr,
                CASE
                    WHEN uom_pr.satuankonversibrg_id IS NULL THEN NULL::text
                    ELSE concat('1 ', uom_pr.uom_besar, ' = ', uom_pr.nilai_konversi, ' ', uom_pr.uom_kecil)
                END AS uom_pr,
            purchasereqbrg_t.reference AS catatan,
            NULL::character varying AS no_po,
            NULL::timestamp without time zone AS tgl_po,
            NULL::timestamp without time zone AS tgl_validasi_po,
            NULL::timestamp without time zone AS tgl_batal_po,
            NULL::text AS catatan_batal_po,
            NULL::text AS catatan_po,
            NULL::integer AS qty_po,
            NULL::text AS uom_po,
            NULL::double precision AS harga,
            NULL::double precision AS diskon,
            NULL::double precision AS ppn,
            NULL::double precision AS subtotal,
            NULL::double precision AS total,
            NULL::double precision AS harga_total,
            NULL::timestamp without time zone AS tgl_penerimaan,
            NULL::integer AS qty_penerimaan,
            NULL::text AS uom_penerimaan,
            NULL::text AS uom_sisa_penerimaan,
            NULL::integer AS sisa_penerimaan,
            NULL::character varying AS kode_supplier,
            NULL::character varying AS nama_supplier,
            NULL::double precision AS pr_jarak_po,
            NULL::double precision AS pr_jarak_tgl_penerimaan,
            NULL::double precision AS po_jarak_validasi_po,
            NULL::integer AS po_jarak_tgl_penerimaan,
            NULL::integer AS po_validasi_tgl_penerimaan,
            NULL::character varying AS no_penerimaan,
            NULL::character varying AS nofaktur_penerimaan,
            NULL::timestamp without time zone AS tgl_verifikasi_penerimaan,
            uom_pr.uom_besar AS satuan_pr,
            NULL::character varying AS satuan_po,
            NULL::text AS penerimaan,
            NULL::text AS status_po,
                CASE
                    WHEN purchasereqbrg_t.status = 1056 THEN 'Approved'::text
                    WHEN purchasereqbrg_t.status = 1057 THEN 'Belum Approved'::text
                    WHEN purchasereqbrg_t.status = 712 THEN 'Belum PO'::text
                    ELSE NULL::text
                END AS status_pr
           FROM purchasereqbrgdetail_t
             LEFT JOIN purchasereqbrg_t ON purchasereqbrgdetail_t.purchasereqbrg_id = purchasereqbrg_t.purchasereqbrg_id
             JOIN barang_m ON purchasereqbrgdetail_t.barang_id = barang_m.barang_id
             LEFT JOIN ( SELECT satuankonversibrg_m.satuankonversibrg_id,
                    satuankonversibrg_m.barang_id,
                    satuankonversibrg_m.satuankecil_id,
                    satuankonversibrg_m.satuanbesar_id,
                    uom_besar.satuanunit_nama AS uom_besar,
                    uom_kecil.satuanunit_nama AS uom_kecil,
                    satuankonversibrg_m.nilai_konversi
                   FROM satuankonversibrg_m
                     LEFT JOIN satuanunit_m uom_besar ON satuankonversibrg_m.satuanbesar_id = uom_besar.satuanunit_id
                     LEFT JOIN satuanunit_m uom_kecil ON satuankonversibrg_m.satuankecil_id = uom_kecil.satuanunit_id
                  WHERE satuankonversibrg_m.is_deleted = false AND satuankonversibrg_m.is_active = true
                  GROUP BY satuankonversibrg_m.satuankonversibrg_id, satuankonversibrg_m.barang_id, satuankonversibrg_m.satuankecil_id, satuankonversibrg_m.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, satuankonversibrg_m.nilai_konversi) uom_pr ON purchasereqbrgdetail_t.barang_id = uom_pr.barang_id AND purchasereqbrgdetail_t.satuan_id = uom_pr.satuanbesar_id AND purchasereqbrgdetail_t.satuankonversi_id = uom_pr.satuankecil_id
          WHERE purchasereqbrg_t.is_deleted = false AND purchasereqbrgdetail_t.status <> 720) a ;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220423_214141_migrate_MHG775_laporananalisapononmedis_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220423_214141_migrate_MHG775_laporananalisapononmedis_v cannot be reverted.\n";

        return false;
    }
    */
}

<?php

use yii\db\Migration;

/**
 * Class m210826_042930_migrate_laporan_po
 */
class m210826_042930_migrate_laporan_po extends Migration
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
            WHEN validasipoobat_t.is_validasi IS FALSE THEN validasipoobatdetail_t.qty_input::double precision
            WHEN validasipoobat_t.is_validasi IS TRUE THEN validasipoobatdetail_t.qty_po::double precision / satuankonversi_m.nilai_konversi
            ELSE validasipoobatdetail_t.qty_po::double precision
        END AS qty_po,
    btrim(sat_besar.satuanunit_nama::text) AS satuan_po,
    satuankonversi_m.nilai_konversi,
    COALESCE(validasipoobatdetail_t.qty_penerimaan, 0) - COALESCE(validasipoobatdetail_t.qty_retur, 0) AS qty_terima,
    btrim(sat_besar.satuanunit_nama::text) AS satuan_terima,
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
    COALESCE(validasipoobatdetail_t.qty_sisa, 0) + COALESCE(validasipoobatdetail_t.qty_retur, 0) AS qty_outstanding
   FROM purchasereqdetail_t
     JOIN ( SELECT a.purchasereq_id,
            a.no_pr,
            a.tgl_pr,
            a.is_prcyto
           FROM purchasereq_t a
          WHERE a.is_deleted = false) purchasereq_t ON purchasereq_t.purchasereq_id = purchasereqdetail_t.purchasereq_id
     LEFT JOIN ( SELECT a.purchasereqdetail_id,
            a.validasipoobat_id,
            a.validasipoobatdetail_id,
            a.s_konversiobt_id,
            a.qty_input,
            a.qty_po,
            a.qty_penerimaan,
            a.qty_retur,
            a.harga,
            a.discount,
            a.discount_rp,
            a.qty_sisa
           FROM validasipoobatdetail_t a
          WHERE a.is_deleted = false) validasipoobatdetail_t ON validasipoobatdetail_t.purchasereqdetail_id = purchasereqdetail_t.purchasereqdetail_id
     LEFT JOIN ( SELECT a.validasipoobat_id,
            a.supplier_id,
            a.pajak_id,
            a.no_poobat,
            a.is_validasi,
            a.ppn_persen,
            a.status_penerimaan,
            a.catatan1,
            a.catatan2,
            a.catatan,
            a.tgl_validasi,
            a.created_date
           FROM validasipoobat_t a
          WHERE a.is_deleted = false) validasipoobat_t ON validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id
     JOIN ( SELECT a.obatalkes_id,
            a.manufaktur_id,
            a.obatalkes_nama,
            a.obatalkes_kode
           FROM obatalkes_m a) obatalkes_m ON purchasereqdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT a.supplier_id,
            a.supplier_nama
           FROM supplier_m a) supplier_m ON validasipoobat_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN ( SELECT a.pajak_id,
            a.pajak_persen
           FROM pajak_m a) pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
     LEFT JOIN ( SELECT a.validasipoobatdetail_id,
            a.obatalkes_id,
            a.s_konversiobt_id,
            a.qty_diterima,
            a.jumlah,
            a.discount_rp,
            a.po_balance
           FROM penerimaanobatdetail_t a
             JOIN ( SELECT penerimaanobatdetail_t_1.validasipoobatdetail_id,
                    max(penerimaanobatdetail_t_1.penerimaanobatdetail_id) AS penerimaanobatdetail_id
                   FROM penerimaanobatdetail_t penerimaanobatdetail_t_1
                  GROUP BY penerimaanobatdetail_t_1.validasipoobatdetail_id) max_det ON a.penerimaanobatdetail_id = max_det.penerimaanobatdetail_id
          WHERE a.is_deleted = false) penerimaanobatdetail_t ON validasipoobatdetail_t.validasipoobatdetail_id = penerimaanobatdetail_t.validasipoobatdetail_id
     LEFT JOIN ( SELECT a.penerimaanobat_id,
            a.validasipoobat_id,
            a.tgl_penerimaan,
            a.no_penerimaan
           FROM penerimaanobat_t a
             JOIN ( SELECT a1.validasipoobat_id,
                    max(a1.penerimaanobat_id) AS penerimaanobat_id
                   FROM penerimaanobat_t a1
                  GROUP BY a1.validasipoobat_id) max_pen ON a.penerimaanobat_id = max_pen.penerimaanobat_id) penerimaanobat_t ON validasipoobat_t.validasipoobat_id = penerimaanobat_t.validasipoobat_id
     LEFT JOIN ( SELECT a.satuankonversi_id,
            a.satuanbesar_id,
            a.satuankecil_id,
            a.nilai_konversi
           FROM satuankonversi_m a) satuankonversi_m ON validasipoobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) sat_kecil ON satuankonversi_m.satuankecil_id = sat_kecil.satuanunit_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) sat_besar ON satuankonversi_m.satuanbesar_id = sat_besar.satuanunit_id
     LEFT JOIN ( SELECT a.manufaktur_id,
            a.nama
           FROM manufaktur_m a) manufaktur_m ON obatalkes_m.manufaktur_id = manufaktur_m.manufaktur_id
  WHERE purchasereqdetail_t.is_deleted = false
  ORDER BY (btrim(purchasereq_t.no_pr::text)), (btrim(validasipoobat_t.no_poobat::text)), (btrim(obatalkes_m.obatalkes_nama::text));");

        $this->execute('DROP VIEW if exists "public"."laporanallpo_v";');

        $this->execute("
            CREATE VIEW \"public\".\"laporanallpo_v\" AS  SELECT 'OBAT'::text AS type,
    purchasereq_t.no_pr,
    purchasereq_t.tgl_pr AS tanggal_pr,
    purchasereq_t.tgl_pr AS tanggal_verifikasi_pr,
    validasipoobat_t.created_date AS tanggal_po,
        CASE validasipoobat_t.is_validasi
            WHEN true THEN validasipoobat_t.tgl_validasi
            ELSE NULL::timestamp without time zone
        END AS tgl_verifikasi_po,
    btrim(validasipoobat_t.no_poobat::text) AS no_po,
    supplier_m.supplier_kode AS supplier_code,
    btrim(supplier_m.supplier_nama::text) AS supplier_name,
    manufaktur_m.nama AS manufacturer,
    obatalkes_m.obatalkes_kode AS item_code,
    obatalkes_m.obatalkes_nama AS item_name,
    validasipoobatdetail_t.qty_input::double precision AS qty_po,
    COALESCE(validasipoobatdetail_t.qty_penerimaan, 0) - COALESCE(validasipoobatdetail_t.qty_retur, 0) AS po_balance,
    COALESCE(validasipoobatdetail_t.qty_sisa, 0) + COALESCE(validasipoobatdetail_t.qty_retur, 0) AS qty_outstanding,
    btrim(sat_besar.satuanunit_nama::text) AS uom,
    btrim(sat_besar.satuanunit_nama::text) AS from_uom,
    satuankonversi_m.nilai_konversi AS factor,
    btrim(sat_kecil.satuanunit_nama::text) AS to_uom,
    validasipoobatdetail_t.harga AS price,
    validasipoobatdetail_t.discount AS deduction_percent,
    pajak_m.pajak_persen AS addition_percent,
    validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp AS gross_amount,
    validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp + (validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp) * validasipoobat_t.ppn_persen::double precision / 100::double precision AS nett_amount,
    btrim(fgetnamalookup(validasipoobat_t.status_penerimaan)::text) AS status_po,
    btrim(validasipoobat_t.catatan1) AS catatan_1,
    btrim(validasipoobat_t.catatan2) AS catatan_2,
    purchasereq_t.is_prcyto AS cyto,
        CASE purchasereq_t.is_prcyto
            WHEN true THEN 'CITO'::text
            ELSE validasipoobat_t.catatan1
        END AS remarks,
        CASE
            WHEN validasipoobat_t.catatan IS NOT NULL THEN validasipoobat_t.last_modified_date
            ELSE NULL::timestamp without time zone
        END AS reject_date,
    btrim(validasipoobat_t.catatan) AS reject_remarks,
    btrim(penerimaanobat_t.no_penerimaan::text) AS no_penerimaan,
    penerimaanobat_t.tgl_penerimaan AS tanggal_penerimaan,
    purchasereq_t.is_prcyto AS jenis_pr,
    fgetnamalookup(purchasereqdetail_t.status::integer) AS status_pr,
    validasipoobatdetail_t.discount_rp AS deduction_rupiah
   FROM validasipoobat_t
     JOIN ( SELECT a.validasipoobat_id,
            a.purchasereqdetail_id,
            a.obatalkes_id,
            a.validasipoobatdetail_id,
            a.s_konversiobt_id,
            a.qty_input,
            a.qty_penerimaan,
            a.qty_retur,
            a.qty_sisa,
            a.harga,
            a.discount,
            a.discount_rp
           FROM validasipoobatdetail_t a
          WHERE a.is_deleted = false) validasipoobatdetail_t ON validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id
     LEFT JOIN ( SELECT a.purchasereqdetail_id,
            a.purchasereq_id,
            a.status
           FROM purchasereqdetail_t a) purchasereqdetail_t ON purchasereqdetail_t.purchasereqdetail_id = validasipoobatdetail_t.purchasereqdetail_id
     LEFT JOIN ( SELECT a.purchasereq_id,
            a.no_pr,
            a.tgl_pr,
            a.is_prcyto
           FROM purchasereq_t a) purchasereq_t ON purchasereq_t.purchasereq_id = purchasereqdetail_t.purchasereq_id
     JOIN ( SELECT a.obatalkes_id,
            a.manufaktur_id,
            a.obatalkes_kode,
            a.obatalkes_nama
           FROM obatalkes_m a) obatalkes_m ON validasipoobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT a.supplier_id,
            a.supplier_nama,
            a.supplier_kode
           FROM supplier_m a) supplier_m ON validasipoobat_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN ( SELECT a.pajak_id,
            a.pajak_persen
           FROM pajak_m a) pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
     LEFT JOIN ( SELECT a.validasipoobatdetail_id,
            a.obatalkes_id,
            a.s_konversiobt_id,
            a.qty_diterima,
            a.jumlah,
            a.discount_rp,
            a.po_balance
           FROM penerimaanobatdetail_t a
             JOIN ( SELECT a1.validasipoobatdetail_id,
                    max(a1.penerimaanobatdetail_id) AS penerimaanobatdetail_id
                   FROM penerimaanobatdetail_t a1
                  GROUP BY a1.validasipoobatdetail_id) max_det ON a.penerimaanobatdetail_id = max_det.penerimaanobatdetail_id
          WHERE a.is_deleted = false) penerimaanobatdetail_t ON validasipoobatdetail_t.validasipoobatdetail_id = penerimaanobatdetail_t.validasipoobatdetail_id
     LEFT JOIN ( SELECT a.penerimaanobat_id,
            a.validasipoobat_id,
            a.tgl_penerimaan,
            a.no_penerimaan
           FROM penerimaanobat_t a
             JOIN ( SELECT a1.validasipoobat_id,
                    max(a1.penerimaanobat_id) AS penerimaanobat_id
                   FROM penerimaanobat_t a1
                  GROUP BY a1.validasipoobat_id) max_pen ON a.penerimaanobat_id = max_pen.penerimaanobat_id) penerimaanobat_t ON validasipoobat_t.validasipoobat_id = penerimaanobat_t.validasipoobat_id
     LEFT JOIN ( SELECT a.satuankonversi_id,
            a.satuanbesar_id,
            a.satuankecil_id,
            a.nilai_konversi
           FROM satuankonversi_m a) satuankonversi_m ON validasipoobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) sat_kecil ON satuankonversi_m.satuankecil_id = sat_kecil.satuanunit_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) sat_besar ON satuankonversi_m.satuanbesar_id = sat_besar.satuanunit_id
     LEFT JOIN ( SELECT a.manufaktur_id,
            a.nama
           FROM manufaktur_m a) manufaktur_m ON obatalkes_m.manufaktur_id = manufaktur_m.manufaktur_id
UNION ALL
 SELECT 'BARANG'::text AS type,
    purchasereqbrg_t.no_pr,
    purchasereqbrg_t.tgl_pr AS tanggal_pr,
    purchasereqbrg_t.tgl_pr AS tanggal_verifikasi_pr,
    validasipobarang_t.created_date AS tanggal_po,
        CASE validasipobarang_t.is_validasi
            WHEN true THEN validasipobarang_t.tgl_validasi
            ELSE NULL::timestamp without time zone
        END AS tgl_verifikasi_po,
    btrim(validasipobarang_t.no_pobarang::text) AS no_po,
    supplier_m.supplier_kode AS supplier_code,
    btrim(supplier_m.supplier_nama::text) AS supplier_name,
    manufaktur_m.nama AS manufacturer,
    barang_m.barang_kode::character varying(100) AS item_code,
    barang_m.barang_nama::character varying(255) AS item_name,
    validasipobarangdetail_t.qty_input::double precision AS qty_po,
    COALESCE(validasipobarangdetail_t.qty_penerimaan, 0) - COALESCE(returdetailjumlah.qty_retur::integer, 0) AS po_balance,
    COALESCE(validasipobarangdetail_t.qty_sisa, 0) + COALESCE(returdetailjumlah.qty_retur::integer, 0) AS qty_outstanding,
    btrim(sat_besar.satuanunit_nama::text) AS uom,
    btrim(sat_besar.satuanunit_nama::text) AS from_uom,
    satuankonversibrg_m.nilai_konversi AS factor,
    btrim(sat_kecil.satuanunit_nama::text) AS to_uom,
    validasipobarangdetail_t.harga AS price,
    validasipobarangdetail_t.discount AS deduction_percent,
    pajak_m.pajak_persen AS addition_percent,
    validasipobarangdetail_t.harga * validasipobarangdetail_t.qty_input::double precision - validasipobarangdetail_t.discount_rp AS gross_amount,
    validasipobarangdetail_t.harga * validasipobarangdetail_t.qty_input::double precision - validasipobarangdetail_t.discount_rp + (validasipobarangdetail_t.harga * validasipobarangdetail_t.qty_input::double precision - validasipobarangdetail_t.discount_rp) * validasipobarang_t.ppn_persen::double precision / 100::double precision AS nett_amount,
    btrim(fgetnamalookup(validasipobarang_t.status_penerimaan)::text) AS status_po,
    btrim(validasipobarang_t.catatan1) AS catatan_1,
    btrim(validasipobarang_t.catatan2) AS catatan_2,
    purchasereqbrg_t.is_prcyto AS cyto,
        CASE purchasereqbrg_t.is_prcyto
            WHEN true THEN 'CITO'::text
            ELSE validasipobarang_t.catatan1
        END AS remarks,
        CASE
            WHEN validasipobarang_t.catatan IS NOT NULL THEN validasipobarang_t.last_modified_date
            ELSE NULL::timestamp without time zone
        END AS reject_date,
    btrim(validasipobarang_t.catatan) AS reject_remarks,
    btrim(penerimaanbarang_t.no_penerimaan::text) AS no_penerimaan,
    penerimaanbarang_t.tgl_penerimaan AS tanggal_penerimaan,
    purchasereqbrg_t.is_prcyto AS jenis_pr,
    fgetnamalookup(purchasereqbrgdetail_t.status::integer) AS status_pr,
    validasipobarangdetail_t.discount_rp AS deduction_rupiah
   FROM validasipobarangdetail_t
     JOIN ( SELECT a.validasipobarang_id,
            a.supplier_id,
            a.pajak_id,
            a.created_date,
            a.is_validasi,
            a.tgl_validasi,
            a.no_pobarang,
            a.ppn_persen,
            a.status_penerimaan,
            a.catatan1,
            a.catatan2,
            a.catatan,
            a.last_modified_date
           FROM validasipobarang_t a) validasipobarang_t ON validasipobarang_t.validasipobarang_id = validasipobarangdetail_t.validasipobarang_id
     LEFT JOIN ( SELECT a.purchasereqbrgdetail_id,
            a.purchasereqbrg_id,
            a.status
           FROM purchasereqbrgdetail_t a) purchasereqbrgdetail_t ON purchasereqbrgdetail_t.purchasereqbrgdetail_id = validasipobarangdetail_t.purchasereqbrgdetail_id
     LEFT JOIN ( SELECT a.purchasereqbrg_id,
            a.no_pr,
            a.tgl_pr,
            a.is_prcyto
           FROM purchasereqbrg_t a) purchasereqbrg_t ON purchasereqbrg_t.purchasereqbrg_id = purchasereqbrgdetail_t.purchasereqbrg_id
     JOIN ( SELECT a.barang_id,
            a.barang_nama,
            a.manufaktur_id,
            a.barang_kode
           FROM barang_m a) barang_m ON validasipobarangdetail_t.barang_id = barang_m.barang_id
     LEFT JOIN ( SELECT a.supplier_id,
            a.supplier_kode,
            a.supplier_nama
           FROM supplier_m a) supplier_m ON validasipobarang_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN ( SELECT a.pajak_id,
            a.pajak_persen
           FROM pajak_m a) pajak_m ON validasipobarang_t.pajak_id = pajak_m.pajak_id
     LEFT JOIN ( SELECT a.validasipobarangdetail_id,
            a.penerimaanbarangdetail_id,
            a.barang_id,
            a.s_konversibrg_id,
            a.qty_diterima,
            a.jumlah,
            a.discount_rp,
            a.po_balance
           FROM penerimaanbarangdetail_t a
             JOIN ( SELECT a1.validasipobarangdetail_id,
                    max(a1.penerimaanbarangdetail_id) AS penerimaanbarangdetail_id
                   FROM penerimaanbarangdetail_t a1
                  GROUP BY a1.validasipobarangdetail_id) max_det ON a.penerimaanbarangdetail_id = max_det.penerimaanbarangdetail_id
          WHERE a.is_deleted = false) penerimaanbarangdetail_t ON validasipobarangdetail_t.validasipobarangdetail_id = penerimaanbarangdetail_t.validasipobarangdetail_id
     LEFT JOIN ( SELECT a.penerimaanbarang_id,
            a.validasipobarang_id,
            a.tgl_penerimaan,
            a.no_penerimaan
           FROM penerimaanbarang_t a
             JOIN ( SELECT a1.validasipobarang_id,
                    max(a1.penerimaanbarang_id) AS penerimaanbarang_id
                   FROM penerimaanbarang_t a1
                  GROUP BY a1.validasipobarang_id) max_pen ON a.penerimaanbarang_id = max_pen.penerimaanbarang_id) penerimaanbarang_t ON validasipobarang_t.validasipobarang_id = penerimaanbarang_t.validasipobarang_id
     LEFT JOIN ( SELECT a.satuankonversibrg_id,
            a.satuanbesar_id,
            a.satuankecil_id,
            a.nilai_konversi
           FROM satuankonversibrg_m a) satuankonversibrg_m ON validasipobarangdetail_t.s_konversibrg_id = satuankonversibrg_m.satuankonversibrg_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) sat_kecil ON satuankonversibrg_m.satuankecil_id = sat_kecil.satuanunit_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) sat_besar ON satuankonversibrg_m.satuanbesar_id = sat_besar.satuanunit_id
     LEFT JOIN ( SELECT a.manufaktur_id,
            a.nama
           FROM manufaktur_m a) manufaktur_m ON barang_m.manufaktur_id = manufaktur_m.manufaktur_id
     LEFT JOIN ( SELECT penerimaanbarang_detail.validasipobarangdetail_id,
            sum(a.qty_input) AS qty_retur
           FROM returpenerimaanbarangdetail_t a
             LEFT JOIN ( SELECT a1.penerimaanbarangdetail_id,
                    a1.validasipobarangdetail_id
                   FROM penerimaanbarangdetail_t a1) penerimaanbarang_detail ON penerimaanbarang_detail.penerimaanbarangdetail_id = a.penerimaanbarangdetail_id
          GROUP BY penerimaanbarang_detail.validasipobarangdetail_id) returdetailjumlah ON validasipobarangdetail_t.validasipobarangdetail_id = returdetailjumlah.validasipobarangdetail_id
  WHERE validasipobarangdetail_t.is_deleted = false;");

        $this->execute('DROP VIEW if exists "public"."laporanpooutstanding_v";');

        $this->execute("
            CREATE VIEW \"public\".\"laporanpooutstanding_v\" AS  SELECT pr.no_pr,
    validasipoobat_t.no_poobat AS no_po,
    validasipoobat_t.created_date AS tgl_po_dibuat,
    validasipoobat_t.tgl_validasi,
    supplier_m.supplier_kode,
    supplier_m.supplier_nama,
    obatalkes_m.obatalkes_kode AS kode_item,
    obatalkes_m.obatalkes_nama AS nama_item,
    validasipoobatdetail_t.qty_input AS qty_po,
    kecil.satuanunit_nama AS satuan_kecil,
    besar.satuanunit_nama AS satuan_besar,
    validasipoobatdetail_t.qty_po AS qty,
    COALESCE(validasipoobatdetail_t.qty_penerimaan, 0) - COALESCE(validasipoobatdetail_t.qty_retur, 0) AS qty_penerimaan,
    validasipoobatdetail_t.harga,
    validasipoobatdetail_t.discount,
    validasipoobatdetail_t.discount_rp,
    pajak_m.pajak_persen AS ppn_persen,
    validasipoobat_t.ppn_nilai,
    validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp AS sub_total,
    validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp + (validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp) * validasipoobat_t.ppn_persen::double precision / 100::double precision AS total,
    validasipoobat_t.catatan1,
    validasipoobat_t.catatan2,
    concat('1 ', besar.satuanunit_nama, ' - ', satuankonversi_m.nilai_konversi, ' ', kecil.satuanunit_nama) AS uom,
    fgetnamalookup(validasipoobat_t.status_penerimaan) AS status,
    pr.tgl_pr,
    COALESCE(validasipoobatdetail_t.qty_sisa, 0) + COALESCE(validasipoobatdetail_t.qty_retur, 0) AS po_balance,
    jenisobatalkes_m.jenisobatalkes_nama,
    manufaktur_m.nama,
    manufaktur_m.nama AS manufaktur_nama
   FROM validasipoobat_t
     JOIN ( SELECT a.validasipoobat_id,
            a.validasipoobatdetail_id,
            a.obatalkes_id,
            a.s_konversiobt_id,
            a.qty_input,
            a.qty_po,
            a.qty_penerimaan,
            a.qty_retur,
            a.harga,
            a.discount,
            a.discount_rp,
            a.qty_sisa
           FROM validasipoobatdetail_t a
          WHERE a.is_deleted = false) validasipoobatdetail_t ON validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id
     JOIN ( SELECT a.pajak_id,
            a.pajak_persen
           FROM pajak_m a) pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
     LEFT JOIN ( SELECT a.validasipoobatdetail_id,
            a.penerimaanobatdetail_id,
            a.po_balance
           FROM penerimaanobatdetail_t a
             JOIN ( SELECT a1.validasipoobatdetail_id,
                    max(a1.penerimaanobatdetail_id) AS penerimaanobatdetail_id
                   FROM penerimaanobatdetail_t a1
                  GROUP BY a1.validasipoobatdetail_id) max_det ON a.penerimaanobatdetail_id = max_det.penerimaanobatdetail_id
          WHERE a.is_deleted = false) penerimaanobatdetail_t ON validasipoobatdetail_t.validasipoobatdetail_id = penerimaanobatdetail_t.validasipoobatdetail_id
     LEFT JOIN ( SELECT a.supplier_id,
            a.supplier_kode,
            a.supplier_nama
           FROM supplier_m a) supplier_m ON validasipoobat_t.supplier_id = supplier_m.supplier_id
     JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_kode,
            a.obatalkes_nama,
            a.jenisobatalkes_id,
            a.manufaktur_id
           FROM obatalkes_m a) obatalkes_m ON validasipoobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT a.jenisobatalkes_id,
            a.jenisobatalkes_nama
           FROM jenisobatalkes_m a) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN ( SELECT a.manufaktur_id,
            a.nama
           FROM manufaktur_m a) manufaktur_m ON obatalkes_m.manufaktur_id = manufaktur_m.manufaktur_id
     LEFT JOIN ( SELECT a.satuankonversi_id,
            a.satuankecil_id,
            a.satuanbesar_id,
            a.nilai_konversi
           FROM satuankonversi_m a) satuankonversi_m ON validasipoobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) kecil ON satuankonversi_m.satuankecil_id = kecil.satuanunit_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) besar ON satuankonversi_m.satuanbesar_id = besar.satuanunit_id
     LEFT JOIN ( SELECT a.no_pr,
            a.tgl_pr::timestamp without time zone AS tgl_pr,
            validasipoobatdetail_t_1.validasipoobat_id,
            purchasereqdetail_t.qty_input,
            kecil_1.satuanunit_nama AS satuan,
            besar_1.satuanunit_nama AS satuan_konversi,
            purchasereqdetail_t.obatalkes_id
           FROM purchasereq_t a
             JOIN ( SELECT a1.purchasereq_id,
                    a1.purchasereqdetail_id,
                    a1.satuan_id,
                    a1.satuankonversi_id,
                    a1.qty_input,
                    a1.obatalkes_id
                   FROM purchasereqdetail_t a1) purchasereqdetail_t ON a.purchasereq_id = purchasereqdetail_t.purchasereq_id
             JOIN ( SELECT a1.purchasereqdetail_id,
                    a1.validasipoobat_id
                   FROM validasipoobatdetail_t a1) validasipoobatdetail_t_1 ON purchasereqdetail_t.purchasereqdetail_id = validasipoobatdetail_t_1.purchasereqdetail_id
             LEFT JOIN ( SELECT a1.satuanunit_id,
                    a1.satuanunit_nama
                   FROM satuanunit_m a1) kecil_1 ON purchasereqdetail_t.satuan_id = kecil_1.satuanunit_id
             LEFT JOIN ( SELECT a1.satuanunit_id,
                    a1.satuanunit_nama
                   FROM satuanunit_m a1) besar_1 ON purchasereqdetail_t.satuankonversi_id = besar_1.satuanunit_id
          WHERE a.is_deleted = false
          GROUP BY a.no_pr, a.tgl_pr, validasipoobatdetail_t_1.validasipoobat_id, purchasereqdetail_t.qty_input, kecil_1.satuanunit_nama, besar_1.satuanunit_nama, purchasereqdetail_t.obatalkes_id) pr ON validasipoobat_t.validasipoobat_id = pr.validasipoobat_id AND validasipoobatdetail_t.obatalkes_id = pr.obatalkes_id
  WHERE (validasipoobat_t.status_penerimaan <> ALL (ARRAY[574, 580])) AND validasipoobat_t.is_validasi = true;");

        $this->execute('DROP VIEW if exists "public"."laporananalisapo_v";');

        $this->execute("CREATE VIEW \"public\".\"laporananalisapo_v\" AS  SELECT validasipoobat_t.validasipoobat_id AS no,
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
    COALESCE(date_part('day'::text, validasipoobat_t.created_date::date) - date_part('day'::text, purchasereq_t.tgl_pr), 0::double precision) AS pr_to_po,
        CASE
            WHEN validasipoobat_t.is_validasi = true THEN COALESCE(date_part('day'::text, validasipoobat_t.tgl_validasi::date) - date_part('day'::text, purchasereq_t.tgl_pr), 0::double precision)
            ELSE COALESCE(date_part('day'::text, validasipoobat_t.created_date::date) - date_part('day'::text, purchasereq_t.tgl_pr), 0::double precision)
        END AS pr_to_povalidasi,
        CASE
            WHEN validasipoobat_t.is_validasi = true THEN COALESCE(date_part('day'::text, validasipoobat_t.tgl_validasi::date) - date_part('day'::text, validasipoobat_t.created_date), 0::double precision)
            ELSE COALESCE(date_part('day'::text, validasipoobat_t.created_date::date) - date_part('day'::text, validasipoobat_t.created_date), 0::double precision)
        END AS po_to_povalidasi,
    COALESCE(date_part('day'::text, penerimaan.tgl::date) - date_part('day'::text, purchasereq_t.tgl_pr), 0::double precision) AS pr_to_penerimaan,
        CASE
            WHEN validasipoobat_t.is_validasi = true THEN COALESCE(date_part('day'::text, penerimaan.tgl::date) - date_part('day'::text, validasipoobat_t.tgl_validasi), 0::double precision)
            ELSE COALESCE(date_part('day'::text, penerimaan.tgl::date) - date_part('day'::text, validasipoobat_t.created_date), 0::double precision)
        END AS povalidasi_to_penerimaan
   FROM validasipoobat_t
     JOIN ( SELECT a.validasipoobat_id,
            a.purchasereqdetail_id,
            a.obatalkes_id,
            a.s_konversiobt_id,
            a.validasipoobatdetail_id,
            a.qty_input,
            a.harga,
            a.discount,
            a.jumlah,
            a.discount_rp,
            a.qty_penerimaan,
            a.qty_sisa
           FROM validasipoobatdetail_t a
          WHERE a.is_deleted = false) validasipoobatdetail_t ON validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id
     JOIN ( SELECT a.purchasereqdetail_id,
            a.purchasereq_id,
            a.obatalkes_id,
            a.satuan_id,
            a.satuankonversi_id,
            a.qty_konversi,
            a.qty_input,
            a.catatan
           FROM purchasereqdetail_t a) purchasereqdetail_t ON validasipoobatdetail_t.purchasereqdetail_id = purchasereqdetail_t.purchasereqdetail_id
     JOIN ( SELECT a.purchasereq_id,
            a.no_pr,
            a.tgl_pr
           FROM purchasereq_t a) purchasereq_t ON purchasereqdetail_t.purchasereq_id = purchasereq_t.purchasereq_id
     LEFT JOIN ( SELECT a.satuankonversi_id,
            a.obatalkes_id,
            a.satuankecil_id,
            a.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            a.nilai_konversi
           FROM satuankonversi_m a
             LEFT JOIN ( SELECT a1.satuanunit_id,
                    a1.satuanunit_nama
                   FROM satuanunit_m a1) uom_besar ON a.satuanbesar_id = uom_besar.satuanunit_id
             LEFT JOIN ( SELECT a1.satuanunit_id,
                    a1.satuanunit_nama
                   FROM satuanunit_m a1) uom_kecil ON a.satuankecil_id = uom_kecil.satuanunit_id
          WHERE a.is_deleted = false AND a.is_active = true
          GROUP BY a.satuankonversi_id, a.obatalkes_id, a.satuankecil_id, a.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, a.nilai_konversi) uom_pr ON purchasereqdetail_t.obatalkes_id = uom_pr.obatalkes_id AND purchasereqdetail_t.satuan_id = uom_pr.satuanbesar_id AND purchasereqdetail_t.satuankonversi_id = uom_pr.satuankecil_id
     LEFT JOIN ( SELECT a.supplier_id,
            a.supplier_kode,
            a.supplier_nama
           FROM supplier_m a) supplier_m ON validasipoobat_t.supplier_id = supplier_m.supplier_id
     JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_kode,
            a.jenisobatalkes_id,
            a.manufaktur_id,
            a.obatalkes_nama
           FROM obatalkes_m a) obatalkes_m ON validasipoobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN ( SELECT a.jenisobatalkes_id,
            a.jenisobatalkes_nama
           FROM jenisobatalkes_m a) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
     LEFT JOIN ( SELECT a.satuankonversi_id,
            a.satuankecil_id,
            a.satuanbesar_id
           FROM satuankonversi_m a) satuankonversi_m ON validasipoobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) kecil ON satuankonversi_m.satuankecil_id = kecil.satuanunit_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) besar ON satuankonversi_m.satuanbesar_id = besar.satuanunit_id
     LEFT JOIN ( SELECT a.manufaktur_id,
            a.nama
           FROM manufaktur_m a) manufaktur_m ON obatalkes_m.manufaktur_id = manufaktur_m.manufaktur_id
     LEFT JOIN ( SELECT a.pajak_id,
            a.pajak_persen
           FROM pajak_m a) pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
     LEFT JOIN ( SELECT a.satuankonversi_id,
            a.obatalkes_id,
            a.satuankecil_id,
            a.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            a.nilai_konversi
           FROM satuankonversi_m a
             LEFT JOIN ( SELECT a1.satuanunit_id,
                    a1.satuanunit_nama
                   FROM satuanunit_m a1) uom_besar ON a.satuanbesar_id = uom_besar.satuanunit_id
             LEFT JOIN ( SELECT a1.satuanunit_id,
                    a1.satuanunit_nama
                   FROM satuanunit_m a1) uom_kecil ON a.satuankecil_id = uom_kecil.satuanunit_id
          WHERE a.is_deleted = false AND a.is_active = true) uom_po ON validasipoobatdetail_t.obatalkes_id = uom_po.obatalkes_id AND validasipoobatdetail_t.s_konversiobt_id = uom_po.satuankonversi_id
     LEFT JOIN ( SELECT a.validasipoobatdetail_id,
            max(penerimaanobat_t.tgl_penerimaan) AS tgl,
            a.obatalkes_id,
            a.s_konversiobt_id
           FROM penerimaanobatdetail_t a
             JOIN ( SELECT a1.penerimaanobat_id,
                    a1.tgl_penerimaan
                   FROM penerimaanobat_t a1) penerimaanobat_t ON a.penerimaanobat_id = penerimaanobat_t.penerimaanobat_id
          GROUP BY a.validasipoobatdetail_id, a.obatalkes_id, a.s_konversiobt_id) penerimaan ON validasipoobatdetail_t.validasipoobatdetail_id = penerimaan.validasipoobatdetail_id
     LEFT JOIN ( SELECT a.satuankonversi_id,
            a.obatalkes_id,
            a.satuankecil_id,
            a.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            a.nilai_konversi
           FROM satuankonversi_m a
             LEFT JOIN ( SELECT a1.satuanunit_id,
                    a1.satuanunit_nama
                   FROM satuanunit_m a1) uom_besar ON a.satuanbesar_id = uom_besar.satuanunit_id
             LEFT JOIN ( SELECT a1.satuanunit_id,
                    a1.satuanunit_nama
                   FROM satuanunit_m a1) uom_kecil ON a.satuankecil_id = uom_kecil.satuanunit_id
          WHERE a.is_deleted = false AND a.is_active = true) uom_terima ON penerimaan.obatalkes_id = uom_terima.obatalkes_id AND penerimaan.s_konversiobt_id = uom_terima.satuankonversi_id;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210826_042930_migrate_laporan_po cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210826_042930_migrate_laporan_po cannot be reverted.\n";

        return false;
    }
    */
}

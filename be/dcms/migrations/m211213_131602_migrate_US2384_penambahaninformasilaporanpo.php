<?php

use yii\db\Migration;

/**
 * Class m211213_131602_migrate_US2384_penambahaninformasilaporanpo
 */
class m211213_131602_migrate_US2384_penambahaninformasilaporanpo extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."validasipoobat_t" 
            ADD COLUMN IF NOT EXISTS "tgl_batal_po" timestamp(0);
        ');

        $this->execute('ALTER TABLE "public"."validasipobarang_t" 
            ADD COLUMN IF NOT EXISTS "tgl_batal_po" timestamp(0);
        ');

        $this->execute('DROP VIEW if exists public.laporanallpo_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporanallpo_v\" AS
            SELECT 'OBAT'::text AS type,
            purchasereq_t.no_pr,
            purchasereq_t.tgl_pr AS tanggal_pr,
            purchasereq_t.tgl_pr AS tanggal_verifikasi_pr,
            validasipoobat_t.created_date AS tanggal_po,
            CASE validasipoobat_t.is_validasi
            WHEN true THEN validasipoobat_t.tgl_validasi
            ELSE NULL::timestamp without time zone
            END AS tgl_verifikasi_po,
            btrim((validasipoobat_t.no_poobat)::text) AS no_po,
            supplier_m.supplier_kode AS supplier_code,
            btrim((supplier_m.supplier_nama)::text) AS supplier_name,
            manufaktur_m.nama AS manufacturer,
            obatalkes_m.obatalkes_kode AS item_code,
            obatalkes_m.obatalkes_nama AS item_name,
            (validasipoobatdetail_t.qty_input)::double precision AS qty_po,
            (COALESCE(validasipoobatdetail_t.qty_penerimaan, 0) - COALESCE(validasipoobatdetail_t.qty_retur, 0)) AS po_balance,
            (COALESCE(validasipoobatdetail_t.qty_sisa, 0) + COALESCE(validasipoobatdetail_t.qty_retur, 0)) AS qty_outstanding,
            btrim((sat_besar.satuanunit_nama)::text) AS uom,
            btrim((sat_besar.satuanunit_nama)::text) AS from_uom,
            satuankonversi_m.nilai_konversi AS factor,
            btrim((sat_kecil.satuanunit_nama)::text) AS to_uom,
            validasipoobatdetail_t.harga AS price,
            validasipoobatdetail_t.discount AS deduction_percent,
            pajak_m.pajak_persen AS addition_percent,
            ((validasipoobatdetail_t.harga * (validasipoobatdetail_t.qty_input)::double precision) - validasipoobatdetail_t.discount_rp) AS gross_amount,
            (((validasipoobatdetail_t.harga * (validasipoobatdetail_t.qty_input)::double precision) - validasipoobatdetail_t.discount_rp) + ((((validasipoobatdetail_t.harga * (validasipoobatdetail_t.qty_input)::double precision) - validasipoobatdetail_t.discount_rp) * (validasipoobat_t.ppn_persen)::double precision) / (100)::double precision)) AS nett_amount,
            CASE
            WHEN ((obatalkes_m.is_deleted = true) OR (validasipoobatdetail_t.is_deleted = true)) THEN 'Dibatalkan'::text
            WHEN ((COALESCE(validasipoobatdetail_t.qty_penerimaan, 0) - COALESCE(validasipoobatdetail_t.qty_retur, 0)) = 0) THEN 'Belum Diterima'::text
            WHEN ((COALESCE(validasipoobatdetail_t.qty_penerimaan, 0) - COALESCE(validasipoobatdetail_t.qty_retur, 0)) < validasipoobatdetail_t.qty_input) THEN 'Belum Semua Diterima'::text
            WHEN ((COALESCE(validasipoobatdetail_t.qty_penerimaan, 0) - COALESCE(validasipoobatdetail_t.qty_retur, 0)) = validasipoobatdetail_t.qty_input) THEN 'Sudah Diterima'::text
            ELSE 'Belum Diterima'::text
            END AS status_po,
            btrim(validasipoobat_t.catatan1) AS catatan_1,
            btrim(validasipoobat_t.catatan2) AS catatan_2,
            purchasereq_t.is_prcyto AS cyto,
            CASE purchasereq_t.is_prcyto
            WHEN true THEN 'CITO'::text
            ELSE validasipoobat_t.catatan1
            END AS remarks,
            CASE
            WHEN (validasipoobat_t.catatan IS NOT NULL) THEN validasipoobat_t.last_modified_date
            ELSE validasipoobat_t.tgl_batal_po
            END AS reject_date,
            btrim(validasipoobat_t.catatan) AS reject_remarks,
            btrim((penerimaanobat_t.no_penerimaan)::text) AS no_penerimaan,
            validasipoobat_t.tgl_validasi AS tanggal_penerimaan,
            purchasereq_t.is_prcyto AS jenis_pr,
            fgetnamalookup((purchasereqdetail_t.status)::integer) AS status_pr,
            validasipoobatdetail_t.discount_rp AS deduction_rupiah,
            supplier_m.supplier_id
            FROM ((((((((((((validasipoobat_t
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
            a.discount_rp,
            a.is_deleted
            FROM validasipoobatdetail_t a) validasipoobatdetail_t ON ((validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id)))
            LEFT JOIN ( SELECT a.purchasereqdetail_id,
            a.purchasereq_id,
            a.status
            FROM purchasereqdetail_t a) purchasereqdetail_t ON ((purchasereqdetail_t.purchasereqdetail_id = validasipoobatdetail_t.purchasereqdetail_id)))
            LEFT JOIN ( SELECT a.purchasereq_id,
            a.no_pr,
            a.tgl_pr,
            a.is_prcyto
            FROM purchasereq_t a) purchasereq_t ON ((purchasereq_t.purchasereq_id = purchasereqdetail_t.purchasereq_id)))
            JOIN ( SELECT a.obatalkes_id,
            a.manufaktur_id,
            a.obatalkes_kode,
            a.obatalkes_nama,
            a.is_deleted
            FROM obatalkes_m a) obatalkes_m ON ((validasipoobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
            LEFT JOIN ( SELECT a.supplier_id,
            a.supplier_nama,
            a.supplier_kode
            FROM supplier_m a) supplier_m ON ((validasipoobat_t.supplier_id = supplier_m.supplier_id)))
            LEFT JOIN ( SELECT a.pajak_id,
            a.pajak_persen
            FROM pajak_m a) pajak_m ON ((validasipoobat_t.pajak_id = pajak_m.pajak_id)))
            LEFT JOIN ( SELECT a.validasipoobatdetail_id,
            a.obatalkes_id,
            a.s_konversiobt_id,
            a.qty_diterima,
            a.jumlah,
            a.discount_rp,
            a.po_balance
            FROM (penerimaanobatdetail_t a
            JOIN ( SELECT a1.validasipoobatdetail_id,
            max(a1.penerimaanobatdetail_id) AS penerimaanobatdetail_id
            FROM penerimaanobatdetail_t a1
            GROUP BY a1.validasipoobatdetail_id) max_det ON ((a.penerimaanobatdetail_id = max_det.penerimaanobatdetail_id)))
            WHERE (a.is_deleted = false)) penerimaanobatdetail_t ON ((validasipoobatdetail_t.validasipoobatdetail_id = penerimaanobatdetail_t.validasipoobatdetail_id)))
            LEFT JOIN ( SELECT a.penerimaanobat_id,
            a.validasipoobat_id,
            a.tgl_penerimaan,
            a.no_penerimaan
            FROM (penerimaanobat_t a
            JOIN ( SELECT a1.validasipoobat_id,
            max(a1.penerimaanobat_id) AS penerimaanobat_id
            FROM penerimaanobat_t a1
            GROUP BY a1.validasipoobat_id) max_pen ON ((a.penerimaanobat_id = max_pen.penerimaanobat_id)))) penerimaanobat_t ON ((validasipoobat_t.validasipoobat_id = penerimaanobat_t.validasipoobat_id)))
            LEFT JOIN ( SELECT a.satuankonversi_id,
            a.satuanbesar_id,
            a.satuankecil_id,
            a.nilai_konversi
            FROM satuankonversi_m a) satuankonversi_m ON ((validasipoobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id)))
            LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
            FROM satuanunit_m a) sat_kecil ON ((satuankonversi_m.satuankecil_id = sat_kecil.satuanunit_id)))
            LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
            FROM satuanunit_m a) sat_besar ON ((satuankonversi_m.satuanbesar_id = sat_besar.satuanunit_id)))
            LEFT JOIN ( SELECT a.manufaktur_id,
            a.nama
            FROM manufaktur_m a) manufaktur_m ON ((obatalkes_m.manufaktur_id = manufaktur_m.manufaktur_id)))
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
            btrim((validasipobarang_t.no_pobarang)::text) AS no_po,
            supplier_m.supplier_kode AS supplier_code,
            btrim((supplier_m.supplier_nama)::text) AS supplier_name,
            manufaktur_m.nama AS manufacturer,
            (barang_m.barang_kode)::character varying(100) AS item_code,
            (barang_m.barang_nama)::character varying(255) AS item_name,
            (validasipobarangdetail_t.qty_input)::double precision AS qty_po,
            (COALESCE(validasipobarangdetail_t.qty_penerimaan, 0) - COALESCE((returdetailjumlah.qty_retur)::integer, 0)) AS po_balance,
            (COALESCE(validasipobarangdetail_t.qty_sisa, 0) + COALESCE((returdetailjumlah.qty_retur)::integer, 0)) AS qty_outstanding,
            btrim((sat_besar.satuanunit_nama)::text) AS uom,
            btrim((sat_besar.satuanunit_nama)::text) AS from_uom,
            satuankonversibrg_m.nilai_konversi AS factor,
            btrim((sat_kecil.satuanunit_nama)::text) AS to_uom,
            validasipobarangdetail_t.harga AS price,
            validasipobarangdetail_t.discount AS deduction_percent,
            pajak_m.pajak_persen AS addition_percent,
            ((validasipobarangdetail_t.harga * (validasipobarangdetail_t.qty_input)::double precision) - validasipobarangdetail_t.discount_rp) AS gross_amount,
            (((validasipobarangdetail_t.harga * (validasipobarangdetail_t.qty_input)::double precision) - validasipobarangdetail_t.discount_rp) + ((((validasipobarangdetail_t.harga * (validasipobarangdetail_t.qty_input)::double precision) - validasipobarangdetail_t.discount_rp) * (validasipobarang_t.ppn_persen)::double precision) / (100)::double precision)) AS nett_amount,
            CASE
            WHEN (validasipobarangdetail_t.is_deleted = true) THEN 'Dibatalkan'::text
            ELSE btrim((fgetnamalookup(validasipobarang_t.status_penerimaan))::text)
            END AS status_po,
            btrim(validasipobarang_t.catatan1) AS catatan_1,
            btrim(validasipobarang_t.catatan2) AS catatan_2,
            purchasereqbrg_t.is_prcyto AS cyto,
            CASE purchasereqbrg_t.is_prcyto
            WHEN true THEN 'CITO'::text
            ELSE validasipobarang_t.catatan1
            END AS remarks,
            CASE
            WHEN (validasipobarang_t.catatan IS NOT NULL) THEN validasipobarang_t.last_modified_date
            ELSE validasipobarang_t.tgl_batal_po
            END AS reject_date,
            btrim(validasipobarang_t.catatan) AS reject_remarks,
            btrim((penerimaanbarang_t.no_penerimaan)::text) AS no_penerimaan,
            validasipobarang_t.tgl_validasi AS tanggal_penerimaan,
            purchasereqbrg_t.is_prcyto AS jenis_pr,
            fgetnamalookup((purchasereqbrgdetail_t.status)::integer) AS status_pr,
            validasipobarangdetail_t.discount_rp AS deduction_rupiah,
            supplier_m.supplier_id
            FROM (((((((((((((validasipobarangdetail_t
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
            a.last_modified_date,
            a.tgl_batal_po
            FROM validasipobarang_t a) validasipobarang_t ON ((validasipobarang_t.validasipobarang_id = validasipobarangdetail_t.validasipobarang_id)))
            LEFT JOIN ( SELECT a.purchasereqbrgdetail_id,
            a.purchasereqbrg_id,
            a.status
            FROM purchasereqbrgdetail_t a) purchasereqbrgdetail_t ON ((purchasereqbrgdetail_t.purchasereqbrgdetail_id = validasipobarangdetail_t.purchasereqbrgdetail_id)))
            LEFT JOIN ( SELECT a.purchasereqbrg_id,
            a.no_pr,
            a.tgl_pr,
            a.is_prcyto
            FROM purchasereqbrg_t a) purchasereqbrg_t ON ((purchasereqbrg_t.purchasereqbrg_id = purchasereqbrgdetail_t.purchasereqbrg_id)))
            JOIN ( SELECT a.barang_id,
            a.barang_nama,
            a.manufaktur_id,
            a.barang_kode
            FROM barang_m a) barang_m ON ((validasipobarangdetail_t.barang_id = barang_m.barang_id)))
            LEFT JOIN ( SELECT a.supplier_id,
            a.supplier_kode,
            a.supplier_nama
            FROM supplier_m a) supplier_m ON ((validasipobarang_t.supplier_id = supplier_m.supplier_id)))
            LEFT JOIN ( SELECT a.pajak_id,
            a.pajak_persen
            FROM pajak_m a) pajak_m ON ((validasipobarang_t.pajak_id = pajak_m.pajak_id)))
            LEFT JOIN ( SELECT a.validasipobarangdetail_id,
            a.penerimaanbarangdetail_id,
            a.barang_id,
            a.s_konversibrg_id,
            a.qty_diterima,
            a.jumlah,
            a.discount_rp,
            a.po_balance
            FROM (penerimaanbarangdetail_t a
            JOIN ( SELECT a1.validasipobarangdetail_id,
            max(a1.penerimaanbarangdetail_id) AS penerimaanbarangdetail_id
            FROM penerimaanbarangdetail_t a1
            GROUP BY a1.validasipobarangdetail_id) max_det ON ((a.penerimaanbarangdetail_id = max_det.penerimaanbarangdetail_id)))
            WHERE (a.is_deleted = false)) penerimaanbarangdetail_t ON ((validasipobarangdetail_t.validasipobarangdetail_id = penerimaanbarangdetail_t.validasipobarangdetail_id)))
            LEFT JOIN ( SELECT a.penerimaanbarang_id,
            a.validasipobarang_id,
            a.tgl_penerimaan,
            a.no_penerimaan
            FROM (penerimaanbarang_t a
            JOIN ( SELECT a1.validasipobarang_id,
            max(a1.penerimaanbarang_id) AS penerimaanbarang_id
            FROM penerimaanbarang_t a1
            GROUP BY a1.validasipobarang_id) max_pen ON ((a.penerimaanbarang_id = max_pen.penerimaanbarang_id)))) penerimaanbarang_t ON ((validasipobarang_t.validasipobarang_id = penerimaanbarang_t.validasipobarang_id)))
            LEFT JOIN ( SELECT a.satuankonversibrg_id,
            a.satuanbesar_id,
            a.satuankecil_id,
            a.nilai_konversi
            FROM satuankonversibrg_m a) satuankonversibrg_m ON ((validasipobarangdetail_t.s_konversibrg_id = satuankonversibrg_m.satuankonversibrg_id)))
            LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
            FROM satuanunit_m a) sat_kecil ON ((satuankonversibrg_m.satuankecil_id = sat_kecil.satuanunit_id)))
            LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
            FROM satuanunit_m a) sat_besar ON ((satuankonversibrg_m.satuanbesar_id = sat_besar.satuanunit_id)))
            LEFT JOIN ( SELECT a.manufaktur_id,
            a.nama
            FROM manufaktur_m a) manufaktur_m ON ((barang_m.manufaktur_id = manufaktur_m.manufaktur_id)))
            LEFT JOIN ( SELECT penerimaanbarang_detail.validasipobarangdetail_id,
            sum(a.qty_input) AS qty_retur
            FROM (returpenerimaanbarangdetail_t a
            LEFT JOIN ( SELECT a1.penerimaanbarangdetail_id,
            a1.validasipobarangdetail_id
            FROM penerimaanbarangdetail_t a1) penerimaanbarang_detail ON ((penerimaanbarang_detail.penerimaanbarangdetail_id = a.penerimaanbarangdetail_id)))
            GROUP BY penerimaanbarang_detail.validasipobarangdetail_id) returdetailjumlah ON ((validasipobarangdetail_t.validasipobarangdetail_id = returdetailjumlah.validasipobarangdetail_id)))
            ;");
        $this->execute('
            ALTER TABLE public.laporanallpo_v OWNER TO postgres;
            ');

        $this->execute('DROP VIEW if exists public.infopo_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infopo_v\" AS
            SELECT
            CASE
            WHEN ((validasipoobat_t.is_manual = false) AND (validasipoobat_t.additional_data IS NOT NULL)) THEN 'PURCHASE REQUEST'::text
            WHEN (validasipoobat_t.is_manual = false) THEN 'REKOMENDASI'::text
            ELSE 'PO_MANUAL'::text
            END AS asal_transaksi,
            'obat'::text AS type_po,
            validasipoobat_t.validasipoobat_id AS transaksi_id,
            validasipoobat_t.tgl_validasi AS tanggal_po,
            COALESCE(rekomendasi_obat.tgl_rekomendasiobat, pr.tgl_pr) AS tgl_rekomendasi,
            CASE
            WHEN ((validasipoobat_t.is_manual = false) AND (validasipoobat_t.additional_data IS NOT NULL)) THEN pr.no_pr
            WHEN (validasipoobat_t.is_manual = false) THEN rekomendasi_obat.no_rekomendasiobat
            ELSE NULL::character varying
            END AS nomor,
            validasipoobat_t.no_poobat AS no_transaksi,
            validasipoobat_t.supplier_id,
            supplier_m.supplier_nama,
            validasipoobat_t.ruangan_id,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_nama,
            validasipoobat_t.is_validasi,
            CASE
            WHEN (validasipoobat_t.is_validasi = false) THEN 'Belum Validasi'::text
            ELSE 'Sudah Validasi'::text
            END AS status_validasi,
            validasipoobat_t.status_penerimaan,
            fgetnamalookup(validasipoobat_t.status_penerimaan) AS stat_penerimaan,
            payterm_m.jumlah_hari AS payment_term,
            validasipoobat_t.total AS total_harga_po,
            validasipoobat_t.tgl_rencanaterima,
            validasipoobat_t.peg_mengetahui_id,
            peg_mengetahui.nama_pegawai AS peg_mengetahui,
            validasipoobat_t.peg_menyetujui_id,
            peg_menyetujui.nama_pegawai AS peg_menyetujui,
            validasipoobat_t.sub_total,
            total_diskon.total_discount,
            validasipoobat_t.ppn_persen,
            validasipoobat_t.ppn_nilai,
            validasipoobat_t.total,
            validasipoobat_t.status_penerimaan AS lookup_id,
            payterm_m.payterm_id,
            CASE
            WHEN ((validasipoobat_t.is_manual = false) AND (validasipoobat_t.additional_data IS NOT NULL)) THEN pr.pegawai_id
            WHEN (validasipoobat_t.is_manual = false) THEN rekomendasi_obat.pegawai_id
            ELSE validasipoobat_t.diorder_oleh
            END AS diorder_oleh,
            CASE
            WHEN ((validasipoobat_t.is_manual = false) AND (validasipoobat_t.additional_data IS NOT NULL)) THEN pr.nama_pegawai
            WHEN (validasipoobat_t.is_manual = false) THEN rekomendasi_obat.nama_pegawai
            ELSE diorder_oleh.nama_pegawai
            END AS diorder_oleh_nama,
            validasipoobat_t.pajak_id,
            validasipoobat_t.catatan1,
            validasipoobat_t.catatan2,
            validasipoobat_t.is_closing,
            ruangan_m.instalasi_id,
            validasipoobat_t.modified_count,
            validasipoobat_t.last_modified_date,
            validasipoobat_t.last_modified_by,
            peg_mengubah.nama_pegawai AS peg_mengubah,
            validasipoobat_t.last_modified_date AS tgl_perubahan,
            peg_validasi.nama_pegawai AS pegawai_validasi,
            validasipoobat_t.is_verifikasi,
            validasipoobat_t.created_date AS tanggal_buat_po,
            diorder.pegawai_id AS diorder_id,
            diorder.nama_pegawai AS diorder_nama,
            validasipoobat_t.tgl_validasi AS tgl_penerimaan,
            sum(validasipoobatdetail_t.qty_po) AS qty_po,
            validasipoobat_t.status_penerimaan AS status_po_id,
            fgetnamalookup(validasipoobat_t.status_penerimaan) AS status_po_nama,
            validasipoobat_t.tgl_batal_po,
            btrim(validasipoobat_t.catatan) AS catatan_batal_po
            FROM (((((((((((((((validasipoobat_t
            LEFT JOIN validasipoobatdetail_t ON ((validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id)))
            LEFT JOIN supplier_m ON ((validasipoobat_t.supplier_id = supplier_m.supplier_id)))
            JOIN ruangan_m ON ((validasipoobat_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN payterm_m ON ((validasipoobat_t.payterm_id = payterm_m.payterm_id)))
            LEFT JOIN pegawai_m peg_mengetahui ON ((validasipoobat_t.peg_mengetahui_id = peg_mengetahui.pegawai_id)))
            LEFT JOIN pegawai_m peg_menyetujui ON ((validasipoobat_t.peg_menyetujui_id = peg_menyetujui.pegawai_id)))
            LEFT JOIN pegawai_m diorder_oleh ON ((validasipoobat_t.diorder_oleh = diorder_oleh.pegawai_id)))
            LEFT JOIN pegawai_m diorder ON ((validasipoobat_t.diorder_oleh = diorder.pegawai_id)))
            LEFT JOIN pegawai_m peg_validasi ON ((validasipoobat_t.peg_validasi_id = peg_validasi.pegawai_id)))
            LEFT JOIN loginpemakai_k ON ((validasipoobat_t.last_modified_by = loginpemakai_k.loginpemakai_id)))
            LEFT JOIN pegawai_m peg_mengubah ON ((loginpemakai_k.pegawai_id = peg_mengubah.pegawai_id)))
            LEFT JOIN ( SELECT rekomendasiobat_t.no_rekomendasiobat,
            rekomendasiobat_t.tgl_rekomendasiobat,
            validasipoobatdetail_t_1.validasipoobat_id,
            loginpemakai_k_1.pegawai_id,
            pegawai_m.nama_pegawai
            FROM ((((rekomendasiobat_t
            JOIN rekomendasiobatdetail_t ON ((rekomendasiobat_t.rekomendasiobat_id = rekomendasiobatdetail_t.rekomendasiobat_id)))
            JOIN validasipoobatdetail_t validasipoobatdetail_t_1 ON ((rekomendasiobatdetail_t.rekomendasiobatdetail_id = validasipoobatdetail_t_1.rekomendasiobatdetail_id)))
            LEFT JOIN loginpemakai_k loginpemakai_k_1 ON ((rekomendasiobat_t.created_by = loginpemakai_k_1.loginpemakai_id)))
            LEFT JOIN pegawai_m ON ((loginpemakai_k_1.pegawai_id = pegawai_m.pegawai_id)))
            GROUP BY rekomendasiobat_t.no_rekomendasiobat, rekomendasiobat_t.tgl_rekomendasiobat, validasipoobatdetail_t_1.validasipoobat_id, loginpemakai_k_1.pegawai_id, pegawai_m.nama_pegawai) rekomendasi_obat ON ((validasipoobat_t.validasipoobat_id = rekomendasi_obat.validasipoobat_id)))
            LEFT JOIN ( SELECT purchasereq_t.no_pr,
            (purchasereq_t.tgl_pr)::timestamp without time zone AS tgl_pr,
            validasipoobatdetail_t_1.validasipoobat_id,
            loginpemakai_k_1.pegawai_id,
            pegawai_m.nama_pegawai
            FROM ((((purchasereq_t
            JOIN purchasereqdetail_t ON ((purchasereq_t.purchasereq_id = purchasereqdetail_t.purchasereq_id)))
            JOIN validasipoobatdetail_t validasipoobatdetail_t_1 ON ((purchasereqdetail_t.purchasereqdetail_id = validasipoobatdetail_t_1.purchasereqdetail_id)))
            LEFT JOIN loginpemakai_k loginpemakai_k_1 ON ((purchasereq_t.created_by = loginpemakai_k_1.loginpemakai_id)))
            LEFT JOIN pegawai_m ON ((loginpemakai_k_1.pegawai_id = pegawai_m.pegawai_id)))
            WHERE (purchasereq_t.is_deleted = false)
            GROUP BY purchasereq_t.no_pr, purchasereq_t.tgl_pr, validasipoobatdetail_t_1.validasipoobat_id, loginpemakai_k_1.pegawai_id, pegawai_m.nama_pegawai) pr ON ((validasipoobat_t.validasipoobat_id = pr.validasipoobat_id)))
            LEFT JOIN ( SELECT validasipoobatdetail_t_1.validasipoobat_id,
            sum(((validasipoobatdetail_t_1.harga * (validasipoobatdetail_t_1.qty_input)::double precision) * (validasipoobatdetail_t_1.discount / (100)::double precision))) AS total_discount
            FROM validasipoobatdetail_t validasipoobatdetail_t_1
            WHERE (validasipoobatdetail_t_1.is_deleted = false)
            GROUP BY validasipoobatdetail_t_1.validasipoobat_id) total_diskon ON ((validasipoobat_t.validasipoobat_id = total_diskon.validasipoobat_id)))
            WHERE (validasipoobat_t.is_deleted = false)
            GROUP BY validasipoobat_t.additional_data, validasipoobat_t.is_manual, validasipoobat_t.validasipoobat_id, validasipoobat_t.tgl_validasi, rekomendasi_obat.tgl_rekomendasiobat, pr.tgl_pr, rekomendasi_obat.no_rekomendasiobat, validasipoobat_t.no_poobat, validasipoobat_t.supplier_id, supplier_m.supplier_nama, validasipoobat_t.ruangan_id, ruangan_m.ruangan_nama, instalasi_m.instalasi_nama, validasipoobat_t.is_validasi, validasipoobat_t.status_penerimaan, payterm_m.jumlah_hari, validasipoobat_t.total, validasipoobat_t.tgl_rencanaterima, validasipoobat_t.peg_mengetahui_id, peg_mengetahui.nama_pegawai, validasipoobat_t.peg_menyetujui_id, peg_menyetujui.nama_pegawai, validasipoobat_t.sub_total, total_diskon.total_discount, validasipoobat_t.ppn_persen, validasipoobat_t.ppn_nilai, payterm_m.payterm_id, validasipoobat_t.pajak_id, validasipoobat_t.catatan1, validasipoobat_t.catatan2, validasipoobat_t.is_closing, ruangan_m.instalasi_id, validasipoobat_t.modified_count, validasipoobat_t.last_modified_date, validasipoobat_t.last_modified_by, peg_mengubah.nama_pegawai, peg_validasi.nama_pegawai, validasipoobat_t.is_verifikasi, validasipoobat_t.created_date, diorder.pegawai_id, diorder.nama_pegawai, validasipoobat_t.tgl_batal_po, validasipoobat_t.catatan, pr.no_pr, pr.pegawai_id, rekomendasi_obat.pegawai_id, pr.nama_pegawai, rekomendasi_obat.nama_pegawai, diorder_oleh.nama_pegawai
            UNION ALL
            SELECT
            CASE
            WHEN ((validasipobarang_t.is_manual = false) AND (validasipobarang_t.additional_data IS NOT NULL)) THEN 'PURCHASE REQUEST'::text
            WHEN (validasipobarang_t.is_manual = false) THEN 'REKOMENDASI'::text
            ELSE 'PO_MANUAL'::text
            END AS asal_transaksi,
            'barang'::text AS type_po,
            validasipobarang_t.validasipobarang_id AS transaksi_id,
            validasipobarang_t.tgl_validasi AS tanggal_po,
            COALESCE(rekomendasi_barang.tgl_rekomendasibarang, pr.tgl_pr) AS tgl_rekomendasi,
            CASE
            WHEN ((validasipobarang_t.is_manual = false) AND (validasipobarang_t.additional_data IS NOT NULL)) THEN pr.no_pr
            WHEN (validasipobarang_t.is_manual = false) THEN rekomendasi_barang.no_rekomendasibarang
            ELSE NULL::character varying
            END AS nomor,
            validasipobarang_t.no_pobarang AS no_transaksi,
            validasipobarang_t.supplier_id,
            supplier_m.supplier_nama,
            validasipobarang_t.ruangan_id,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_nama,
            validasipobarang_t.is_validasi,
            CASE
            WHEN (validasipobarang_t.is_validasi = false) THEN 'Belum Validasi'::text
            ELSE 'Sudah Validasi'::text
            END AS status_validasi,
            validasipobarang_t.status_penerimaan,
            fgetnamalookup(validasipobarang_t.status_penerimaan) AS stat_penerimaan,
            payterm_m.jumlah_hari AS payment_term,
            validasipobarang_t.total AS total_harga_po,
            validasipobarang_t.tgl_rencanaterima,
            validasipobarang_t.peg_mengetahui_id,
            peg_mengetahui.nama_pegawai AS peg_mengetahui,
            validasipobarang_t.peg_menyetujui_id,
            peg_menyetujui.nama_pegawai AS peg_menyetujui,
            validasipobarang_t.sub_total,
            total_diskon.total_discount,
            validasipobarang_t.ppn_persen,
            validasipobarang_t.ppn_nilai,
            validasipobarang_t.total,
            validasipobarang_t.status_penerimaan AS lookup_id,
            payterm_m.payterm_id,
            validasipobarang_t.diorder_oleh,
            diorder_oleh.nama_pegawai AS diorder_oleh_nama,
            validasipobarang_t.pajak_id,
            validasipobarang_t.catatan1,
            validasipobarang_t.catatan2,
            validasipobarang_t.is_closing,
            ruangan_m.instalasi_id,
            validasipobarang_t.modified_count,
            validasipobarang_t.last_modified_date,
            validasipobarang_t.last_modified_by,
            peg_mengubah.nama_pegawai AS peg_mengubah,
            validasipobarang_t.last_modified_date AS tgl_perubahan,
            peg_validasi.nama_pegawai AS pegawai_validasi,
            validasipobarang_t.is_verifikasi,
            validasipobarang_t.created_date AS tanggal_buat_po,
            diorder.pegawai_id AS diorder_id,
            diorder.nama_pegawai AS diorder_nama,
            validasipobarang_t.tgl_validasi AS tgl_penerimaan,
            sum(validasipobarangdetail_t.qty_po) AS qty_po,
            validasipobarang_t.status_penerimaan AS status_po_id,
            fgetnamalookup(validasipobarang_t.status_penerimaan) AS status_po_nama,
            validasipobarang_t.tgl_batal_po,
            btrim(validasipobarang_t.catatan) AS catatan_batal_po
            FROM (((((((((((((((validasipobarang_t
            LEFT JOIN validasipobarangdetail_t ON ((validasipobarang_t.validasipobarang_id = validasipobarangdetail_t.validasipobarang_id)))
            LEFT JOIN supplier_m ON ((validasipobarang_t.supplier_id = supplier_m.supplier_id)))
            JOIN ruangan_m ON ((validasipobarang_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN payterm_m ON ((validasipobarang_t.payterm_id = payterm_m.payterm_id)))
            LEFT JOIN pegawai_m peg_mengetahui ON ((validasipobarang_t.peg_mengetahui_id = peg_mengetahui.pegawai_id)))
            LEFT JOIN pegawai_m peg_menyetujui ON ((validasipobarang_t.peg_menyetujui_id = peg_menyetujui.pegawai_id)))
            LEFT JOIN pegawai_m diorder_oleh ON ((validasipobarang_t.diorder_oleh = diorder_oleh.pegawai_id)))
            LEFT JOIN pegawai_m diorder ON ((validasipobarang_t.diorder_oleh = diorder.pegawai_id)))
            LEFT JOIN pegawai_m peg_validasi ON ((validasipobarang_t.peg_validasi_id = peg_validasi.pegawai_id)))
            LEFT JOIN loginpemakai_k ON ((validasipobarang_t.last_modified_by = loginpemakai_k.loginpemakai_id)))
            LEFT JOIN pegawai_m peg_mengubah ON ((loginpemakai_k.pegawai_id = peg_mengubah.pegawai_id)))
            LEFT JOIN ( SELECT rekomendasibarang_t.no_rekomendasibarang,
            rekomendasibarang_t.tgl_rekomendasibarang,
            validasipobarangdetail_t_1.validasipobarang_id
            FROM ((rekomendasibarang_t
            JOIN rekomendasibarangdetail_t ON ((rekomendasibarang_t.rekomendasibarang_id = rekomendasibarangdetail_t.rekomendasibarang_id)))
            JOIN validasipobarangdetail_t validasipobarangdetail_t_1 ON ((rekomendasibarangdetail_t.rekomendasibarangdetail_id = validasipobarangdetail_t_1.rekomendasibarangdetail_id)))
            GROUP BY rekomendasibarang_t.no_rekomendasibarang, rekomendasibarang_t.tgl_rekomendasibarang, validasipobarangdetail_t_1.validasipobarang_id) rekomendasi_barang ON ((validasipobarang_t.validasipobarang_id = rekomendasi_barang.validasipobarang_id)))
            LEFT JOIN ( SELECT purchasereqbrg_t.no_pr,
            (purchasereqbrg_t.tgl_pr)::timestamp without time zone AS tgl_pr,
            validasipobarangdetail_t_1.validasipobarang_id
            FROM ((purchasereqbrg_t
            JOIN purchasereqbrgdetail_t ON ((purchasereqbrg_t.purchasereqbrg_id = purchasereqbrgdetail_t.purchasereqbrg_id)))
            JOIN validasipobarangdetail_t validasipobarangdetail_t_1 ON ((purchasereqbrgdetail_t.purchasereqbrgdetail_id = validasipobarangdetail_t_1.purchasereqbrgdetail_id)))
            WHERE (purchasereqbrg_t.is_deleted = false)
            GROUP BY purchasereqbrg_t.no_pr, purchasereqbrg_t.tgl_pr, validasipobarangdetail_t_1.validasipobarang_id) pr ON ((validasipobarang_t.validasipobarang_id = pr.validasipobarang_id)))
            LEFT JOIN ( SELECT validasipobarangdetail_t_1.validasipobarang_id,
            sum(((validasipobarangdetail_t_1.harga * (validasipobarangdetail_t_1.qty_input)::double precision) * (validasipobarangdetail_t_1.discount / (100)::double precision))) AS total_discount
            FROM validasipobarangdetail_t validasipobarangdetail_t_1
            WHERE (validasipobarangdetail_t_1.is_deleted = false)
            GROUP BY validasipobarangdetail_t_1.validasipobarang_id) total_diskon ON ((validasipobarang_t.validasipobarang_id = total_diskon.validasipobarang_id)))
            WHERE (validasipobarang_t.is_deleted = false)
            GROUP BY pr.no_pr, rekomendasi_barang.no_rekomendasibarang, validasipobarang_t.additional_data, validasipobarang_t.is_manual, validasipobarang_t.validasipobarang_id, validasipobarang_t.tgl_validasi, rekomendasi_barang.tgl_rekomendasibarang, pr.tgl_pr, validasipobarang_t.no_pobarang, validasipobarang_t.supplier_id, supplier_m.supplier_nama, validasipobarang_t.ruangan_id, ruangan_m.ruangan_nama, instalasi_m.instalasi_nama, validasipobarang_t.is_validasi, validasipobarang_t.status_penerimaan, payterm_m.jumlah_hari, validasipobarang_t.total, validasipobarang_t.tgl_rencanaterima, validasipobarang_t.peg_mengetahui_id, peg_mengetahui.nama_pegawai, validasipobarang_t.peg_menyetujui_id, peg_menyetujui.nama_pegawai, validasipobarang_t.sub_total, total_diskon.total_discount, validasipobarang_t.ppn_persen, validasipobarang_t.ppn_nilai, payterm_m.payterm_id, validasipobarang_t.diorder_oleh, diorder_oleh.nama_pegawai, validasipobarang_t.pajak_id, validasipobarang_t.catatan1, validasipobarang_t.catatan2, validasipobarang_t.is_closing, ruangan_m.instalasi_id, validasipobarang_t.modified_count, validasipobarang_t.last_modified_date, validasipobarang_t.last_modified_by, peg_mengubah.nama_pegawai, peg_validasi.nama_pegawai, validasipobarang_t.is_verifikasi, validasipobarang_t.created_date, diorder.pegawai_id, diorder.nama_pegawai, validasipobarang_t.tgl_batal_po, validasipobarang_t.catatan
            ;");
        $this->execute('
            ALTER TABLE public.infopo_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211213_131602_migrate_US2384_penambahaninformasilaporanpo cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211213_131602_migrate_US2384_penambahaninformasilaporanpo cannot be reverted.\n";

        return false;
    }
    */
}

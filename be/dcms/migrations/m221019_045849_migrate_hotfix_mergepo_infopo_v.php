<?php

use yii\db\Migration;

/**
 * Class m221019_045849_migrate_hotfix_mergepo_infopo_v
 */
class m221019_045849_migrate_hotfix_mergepo_infopo_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infopo_v";
        ');

        $this->execute("
            CREATE VIEW \"public\".\"infopo_v\" AS  SELECT a.asal_transaksi,
    a.type_po,
    a.transaksi_id,
    a.tanggal_po,
    a.tgl_rekomendasi,
    string_agg(a.nomor::text, ','::text) AS nomor,
    a.no_transaksi,
    a.supplier_id,
    a.supplier_nama,
    a.ruangan_id,
    a.ruangan_nama,
    a.instalasi_nama,
    a.is_validasi,
    a.status_validasi,
    a.status_penerimaan,
    a.stat_penerimaan,
    a.payment_term,
    a.total_harga_po,
    a.tgl_rencanaterima,
    a.peg_mengetahui_id,
    a.peg_mengetahui,
    a.peg_menyetujui_id,
    a.peg_menyetujui,
    a.sub_total,
    a.total_discount,
    a.ppn_persen,
    a.ppn_nilai,
    a.total,
    a.lookup_id,
    a.payterm_id,
    a.diorder_oleh,
    a.diorder_oleh_nama,
    a.pajak_id,
    a.catatan1,
    a.catatan2,
    a.is_closing,
    a.instalasi_id,
    a.modified_count,
    a.last_modified_date,
    a.last_modified_by,
    a.peg_mengubah,
    a.tgl_perubahan,
    a.pegawai_validasi,
    a.is_verifikasi,
    a.tanggal_buat_po,
    a.diorder_id,
    a.diorder_nama,
    a.tgl_penerimaan,
    a.qty_po,
    a.status_po_id,
    a.status_po_nama,
    a.tgl_batal_po,
    a.catatan_batal_po,
    a.peg_penerima_nama,
    a.tgl_tercetak,
    a.po_consigment,
    a.po_cito,
    a.po_admin,
    a.catatan
   FROM ( SELECT
                CASE
                    WHEN validasipoobat_t.is_manual = false AND validasipoobat_t.additional_data IS NOT NULL THEN 'PURCHASE REQUEST'::text
                    WHEN validasipoobat_t.is_manual = false THEN 'REKOMENDASI'::text
                    ELSE 'PO_MANUAL'::text
                END AS asal_transaksi,
            'obat'::text AS type_po,
            validasipoobat_t.validasipoobat_id AS transaksi_id,
            validasipoobat_t.tgl_validasi AS tanggal_po,
            COALESCE(rekomendasi_obat.tgl_rekomendasiobat, pr2.tgl_pr) AS tgl_rekomendasi,
                CASE
                    WHEN validasipoobat_t.is_manual = false AND validasipoobat_t.additional_data IS NOT NULL THEN pr.no_pr
                    WHEN validasipoobat_t.is_manual = false THEN rekomendasi_obat.no_rekomendasiobat
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
                    WHEN validasipoobat_t.is_validasi = false THEN 'Belum Validasi'::text
                    ELSE 'Sudah Validasi'::text
                END AS status_validasi,
            validasipoobat_t.status_penerimaan,
            lookup_penerimaan.lookup_name AS stat_penerimaan,
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
                    WHEN validasipoobat_t.is_manual = false AND validasipoobat_t.additional_data IS NOT NULL THEN pr.pegawai_id
                    WHEN validasipoobat_t.is_manual = false THEN rekomendasi_obat.pegawai_id
                    ELSE validasipoobat_t.diorder_oleh
                END AS diorder_oleh,
                CASE
                    WHEN validasipoobat_t.is_manual = false AND validasipoobat_t.additional_data IS NOT NULL THEN pr.nama_pegawai
                    WHEN validasipoobat_t.is_manual = false THEN rekomendasi_obat.nama_pegawai
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
            lookup_penerimaan.lookup_name AS status_po_nama,
            validasipoobat_t.tgl_batal_po,
            btrim(validasipoobat_t.catatan) AS catatan_batal_po,
            peg_penerima.nama_pegawai AS peg_penerima_nama,
            validasipoobat_t.tgl_tercetak,
                CASE
                    WHEN validasipoobat_t.is_consigment = true THEN 'Ya'::text
                    WHEN validasipoobat_t.is_consigment = false THEN 'Tidak'::text
                    WHEN validasipoobat_t.is_consigment IS NULL THEN 'Tidak'::text
                    ELSE NULL::text
                END AS po_consigment,
                CASE
                    WHEN validasipoobat_t.is_cito = true THEN 'Cito'::text
                    WHEN validasipoobat_t.is_cito = false THEN 'Reguler'::text
                    WHEN validasipoobat_t.is_cito IS NULL THEN 'Reguler'::text
                    ELSE NULL::text
                END AS po_cito,
                CASE
                    WHEN validasipoobat_t.is_admin = true THEN 'Ya'::text
                    WHEN validasipoobat_t.is_admin = false THEN 'Tidak'::text
                    WHEN validasipoobat_t.is_admin IS NULL THEN 'Tidak'::text
                    ELSE NULL::text
                END AS po_admin,
            pr.catatan
           FROM validasipoobat_t
             LEFT JOIN ( SELECT validasipoobatdetail_t_1.validasipoobatdetail_id,
                    validasipoobatdetail_t_1.validasipoobat_id,
                    validasipoobatdetail_t_1.qty_po
                   FROM validasipoobatdetail_t validasipoobatdetail_t_1) validasipoobatdetail_t ON validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id
             LEFT JOIN ( SELECT supplier_m_1.supplier_id,
                    supplier_m_1.supplier_nama
                   FROM supplier_m supplier_m_1) supplier_m ON validasipoobat_t.supplier_id = supplier_m.supplier_id
             JOIN ( SELECT ruangan_m_1.ruangan_id,
                    ruangan_m_1.ruangan_nama,
                    ruangan_m_1.instalasi_id
                   FROM ruangan_m ruangan_m_1) ruangan_m ON validasipoobat_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT instalasi_m_1.instalasi_id,
                    instalasi_m_1.instalasi_nama
                   FROM instalasi_m instalasi_m_1) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN ( SELECT payterm_m_1.payterm_id,
                    payterm_m_1.jumlah_hari
                   FROM payterm_m payterm_m_1) payterm_m ON validasipoobat_t.payterm_id = payterm_m.payterm_id
             LEFT JOIN ( SELECT pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai
                   FROM pegawai_m) peg_mengetahui ON validasipoobat_t.peg_mengetahui_id = peg_mengetahui.pegawai_id
             LEFT JOIN ( SELECT pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai
                   FROM pegawai_m) peg_menyetujui ON validasipoobat_t.peg_menyetujui_id = peg_menyetujui.pegawai_id
             LEFT JOIN ( SELECT pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai
                   FROM pegawai_m) diorder_oleh ON validasipoobat_t.diorder_oleh = diorder_oleh.pegawai_id
             LEFT JOIN ( SELECT pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai
                   FROM pegawai_m) diorder ON validasipoobat_t.diorder_oleh = diorder.pegawai_id
             LEFT JOIN ( SELECT pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai
                   FROM pegawai_m) peg_validasi ON validasipoobat_t.peg_validasi_id = peg_validasi.pegawai_id
             LEFT JOIN ( SELECT loginpemakai_k_1.loginpemakai_id,
                    loginpemakai_k_1.pegawai_id
                   FROM loginpemakai_k loginpemakai_k_1) loginpemakai_k ON validasipoobat_t.last_modified_by = loginpemakai_k.loginpemakai_id
             LEFT JOIN ( SELECT pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai
                   FROM pegawai_m) peg_mengubah ON loginpemakai_k.pegawai_id = peg_mengubah.pegawai_id
             LEFT JOIN ( SELECT pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai
                   FROM pegawai_m) peg_penerima ON validasipoobat_t.peg_penerima_id = peg_penerima.pegawai_id
             LEFT JOIN ( SELECT rekomendasiobat_t.no_rekomendasiobat,
                    rekomendasiobat_t.tgl_rekomendasiobat,
                    validasipoobatdetail_t_1.validasipoobat_id,
                    loginpemakai_k_1.pegawai_id,
                    pegawai_m.nama_pegawai
                   FROM rekomendasiobat_t
                     JOIN ( SELECT a_1.rekomendasiobat_id,
                            a_1.rekomendasiobatdetail_id
                           FROM rekomendasiobatdetail_t a_1) rekomendasiobatdetail_t ON rekomendasiobat_t.rekomendasiobat_id = rekomendasiobatdetail_t.rekomendasiobat_id
                     JOIN ( SELECT a_1.validasipoobatdetail_id,
                            a_1.validasipoobat_id,
                            a_1.rekomendasiobatdetail_id
                           FROM validasipoobatdetail_t a_1) validasipoobatdetail_t_1 ON rekomendasiobatdetail_t.rekomendasiobatdetail_id = validasipoobatdetail_t_1.rekomendasiobatdetail_id
                     LEFT JOIN ( SELECT a_1.pegawai_id,
                            a_1.loginpemakai_id
                           FROM loginpemakai_k a_1) loginpemakai_k_1 ON rekomendasiobat_t.created_by = loginpemakai_k_1.loginpemakai_id
                     LEFT JOIN ( SELECT pegawai_m_1.pegawai_id,
                            pegawai_m_1.nama_pegawai
                           FROM pegawai_m pegawai_m_1) pegawai_m ON loginpemakai_k_1.pegawai_id = pegawai_m.pegawai_id
                  GROUP BY rekomendasiobat_t.no_rekomendasiobat, rekomendasiobat_t.tgl_rekomendasiobat, validasipoobatdetail_t_1.validasipoobat_id, loginpemakai_k_1.pegawai_id, pegawai_m.nama_pegawai) rekomendasi_obat ON validasipoobat_t.validasipoobat_id = rekomendasi_obat.validasipoobat_id
             LEFT JOIN ( SELECT purchasereq_t.no_pr,
                    purchasereq_t.tgl_pr,
                    validasipoobatdetail_t_1.validasipoobat_id,
                    loginpemakai_k_1.pegawai_id,
                    pegawai_m.nama_pegawai,
                    purchasereq_t.is_prcyto,
                    purchasereqdetail_t.catatan
                   FROM purchasereq_t
                     JOIN ( SELECT a_1.purchasereqdetail_id,
                            a_1.purchasereq_id,
                            a_1.catatan
                           FROM purchasereqdetail_t a_1) purchasereqdetail_t ON purchasereq_t.purchasereq_id = purchasereqdetail_t.purchasereq_id
                     JOIN ( SELECT a_1.validasipoobatdetail_id,
                            a_1.validasipoobat_id,
                            a_1.purchasereqdetail_id
                           FROM validasipoobatdetail_t a_1) validasipoobatdetail_t_1 ON purchasereqdetail_t.purchasereqdetail_id = validasipoobatdetail_t_1.purchasereqdetail_id
                     LEFT JOIN ( SELECT a_1.pegawai_id,
                            a_1.loginpemakai_id
                           FROM loginpemakai_k a_1) loginpemakai_k_1 ON purchasereq_t.created_by = loginpemakai_k_1.loginpemakai_id
                     LEFT JOIN ( SELECT pegawai_m_1.pegawai_id,
                            pegawai_m_1.nama_pegawai
                           FROM pegawai_m pegawai_m_1) pegawai_m ON loginpemakai_k_1.pegawai_id = pegawai_m.pegawai_id
                  WHERE purchasereq_t.is_deleted = false
                  GROUP BY purchasereq_t.no_pr, purchasereq_t.tgl_pr, validasipoobatdetail_t_1.validasipoobat_id, purchasereq_t.is_prcyto, loginpemakai_k_1.pegawai_id, pegawai_m.nama_pegawai, purchasereqdetail_t.catatan) pr ON validasipoobat_t.validasipoobat_id = pr.validasipoobat_id
             LEFT JOIN ( SELECT max(purchasereq_t.tgl_pr) AS tgl_pr,
                    validasipoobatdetail_t_1.validasipoobat_id
                   FROM purchasereq_t
                     JOIN ( SELECT a_1.purchasereqdetail_id,
                            a_1.purchasereq_id
                           FROM purchasereqdetail_t a_1) purchasereqdetail_t ON purchasereq_t.purchasereq_id = purchasereqdetail_t.purchasereq_id
                     JOIN ( SELECT a_1.validasipoobatdetail_id,
                            a_1.validasipoobat_id,
                            a_1.purchasereqdetail_id
                           FROM validasipoobatdetail_t a_1) validasipoobatdetail_t_1 ON purchasereqdetail_t.purchasereqdetail_id = validasipoobatdetail_t_1.purchasereqdetail_id
                     LEFT JOIN ( SELECT a_1.pegawai_id,
                            a_1.loginpemakai_id
                           FROM loginpemakai_k a_1) loginpemakai_k_1 ON purchasereq_t.created_by = loginpemakai_k_1.loginpemakai_id
                     LEFT JOIN ( SELECT pegawai_m_1.pegawai_id,
                            pegawai_m_1.nama_pegawai
                           FROM pegawai_m pegawai_m_1) pegawai_m ON loginpemakai_k_1.pegawai_id = pegawai_m.pegawai_id
                  WHERE purchasereq_t.is_deleted = false
                  GROUP BY validasipoobatdetail_t_1.validasipoobat_id) pr2 ON validasipoobat_t.validasipoobat_id = pr2.validasipoobat_id
             LEFT JOIN ( SELECT validasipoobatdetail_t_1.validasipoobat_id,
                    sum(validasipoobatdetail_t_1.harga * validasipoobatdetail_t_1.qty_input::double precision * (validasipoobatdetail_t_1.discount / 100::double precision)) AS total_discount
                   FROM validasipoobatdetail_t validasipoobatdetail_t_1
                  WHERE validasipoobatdetail_t_1.is_deleted = false
                  GROUP BY validasipoobatdetail_t_1.validasipoobat_id) total_diskon ON validasipoobat_t.validasipoobat_id = total_diskon.validasipoobat_id
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name
                   FROM lookup_m) lookup_penerimaan ON validasipoobat_t.status_penerimaan = lookup_penerimaan.lookup_id
          WHERE validasipoobat_t.is_deleted = false
          GROUP BY pr.is_prcyto, validasipoobat_t.additional_data, validasipoobat_t.is_manual, validasipoobat_t.validasipoobat_id, validasipoobat_t.tgl_validasi, rekomendasi_obat.tgl_rekomendasiobat, pr2.tgl_pr, rekomendasi_obat.no_rekomendasiobat, validasipoobat_t.no_poobat, validasipoobat_t.supplier_id, supplier_m.supplier_nama, validasipoobat_t.ruangan_id, ruangan_m.ruangan_nama, instalasi_m.instalasi_nama, validasipoobat_t.is_validasi, validasipoobat_t.status_penerimaan, payterm_m.jumlah_hari, validasipoobat_t.total, validasipoobat_t.tgl_rencanaterima, validasipoobat_t.peg_mengetahui_id, peg_mengetahui.nama_pegawai, validasipoobat_t.peg_menyetujui_id, peg_menyetujui.nama_pegawai, validasipoobat_t.sub_total, total_diskon.total_discount, validasipoobat_t.ppn_persen, validasipoobat_t.ppn_nilai, payterm_m.payterm_id, validasipoobat_t.pajak_id, validasipoobat_t.catatan1, validasipoobat_t.catatan2, validasipoobat_t.is_closing, ruangan_m.instalasi_id, validasipoobat_t.modified_count, validasipoobat_t.last_modified_date, validasipoobat_t.last_modified_by, peg_mengubah.nama_pegawai, peg_validasi.nama_pegawai, validasipoobat_t.is_verifikasi, validasipoobat_t.created_date, diorder.pegawai_id, diorder.nama_pegawai, validasipoobat_t.tgl_batal_po, validasipoobat_t.catatan, pr.no_pr, pr.pegawai_id, rekomendasi_obat.pegawai_id, pr.nama_pegawai, rekomendasi_obat.nama_pegawai, diorder_oleh.nama_pegawai, peg_penerima.nama_pegawai, lookup_penerimaan.lookup_name, pr.catatan) a
  GROUP BY a.asal_transaksi, a.type_po, a.transaksi_id, a.tanggal_po, a.tgl_rekomendasi, a.no_transaksi, a.supplier_id, a.supplier_nama, a.ruangan_id, a.ruangan_nama, a.instalasi_nama, a.is_validasi, a.status_validasi, a.status_penerimaan, a.stat_penerimaan, a.payment_term, a.total_harga_po, a.tgl_rencanaterima, a.peg_mengetahui_id, a.peg_mengetahui, a.peg_menyetujui_id, a.peg_menyetujui, a.sub_total, a.total_discount, a.ppn_persen, a.ppn_nilai, a.total, a.lookup_id, a.payterm_id, a.diorder_oleh, a.diorder_oleh_nama, a.pajak_id, a.catatan1, a.catatan2, a.is_closing, a.instalasi_id, a.modified_count, a.last_modified_date, a.last_modified_by, a.peg_mengubah, a.tgl_perubahan, a.pegawai_validasi, a.is_verifikasi, a.tanggal_buat_po, a.diorder_id, a.diorder_nama, a.tgl_penerimaan, a.qty_po, a.status_po_id, a.status_po_nama, a.tgl_batal_po, a.catatan_batal_po, a.peg_penerima_nama, a.tgl_tercetak, a.po_consigment, a.po_cito, a.po_admin, a.catatan
UNION ALL
 SELECT a.asal_transaksi,
    a.type_po,
    a.transaksi_id,
    a.tanggal_po,
    a.tgl_rekomendasi,
    string_agg(a.nomor::text, ','::text) AS nomor,
    a.no_transaksi,
    a.supplier_id,
    a.supplier_nama,
    a.ruangan_id,
    a.ruangan_nama,
    a.instalasi_nama,
    a.is_validasi,
    a.status_validasi,
    a.status_penerimaan,
    a.stat_penerimaan,
    a.payment_term,
    a.total_harga_po,
    a.tgl_rencanaterima,
    a.peg_mengetahui_id,
    a.peg_mengetahui,
    a.peg_menyetujui_id,
    a.peg_menyetujui,
    a.sub_total,
    a.total_discount,
    a.ppn_persen,
    a.ppn_nilai,
    a.total,
    a.lookup_id,
    a.payterm_id,
    a.diorder_oleh,
    a.diorder_oleh_nama,
    a.pajak_id,
    a.catatan1,
    a.catatan2,
    a.is_closing,
    a.instalasi_id,
    a.modified_count,
    a.last_modified_date,
    a.last_modified_by,
    a.peg_mengubah,
    a.tgl_perubahan,
    a.pegawai_validasi,
    a.is_verifikasi,
    a.tanggal_buat_po,
    a.diorder_id,
    a.diorder_nama,
    a.tgl_penerimaan,
    a.qty_po,
    a.status_po_id,
    a.status_po_nama,
    a.tgl_batal_po,
    a.catatan_batal_po,
    a.peg_penerima_nama,
    a.tgl_tercetak,
    a.po_consigment,
    a.po_cito,
    a.po_admin,
    a.catatan
   FROM ( SELECT
                CASE
                    WHEN validasipobarang_t.is_manual = false AND validasipobarang_t.additional_data IS NOT NULL THEN 'PURCHASE REQUEST'::text
                    WHEN validasipobarang_t.is_manual = false THEN 'REKOMENDASI'::text
                    ELSE 'PO_MANUAL'::text
                END AS asal_transaksi,
            'barang'::text AS type_po,
            validasipobarang_t.validasipobarang_id AS transaksi_id,
            validasipobarang_t.tgl_validasi AS tanggal_po,
            COALESCE(rekomendasi_barang.tgl_rekomendasibarang, pr2.tgl_pr) AS tgl_rekomendasi,
                CASE
                    WHEN validasipobarang_t.is_manual = false AND validasipobarang_t.additional_data IS NOT NULL THEN pr.no_pr
                    WHEN validasipobarang_t.is_manual = false THEN rekomendasi_barang.no_rekomendasibarang
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
                    WHEN validasipobarang_t.is_validasi = false THEN 'Belum Validasi'::text
                    ELSE 'Sudah Validasi'::text
                END AS status_validasi,
            validasipobarang_t.status_penerimaan,
            lookup_penerimaan.lookup_name AS stat_penerimaan,
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
            lookup_penerimaan.lookup_name AS status_po_nama,
            validasipobarang_t.tgl_batal_po,
            btrim(validasipobarang_t.catatan) AS catatan_batal_po,
            peg_penerima.nama_pegawai AS peg_penerima_nama,
            validasipobarang_t.tgl_tercetak,
            'Tidak'::text AS po_consigment,
                CASE
                    WHEN validasipobarang_t.is_cito = true THEN 'Cito'::text
                    WHEN validasipobarang_t.is_cito = false THEN 'Reguler'::text
                    WHEN validasipobarang_t.is_cito IS NULL THEN 'Reguler'::text
                    ELSE NULL::text
                END AS po_cito,
                CASE
                    WHEN validasipobarang_t.is_admin = true THEN 'Ya'::text
                    WHEN validasipobarang_t.is_admin = false THEN 'Tidak'::text
                    WHEN validasipobarang_t.is_admin IS NULL THEN 'Tidak'::text
                    ELSE NULL::text
                END AS po_admin,
            pr.catatan
           FROM validasipobarang_t
             LEFT JOIN ( SELECT validasipobarangdetail_t_1.validasipobarangdetail_id,
                    validasipobarangdetail_t_1.validasipobarang_id,
                    validasipobarangdetail_t_1.qty_po
                   FROM validasipobarangdetail_t validasipobarangdetail_t_1) validasipobarangdetail_t ON validasipobarang_t.validasipobarang_id = validasipobarangdetail_t.validasipobarang_id
             LEFT JOIN ( SELECT supplier_m_1.supplier_id,
                    supplier_m_1.supplier_nama
                   FROM supplier_m supplier_m_1) supplier_m ON validasipobarang_t.supplier_id = supplier_m.supplier_id
             JOIN ( SELECT ruangan_m_1.ruangan_id,
                    ruangan_m_1.ruangan_nama,
                    ruangan_m_1.instalasi_id
                   FROM ruangan_m ruangan_m_1) ruangan_m ON validasipobarang_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT instalasi_m_1.instalasi_id,
                    instalasi_m_1.instalasi_nama
                   FROM instalasi_m instalasi_m_1) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN ( SELECT payterm_m_1.payterm_id,
                    payterm_m_1.jumlah_hari
                   FROM payterm_m payterm_m_1) payterm_m ON validasipobarang_t.payterm_id = payterm_m.payterm_id
             LEFT JOIN ( SELECT pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai
                   FROM pegawai_m) peg_mengetahui ON validasipobarang_t.peg_mengetahui_id = peg_mengetahui.pegawai_id
             LEFT JOIN ( SELECT pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai
                   FROM pegawai_m) peg_menyetujui ON validasipobarang_t.peg_menyetujui_id = peg_menyetujui.pegawai_id
             LEFT JOIN ( SELECT pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai
                   FROM pegawai_m) diorder_oleh ON validasipobarang_t.diorder_oleh = diorder_oleh.pegawai_id
             LEFT JOIN ( SELECT pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai
                   FROM pegawai_m) diorder ON validasipobarang_t.diorder_oleh = diorder.pegawai_id
             LEFT JOIN ( SELECT pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai
                   FROM pegawai_m) peg_validasi ON validasipobarang_t.peg_validasi_id = peg_validasi.pegawai_id
             LEFT JOIN ( SELECT loginpemakai_k_1.loginpemakai_id,
                    loginpemakai_k_1.pegawai_id
                   FROM loginpemakai_k loginpemakai_k_1) loginpemakai_k ON validasipobarang_t.last_modified_by = loginpemakai_k.loginpemakai_id
             LEFT JOIN ( SELECT pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai
                   FROM pegawai_m) peg_mengubah ON loginpemakai_k.pegawai_id = peg_mengubah.pegawai_id
             LEFT JOIN ( SELECT pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai
                   FROM pegawai_m) peg_penerima ON validasipobarang_t.peg_penerima_id = peg_penerima.pegawai_id
             LEFT JOIN ( SELECT rekomendasibarang_t.no_rekomendasibarang,
                    rekomendasibarang_t.tgl_rekomendasibarang,
                    validasipobarangdetail_t_1.validasipobarang_id
                   FROM rekomendasibarang_t
                     JOIN ( SELECT a_1.rekomendasibarang_id,
                            a_1.rekomendasibarangdetail_id
                           FROM rekomendasibarangdetail_t a_1) rekomendasibarangdetail_t ON rekomendasibarang_t.rekomendasibarang_id = rekomendasibarangdetail_t.rekomendasibarang_id
                     JOIN ( SELECT a_1.validasipobarangdetail_id,
                            a_1.validasipobarang_id,
                            a_1.rekomendasibarangdetail_id
                           FROM validasipobarangdetail_t a_1) validasipobarangdetail_t_1 ON rekomendasibarangdetail_t.rekomendasibarangdetail_id = validasipobarangdetail_t_1.rekomendasibarangdetail_id
                  GROUP BY rekomendasibarang_t.no_rekomendasibarang, rekomendasibarang_t.tgl_rekomendasibarang, validasipobarangdetail_t_1.validasipobarang_id) rekomendasi_barang ON validasipobarang_t.validasipobarang_id = rekomendasi_barang.validasipobarang_id
             LEFT JOIN ( SELECT purchasereqbrg_t.no_pr,
                    purchasereqbrg_t.tgl_pr,
                    validasipobarangdetail_t_1.validasipobarang_id,
                    purchasereqbrg_t.is_prcyto,
                    purchasereqbrgdetail_t.catatan
                   FROM purchasereqbrg_t
                     JOIN ( SELECT a_1.purchasereqbrg_id,
                            a_1.purchasereqbrgdetail_id,
                            a_1.catatan
                           FROM purchasereqbrgdetail_t a_1) purchasereqbrgdetail_t ON purchasereqbrg_t.purchasereqbrg_id = purchasereqbrgdetail_t.purchasereqbrg_id
                     JOIN ( SELECT a_1.purchasereqbrgdetail_id,
                            a_1.validasipobarang_id
                           FROM validasipobarangdetail_t a_1) validasipobarangdetail_t_1 ON purchasereqbrgdetail_t.purchasereqbrgdetail_id = validasipobarangdetail_t_1.purchasereqbrgdetail_id
                  WHERE purchasereqbrg_t.is_deleted = false
                  GROUP BY purchasereqbrg_t.is_prcyto, purchasereqbrg_t.no_pr, purchasereqbrg_t.tgl_pr, validasipobarangdetail_t_1.validasipobarang_id, purchasereqbrgdetail_t.catatan) pr ON validasipobarang_t.validasipobarang_id = pr.validasipobarang_id
             LEFT JOIN ( SELECT max(purchasereqbrg_t.tgl_pr) AS tgl_pr,
                    validasipobarangdetail_t_1.validasipobarang_id
                   FROM purchasereqbrg_t
                     JOIN ( SELECT a_1.purchasereqbrg_id,
                            a_1.purchasereqbrgdetail_id
                           FROM purchasereqbrgdetail_t a_1) purchasereqbrgdetail_t ON purchasereqbrg_t.purchasereqbrg_id = purchasereqbrgdetail_t.purchasereqbrg_id
                     JOIN ( SELECT a_1.purchasereqbrgdetail_id,
                            a_1.validasipobarang_id
                           FROM validasipobarangdetail_t a_1) validasipobarangdetail_t_1 ON purchasereqbrgdetail_t.purchasereqbrgdetail_id = validasipobarangdetail_t_1.purchasereqbrgdetail_id
                  WHERE purchasereqbrg_t.is_deleted = false
                  GROUP BY validasipobarangdetail_t_1.validasipobarang_id) pr2 ON validasipobarang_t.validasipobarang_id = pr2.validasipobarang_id
             LEFT JOIN ( SELECT validasipobarangdetail_t_1.validasipobarang_id,
                    sum(validasipobarangdetail_t_1.harga * validasipobarangdetail_t_1.qty_input::double precision * (validasipobarangdetail_t_1.discount / 100::double precision)) AS total_discount
                   FROM validasipobarangdetail_t validasipobarangdetail_t_1
                  WHERE validasipobarangdetail_t_1.is_deleted = false
                  GROUP BY validasipobarangdetail_t_1.validasipobarang_id) total_diskon ON validasipobarang_t.validasipobarang_id = total_diskon.validasipobarang_id
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name
                   FROM lookup_m) lookup_penerimaan ON validasipobarang_t.status_penerimaan = lookup_penerimaan.lookup_id
          WHERE validasipobarang_t.is_deleted = false
          GROUP BY pr.is_prcyto, pr.no_pr, rekomendasi_barang.no_rekomendasibarang, validasipobarang_t.additional_data, validasipobarang_t.is_manual, validasipobarang_t.validasipobarang_id, validasipobarang_t.tgl_validasi, rekomendasi_barang.tgl_rekomendasibarang, pr2.tgl_pr, validasipobarang_t.no_pobarang, validasipobarang_t.supplier_id, supplier_m.supplier_nama, validasipobarang_t.ruangan_id, ruangan_m.ruangan_nama, instalasi_m.instalasi_nama, validasipobarang_t.is_validasi, validasipobarang_t.status_penerimaan, payterm_m.jumlah_hari, validasipobarang_t.total, validasipobarang_t.tgl_rencanaterima, validasipobarang_t.peg_mengetahui_id, peg_mengetahui.nama_pegawai, validasipobarang_t.peg_menyetujui_id, peg_menyetujui.nama_pegawai, validasipobarang_t.sub_total, total_diskon.total_discount, validasipobarang_t.ppn_persen, validasipobarang_t.ppn_nilai, payterm_m.payterm_id, validasipobarang_t.diorder_oleh, diorder_oleh.nama_pegawai, validasipobarang_t.pajak_id, validasipobarang_t.catatan1, validasipobarang_t.catatan2, validasipobarang_t.is_closing, ruangan_m.instalasi_id, validasipobarang_t.modified_count, validasipobarang_t.last_modified_date, validasipobarang_t.last_modified_by, peg_mengubah.nama_pegawai, peg_validasi.nama_pegawai, validasipobarang_t.is_verifikasi, validasipobarang_t.created_date, diorder.pegawai_id, diorder.nama_pegawai, validasipobarang_t.tgl_batal_po, validasipobarang_t.catatan, peg_penerima.nama_pegawai, lookup_penerimaan.lookup_name, pr.catatan) a
  GROUP BY a.asal_transaksi, a.type_po, a.transaksi_id, a.tanggal_po, a.tgl_rekomendasi, a.no_transaksi, a.supplier_id, a.supplier_nama, a.ruangan_id, a.ruangan_nama, a.instalasi_nama, a.is_validasi, a.status_validasi, a.status_penerimaan, a.stat_penerimaan, a.payment_term, a.total_harga_po, a.tgl_rencanaterima, a.peg_mengetahui_id, a.peg_mengetahui, a.peg_menyetujui_id, a.peg_menyetujui, a.sub_total, a.total_discount, a.ppn_persen, a.ppn_nilai, a.total, a.lookup_id, a.payterm_id, a.diorder_oleh, a.diorder_oleh_nama, a.pajak_id, a.catatan1, a.catatan2, a.is_closing, a.instalasi_id, a.modified_count, a.last_modified_date, a.last_modified_by, a.peg_mengubah, a.tgl_perubahan, a.pegawai_validasi, a.is_verifikasi, a.tanggal_buat_po, a.diorder_id, a.diorder_nama, a.tgl_penerimaan, a.qty_po, a.status_po_id, a.status_po_nama, a.tgl_batal_po, a.catatan_batal_po, a.peg_penerima_nama, a.tgl_tercetak, a.po_consigment, a.po_cito, a.po_admin, a.catatan; ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221019_045849_migrate_hotfix_mergepo_infopo_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221019_045849_migrate_hotfix_mergepo_infopo_v cannot be reverted.\n";

        return false;
    }
    */
}

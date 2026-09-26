<?php

use yii\db\Migration;

/**
 * Class m211014_022423_migrate_infopo_v
 */
class m211014_022423_migrate_infopo_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    $this->execute('DROP VIEW if exists "public"."infopo_v";');
    
    $this->execute("
        CREATE VIEW \"public\".\"infopo_v\" AS  SELECT
        CASE
            WHEN validasipoobat_t.is_manual = false AND validasipoobat_t.additional_data IS NOT NULL THEN 'PURCHASE REQUEST'::text
            WHEN validasipoobat_t.is_manual = false THEN 'REKOMENDASI'::text
            ELSE 'PO_MANUAL'::text
        END AS asal_transaksi,
    'obat'::text AS type_po,
    validasipoobat_t.validasipoobat_id AS transaksi_id,
    validasipoobat_t.tgl_validasi AS tanggal_po,
    COALESCE(rekomendasi_obat.tgl_rekomendasiobat, pr.tgl_pr) AS tgl_rekomendasi,
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
    validasipoobat_t.created_date AS tanggal_buat_po
   FROM validasipoobat_t
     LEFT JOIN supplier_m ON validasipoobat_t.supplier_id = supplier_m.supplier_id
     JOIN ruangan_m ON validasipoobat_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN payterm_m ON validasipoobat_t.payterm_id = payterm_m.payterm_id
     LEFT JOIN pegawai_m peg_mengetahui ON validasipoobat_t.peg_mengetahui_id = peg_mengetahui.pegawai_id
     LEFT JOIN pegawai_m peg_menyetujui ON validasipoobat_t.peg_menyetujui_id = peg_menyetujui.pegawai_id
     LEFT JOIN pegawai_m diorder_oleh ON validasipoobat_t.diorder_oleh = diorder_oleh.pegawai_id
     LEFT JOIN pegawai_m peg_validasi ON validasipoobat_t.peg_validasi_id = peg_validasi.pegawai_id
     LEFT JOIN loginpemakai_k ON validasipoobat_t.last_modified_by = loginpemakai_k.loginpemakai_id
     LEFT JOIN pegawai_m peg_mengubah ON loginpemakai_k.pegawai_id = peg_mengubah.pegawai_id
     LEFT JOIN ( SELECT rekomendasiobat_t.no_rekomendasiobat,
            rekomendasiobat_t.tgl_rekomendasiobat,
            validasipoobatdetail_t.validasipoobat_id,
            loginpemakai_k_1.pegawai_id,
            pegawai_m.nama_pegawai
           FROM rekomendasiobat_t
             JOIN rekomendasiobatdetail_t ON rekomendasiobat_t.rekomendasiobat_id = rekomendasiobatdetail_t.rekomendasiobat_id
             JOIN validasipoobatdetail_t ON rekomendasiobatdetail_t.rekomendasiobatdetail_id = validasipoobatdetail_t.rekomendasiobatdetail_id
             LEFT JOIN loginpemakai_k loginpemakai_k_1 ON rekomendasiobat_t.created_by = loginpemakai_k_1.loginpemakai_id
             LEFT JOIN pegawai_m ON loginpemakai_k_1.pegawai_id = pegawai_m.pegawai_id
          GROUP BY rekomendasiobat_t.no_rekomendasiobat, rekomendasiobat_t.tgl_rekomendasiobat, validasipoobatdetail_t.validasipoobat_id, loginpemakai_k_1.pegawai_id, pegawai_m.nama_pegawai) rekomendasi_obat ON validasipoobat_t.validasipoobat_id = rekomendasi_obat.validasipoobat_id
     LEFT JOIN ( SELECT purchasereq_t.no_pr,
            purchasereq_t.tgl_pr::timestamp without time zone AS tgl_pr,
            validasipoobatdetail_t.validasipoobat_id,
            loginpemakai_k_1.pegawai_id,
            pegawai_m.nama_pegawai
           FROM purchasereq_t
             JOIN purchasereqdetail_t ON purchasereq_t.purchasereq_id = purchasereqdetail_t.purchasereq_id
             JOIN validasipoobatdetail_t ON purchasereqdetail_t.purchasereqdetail_id = validasipoobatdetail_t.purchasereqdetail_id
             LEFT JOIN loginpemakai_k loginpemakai_k_1 ON purchasereq_t.created_by = loginpemakai_k_1.loginpemakai_id
             LEFT JOIN pegawai_m ON loginpemakai_k_1.pegawai_id = pegawai_m.pegawai_id
          WHERE purchasereq_t.is_deleted = false
          GROUP BY purchasereq_t.no_pr, purchasereq_t.tgl_pr, validasipoobatdetail_t.validasipoobat_id, loginpemakai_k_1.pegawai_id, pegawai_m.nama_pegawai) pr ON validasipoobat_t.validasipoobat_id = pr.validasipoobat_id
     LEFT JOIN ( SELECT validasipoobatdetail_t.validasipoobat_id,
            sum(validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision * (validasipoobatdetail_t.discount / 100::double precision)) AS total_discount
           FROM validasipoobatdetail_t
          WHERE validasipoobatdetail_t.is_deleted = false
          GROUP BY validasipoobatdetail_t.validasipoobat_id) total_diskon ON validasipoobat_t.validasipoobat_id = total_diskon.validasipoobat_id
  WHERE validasipoobat_t.is_deleted = false
UNION ALL
 SELECT
        CASE
            WHEN validasipobarang_t.is_manual = false AND validasipobarang_t.additional_data IS NOT NULL THEN 'PURCHASE REQUEST'::text
            WHEN validasipobarang_t.is_manual = false THEN 'REKOMENDASI'::text
            ELSE 'PO_MANUAL'::text
        END AS asal_transaksi,
    'barang'::text AS type_po,
    validasipobarang_t.validasipobarang_id AS transaksi_id,
    validasipobarang_t.tgl_validasi AS tanggal_po,
    COALESCE(rekomendasi_barang.tgl_rekomendasibarang, pr.tgl_pr) AS tgl_rekomendasi,
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
    validasipobarang_t.created_date AS tanggal_buat_po
   FROM validasipobarang_t
     LEFT JOIN supplier_m ON validasipobarang_t.supplier_id = supplier_m.supplier_id
     JOIN ruangan_m ON validasipobarang_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN payterm_m ON validasipobarang_t.payterm_id = payterm_m.payterm_id
     LEFT JOIN pegawai_m peg_mengetahui ON validasipobarang_t.peg_mengetahui_id = peg_mengetahui.pegawai_id
     LEFT JOIN pegawai_m peg_menyetujui ON validasipobarang_t.peg_menyetujui_id = peg_menyetujui.pegawai_id
     LEFT JOIN pegawai_m diorder_oleh ON validasipobarang_t.diorder_oleh = diorder_oleh.pegawai_id
     LEFT JOIN pegawai_m peg_validasi ON validasipobarang_t.peg_validasi_id = peg_validasi.pegawai_id
     LEFT JOIN loginpemakai_k ON validasipobarang_t.last_modified_by = loginpemakai_k.loginpemakai_id
     LEFT JOIN pegawai_m peg_mengubah ON loginpemakai_k.pegawai_id = peg_mengubah.pegawai_id
     LEFT JOIN ( SELECT rekomendasibarang_t.no_rekomendasibarang,
            rekomendasibarang_t.tgl_rekomendasibarang,
            validasipobarangdetail_t.validasipobarang_id
           FROM rekomendasibarang_t
             JOIN rekomendasibarangdetail_t ON rekomendasibarang_t.rekomendasibarang_id = rekomendasibarangdetail_t.rekomendasibarang_id
             JOIN validasipobarangdetail_t ON rekomendasibarangdetail_t.rekomendasibarangdetail_id = validasipobarangdetail_t.rekomendasibarangdetail_id
          GROUP BY rekomendasibarang_t.no_rekomendasibarang, rekomendasibarang_t.tgl_rekomendasibarang, validasipobarangdetail_t.validasipobarang_id) rekomendasi_barang ON validasipobarang_t.validasipobarang_id = rekomendasi_barang.validasipobarang_id
     LEFT JOIN ( SELECT purchasereqbrg_t.no_pr,
            purchasereqbrg_t.tgl_pr::timestamp without time zone AS tgl_pr,
            validasipobarangdetail_t.validasipobarang_id
           FROM purchasereqbrg_t
             JOIN purchasereqbrgdetail_t ON purchasereqbrg_t.purchasereqbrg_id = purchasereqbrgdetail_t.purchasereqbrg_id
             JOIN validasipobarangdetail_t ON purchasereqbrgdetail_t.purchasereqbrgdetail_id = validasipobarangdetail_t.purchasereqbrgdetail_id
          WHERE purchasereqbrg_t.is_deleted = false
          GROUP BY purchasereqbrg_t.no_pr, purchasereqbrg_t.tgl_pr, validasipobarangdetail_t.validasipobarang_id) pr ON validasipobarang_t.validasipobarang_id = pr.validasipobarang_id
     LEFT JOIN ( SELECT validasipobarangdetail_t.validasipobarang_id,
            sum(validasipobarangdetail_t.harga * validasipobarangdetail_t.qty_input::double precision * (validasipobarangdetail_t.discount / 100::double precision)) AS total_discount
           FROM validasipobarangdetail_t
          WHERE validasipobarangdetail_t.is_deleted = false
          GROUP BY validasipobarangdetail_t.validasipobarang_id) total_diskon ON validasipobarang_t.validasipobarang_id = total_diskon.validasipobarang_id
  WHERE validasipobarang_t.is_deleted = false;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211014_022423_migrate_infopo_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211014_022423_migrate_infopo_v cannot be reverted.\n";

        return false;
    }
    */
}

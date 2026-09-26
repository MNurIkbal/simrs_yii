<?php

use yii\db\Migration;

/**
 * Class m200714_023223_migrate_mhkn_20200714
 */
class m200714_023223_migrate_mhkn_20200714 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('select public.deps_save_and_drop_dependencies(\'public\', \'penerimaansuppdetail_t\');');

        $this->execute('ALTER TABLE "public"."penerimaansuppdetail_t" 
                        ALTER COLUMN "diskon" TYPE decimal(15,2) USING "diskon"::decimal(15,2);');

        $this->execute('select public.deps_restore_dependencies(\'public\', \'penerimaansuppdetail_t\');');

        $this->execute('DROP VIEW if exists "public"."infopodetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopodetail_v\" AS  SELECT 'obat'::text AS jenis,
        CASE
            WHEN (validasipoobat_t.is_manual = false) THEN 'REKOMENDASI'::text
            ELSE 'PO_MANUAL'::text
        END AS asal_transaksi,
    validasipoobat_t.validasipoobat_id AS transaksi_id,
    validasipoobat_t.tgl_validasi AS tanggal_po,
    rekomendasiobat_t.no_rekomendasiobat AS nomor,
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
    validasipoobat_t.diorder_oleh,
    validasipoobat_t.peg_mengetahui_id,
    peg_mengetahui.nama_pegawai AS peg_mengetahui,
    validasipoobat_t.peg_menyetujui_id,
    peg_menyetujui.nama_pegawai AS peg_menyetujui,
    validasipoobat_t.sub_total,
    validasipoobat_t.total_discount,
    validasipoobat_t.ppn_persen,
    validasipoobat_t.ppn_nilai,
    validasipoobat_t.total,
    validasipoobatdetail_t.obatalkes_id AS obat_barang_id,
    obatalkes_m.obatalkes_nama AS obat_barang_nama,
    rekomendasiobatdetail_t.rekomendasi AS qty_rekomendasi,
    validasipoobatdetail_t.qty_po AS qty,
    validasipoobatdetail_t.qty_penerimaan,
    validasipoobatdetail_t.s_konversiobt_id,
    concat('1 ', besar.satuanunit_nama, ' - ', satuankonversi_m.nilai_konversi, ' ', kecil.satuanunit_nama) AS satuan,
    validasipoobatdetail_t.harga,
    validasipoobatdetail_t.discount,
    validasipoobatdetail_t.discount_rp,
    validasipoobatdetail_t.jumlah,
    (((COALESCE(validasipoobatdetail_t.qty_input, 0) - COALESCE(validasipoobatdetail_t.qty_penerimaan, 0)) - COALESCE(validasipoobatdetail_t.qty_closing, 0)) + COALESCE(validasipoobatdetail_t.qty_retur, 0)) AS po_balance,
        CASE
            WHEN (validasipoobatdetail_t.is_completed = false) THEN '-'::text
            ELSE 'Completed'::text
        END AS is_completed,
    validasipoobatdetail_t.qty_input,
    true AS is_obat,
    validasipoobatdetail_t.validasipoobatdetail_id AS id_detail,
    true AS is_kadaluarsa,
    validasipoobat_t.is_verifikasi,
    kecil.satuanunit_nama AS satuan_kecil,
    obatalkes_m.obatalkes_kode AS kode_item,
    supplier_m.no_tlp AS no_telepon,
    supplier_m.no_fax,
    besar.satuanunit_nama AS satuan_besar
   FROM (((((((((((((validasipoobat_t
     JOIN validasipoobatdetail_t ON ((validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id)))
     LEFT JOIN rekomendasiobatdetail_t ON ((validasipoobatdetail_t.rekomendasiobatdetail_id = rekomendasiobatdetail_t.rekomendasiobatdetail_id)))
     LEFT JOIN rekomendasiobat_t ON ((rekomendasiobatdetail_t.rekomendasiobat_id = rekomendasiobat_t.rekomendasiobat_id)))
     JOIN supplier_m ON ((validasipoobat_t.supplier_id = supplier_m.supplier_id)))
     JOIN ruangan_m ON ((validasipoobat_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN obatalkes_m ON ((validasipoobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN satuankonversi_m ON ((validasipoobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id)))
     LEFT JOIN satuanunit_m kecil ON ((satuankonversi_m.satuankecil_id = kecil.satuanunit_id)))
     LEFT JOIN satuanunit_m besar ON ((satuankonversi_m.satuanbesar_id = besar.satuanunit_id)))
     LEFT JOIN payterm_m ON ((validasipoobat_t.payterm_id = payterm_m.payterm_id)))
     LEFT JOIN pegawai_m peg_mengetahui ON ((validasipoobat_t.peg_mengetahui_id = peg_mengetahui.pegawai_id)))
     LEFT JOIN pegawai_m peg_menyetujui ON ((validasipoobat_t.peg_menyetujui_id = peg_menyetujui.pegawai_id)))
  WHERE (validasipoobat_t.is_deleted = false)
UNION ALL
 SELECT 'barang'::text AS jenis,
        CASE
            WHEN (validasipobarang_t.is_manual = false) THEN 'REKOMENDASI'::text
            ELSE 'PO_MANUAL'::text
        END AS asal_transaksi,
    validasipobarang_t.validasipobarang_id AS transaksi_id,
    validasipobarang_t.tgl_validasi AS tanggal_po,
    rekomendasibarang_t.no_rekomendasibarang AS nomor,
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
    validasipobarang_t.diorder_oleh,
    validasipobarang_t.peg_mengetahui_id,
    peg_mengetahui.nama_pegawai AS peg_mengetahui,
    validasipobarang_t.peg_menyetujui_id,
    peg_menyetujui.nama_pegawai AS peg_menyetujui,
    validasipobarang_t.sub_total,
    validasipobarang_t.total_discount,
    validasipobarang_t.ppn_persen,
    validasipobarang_t.ppn_nilai,
    validasipobarang_t.total,
    validasipobarangdetail_t.barang_id AS obat_barang_id,
    barang_m.barang_nama AS obat_barang_nama,
    rekomendasibarangdetail_t.rekomendasi AS qty_rekomendasi,
    validasipobarangdetail_t.qty_po AS qty,
    validasipobarangdetail_t.qty_penerimaan,
    validasipobarangdetail_t.s_konversibrg_id AS s_konversiobt_id,
    concat('1 ', besar.satuanunit_nama, ' - ', satuankonversibrg_m.nilai_konversi, ' ', kecil.satuanunit_nama) AS satuan,
    validasipobarangdetail_t.harga,
    validasipobarangdetail_t.discount,
    validasipobarangdetail_t.discount_rp,
    validasipobarangdetail_t.jumlah,
    (((COALESCE(validasipobarangdetail_t.qty_input, 0) - COALESCE(validasipobarangdetail_t.qty_penerimaan, 0)) - COALESCE(validasipobarangdetail_t.qty_closing, 0)) + COALESCE(validasipobarangdetail_t.qty_retur, 0)) AS po_balance,
        CASE
            WHEN (validasipobarangdetail_t.is_completed = false) THEN '-'::text
            ELSE 'Completed'::text
        END AS is_completed,
    validasipobarangdetail_t.qty_input,
    false AS is_obat,
    validasipobarangdetail_t.validasipobarangdetail_id AS id_detail,
    barang_m.is_kadaluarsa,
    validasipobarang_t.is_verifikasi,
    kecil.satuanunit_nama AS satuan_kecil,
    barang_m.barang_kode AS kode_item,
    supplier_m.no_tlp AS no_telepon,
    supplier_m.no_fax,
    besar.satuanunit_nama AS satuan_besar
   FROM (((((((((((((validasipobarang_t
     JOIN validasipobarangdetail_t ON ((validasipobarang_t.validasipobarang_id = validasipobarangdetail_t.validasipobarang_id)))
     LEFT JOIN rekomendasibarangdetail_t ON ((validasipobarangdetail_t.rekomendasibarangdetail_id = rekomendasibarangdetail_t.rekomendasibarangdetail_id)))
     LEFT JOIN rekomendasibarang_t ON ((rekomendasibarangdetail_t.rekomendasibarang_id = rekomendasibarang_t.rekomendasibarang_id)))
     JOIN supplier_m ON ((validasipobarang_t.supplier_id = supplier_m.supplier_id)))
     JOIN ruangan_m ON ((validasipobarang_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN barang_m ON ((validasipobarangdetail_t.barang_id = barang_m.barang_id)))
     LEFT JOIN satuankonversibrg_m ON ((validasipobarangdetail_t.s_konversibrg_id = satuankonversibrg_m.satuankonversibrg_id)))
     LEFT JOIN satuanunit_m kecil ON ((satuankonversibrg_m.satuankecil_id = kecil.satuanunit_id)))
     LEFT JOIN satuanunit_m besar ON ((satuankonversibrg_m.satuanbesar_id = besar.satuanunit_id)))
     LEFT JOIN payterm_m ON ((validasipobarang_t.payterm_id = payterm_m.payterm_id)))
     LEFT JOIN pegawai_m peg_mengetahui ON ((validasipobarang_t.peg_mengetahui_id = peg_mengetahui.pegawai_id)))
     LEFT JOIN pegawai_m peg_menyetujui ON ((validasipobarang_t.peg_menyetujui_id = peg_menyetujui.pegawai_id)))
  WHERE (validasipobarang_t.is_deleted = false);");

        $this->execute("
            CREATE VIEW \"public\".\"infopasienbelumbayar_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.nama_pasien,
    pasien_m.no_rekam_medik,
    pasien_m.tanggal_lahir,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN cb_1.carabayar_nama
            ELSE cb_2.carabayar_nama
        END AS carabayar_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pj_1.penjamin_nama
            ELSE pj_1.penjamin_nama
        END AS penjamin_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN dr_1.nama_pegawai
            ELSE dr_2.nama_pegawai
        END AS nama_dokter,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ins_1.instalasi_nama
            ELSE ins_1.instalasi_nama
        END AS instalasi_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruang_1.ruangan_nama
            ELSE ruang_2.ruangan_nama
        END AS ruangan_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN fgetnamalookup((pendaftaran_t.status_periksa)::integer)
            ELSE fgetnamalookup(pasienadmisi_t.status_ranap)
        END AS status_periksa,
    0 AS total_tagihan,
    0 AS uang_masuk,
    0 AS sisa_tagihan
   FROM (((((((((((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN carabayar_m cb_1 ON ((pendaftaran_t.carabayar_id = cb_1.carabayar_id)))
     LEFT JOIN carabayar_m cb_2 ON ((pasienadmisi_t.carabayar_id = cb_2.carabayar_id)))
     LEFT JOIN penjamin_m pj_1 ON ((pendaftaran_t.penjamin_id = pj_1.penjamin_id)))
     LEFT JOIN penjamin_m pj_2 ON ((pasienadmisi_t.penjamin_id = pj_2.penjamin_id)))
     LEFT JOIN pegawai_m dr_1 ON ((pendaftaran_t.pegawai_id = dr_1.pegawai_id)))
     LEFT JOIN pegawai_m dr_2 ON ((pasienadmisi_t.pegawai_id = dr_2.pegawai_id)))
     LEFT JOIN ruangan_m ruang_1 ON ((pendaftaran_t.ruangan_id = ruang_1.ruangan_id)))
     LEFT JOIN ruangan_m ruang_2 ON ((pasienadmisi_t.ruangan_id = ruang_2.ruangan_id)))
     LEFT JOIN instalasi_m ins_1 ON ((ruang_1.instalasi_id = ins_1.instalasi_id)))
     LEFT JOIN instalasi_m ins_2 ON ((ruang_2.instalasi_id = dr_2.pegawai_id)))
     LEFT JOIN ( SELECT tindakanpelayanan_t.pendaftaran_id,
            sum(tindakanpelayanan_t.tarif_tindakan) AS total_tindakan
           FROM tindakanpelayanan_t
          GROUP BY tindakanpelayanan_t.pendaftaran_id) tindakan ON ((pendaftaran_t.pendaftaran_id = tindakan.pendaftaran_id)));");
        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200714_023223_migrate_mhkn_20200714 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200714_023223_migrate_mhkn_20200714 cannot be reverted.\n";

        return false;
    }
    */
}

<?php

use yii\db\Migration;

/**
 * Class m220103_033815_migrate_US2125_infopodetail_v
 */
class m220103_033815_migrate_US2125_infopodetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
  	   	$this->execute('DROP VIEW if exists public.infopodetail_v;');
		
         $this->execute("
             CREATE VIEW \"public\".\"infopodetail_v\" AS
		 ( SELECT 'obat'::text AS jenis,
		         CASE
		             WHEN ((validasipoobat_t.is_manual = false) AND (validasipoobat_t.additional_data IS NOT NULL)) THEN 'PURCHASE REQUEST'::text
		             WHEN (validasipoobat_t.is_manual = false) THEN 'REKOMENDASI'::text
		             ELSE 'PO_MANUAL'::text
		         END AS asal_transaksi,
		     validasipoobat_t.validasipoobat_id AS transaksi_id,
		     validasipoobat_t.tgl_validasi AS tanggal_po,
		         CASE
		             WHEN ((validasipoobat_t.is_manual = false) AND (validasipoobat_t.additional_data IS NOT NULL)) THEN pr.no_pr
		             WHEN (validasipoobat_t.is_manual = false) THEN rekomendasiobat_t.no_rekomendasiobat
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
		     validasipoobat_t.diorder_oleh,
		     validasipoobat_t.peg_mengetahui_id,
		     peg_mengetahui.nama_pegawai AS peg_mengetahui,
		     validasipoobat_t.peg_menyetujui_id,
		     peg_menyetujui.nama_pegawai AS peg_menyetujui,
		     validasipoobat_t.sub_total,
		     validasipoobat_t.total_discount,
		     pajak_m.pajak_persen AS ppn_persen,
		     validasipoobat_t.ppn_nilai,
		     validasipoobat_t.total,
		     validasipoobatdetail_t.obatalkes_id AS obat_barang_id,
		     obatalkes_m.obatalkes_nama AS obat_barang_nama,
		         CASE
		             WHEN ((validasipoobat_t.is_manual = false) AND (validasipoobat_t.additional_data IS NOT NULL)) THEN pr.qty_input
		             WHEN (validasipoobat_t.is_manual = false) THEN (rekomendasiobatdetail_t.rekomendasi)::numeric
		             ELSE (0)::numeric
		         END AS qty_rekomendasi,
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
		     besar.satuanunit_nama AS satuan_besar,
		     pr.satuan AS satuan_pr,
		     pr.satuan_konversi AS satuan_konversi_pr,
		         CASE
		             WHEN (penerimaan.validasipoobatdetail_id IS NULL) THEN false
		             ELSE true
		         END AS is_terima,
		     besar.satuanunit_id AS satuan_besar_id,
		     pr.no_pr,
		     validasipoobatdetail_t.is_active,
		     validasipoobatdetail_t.is_disc_nominal,
		     ((((validasipoobatdetail_t.qty_input)::double precision * validasipoobatdetail_t.harga) - validasipoobatdetail_t.discount_rp) + ((((validasipoobatdetail_t.qty_input)::double precision * validasipoobatdetail_t.harga) - validasipoobatdetail_t.discount_rp) * ((pajak_m.pajak_persen)::double precision / (100)::double precision))) AS jumlah_with_ppn
		    FROM ((((((((((((((((validasipoobat_t
		      JOIN validasipoobatdetail_t ON ((validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id)))
		      LEFT JOIN rekomendasiobatdetail_t ON ((validasipoobatdetail_t.rekomendasiobatdetail_id = rekomendasiobatdetail_t.rekomendasiobatdetail_id)))
		      LEFT JOIN rekomendasiobat_t ON ((rekomendasiobatdetail_t.rekomendasiobat_id = rekomendasiobat_t.rekomendasiobat_id)))
		      LEFT JOIN supplier_m ON ((validasipoobat_t.supplier_id = supplier_m.supplier_id)))
		      JOIN ruangan_m ON ((validasipoobat_t.ruangan_id = ruangan_m.ruangan_id)))
		      JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
		      JOIN obatalkes_m ON ((validasipoobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
		      LEFT JOIN ( SELECT penerimaanobatdetail_t.validasipoobatdetail_id
		            FROM penerimaanobatdetail_t
		           GROUP BY penerimaanobatdetail_t.validasipoobatdetail_id) penerimaan ON ((validasipoobatdetail_t.validasipoobatdetail_id = penerimaan.validasipoobatdetail_id)))
		      LEFT JOIN satuankonversi_m ON ((validasipoobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id)))
		      LEFT JOIN satuanunit_m kecil ON ((satuankonversi_m.satuankecil_id = kecil.satuanunit_id)))
		      LEFT JOIN satuanunit_m besar ON ((satuankonversi_m.satuanbesar_id = besar.satuanunit_id)))
		      LEFT JOIN payterm_m ON ((validasipoobat_t.payterm_id = payterm_m.payterm_id)))
		      LEFT JOIN pegawai_m peg_mengetahui ON ((validasipoobat_t.peg_mengetahui_id = peg_mengetahui.pegawai_id)))
		      LEFT JOIN pegawai_m peg_menyetujui ON ((validasipoobat_t.peg_menyetujui_id = peg_menyetujui.pegawai_id)))
		      LEFT JOIN pajak_m ON ((validasipoobat_t.pajak_id = pajak_m.pajak_id)))
		      LEFT JOIN ( SELECT purchasereq_t.no_pr,
		             (purchasereq_t.tgl_pr)::timestamp without time zone AS tgl_pr,
		             purchasereqdetail_t.qty_input,
		             kecil_1.satuanunit_nama AS satuan,
		             besar_1.satuanunit_nama AS satuan_konversi,
		             purchasereqdetail_t.obatalkes_id,
		             purchasereqdetail_t.status,
		             purchasereqdetail_t.purchasereqdetail_id
		            FROM (((purchasereq_t
		              JOIN purchasereqdetail_t ON ((purchasereq_t.purchasereq_id = purchasereqdetail_t.purchasereq_id)))
		              LEFT JOIN satuanunit_m kecil_1 ON ((purchasereqdetail_t.satuan_id = kecil_1.satuanunit_id)))
		              LEFT JOIN satuanunit_m besar_1 ON ((purchasereqdetail_t.satuankonversi_id = besar_1.satuanunit_id)))
		           WHERE (purchasereq_t.is_deleted = false)
		           GROUP BY purchasereq_t.no_pr, purchasereq_t.tgl_pr, purchasereqdetail_t.qty_input, kecil_1.satuanunit_nama, besar_1.satuanunit_nama, purchasereqdetail_t.obatalkes_id, purchasereqdetail_t.status, purchasereqdetail_t.purchasereqdetail_id) pr ON ((validasipoobatdetail_t.purchasereqdetail_id = pr.purchasereqdetail_id)))
		   WHERE (((validasipoobat_t.is_deleted = false) AND (validasipoobatdetail_t.is_deleted = false)) OR ((validasipoobat_t.status_penerimaan = 575) AND (validasipoobatdetail_t.is_deleted = true)))
		   ORDER BY pr.no_pr)
		 UNION ALL
		 ( SELECT 'barang'::text AS jenis,
		         CASE
		             WHEN ((validasipobarang_t.is_manual = false) AND (validasipobarang_t.additional_data IS NOT NULL)) THEN 'PURCHASE REQUEST'::text
		             WHEN (validasipobarang_t.is_manual = false) THEN 'REKOMENDASI'::text
		             ELSE 'PO_MANUAL'::text
		         END AS asal_transaksi,
		     validasipobarang_t.validasipobarang_id AS transaksi_id,
		     validasipobarang_t.tgl_validasi AS tanggal_po,
		         CASE
		             WHEN ((validasipobarang_t.is_manual = false) AND (validasipobarang_t.additional_data IS NOT NULL)) THEN pr.no_pr
		             WHEN (validasipobarang_t.is_manual = false) THEN rekomendasibarang_t.no_rekomendasibarang
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
		     validasipobarang_t.diorder_oleh,
		     validasipobarang_t.peg_mengetahui_id,
		     peg_mengetahui.nama_pegawai AS peg_mengetahui,
		     validasipobarang_t.peg_menyetujui_id,
		     peg_menyetujui.nama_pegawai AS peg_menyetujui,
		     validasipobarang_t.sub_total,
		     validasipobarang_t.total_discount,
		     pajak_m.pajak_persen AS ppn_persen,
		     validasipobarang_t.ppn_nilai,
		     validasipobarang_t.total,
		     validasipobarangdetail_t.barang_id AS obat_barang_id,
		     barang_m.barang_nama AS obat_barang_nama,
		         CASE
		             WHEN ((validasipobarang_t.is_manual = false) AND (validasipobarang_t.additional_data IS NOT NULL)) THEN pr.qty_input
		             WHEN (validasipobarang_t.is_manual = false) THEN (rekomendasibarangdetail_t.rekomendasi)::numeric
		             ELSE (0)::numeric
		         END AS qty_rekomendasi,
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
		     besar.satuanunit_nama AS satuan_besar,
		     NULL::character varying AS satuan_pr,
		     NULL::character varying AS satuan_konversi_pr,
		     false AS is_terima,
		     besar.satuanunit_id AS satuan_besar_id,
		     pr.no_pr,
		     validasipobarangdetail_t.is_active,
		     validasipobarangdetail_t.is_disc_nominal,
		     ((((validasipobarangdetail_t.qty_input)::double precision * validasipobarangdetail_t.harga) - validasipobarangdetail_t.discount_rp) + ((((validasipobarangdetail_t.qty_input)::double precision * validasipobarangdetail_t.harga) - validasipobarangdetail_t.discount_rp) * ((pajak_m.pajak_persen)::double precision / (100)::double precision))) AS jumlah_with_ppn
		    FROM (((((((((((((((validasipobarang_t
		      JOIN validasipobarangdetail_t ON ((validasipobarang_t.validasipobarang_id = validasipobarangdetail_t.validasipobarang_id)))
		      LEFT JOIN rekomendasibarangdetail_t ON ((validasipobarangdetail_t.rekomendasibarangdetail_id = rekomendasibarangdetail_t.rekomendasibarangdetail_id)))
		      LEFT JOIN rekomendasibarang_t ON ((rekomendasibarangdetail_t.rekomendasibarang_id = rekomendasibarang_t.rekomendasibarang_id)))
		      LEFT JOIN supplier_m ON ((validasipobarang_t.supplier_id = supplier_m.supplier_id)))
		      JOIN ruangan_m ON ((validasipobarang_t.ruangan_id = ruangan_m.ruangan_id)))
		      JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
		      JOIN barang_m ON ((validasipobarangdetail_t.barang_id = barang_m.barang_id)))
		      LEFT JOIN satuankonversibrg_m ON ((validasipobarangdetail_t.s_konversibrg_id = satuankonversibrg_m.satuankonversibrg_id)))
		      LEFT JOIN satuanunit_m kecil ON ((satuankonversibrg_m.satuankecil_id = kecil.satuanunit_id)))
		      LEFT JOIN satuanunit_m besar ON ((satuankonversibrg_m.satuanbesar_id = besar.satuanunit_id)))
		      LEFT JOIN payterm_m ON ((validasipobarang_t.payterm_id = payterm_m.payterm_id)))
		      LEFT JOIN pegawai_m peg_mengetahui ON ((validasipobarang_t.peg_mengetahui_id = peg_mengetahui.pegawai_id)))
		      LEFT JOIN pegawai_m peg_menyetujui ON ((validasipobarang_t.peg_menyetujui_id = peg_menyetujui.pegawai_id)))
		      LEFT JOIN pajak_m ON ((validasipobarang_t.pajak_id = pajak_m.pajak_id)))
		      LEFT JOIN ( SELECT purchasereqbrg_t.no_pr,
		             (purchasereqbrg_t.tgl_pr)::timestamp without time zone AS tgl_pr,
		             purchasereqbrgdetail_t.qty_input,
		             kecil_1.satuanunit_nama AS satuan,
		             besar_1.satuanunit_nama AS satuan_konversi,
		             purchasereqbrgdetail_t.barang_id,
		             purchasereqbrgdetail_t.status,
		             purchasereqbrgdetail_t.purchasereqbrgdetail_id
		            FROM (((purchasereqbrg_t
		              JOIN purchasereqbrgdetail_t ON ((purchasereqbrg_t.purchasereqbrg_id = purchasereqbrgdetail_t.purchasereqbrg_id)))
		              LEFT JOIN satuanunit_m kecil_1 ON ((purchasereqbrgdetail_t.satuan_id = kecil_1.satuanunit_id)))
		              LEFT JOIN satuanunit_m besar_1 ON ((purchasereqbrgdetail_t.satuankonversi_id = besar_1.satuanunit_id)))
		           WHERE (purchasereqbrg_t.is_deleted = false)
		           GROUP BY purchasereqbrg_t.no_pr, purchasereqbrg_t.tgl_pr, purchasereqbrgdetail_t.qty_input, kecil_1.satuanunit_nama, besar_1.satuanunit_nama, purchasereqbrgdetail_t.barang_id, purchasereqbrgdetail_t.status, purchasereqbrgdetail_t.purchasereqbrgdetail_id) pr ON ((validasipobarangdetail_t.purchasereqbrgdetail_id = pr.purchasereqbrgdetail_id)))
		   WHERE (((validasipobarang_t.is_deleted = false) AND (validasipobarangdetail_t.is_deleted = false)) OR ((validasipobarang_t.status_penerimaan = 575) AND (validasipobarangdetail_t.is_deleted = true)))
		   ORDER BY pr.no_pr);");
    
             $this->execute('
                 ALTER TABLE public.infopodetail_v OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220103_033815_migrate_US2125_infopodetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220103_033815_migrate_US2125_infopodetail_v cannot be reverted.\n";

        return false;
    }
    */
}

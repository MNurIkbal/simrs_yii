-- public.laporanpenerimaanbarang_v source

CREATE OR REPLACE VIEW public.laporanpenerimaanbarang_v
AS SELECT supplier_m.supplier_kode,
    supplier_m.supplier_nama,
    terima.tgl_penerimaan,
    terima.no_penerimaan,
    terima.nama_pegawai AS diterima_oleh,
        CASE
            WHEN terima.is_verifikasi::integer = 1 THEN 1
            WHEN terima.is_verifikasi::integer = 2 THEN 3
            ELSE 2
        END AS status_penerimaan_id,
        CASE
            WHEN terima.is_verifikasi::integer = 1 THEN 'Sudah diverifikasi'::text
            WHEN terima.is_verifikasi::integer = 2 THEN 'Dibatalkan'::text
            ELSE 'Belum diverifikasi'::text
        END AS status_penerimaan,
    terima.tgl_po,
    terima.tgl_validasi_po,
    terima.nomor_po,
    barang_m.barang_kode AS kode_item,
    barang_m.barang_nama,
    kelompokbarang_m.kelompokbarang_nama,
    terima.qty_po,
    besar.satuanunit_nama AS satuan_po,
    terima.qty_diterima,
    besar.satuanunit_nama AS satuan_terima,
    terima.po_balance,
        CASE
            WHEN terima.po_balance IS NULL THEN NULL::character varying
            ELSE besar.satuanunit_nama
        END AS satuan_balance,
    satuankonversibrg_m.nilai_konversi,
    kecil.satuanunit_nama AS satuan_kecil,
    terima.harga,
    terima.discount,
    terima.ppn_persen,
    terima.sub_total,
    terima.total,
    terima.catatan1 AS catatan_po,
    terima.tgl_pr,
    terima.no_pr,
    terima.no_batch,
    terima.tgl_kadaluarsa,
    terima.no_suratjalan,
    terima.no_faktur,
    terima.qty_diterima::double precision * satuankonversibrg_m.nilai_konversi AS qty_konversi,
    supplier_m.supplier_id,
    terima.penerimaanbarang_id,
    terima.payterm_nama,
    terima.payterm_id,
    barang_m.barang_harganetto
   FROM ( SELECT penerimaanbarang_t.penerimaanbarang_id,
            penerimaanbarang_t.tgl_penerimaan,
            penerimaanbarang_t.no_penerimaan,
            validasipobarang_t.no_pobarang AS nomor_po,
            penerimaanbarang_t.supplier_id,
            penerimaanbarang_t.no_suratjalan,
            penerimaanbarang_t.tgl_suratjalan,
            penerimaanbarang_t.no_faktur,
            penerimaanbarang_t.diterima_oleh,
            penerimaanbarang_t.upload_berkas,
            penerimaanbarang_t.catatan_berkas,
            penerimaanbarang_t.catatan,
            penerimaanbarang_t.peg_mengetahui,
            penerimaanbarang_t.peg_menyetujui,
            penerimaanbarangdetail_t.penerimaanbarangdetail_id,
            penerimaanbarangdetail_t.barang_id,
            penerimaanbarangdetail_t.qty_po,
            penerimaanbarangdetail_t.qty_diterima,
            COALESCE(validasipobarangdetail_t.qty_sisa, 0) + COALESCE(validasipobarangdetail_t.qty_retur, 0) AS po_balance,
            penerimaanbarangdetail_t.tgl_kadaluarsa,
            penerimaanbarangdetail_t.no_batch,
            penerimaanbarangdetail_t.s_konversibrg_id,
            penerimaanbarangdetail_t.keterangan,
            penerimaanbarang_t.is_verifikasi,
            penerimaanbarangdetail_t.validasipobarangdetail_id,
            penerimaanbarangdetail_t.harga,
            penerimaanbarangdetail_t.discount,
            penerimaanbarangdetail_t.discount_rp,
            penerimaanbarangdetail_t.jumlah,
            validasipobarang_t.pajak_id,
            penerimaanbarangdetail_t.qty_diterima::double precision * penerimaanbarangdetail_t.harga AS sub_total,
            validasipobarang_t.total_discount,
            pajak_m.pajak_persen AS ppn_persen,
            validasipobarang_t.ppn_nilai,
            validasipobarang_t.created_date AS tgl_po,
            validasipobarang_t.tgl_validasi AS tgl_validasi_po,
            penerimaanbarangdetail_t.qty_diterima::double precision * penerimaanbarangdetail_t.harga - penerimaanbarangdetail_t.discount_rp + (penerimaanbarangdetail_t.qty_diterima::double precision * penerimaanbarangdetail_t.harga - penerimaanbarangdetail_t.discount_rp) * (pajak_m.pajak_persen::double precision / 100::double precision) AS total,
            purchasereqbrg_t.no_pr,
            purchasereqbrg_t.tgl_pr,
            validasipobarang_t.catatan1,
            validasipobarang_t.catatan2,
            pegawai_m.nama_pegawai,
            payterm_m.payterm_nama,
            payterm_m.payterm_id
           FROM penerimaanbarang_t
             JOIN penerimaanbarangdetail_t ON penerimaanbarang_t.penerimaanbarang_id = penerimaanbarangdetail_t.penerimaanbarang_id
             LEFT JOIN validasipobarang_t ON penerimaanbarang_t.validasipobarang_id = validasipobarang_t.validasipobarang_id
             JOIN validasipobarangdetail_t ON penerimaanbarangdetail_t.validasipobarangdetail_id = validasipobarangdetail_t.validasipobarangdetail_id
             LEFT JOIN purchasereqbrgdetail_t ON validasipobarangdetail_t.purchasereqbrgdetail_id = purchasereqbrgdetail_t.purchasereqbrgdetail_id
             LEFT JOIN purchasereqbrg_t ON purchasereqbrgdetail_t.purchasereqbrg_id = purchasereqbrg_t.purchasereqbrg_id
             LEFT JOIN pegawai_m ON penerimaanbarang_t.diterima_oleh = pegawai_m.pegawai_id
             LEFT JOIN pajak_m ON pajak_m.pajak_id = validasipobarang_t.pajak_id
             LEFT JOIN payterm_m ON payterm_m.payterm_id = validasipobarang_t.payterm_id AND payterm_m.is_deleted = false
          WHERE penerimaanbarangdetail_t.is_deleted = false AND penerimaanbarang_t.is_deleted = false
        UNION ALL
         SELECT penerimaansupp_t.penerimaansupp_id,
            penerimaansupp_t.tgl_penerimaan,
            penerimaansupp_t.no_penerimaan,
            NULL::character varying AS nomor_po,
            penerimaansupp_t.supplier_id,
            penerimaansupp_t.no_suratjalan,
            NULL::timestamp without time zone AS tgl_suratjalan,
            penerimaansupp_t.no_faktur,
            penerimaansupp_t.peg_penerima_id,
            NULL::text AS upload_berkas,
            NULL::text AS catatan_berkas,
            NULL::text AS catatan,
            penerimaansupp_t.peg_mengetahui,
            penerimaansupp_t.peg_menyetujui,
            penerimaansuppdetail_t.penerimaansuppdetail_id,
            penerimaansuppdetail_t.barang_id,
            0 AS qty_po,
            penerimaansuppdetail_t.qty_besar,
            NULL::integer AS po_balance,
            penerimaansuppdetail_t.tgl_kadaluarsa,
            penerimaansuppdetail_t.no_batch,
            penerimaansuppdetail_t.satuankonversi_id,
            penerimaansuppdetail_t.keterangan,
            NULL::smallint AS is_verifikasi,
            NULL::integer AS validasipoobatdetail_id,
            penerimaansuppdetail_t.harga_netto_satuan,
            penerimaansuppdetail_t.diskon,
            penerimaansuppdetail_t.diskon AS discount_rp,
            0 AS jumlah,
            penerimaansupp_t.pajak_id,
            penerimaansuppdetail_t.harga_netto_satuan * penerimaansuppdetail_t.qty_besar::double precision AS sub_total,
            0 AS total_discount,
            pajak_m.pajak_persen AS ppn_persen,
            pajak_m.pajak_persen AS ppn_nilai,
            NULL::timestamp without time zone AS tgl_po,
            penerimaansupp_t.tgl_verifikasi AS tgl_validasi_po,
            penerimaansuppdetail_t.harga_netto_satuan * penerimaansuppdetail_t.qty_besar::double precision - penerimaansuppdetail_t.harga_netto_satuan * penerimaansuppdetail_t.qty_besar::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision + (penerimaansuppdetail_t.harga_netto_satuan * penerimaansuppdetail_t.qty_besar::double precision - penerimaansuppdetail_t.harga_netto_satuan * penerimaansuppdetail_t.qty_besar::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision) * (pajak_m.pajak_persen::double precision / 100::double precision) AS total,
            NULL::character varying AS no_pr,
            NULL::date AS tgl_pr,
            NULL::text AS catatan1,
            NULL::text AS catatan2,
            pegawai_m.nama_pegawai,
            payterm_m.payterm_nama,
            payterm_m.payterm_id
           FROM penerimaansupp_t
             JOIN penerimaansuppdetail_t ON penerimaansupp_t.penerimaansupp_id = penerimaansuppdetail_t.penerimaansupp_id
             JOIN pajak_m ON penerimaansupp_t.pajak_id = pajak_m.pajak_id
             LEFT JOIN loginpemakai_k ON penerimaansupp_t.created_by = loginpemakai_k.loginpemakai_id
             LEFT JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN payterm_m ON penerimaansupp_t.payterm_id = payterm_m.payterm_id
          WHERE penerimaansuppdetail_t.is_deleted = false AND penerimaansupp_t.is_deleted = false) terima
     JOIN supplier_m ON terima.supplier_id = supplier_m.supplier_id
     JOIN barang_m ON terima.barang_id = barang_m.barang_id
     LEFT JOIN satuankonversibrg_m ON terima.s_konversibrg_id = satuankonversibrg_m.satuankonversibrg_id
     LEFT JOIN satuanunit_m besar ON satuankonversibrg_m.satuanbesar_id = besar.satuanunit_id
     LEFT JOIN satuanunit_m kecil ON satuankonversibrg_m.satuankecil_id = kecil.satuanunit_id
     LEFT JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
     LEFT JOIN pegawai_m peg_mengetahui ON terima.peg_mengetahui = peg_mengetahui.pegawai_id
     LEFT JOIN pegawai_m peg_menyetujui ON terima.peg_menyetujui = peg_menyetujui.pegawai_id
     LEFT JOIN ( SELECT retur_barang.penerimaanbarangdetail_id,
            sum(retur_barang.qty_retur) AS on_retur
           FROM returpenerimaanbarangdetail_t retur_barang
          GROUP BY retur_barang.penerimaanbarangdetail_id) returdetailjumlah ON terima.penerimaanbarangdetail_id = returdetailjumlah.penerimaanbarangdetail_id;
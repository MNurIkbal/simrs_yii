-- public.inputhasillab_v source

CREATE OR REPLACE VIEW public.inputhasillab_v
AS SELECT tindakanpelayanan_t.tindakanpelayanan_id,
    tindakanpelayanan_t.pasienmasukpenunjang_id,
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
    samplelab_m.nama_sample,
    daftartindakan_m.daftartindakan_nama,
    hasilpemeriksaanlab_t.is_expertise,
        CASE pendaftaran_t.instalasi_id
            WHEN 1 THEN COALESCE(instruksi_t.catatan_instruksi, pasienkirimkeunitlain_t.catatan_dokterpengirim)
            ELSE instruksi_t.catatan_instruksi
        END AS catatan_instruksi,
    ambilsample_t.ambilsample_id,
    ambilsample_t.samplelab_id,
    ambilsample_t.tgl_ambilsample,
    ambilsample_t.jam_ambilsample,
    hasilpemeriksaanlab_t.hasilpemeriksaanlab_id,
    ambilsample_t.keterangan,
    ambilsample_t.jumlah,
    ambilsample_t.satuan_jumlah,
    satuanlab_m.satuanlab_nama,
    pasienmasukpenunjang_t.additional_data,
    'unit'::text AS tipe
   FROM tindakanpelayanan_t
     JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN ambilsample_t ON tindakanpelayanan_t.tindakanpelayanan_id = ambilsample_t.tindakanpelayanan_id AND pasienmasukpenunjang_t.pasienmasukpenunjang_id = ambilsample_t.pasienmasukpenunjang_id AND ambilsample_t.is_deleted = false
     JOIN samplelab_m ON ambilsample_t.samplelab_id = samplelab_m.samplelab_id
     LEFT JOIN instruksi_t ON pasienkirimkeunitlain_t.instruksi_id = instruksi_t.instruksi_id
     LEFT JOIN hasilpemeriksaanlab_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanlab_t.pasienmasukpenunjang_id AND ambilsample_t.samplelab_id = hasilpemeriksaanlab_t.samplelab_id AND hasilpemeriksaanlab_t.is_deleted IS FALSE
     LEFT JOIN satuanlab_m ON ambilsample_t.satuan_jumlah = satuanlab_m.satuanlab_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
  WHERE ruangan_m.instalasi_id = 4 AND ambilsample_t.is_deleted IS FALSE
UNION ALL
 SELECT tindakanpelayanan_t.tindakanpelayanan_id,
    tindakanpelayanan_t.pasienmasukpenunjang_id,
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
    samplelab_m.nama_sample,
    daftartindakan_m.daftartindakan_nama,
    hasilpemeriksaanlab_t.is_expertise,
        CASE pendaftaran_t.instalasi_id
            WHEN 1 THEN COALESCE(instruksi_t.catatan_instruksi, pasienkirimkeunitlain_t.catatan_dokterpengirim)
            ELSE instruksi_t.catatan_instruksi
        END AS catatan_instruksi,
    ambilsample_t.ambilsample_id,
    ambilsample_t.samplelab_id,
    ambilsample_t.tgl_ambilsample,
    ambilsample_t.jam_ambilsample,
    hasilpemeriksaanlab_t.hasilpemeriksaanlab_id,
    ambilsample_t.keterangan,
    ambilsample_t.jumlah,
    ambilsample_t.satuan_jumlah,
    satuanlab_m.satuanlab_nama,
    pasienmasukpenunjang_t.additional_data,
    'unit'::text AS tipe
   FROM tindakanpelayanan_t
     JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
     JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN ambilsample_t ON tindakanpelayanan_t.tindakanpelayanan_id = ambilsample_t.tindakanpelayanan_id AND pasienmasukpenunjang_t.pasienmasukpenunjang_id = ambilsample_t.pasienmasukpenunjang_id AND ambilsample_t.tindakanpaket_id = paketpelayanan_mp.daftartindakan_id AND ambilsample_t.is_deleted = false
     JOIN samplelab_m ON ambilsample_t.samplelab_id = samplelab_m.samplelab_id
     LEFT JOIN instruksi_t ON pasienkirimkeunitlain_t.instruksi_id = instruksi_t.instruksi_id
     LEFT JOIN hasilpemeriksaanlab_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanlab_t.pasienmasukpenunjang_id AND ambilsample_t.samplelab_id = hasilpemeriksaanlab_t.samplelab_id AND hasilpemeriksaanlab_t.is_deleted IS FALSE
     LEFT JOIN satuanlab_m ON ambilsample_t.satuan_jumlah = satuanlab_m.satuanlab_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
  WHERE ruangan_m.instalasi_id = 4 AND ambilsample_t.is_deleted IS FALSE
UNION ALL
 SELECT tindakanpelayanan_t.tindakanpelayanan_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    NULL::text AS catatan_dokterpengirim,
    samplelab_m.nama_sample,
    detail.detail_3 AS daftartindakan_nama,
    hasilpemeriksaanlab_t.is_expertise,
    NULL::text AS catatan_instruksi,
    ambilsample_t.ambilsample_id,
    ambilsample_t.samplelab_id,
    ambilsample_t.tgl_ambilsample,
    ambilsample_t.jam_ambilsample,
    hasilpemeriksaanlab_t.hasilpemeriksaanlab_id,
    ambilsample_t.keterangan,
    ambilsample_t.jumlah,
    ambilsample_t.satuan_jumlah,
    satuanlab_m.satuanlab_nama,
    pasienmasukpenunjang_t.additional_data,
    'unit'::text AS tipe
   FROM tindakanpelayanan_t
     JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
     JOIN ( SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
            paketpelayanan_mp.paketdetail_id AS detail_2id,
            paket_detail.tipepaket_nama AS detail_2,
            paket_detail.daftartindakan_id AS detail_3id,
            paket_detail.daftartindakan_nama AS detail_3
           FROM tipepaket_m
             JOIN paketpelayanan_mp ON tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id AND paketpelayanan_mp.is_deleted = false
             JOIN ( SELECT a.tipepaket_id,
                    a.tipepaket_nama,
                    daftartindakan_m.daftartindakan_id,
                    daftartindakan_m.daftartindakan_nama
                   FROM tipepaket_m a
                     JOIN paketpelayanan_mp paketpelayanan_mp_1 ON a.tipepaket_id = paketpelayanan_mp_1.tipepaket_id
                     JOIN daftartindakan_m ON paketpelayanan_mp_1.daftartindakan_id = daftartindakan_m.daftartindakan_id) paket_detail ON paketpelayanan_mp.paketdetail_id = paket_detail.tipepaket_id
          WHERE tipepaket_m.is_deleted = false
        UNION ALL
         SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
            NULL::integer AS detail_2id,
            NULL::character varying AS detail_2,
            paketpelayanan_mp.daftartindakan_id AS detail_3id,
            tindakan_detail.daftartindakan_nama AS detail_3
           FROM tipepaket_m
             JOIN paketpelayanan_mp ON tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id AND paketpelayanan_mp.is_deleted = false
             JOIN daftartindakan_m tindakan_detail ON paketpelayanan_mp.daftartindakan_id = tindakan_detail.daftartindakan_id
          WHERE tipepaket_m.is_deleted = false) detail ON tindakanpelayanan_t.tipepaket_id = detail.detail_1id
     JOIN ambilsample_t ON tindakanpelayanan_t.tindakanpelayanan_id = ambilsample_t.tindakanpelayanan_id AND pasienmasukpenunjang_t.pasienmasukpenunjang_id = ambilsample_t.pasienmasukpenunjang_id AND ambilsample_t.tindakanpaket_id = detail.detail_3id AND ambilsample_t.is_deleted = false
     JOIN samplelab_m ON ambilsample_t.samplelab_id = samplelab_m.samplelab_id
     LEFT JOIN hasilpemeriksaanlab_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanlab_t.pasienmasukpenunjang_id AND ambilsample_t.samplelab_id = hasilpemeriksaanlab_t.samplelab_id AND hasilpemeriksaanlab_t.is_deleted IS FALSE
     LEFT JOIN satuanlab_m ON ambilsample_t.satuan_jumlah = satuanlab_m.satuanlab_id
     JOIN ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
  WHERE ruangan_m.instalasi_id = 4 AND ambilsample_t.is_deleted IS FALSE
UNION ALL
 SELECT tindakanpelayanan_t.tindakanpelayanan_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    NULL::text AS catatan_dokterpengirim,
    NULL::text AS nama_sample,
    daftartindakan_m.daftartindakan_nama,
    NULL::boolean AS is_expertise,
    NULL::text AS catatan_instruksi,
    NULL::integer AS ambilsample_id,
    NULL::integer AS samplelab_id,
    NULL::date AS tgl_ambilsample,
    NULL::time without time zone AS jam_ambilsample,
    NULL::integer AS hasilpemeriksaanlab_id,
    NULL::text AS keterangan,
    NULL::integer AS jumlah,
    NULL::integer AS satuan_jumlah,
    NULL::character varying AS satuanlab_nama,
    COALESCE(pasienkirimkeunitlain_t.additional_data, pasienkirimkeunitlain_t.additional_data) AS additional_data,
    'wyna'::text AS tipe
   FROM pasienmasukpenunjang_t
     JOIN tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
     JOIN ( SELECT hasilpemeriksaanlab_wynacom_t.his_reg_no
           FROM hasilpemeriksaanlab_wynacom_t
          GROUP BY hasilpemeriksaanlab_wynacom_t.his_reg_no) hasilpemeriksaanlab_wynacom ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasilpemeriksaanlab_wynacom.his_reg_no::text
     LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
UNION ALL
 SELECT tindakanpelayanan_t.tindakanpelayanan_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    NULL::text AS catatan_dokterpengirim,
    NULL::text AS nama_sample,
    daftartindakan_m.daftartindakan_nama,
    NULL::boolean AS is_expertise,
    NULL::text AS catatan_instruksi,
    NULL::integer AS ambilsample_id,
    NULL::integer AS samplelab_id,
    NULL::date AS tgl_ambilsample,
    NULL::time without time zone AS jam_ambilsample,
    NULL::integer AS hasilpemeriksaanlab_id,
    NULL::text AS keterangan,
    NULL::integer AS jumlah,
    NULL::integer AS satuan_jumlah,
    NULL::character varying AS satuanlab_nama,
    COALESCE(pasienkirimkeunitlain_t.additional_data, pasienkirimkeunitlain_t.additional_data) AS additional_data,
    'roche'::text AS tipe
   FROM pasienmasukpenunjang_t
     JOIN ( SELECT tindakanpelayanan_t_1.tindakanpelayanan_id,
            tindakanpelayanan_t_1.pasienmasukpenunjang_id,
            tindakanpelayanan_t_1.daftartindakan_id
           FROM tindakanpelayanan_t tindakanpelayanan_t_1) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
     JOIN ( SELECT hasilpemeriksaanlab_roche_t.order_no
           FROM hasilpemeriksaanlab_roche_t
          WHERE hasilpemeriksaanlab_roche_t.is_deleted IS FALSE
          GROUP BY hasilpemeriksaanlab_roche_t.order_no) hasilpemeriksaanlab_roche ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasilpemeriksaanlab_roche.order_no::text
     LEFT JOIN ( SELECT pasienkirimkeunitlain_t_1.pasienkirimkeunitlain_id,
            pasienkirimkeunitlain_t_1.additional_data
           FROM pasienkirimkeunitlain_t pasienkirimkeunitlain_t_1) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
     JOIN ( SELECT daftartindakan_m_1.daftartindakan_id,
            daftartindakan_m_1.daftartindakan_nama
           FROM daftartindakan_m daftartindakan_m_1) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id;
-- public.detailcetakresep_v source

CREATE OR REPLACE VIEW public.detailcetakresep_v
AS SELECT 'reseptur'::text AS jenis,
    reseptur_t.noresep,
    obatalkes_m.obatalkes_nama,
    resepturdetail_t.hargajual_reseptur AS totalharga_jual,
    (resepturdetail_t.additional_data::json ->> 'satuan_input'::text)::character varying AS satuan_input,
    resepturdetail_t.qty_reseptur AS qty_oa,
    resepturdetail_t.det,
        CASE
            WHEN resepturdetail_t.qty_medis IS NOT NULL THEN resepturdetail_t.qty_medis
            ELSE resepturdetail_t.qty_reseptur
        END AS qty_transaksi,
        CASE
            WHEN resepturdetail_t.signa IS NOT NULL THEN resepturdetail_t.signa ->> 'text'::text
            ELSE NULL::text
        END AS signa_nama,
    resepturdetail_t.etiket,
    resepturdetail_t.nama_racikan,
    resepturdetail_t.qty_racikan,
    satuan_racikan.satuanunit_nama AS satuan_racikan_nama,
    resepturdetail_t.racikan_id,
    resepturdetail_t.rke
   FROM resepturdetail_t
     JOIN ( SELECT a.reseptur_id,
            a.pendaftaran_id,
            a.pasien_id,
            a.ruangan_id,
            a.ruanganreseptur_id,
            a.noresep,
            a.tglreseptur,
            a.status_reseptur
           FROM reseptur_t a) reseptur_t ON resepturdetail_t.reseptur_id = reseptur_t.reseptur_id
     LEFT JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_nama,
            a.harganetto
           FROM obatalkes_m a) obatalkes_m ON resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT a.signa_id,
            a.signa_nama,
            a.qty_obat,
            a.iterasi
           FROM signaobat_m a) signaobat_m ON resepturdetail_t.signa_id = signaobat_m.signa_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) satuan_racikan ON resepturdetail_t.satuan_racikan_id = satuan_racikan.satuanunit_id
  WHERE resepturdetail_t.is_deleted = false AND resepturdetail_t.is_active = true
UNION ALL
 SELECT 'resep'::text AS jenis,
    penjualanresep_t.noresep,
    obatalkes_m.obatalkes_nama,
    obatalkespasien_t.hargajual_oa AS totalharga_jual,
    (obatalkespasien_t.additional_data::json ->> 'satuan_input'::text)::character varying AS satuan_input,
    obatalkespasien_t.qty_oa,
    obatalkespasien_t.det,
        CASE
            WHEN obatalkespasien_t.qty_medis IS NOT NULL THEN obatalkespasien_t.qty_medis
            ELSE obatalkespasien_t.qty_oa
        END AS qty_transaksi,
        CASE
            WHEN obatalkespasien_t.signa IS NOT NULL THEN obatalkespasien_t.signa ->> 'text'::text
            ELSE signaobat_m.signa_nama::text
        END AS signa_nama,
    obatalkespasien_t.etiket,
    obatalkespasien_t.nama_racikan,
    obatalkespasien_t.qty_racikan,
    satuan_racikan.satuanunit_nama AS satuan_racikan_nama,
    obatalkespasien_t.racikan_id,
    obatalkespasien_t.rke
   FROM obatalkespasien_t
     JOIN ( SELECT b.penjualanresep_id,
            b.pendaftaran_id,
            b.pasien_id,
            b.pegawai_id,
            b.ruangan_id,
            b.reseptur_id,
            b.noresep,
            b.tglresep,
            b.status_reseptur,
            b.biayaadministrasi,
            b.totalhargajual,
            b.nama_pembeli
           FROM penjualanresep_t b) penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
     LEFT JOIN ( SELECT b.obatalkes_id,
            b.obatalkes_nama,
            b.harganetto
           FROM obatalkes_m b) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT b.signa_id,
            b.signa_nama,
            b.qty_obat,
            b.iterasi
           FROM signaobat_m b) signaobat_m ON obatalkespasien_t.signa_oa::integer = signaobat_m.signa_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) satuan_racikan ON obatalkespasien_t.satuan_racikan_id = satuan_racikan.satuanunit_id
  WHERE obatalkespasien_t.is_deleted = false AND obatalkespasien_t.is_active = true;
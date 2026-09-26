-- public.dokumen_v source

CREATE OR REPLACE VIEW public.dokumen_v
AS SELECT dokumen_m.dokumen_id,
    dokumen_m.jenis_dokumen_id,
    lookup_m.lookup_name AS jenis_dokumen_nama,
    dokumen_m.nama_dokumen,
    dokumen_m.nama_dokumen_lainnya,
    dokumen_m.is_eklaim
   FROM dokumen_m
     JOIN lookup_m ON dokumen_m.jenis_dokumen_id = lookup_m.lookup_id
  ORDER BY dokumen_m.dokumen_id;
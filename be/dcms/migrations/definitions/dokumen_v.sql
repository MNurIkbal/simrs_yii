CREATE VIEW "public"."dokumen_v" AS  SELECT dokumen_m.dokumen_id,
    dokumen_m.jenis_dokumen_id,
    lookup_m.lookup_name AS jenis_dokumen_nama,
    dokumen_m.nama_dokumen,
    dokumen_m.nama_dokumen_lainnya,
    dokumen_m.is_eklaim,
    dokumen_m.is_active
   FROM dokumen_m
     JOIN lookup_m ON dokumen_m.jenis_dokumen_id = lookup_m.lookup_id
  WHERE dokumen_m.is_deleted = false
  ORDER BY dokumen_m.dokumen_id;
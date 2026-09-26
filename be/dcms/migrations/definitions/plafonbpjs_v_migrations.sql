CREATE VIEW "public"."plafonbpjs_v" AS  SELECT plafonbpjs_m.plafonbpjs_id,
    plafonbpjs_m.plafon,
    instalasi_m.instalasi_nama,
    kelaspelayanan_m.kelaspelayanan_nama,
    plafonbpjs_m.is_active,
    plafonbpjs_m.instalasi_id,
    plafonbpjs_m.kelaspelayanan_id
   FROM plafonbpjs_m
     JOIN ( SELECT instalasi_m_1.instalasi_id,
            instalasi_m_1.instalasi_nama
           FROM instalasi_m instalasi_m_1) instalasi_m ON plafonbpjs_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ( SELECT kelaspelayanan_m_1.kelaspelayanan_id,
            kelaspelayanan_m_1.kelaspelayanan_nama
           FROM kelaspelayanan_m kelaspelayanan_m_1) kelaspelayanan_m ON plafonbpjs_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
  WHERE plafonbpjs_m.is_deleted = false;
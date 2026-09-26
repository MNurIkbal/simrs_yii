-- public.masterkamarruangan_v source

CREATE OR REPLACE VIEW public.masterkamarruangan_v
AS SELECT kamarruangan_m.ruangan_id,
    kamarruangan_m.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamarruangan_m.kamarruangan_jenis,
    fgetnamalookup(kamarruangan_m.kamarruangan_jenis) AS jenis_kamar,
    kamarruangan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    NULL::text AS status_isi,
    kamarruangan_m.kamarruangan_id,
    kamarruangan_m.is_dashboard,
    kamarruangan_m.is_active,
    kamarruangan_m.kamarruangan_deskripsi,
    kamarruangan_m.kamarruangan_kode
   FROM kamarruangan_m
     JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.is_deleted = false
     JOIN jeniskasuspenyakit_m ON kamarruangan_m.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id AND jeniskasuspenyakit_m.is_deleted = false
     JOIN kelaspelayanan_m ON kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id AND kelaspelayanan_m.is_deleted = false
  WHERE kamarruangan_m.is_deleted = false
  ORDER BY jeniskasuspenyakit_m.jeniskasuspenyakit_id, ruangan_m.ruangan_id;
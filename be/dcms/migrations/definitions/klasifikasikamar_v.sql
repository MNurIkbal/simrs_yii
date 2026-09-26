CREATE OR REPLACE VIEW public.klasifikasikamar_v
AS SELECT kamarruangan_m.kamarruangan_id,
    kamarruangan_m.kamarruangan_nokamar,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    kelaspelayanan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    klasifikasikamar_m.klasifikasikamar_id,
    klasifikasikamar_m.sirsonline_id,
    klasifikasikamar_m.eiscovid_id,
    klasifikasikamar_m.applicare_id,
    klasifikasikamar_m.spgdt_id,
    sirsonline_m.sirsonline_nama,
    eiscovid_m.eiscovid_nama,
    applicare_m.applicare_nama,
    spgdt_m.spgdt_nama,
    klasifikasikamar_m.klasifikasikamar_nama,
    kamarruangan_m.is_active,
    kamarruangan_m.is_deleted,
    klasifikasikamar_m.kodekelas_aplicare,
    klasifikasikamar_m.namakelas_aplicare,
    kamarruangan_m.kamarruangan_kode,
    klasifikasikamar_m.kodett_rsonline,
    klasifikasikamar_m.namatt_rsonline,
    kamarruangan_m.id_t_tt_rsonline
   FROM kamarruangan_m
     JOIN klasifikasikamar_m ON kamarruangan_m.klasifikasikamar_id = klasifikasikamar_m.klasifikasikamar_id
     LEFT JOIN sirsonline_m ON klasifikasikamar_m.sirsonline_id = sirsonline_m.sirsonline_id
     LEFT JOIN eiscovid_m ON klasifikasikamar_m.eiscovid_id = eiscovid_m.eiscovid_id
     LEFT JOIN applicare_m ON klasifikasikamar_m.applicare_id = applicare_m.applicare_id
     LEFT JOIN spgdt_m ON klasifikasikamar_m.spgdt_id = spgdt_m.spgdt_id
     LEFT JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN kelaspelayanan_m ON kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
  WHERE kamarruangan_m.is_active = true AND kamarruangan_m.is_deleted = false;
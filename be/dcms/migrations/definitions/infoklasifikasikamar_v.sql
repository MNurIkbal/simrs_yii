-- public.infoklasifikasikamar_v source

CREATE OR REPLACE VIEW public.infoklasifikasikamar_v
AS SELECT klasifikasikamar_m.klasifikasikamar_id,
    klasifikasikamar_m.sirsonline_id,
    klasifikasikamar_m.eiscovid_id,
    klasifikasikamar_m.applicare_id,
    klasifikasikamar_m.spgdt_id,
    sirsonline_m.sirsonline_nama,
    eiscovid_m.eiscovid_nama,
    applicare_m.applicare_nama,
    spgdt_m.spgdt_nama,
    klasifikasikamar_m.klasifikasikamar_nama,
    klasifikasikamar_m.is_active,
    klasifikasikamar_m.is_deleted,
    klasifikasikamar_m.kodekelas_aplicare,
    klasifikasikamar_m.namakelas_aplicare,
    klasifikasikamar_m.kodett_rsonline,
    klasifikasikamar_m.namatt_rsonline
   FROM klasifikasikamar_m
     LEFT JOIN sirsonline_m ON klasifikasikamar_m.sirsonline_id = sirsonline_m.sirsonline_id
     LEFT JOIN eiscovid_m ON klasifikasikamar_m.eiscovid_id = eiscovid_m.eiscovid_id
     LEFT JOIN applicare_m ON klasifikasikamar_m.applicare_id = applicare_m.applicare_id
     LEFT JOIN spgdt_m ON klasifikasikamar_m.spgdt_id = spgdt_m.spgdt_id;
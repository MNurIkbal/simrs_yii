CREATE VIEW public.reseptempdetail_v AS  SELECT reseptemp_m.reseptemp_id,
    reseptemp_m.reseptemp_nama,
    reseptemp_m.dokter_id,
    pegawai_m.nama_pegawai,
    reseptempdetail_m.racikan_id,
    racikan_m.racikan_nama,
    racikan_m.racikan_singkatan,
    reseptempdetail_m.rke,
    reseptempdetail_m.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    obatalkes_m.hargajual,
    obatalkes_m.satuankecil_id,
    satuanunit_m.satuanunit_nama,
    reseptempdetail_m.qty,
    reseptempdetail_m.signa_id,
    reseptempdetail_m.additional_data::json ->> 'satuaninput_id'::text AS satuaninput_id,
    satuanunit_m.satuanunit_nama::text AS satuan_input,
    obatalkes_m.satuankecil_id::text AS satuankonversi_id,
    satuanunit_m.satuanunit_nama::text AS satuan_konversi,
    reseptempdetail_m.additional_data::json ->> 'harga_konversi'::text AS harga_konversi,
    reseptempdetail_m.additional_data::json ->> 'harga_jual'::text AS harga_jual,
    1::text AS nilai_konversi,
    reseptempdetail_m.additional_data::json ->> 'etiket'::text AS etiket,
    reseptempdetail_m.additional_data,
        CASE COALESCE(signaobat_m.signa_id, 0)
            WHEN 0 THEN (reseptempdetail_m.signa ->> 'text'::text)::character varying
            ELSE signaobat_m.signa_nama
        END AS signa,
    reseptempdetail_m.is_deleted,
    reseptempdetail_m.deleted_by,
    reseptempdetail_m.deleted_date,
    reseptempdetail_m.is_kronis
   FROM reseptemp_m
     JOIN reseptempdetail_m ON reseptemp_m.reseptemp_id = reseptempdetail_m.reseptemp_id
     JOIN pegawai_m ON reseptemp_m.dokter_id = pegawai_m.pegawai_id
     JOIN racikan_m ON reseptempdetail_m.racikan_id = racikan_m.racikan_id
     LEFT JOIN obatalkes_m ON reseptempdetail_m.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN satuanunit_m ON obatalkes_m.satuankecil_id = satuanunit_m.satuanunit_id
     LEFT JOIN signaobat_m ON reseptempdetail_m.signa_id = signaobat_m.signa_id;
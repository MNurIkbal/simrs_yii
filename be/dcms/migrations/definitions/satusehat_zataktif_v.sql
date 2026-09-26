CREATE OR REPLACE VIEW public.satusehat_zataktif_v
AS
 SELECT a.satusehat_zataktif_id,
    obatalkes_m.obatalkes_kode,
    obatalkes_m.obatalkes_nama,
    zataktif_m.zataktif_kode,
    zataktif_m.zataktif_nama,
    a.bahanbaku_numerator,
    ( SELECT a_1.satuanunit_nama
           FROM satuanunit_m a_1
          WHERE a_1.satuanunit_id = a.bahanbaku_satuan) AS bahanbaku_satuan,
    a.bahanbaku_codesystem,
    a.bahanbaku_denominator,
    ( SELECT a_1.satuanunit_nama
           FROM satuanunit_m a_1
          WHERE a_1.satuanunit_id = a.bahanbaku_ucum) AS bahanbaku_ucum,
    a.bahanbaku_denominator_disesuaikan,
    ( SELECT a_1.satuanunit_nama
           FROM satuanunit_m a_1
          WHERE a_1.satuanunit_id = a.bahanbaku_satuan_disesuaikan) AS bahanbaku_satuan_disesuaikan,
    a.bahanbaku_codesystem_disesuaikan,
    bentuksediaan_m.bentuksediaan_kode,
    bentuksediaan_m.bentuksediaan_nama,
    ruteobat_m.ruteobat_id,
    ruteobat_m.kode_rute,
    ruteobat_m.nama_rute
   FROM satusehat_zataktif_mp a
     JOIN ( SELECT a_1.obatalkes_id,
            a_1.obatalkes_kode,
            a_1.obatalkes_nama,
            a_1.bentuksediaan_id,
            a_1.ruteobat_id
           FROM obatalkes_m a_1) obatalkes_m ON obatalkes_m.obatalkes_id = a.obatalkes_id
     JOIN zataktifobat_mp ON zataktifobat_mp.obatalkes_id = a.obatalkes_id AND zataktifobat_mp.zataktif_id = a.zataktif_id
     JOIN zataktif_m ON zataktif_m.zataktif_id = a.zataktif_id AND zataktif_m.zataktif_id = zataktifobat_mp.zataktif_id
     LEFT JOIN ruteobat_m ON ruteobat_m.ruteobat_id = obatalkes_m.ruteobat_id
     LEFT JOIN bentuksediaan_m ON bentuksediaan_m.bentuksediaan_id = obatalkes_m.bentuksediaan_id;
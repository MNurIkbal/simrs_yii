CREATE VIEW "public"."infoproduksiobatalkesbahanbaku_v" AS  SELECT produksiobatalkesbahanbaku_t.produksiobatalkesbahanbaku_id,
    produksiobatalkesbahanbaku_t.produksiobatalkesdetail_id,
    produksiobatalkesdetail.produksiobatalkes_id,
    produksiobatalkesbahanbaku_t.obatalkes_id,
    obatalkes_m.obatalkes_kode,
    obatalkes_m.obatalkes_nama,
    produksiobatalkesbahanbaku_t.satuankecil_id,
    satuanunit_m.satuanunit_nama AS satuan_kecil,
    produksiobatalkesbahanbaku_t.qty_obat,
    produksiobatalkesbahanbaku_t.harganetto_satuan,
    produksiobatalkesbahanbaku_t.harganetto AS totalharga
   FROM produksiobatalkesbahanbaku_t
     LEFT JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_kode,
            a.obatalkes_nama
           FROM obatalkes_m a) obatalkes_m ON obatalkes_m.obatalkes_id = produksiobatalkesbahanbaku_t.obatalkes_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) satuanunit_m ON satuanunit_m.satuanunit_id = produksiobatalkesbahanbaku_t.satuankecil_id
     LEFT JOIN ( SELECT a.produksiobatalkes_id,
            a.produksiobatalkesdetail_id
           FROM produksiobatalkesdetail_t a) produksiobatalkesdetail ON produksiobatalkesdetail.produksiobatalkesdetail_id = produksiobatalkesbahanbaku_t.produksiobatalkesdetail_id
  WHERE produksiobatalkesbahanbaku_t.is_deleted = false;


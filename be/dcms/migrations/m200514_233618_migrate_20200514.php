<?php

use yii\db\Migration;

/**
 * Class m200514_233618_migrate_20200514
 */
class m200514_233618_migrate_20200514 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."tariftindakan_m" ADD COLUMN "persen_penyulit" numeric(15,2);');
        $this->execute('ALTER TABLE "public"."timoperasi_t" ADD COLUMN "persentase" numeric(15,2);');
        $this->execute('ALTER TABLE "public"."timoperasi_t" ADD COLUMN "harga" numeric(15,2);');
        
        $this->execute('DROP VIEW if exists"public"."infotarifpenunjang_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infotarifpenunjang_v\" AS  SELECT 'LAB'::text AS jenis_tindakan,
    tariftindakan_m.tariftindakan_id,
    tindakanruangan_mp.ruangan_id,
    ruangan_m.ruangan_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    pemeriksaanlab_m.kelompokpemeriksaanlab_id,
    kelompokpemeriksaanlab_m.nama_kelompok,
    pemeriksaanlab_m.jenispemeriksaanlab_id,
    jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
    tariftindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    pemeriksaanlab_m.pemeriksaanlab_id,
    pemeriksaanlab_m.pemeriksaanlab_nama,
    tariftindakan_m.komponentarif_id,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    tindakanruangan_mp.is_default,
    ruangan_m.instalasi_id,
    COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit
   FROM ((((((((((tariftindakan_m
     JOIN daftartindakan_m ON ((tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     JOIN pemeriksaanlab_m ON (((tariftindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id) AND (pemeriksaanlab_m.is_deleted = false))))
     JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
     JOIN kelompokpemeriksaanlab_m ON ((pemeriksaanlab_m.kelompokpemeriksaanlab_id = kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id)))
     JOIN kelaspelayanan_m ON ((tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN perdatarif_m ON (((tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id) AND (perdatarif_m.is_deleted = false) AND (perdatarif_m.is_active = true))))
     JOIN penjamin_m ON ((tariftindakan_m.penjamin_id = penjamin_m.penjamin_id)))
     JOIN komponentarif_m ON ((tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id)))
     JOIN tindakanruangan_mp ON (((tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id) AND (tindakanruangan_mp.is_deleted = false))))
     JOIN ruangan_m ON ((tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id)))
  WHERE ((tariftindakan_m.is_active = true) AND (tariftindakan_m.is_deleted = false))
UNION ALL
 SELECT 'PAKET'::text AS jenis_tindakan,
    tariftindakan_m.tariftindakan_id,
    paketruangan_mp.ruangan_id,
    ruangan_m.ruangan_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    NULL::integer AS kelompokpemeriksaanlab_id,
    NULL::character varying AS nama_kelompok,
    NULL::integer AS jenispemeriksaanlab_id,
    NULL::character varying AS jenispemeriksaanlab_nama,
    tariftindakan_m.tipepaket_id AS daftartindakan_id,
    paket.tipepaket_nama AS daftartindakan_nama,
    NULL::integer AS pemeriksaanlab_id,
    NULL::character varying AS pemeriksaanlab_nama,
    tariftindakan_m.komponentarif_id,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    paketruangan_mp.is_default,
    ruangan_m.instalasi_id,
    COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit
   FROM (((((((tariftindakan_m
     JOIN ( SELECT tipepaket_m.tipepaket_id,
            tipepaket_m.tipepaket_nama
           FROM ((tipepaket_m
             JOIN ( SELECT paketpelayanan_mp_1.tipepaket_id,
                    count(paketpelayanan_mp_1.tipepaket_id) AS jumlah
                   FROM paketpelayanan_mp paketpelayanan_mp_1
                  WHERE (paketpelayanan_mp_1.is_deleted IS FALSE)
                  GROUP BY paketpelayanan_mp_1.tipepaket_id) paketpelayanan_mp ON ((tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
             JOIN ( SELECT paketpelayanan_mp_1.tipepaket_id,
                    count(paketpelayanan_mp_1.tipepaket_id) AS jumlah
                   FROM (paketpelayanan_mp paketpelayanan_mp_1
                     JOIN pemeriksaanlab_m pemeriksaanlab_m_1 ON (((paketpelayanan_mp_1.daftartindakan_id = pemeriksaanlab_m_1.daftartindakan_id) AND (pemeriksaanlab_m_1.is_deleted = false))))
                  WHERE (paketpelayanan_mp_1.is_deleted = false)
                  GROUP BY paketpelayanan_mp_1.tipepaket_id) pemeriksaanlab_m ON (((paketpelayanan_mp.tipepaket_id = pemeriksaanlab_m.tipepaket_id) AND (paketpelayanan_mp.jumlah = pemeriksaanlab_m.jumlah))))
          GROUP BY tipepaket_m.tipepaket_id, tipepaket_m.tipepaket_nama) paket ON ((tariftindakan_m.tipepaket_id = paket.tipepaket_id)))
     JOIN paketruangan_mp ON (((tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id) AND (paketruangan_mp.is_deleted = false))))
     JOIN ruangan_m ON ((paketruangan_mp.ruangan_id = ruangan_m.ruangan_id)))
     JOIN kelaspelayanan_m ON ((tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN perdatarif_m ON (((tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id) AND (perdatarif_m.is_deleted = false) AND (perdatarif_m.is_active = true))))
     JOIN penjamin_m ON ((tariftindakan_m.penjamin_id = penjamin_m.penjamin_id)))
     JOIN komponentarif_m ON ((tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id)))
  WHERE ((tariftindakan_m.is_active = true) AND (tariftindakan_m.is_deleted = false))
UNION ALL
 SELECT 'RAD'::text AS jenis_tindakan,
    tariftindakan_m.tariftindakan_id,
    tindakanruangan_mp.ruangan_id,
    ruangan_m.ruangan_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    pemeriksaanrad_m.kelompokpemeriksaanrad_id AS kelompokpemeriksaanlab_id,
    kelompokpemeriksaanrad_m.nama_kelompok,
    pemeriksaanrad_m.jenispemeriksaanrad_id AS jenispemeriksaanlab_id,
    jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS jenispemeriksaanlab_nama,
    tariftindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    pemeriksaanrad_m.pemeriksaanradiologi_id AS pemeriksaanlab_id,
    pemeriksaanrad_m.pemeriksaanrad_nama AS pemeriksaanlab_nama,
    tariftindakan_m.komponentarif_id,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    tindakanruangan_mp.is_default,
    ruangan_m.instalasi_id,
    COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit
   FROM ((((((((((tariftindakan_m
     JOIN daftartindakan_m ON ((tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     JOIN pemeriksaanrad_m ON (((tariftindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id) AND (pemeriksaanrad_m.is_deleted = false))))
     JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))
     JOIN kelompokpemeriksaanrad_m ON ((pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id)))
     JOIN kelaspelayanan_m ON ((tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN perdatarif_m ON (((tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id) AND (perdatarif_m.is_deleted = false) AND (perdatarif_m.is_active = true))))
     JOIN penjamin_m ON ((tariftindakan_m.penjamin_id = penjamin_m.penjamin_id)))
     JOIN komponentarif_m ON ((tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id)))
     JOIN tindakanruangan_mp ON (((tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id) AND (tindakanruangan_mp.is_deleted = false))))
     JOIN ruangan_m ON ((tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id)))
  WHERE ((tariftindakan_m.is_active = true) AND (tariftindakan_m.is_deleted = false))
UNION ALL
 SELECT 'PAKET'::text AS jenis_tindakan,
    tariftindakan_m.tariftindakan_id,
    paketruangan_mp.ruangan_id,
    ruangan_m.ruangan_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    NULL::integer AS kelompokpemeriksaanlab_id,
    NULL::character varying AS nama_kelompok,
    NULL::integer AS jenispemeriksaanlab_id,
    NULL::character varying AS jenispemeriksaanlab_nama,
    tariftindakan_m.daftartindakan_id,
    paket.tipepaket_nama AS daftartindakan_nama,
    NULL::integer AS pemeriksaanlab_id,
    NULL::character varying AS pemeriksaanlab_nama,
    tariftindakan_m.komponentarif_id,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    paketruangan_mp.is_default,
    ruangan_m.instalasi_id,
    COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit
   FROM (((((((tariftindakan_m
     JOIN ( SELECT tipepaket_m.tipepaket_id,
            tipepaket_m.tipepaket_nama
           FROM ((tipepaket_m
             JOIN ( SELECT paketpelayanan_mp_1.tipepaket_id,
                    count(paketpelayanan_mp_1.tipepaket_id) AS jumlah
                   FROM paketpelayanan_mp paketpelayanan_mp_1
                  WHERE (paketpelayanan_mp_1.is_deleted IS FALSE)
                  GROUP BY paketpelayanan_mp_1.tipepaket_id) paketpelayanan_mp ON ((tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
             JOIN ( SELECT paketpelayanan_mp_1.tipepaket_id,
                    count(paketpelayanan_mp_1.tipepaket_id) AS jumlah
                   FROM (paketpelayanan_mp paketpelayanan_mp_1
                     JOIN pemeriksaanrad_m pemeriksaanrad_m_1 ON (((paketpelayanan_mp_1.daftartindakan_id = pemeriksaanrad_m_1.daftartindakan_id) AND (pemeriksaanrad_m_1.is_deleted = false))))
                  WHERE (paketpelayanan_mp_1.is_deleted = false)
                  GROUP BY paketpelayanan_mp_1.tipepaket_id) pemeriksaanrad_m ON (((paketpelayanan_mp.tipepaket_id = pemeriksaanrad_m.tipepaket_id) AND (paketpelayanan_mp.jumlah = pemeriksaanrad_m.jumlah))))
          GROUP BY tipepaket_m.tipepaket_id, tipepaket_m.tipepaket_nama) paket ON ((tariftindakan_m.tipepaket_id = paket.tipepaket_id)))
     JOIN paketruangan_mp ON (((tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id) AND (paketruangan_mp.is_deleted = false))))
     JOIN ruangan_m ON ((paketruangan_mp.ruangan_id = ruangan_m.ruangan_id)))
     JOIN kelaspelayanan_m ON ((tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN perdatarif_m ON (((tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id) AND (perdatarif_m.is_deleted = false) AND (perdatarif_m.is_active = true))))
     JOIN penjamin_m ON ((tariftindakan_m.penjamin_id = penjamin_m.penjamin_id)))
     JOIN komponentarif_m ON ((tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id)))
  WHERE ((tariftindakan_m.is_active = true) AND (tariftindakan_m.is_deleted = false))
UNION ALL
 SELECT 'IBS'::text AS jenis_tindakan,
    tariftindakan_m.tariftindakan_id,
    tindakanruangan_mp.ruangan_id,
    ruangan_m.ruangan_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    operasi_m.golonganoperasi_id AS kelompokpemeriksaanlab_id,
    golonganoperasi_m.golonganoperasi_nama AS nama_kelompok,
    operasi_m.kegiatanoperasi_id AS jenispemeriksaanlab_id,
    kegiatanoperasi_m.kegiatanoperasi_nama AS jenispemeriksaanlab_nama,
    tariftindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    operasi_m.operasi_id AS pemeriksaanlab_id,
    operasi_m.operasi_nama AS pemeriksaanlab_nama,
    tariftindakan_m.komponentarif_id,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    tindakanruangan_mp.is_default,
    ruangan_m.instalasi_id,
    COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit
   FROM ((((((((((tariftindakan_m
     JOIN daftartindakan_m ON ((tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
     JOIN operasi_m ON (((tariftindakan_m.daftartindakan_id = operasi_m.daftartindakan_id) AND (operasi_m.is_deleted = false))))
     JOIN golonganoperasi_m ON ((operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id)))
     JOIN kegiatanoperasi_m ON ((operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id)))
     JOIN kelaspelayanan_m ON ((tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN perdatarif_m ON (((tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id) AND (perdatarif_m.is_deleted = false) AND (perdatarif_m.is_active = true))))
     JOIN penjamin_m ON ((tariftindakan_m.penjamin_id = penjamin_m.penjamin_id)))
     JOIN komponentarif_m ON ((tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id)))
     JOIN tindakanruangan_mp ON (((tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id) AND (tindakanruangan_mp.is_deleted = false))))
     JOIN ruangan_m ON ((tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id)))
  WHERE ((tariftindakan_m.is_active = true) AND (tariftindakan_m.is_deleted = false))
UNION ALL
 SELECT 'PAKET'::text AS jenis_tindakan,
    tariftindakan_m.tariftindakan_id,
    paketruangan_mp.ruangan_id,
    ruangan_m.ruangan_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    NULL::integer AS kelompokpemeriksaanlab_id,
    NULL::character varying AS nama_kelompok,
    NULL::integer AS jenispemeriksaanlab_id,
    NULL::character varying AS jenispemeriksaanlab_nama,
    tariftindakan_m.daftartindakan_id,
    paket.tipepaket_nama AS daftartindakan_nama,
    NULL::integer AS pemeriksaanlab_id,
    NULL::character varying AS pemeriksaanlab_nama,
    tariftindakan_m.komponentarif_id,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    paketruangan_mp.is_default,
    ruangan_m.instalasi_id,
    COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit
   FROM (((((((tariftindakan_m
     JOIN ( SELECT tipepaket_m.tipepaket_id,
            tipepaket_m.tipepaket_nama
           FROM ((tipepaket_m
             JOIN ( SELECT paketpelayanan_mp_1.tipepaket_id,
                    count(paketpelayanan_mp_1.tipepaket_id) AS jumlah
                   FROM paketpelayanan_mp paketpelayanan_mp_1
                  WHERE (paketpelayanan_mp_1.is_deleted IS FALSE)
                  GROUP BY paketpelayanan_mp_1.tipepaket_id) paketpelayanan_mp ON ((tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
             JOIN ( SELECT paketpelayanan_mp_1.tipepaket_id,
                    count(paketpelayanan_mp_1.tipepaket_id) AS jumlah
                   FROM (paketpelayanan_mp paketpelayanan_mp_1
                     JOIN operasi_m operasi_m_1 ON (((paketpelayanan_mp_1.daftartindakan_id = operasi_m_1.daftartindakan_id) AND (operasi_m_1.is_deleted = false))))
                  WHERE (paketpelayanan_mp_1.is_deleted = false)
                  GROUP BY paketpelayanan_mp_1.tipepaket_id) operasi_m ON (((paketpelayanan_mp.tipepaket_id = operasi_m.tipepaket_id) AND (paketpelayanan_mp.jumlah = operasi_m.jumlah))))
          GROUP BY tipepaket_m.tipepaket_id, tipepaket_m.tipepaket_nama) paket ON ((tariftindakan_m.tipepaket_id = paket.tipepaket_id)))
     JOIN paketruangan_mp ON (((tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id) AND (paketruangan_mp.is_deleted = false))))
     JOIN ruangan_m ON ((paketruangan_mp.ruangan_id = ruangan_m.ruangan_id)))
     JOIN kelaspelayanan_m ON ((tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN perdatarif_m ON (((tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id) AND (perdatarif_m.is_deleted = false) AND (perdatarif_m.is_active = true))))
     JOIN penjamin_m ON ((tariftindakan_m.penjamin_id = penjamin_m.penjamin_id)))
     JOIN komponentarif_m ON ((tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id)))
  WHERE ((tariftindakan_m.is_active = true) AND (tariftindakan_m.is_deleted = false));");

        $this->execute('DROP VIEW if exists "public"."infoadjusmenobatdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infoadjusmenobatdetail_v\" AS  SELECT 'masuk'::text AS jenis,
    adjusmenobat_t.adjusmenobat_id,
    adjusmenobat_t.no_adjusmen,
    adjusmenobatmasuk_t.adjusmenobatmasuk_id AS detail_id,
    adjusmenobatmasuk_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    adjusmenobatmasuk_t.qty AS qty_input,
    adjusmenobatmasuk_t.qty_konversi,
    adjusmenobatmasuk_t.tgl_kadaluarsa,
    adjusmenobatmasuk_t.harga_netto,
    adjusmenobatmasuk_t.no_batch,
    adjusmenobatmasuk_t.keterangan,
    NULL::text AS alasan,
    adjusmenobatmasuk_t.satuankecil_id,
    satuan_kecil.satuanunit_nama AS satuan_kecil,
    adjusmenobatmasuk_t.satuanbesar_id,
    satuan_besar.satuanunit_nama AS satuan_besar
   FROM ((((adjusmenobat_t
     JOIN adjusmenobatmasuk_t ON ((adjusmenobat_t.adjusmenobat_id = adjusmenobatmasuk_t.adjusmenobat_id)))
     JOIN obatalkes_m ON ((adjusmenobatmasuk_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN satuanunit_m satuan_kecil ON ((adjusmenobatmasuk_t.satuankecil_id = satuan_kecil.satuanunit_id)))
     JOIN satuanunit_m satuan_besar ON ((adjusmenobatmasuk_t.satuanbesar_id = satuan_besar.satuanunit_id)))
  WHERE ((adjusmenobat_t.is_deleted = false) AND (adjusmenobatmasuk_t.is_deleted = false))
UNION ALL
 SELECT 'keluar'::text AS jenis,
    adjusmenobat_t.adjusmenobat_id,
    adjusmenobat_t.no_adjusmen,
    adjusmenobatkeluar_t.adjusmenobatkeluar_id AS detail_id,
    adjusmenobatkeluar_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    adjusmenobatkeluar_t.qty AS qty_input,
    adjusmenobatkeluar_t.qty_konversi,
    NULL::timestamp without time zone AS tgl_kadaluarsa,
    NULL::double precision AS harga_netto,
    adjusmenobatkeluar_t.no_batch,
    adjusmenobatkeluar_t.keterangan,
    adjusmenobatkeluar_t.alasan,
    adjusmenobatkeluar_t.satuankecil_id,
    satuan_kecil.satuanunit_nama AS satuan_kecil,
    adjusmenobatkeluar_t.satuanbesar_id,
    satuan_besar.satuanunit_nama AS satuan_besar
   FROM ((((adjusmenobat_t
     JOIN adjusmenobatkeluar_t ON ((adjusmenobat_t.adjusmenobat_id = adjusmenobatkeluar_t.adjusmenobat_id)))
     JOIN obatalkes_m ON ((adjusmenobatkeluar_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN satuanunit_m satuan_kecil ON ((adjusmenobatkeluar_t.satuankecil_id = satuan_kecil.satuanunit_id)))
     JOIN satuanunit_m satuan_besar ON ((adjusmenobatkeluar_t.satuanbesar_id = satuan_besar.satuanunit_id)))
  WHERE ((adjusmenobat_t.is_deleted = false) AND (adjusmenobatkeluar_t.is_deleted = false));");

        $this->execute('DROP VIEW if exists "public"."infoobatalkesexpired_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infoobatalkesexpired_v\" AS  SELECT hit.obatalkes_id,
    sum((hit.qtystok_in - hit.qtystok_out)) AS stok,
        CASE
            WHEN (mutasi.status_mutasi = 401) THEN sum((hit.qtystok_in - mutasi.jumlah))
            ELSE sum((hit.qtystok_in - hit.qtystok_out))
        END AS stok_exp,
    mutasi.jumlah,
    mutasi.status_mutasi,
    hit.obatalkes_nama,
    hit.satuankecil_id,
    hit.s_kecil AS satuan_kecil,
    hit.tglkadaluarsa,
    hit.harganetto,
    sum(harga_netto.harga_netto) AS jumlah_harganetto,
    hit.instalasi_nama,
    hit.ruangan_nama,
    hit.periodestokobat_id,
    hit.tglperiodestok_awal AS tglperiodeposting_awal,
    hit.tglperiodestok_akhir AS tglperiodeposting_akhir,
    hit.ruangan_id,
    hit.instalasi_id,
    array_agg(hit.id_stok) AS id_stok,
    hit.nobatch,
    hit.margin,
    hit.ppn,
    hit.disc,
    hit.hn_last,
    hit.a1 AS hn_last_margin,
    hit.a2 AS hn_last_diskon,
    hit.a3 AS hn_last_margin_diskon,
    hit.a4 AS hn_last_ppn,
    hit.a5 AS hargajual_last,
    hit.hn_min,
    hit.b1 AS hn_min_margin,
    hit.b2 AS hn_min_diskon,
    hit.b3 AS hn_min_margin_diskon,
    hit.b4 AS hn_min_ppn,
    hit.b5 AS hargajual_min,
    hit.hn_max,
    hit.c1 AS hn_max_margin,
    hit.c2 AS hn_max_diskon,
    hit.c3 AS hn_max_margin_diskon,
    hit.c4 AS hn_max_ppn,
    hit.c5 AS hargajual_max,
    hit.hn_avg,
    hit.d1 AS hn_avg_margin,
    hit.d2 AS hn_avg_diskon,
    hit.d3 AS hn_avg_margin_diskon,
    hit.d4 AS hn_avg_ppn,
    hit.d5 AS hargajual_avg,
    0 AS hargaygdipakai,
    0 AS harganetto_ygdipakai,
    0 AS hn_margin,
    0 AS hn_diskon,
    0 AS hn_ppn
   FROM ((( SELECT
                CASE
                    WHEN (stokobatalkes_t.stokobatalkesasal_id IS NULL) THEN stokobatalkes_t.stokobatalkes_id
                    ELSE stokobatalkes_t.stokobatalkesasal_id
                END AS id_stok,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.qtystok_in,
            stokobatalkes_t.qtystok_out,
            stokobatalkes_t.tglkadaluarsa,
            obatalkes_m.obatalkes_nama,
            stokobatalkes_t.satuankecil_id,
            satuan_kecil.satuanunit_nama AS s_kecil,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            NULL::text AS periodestokobat_id,
            NULL::text AS tglperiodestok_awal,
            NULL::text AS tglperiodestok_akhir,
            ruangan_m.ruangan_id,
            instalasi_m.instalasi_id,
            stokobatalkes_t.nobatch,
            obatalkes_m.harganetto,
            obatalkes_m.hargaterakhir AS hn_last,
            obatalkes_m.hargaminimum AS hn_min,
            obatalkes_m.hargamaksimum AS hn_max,
            obatalkes_m.hargaratarata AS hn_avg,
            konfigfarmasi_k.persenppn AS ppn,
            konfigfarmasi_k.persenmargin AS margin,
            konfigfarmasi_k.persen_diskon AS disc,
            NULL::text AS a1,
            NULL::text AS a2,
            NULL::text AS a3,
            NULL::text AS a4,
            NULL::text AS a5,
            NULL::text AS b1,
            NULL::text AS b2,
            NULL::text AS b3,
            NULL::text AS b4,
            NULL::text AS b5,
            NULL::text AS c1,
            NULL::text AS c2,
            NULL::text AS c3,
            NULL::text AS c4,
            NULL::text AS c5,
            NULL::text AS d1,
            NULL::text AS d2,
            NULL::text AS d3,
            NULL::text AS d4,
            NULL::text AS d5
           FROM (((((stokobatalkes_t
             JOIN obatalkes_m ON ((stokobatalkes_t.obatalkes_id = obatalkes_m.obatalkes_id)))
             JOIN ruangan_m ON ((stokobatalkes_t.ruangan_id = ruangan_m.ruangan_id)))
             JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
             LEFT JOIN satuanunit_m satuan_kecil ON ((stokobatalkes_t.satuankecil_id = satuan_kecil.satuanunit_id)))
             JOIN konfigfarmasi_k ON ((konfigfarmasi_k.is_deleted = false)))) hit
     LEFT JOIN ( SELECT mutasiobatdetail_t.obatalkes_id,
            mutasiobatdetail_t.tgl_kadaluarsa,
            sum(mutasiobatdetail_t.jumlah_mutasi) AS jumlah,
            mutasiobatruangan_t.status_mutasi,
            mutasiobatruangan_t.ruanganasal_id AS ruangan_id
           FROM (mutasiobatdetail_t
             JOIN mutasiobatruangan_t ON ((mutasiobatdetail_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id)))
          GROUP BY mutasiobatdetail_t.obatalkes_id, mutasiobatdetail_t.tgl_kadaluarsa, mutasiobatruangan_t.status_mutasi, mutasiobatruangan_t.ruanganasal_id) mutasi ON (((hit.obatalkes_id = mutasi.obatalkes_id) AND (hit.tglkadaluarsa = mutasi.tgl_kadaluarsa) AND (hit.ruangan_id = mutasi.ruangan_id))))
     LEFT JOIN ( SELECT
                CASE
                    WHEN (stokobatalkes_t.stokobatalkesasal_id IS NULL) THEN stokobatalkes_t.stokobatalkes_id
                    ELSE stokobatalkes_t.stokobatalkesasal_id
                END AS id_stok,
            stokobatalkes_t.obatalkes_id,
            stokobatalkes_t.tglkadaluarsa,
            stokobatalkes_t.ruangan_id,
            sum((obatalkes_m.harganetto * (stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out))) AS harga_netto
           FROM (stokobatalkes_t
             JOIN obatalkes_m ON ((stokobatalkes_t.obatalkes_id = obatalkes_m.obatalkes_id)))
          GROUP BY
                CASE
                    WHEN (stokobatalkes_t.stokobatalkesasal_id IS NULL) THEN stokobatalkes_t.stokobatalkes_id
                    ELSE stokobatalkes_t.stokobatalkesasal_id
                END, stokobatalkes_t.obatalkes_id, stokobatalkes_t.tglkadaluarsa, stokobatalkes_t.ruangan_id) harga_netto ON (((hit.obatalkes_id = harga_netto.obatalkes_id) AND (hit.tglkadaluarsa = harga_netto.tglkadaluarsa) AND (hit.ruangan_id = harga_netto.ruangan_id))))
  GROUP BY hit.obatalkes_id, mutasi.jumlah, mutasi.status_mutasi, hit.obatalkes_nama, hit.satuankecil_id, hit.s_kecil, hit.tglkadaluarsa, hit.harganetto, hit.instalasi_nama, hit.ruangan_nama, hit.periodestokobat_id, hit.tglperiodestok_awal, hit.tglperiodestok_akhir, hit.ruangan_id, hit.instalasi_id, hit.nobatch, hit.margin, hit.ppn, hit.disc, hit.hn_last, hit.a1, hit.a2, hit.a3, hit.a4, hit.a5, hit.hn_min, hit.b1, hit.b2, hit.b3, hit.b4, hit.b5, hit.hn_max, hit.c1, hit.c2, hit.c3, hit.c4, hit.c5, hit.hn_avg, hit.d1, hit.d2, hit.d3, hit.d4, hit.d5, 0::integer, 0::integer, 0::integer, 0::integer, 0::integer;");

        $this->execute("
            CREATE VIEW \"public\".\"infoobatexpired_v\" AS  SELECT array_agg(stokobatalkes_t.stokobatalkes_id) AS id_stok,
    stokobatalkes_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    stokobatalkes_t.tglkadaluarsa,
    sum((stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out)) AS stok,
        CASE
            WHEN (mutasi.status_mutasi = 401) THEN sum((stokobatalkes_t.qtystok_in - mutasi.jumlah))
            ELSE sum((stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out))
        END AS stok_exp,
    mutasi.jumlah,
    mutasi.status_mutasi,
    obatalkes_m.harganetto,
    (sum(obatalkes_m.harganetto) * sum((stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out))) AS jumlah_harganetto,
    obatalkes_m.satuankecil_id,
    satuanunit_m.satuanunit_nama AS satuan_kecil,
    stokobatalkes_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama
   FROM (((((stokobatalkes_t
     JOIN obatalkes_m ON ((obatalkes_m.obatalkes_id = stokobatalkes_t.obatalkes_id)))
     JOIN ruangan_m ON ((ruangan_m.ruangan_id = stokobatalkes_t.ruangan_id)))
     JOIN instalasi_m ON ((instalasi_m.instalasi_id = ruangan_m.instalasi_id)))
     LEFT JOIN satuanunit_m ON ((satuanunit_m.satuanunit_id = stokobatalkes_t.satuankecil_id)))
     LEFT JOIN ( SELECT mutasiobatdetail_t.obatalkes_id,
            mutasiobatdetail_t.tgl_kadaluarsa,
            sum(mutasiobatdetail_t.jumlah_mutasi) AS jumlah,
            mutasiobatruangan_t.status_mutasi,
            mutasiobatruangan_t.ruanganasal_id AS ruangan_id
           FROM (mutasiobatdetail_t
             JOIN mutasiobatruangan_t ON ((mutasiobatdetail_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id)))
          GROUP BY mutasiobatdetail_t.obatalkes_id, mutasiobatdetail_t.tgl_kadaluarsa, mutasiobatruangan_t.status_mutasi, mutasiobatruangan_t.ruanganasal_id) mutasi ON (((stokobatalkes_t.obatalkes_id = mutasi.obatalkes_id) AND (stokobatalkes_t.tglkadaluarsa = mutasi.tgl_kadaluarsa) AND (stokobatalkes_t.ruangan_id = mutasi.ruangan_id))))
  GROUP BY stokobatalkes_t.obatalkes_id, obatalkes_m.obatalkes_nama, obatalkes_m.satuankecil_id, satuanunit_m.satuanunit_nama, stokobatalkes_t.ruangan_id, ruangan_m.ruangan_nama, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, stokobatalkes_t.tglkadaluarsa, obatalkes_m.harganetto, mutasi.jumlah, mutasi.status_mutasi
 HAVING (sum((stokobatalkes_t.qtystok_in - stokobatalkes_t.qtystok_out)) > (0)::double precision);");
        
 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200514_233618_migrate_20200514 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200514_233618_migrate_20200514 cannot be reverted.\n";

        return false;
    }
    */
}

<?php

use yii\db\Migration;

/**
 * Class m190912_065148_tarif_penunjang
 */
class m190912_065148_tarif_penunjang extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infotarifpenunjang_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infotarifpenunjang_v AS 
 SELECT 'LAB'::text AS jenis_tindakan,
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
    ruangan_m.instalasi_id
   FROM tariftindakan_m
     JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN pemeriksaanlab_m ON tariftindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id AND pemeriksaanlab_m.is_deleted = false
     JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
     JOIN kelompokpemeriksaanlab_m ON pemeriksaanlab_m.kelompokpemeriksaanlab_id = kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_deleted = false AND perdatarif_m.is_active = true
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
     JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id AND tindakanruangan_mp.is_deleted = false
     JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
  WHERE tariftindakan_m.is_active = true AND tariftindakan_m.is_deleted = false
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
    ruangan_m.instalasi_id
   FROM tariftindakan_m
     JOIN ( SELECT tipepaket_m.tipepaket_id,
            tipepaket_m.tipepaket_nama
           FROM tipepaket_m
             JOIN ( SELECT paketpelayanan_mp_1.tipepaket_id,
                    count(paketpelayanan_mp_1.tipepaket_id) AS jumlah
                   FROM paketpelayanan_mp paketpelayanan_mp_1
                  WHERE paketpelayanan_mp_1.is_deleted IS FALSE
                  GROUP BY paketpelayanan_mp_1.tipepaket_id) paketpelayanan_mp ON tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id
             JOIN ( SELECT paketpelayanan_mp_1.tipepaket_id,
                    count(paketpelayanan_mp_1.tipepaket_id) AS jumlah
                   FROM paketpelayanan_mp paketpelayanan_mp_1
                     JOIN pemeriksaanlab_m pemeriksaanlab_m_1 ON paketpelayanan_mp_1.daftartindakan_id = pemeriksaanlab_m_1.daftartindakan_id AND pemeriksaanlab_m_1.is_deleted = false
                  WHERE paketpelayanan_mp_1.is_deleted = false
                  GROUP BY paketpelayanan_mp_1.tipepaket_id) pemeriksaanlab_m ON paketpelayanan_mp.tipepaket_id = pemeriksaanlab_m.tipepaket_id AND paketpelayanan_mp.jumlah = pemeriksaanlab_m.jumlah
          GROUP BY tipepaket_m.tipepaket_id, tipepaket_m.tipepaket_nama) paket ON tariftindakan_m.tipepaket_id = paket.tipepaket_id
     JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id AND paketruangan_mp.is_deleted = false
     JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_deleted = false AND perdatarif_m.is_active = true
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
  WHERE tariftindakan_m.is_active = true AND tariftindakan_m.is_deleted = false
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
    ruangan_m.instalasi_id
   FROM tariftindakan_m
     JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN pemeriksaanrad_m ON tariftindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id AND pemeriksaanrad_m.is_deleted = false
     JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
     JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_deleted = false AND perdatarif_m.is_active = true
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
     JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id AND tindakanruangan_mp.is_deleted = false
     JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
  WHERE tariftindakan_m.is_active = true AND tariftindakan_m.is_deleted = false
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
    ruangan_m.instalasi_id
   FROM tariftindakan_m
     JOIN ( SELECT tipepaket_m.tipepaket_id,
            tipepaket_m.tipepaket_nama
           FROM tipepaket_m
             JOIN ( SELECT paketpelayanan_mp_1.tipepaket_id,
                    count(paketpelayanan_mp_1.tipepaket_id) AS jumlah
                   FROM paketpelayanan_mp paketpelayanan_mp_1
                  WHERE paketpelayanan_mp_1.is_deleted IS FALSE
                  GROUP BY paketpelayanan_mp_1.tipepaket_id) paketpelayanan_mp ON tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id
             JOIN ( SELECT paketpelayanan_mp_1.tipepaket_id,
                    count(paketpelayanan_mp_1.tipepaket_id) AS jumlah
                   FROM paketpelayanan_mp paketpelayanan_mp_1
                     JOIN pemeriksaanrad_m pemeriksaanrad_m_1 ON paketpelayanan_mp_1.daftartindakan_id = pemeriksaanrad_m_1.daftartindakan_id AND pemeriksaanrad_m_1.is_deleted = false
                  WHERE paketpelayanan_mp_1.is_deleted = false
                  GROUP BY paketpelayanan_mp_1.tipepaket_id) pemeriksaanrad_m ON paketpelayanan_mp.tipepaket_id = pemeriksaanrad_m.tipepaket_id AND paketpelayanan_mp.jumlah = pemeriksaanrad_m.jumlah
          GROUP BY tipepaket_m.tipepaket_id, tipepaket_m.tipepaket_nama) paket ON tariftindakan_m.tipepaket_id = paket.tipepaket_id
     JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id AND paketruangan_mp.is_deleted = false
     JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_deleted = false AND perdatarif_m.is_active = true
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
  WHERE tariftindakan_m.is_active = true AND tariftindakan_m.is_deleted = false
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
    ruangan_m.instalasi_id
   FROM tariftindakan_m
     JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN operasi_m ON tariftindakan_m.daftartindakan_id = operasi_m.daftartindakan_id AND operasi_m.is_deleted = false
     JOIN golonganoperasi_m ON operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id
     JOIN kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_deleted = false AND perdatarif_m.is_active = true
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
     JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id AND tindakanruangan_mp.is_deleted = false
     JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
  WHERE tariftindakan_m.is_active = true AND tariftindakan_m.is_deleted = false
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
    ruangan_m.instalasi_id
   FROM tariftindakan_m
     JOIN ( SELECT tipepaket_m.tipepaket_id,
            tipepaket_m.tipepaket_nama
           FROM tipepaket_m
             JOIN ( SELECT paketpelayanan_mp_1.tipepaket_id,
                    count(paketpelayanan_mp_1.tipepaket_id) AS jumlah
                   FROM paketpelayanan_mp paketpelayanan_mp_1
                  WHERE paketpelayanan_mp_1.is_deleted IS FALSE
                  GROUP BY paketpelayanan_mp_1.tipepaket_id) paketpelayanan_mp ON tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id
             JOIN ( SELECT paketpelayanan_mp_1.tipepaket_id,
                    count(paketpelayanan_mp_1.tipepaket_id) AS jumlah
                   FROM paketpelayanan_mp paketpelayanan_mp_1
                     JOIN operasi_m operasi_m_1 ON paketpelayanan_mp_1.daftartindakan_id = operasi_m_1.daftartindakan_id AND operasi_m_1.is_deleted = false
                  WHERE paketpelayanan_mp_1.is_deleted = false
                  GROUP BY paketpelayanan_mp_1.tipepaket_id) operasi_m ON paketpelayanan_mp.tipepaket_id = operasi_m.tipepaket_id AND paketpelayanan_mp.jumlah = operasi_m.jumlah
          GROUP BY tipepaket_m.tipepaket_id, tipepaket_m.tipepaket_nama) paket ON tariftindakan_m.tipepaket_id = paket.tipepaket_id
     JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id AND paketruangan_mp.is_deleted = false
     JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_deleted = false AND perdatarif_m.is_active = true
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
  WHERE tariftindakan_m.is_active = true AND tariftindakan_m.is_deleted = false;");

        $this->execute('ALTER TABLE public.infotarifpenunjang_v
  OWNER TO postgres;');

         $this->execute('DROP VIEW if exists public.tariftindakanlab_v;');

         $this->execute("
            CREATE OR REPLACE VIEW public.tariftindakanlab_v AS 
 SELECT tariftindakan_m.tariftindakan_id,
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
    tindakanruangan_mp.is_default
   FROM tariftindakan_m
     JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN pemeriksaanlab_m ON tariftindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id AND pemeriksaanlab_m.is_active = true AND pemeriksaanlab_m.is_deleted = false
     JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
     JOIN kelompokpemeriksaanlab_m ON pemeriksaanlab_m.kelompokpemeriksaanlab_id = kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_active = true AND perdatarif_m.is_deleted = false
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
     JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
     JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
  WHERE tariftindakan_m.is_active = true AND tariftindakan_m.is_deleted = false;");

          $this->execute('ALTER TABLE public.tariftindakanlab_v
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.tariftindakanrad_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.tariftindakanrad_v AS 
 SELECT tariftindakan_m.tariftindakan_id,
    tindakanruangan_mp.ruangan_id,
    ruangan_m.ruangan_nama,
    tariftindakan_m.perdatarif_id,
    perdatarif_m.perdanama_sk,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    pemeriksaanrad_m.jenispemeriksaanrad_id,
    jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
    tariftindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    pemeriksaanrad_m.pemeriksaanradiologi_id,
    pemeriksaanrad_m.pemeriksaanrad_nama,
    tariftindakan_m.komponentarif_id,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.persendiskon_tindakan,
    tindakanruangan_mp.is_default
   FROM tariftindakan_m
     JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN pemeriksaanrad_m ON tariftindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id AND pemeriksaanrad_m.is_active = true AND pemeriksaanrad_m.is_deleted = false
     JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_active = true AND perdatarif_m.is_deleted = false
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
     JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
     JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
  WHERE tariftindakan_m.is_active = true AND tariftindakan_m.is_deleted = false AND pemeriksaanrad_m.is_deleted = false;");

        $this->execute('ALTER TABLE public.tariftindakanrad_v
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.pakettindakanrad_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.pakettindakanrad_v AS 
 SELECT tariftindakan_m.tariftindakan_id,
    paketruangan_mp.ruangan_id,
    ruangan_m.ruangan_nama,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    tariftindakan_m.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    tariftindakan_m.komponentarif_id,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.hargadiskon_tindakan,
    NULL::text AS daftartindakan_nama
   FROM tariftindakan_m
     JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id
     JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_active = true AND perdatarif_m.is_deleted = false
  WHERE ruangan_m.instalasi_id = 5;");

        $this->execute('ALTER TABLE public.pakettindakanrad_v
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.pakettindakanlab_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.pakettindakanlab_v AS 
 SELECT tariftindakan_m.tariftindakan_id,
    paketruangan_mp.ruangan_id,
    ruangan_m.ruangan_nama,
    tariftindakan_m.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tariftindakan_m.penjamin_id,
    penjamin_m.penjamin_nama,
    tariftindakan_m.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    tariftindakan_m.komponentarif_id,
    komponentarif_m.komponentarif_nama,
    tariftindakan_m.harga_tariftindakan,
    tariftindakan_m.persencyto_tindakan,
    tariftindakan_m.hargadiskon_tindakan
   FROM tariftindakan_m
     JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id
     JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id
     JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
     JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
     JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_active = true AND perdatarif_m.is_deleted = false
  WHERE ruangan_m.instalasi_id = 4;");

        $this->execute('ALTER TABLE public.pakettindakanlab_v
  OWNER TO postgres;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190912_065148_tarif_penunjang cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190912_065148_tarif_penunjang cannot be reverted.\n";

        return false;
    }
    */
}

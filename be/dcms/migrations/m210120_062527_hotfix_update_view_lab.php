<?php

use yii\db\Migration;

/**
 * Class m210120_062527_hotfix_update_view_lab
 */
class m210120_062527_hotfix_update_view_lab extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS"public"."tariftindakanlab_v";
        ');

        $this->execute('
            CREATE VIEW "public"."tariftindakanlab_v" AS  SELECT tariftindakan_m.tariftindakan_id,
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
               FROM ((((((((((tariftindakan_m
                 JOIN daftartindakan_m ON ((tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
                 JOIN pemeriksaanlab_m ON (((tariftindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id) AND (pemeriksaanlab_m.is_active = true) AND (pemeriksaanlab_m.is_deleted = false))))
                 JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
                 JOIN kelompokpemeriksaanlab_m ON ((pemeriksaanlab_m.kelompokpemeriksaanlab_id = kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id)))
                 JOIN kelaspelayanan_m ON ((tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 JOIN perdatarif_m ON (((tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id) AND (perdatarif_m.is_active = true) AND (perdatarif_m.is_deleted = false))))
                 JOIN penjamin_m ON ((tariftindakan_m.penjamin_id = penjamin_m.penjamin_id)))
                 JOIN komponentarif_m ON ((tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id)))
                 JOIN tindakanruangan_mp ON ((tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id)))
                 JOIN ruangan_m ON ((tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id)))
              WHERE ((tariftindakan_m.is_active = true) AND (tariftindakan_m.is_deleted = false))
              GROUP BY tariftindakan_m.tariftindakan_id, tindakanruangan_mp.ruangan_id, ruangan_m.ruangan_nama, tariftindakan_m.perdatarif_id, perdatarif_m.perdanama_sk, tariftindakan_m.kelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_nama, tariftindakan_m.penjamin_id, penjamin_m.penjamin_nama, pemeriksaanlab_m.kelompokpemeriksaanlab_id, kelompokpemeriksaanlab_m.nama_kelompok, pemeriksaanlab_m.jenispemeriksaanlab_id, jenispemeriksaanlab_m.jenispemeriksaanlab_nama, tariftindakan_m.daftartindakan_id, daftartindakan_m.daftartindakan_nama, pemeriksaanlab_m.pemeriksaanlab_id, pemeriksaanlab_m.pemeriksaanlab_nama, tariftindakan_m.komponentarif_id, komponentarif_m.komponentarif_nama, tariftindakan_m.harga_tariftindakan, tariftindakan_m.persencyto_tindakan, tariftindakan_m.persendiskon_tindakan, tindakanruangan_mp.is_default;
        ');

        $this->execute('
            DROP VIEW IF EXISTS"public"."infotarifpenunjang_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infotarifpenunjang_v" AS  SELECT \'LAB\'::text AS jenis_tindakan,
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
              GROUP BY tariftindakan_m.tariftindakan_id, tindakanruangan_mp.ruangan_id, ruangan_m.ruangan_nama, tariftindakan_m.perdatarif_id, perdatarif_m.perdanama_sk, tariftindakan_m.kelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_nama, tariftindakan_m.penjamin_id, penjamin_m.penjamin_nama, pemeriksaanlab_m.kelompokpemeriksaanlab_id, kelompokpemeriksaanlab_m.nama_kelompok, pemeriksaanlab_m.jenispemeriksaanlab_id, jenispemeriksaanlab_m.jenispemeriksaanlab_nama, tariftindakan_m.daftartindakan_id, daftartindakan_m.daftartindakan_nama, pemeriksaanlab_m.pemeriksaanlab_id, pemeriksaanlab_m.pemeriksaanlab_nama, tariftindakan_m.komponentarif_id, komponentarif_m.komponentarif_nama, tariftindakan_m.harga_tariftindakan, tariftindakan_m.persencyto_tindakan, tariftindakan_m.persendiskon_tindakan, tindakanruangan_mp.is_default, ruangan_m.instalasi_id, COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric)
            UNION ALL
             SELECT \'PAKET\'::text AS jenis_tindakan,
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
              GROUP BY tariftindakan_m.tariftindakan_id, paketruangan_mp.ruangan_id, ruangan_m.ruangan_nama, tariftindakan_m.perdatarif_id, perdatarif_m.perdanama_sk, tariftindakan_m.kelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_nama, tariftindakan_m.penjamin_id, penjamin_m.penjamin_nama, tariftindakan_m.tipepaket_id, paket.tipepaket_nama, tariftindakan_m.komponentarif_id, komponentarif_m.komponentarif_nama, tariftindakan_m.harga_tariftindakan, tariftindakan_m.persencyto_tindakan, tariftindakan_m.persendiskon_tindakan, paketruangan_mp.is_default, ruangan_m.instalasi_id, COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric)
            UNION ALL
             SELECT \'RAD\'::text AS jenis_tindakan,
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
              GROUP BY tariftindakan_m.tariftindakan_id, tindakanruangan_mp.ruangan_id, ruangan_m.ruangan_nama, tariftindakan_m.perdatarif_id, perdatarif_m.perdanama_sk, tariftindakan_m.kelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_nama, tariftindakan_m.penjamin_id, penjamin_m.penjamin_nama, pemeriksaanrad_m.kelompokpemeriksaanrad_id, kelompokpemeriksaanrad_m.nama_kelompok, pemeriksaanrad_m.jenispemeriksaanrad_id, jenispemeriksaanrad_m.jenispemeriksaanrad_nama, tariftindakan_m.daftartindakan_id, daftartindakan_m.daftartindakan_nama, pemeriksaanrad_m.pemeriksaanradiologi_id, pemeriksaanrad_m.pemeriksaanrad_nama, tariftindakan_m.komponentarif_id, komponentarif_m.komponentarif_nama, tariftindakan_m.harga_tariftindakan, tariftindakan_m.persencyto_tindakan, tariftindakan_m.persendiskon_tindakan, tindakanruangan_mp.is_default, ruangan_m.instalasi_id, COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric)
            UNION ALL
             SELECT \'PAKET\'::text AS jenis_tindakan,
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
              GROUP BY tariftindakan_m.tariftindakan_id, paketruangan_mp.ruangan_id, ruangan_m.ruangan_nama, tariftindakan_m.perdatarif_id, perdatarif_m.perdanama_sk, tariftindakan_m.kelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_nama, tariftindakan_m.penjamin_id, penjamin_m.penjamin_nama, tariftindakan_m.daftartindakan_id, paket.tipepaket_nama, tariftindakan_m.komponentarif_id, komponentarif_m.komponentarif_nama, tariftindakan_m.harga_tariftindakan, tariftindakan_m.persencyto_tindakan, tariftindakan_m.persendiskon_tindakan, paketruangan_mp.is_default, ruangan_m.instalasi_id, COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric)
            UNION ALL
             SELECT \'IBS\'::text AS jenis_tindakan,
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
              GROUP BY tariftindakan_m.tariftindakan_id, tindakanruangan_mp.ruangan_id, ruangan_m.ruangan_nama, tariftindakan_m.perdatarif_id, perdatarif_m.perdanama_sk, tariftindakan_m.kelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_nama, tariftindakan_m.penjamin_id, penjamin_m.penjamin_nama, operasi_m.golonganoperasi_id, golonganoperasi_m.golonganoperasi_nama, operasi_m.kegiatanoperasi_id, kegiatanoperasi_m.kegiatanoperasi_nama, tariftindakan_m.daftartindakan_id, daftartindakan_m.daftartindakan_nama, operasi_m.operasi_id, operasi_m.operasi_nama, tariftindakan_m.komponentarif_id, komponentarif_m.komponentarif_nama, tariftindakan_m.harga_tariftindakan, tariftindakan_m.persencyto_tindakan, tariftindakan_m.persendiskon_tindakan, tindakanruangan_mp.is_default, ruangan_m.instalasi_id, COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric)
            UNION ALL
             SELECT \'PAKET\'::text AS jenis_tindakan,
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
              WHERE ((tariftindakan_m.is_active = true) AND (tariftindakan_m.is_deleted = false))
              GROUP BY tariftindakan_m.tariftindakan_id, paketruangan_mp.ruangan_id, ruangan_m.ruangan_nama, tariftindakan_m.perdatarif_id, perdatarif_m.perdanama_sk, tariftindakan_m.kelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_nama, tariftindakan_m.penjamin_id, penjamin_m.penjamin_nama, tariftindakan_m.daftartindakan_id, paket.tipepaket_nama, tariftindakan_m.komponentarif_id, komponentarif_m.komponentarif_nama, tariftindakan_m.harga_tariftindakan, tariftindakan_m.persencyto_tindakan, tariftindakan_m.persendiskon_tindakan, paketruangan_mp.is_default, ruangan_m.instalasi_id, COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric);
        ');

        $this->execute('
            DROP VIEW IF EXISTS"public"."infotagihanpenunjang_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infotagihanpenunjang_v" AS  SELECT pendaftaran_t.pendaftaran_id,
                pendaftaran_t.tgl_pendaftaran,
                pasienmasukpenunjang_t.tglmasukpenunjang,
                pendaftaran_t.no_pendaftaran,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                kelaspelayanan_m.kelaspelayanan_nama,
                pegawai_m.nama_pegawai AS dokter,
                ruang_pendaftaran.ruangan_nama AS ruang_pendaftaran,
                instalasi_m.instalasi_nama,
                ruangan_m.ruangan_nama, 
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                (sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, (0)::double precision)) + (sum(COALESCE((obatalkespasien_t.hargajual_oa)::integer, 0)))::double precision) AS jumlah_tagihan,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                pasien_m.jeniskelamin,
                fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
                pendaftaran_t.umur,
                pasien_m.tanggal_lahir,
                pasienmasukpenunjang_t.no_masukpenunjang,
                pasienmasukpenunjang_t.ruangan_id
               FROM ((((((((((((((pasienmasukpenunjang_t
                 JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 LEFT JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
                 LEFT JOIN ( SELECT tindakanpelayanan_t_1.pasienmasukpenunjang_id,
                        sum(COALESCE(tindakanpelayanan_t_1.tarif_tindakan, (0)::double precision)) AS tarif_tindakan
                       FROM tindakanpelayanan_t tindakanpelayanan_t_1
                      WHERE ((tindakanpelayanan_t_1.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t_1.is_deleted = false))
                      GROUP BY tindakanpelayanan_t_1.tindakanpelayanan_id, tindakanpelayanan_t_1.pasienmasukpenunjang_id) tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
                 LEFT JOIN ( SELECT obatalkespasien_t_1.pasienmasukpenunjang_id,
                        sum(COALESCE((obatalkespasien_t_1.hargajual_oa)::integer, 0)) AS hargajual_oa
                       FROM obatalkespasien_t obatalkespasien_t_1
                      WHERE ((obatalkespasien_t_1.obatsudahbayar_id IS NULL) AND (obatalkespasien_t_1.is_deleted = false))
                      GROUP BY obatalkespasien_t_1.pasienmasukpenunjang_id) obatalkespasien_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = obatalkespasien_t.pasienmasukpenunjang_id)))
                 LEFT JOIN pasienadmisi_t ON ((pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                 JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN ruangan_m ruang_pendaftaran ON ((COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id) = ruang_pendaftaran.ruangan_id)))
                 JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
                 JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                 LEFT JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
              WHERE (pendaftaran_t.status_bayar = 349)
              GROUP BY pasienmasukpenunjang_t.pasienmasukpenunjang_id, ruang_pendaftaran.ruangan_nama, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, kelaspelayanan_m.kelaspelayanan_nama, pegawai_m.nama_pegawai, pendaftaran_t.pendaftaran_id, pasienmasukpenunjang_t.tglmasukpenunjang, pendaftaran_t.no_pendaftaran, instalasi_m.instalasi_nama, ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, pendaftaran_t.tgl_pendaftaran, pasien_m.jeniskelamin, (fgetnamalookup((pasien_m.jeniskelamin)::integer)), pasien_m.tanggal_lahir, pasienmasukpenunjang_t.no_masukpenunjang;
        ');

        $this->execute('
            DROP VIEW IF EXISTS"public"."rincianpasienlab_v";
        ');

        $this->execute('
            CREATE VIEW "public"."rincianpasienlab_v" AS  SELECT \'ORDER\'::text AS tipe_pasien,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                pasienmasukpenunjang_t.pendaftaran_id,
                pendaftaran_t.tgl_pendaftaran,
                pendaftaran_t.no_pendaftaran, 
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                pegawai_m.nama_pegawai AS dokter,
                ruangan_m.ruangan_nama,
                kelaspelayanan_m.kelaspelayanan_nama,
                penjamin_m.penjamin_nama,
                carabayar_m.carabayar_nama,
                fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
                sum(tindakanpelayanan_t.tarif_tindakan) AS total_tagihan,
                sum(tindakansudahbayar_t.jmliur_biaya) AS total_sdh_bayar,
                (tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya) AS total_sisa_tagihan,
                pemakaianuangmuka_t.total_uangmuka
               FROM ((((((((((((((pasienmasukpenunjang_t
                 JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
                 JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 LEFT JOIN pasienadmisi_t ON ((pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                 JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
                 JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
                 JOIN ruangan_m ON ((COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id)))
                 JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                 JOIN kelaspelayanan_m ON ((COALESCE( pasienadmisi_t.kelaspelayanan_id,  pendaftaran_t.kelaspelayanan_id) = kelaspelayanan_m.kelaspelayanan_id)))
                 JOIN penjamin_m ON ((COALESCE(pasienadmisi_t.penjamin_id, pendaftaran_t.penjamin_id) = penjamin_m.penjamin_id)))
                 JOIN carabayar_m ON ((COALESCE(pasienadmisi_t.carabayar_id, pendaftaran_t.carabayar_id) = carabayar_m.carabayar_id)))
                 JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
                 LEFT JOIN tindakansudahbayar_t ON ((tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id)))
                 LEFT JOIN pemakaianuangmuka_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pemakaianuangmuka_t.pendaftaran_id)))
              WHERE (pasienkirimkeunitlain_t.instalasi_id = 4)
              GROUP BY pasienmasukpenunjang_t.pasienmasukpenunjang_id, pasienmasukpenunjang_t.pendaftaran_id, pendaftaran_t.tgl_pendaftaran, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pegawai_m.nama_pegawai, ruangan_m.ruangan_nama, kelaspelayanan_m.kelaspelayanan_nama, penjamin_m.penjamin_nama, carabayar_m.carabayar_nama, pendaftaran_t.status_bayar, (tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya), pemakaianuangmuka_t.total_uangmuka
            UNION ALL
             SELECT \'RUJUKAN RS\'::text AS tipe_pasien,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                pasienmasukpenunjang_t.pendaftaran_id,
                pendaftaran_t.tgl_pendaftaran,
                pendaftaran_t.no_pendaftaran,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                pegawai_m.nama_pegawai AS dokter,
                ruangan_m.ruangan_nama,
                kelaspelayanan_m.kelaspelayanan_nama,
                penjamin_m.penjamin_nama,
                carabayar_m.carabayar_nama,
                fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
                sum(tindakanpelayanan_t.tarif_tindakan) AS total_tagihan,
                sum(tindakansudahbayar_t.jmliur_biaya) AS total_sdh_bayar,
                (tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya) AS total_sisa_tagihan,
                pemakaianuangmuka_t.total_uangmuka
               FROM ((((((((((((((pasienmasukpenunjang_t
                 JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
                 JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
                 JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
                 JOIN rujukandari_m ON ((rujukan_t.rujukandari_id = rujukandari_m.rujukandari_id)))
                 JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                 JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
                 LEFT JOIN tindakansudahbayar_t ON ((tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id)))
                 LEFT JOIN pemakaianuangmuka_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pemakaianuangmuka_t.pendaftaran_id)))
              WHERE (pendaftaran_t.instalasi_id = 4)
              GROUP BY pasienmasukpenunjang_t.pasienmasukpenunjang_id, pasienmasukpenunjang_t.pendaftaran_id, pendaftaran_t.tgl_pendaftaran, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pegawai_m.nama_pegawai, ruangan_m.ruangan_nama, kelaspelayanan_m.kelaspelayanan_nama, penjamin_m.penjamin_nama, carabayar_m.carabayar_nama, pendaftaran_t.status_bayar, (tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya), pemakaianuangmuka_t.total_uangmuka
            UNION ALL
             SELECT \'APS\'::text AS tipe_pasien,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                pasienmasukpenunjang_t.pendaftaran_id,
                pendaftaran_t.tgl_pendaftaran,
                pendaftaran_t.no_pendaftaran,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                pegawai_m.nama_pegawai AS dokter,
                ruangan_m.ruangan_nama,
                kelaspelayanan_m.kelaspelayanan_nama,
                penjamin_m.penjamin_nama,
                carabayar_m.carabayar_nama,
                fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
                sum(tindakanpelayanan_t.tarif_tindakan) AS total_tagihan,
                sum(tindakansudahbayar_t.jmliur_biaya) AS total_sdh_bayar,
                (tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya) AS total_sisa_tagihan,
                pemakaianuangmuka_t.total_uangmuka
               FROM (((((((((((pasienmasukpenunjang_t
                 JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
                 JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                 JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
                 JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                 JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
                 LEFT JOIN tindakansudahbayar_t ON ((tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id)))
                 LEFT JOIN pemakaianuangmuka_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pemakaianuangmuka_t.pendaftaran_id)))
              WHERE ((pendaftaran_t.instalasi_id = 4) AND (pendaftaran_t.is_aps = true))
              GROUP BY pasienmasukpenunjang_t.pasienmasukpenunjang_id, pasienmasukpenunjang_t.pendaftaran_id, pendaftaran_t.tgl_pendaftaran, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pegawai_m.nama_pegawai, ruangan_m.ruangan_nama, kelaspelayanan_m.kelaspelayanan_nama, penjamin_m.penjamin_nama, carabayar_m.carabayar_nama, pendaftaran_t.status_bayar, (tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya), pemakaianuangmuka_t.total_uangmuka;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210120_062527_hotfix_update_view_lab cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210120_062527_hotfix_update_view_lab cannot be reverted.\n";

        return false;
    }
    */
}

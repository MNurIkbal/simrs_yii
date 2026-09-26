<?php

use yii\db\Migration;

/**
 * Class m191018_033513_perbahan_schema_20191008
 */
class m191018_033513_perbahan_schema_20191008 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute("
            INSERT INTO public.penomoran_k(penomoran_id, penomoran_nama, prefix, last_generate, last_number, flag_refresh) VALUES 
            (32, 'Terima Mutasi Barang', 'TMB', 'TMB2019100000', '0000', '0'),
            (33, 'Stok Opname Barang', 'SOB', 'SOB2019100000', '0000', '0');");

         $this->execute('ALTER TABLE "public"."klaiminacbg_t" 
                        ADD COLUMN "is_terkirim" bool DEFAULT false;');

         $this->execute('ALTER TABLE "public"."klaimgroup_t" 
                      ALTER COLUMN "last_modified_date" DROP DEFAULT,
                      ALTER COLUMN "deleted_date" DROP DEFAULT;');

/*infoklaiminacbg_v*/
         $this->execute('DROP VIEW if exists public.infoklaiminacbg_v;');

         $this->execute("
            CREATE OR REPLACE VIEW public.infoklaiminacbg_v AS 
 SELECT
        CASE
            WHEN klaiminacbg_t.instalasi_id = 3 THEN 'RI'::text
            ELSE 'RJ'::text
        END AS tipe,
    klaiminacbg_t.instalasi_id,
    klaiminacbg_t.klaiminacbg_id,
    klaimgroup_t.klaimgroup_id,
    klaiminacbg_t.tgl_masuk,
    klaiminacbg_t.tgl_keluar,
    klaimgroup_t.created_date AS tgl_group,
    klaiminacbg_t.no_sep,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    klaiminacbg_t.diagnosa_primer,
    klaiminacbg_t.diagnosa_sekunder,
    klaimgroup_t.spesial_procedure,
    klaiminacbg_t.total_tarifrs,
    klaiminacbg_t.is_terkirim,
        CASE
            WHEN klaiminacbg_t.is_terkirim = false THEN '-'::text
            ELSE 'Terkirim'::text
        END AS status_kirim,
    klaiminacbg_t.pendaftaran_id,
    klaiminacbg_t.pasienadmisi_id
   FROM klaiminacbg_t
     JOIN pasien_m ON klaiminacbg_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN klaimgroup_t ON klaiminacbg_t.klaimgroup_id = klaimgroup_t.klaimgroup_id
  WHERE klaiminacbg_t.is_deleted = false
  ORDER BY klaiminacbg_t.klaiminacbg_id DESC;");

         $this->execute('ALTER TABLE public.infoklaiminacbg_v
  OWNER TO postgres;');


/*infoklaimkirimol_v*/
         $this->execute('DROP VIEW if exists public.infoklaimkirimol_v;');

         $this->execute("
            CREATE OR REPLACE VIEW public.infoklaimkirimol_v AS 
 SELECT
        CASE
            WHEN x.instalasi_id = 3 THEN 'RI'::text
            ELSE 'RJ'::text
        END AS tipe,
    x.instalasi_id,
    x.tgl_keluar,
    x.tgl_group,
    sum(x.rj) AS rawat_jalan,
    sum(x.ri) AS rawat_inap,
    sum(x.rj) + sum(x.ri) AS total,
    sum(x.blm_kirim) AS belum_kirim,
    sum(x.sdh_kirim) AS sudah_kirim
   FROM ( SELECT klaiminacbg_t.instalasi_id,
            to_char(klaiminacbg_t.tgl_keluar, 'YYYY-MM-DD'::text)::date AS tgl_keluar,
            to_char(klaim_group.created_date, 'YYYY-MM-DD'::text)::date AS tgl_group,
            count(klaiminacbg_t.instalasi_id) AS rj,
            0 AS ri,
            0 AS blm_kirim,
            0 AS sdh_kirim
           FROM klaiminacbg_t
             LEFT JOIN ( SELECT klaimgroup_t.klaimgroup_id,
                    klaimgroup_t.created_date
                   FROM klaimgroup_t) klaim_group ON klaiminacbg_t.klaimgroup_id = klaim_group.klaimgroup_id
          WHERE (klaiminacbg_t.instalasi_id = ANY (ARRAY[1, 2])) AND klaiminacbg_t.is_deleted = false
          GROUP BY klaiminacbg_t.instalasi_id, klaiminacbg_t.tgl_keluar, (to_char(klaim_group.created_date, 'YYYY-MM-DD'::text)::date)
        UNION ALL
         SELECT klaiminacbg_t.instalasi_id,
            to_char(klaiminacbg_t.tgl_keluar, 'YYYY-MM-DD'::text)::date AS tgl_keluar,
            to_char(klaim_group.created_date, 'YYYY-MM-DD'::text)::date AS tgl_group,
            0 AS rj,
            count(klaiminacbg_t.instalasi_id) AS ri,
            0 AS blm_kirim,
            0 AS sdh_kirim
           FROM klaiminacbg_t
             LEFT JOIN ( SELECT klaimgroup_t.klaimgroup_id,
                    klaimgroup_t.created_date
                   FROM klaimgroup_t) klaim_group ON klaiminacbg_t.klaimgroup_id = klaim_group.klaimgroup_id
          WHERE klaiminacbg_t.instalasi_id = 3 AND klaiminacbg_t.is_deleted = false
          GROUP BY klaiminacbg_t.instalasi_id, klaiminacbg_t.tgl_keluar, (to_char(klaim_group.created_date, 'YYYY-MM-DD'::text)::date)
        UNION ALL
         SELECT klaiminacbg_t.instalasi_id,
            to_char(klaiminacbg_t.tgl_keluar, 'YYYY-MM-DD'::text)::date AS tgl_keluar,
            to_char(klaim_group.created_date, 'YYYY-MM-DD'::text)::date AS tgl_group,
            0 AS rj,
            0 AS ri,
            count(klaiminacbg_t.is_terkirim) AS blm_kirim,
            0 AS sdh_kirim
           FROM klaiminacbg_t
             LEFT JOIN ( SELECT klaimgroup_t.klaimgroup_id,
                    klaimgroup_t.created_date
                   FROM klaimgroup_t) klaim_group ON klaiminacbg_t.klaimgroup_id = klaim_group.klaimgroup_id
          WHERE klaiminacbg_t.is_terkirim = false AND klaiminacbg_t.is_deleted = false
          GROUP BY klaiminacbg_t.instalasi_id, klaiminacbg_t.tgl_keluar, (to_char(klaim_group.created_date, 'YYYY-MM-DD'::text)::date)
        UNION ALL
         SELECT klaiminacbg_t.instalasi_id,
            to_char(klaiminacbg_t.tgl_keluar, 'YYYY-MM-DD'::text)::date AS tgl_keluar,
            to_char(klaim_group.created_date, 'YYYY-MM-DD'::text)::date AS tgl_group,
            0 AS rj,
            0 AS ri,
            0 AS blm_kirim,
            count(klaiminacbg_t.is_terkirim) AS sdh_kirim
           FROM klaiminacbg_t
             LEFT JOIN ( SELECT klaimgroup_t.klaimgroup_id,
                    klaimgroup_t.created_date
                   FROM klaimgroup_t) klaim_group ON klaiminacbg_t.klaimgroup_id = klaim_group.klaimgroup_id
          WHERE klaiminacbg_t.is_terkirim = true AND klaiminacbg_t.is_deleted = false
          GROUP BY klaiminacbg_t.instalasi_id, klaiminacbg_t.tgl_keluar, (to_char(klaim_group.created_date, 'YYYY-MM-DD'::text)::date)) x
  GROUP BY x.instalasi_id, x.tgl_keluar, x.tgl_group
  ORDER BY x.tgl_keluar;");

         $this->execute('ALTER TABLE public.infoklaimkirimol_v
  OWNER TO postgres;');


/*infopasienbpjs_v*/
         $this->execute('DROP VIEW if exists public.infopasienbpjs_v;');

         $this->execute("
            CREATE OR REPLACE VIEW public.infopasienbpjs_v AS 
 SELECT rincian.jenis,
    rincian.pendaftaran_id,
    rincian.tgl_pendaftaran,
    rincian.no_pendaftaran,
    rincian.instalasi_id,
    rincian.instalasi_nama,
    rincian.pasien_id,
    rincian.no_rekam_medik,
    rincian.nama_pasien,
    rincian.carabayar_id,
    rincian.carabayar_nama,
    rincian.penjamin_id,
    rincian.penjamin_nama,
    rincian.ruangan_id,
    rincian.ruangan_nama,
    rincian.jeniskasuspenyakit_id,
    rincian.jeniskasuspenyakit_nama,
    rincian.pegawai_id,
    rincian.dokter_dpjp,
    rincian.status_verifikasi,
    rincian.status_verif,
    rincian.umur,
    rincian.kelaspelayanan_id,
    rincian.kelaspelayanan_nama,
    rincian.jeniskelas_nama,
    rincian.pasienpulang_id,
    rincian.tglpasienpulang,
    rincian.carakeluar_id,
    rincian.carakeluar_nama,
    rincian.nosep,
    rincian.kamarruangan_id,
    rincian.kamarruangan_nokamar,
    rincian.kamartempattidur_id,
    rincian.no_tempattidur,
    rincian.pasienadmisi_id,
    rincian.status_bayar,
    rincian.stat_bayar,
    rincian.total_tagihan,
    rincian.nokartuasuransi,
    rincian.jeniskelamin,
    rincian.tanggal_lahir,
    rincian.carakeluarinacbg_id,
    rincian.carakeluar_value,
    rincian.lama_rawat,
    rincian.urutankelas,
    rincian.pengajuanklaimdetail_id,
    rincian.bpjs_id,
    rincian.kelas_bpjs,
    masukkamar_t.kelaspelayanan_id AS naik_kelas,
    rincian.naik_kelas AS naik_kelas_klaim,
    masukkamar_t.lamadirawat_kamar,
    rincian.jeniskelas_id,
    profilrumahsakit_m.kodetarifbpjs_id,
    rincian.tarif_polieksekutif,
    rincian.is_naikkelas,
    rincian.is_rawatintensif,
    rincian.lama_kelasintensif,
    rincian.ventilator,
    rincian.status_klaim,
    rincian.klaiminacbg_id,
    rincian.jenis_kelasrawat,
    rincian.is_terkirim
   FROM ( SELECT 'RJ-RD'::text AS jenis,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter_dpjp,
            pendaftaran_t.status_verifikasi,
            fgetnamalookup(pendaftaran_t.status_verifikasi) AS status_verif,
            pendaftaran_t.umur,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            jeniskelas_m.jeniskelas_nama,
            pendaftaran_t.pasienpulang_id,
            pasienpulang_t.tglpasienpulang,
            pasienpulang_t.carakeluar_id,
            carakeluar_m.carakeluar_nama,
            bpjs_t.nosep,
            NULL::integer AS kamarruangan_id,
            NULL::character varying AS kamarruangan_nokamar,
            NULL::integer AS kamartempattidur_id,
            NULL::character varying AS no_tempattidur,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.status_bayar,
            fgetnamalookup(pendaftaran_t.status_bayar) AS stat_bayar,
            cektagihaninstalasi.total AS total_tagihan,
            bpjs_t.nokartuasuransi,
            pasien_m.jeniskelamin,
            pasien_m.tanggal_lahir,
            carakeluar_m.carakeluarinacbg_id,
            fgetvaluelookup(carakeluar_m.carakeluarinacbg_id) AS carakeluar_value,
            bpjs_t.klsrawat AS kelas_bpjs,
            1 AS lama_rawat,
            kelaspelayanan_m.urutankelas,
            pengajuanklaimdetail_t.pengajuanklaimdetail_id,
            pendaftaran_t.bpjs_id,
            jeniskelas_m.jeniskelas_id,
            klaiminacbg_t.tarif_polieksekutif,
            klaiminacbg_t.is_naikkelas,
            klaiminacbg_t.is_rawatintensif,
            klaiminacbg_t.lama_kelasintensif,
            klaiminacbg_t.ventilator,
            klaiminacbg_t.naik_kelas,
            klaiminacbg_t.status_klaim,
            klaiminacbg_t.klaiminacbg_id,
            klaiminacbg_t.jenis_kelasrawat,
            klaiminacbg_t.is_terkirim
           FROM pendaftaran_t
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN jeniskelas_m ON kelaspelayanan_m.jeniskelas_id = jeniskelas_m.jeniskelas_id
             JOIN bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
             JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
             JOIN ( SELECT hitung.pendaftaran_id,
                        CASE
                            WHEN hitung.instalasi_id = 3 THEN pendaftaran_t_1.pasienadmisi_id
                            ELSE NULL::integer
                        END AS pasienadmisi_id,
                    hitung.instalasi_id,
                    hitung.instalasi_nama,
                    sum(hitung.tarif) AS total
                   FROM ( SELECT tindakanpelayanan_t.pendaftaran_id,
                            tindakanpelayanan_t.instalasi_id,
                            instalasi_m_1.instalasi_nama,
                            sum(tindakanpelayanan_t.tarif_tindakan) AS tarif
                           FROM tindakanpelayanan_t
                             LEFT JOIN instalasi_m instalasi_m_1 ON tindakanpelayanan_t.instalasi_id = instalasi_m_1.instalasi_id
                          WHERE tindakanpelayanan_t.pasienmasukpenunjang_id IS NULL
                          GROUP BY tindakanpelayanan_t.pendaftaran_id, tindakanpelayanan_t.instalasi_id, instalasi_m_1.instalasi_nama
                        UNION ALL
                         SELECT tindakanpelayanan_t.pendaftaran_id,
                            ruangan_m_1.instalasi_id,
                            instalasi_m_1.instalasi_nama,
                            sum(tindakanpelayanan_t.tarif_tindakan) AS tarif
                           FROM tindakanpelayanan_t
                             LEFT JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
                             LEFT JOIN ruangan_m ruangan_m_1 ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m_1.ruangan_id
                             LEFT JOIN instalasi_m instalasi_m_1 ON ruangan_m_1.instalasi_id = instalasi_m_1.instalasi_id
                          WHERE tindakanpelayanan_t.pasienmasukpenunjang_id IS NOT NULL
                          GROUP BY tindakanpelayanan_t.pendaftaran_id, ruangan_m_1.instalasi_id, instalasi_m_1.instalasi_nama
                        UNION ALL
                         SELECT obatalkespasien_t.pendaftaran_id,
                            ruangan_m_1.instalasi_id,
                            instalasi_m_1.instalasi_nama,
                            sum(obatalkespasien_t.hargajual_oa) AS sum
                           FROM obatalkespasien_t
                             LEFT JOIN ruangan_m ruangan_m_1 ON obatalkespasien_t.ruangan_id = ruangan_m_1.ruangan_id
                             LEFT JOIN instalasi_m instalasi_m_1 ON ruangan_m_1.instalasi_id = instalasi_m_1.instalasi_id
                          WHERE obatalkespasien_t.resepturdetail_id IS NULL
                          GROUP BY obatalkespasien_t.pendaftaran_id, ruangan_m_1.instalasi_id, instalasi_m_1.instalasi_nama
                        UNION ALL
                         SELECT obatalkespasien_t.pendaftaran_id,
                            ruangan_m_1.instalasi_id,
                            instalasi_m_1.instalasi_nama,
                            sum(obatalkespasien_t.hargajual_oa) AS sum
                           FROM obatalkespasien_t
                             LEFT JOIN resepturdetail_t ON obatalkespasien_t.resepturdetail_id = resepturdetail_t.resepturdetail_id
                             LEFT JOIN reseptur_t ON resepturdetail_t.reseptur_id = reseptur_t.reseptur_id
                             LEFT JOIN ruangan_m ruangan_m_1 ON reseptur_t.ruanganreseptur_id = ruangan_m_1.ruangan_id
                             LEFT JOIN instalasi_m instalasi_m_1 ON ruangan_m_1.instalasi_id = instalasi_m_1.instalasi_id
                          WHERE obatalkespasien_t.resepturdetail_id IS NOT NULL
                          GROUP BY obatalkespasien_t.pendaftaran_id, ruangan_m_1.instalasi_id, instalasi_m_1.instalasi_nama) hitung
                     JOIN pendaftaran_t pendaftaran_t_1 ON hitung.pendaftaran_id = pendaftaran_t_1.pendaftaran_id
                  GROUP BY hitung.pendaftaran_id, hitung.instalasi_id, hitung.instalasi_nama, pendaftaran_t_1.pasienadmisi_id, pendaftaran_t_1.instalasi_id) cektagihaninstalasi ON pendaftaran_t.pendaftaran_id = cektagihaninstalasi.pendaftaran_id AND pendaftaran_t.instalasi_id = cektagihaninstalasi.instalasi_id
             LEFT JOIN pengajuanklaimdetail_t ON pendaftaran_t.pendaftaran_id = pengajuanklaimdetail_t.pendaftaran_id AND pengajuanklaimdetail_t.is_deleted = false
             LEFT JOIN klaiminacbg_t ON pendaftaran_t.pendaftaran_id = klaiminacbg_t.pendaftaran_id AND klaiminacbg_t.is_deleted = false
          WHERE pendaftaran_t.carabayar_id = 6 AND pendaftaran_t.instalasi_id <> 3
        UNION ALL
         SELECT 'RI'::text AS jenis,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasienadmisi_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pasienadmisi_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter_dpjp,
            pasienadmisi_t.status_verifikasi,
            fgetnamalookup(pasienadmisi_t.status_verifikasi) AS status_verif,
            pendaftaran_t.umur,
            pasienadmisi_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            jeniskelas_m.jeniskelas_nama,
            pendaftaran_t.pasienpulang_id,
            pasienpulang_t.tglpasienpulang,
            pasienpulang_t.carakeluar_id,
            carakeluar_m.carakeluar_nama,
            bpjs_t.nosep,
            pasienadmisi_t.kamarruangan_id,
            kamarruangan_m.kamarruangan_nokamar,
            pasienadmisi_t.kamartempattidur_id,
            kamartempattidur_m.no_tempattidur,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.status_bayar,
            fgetnamalookup(pendaftaran_t.status_bayar) AS stat_bayar,
            cektagihaninstalasi.total AS total_tagihan,
            bpjs_t.nokartuasuransi,
            pasien_m.jeniskelamin,
            pasien_m.tanggal_lahir,
            carakeluar_m.carakeluarinacbg_id,
            fgetvaluelookup(carakeluar_m.carakeluarinacbg_id) AS carakeluar_value,
            bpjs_t.klsrawat AS kelas_bpjs,
            pasienpulang_t.lama_rawat,
            kelaspelayanan_m.urutankelas,
            pengajuanklaimdetail_t.pengajuanklaimdetail_id,
            pasienadmisi_t.bpjs_id,
            jeniskelas_m.jeniskelas_id,
            klaiminacbg_t.tarif_polieksekutif,
            klaiminacbg_t.is_naikkelas,
            klaiminacbg_t.is_rawatintensif,
            klaiminacbg_t.lama_kelasintensif,
            klaiminacbg_t.ventilator,
            klaiminacbg_t.naik_kelas,
            klaiminacbg_t.status_klaim,
            klaiminacbg_t.klaiminacbg_id,
            klaiminacbg_t.jenis_kelasrawat,
            klaiminacbg_t.is_terkirim
           FROM pendaftaran_t
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
             JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN jeniskelas_m ON kelaspelayanan_m.jeniskelas_id = jeniskelas_m.jeniskelas_id
             JOIN bpjs_t ON pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id
             JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
             JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
             JOIN ( SELECT hitung.pendaftaran_id,
                        CASE
                            WHEN hitung.instalasi_id = 3 THEN pendaftaran_t_1.pasienadmisi_id
                            ELSE NULL::integer
                        END AS pasienadmisi_id,
                    hitung.instalasi_id,
                    hitung.instalasi_nama,
                    sum(hitung.tarif) AS total
                   FROM ( SELECT tindakanpelayanan_t.pendaftaran_id,
                            tindakanpelayanan_t.instalasi_id,
                            instalasi_m_1.instalasi_nama,
                            sum(tindakanpelayanan_t.tarif_tindakan) AS tarif
                           FROM tindakanpelayanan_t
                             LEFT JOIN instalasi_m instalasi_m_1 ON tindakanpelayanan_t.instalasi_id = instalasi_m_1.instalasi_id
                          WHERE tindakanpelayanan_t.pasienmasukpenunjang_id IS NULL
                          GROUP BY tindakanpelayanan_t.pendaftaran_id, tindakanpelayanan_t.instalasi_id, instalasi_m_1.instalasi_nama
                        UNION ALL
                         SELECT tindakanpelayanan_t.pendaftaran_id,
                            ruangan_m_1.instalasi_id,
                            instalasi_m_1.instalasi_nama,
                            sum(tindakanpelayanan_t.tarif_tindakan) AS tarif
                           FROM tindakanpelayanan_t
                             LEFT JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
                             LEFT JOIN ruangan_m ruangan_m_1 ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m_1.ruangan_id
                             LEFT JOIN instalasi_m instalasi_m_1 ON ruangan_m_1.instalasi_id = instalasi_m_1.instalasi_id
                          WHERE tindakanpelayanan_t.pasienmasukpenunjang_id IS NOT NULL
                          GROUP BY tindakanpelayanan_t.pendaftaran_id, ruangan_m_1.instalasi_id, instalasi_m_1.instalasi_nama
                        UNION ALL
                         SELECT obatalkespasien_t.pendaftaran_id,
                            ruangan_m_1.instalasi_id,
                            instalasi_m_1.instalasi_nama,
                            sum(obatalkespasien_t.hargajual_oa) AS sum
                           FROM obatalkespasien_t
                             LEFT JOIN ruangan_m ruangan_m_1 ON obatalkespasien_t.ruangan_id = ruangan_m_1.ruangan_id
                             LEFT JOIN instalasi_m instalasi_m_1 ON ruangan_m_1.instalasi_id = instalasi_m_1.instalasi_id
                          WHERE obatalkespasien_t.resepturdetail_id IS NULL
                          GROUP BY obatalkespasien_t.pendaftaran_id, ruangan_m_1.instalasi_id, instalasi_m_1.instalasi_nama
                        UNION ALL
                         SELECT obatalkespasien_t.pendaftaran_id,
                            ruangan_m_1.instalasi_id,
                            instalasi_m_1.instalasi_nama,
                            sum(obatalkespasien_t.hargajual_oa) AS sum
                           FROM obatalkespasien_t
                             LEFT JOIN resepturdetail_t ON obatalkespasien_t.resepturdetail_id = resepturdetail_t.resepturdetail_id
                             LEFT JOIN reseptur_t ON resepturdetail_t.reseptur_id = reseptur_t.reseptur_id
                             LEFT JOIN ruangan_m ruangan_m_1 ON reseptur_t.ruanganreseptur_id = ruangan_m_1.ruangan_id
                             LEFT JOIN instalasi_m instalasi_m_1 ON ruangan_m_1.instalasi_id = instalasi_m_1.instalasi_id
                          WHERE obatalkespasien_t.resepturdetail_id IS NOT NULL
                          GROUP BY obatalkespasien_t.pendaftaran_id, ruangan_m_1.instalasi_id, instalasi_m_1.instalasi_nama) hitung
                     JOIN pendaftaran_t pendaftaran_t_1 ON hitung.pendaftaran_id = pendaftaran_t_1.pendaftaran_id
                  GROUP BY hitung.pendaftaran_id, hitung.instalasi_id, hitung.instalasi_nama, pendaftaran_t_1.pasienadmisi_id, pendaftaran_t_1.instalasi_id) cektagihaninstalasi ON pendaftaran_t.pasienadmisi_id = cektagihaninstalasi.pasienadmisi_id AND pendaftaran_t.pendaftaran_id = cektagihaninstalasi.pendaftaran_id
             LEFT JOIN pengajuanklaimdetail_t ON pendaftaran_t.pendaftaran_id = pengajuanklaimdetail_t.pendaftaran_id AND pengajuanklaimdetail_t.is_deleted = false
             LEFT JOIN klaiminacbg_t ON pendaftaran_t.pendaftaran_id = klaiminacbg_t.pendaftaran_id AND klaiminacbg_t.is_deleted = false
          WHERE pasienadmisi_t.carabayar_id = 6) rincian
     LEFT JOIN masukkamar_t ON rincian.pasienadmisi_id = masukkamar_t.pasienadmisi_id AND masukkamar_t.pindahkamar_id IS NULL
     LEFT JOIN profilrumahsakit_m ON profilrumahsakit_m.is_deleted = false AND profilrumahsakit_m.is_active = true
  GROUP BY rincian.jenis, rincian.pendaftaran_id, rincian.tgl_pendaftaran, rincian.no_pendaftaran, rincian.instalasi_id, rincian.instalasi_nama, rincian.pasien_id, rincian.no_rekam_medik, rincian.nama_pasien, rincian.carabayar_id, rincian.carabayar_nama, rincian.penjamin_id, rincian.penjamin_nama, rincian.ruangan_id, rincian.ruangan_nama, rincian.jeniskasuspenyakit_id, rincian.jeniskasuspenyakit_nama, rincian.pegawai_id, rincian.dokter_dpjp, rincian.status_verifikasi, rincian.status_verif, rincian.umur, rincian.kelaspelayanan_id, rincian.kelaspelayanan_nama, rincian.jeniskelas_nama, rincian.pasienpulang_id, rincian.tglpasienpulang, rincian.carakeluar_id, rincian.carakeluar_nama, rincian.nosep, rincian.kamarruangan_id, rincian.kamarruangan_nokamar, rincian.kamartempattidur_id, rincian.no_tempattidur, rincian.pasienadmisi_id, rincian.status_bayar, rincian.stat_bayar, rincian.total_tagihan, rincian.nokartuasuransi, rincian.jeniskelamin, rincian.tanggal_lahir, rincian.carakeluarinacbg_id, rincian.carakeluar_value, rincian.kelas_bpjs, rincian.lama_rawat, rincian.urutankelas, rincian.pengajuanklaimdetail_id, rincian.bpjs_id, masukkamar_t.kelaspelayanan_id, masukkamar_t.lamadirawat_kamar, rincian.jeniskelas_id, profilrumahsakit_m.kodetarifbpjs_id, rincian.tarif_polieksekutif, rincian.is_naikkelas, rincian.is_rawatintensif, rincian.lama_kelasintensif, rincian.ventilator, rincian.naik_kelas, rincian.status_klaim, rincian.klaiminacbg_id, rincian.jenis_kelasrawat, rincian.is_terkirim;
");

         $this->execute('ALTER TABLE public.infopasienbpjs_v
  OWNER TO postgres;');

/*infopasienrskoreksidetail_v*/
         $this->execute('DROP VIEW if exists public.infopasienrskoreksidetail_v;');

         $this->execute("
            CREATE OR REPLACE VIEW public.infopasienrskoreksidetail_v AS 
 SELECT 'RJ'::text AS jenis_rawat,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pasienmorbiditas_t.pasienmorbiditas_id AS diagnosapasien_id,
        CASE
            WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 1 THEN pasienmorbiditas_t.diagnosa_pasien
            ELSE NULL::json
        END AS diagnosa_masuk,
        CASE
            WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
            ELSE NULL::json
        END AS diagnosa_utama,
        CASE
            WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 3 THEN pasienmorbiditas_t.diagnosa_pasien
            ELSE NULL::json
        END AS diagnosa_penyerta,
        CASE
            WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 6 THEN pasienmorbiditas_t.diagnosa_pasien
            ELSE NULL::json
        END AS diagnosa_terapi
   FROM pendaftaran_t
     JOIN pasienmorbiditas_t ON pendaftaran_t.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false
UNION ALL
 SELECT 'RD'::text AS jenis_rawat,
    pendaftaran_t.pendaftaran_id,
    NULL::integer AS pasienadmisi_id,
    cppt_t.cppt_id AS diagnosapasien_id,
    NULL::json AS diagnosa_masuk,
    cppt_t.a_diag_utama AS diagnosa_utama,
    cppt_t.a_diag_penyerta AS diagnosa_penyerta,
    NULL::json AS diagnosa_terapi
   FROM pendaftaran_t
     JOIN ( SELECT cppt_t_1.cppt_id,
            cppt_t_1.pendaftaran_id,
            cppt_t_1.pasienadmisi_id,
            cppt_t_1.a_diag_utama,
            cppt_t_1.a_diag_penyerta
           FROM cppt_t cppt_t_1
             JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                    cppt_last.pendaftaran_id,
                    cppt_last.pasienadmisi_id
                   FROM cppt_t cppt_last
                  WHERE cppt_last.is_deleted = false
                  GROUP BY cppt_last.pendaftaran_id, cppt_last.pasienadmisi_id) cppt_max ON cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id AND cppt_t_1.cppt_id = cppt_max.cppt_id) cppt_t ON pendaftaran_t.pendaftaran_id = cppt_t.pendaftaran_id
UNION ALL
 SELECT 'RI'::text AS jenis_rawat,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    resumemedisri_t.resumemedisri_id AS diagnosapasien_id,
    resumemedisri_t.diag_masuk AS diagnosa_masuk,
    resumemedisri_t.diag_utama AS diagnosa_utama,
    resumemedisri_t.diag_penyerta AS diagnosa_penyerta,
    resumemedisri_t.prosedur_diag AS diagnosa_terapi
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN resumemedisri_t ON pendaftaran_t.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t.pasienadmisi_id = resumemedisri_t.pasienadmisi_id;
");

         $this->execute('ALTER TABLE public.infopasienrskoreksidetail_v
  OWNER TO postgres;');
          
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191018_033513_perbahan_schema_20191008 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191018_033513_perbahan_schema_20191008 cannot be reverted.\n";

        return false;
    }
    */
}

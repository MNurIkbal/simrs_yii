<?php

use yii\db\Migration;

/**
 * Class m190926_092853_optimize_view_15
 */
class m190926_092853_optimize_view_15 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /*sync_pasien_copy*/
         $this->execute('DROP VIEW if exists public.sync_pasien_copy;');

        /*sync_pendaftaran_bu*/
         $this->execute('DROP VIEW if exists public.sync_pendaftaran_bu;');

         /*sync_bpjs_bu*/
         $this->execute('DROP VIEW if exists public.sync_bpjs_bu;');


/*laporandiagnosapasien_v*/
    $this->execute('DROP VIEW if exists public.laporandiagnosapasien_v;');

    $this->execute("
        CREATE OR REPLACE VIEW public.laporandiagnosapasien_v AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    pasien_m.jeniskelamin,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    pasienmorbiditas_t.tglmorbiditas AS tgl_diagnosa,
    pasienmorbiditas_t.pasienmorbiditas_id,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    pasienmorbiditas_t.is_deleted,
    pasienmorbiditas_t.kelompokdiagnosa_id,
    kelompokdiagnosa_m.kelompokdiagnosa_nama,
    pasienmorbiditas_t.diagnosa_pasien,
    pasienmorbiditas_t.diagnosa_pasien ->> 'id'::text AS diagnosa_id,
    pasienmorbiditas_t.diagnosa_pasien ->> 'text'::text AS diagnosa_nama,
    pasienmorbiditas_t.diagnosa_pasien ->> 'kode'::text AS diagnosa_kode,
    klasifikasidiagnosa_m.klasifikasidiagnosa_nama
   FROM pasienmorbiditas_t
     JOIN pendaftaran_t ON pasienmorbiditas_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN kelompokdiagnosa_m ON pasienmorbiditas_t.kelompokdiagnosa_id = kelompokdiagnosa_m.kelompokdiagnosa_id
     JOIN ruangan_m ON pasienmorbiditas_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN diagnosa_m ON (pasienmorbiditas_t.diagnosa_pasien ->> 'kode'::text) = diagnosa_m.diagnosa_kode::text
     LEFT JOIN klasifikasidiagnosa_m ON diagnosa_m.klasifikasidiagnosa_id = klasifikasidiagnosa_m.klasifikasidiagnosa_id
  WHERE pasienmorbiditas_t.is_active = true AND pasienmorbiditas_t.is_deleted = false;");

    $this->execute('ALTER TABLE public.laporandiagnosapasien_v
  OWNER TO postgres;');

/*carakeluar_v*/
    $this->execute('DROP VIEW if exists public.carakeluar_v;');

    $this->execute("
        CREATE OR REPLACE VIEW public.carakeluar_v AS 
 SELECT carakeluar_m.carakeluar_id,
    carakeluar_m.carakeluar_nama,
    carakeluar_m.carakeluar_namalain,
    carakeluar_m.carakeluar_kode,
    carakeluar_m.carakeluar_urutan,
    carakeluar_m.catatan,
    carakeluar_m.is_active,
    carakeluar_m.carakeluarinacbg_id,
    fgetnamalookup(carakeluar_m.carakeluarinacbg_id) AS carakeluarinacbg_nama,
    fgetvaluelookup(carakeluar_m.carakeluarinacbg_id) AS carakeluarinacbg_kode
   FROM carakeluar_m
  WHERE carakeluar_m.is_deleted = false;");

    $this->execute('ALTER TABLE public.carakeluar_v
  OWNER TO postgres;');

/*konfigantrianfarmasi_v*/
    $this->execute('DROP VIEW if exists public.konfigantrianfarmasi_v;');

    $this->execute("
        CREATE OR REPLACE VIEW public.konfigantrianfarmasi_v AS 
 SELECT konfigantrian_m.konfigantrian_id,
    layarantrian_m.layarantrian_id,
    konfigantrian_m.jenisantrian_id,
    konfigantrian_m.fungsiantrian_id,
    konfigantrian_m.carabayar_id,
    layarantrian_m.layarantrian_nama,
    fgetnamalookup(konfigantrian_m.jenisantrian_id) AS jenis_antrian,
    fgetnamalookup(konfigantrian_m.fungsiantrian_id) AS fungsi_antrian,
    fgetvaluelookup(konfigantrian_m.fungsiantrian_id) AS lookup_value,
    carabayar_m.carabayar_nama,
    konfigantrian_m.is_default,
    carabayar_m.is_penjamin,
    konfigantrian_m.kode_antrian,
    konfigantrian_m.instalasi_id,
    instalasi_m.instalasi_nama,
    konfigantrian_m.groupcarabayar_id,
    fgetnamalookup(konfigantrian_m.groupcarabayar_id) AS group_carabayar
   FROM konfigantrian_m
     LEFT JOIN layarantrian_m ON konfigantrian_m.layarantrian_id = layarantrian_m.layarantrian_id
     LEFT JOIN carabayar_m ON konfigantrian_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN instalasi_m ON konfigantrian_m.instalasi_id = instalasi_m.instalasi_id
  WHERE konfigantrian_m.is_active = true AND konfigantrian_m.is_deleted = false AND konfigantrian_m.jenisantrian_id = 176;
");

    $this->execute('ALTER TABLE public.konfigantrianfarmasi_v
  OWNER TO postgres;');

/*konfigantrian_v*/
    $this->execute('DROP VIEW if exists public.konfigantrian_v;');

    $this->execute("
        CREATE OR REPLACE VIEW public.konfigantrian_v AS 
 SELECT konfigantrian_m.konfigantrian_id,
    konfigantrian_m.layarantrian_id,
    konfigantrian_m.jenisantrian_id,
    konfigantrian_m.fungsiantrian_id,
    konfigantrian_m.carabayar_id,
    fgetnamalookup(konfigantrian_m.jenisantrian_id) AS jenis_antrian,
    fgetnamalookup(konfigantrian_m.fungsiantrian_id) AS fungsi_antrian,
    fgetvaluelookup(konfigantrian_m.fungsiantrian_id) AS lookup_value,
    carabayar_m.carabayar_nama,
    konfigantrian_m.is_active AS is_default,
    carabayar_m.is_penjamin,
    konfigantrian_m.kode_antrian,
    konfigantrian_m.instalasi_id,
    instalasi_m.instalasi_nama,
    carabayar_m.groupcarabayar_id,
    fgetnamalookup(konfigantrian_m.groupcarabayar_id) AS group_carabayar,
    konfigantrian_m.penomoran_id,
    konfigantrian_m.klasifikasipasien_id,
    klasifikasipasien_m.klasifikasipasien_nama,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    konfigantrian_m.groupcarabayar_id AS group_id
   FROM konfigantrian_m
     LEFT JOIN carabayar_m ON konfigantrian_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN instalasi_m ON konfigantrian_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ruangan_m ON konfigantrian_m.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN klasifikasipasien_m ON konfigantrian_m.klasifikasipasien_id = klasifikasipasien_m.klasifikasipasien_id
  WHERE konfigantrian_m.is_deleted = false AND (konfigantrian_m.jenisantrian_id = ANY (ARRAY[176, 177, 178, 179, 312]));
");

    $this->execute('ALTER TABLE public.konfigantrian_v
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
    rincian.jenis_kelasrawat
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
            klaiminacbg_t.jenis_kelasrawat
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
            klaiminacbg_t.jenis_kelasrawat
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
  GROUP BY rincian.jenis, rincian.pendaftaran_id, rincian.tgl_pendaftaran, rincian.no_pendaftaran, rincian.instalasi_id, rincian.instalasi_nama, rincian.pasien_id, rincian.no_rekam_medik, rincian.nama_pasien, rincian.carabayar_id, rincian.carabayar_nama, rincian.penjamin_id, rincian.penjamin_nama, rincian.ruangan_id, rincian.ruangan_nama, rincian.jeniskasuspenyakit_id, rincian.jeniskasuspenyakit_nama, rincian.pegawai_id, rincian.dokter_dpjp, rincian.status_verifikasi, rincian.status_verif, rincian.umur, rincian.kelaspelayanan_id, rincian.kelaspelayanan_nama, rincian.jeniskelas_nama, rincian.pasienpulang_id, rincian.tglpasienpulang, rincian.carakeluar_id, rincian.carakeluar_nama, rincian.nosep, rincian.kamarruangan_id, rincian.kamarruangan_nokamar, rincian.kamartempattidur_id, rincian.no_tempattidur, rincian.pasienadmisi_id, rincian.status_bayar, rincian.stat_bayar, rincian.total_tagihan, rincian.nokartuasuransi, rincian.jeniskelamin, rincian.tanggal_lahir, rincian.carakeluarinacbg_id, rincian.carakeluar_value, rincian.kelas_bpjs, rincian.lama_rawat, rincian.urutankelas, rincian.pengajuanklaimdetail_id, rincian.bpjs_id, masukkamar_t.kelaspelayanan_id, masukkamar_t.lamadirawat_kamar, rincian.jeniskelas_id, profilrumahsakit_m.kodetarifbpjs_id, rincian.tarif_polieksekutif, rincian.is_naikkelas, rincian.is_rawatintensif, rincian.lama_kelasintensif, rincian.ventilator, rincian.naik_kelas, rincian.status_klaim, rincian.klaiminacbg_id, rincian.jenis_kelasrawat;
");

    $this->execute('ALTER TABLE public.infopasienbpjs_v
  OWNER TO postgres;');

/*sync_pasien*/
    $this->execute('DROP VIEW if exists public.sync_pasien;');

    $this->execute('
        CREATE OR REPLACE VIEW public.sync_pasien AS 
 SELECT pasien_m.no_rekam_medik::integer AS "Kode_CM",
    \'0\'::text AS "Kode_stsCM",
    fgetvaluelookup(pasien_m.namadepan::integer) AS "Gelar_Depan",
    pasien_m.nama_pasien AS "Nama",
    NULL::text AS "Gelar_Belakang",
    pasien_m.jeniskelamin,
    fgetkodelookup(pasien_m.jeniskelamin::integer) AS "Kode_JnsKelamin",
    pasien_m.nama_ibu AS "Ibu_kandung",
    pasien_m.tempat_lahir AS "Tempat_lahir",
    pasien_m.tanggal_lahir AS "Tgl_lahir",
    fgetkodelookup(pasien_m.agama::integer) AS "Kode_Agama",
    pasien_m.alamat_pasien AS "Alamat",
    pasien_m.rt AS "RT",
    pasien_m.rw AS "RW",
    NULL::text AS "Kode_Kelurahan",
    NULL::text AS "Kode_Kecamatan",
    NULL::text AS "Kode_Kabupaten",
    NULL::text AS "Kode_Propinsi",
    fgetkodelookup(pasien_m.warga_negara::integer) AS "Kode_Negara",
    fgetkodelookup(pasien_m.warga_negara::integer) AS "Warga_Negara",
    pendidikan_m.pendidikan_namalainnya AS "Kode_Pendidikan",
    pekerjaan_m.pekerjaan_namalainnya AS "Kode_Pekerjaan",
    fgetkodelookup(pasien_m.statusperkawinan::integer) AS "Kode_stsKawin",
    pasien_m.created_date AS "Tgl_Daftar",
    ( SELECT carabayar_m.carabayar_kode
           FROM pendaftaran_t
             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
          WHERE pasien_m.pasien_id = pendaftaran_t.pasien_id
          ORDER BY pendaftaran_t.tgl_pendaftaran DESC
         LIMIT 1) AS "Kode_StsBayar",
    ( SELECT
                CASE
                    WHEN pendaftaran_t.bpjs_id IS NOT NULL THEN \'1\'::character varying
                    ELSE asalrujukan_m.asalrujukan_kode
                END AS "Kode_Rujukan"
           FROM pendaftaran_t
             LEFT JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
             LEFT JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
          WHERE pasien_m.pasien_id = pendaftaran_t.pasien_id
          ORDER BY pendaftaran_t.tgl_pendaftaran DESC
         LIMIT 1) AS "Kode_Rujukan",
    ( SELECT
                CASE
                    WHEN pendaftaran_t.bpjs_id IS NOT NULL THEN bpjs_t.ppkrujukan
                    ELSE rujukandari_m.nama_perujuk
                END AS "Nama_Perujuk"
           FROM pendaftaran_t
             LEFT JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
             LEFT JOIN rujukandari_m ON rujukan_t.rujukandari_id = rujukandari_m.rujukandari_id
             LEFT JOIN bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
          WHERE pasien_m.pasien_id = pendaftaran_t.pasien_id
          ORDER BY pendaftaran_t.tgl_pendaftaran DESC
         LIMIT 1) AS "Nama_Perujuk",
    ( SELECT penjamin_m.s_kode
           FROM pendaftaran_t
             LEFT JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
          WHERE pasien_m.pasien_id = pendaftaran_t.pasien_id
          ORDER BY pendaftaran_t.tgl_pendaftaran DESC
         LIMIT 1) AS "Kode_Perusahaan",
    pasien_m.no_identitas_pasien AS "No_Identitas",
    pasien_m.no_telepon_pasien AS "Telepon",
    ( SELECT pendaftaran_t.tgl_pendaftaran
           FROM pendaftaran_t
          WHERE pasien_m.pasien_id = pendaftaran_t.pasien_id
          ORDER BY pendaftaran_t.tgl_pendaftaran DESC
         LIMIT 1) AS tgl_kunjungan,
    ( SELECT pendaftaran_t.keterangan_pendaftaran
           FROM pendaftaran_t
          WHERE pasien_m.pasien_id = pendaftaran_t.pasien_id
          ORDER BY pendaftaran_t.tgl_pendaftaran DESC
         LIMIT 1) AS "KETERANGAN",
    ( SELECT bpjs_t.nokartuasuransi
           FROM pendaftaran_t
             LEFT JOIN bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
          WHERE pasien_m.pasien_id = pendaftaran_t.pasien_id
          ORDER BY pendaftaran_t.tgl_pendaftaran DESC
         LIMIT 1) AS "No_Kartu",
    0 AS retensi_status,
    NULL::text AS retensi_nprs,
    NULL::text AS retensi_tgl_acuan
   FROM pasien_m
     LEFT JOIN pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
     LEFT JOIN pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
  WHERE pasien_m.is_aps IS FALSE;
');

    $this->execute('ALTER TABLE public.sync_pasien
  OWNER TO postgres;');

/*sync_pendaftaran*/
    $this->execute('DROP VIEW if exists public.sync_pendaftaran;');

    $this->execute("
        CREATE OR REPLACE VIEW public.sync_pendaftaran AS 
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran AS \"No_Reg\",
    to_char(pendaftaran_t.tgl_pendaftaran, 'YYYY-MM-DD'::text) AS \"Tanggal\",
    to_char(pendaftaran_t.tgl_pendaftaran, 'HH24:MI:SS'::text) AS \"Jam\",
    pasien_m.pasien_id,
    pasien_m.no_rekam_medik AS \"Kode_CM\",
    ruangan_m.ruangan_id,
        CASE
            WHEN ruangan_m.kode_ruanganpoli IS NULL THEN ''::character varying(3)
            ELSE ruangan_m.kode_ruanganpoli
        END AS kode_subunit,
    'E000'::text AS \"Kode_Event\",
    pendaftaran_t.kunjungan,
    fgetkodelookup(pendaftaran_t.kunjungan::integer) AS \"Kode_StsCM\",
    pendidikan_m.pendidikan_kode AS \"Kode_Pendidikan\",
    pekerjaan_m.pekerjaan_kode AS \"Kode_Pekerjaan\",
    fgetkodelookup(pasien_m.statusperkawinan::integer) AS \"Kode_StsKawin\",
    pendaftaran_t.rujukan_id,
    pendaftaran_t.bpjs_id,
    rujukan_t.asalrujukan_id,
        CASE
            WHEN pendaftaran_t.bpjs_id IS NOT NULL THEN '1'::character varying
            ELSE asalrujukan_m.asalrujukan_kode
        END AS \"Kode_Rujukan\",
    rujukan_t.rujukandari_id,
        CASE
            WHEN pendaftaran_t.bpjs_id IS NOT NULL THEN bpjs_t.ppkpelayanan
            ELSE rujukandari_m.kode_ppk
        END AS kode_ppk,
        CASE
            WHEN pendaftaran_t.bpjs_id IS NOT NULL THEN bpjs_t.ppkrujukan
            ELSE rujukandari_m.nama_perujuk
        END AS \"Nama_Perujuk\",
    carabayar_m.carabayar_kode AS \"Kode_StsBayar\",
    penanggungjawab_m.penanggungjawab_nama AS \"Nama_Pengantar\",
    penanggungjawab_m.penanggungjawab_alamat AS \"Alamat_Pengantar\",
    penanggungjawab_m.penanggungjawab_notelp AS \"Telp_Pengantar\",
    penanggungjawab_m.penanggungjawab_nohp AS hp_pengantar,
    fgetnamalookup(penanggungjawab_m.pengantar::integer) AS \"Hub_Pengantar\",
    penanggungjawab_m.penanggungjawab_nama AS \"Nama_Penanggung\",
    penanggungjawab_m.penanggungjawab_alamat AS \"Alamat_Penanggung\",
    penanggungjawab_m.penanggungjawab_notelp AS \"Telp_Penanggung\",
    penanggungjawab_m.penanggungjawab_nohp AS hp_penanggung,
    fgetnamalookup(penanggungjawab_m.pengantar::integer) AS \"Hub_Penanggung\",
    bpjs_t.tglsep AS tgl_commit,
    pendaftaran_t.keterangan_pendaftaran AS \"Keterangan\",
    NULL::text AS \"CEK\",
    peg_pendaftaran.nomorindukpegawai AS \"Petugas\",
    dokter.nomorindukpegawai AS \"NPRS_PJawab\",
    bpjs_t.klsrawat AS \"Hak_Kelas\",
        CASE
            WHEN bpjs_t.politujuan IS NULL THEN ''::character varying(100)
            ELSE bpjs_t.politujuan
        END AS kode_subunitasal,
    bpjs_t.diagnosaawal AS \"Diag_Awal\",
    0 AS \"IS_CLONED\",
    penjamin_m.s_kode AS \"KODE_PERUSAHAAN\",
    bpjs_t.nokartuasuransi AS no_kartu,
    bpjs_t.nosep AS no_sep
   FROM pendaftaran_t
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
     LEFT JOIN pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
     LEFT JOIN rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN rujukandari_m ON rujukan_t.rujukandari_id = rujukandari_m.rujukandari_id
     LEFT JOIN bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id
     LEFT JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
     LEFT JOIN pegawai_m dokter ON pendaftaran_t.pegawai_id = dokter.pegawai_id
     LEFT JOIN loginpemakai_k ON pendaftaran_t.created_by = loginpemakai_k.loginpemakai_id
     LEFT JOIN pegawai_m peg_pendaftaran ON loginpemakai_k.pegawai_id = peg_pendaftaran.pegawai_id;
     ");

    $this->execute('ALTER TABLE public.sync_pendaftaran
  OWNER TO postgres;');

/*infopasiengizi_v*/
    $this->execute('DROP VIEW if exists public.infopasiengizi_v;');

    $this->execute("
        CREATE OR REPLACE VIEW public.infopasiengizi_v AS 
 SELECT pasienadmisi_t.pasienadmisi_id,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.pegawai_id AS dokter_pendaftaran_id,
    pasienadmisi_t.pegawai_id AS dokter_admisi_id,
    pasienadmisi_t.carabayar_id,
    pasienadmisi_t.penjamin_id,
    bpjs_t.klsrawat,
    pasienadmisi_t.kelaspelayanan_id,
    pasienadmisi_t.ruangan_id,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien,
    fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
    dokter_pendaftaran.nama_pegawai AS dokter_pendaftaran,
    dokter_admisi.nama_pegawai AS dokter_admisi,
    bpjs_t.klsrawat AS hak_kelas,
    kls_bpjs.kelaspelayanan_nama AS hak_kelas_nama,
    kelaspelayanan_m.kelaspelayanan_nama AS kelas_pelayanan,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    pasienadmisi_t.tgl_pulang,
    rencanapulang_t.rencana_pulang,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    pasienadmisi_t.status_ranap,
    fgetnamalookup(pasienadmisi_t.status_ranap) AS stat_ranap,
    pendaftaran_t.jeniskasuspenyakit_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pendaftaran_t.golonganumur_id,
    pasienadmisi_t.tgl_pindahkamar,
    asesmenmedis_t.r_alergiobat,
    asesmenmedis_t.is_hamil,
    asesmenmedis_t.sumber_info,
    asesmenmedis_t.sumber_hubungan,
    asesmenmedis_t.luas_permukaantubuh,
    asesmenmedis_t.tinggi_badan,
    asesmenmedis_t.berat_badan,
    asesmenmedis_t.r_penyakitkeluarga,
    asesmenmedis_t.r_imunisasi,
    asesmenmedis_t.diagnosa_id,
    asesmenmedis_t.diagnosa_id AS diagnosa_nama,
    pasienadmisi_t.kamarruangan_id,
    pasienadmisi_t.kamartempattidur_id,
    pasien_m.photopasien,
    pendaftaran_t.caramasuk_id,
    caramasuk_m.caramasuk_nama,
    asesmenmedis_t.discharge_plan,
    asesmenawal_t.obatan_rumah,
    asesmenawal_t.obat_darirumah,
        CASE
            WHEN (( SELECT count(*) AS count
               FROM cppt_t x
              WHERE x.pendaftaran_id = pendaftaran_t.pendaftaran_id AND x.is_instruksi_pulang = true)) > 0 THEN true
            ELSE false
        END AS instruksi_pulang,
    kelaspelayanan_m.kelaspelayanan_nama,
    pasienadmisi_t.pasienpulang_id,
    pasien_m.jeniskelamin,
    skrininggizi_t.skrininggizi_id,
    skrininggizi_t.skor,
        CASE
            WHEN skrininggizi_t.status_asesmen IS NULL THEN 80
            ELSE skrininggizi_t.status_asesmen
        END AS status_asesmen,
        CASE
            WHEN skrininggizi_t.status_asesmen IS NULL THEN 'Tidak Asesmen'::character varying
            ELSE fgetnamalookupkeperawatan(skrininggizi_t.status_asesmen)
        END AS stat_asesmen_gizi,
    pasienpulang_t.carakeluar_id,
    carakeluar_m.carakeluar_nama,
    asesmenmedis_t.is_merokok,
    asesmenmedis_t.jml_rokok,
    pekerjaan_m.pekerjaan_nama,
    pendidikan_m.pendidikan_nama,
    asesmenawal_t.asmen_riwayat,
    asesmenmedis_t.r_peskk
   FROM pendaftaran_t
     JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN pegawai_m dokter_pendaftaran ON pendaftaran_t.pegawai_id = dokter_pendaftaran.pegawai_id
     JOIN pegawai_m dokter_admisi ON pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id
     JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN bpjs_t ON pendaftaran_t.pendaftaran_id = bpjs_t.pendaftaran_id
     JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN kelaspelayanan_m kls_bpjs ON bpjs_t.klsrawat = kls_bpjs.bpjs_kelas
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN asesmenmedis_t ON pendaftaran_t.pendaftaran_id = asesmenmedis_t.pendaftaran_id AND asesmenmedis_t.is_deleted = false
     LEFT JOIN caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
     LEFT JOIN asesmenawal_t ON pasienadmisi_t.pasienadmisi_id = asesmenawal_t.pasienadmisi_id
     LEFT JOIN rencanapulang_t ON pasienadmisi_t.pasienadmisi_id = rencanapulang_t.pasienadmisi_id AND rencanapulang_t.is_deleted = false
     LEFT JOIN skrininggizi_t ON pendaftaran_t.pendaftaran_id = skrininggizi_t.pendaftaran_id AND skrininggizi_t.is_active = true
     LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
     LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
     LEFT JOIN pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
     LEFT JOIN pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
  WHERE pasienadmisi_t.is_active = true AND pasienadmisi_t.is_deleted = false AND pasienadmisi_t.status_ranap <> 453;
");

    $this->execute('ALTER TABLE public.infopasiengizi_v
  OWNER TO postgres;');

/*rencanapulangdetail_v*/
    $this->execute('DROP VIEW if exists public.rencanapulangdetail_v;');

    $this->execute("
        CREATE OR REPLACE VIEW public.rencanapulangdetail_v AS 
 SELECT t.rencanapulang_id,
    t.edukasi_kesehatan,
    t.tgl_edukasi,
    t.pemberi_edukasi,
    t.ppa,
    t.is_deleted,
    fgetnamalookupkeperawatan(t.edukasi_kesehatan::integer) AS edukasi_kesehatan_nama,
    b.nama_pegawai
   FROM rencanapulangdetail_t t
     JOIN pegawai_m b ON t.ppa = b.pegawai_id;");

    $this->execute('ALTER TABLE public.rencanapulangdetail_v
  OWNER TO postgres;');

/*asesmenawalgizi_v*/
    $this->execute('DROP VIEW if exists public.asesmenawalgizi_v;');

    $this->execute("
        CREATE OR REPLACE VIEW public.asesmenawalgizi_v AS 
 SELECT pasien.no_rekam_medik,
    pasien.pendaftaran_id,
    pasien.tgl_pendaftaran,
    pasien.no_pendaftaran,
    pasien.nama_pasien,
    pasien.jenis_kelamin,
    pasien.jeniskasuspenyakit_nama,
    pasien.tanggal_lahir,
    pasien.umur,
    pasien.dokter_admisi,
    pasien.kelaspelayanan_nama,
    pasien.kamarruangan_nokamar,
    pasien.no_tempattidur,
    pasien.carabayar_nama,
    pasien.penjamin_nama,
    t.bb_biasanya,
    t.bb_saatini,
    t.perubahan_kg,
    t.perubahan_persen,
    fgetnamalookupkeperawatan(t.perubahan_hasil::integer) AS v_perubahan_hasil,
    fgetnamalookupkeperawatan(t.kategori_bb::integer) AS v_kategori_bb,
    fgetnamalookupkeperawatan(t.asupanmkn::integer) AS v_asupanmkn,
    fgetnamalookupkeperawatan(t.kategori_asupanmkn::integer) AS v_kategori_asupanmkn,
    fgetnamalookupkeperawatan(t.gastrointestinal_mual::integer) AS v_gastrointestinal_mual,
    fgetnamalookupkeperawatan(t.gastrointestinal_muntah::integer) AS v_gastrointestinal_muntah,
    fgetnamalookupkeperawatan(t.gastrointestinal_diare::integer) AS v_gastrointestinal_diare,
    fgetnamalookupkeperawatan(t.gastrointestinal_anoreksia::integer) AS v_gastrointestinal_anoreksia,
    fgetnamalookupkeperawatan(t.kategori_gastrointestinal::integer) AS v_kategori_gastrointestinal,
    fgetnamalookupkeperawatan(t.fungsional::integer) AS v_fungsional,
    fgetnamalookupkeperawatan(t.kategori_fungsional::integer) AS v_kategori_fungsional,
    t.diagnosa_medis,
    fgetnamalookupkeperawatan(t.keb_metabolik::integer) AS v_keb_metabolik,
    fgetnamalookupkeperawatan(t.kategori_hubungan::integer) AS v_kategori_hubungan,
    t.fisik_lemak,
    t.fisik_otot,
    t.fisik_udem,
    t.fisik_asites,
    fgetnamalookupkeperawatan(t.kategori_fisik::integer) AS v_kategori_fisik,
    fgetnamalookupkeperawatan(t.penilaian_sga::integer) AS v_penilaian_sga,
    t.diet,
    t.pagt,
    t.saran_terapi,
    pegawai_m.nama_pegawai,
    t.created_date
   FROM asesmenawalgizi_t t
     JOIN ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pasien_m.tanggal_lahir,
            pendaftaran_t.umur,
            dokter_admisi.nama_pegawai AS dokter_admisi,
            kelaspelayanan_m.kelaspelayanan_nama,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            ruangan_m.ruangan_nama,
            kamarruangan_m.kamarruangan_nokamar,
            kamartempattidur_m.no_tempattidur
           FROM pendaftaran_t
             JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN pegawai_m dokter_admisi ON pasienadmisi_t.pegawai_id = dokter_admisi.pegawai_id
             JOIN kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
             JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id) pasien ON t.pendaftaran_id = pasien.pendaftaran_id
     JOIN loginpemakai_k ON t.created_by = loginpemakai_k.loginpemakai_id
     JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id;");

    $this->execute('ALTER TABLE public.asesmenawalgizi_v
  OWNER TO postgres;');

/*skrininggizi_v*/
    $this->execute('DROP VIEW if exists public.skrininggizi_v;');

    $this->execute("
        CREATE OR REPLACE VIEW public.skrininggizi_v AS 
 SELECT t.skrininggizi_id,
    t.pendaftaran_id,
    t.pasienadmisi_id,
    t.bb_ygdirencanakan,
    t.bb_turun,
    fgetvaluelookupkeperawatan(t.bb_turun) AS bb_turun_nama,
    t.porsi_makan,
    fgetnamalookupkeperawatan(t.porsi_makan) AS porsi_makan_nama,
    t.sakit_berat,
    fgetnamalookupkeperawatan(t.sakit_berat) AS sakit_berat_nama,
    t.skor,
    fgetnamalookupkeperawatan(t.bb_turun) AS bb_turun_skor,
    fgetvaluelookupkeperawatan(t.porsi_makan) AS porsi_makan_skor,
    fgetvaluelookupkeperawatan(t.sakit_berat) AS sakit_berat_skor
   FROM skrininggizi_t t;");

    $this->execute('ALTER TABLE public.skrininggizi_v
  OWNER TO postgres;
');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190926_092853_optimize_view_15 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190926_092853_optimize_view_15 cannot be reverted.\n";

        return false;
    }
    */
}

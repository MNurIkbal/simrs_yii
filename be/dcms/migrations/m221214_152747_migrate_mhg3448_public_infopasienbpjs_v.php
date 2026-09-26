<?php

use yii\db\Migration;

/**
 * Class m221214_152747_migrate_mhg3448_public_infopasienbpjs_v
 */
class m221214_152747_migrate_mhg3448_public_infopasienbpjs_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."infopasienbpjs_v";');
        $this->execute("CREATE OR REPLACE VIEW public.infopasienbpjs_v
            AS SELECT rincian.jenis,
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
                rincian.is_terkirim,
                rincian.alamat_pasien,
                rincian.tgl_stopakomodasi,
                rincian.is_stopakomodasi
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
                        fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin,
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
                        klaiminacbg_t.is_terkirim,
                        pasien_m.alamat_pasien,
                        pendaftaran_t.tgl_stopakomodasi,
                        pendaftaran_t.is_stopakomodasi
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
                                        LEFT JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                    WHERE tindakanpelayanan_t.pasienmasukpenunjang_id IS NULL AND daftartindakan_m.groupinacbg_id IS NOT NULL
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
                                        LEFT JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                    WHERE tindakanpelayanan_t.pasienmasukpenunjang_id IS NOT NULL AND daftartindakan_m.groupinacbg_id IS NOT NULL
                                    GROUP BY tindakanpelayanan_t.pendaftaran_id, ruangan_m_1.instalasi_id, instalasi_m_1.instalasi_nama
                                    UNION ALL
                                    SELECT obatalkespasien_t.pendaftaran_id,
                                        ruangan_m_1.instalasi_id,
                                        instalasi_m_1.instalasi_nama,
                                        sum(obatalkespasien_t.hargajual_oa) AS sum
                                    FROM obatalkespasien_t
                                        JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
                                        JOIN groupinacbg_m ON obatalkes_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
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
                                        JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
                                        JOIN groupinacbg_m ON obatalkes_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
                                        LEFT JOIN resepturdetail_t ON obatalkespasien_t.resepturdetail_id = resepturdetail_t.resepturdetail_id
                                        LEFT JOIN reseptur_t ON resepturdetail_t.reseptur_id = reseptur_t.reseptur_id
                                        LEFT JOIN ruangan_m ruangan_m_1 ON reseptur_t.ruanganreseptur_id = ruangan_m_1.ruangan_id
                                        LEFT JOIN instalasi_m instalasi_m_1 ON ruangan_m_1.instalasi_id = instalasi_m_1.instalasi_id
                                    WHERE obatalkespasien_t.resepturdetail_id IS NOT NULL
                                    GROUP BY obatalkespasien_t.pendaftaran_id, ruangan_m_1.instalasi_id, instalasi_m_1.instalasi_nama) hitung
                                JOIN pendaftaran_t pendaftaran_t_1 ON hitung.pendaftaran_id = pendaftaran_t_1.pendaftaran_id
                            GROUP BY hitung.pendaftaran_id, hitung.instalasi_id, hitung.instalasi_nama, pendaftaran_t_1.pasienadmisi_id, pendaftaran_t_1.instalasi_id) cektagihaninstalasi ON pendaftaran_t.pendaftaran_id = cektagihaninstalasi.pendaftaran_id
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
                        pasienadmisi_t.pasienpulang_id,
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
                        fgetnamalookup(pasien_m.jeniskelamin::integer) AS jeniskelamin,
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
                        klaiminacbg_t.is_terkirim,
                        pasien_m.alamat_pasien,
                        pendaftaran_t.tgl_stopakomodasi,
                        pendaftaran_t.is_stopakomodasi
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
                        LEFT JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
                        LEFT JOIN carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
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
                    WHERE pasienadmisi_t.carabayar_id = 6 AND pendaftaran_t.is_stopakomodasi = true) rincian
                LEFT JOIN masukkamar_t ON rincian.pasienadmisi_id = masukkamar_t.pasienadmisi_id AND masukkamar_t.pindahkamar_id IS NULL
                LEFT JOIN profilrumahsakit_m ON profilrumahsakit_m.is_deleted = false AND profilrumahsakit_m.is_active = true
            GROUP BY rincian.jenis, rincian.pendaftaran_id, rincian.tgl_pendaftaran, rincian.no_pendaftaran, rincian.instalasi_id, rincian.instalasi_nama, rincian.pasien_id, rincian.no_rekam_medik, rincian.nama_pasien, rincian.carabayar_id, rincian.carabayar_nama, rincian.penjamin_id, rincian.penjamin_nama, rincian.ruangan_id, rincian.ruangan_nama, rincian.jeniskasuspenyakit_id, rincian.jeniskasuspenyakit_nama, rincian.pegawai_id, rincian.dokter_dpjp, rincian.status_verifikasi, rincian.status_verif, rincian.umur, rincian.kelaspelayanan_id, rincian.kelaspelayanan_nama, rincian.jeniskelas_nama, rincian.pasienpulang_id, rincian.tglpasienpulang, rincian.carakeluar_id, rincian.carakeluar_nama, rincian.nosep, rincian.kamarruangan_id, rincian.kamarruangan_nokamar, rincian.kamartempattidur_id, rincian.no_tempattidur, rincian.pasienadmisi_id, rincian.status_bayar, rincian.stat_bayar, rincian.total_tagihan, rincian.nokartuasuransi, rincian.jeniskelamin, rincian.tanggal_lahir, rincian.carakeluarinacbg_id, rincian.carakeluar_value, rincian.kelas_bpjs, rincian.lama_rawat, rincian.urutankelas, rincian.pengajuanklaimdetail_id, rincian.bpjs_id, masukkamar_t.kelaspelayanan_id, masukkamar_t.lamadirawat_kamar, rincian.jeniskelas_id, profilrumahsakit_m.kodetarifbpjs_id, rincian.tarif_polieksekutif, rincian.is_naikkelas, rincian.is_rawatintensif, rincian.lama_kelasintensif, rincian.ventilator, rincian.naik_kelas, rincian.status_klaim, rincian.klaiminacbg_id, rincian.jenis_kelasrawat, rincian.is_terkirim, rincian.alamat_pasien, rincian.tgl_stopakomodasi, rincian.is_stopakomodasi;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221214_152747_migrate_mhg3448_public_infopasienbpjs_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221214_152747_migrate_mhg3448_public_infopasienbpjs_v cannot be reverted.\n";

        return false;
    }
    */
}

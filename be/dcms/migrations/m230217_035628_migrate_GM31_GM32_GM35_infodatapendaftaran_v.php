<?php

use yii\db\Migration;

/**
 * Class m230217_035628_migrate_GM31_GM32_GM35_infodatapendaftaran_v
 */
class m230217_035628_migrate_GM31_GM32_GM35_infodatapendaftaran_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."infodatapendaftaran_v";');
        $this->execute("CREATE OR REPLACE VIEW public.infodatapendaftaran_v
        AS SELECT data_info.pendaftaran_id,
            data_info.instalasi_id AS ins_id,
            data_info.ruangan_id AS rua_id,
            data_info.pasien_id,
                CASE
                    WHEN data_info.pasienadmisi_id IS NULL THEN data_info.penjamin_id
                    ELSE data_info.penjaminri_id
                END AS pen_id,
                CASE
                    WHEN data_info.pasienadmisi_id IS NULL THEN data_info.carabayar_id
                    ELSE data_info.carabayarri_id
                END AS car_id,
                CASE
                    WHEN data_info.pasienadmisi_id IS NULL THEN data_info.kelaspelayanan_id
                    ELSE data_info.kelaspelayananri_id
                END AS kelaspelayanan_id,
            data_info.pasienpulang_id,
            data_info.no_pendaftaran,
            data_info.tgl_pendaftaran,
            data_info.no_rekam_medik,
            data_info.nama_pasien,
            data_info.no_mobile_pasien,
            data_info.instalasi_nama AS ins_nama,
            data_info.ruangan_nama AS rua_nama,
                CASE
                    WHEN data_info.pasienadmisi_id IS NULL THEN data_info.carabayar_nama
                    ELSE data_info.carabayar_nama_ri
                END AS car,
                CASE
                    WHEN data_info.pasienadmisi_id IS NULL THEN data_info.penjamin_nama
                    ELSE data_info.penjamin_nama_ri
                END AS pen,
            data_info.kelaspelayanan_nama,
            data_info.jumlah_uangmuka,
            data_info.pasienpulangri_id,
            data_info.pasienadmisi_id,
            data_info.status_pasien,
            data_info.pasienmasukpenunjang_id,
                CASE
                    WHEN data_info.pasienadmisi_id IS NULL THEN data_info.tglpasienpulang
                    WHEN data_info.pasienadmisi_id IS NOT NULL AND data_info.tglpasienpulang_ri IS NULL THEN data_info.tgl_stopakomodasi
                    ELSE data_info.tglpasienpulang_ri
                END AS tglpasienpulang,
            data_info.dokterrj_id,
            data_info.nama_dok_rj_rd,
            data_info.dokterri_id,
            data_info.nama_dok_ri,
            data_info.jeniskasuspenyakit_nama,
            data_info.umur,
                CASE
                    WHEN data_info.pasienadmisi_id IS NULL THEN data_info.carabayar_id
                    ELSE data_info.carabayarri_id
                END AS carabayar_id,
                CASE
                    WHEN data_info.pasienadmisi_id IS NULL THEN data_info.penjamin_id
                    ELSE data_info.penjaminri_id
                END AS penjamin_id,
                CASE
                    WHEN data_info.pasienadmisi_id IS NULL THEN data_info.carabayar_nama
                    ELSE data_info.carabayar_nama_ri
                END AS carabayar_nama,
                CASE
                    WHEN data_info.pasienadmisi_id IS NULL THEN data_info.penjamin_nama
                    ELSE data_info.penjamin_nama_ri
                END AS penjamin_nama,
                CASE
                    WHEN data_info.pasienadmisi_id IS NULL THEN data_info.instalasi_id
                    ELSE data_info.instalasiri_id
                END AS instalasi_id,
                CASE
                    WHEN data_info.pasienadmisi_id IS NULL THEN data_info.ruangan_id
                    ELSE data_info.ruanganri_id
                END AS ruangan_id,
                CASE
                    WHEN data_info.pasienadmisi_id IS NULL THEN data_info.instalasi_nama
                    ELSE data_info.instalasi_nama_ri
                END AS instalasi_nama,
                CASE
                    WHEN data_info.pasienadmisi_id IS NULL THEN data_info.ruangan_nama
                    ELSE data_info.ruangan_nama_ri
                END AS ruangan_nama,
            data_info.status_bayar,
            data_info.jeniskasuspenyakit_id,
            data_info.tanggal_lahir,
            data_info.penjualanresep_id,
            data_info.jasa,
            data_info.administrasi,
            data_info.obat,
            data_info.totalharga_jual,
                CASE
                    WHEN data_info.pasienadmisi_id IS NULL THEN data_info.kelas_bpjspendaftaran
                    ELSE data_info.kelas_bpjsadmisi
                END AS hak_kelas,
                CASE
                    WHEN data_info.pasienadmisi_id IS NULL AND data_info.bpjs_idpendaftaran IS NOT NULL THEN data_info.no_bpjspendaftaran
                    WHEN data_info.pasienadmisi_id IS NOT NULL AND data_info.bpjs_idadmisi IS NOT NULL THEN data_info.no_bpjsadmisi
                    WHEN data_info.pasienadmisi_id IS NULL AND data_info.bpjs_idadmisi IS NULL THEN data_info.no_asuransipendaftaran
                    WHEN data_info.pasienadmisi_id IS NOT NULL AND data_info.bpjs_idadmisi IS NULL THEN data_info.no_asuransiadmisi
                    ELSE NULL::character varying
                END AS no_kartu,
                CASE
                    WHEN data_info.pasienadmisi_id IS NULL THEN data_info.groupcarabayar_pendaftaran
                    ELSE data_info.groupcarabayar_admisi
                END AS group_carabayar,
            data_info.total_piutang,
            data_info.keadaanmasuk_id,
            data_info.keadaan_masuk,
            data_info.transportasi_id,
            data_info.transportasi,
            data_info.keterangan_pendaftaran,
            round(data_info.tagihan_belumbayar::numeric, 2)::double precision AS tagihan_belumbayar,
            round(data_info.sisa_penunjang::numeric, 2)::double precision AS sisa_penunjang,
            round(data_info.sisa_karcis::numeric, 2)::double precision AS sisa_karcis,
            round(data_info.sisa_obat::numeric, 2)::double precision AS sisa_obat,
            data_info.status_periksa,
            data_info.tinggi_badan,
            data_info.berat_badan,
            data_info.nama_depan,
            data_info.jenisidentitas,
            data_info.no_identitas_pasien,
            data_info.no_telepon_pasien,
            data_info.alamatemail,
            data_info.alamat_pasien,
            data_info.alamat_sekarang,
            data_info.additional_pasien,
            data_info.jenis_kelamin,
            data_info.namadepan_penanggung,
            data_info.nama_pasien_penanggung,
            data_info.propinsi_id_penanggung,
            data_info.kabupaten_id_penanggung,
            data_info.kecamatan_id_penanggung,
            data_info.kelurahan_id_penanggung,
            data_info.rt_penanggung,
            data_info.rw_penanggung,
            data_info.kode_pos_penanggung,
            data_info.alamat_pasien_penanggung,
            data_info.no_telepon_pasien_penanggung,
            data_info.pekerjaan_id_penanggung,
            data_info.pt_penanggung,
            data_info.namabagian_penanggung,
            data_info.noindukkaryawan_penanggung,
            data_info.jpkm_penanggung,
            data_info.pasienbatalperiksa_id,
            data_info.status_ranap,
            data_info.no_sep,
            data_info.lob_id,
            data_info.kelas_ditagihkan,
            data_info.kelas_ditagihkan_id,
            data_info.ruangan_titipan_id,
            data_info.ruangan_titipan_nama,
            data_info.is_stopakomodasi,
            data_info.no_masukpenunjang
           FROM ( SELECT pendaftaran_t.pendaftaran_id,
                    pendaftaran_t.instalasi_id,
                    pendaftaran_t.ruangan_id,
                    pendaftaran_t.pasien_id,
                    pendaftaran_t.penjamin_id,
                    pendaftaran_t.carabayar_id,
                    pendaftaran_t.kelaspelayanan_id,
                    pasienadmisi_t.kelaspelayanan_id AS kelaspelayananri_id,
                    pendaftaran_t.pasienpulang_id,
                    pendaftaran_t.no_pendaftaran,
                    pendaftaran_t.tgl_pendaftaran,
                    pasien_m.no_rekam_medik,
                    concat(COALESCE(lkp_namadepan.lookup_name, ''::character varying), ' ', pasien_m.nama_pasien) AS nama_pasien,
                    pasien_m.no_mobile_pasien,
                    pasien_m.jenisidentitas,
                    pasien_m.no_identitas_pasien,
                    pasien_m.no_telepon_pasien,
                    pasien_m.alamatemail,
                    pasien_m.alamat_pasien,
                    pasien_m.alamat_sekarang,
                    pasien_m.additional_pasien,
                    instalasi_m.instalasi_nama,
                    ruangan_m.ruangan_nama,
                    carabayar_m.carabayar_nama,
                    penjamin_m.penjamin_nama,
                        CASE
                            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN kelaspelayanan_m.kelaspelayanan_nama
                            ELSE kelaspelayanan_ri.kelaspelayanan_nama
                        END AS kelaspelayanan_nama,
                    pasienpulang_t.tglpasienpulang,
                    pulang_ri.tglpasienpulang AS tglpasienpulang_ri,
                    COALESCE(bayaruangmuka_t.jumlah_uangmuka, 0::double precision) - COALESCE(pemakaianuangmuka_t.pemakaian_uangmuka, 0::double precision) - COALESCE(pengembalianuangmuka_t.total_pengembalian, 0::double precision) AS jumlah_uangmuka,
                    pasienadmisi_t.pasienpulang_id AS pasienpulangri_id,
                    pasienadmisi_t.pasienadmisi_id,
                    pendaftaran_t.status_pasien,
                    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                    pasienmasukpenunjang_t.no_masukpenunjang,
                    dok_rj_rd.nama_pegawai AS nama_dok_rj_rd,
                    dok_ri.nama_pegawai AS nama_dok_ri,
                    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                    pendaftaran_t.umur,
                    pasienadmisi_t.carabayar_id AS carabayarri_id,
                    carabayar_ri.carabayar_nama AS carabayar_nama_ri,
                    pasienadmisi_t.penjamin_id AS penjaminri_id,
                    penjamin_ri.penjamin_nama AS penjamin_nama_ri,
                    pasienadmisi_t.ruangan_id AS ruanganri_id,
                    ruang_ri.instalasi_id AS instalasiri_id,
                    ruang_ri.ruangan_nama AS ruangan_nama_ri,
                    ins_ri.instalasi_nama AS instalasi_nama_ri,
                    pendaftaran_t.status_bayar,
                    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
                    pasien_m.tanggal_lahir,
                    NULL::integer AS penjualanresep_id,
                    0 AS jasa,
                        CASE
                            WHEN penjualan_resep.biaya_adm IS NULL THEN 0::double precision
                            ELSE penjualan_resep.biaya_adm
                        END AS administrasi,
                    0 AS obat,
                    0 AS totalharga_jual,
                    bpjs_pendaftaran.klsrawat AS kelas_bpjspendaftaran,
                    bpjs_admisi.klsrawat AS kelas_bpjsadmisi,
                    bpjs_pendaftaran.bpjs_id AS bpjs_idpendaftaran,
                    bpjs_admisi.bpjs_id AS bpjs_idadmisi,
                    bpjs_pendaftaran.nokartuasuransi AS no_bpjspendaftaran,
                    bpjs_admisi.nokartuasuransi AS no_bpjsadmisi,
                    asuransi_pendaftaran.nokartuasuransi AS no_asuransipendaftaran,
                    asuransi_admisi.nokartuasuransi AS no_asuransiadmisi,
                    carabayar_m.groupcarabayar_id AS groupcarabayar_pendaftaran,
                    carabayar_ri.groupcarabayar_id AS groupcarabayar_admisi,
                    COALESCE(pemberianpiutang_t.total_piutang, 0::double precision) AS total_piutang,
                    pendaftaran_t.pegawai_id AS dokterrj_id,
                    pasienadmisi_t.pegawai_id AS dokterri_id,
                    pendaftaran_t.keadaan_masuk AS keadaanmasuk_id,
                    lkp_keadaanmasuk.lookup_name AS keadaan_masuk,
                    pendaftaran_t.transportasi AS transportasi_id,
                    lkp_transportasi.lookup_name AS transportasi,
                    pendaftaran_t.keterangan_pendaftaran,
                    COALESCE(belum_bayar.total_tagihan, 0::double precision) AS tagihan_belumbayar,
                    COALESCE(sisa_penunjang.total_tagihan, 0::double precision) AS sisa_penunjang,
                    COALESCE(sisa_karcis.total_tagihan, 0::double precision) AS sisa_karcis,
                    COALESCE(sisa_obat.total_tagihan, 0::double precision) AS sisa_obat,
                    pendaftaran_t.status_periksa,
                        CASE
                            WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.instalasi_id = 1 THEN periksa_fisik_rj.tinggi
                            WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.instalasi_id = 2 THEN periksa_fisik_rd.tinggi::double precision
                            WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL THEN periksa_fisik_ri.tinggi::double precision
                            ELSE NULL::double precision
                        END AS tinggi_badan,
                        CASE
                            WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.instalasi_id = 1 THEN periksa_fisik_rj.berat
                            WHEN pendaftaran_t.pasienadmisi_id IS NULL AND pendaftaran_t.instalasi_id = 2 THEN periksa_fisik_rd.berat::double precision
                            WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL THEN periksa_fisik_ri.berat::double precision
                            ELSE NULL::double precision
                        END AS berat_badan,
                    pendaftaran_t.tgl_stopakomodasi,
                    lkp_namadepan.lookup_name AS nama_depan,
                    lkp_jeniskelamin.lookup_name AS jenis_kelamin,
                    pendaftaran_t.namadepan AS namadepan_penanggung,
                    pendaftaran_t.nama_pasien AS nama_pasien_penanggung,
                    pendaftaran_t.propinsi_id AS propinsi_id_penanggung,
                    pendaftaran_t.kabupaten_id AS kabupaten_id_penanggung,
                    pendaftaran_t.kecamatan_id AS kecamatan_id_penanggung,
                    pendaftaran_t.kelurahan_id AS kelurahan_id_penanggung,
                    pendaftaran_t.rt AS rt_penanggung,
                    pendaftaran_t.rw AS rw_penanggung,
                    pendaftaran_t.kode_pos AS kode_pos_penanggung,
                    pendaftaran_t.alamat_pasien AS alamat_pasien_penanggung,
                    pendaftaran_t.no_telepon_pasien AS no_telepon_pasien_penanggung,
                    pendaftaran_t.pekerjaan_id AS pekerjaan_id_penanggung,
                    pendaftaran_t.pt AS pt_penanggung,
                    pendaftaran_t.namabagian AS namabagian_penanggung,
                    pendaftaran_t.noindukkaryawan AS noindukkaryawan_penanggung,
                    pendaftaran_t.jpkm AS jpkm_penanggung,
                    pendaftaran_t.pasienbatalperiksa_id,
                    pasienadmisi_t.status_ranap,
                        CASE
                            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN bpjs_pendaftaran.nosep
                            ELSE bpjs_admisi.nosep
                        END AS no_sep,
                    instalasi_m.lob_id,
                    kelas_ditagihkan.kelaspelayanan_nama AS kelas_ditagihkan,
                    pasienadmisi_t.kelas_ditagihkan_id,
                    ruangan_titipan.ruangan_id AS ruangan_titipan_id,
                    ruangan_titipan.ruangan_nama AS ruangan_titipan_nama,
                    pendaftaran_t.is_stopakomodasi
                   FROM pendaftaran_t
                     LEFT JOIN ( SELECT pasienadmisi.pasienadmisi_id,
                            pasienadmisi.ruangan_id,
                            pasienadmisi.carabayar_id,
                            pasienadmisi.penjamin_id,
                            pasienadmisi.kelaspelayanan_id,
                            pasienadmisi.pasienpulang_id,
                            pasienadmisi.pegawai_id,
                            pasienadmisi.bpjs_id,
                            pasienadmisi.asuransipasien_id,
                            pasienadmisi.status_ranap,
                            pasienadmisi.kelas_ditagihkan_id,
                            pasienadmisi.ruangan_titipan_id
                           FROM pasienadmisi_t pasienadmisi) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                     LEFT JOIN ruangan_m ruangan_titipan ON pasienadmisi_t.ruangan_titipan_id = ruangan_titipan.ruangan_id
                     JOIN ( SELECT pasien.pasien_id,
                            pasien.nama_pasien,
                            pasien.no_rekam_medik,
                            pasien.tanggal_lahir,
                            pasien.namadepan,
                            pasien.jeniskelamin,
                            pasien.no_mobile_pasien,
                            pasien.jenisidentitas,
                            pasien.no_identitas_pasien,
                            pasien.no_telepon_pasien,
                            pasien.alamatemail,
                            pasien.alamat_pasien,
                            pasien.alamat_sekarang,
                            pasien.additional_pasien
                           FROM pasien_m pasien) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                     JOIN ( SELECT instalasi.instalasi_id,
                            instalasi.instalasi_nama,
                            instalasi.lob_id
                           FROM instalasi_m instalasi) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
                     JOIN ( SELECT ruangan.ruangan_id,
                            ruangan.ruangan_nama
                           FROM ruangan_m ruangan) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
                     LEFT JOIN ( SELECT ruangan_ri.ruangan_id,
                            ruangan_ri.instalasi_id,
                            ruangan_ri.ruangan_nama
                           FROM ruangan_m ruangan_ri) ruang_ri ON pasienadmisi_t.ruangan_id = ruang_ri.ruangan_id
                     LEFT JOIN ( SELECT instalasi_ri.instalasi_id,
                            instalasi_ri.instalasi_nama
                           FROM instalasi_m instalasi_ri) ins_ri ON ruang_ri.instalasi_id = ins_ri.instalasi_id
                     JOIN ( SELECT cara_bayar.carabayar_id,
                            cara_bayar.carabayar_nama,
                            cara_bayar.groupcarabayar_id
                           FROM carabayar_m cara_bayar) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
                     JOIN ( SELECT penjamin.penjamin_id,
                            penjamin.penjamin_nama
                           FROM penjamin_m penjamin) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
                     LEFT JOIN ( SELECT cb_ri.carabayar_id,
                            cb_ri.carabayar_nama,
                            cb_ri.groupcarabayar_id
                           FROM carabayar_m cb_ri) carabayar_ri ON pasienadmisi_t.carabayar_id = carabayar_ri.carabayar_id
                     LEFT JOIN ( SELECT penj_ri.penjamin_id,
                            penj_ri.penjamin_nama
                           FROM penjamin_m penj_ri) penjamin_ri ON pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id
                     JOIN ( SELECT kelas.kelaspelayanan_id,
                            kelas.kelaspelayanan_nama
                           FROM kelaspelayanan_m kelas) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                     LEFT JOIN ( SELECT kelas_ri.kelaspelayanan_id,
                            kelas_ri.kelaspelayanan_nama
                           FROM kelaspelayanan_m kelas_ri) kelaspelayanan_ri ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_ri.kelaspelayanan_id
                     LEFT JOIN ( SELECT kelas_ditagihkan_1.kelaspelayanan_id,
                            kelas_ditagihkan_1.kelaspelayanan_nama
                           FROM kelaspelayanan_m kelas_ditagihkan_1) kelas_ditagihkan ON pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id
                     JOIN ( SELECT kasuspenyakit.jeniskasuspenyakit_id,
                            kasuspenyakit.jeniskasuspenyakit_nama
                           FROM jeniskasuspenyakit_m kasuspenyakit) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
                     LEFT JOIN ( SELECT pasienpulang_rj.pasienpulang_id,
                            pasienpulang_rj.tglpasienpulang
                           FROM pasienpulang_t pasienpulang_rj) pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
                     LEFT JOIN ( SELECT pasienpulang_ri.pasienpulang_id,
                            pasienpulang_ri.tglpasienpulang
                           FROM pasienpulang_t pasienpulang_ri) pulang_ri ON pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id
                     LEFT JOIN ( SELECT x.pendaftaran_id,
                            sum(x.jumlah_uangmuka) AS jumlah_uangmuka
                           FROM ( SELECT m.pendaftaran_id,
                                    sum(m.jumlah_uangmuka) AS jumlah_uangmuka
                                   FROM bayaruangmuka_t m
                                  WHERE m.is_deleted IS FALSE
                                  GROUP BY m.pendaftaran_id
                                UNION ALL
                                 SELECT gabungpelayanandetail_t_1.ref_pendaftaran_id AS pendaftaran_id,
                                    sum(m.jumlah_uangmuka) AS jumlah_uangmuka
                                   FROM bayaruangmuka_t m
                                     JOIN ( SELECT a.pendaftaran_id,
                                            a.ref_pendaftaran_id,
                                            a.is_deleted
                                           FROM gabungpelayanandetail_t a) gabungpelayanandetail_t_1 ON m.pendaftaran_id = gabungpelayanandetail_t_1.pendaftaran_id
                                  WHERE m.is_deleted IS FALSE AND gabungpelayanandetail_t_1.is_deleted IS FALSE
                                  GROUP BY gabungpelayanandetail_t_1.ref_pendaftaran_id) x
                          GROUP BY x.pendaftaran_id) bayaruangmuka_t ON pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id
                     LEFT JOIN ( SELECT pasienmasukpenunjang.pasienmasukpenunjang_id,
                            pasienmasukpenunjang.pendaftaran_id,
                            pasienmasukpenunjang.no_masukpenunjang
                           FROM pasienmasukpenunjang_t pasienmasukpenunjang) pasienmasukpenunjang_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LEFT JOIN ( SELECT pengembalianuangmuka_t_1.pendaftaran_id,
                            sum(pengembalianuangmuka_t_1.total_pengembalian) AS total_pengembalian
                           FROM pengembalianuangmuka_t pengembalianuangmuka_t_1
                          WHERE pengembalianuangmuka_t_1.is_deleted = false
                          GROUP BY pengembalianuangmuka_t_1.pendaftaran_id) pengembalianuangmuka_t ON pendaftaran_t.pendaftaran_id = pengembalianuangmuka_t.pendaftaran_id
                     LEFT JOIN ( SELECT pemakaianuangmuka_t_1.pendaftaran_id,
                            sum(pemakaianuangmuka_t_1.pemakaian_uangmuka) AS pemakaian_uangmuka
                           FROM pemakaianuangmuka_t pemakaianuangmuka_t_1
                          WHERE pemakaianuangmuka_t_1.is_deleted = false
                          GROUP BY pemakaianuangmuka_t_1.pendaftaran_id) pemakaianuangmuka_t ON pendaftaran_t.pendaftaran_id = pemakaianuangmuka_t.pendaftaran_id
                     LEFT JOIN ( SELECT peg_rj.pegawai_id,
                            peg_rj.nama_pegawai
                           FROM pegawai_m peg_rj) dok_rj_rd ON pendaftaran_t.pegawai_id = dok_rj_rd.pegawai_id
                     LEFT JOIN ( SELECT peg_ri.pegawai_id,
                            peg_ri.nama_pegawai
                           FROM pegawai_m peg_ri) dok_ri ON pasienadmisi_t.pegawai_id = dok_ri.pegawai_id
                     LEFT JOIN ( SELECT bpjs_rj.bpjs_id,
                            bpjs_rj.klsrawat,
                            bpjs_rj.nokartuasuransi,
                            bpjs_rj.nosep
                           FROM bpjs_t bpjs_rj) bpjs_pendaftaran ON pendaftaran_t.bpjs_id = bpjs_pendaftaran.bpjs_id
                     LEFT JOIN ( SELECT bpjs_ri.bpjs_id,
                            bpjs_ri.klsrawat,
                            bpjs_ri.nokartuasuransi,
                            bpjs_ri.nosep
                           FROM bpjs_t bpjs_ri) bpjs_admisi ON pasienadmisi_t.bpjs_id = bpjs_admisi.bpjs_id
                     LEFT JOIN ( SELECT asuransi_rj.asuransipasien_id,
                            asuransi_rj.nokartuasuransi
                           FROM asuransipasien_m asuransi_rj) asuransi_pendaftaran ON pendaftaran_t.asuransipasien_id = asuransi_pendaftaran.asuransipasien_id
                     LEFT JOIN ( SELECT asuransi_ri.asuransipasien_id,
                            asuransi_ri.nokartuasuransi
                           FROM asuransipasien_m asuransi_ri) asuransi_admisi ON pasienadmisi_t.asuransipasien_id = asuransi_admisi.asuransipasien_id
                     LEFT JOIN ( SELECT pemberianpiutang.pendaftaran_id,
                            pemberianpiutang.total_piutang
                           FROM pemberianpiutang_t pemberianpiutang
                          WHERE pemberianpiutang.is_deleted = false) pemberianpiutang_t ON pemberianpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LEFT JOIN ( SELECT sum(pt.biayaadministrasi) AS biaya_adm,
                            pt.pendaftaran_id
                           FROM penjualanresep_t pt
                          WHERE pt.status_bayar = 349 AND pt.is_deleted = false
                          GROUP BY pt.pendaftaran_id) penjualan_resep ON penjualan_resep.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LEFT JOIN ( SELECT tagihan.pendaftaran_id,
                            sum(tagihan.tagihan) AS total_tagihan
                           FROM ( SELECT tindakanpelayanan_t.pendaftaran_id,
                                    sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan
                                   FROM tindakanpelayanan_t
                                  WHERE tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL
                                  GROUP BY tindakanpelayanan_t.pendaftaran_id
                                UNION ALL
                                 SELECT obatalkespasien_t.pendaftaran_id,
                                    sum(obatalkespasien_t.hargajual_oa) AS tagihan
                                   FROM obatalkespasien_t
                                  WHERE obatalkespasien_t.is_deleted = false AND obatalkespasien_t.obatsudahbayar_id IS NULL
                                  GROUP BY obatalkespasien_t.pendaftaran_id) tagihan
                          GROUP BY tagihan.pendaftaran_id) belum_bayar ON pendaftaran_t.pendaftaran_id = belum_bayar.pendaftaran_id
                     LEFT JOIN ( SELECT tagihan.pendaftaran_id,
                            sum(tagihan.tagihan) AS total_tagihan
                           FROM ( SELECT tindakanpelayanan_t.pendaftaran_id,
                                    sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan
                                   FROM tindakanpelayanan_t
                                     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                  WHERE tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.pasienmasukpenunjang_id IS NOT NULL AND daftartindakan_m.kelompoktindakan_id <> 17
                                  GROUP BY tindakanpelayanan_t.pendaftaran_id) tagihan
                          GROUP BY tagihan.pendaftaran_id) sisa_penunjang ON pendaftaran_t.pendaftaran_id = sisa_penunjang.pendaftaran_id
                     LEFT JOIN ( SELECT tagihan.pendaftaran_id,
                            sum(tagihan.tagihan) AS total_tagihan
                           FROM ( SELECT tindakanpelayanan_t.pendaftaran_id,
                                    sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan
                                   FROM tindakanpelayanan_t
                                     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                                  WHERE tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND daftartindakan_m.kelompoktindakan_id = 17
                                  GROUP BY tindakanpelayanan_t.pendaftaran_id) tagihan
                          GROUP BY tagihan.pendaftaran_id) sisa_karcis ON pendaftaran_t.pendaftaran_id = sisa_karcis.pendaftaran_id
                     LEFT JOIN ( SELECT tagihan.pendaftaran_id,
                            sum(tagihan.tagihan) AS total_tagihan
                           FROM ( SELECT obatalkespasien_t.pendaftaran_id,
                                    sum(obatalkespasien_t.hargajual_oa) AS tagihan
                                   FROM obatalkespasien_t
                                  WHERE obatalkespasien_t.is_deleted = false AND obatalkespasien_t.obatsudahbayar_id IS NULL
                                  GROUP BY obatalkespasien_t.pendaftaran_id) tagihan
                          GROUP BY tagihan.pendaftaran_id) sisa_obat ON pendaftaran_t.pendaftaran_id = sisa_obat.pendaftaran_id
                     LEFT JOIN ( SELECT pemeriksaanfisik_t.pendaftaran_id,
                            pemeriksaanfisik_t.tinggibadan_cm AS tinggi,
                            pemeriksaanfisik_t.beratbadan_kg AS berat
                           FROM pemeriksaanfisik_t
                          WHERE pemeriksaanfisik_t.is_deleted = false) periksa_fisik_rj ON pendaftaran_t.pendaftaran_id = periksa_fisik_rj.pendaftaran_id
                     LEFT JOIN ( SELECT asesmenperawatrd_t.pendaftaran_id,
                            asesmenperawatrd_t.tinggi_badan AS tinggi,
                            asesmenperawatrd_t.berat_badan AS berat
                           FROM asesmenperawatrd_t
                          WHERE asesmenperawatrd_t.is_deleted = false) periksa_fisik_rd ON pendaftaran_t.pendaftaran_id = periksa_fisik_rd.pendaftaran_id
                     LEFT JOIN ( SELECT asesmenmedis_t.pendaftaran_id,
                            asesmenmedis_t.tinggi_badan AS tinggi,
                            asesmenmedis_t.berat_badan AS berat
                           FROM asesmenmedis_t
                          WHERE asesmenmedis_t.is_deleted = false) periksa_fisik_ri ON pendaftaran_t.pendaftaran_id = periksa_fisik_ri.pendaftaran_id
                     LEFT JOIN ( SELECT a.lookup_id,
                            a.lookup_name
                           FROM lookup_m a) lkp_namadepan ON pasien_m.namadepan::integer = lkp_namadepan.lookup_id
                     LEFT JOIN ( SELECT a.lookup_id,
                            a.lookup_name
                           FROM lookup_m a) lkp_keadaanmasuk ON pendaftaran_t.keadaan_masuk::integer = lkp_keadaanmasuk.lookup_id
                     LEFT JOIN ( SELECT a.lookup_id,
                            a.lookup_name
                           FROM lookup_m a) lkp_transportasi ON pendaftaran_t.transportasi::integer = lkp_transportasi.lookup_id
                     LEFT JOIN ( SELECT a.lookup_id,
                            a.lookup_name
                           FROM lookup_m a) lkp_jeniskelamin ON pasien_m.jeniskelamin::integer = lkp_namadepan.lookup_id
                  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.instalasi_id, pendaftaran_t.ruangan_id, pendaftaran_t.pasien_id, pendaftaran_t.penjamin_id, pendaftaran_t.carabayar_id, pendaftaran_t.kelaspelayanan_id, pasienadmisi_t.kelaspelayanan_id, pasienadmisi_t.pegawai_id, pasienadmisi_t.status_ranap, pendaftaran_t.pasienpulang_id, pendaftaran_t.no_pendaftaran, pendaftaran_t.tgl_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pasien_m.no_mobile_pasien, instalasi_m.instalasi_nama, ruangan_m.ruangan_nama, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, kelaspelayanan_m.kelaspelayanan_nama, pasienpulang_t.tglpasienpulang, pasienadmisi_t.pasienpulang_id, pasienadmisi_t.pasienadmisi_id, pasienmasukpenunjang_t.pasienmasukpenunjang_id, pendaftaran_t.status_pasien, pulang_ri.tglpasienpulang, dok_rj_rd.nama_pegawai, dok_ri.nama_pegawai, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pasienadmisi_t.carabayar_id, carabayar_ri.carabayar_nama, pasienadmisi_t.penjamin_id, penjamin_ri.penjamin_nama, pasienadmisi_t.ruangan_id, ruang_ri.instalasi_id, ruang_ri.ruangan_nama, ins_ri.instalasi_nama, bayaruangmuka_t.jumlah_uangmuka, pemakaianuangmuka_t.pemakaian_uangmuka, pengembalianuangmuka_t.total_pengembalian, pendaftaran_t.status_bayar, jeniskasuspenyakit_m.jeniskasuspenyakit_id, pasien_m.tanggal_lahir, bpjs_pendaftaran.klsrawat, bpjs_admisi.klsrawat, bpjs_pendaftaran.bpjs_id, bpjs_admisi.bpjs_id, bpjs_pendaftaran.nokartuasuransi, bpjs_admisi.nokartuasuransi, asuransi_pendaftaran.nokartuasuransi, asuransi_admisi.nokartuasuransi, kelaspelayanan_ri.kelaspelayanan_nama, carabayar_m.groupcarabayar_id, carabayar_ri.groupcarabayar_id, pemberianpiutang_t.total_piutang, pendaftaran_t.keadaan_masuk, pendaftaran_t.transportasi, pendaftaran_t.keterangan_pendaftaran, penjualan_resep.biaya_adm, belum_bayar.total_tagihan, sisa_penunjang.total_tagihan, sisa_karcis.total_tagihan, (COALESCE(sisa_obat.total_tagihan, 0::double precision)), pendaftaran_t.status_periksa, periksa_fisik_rj.tinggi, periksa_fisik_rj.berat, periksa_fisik_rd.tinggi, periksa_fisik_rd.berat, periksa_fisik_ri.tinggi, periksa_fisik_ri.berat, pasien_m.namadepan, pasien_m.jenisidentitas, pasien_m.additional_pasien, pasien_m.no_identitas_pasien, pasien_m.no_telepon_pasien, pasien_m.alamatemail, pasien_m.alamat_sekarang, pasien_m.alamat_pasien, pasien_m.jeniskelamin, pasienmasukpenunjang_t.no_masukpenunjang, ruangan_titipan.ruangan_id, ruangan_titipan.ruangan_nama, (
                        CASE
                            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN bpjs_pendaftaran.nosep
                            ELSE bpjs_admisi.nosep
                        END), instalasi_m.lob_id, kelas_ditagihkan.kelaspelayanan_nama, pasienadmisi_t.kelas_ditagihkan_id, lkp_namadepan.lookup_name, lkp_keadaanmasuk.lookup_name, lkp_transportasi.lookup_name, lkp_jeniskelamin.lookup_name
                UNION ALL
                 SELECT NULL::integer AS pendaftaran_id,
                    ruangan_m.instalasi_id,
                    penjualanresep_t.ruangan_id,
                    penjualanresep_t.pasien_id,
                    penjualanresep_t.penjamin_id,
                    penjualanresep_t.carabayar_id,
                    penjualanresep_t.kelaspelayanan_id,
                    penjualanresep_t.kelaspelayanan_id AS kelaspelayananri_id,
                    0 AS pasienpulang_id,
                    penjualanresep_t.noresep AS no_pendaftaran,
                    penjualanresep_t.tglpenjualan AS tgl_pendaftaran,
                    pasien_m.no_rekam_medik,
                        CASE
                            WHEN penjualanresep_t.jenispenjualan::text = '343'::text THEN penjualanresep_t.nama_pembeli
                            WHEN penjualanresep_t.jenispenjualan::text = '344'::text THEN concat(fgetnamalookup(pasien_m.namadepan::integer), ' ', pasien_m.nama_pasien)::character varying
                            WHEN penjualanresep_t.jenispenjualan::text = '345'::text THEN concat(fgetnamalookup(pegawai_m.gelardepan::integer), ' ', karyawan.nama_pegawai)::character varying
                            ELSE NULL::character varying
                        END AS nama_pasien,
                    pasien_m.no_mobile_pasien,
                    pasien_m.jenisidentitas,
                    pasien_m.no_identitas_pasien,
                    pasien_m.no_telepon_pasien,
                    pasien_m.alamatemail,
                    pasien_m.alamat_pasien,
                    pasien_m.alamat_sekarang,
                    pasien_m.additional_pasien,
                    instalasi_m.instalasi_nama,
                    ruangan_m.ruangan_nama,
                    carabayar_m.carabayar_nama,
                    penjamin_m.penjamin_nama,
                    NULL::character varying AS kelaspelayanan_nama,
                    penjualanresep_t.tglresep AS tglpasienpulang,
                    penjualanresep_t.tglresep AS tglpasienpulang_ri,
                    0 AS jumlah_uangmuka,
                    0 AS pasienpulangri_id,
                    0 AS pasienadmisi_id,
                    NULL::character varying AS status_pasien,
                    0 AS pasienmasukpenunjang_id,
                    NULL::character varying AS no_masukpenunjang,
                    pegawai_m.nama_pegawai AS nama_dok_rj_rd,
                    pegawai_m.nama_pegawai AS nama_dok_ri,
                    NULL::character varying AS jeniskasuspenyakit_nama,
                    NULL::character varying AS umur,
                    penjualanresep_t.carabayar_id AS carabayarri_id,
                    carabayar_m.carabayar_nama AS carabayar_nama_ri,
                    penjualanresep_t.penjamin_id AS penjaminri_id,
                    penjamin_m.penjamin_nama AS penjamin_nama_ri,
                    penjualanresep_t.ruangan_id AS ruanganri_id,
                    ruangan_m.instalasi_id AS instalasiri_id,
                    ruangan_m.ruangan_nama AS ruangan_nama_ri,
                    instalasi_m.instalasi_nama AS instalasi_nama_ri,
                    penjualanresep_t.status_bayar,
                    0 AS jeniskasuspenyakit_id,
                    pasien_m.tanggal_lahir,
                    penjualanresep_t.penjualanresep_id,
                    COALESCE(penjualanresep_t.totaltarifservice, 0::double precision) AS jasa,
                    COALESCE(penjualanresep_t.biayaadministrasi, 0::double precision) AS administrasi,
                    COALESCE(penjualanresep_t.totalhargajual, 0::double precision) AS obat,
                    COALESCE(penjualanresep_t.totalhargajual, 0::double precision) + COALESCE(penjualanresep_t.totaltarifservice, 0::double precision) + COALESCE(penjualanresep_t.biayaadministrasi, 0::double precision) AS totalharga_jual,
                    NULL::integer AS kelas_bpjspendaftaran,
                    NULL::integer AS kelas_bpjsadmisi,
                    NULL::integer AS bpjs_idpendaftaran,
                    NULL::integer AS bpjs_idadmisi,
                    NULL::character varying AS no_bpjspendaftaran,
                    NULL::character varying AS no_bpjsadmisi,
                    NULL::character varying AS no_asuransipendaftaran,
                    NULL::character varying AS no_asuransiadmisi,
                    carabayar_m.groupcarabayar_id AS groupcarabayar_pendaftaran,
                    carabayar_m.groupcarabayar_id AS groupcarabayar_admisi,
                    pemberianpiutang_t.total_piutang,
                    NULL::integer AS dokterrj_id,
                    NULL::integer AS dokterri_id,
                    NULL::character varying AS keadaanmasuk_id,
                    NULL::character varying AS keadaan_masuk,
                    NULL::character varying AS transportasi_id,
                    NULL::character varying AS transportasi,
                    NULL::text AS keterangan_pendaftaran,
                    round(tagihan_resep.tagihan_obat) AS tagihan_belumbayar,
                    0 AS sisa_penunjang,
                    0 AS sisa_karcis,
                    tagihan_resep.tagihan_obat AS sisa_obat,
                    NULL::character varying AS status_periksa,
                    NULL::double precision AS tinggi,
                    NULL::double precision AS berat,
                    NULL::timestamp without time zone AS tgl_stopakomodasi,
                        CASE
                            WHEN penjualanresep_t.jenispenjualan::text = '343'::text THEN NULL::character varying
                            WHEN penjualanresep_t.jenispenjualan::text = '344'::text THEN lkp_namadepan.lookup_name
                            WHEN penjualanresep_t.jenispenjualan::text = '345'::text THEN lkp_gelardepan.lookup_name
                            ELSE NULL::character varying
                        END AS nama_depan,
                    lkp_jeniskelamin.lookup_name AS jenis_kelamin,
                    NULL::character varying AS namadepan_penanggung,
                    NULL::character varying AS nama_pasien_penanggung,
                    NULL::integer AS propinsi_id_penanggung,
                    NULL::integer AS kabupaten_id_penanggung,
                    NULL::integer AS kecamatan_id_penanggung,
                    NULL::integer AS kelurahan_id_penanggung,
                    NULL::integer AS rt_penanggung,
                    NULL::integer AS rw_penanggung,
                    NULL::character varying AS kode_pos_penanggung,
                    NULL::character varying AS alamat_pasien_penanggung,
                    NULL::character varying AS no_telepon_pasien_penanggung,
                    NULL::integer AS pekerjaan_id_penanggung,
                    NULL::character varying AS pt_penanggung,
                    NULL::character varying AS namabagian_penanggung,
                    NULL::character varying AS noindukkaryawan_penanggung,
                    NULL::character varying AS jpkm_penanggung,
                    NULL::integer AS pasienbatalperiksa_id,
                    NULL::integer AS status_ranap,
                    NULL::character varying AS no_sep,
                    instalasi_m.lob_id,
                    NULL::character varying AS kelas_ditagihkan,
                    NULL::integer AS kelas_ditagihkan_id,
                    NULL::integer AS ruangan_titipan_id,
                    NULL::character varying AS ruangan_titipan_nama,
                    NULL::boolean AS is_stopakomodasi
                   FROM penjualanresep_t
                     LEFT JOIN ( SELECT pasien.pasien_id,
                            pasien.nama_pasien,
                            pasien.no_rekam_medik,
                            pasien.tanggal_lahir,
                            pasien.namadepan,
                            pasien.jeniskelamin,
                            pasien.no_mobile_pasien,
                            pasien.jenisidentitas,
                            pasien.no_identitas_pasien,
                            pasien.no_telepon_pasien,
                            pasien.alamatemail,
                            pasien.alamat_pasien,
                            pasien.alamat_sekarang,
                            pasien.additional_pasien
                           FROM pasien_m pasien) pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
                     LEFT JOIN ( SELECT ruangan.ruangan_id,
                            ruangan.ruangan_nama,
                            ruangan.instalasi_id
                           FROM ruangan_m ruangan) ruangan_m ON penjualanresep_t.ruangan_id = ruangan_m.ruangan_id
                     LEFT JOIN ( SELECT instalasi.instalasi_id,
                            instalasi.instalasi_nama,
                            instalasi.lob_id
                           FROM instalasi_m instalasi) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                     LEFT JOIN ( SELECT carabayar.carabayar_id,
                            carabayar.carabayar_nama,
                            carabayar.groupcarabayar_id
                           FROM carabayar_m carabayar) carabayar_m ON penjualanresep_t.carabayar_id = carabayar_m.carabayar_id
                     LEFT JOIN ( SELECT penjamin.penjamin_id,
                            penjamin.penjamin_nama
                           FROM penjamin_m penjamin) penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
                     LEFT JOIN ( SELECT pegawai.pegawai_id,
                            pegawai.nama_pegawai,
                            pegawai.gelardepan
                           FROM pegawai_m pegawai) pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
                     LEFT JOIN ( SELECT pegawai.pegawai_id,
                            pegawai.nama_pegawai,
                            pegawai.gelardepan
                           FROM pegawai_m pegawai) karyawan ON penjualanresep_t.karyawan_id = karyawan.pegawai_id
                     LEFT JOIN ( SELECT pemberianpiutang.penjualanresep_id,
                            pemberianpiutang.total_piutang
                           FROM pemberianpiutang_t pemberianpiutang
                          WHERE pemberianpiutang.is_deleted = false) pemberianpiutang_t ON penjualanresep_t.penjualanresep_id = pemberianpiutang_t.penjualanresep_id
                     LEFT JOIN ( SELECT obatalkespasien_t.penjualanresep_id,
                            sum(obatalkespasien_t.hargajual_oa) AS tagihan_obat
                           FROM obatalkespasien_t
                          WHERE obatalkespasien_t.is_deleted = false AND obatalkespasien_t.obatsudahbayar_id IS NULL
                          GROUP BY obatalkespasien_t.penjualanresep_id) tagihan_resep ON penjualanresep_t.penjualanresep_id = tagihan_resep.penjualanresep_id
                     LEFT JOIN ( SELECT a.lookup_id,
                            a.lookup_name
                           FROM lookup_m a) lkp_namadepan ON pasien_m.namadepan::integer = lkp_namadepan.lookup_id
                     LEFT JOIN ( SELECT a.lookup_id,
                            a.lookup_name
                           FROM lookup_m a) lkp_jeniskelamin ON pasien_m.jeniskelamin::integer = lkp_namadepan.lookup_id
                     LEFT JOIN ( SELECT a.lookup_id,
                            a.lookup_name
                           FROM lookup_m a) lkp_gelardepan ON pegawai_m.gelardepan::integer = lkp_gelardepan.lookup_id
                  WHERE (penjualanresep_t.jenispenjualan::text = ANY (ARRAY['343'::text, '345'::text])) AND penjualanresep_t.is_deleted = false) data_info;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230217_035628_migrate_GM31_GM32_GM35_infodatapendaftaran_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230217_035628_migrate_GM31_GM32_GM35_infodatapendaftaran_v cannot be reverted.\n";

        return false;
    }
    */
}

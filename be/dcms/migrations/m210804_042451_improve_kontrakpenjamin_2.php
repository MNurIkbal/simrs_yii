<?php

use yii\db\Migration;

/**
 * Class m210804_042451_improve_kontrakpenjamin_2
 */
class m210804_042451_improve_kontrakpenjamin_2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DELETE FROM lookup_m WHERE lookup_type in (\'jenis_layanan\',\'LOB\');');

        $this->execute("
INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
(1033, 'jenis_layanan', 'Kelas', 'Kelas', 1, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(1025, 'jenis_layanan', 'Kelompok Tindakan', 'Kelompok Tindakan', 2, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(1026, 'jenis_layanan', 'Tindakan', 'Tindakan', 3, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(1027, 'jenis_layanan', 'Paket', 'Paket', 4, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(1028, 'jenis_layanan', 'Obat', 'Obat', 5, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(1034, 'LOB', 'OPD', 'OPD', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(1035, 'LOB', 'IGD', 'IGD', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(1036, 'LOB', 'IPD', 'IPD', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(1037, 'LOB', 'MCU', 'MCU', NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
");
        
        $this->execute('ALTER TABLE "public"."instalasi_m" ADD COLUMN if not exists "lob_id" int4;');

        $this->execute('COMMENT ON COLUMN "public"."instalasi_m"."lob_id" IS \'lookup_type = LOB\';');

       
        $this->execute('DROP VIEW if exists "public"."kontrakpenjamin_v";');
        
        $this->execute("
            CREATE VIEW \"public\".\"kontrakpenjamin_v\" AS  SELECT kontrakpenjamin_m.kontrakpenjamin_id,
    kontrakpenjamin_m.penjamin_id,
    penjamin_m.penjamin_nama,
    penjamin_m.carabayar_id,
    carabayar_m.carabayar_nama,
    kontrakpenjamin_m.no_kontrak,
    kontrakpenjamin_m.nama_kontrak,
    kontrakpenjamin_m.tgl_mulai,
    kontrakpenjamin_m.tgl_selesai,
    kontrakpenjamin_m.is_active
   FROM kontrakpenjamin_m
     JOIN penjamin_m ON kontrakpenjamin_m.penjamin_id = penjamin_m.penjamin_id
     JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
  WHERE kontrakpenjamin_m.is_deleted = false;");

        $this->execute('ALTER TABLE "public"."kontrakpenjamin_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."infodatapendaftaran_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infodatapendaftaran_v\" AS  SELECT data_info.pendaftaran_id,
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
    data_info.lob_id
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
            concat(fgetnamalookup(pasien_m.namadepan::integer), ' ', pasien_m.nama_pasien) AS nama_pasien,
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
            fgetnamalookup(pendaftaran_t.keadaan_masuk::integer) AS keadaan_masuk,
            pendaftaran_t.transportasi AS transportasi_id,
            fgetnamalookup(pendaftaran_t.transportasi::integer) AS transportasi,
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
            fgetnamalookup(pasien_m.namadepan::integer) AS nama_depan,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
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
            instalasi_m.lob_id
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
                    pasienadmisi.status_ranap
                   FROM pasienadmisi_t pasienadmisi) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
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
             JOIN ( SELECT kasuspenyakit.jeniskasuspenyakit_id,
                    kasuspenyakit.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m kasuspenyakit) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             LEFT JOIN ( SELECT pasienpulang_rj.pasienpulang_id,
                    pasienpulang_rj.tglpasienpulang
                   FROM pasienpulang_t pasienpulang_rj) pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN ( SELECT pasienpulang_ri.pasienpulang_id,
                    pasienpulang_ri.tglpasienpulang
                   FROM pasienpulang_t pasienpulang_ri) pulang_ri ON pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id
             LEFT JOIN ( SELECT bayaruangmuka_t_1.pendaftaran_id,
                    sum(bayaruangmuka_t_1.jumlah_uangmuka) AS jumlah_uangmuka
                   FROM bayaruangmuka_t bayaruangmuka_t_1
                  WHERE bayaruangmuka_t_1.is_deleted = false
                  GROUP BY bayaruangmuka_t_1.pendaftaran_id) bayaruangmuka_t ON pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id
             LEFT JOIN ( SELECT pasienmasukpenunjang.pasienmasukpenunjang_id,
                    pasienmasukpenunjang.pendaftaran_id
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
                   FROM pemberianpiutang_t pemberianpiutang) pemberianpiutang_t ON pemberianpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
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
          GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.instalasi_id, pendaftaran_t.ruangan_id, pendaftaran_t.pasien_id, pendaftaran_t.penjamin_id, pendaftaran_t.carabayar_id, pendaftaran_t.kelaspelayanan_id, pasienadmisi_t.kelaspelayanan_id, pasienadmisi_t.pegawai_id, pasienadmisi_t.status_ranap, pendaftaran_t.pasienpulang_id, pendaftaran_t.no_pendaftaran, pendaftaran_t.tgl_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pasien_m.no_mobile_pasien, instalasi_m.instalasi_nama, ruangan_m.ruangan_nama, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, kelaspelayanan_m.kelaspelayanan_nama, pasienpulang_t.tglpasienpulang, pasienadmisi_t.pasienpulang_id, pasienadmisi_t.pasienadmisi_id, pasienmasukpenunjang_t.pasienmasukpenunjang_id, pendaftaran_t.status_pasien, pulang_ri.tglpasienpulang, dok_rj_rd.nama_pegawai, dok_ri.nama_pegawai, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pasienadmisi_t.carabayar_id, carabayar_ri.carabayar_nama, pasienadmisi_t.penjamin_id, penjamin_ri.penjamin_nama, pasienadmisi_t.ruangan_id, ruang_ri.instalasi_id, ruang_ri.ruangan_nama, ins_ri.instalasi_nama, bayaruangmuka_t.jumlah_uangmuka, pemakaianuangmuka_t.pemakaian_uangmuka, pengembalianuangmuka_t.total_pengembalian, pendaftaran_t.status_bayar, jeniskasuspenyakit_m.jeniskasuspenyakit_id, pasien_m.tanggal_lahir, bpjs_pendaftaran.klsrawat, bpjs_admisi.klsrawat, bpjs_pendaftaran.bpjs_id, bpjs_admisi.bpjs_id, bpjs_pendaftaran.nokartuasuransi, bpjs_admisi.nokartuasuransi, asuransi_pendaftaran.nokartuasuransi, asuransi_admisi.nokartuasuransi, kelaspelayanan_ri.kelaspelayanan_nama, carabayar_m.groupcarabayar_id, carabayar_ri.groupcarabayar_id, pemberianpiutang_t.total_piutang, pendaftaran_t.keadaan_masuk, pendaftaran_t.transportasi, pendaftaran_t.keterangan_pendaftaran, penjualan_resep.biaya_adm, belum_bayar.total_tagihan, sisa_penunjang.total_tagihan, sisa_karcis.total_tagihan, (COALESCE(sisa_obat.total_tagihan, 0::double precision)), pendaftaran_t.status_periksa, periksa_fisik_rj.tinggi, periksa_fisik_rj.berat, periksa_fisik_rd.tinggi, periksa_fisik_rd.berat, periksa_fisik_ri.tinggi, periksa_fisik_ri.berat, pasien_m.namadepan, pasien_m.jenisidentitas, pasien_m.additional_pasien, pasien_m.no_identitas_pasien, pasien_m.no_telepon_pasien, pasien_m.alamatemail, pasien_m.alamat_sekarang, pasien_m.alamat_pasien, pasien_m.jeniskelamin, (
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN bpjs_pendaftaran.nosep
                    ELSE bpjs_admisi.nosep
                END), instalasi_m.lob_id
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
            tagihan_resep.tagihan_obat::integer AS tagihan_belumbayar,
            0 AS sisa_penunjang,
            0 AS sisa_karcis,
            tagihan_resep.tagihan_obat::integer AS sisa_obat,
            NULL::character varying AS status_periksa,
            NULL::double precision AS tinggi,
            NULL::double precision AS berat,
            NULL::timestamp without time zone AS tgl_stopakomodasi,
                CASE
                    WHEN penjualanresep_t.jenispenjualan::text = '343'::text THEN NULL::character varying
                    WHEN penjualanresep_t.jenispenjualan::text = '344'::text THEN fgetnamalookup(pasien_m.namadepan::integer)
                    WHEN penjualanresep_t.jenispenjualan::text = '345'::text THEN fgetnamalookup(pegawai_m.gelardepan::integer)
                    ELSE NULL::character varying
                END AS nama_depan,
            fgetnamalookup(pasien_m.jeniskelamin::integer) AS jenis_kelamin,
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
            instalasi_m.lob_id
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
                   FROM pemberianpiutang_t pemberianpiutang) pemberianpiutang_t ON penjualanresep_t.penjualanresep_id = pemberianpiutang_t.penjualanresep_id
             LEFT JOIN ( SELECT obatalkespasien_t.penjualanresep_id,
                    sum(obatalkespasien_t.hargajual_oa) AS tagihan_obat
                   FROM obatalkespasien_t
                  WHERE obatalkespasien_t.is_deleted = false AND obatalkespasien_t.obatsudahbayar_id IS NULL
                  GROUP BY obatalkespasien_t.penjualanresep_id) tagihan_resep ON penjualanresep_t.penjualanresep_id = tagihan_resep.penjualanresep_id
          WHERE (penjualanresep_t.jenispenjualan::text = ANY (ARRAY['343'::text, '345'::text])) AND penjualanresep_t.is_deleted = false) data_info;");
        
        $this->execute('ALTER TABLE "public"."infodatapendaftaran_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists "public"."infotagihanpasien_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infotagihanpasien_v\" AS  SELECT tagihan.pendaftaran_id,
    tagihan.no_pendaftaran,
    tagihan.tgl_pendaftaran,
    tagihan.tgl_pelayanan,
    tagihan.kelompoktindakan_id,
    tagihan.kelompoktindakan_nama,
    tagihan.pelayanan_id,
    tagihan.tindakan_obat_id,
    tagihan.tindakan_obat_nama,
    tagihan.is_obat,
    tagihan.tarif_satuan,
    tagihan.qty,
    tagihan.tarif_cyto,
    tagihan.sub_total,
    tagihan.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_pelayanan,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_pelayanan,
    tagihan.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tagihan.carabayar_pelayanan_id,
    carabayar_m.carabayar_nama AS carabayar_pelayanan,
    tagihan.penjamin_pelayanan_id,
    penjamin_m.penjamin_nama AS penjamin_pelayanan,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    tagihan.dokterpenanggungjawab_id,
    dokter_dpjp.nama_pegawai AS dokterpenanggungjawab_nama,
    pasien_m.pasien_id,
    pasien_m.no_mobile_pasien,
    pasien_m.alamatemail,
    tagihan.penjamin_pendaftaran_id,
    tagihan.pasienmasukpenunjang_id,
    tagihan.is_deleted,
    carabayar_m.groupcarabayar_id,
    tagihan.penjualanresep_id,
    tagihan.is_valid,
    tagihan.is_cyto,
    tagihan.pasienadmisi_id,
    tagihan.implementasi_id,
    tagihan.jeniskasuspenyakit_id,
    tagihan.discount,
    tagihan.tipepaket_id,
    tagihan.kamarruangan_id,
    tagihan.is_akomodasi,
    tagihan.kamartempattidur_id,
    tagihan.additional_data,
    tagihan.is_konsultasi,
    tagihan.tarifpenyulit_tindakan,
    tagihan.is_overwrite,
    tagihan.harga_origin,
    tagihan.cyto_origin,
    tagihan.penyulit_origin,
    instalasi_m.lob_id
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            tindakanpelayanan_t.tindakansudahbayar_id,
            tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            daftartindakan_m.kelompoktindakan_id,
            kelompoktindakan_m.kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            tindakanpelayanan_t.dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.is_deleted,
            0 AS penjualanresep_id,
            tindakanpelayanan_t.is_valid,
            tindakanpelayanan_t.cyto_tindakan AS is_cyto,
            tindakanpelayanan_t.pasienadmisi_id,
            tindakanpelayanan_t.implementasi_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            tindakanpelayanan_t.tarif_diskon AS discount,
            tindakanpelayanan_t.tipepaket_id,
            tindakanpelayanan_t.kamarruangan_id,
            daftartindakan_m.is_akomodasi,
            tindakanpelayanan_t.kamartempattidur_id,
            tindakanpelayanan_t.additional_data,
            daftartindakan_m.is_konsultasi,
            tindakanpelayanan_t.tarifpenyulit_tindakan,
            tindakanpelayanan_t.is_overwrite,
            tindakanpelayanan_t.harga_origin,
            tindakanpelayanan_t.cyto_origin,
            tindakanpelayanan_t.penyulit_origin
           FROM pendaftaran_t
             JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
             JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            tindakanpelayanan_t.tindakansudahbayar_id,
            tindakanpelayanan_t.tipepaket_id AS tindakan_obat_id,
            tipepaket_m.tipepaket_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                CASE
                    WHEN pendaftaran_t.instalasi_id = 21 THEN 17
                    ELSE NULL::integer
                END AS kelompoktindakan_id,
            'Others'::character varying AS kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            tindakanpelayanan_t.dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.is_deleted,
            0 AS penjualanresep_id,
            tindakanpelayanan_t.is_valid,
            tindakanpelayanan_t.cyto_tindakan AS is_cyto,
            tindakanpelayanan_t.pasienadmisi_id,
            tindakanpelayanan_t.implementasi_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            tindakanpelayanan_t.tarif_diskon AS discount,
            tindakanpelayanan_t.tipepaket_id,
            tindakanpelayanan_t.kamarruangan_id,
            NULL::boolean AS is_akomodasi,
            tindakanpelayanan_t.kamartempattidur_id,
            tindakanpelayanan_t.additional_data,
            NULL::boolean AS is_konsultasi,
            tindakanpelayanan_t.tarifpenyulit_tindakan,
            tindakanpelayanan_t.is_overwrite,
            tindakanpelayanan_t.harga_origin,
            tindakanpelayanan_t.cyto_origin,
            tindakanpelayanan_t.penyulit_origin
           FROM pendaftaran_t
             JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
             JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
        UNION ALL
         SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
            obatalkespasien_t.obatsudahbayar_id,
            obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
            obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
            true AS is_obat,
            obatalkespasien_t.hargasatuan_oa,
                CASE
                    WHEN obatalkespasien_t.det = 0::double precision THEN obatalkespasien_t.qty_oa
                    WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
                    ELSE obatalkespasien_t.det
                END AS qty,
            obatalkespasien_t.tarifcyto AS tarif_cyto,
            obatalkespasien_t.hargajual_oa AS sub_total,
            obatalkespasien_t.ruangan_id,
            obatalkespasien_t.kelaspelayanan_id,
            obatalkespasien_t.carabayar_id AS carabayar_pelayanan_id,
            obatalkespasien_t.penjamin_id AS penjamin_pelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            'Medicine'::character varying AS kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            obatalkespasien_t.pegawai_id AS dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            obatalkespasien_t.pasienmasukpenunjang_id,
            obatalkespasien_t.is_deleted,
            obatalkespasien_t.penjualanresep_id,
            NULL::boolean AS is_valid,
            NULL::boolean AS is_cyto,
            obatalkespasien_t.pasienadmisi_id,
            NULL::integer AS implementasi_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            obatalkespasien_t.tarif_diskon AS discount,
            NULL::integer AS tipepaket_id,
            NULL::integer AS kamarruangan_id,
            NULL::boolean AS is_akomodasi,
            NULL::integer AS kamartempattidur_id,
            NULL::text AS additional_data,
            NULL::boolean AS is_konsultasi,
            0 AS tarifpenyulit_tindakan,
            obatalkespasien_t.is_overwrite,
            obatalkespasien_t.harga_origin,
            0 AS cyto_origin,
            0 AS penyulit_origin
           FROM pendaftaran_t
             JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id AND obatalkespasien_t.is_deleted = false
             JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id) tagihan
     LEFT JOIN ruangan_m ON tagihan.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN kelaspelayanan_m ON tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN penjamin_m ON tagihan.penjamin_pelayanan_id = penjamin_m.penjamin_id
     LEFT JOIN carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN pasien_m ON tagihan.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m dokter_dpjp ON tagihan.dokterpenanggungjawab_id = dokter_dpjp.pegawai_id
  WHERE tagihan.tindakansudahbayar_id IS NULL AND tagihan.is_deleted = false
  ORDER BY tagihan.tgl_pelayanan DESC;");
        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210804_042451_improve_kontrakpenjamin_2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210804_042451_improve_kontrakpenjamin_2 cannot be reverted.\n";

        return false;
    }
    */
}

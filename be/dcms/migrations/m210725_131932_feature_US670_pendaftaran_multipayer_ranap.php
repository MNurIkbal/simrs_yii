<?php

use yii\db\Migration;

/**
 * Class m210725_131932_feature_US670_pendaftaran_multipayer_ranap
 */
class m210725_131932_feature_US670_pendaftaran_multipayer_ranap extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /**
        * TA411 View Multipayer
        */
        $this->execute('ALTER TABLE "public"."pendaftaran_multipayer_t" 
            DROP COLUMN IF EXISTS "nokartuasuransi",
            DROP COLUMN IF EXISTS "namapemilikasuransi",
            DROP COLUMN IF EXISTS "nomorpokokperusahaan",
            DROP COLUMN IF EXISTS "kelastanggunganasuransi_id",
            DROP COLUMN IF EXISTS "namaperusahaan",
            DROP COLUMN IF EXISTS "tgl_konfirmasi",
            DROP COLUMN IF EXISTS "status_konfirmasi";
        ');
        


        $this->execute('ALTER TABLE "public"."pasien_m" 
          ADD COLUMN IF NOT EXISTS "pasienubahdata_id" int4,
          ADD COLUMN IF NOT EXISTS "alamatdepan" varchar(20) COLLATE "pg_catalog"."default",
          ADD COLUMN IF NOT EXISTS "departemen" varchar(50) COLLATE "pg_catalog"."default",
          ADD COLUMN IF NOT EXISTS "posisi_bagian" varchar(50) COLLATE "pg_catalog"."default",
          ADD COLUMN IF NOT EXISTS "nama_perusahaan" varchar(50) COLLATE "pg_catalog"."default",
          ADD COLUMN IF NOT EXISTS "nomorindukpegawai" varchar(30) COLLATE "pg_catalog"."default",
          ADD COLUMN IF NOT EXISTS "alergi" text COLLATE "pg_catalog"."default",
          ADD COLUMN IF NOT EXISTS "kode_pos" varchar(100) COLLATE "pg_catalog"."default",
          ADD COLUMN IF NOT EXISTS "negara_id" int4;
      ');

        $this->execute('DROP VIEW if exists public.pendaftaran_multipayer_v;');
        $this->execute("
            CREATE VIEW \"public\".\"pendaftaran_multipayer_v\" AS
            SELECT pendaftaran_multipayer_t.pendaftaran_multipayer_id,
            pendaftaran_multipayer_t.asuransipasien_id,
            pendaftaran_multipayer_t.pendaftaran_id,
            pendaftaran_multipayer_t.pasien_id,
            pendaftaran_multipayer_t.penjamin_id,
            pendaftaran_multipayer_t.carabayar_id,
            pendaftaran_t.tgl_pendaftaran,
            asuransipasien_m.nokartuasuransi,
            asuransipasien_m.namapemilikasuransi,
            asuransipasien_m.nomorpokokperusahaan,
            asuransipasien_m.kelastanggunganasuransi_id,
            asuransipasien_m.namaperusahaan,
            asuransipasien_m.tgl_konfirmasi,
            asuransipasien_m.status_konfirmasi,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            penjamin_m.penjamin_nama,
            carabayar_m.carabayar_nama,
            kelaspelayanan_m.kelaspelayanan_nama
            FROM ((((((pendaftaran_multipayer_t
            LEFT JOIN ( SELECT a.pendaftaran_id,
            a.nokartuasuransi,
            a.namapemilikasuransi,
            a.nomorpokokperusahaan,
            a.kelastanggunganasuransi_id,
            a.namaperusahaan,
            a.tgl_konfirmasi,
            a.status_konfirmasi
            FROM asuransipasien_m a) asuransipasien_m ON ((pendaftaran_multipayer_t.pendaftaran_id = asuransipasien_m.pendaftaran_id)))
            JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik,
            a.nama_pasien
            FROM pasien_m a) pasien_m ON ((pendaftaran_multipayer_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
            FROM penjamin_m a) penjamin_m ON ((pendaftaran_multipayer_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
            FROM carabayar_m a) carabayar_m ON ((pendaftaran_multipayer_t.carabayar_id = carabayar_m.carabayar_id)))
            LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
            FROM kelaspelayanan_m a) kelaspelayanan_m ON ((asuransipasien_m.kelastanggunganasuransi_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN ( SELECT a.pendaftaran_id,
            a.tgl_pendaftaran
            FROM pendaftaran_t a) pendaftaran_t ON ((pendaftaran_multipayer_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
            ;");
        $this->execute('
            ALTER TABLE public.pendaftaran_multipayer_v OWNER TO postgres;
            ');

        /**
        * TA321 Improve View Informasi RI infokunjunganri_v
        */

        $this->execute('DROP VIEW if exists public.infokunjunganri_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infokunjunganri_v\" AS
            SELECT pasien_m.pasien_id,
            pasien_m.jenisidentitas,
            pasien_m.no_identitas_pasien,
            pasien_m.namadepan,
            fgetnamalookup((pasien_m.namadepan)::integer) AS nama_depan,
            pasien_m.nama_pasien,
            pasien_m.nama_bin,
            pasien_m.jeniskelamin,
            pasien_m.tempat_lahir,
            pasien_m.tanggal_lahir,
            pasien_m.alamat_pasien,
            pasien_m.rt,
            pasien_m.rw,
            pasien_m.agama,
            pasien_m.golongandarah,
            pasien_m.photopasien,
            pasien_m.alamatemail,
            pasien_m.statusrekammedis,
            pasien_m.statusperkawinan,
            pasien_m.no_rekam_medik,
            pasien_m.tgl_rekam_medik,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
            pendaftaran_t.no_urutantri,
            pendaftaran_t.transportasi,
            pendaftaran_t.keadaan_masuk,
            pendaftaran_t.status_pasien,
            pendaftaran_t.alih_status,
            pendaftaran_t.by_phone,
            pendaftaran_t.kunjungan_rumah,
            pendaftaran_t.status_masuk,
            pendaftaran_t.umur,
            pendaftaran_t.golonganumur_id,
            asuransipasien_m.nokartuasuransi AS no_asuransi,
            asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
            asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_id,
            penjamin_m.penjamin_nama,
            caramasuk_m.caramasuk_id,
            caramasuk_m.caramasuk_nama,
            pendaftaran_t.shift_id,
            rujukan_t.no_rujukan,
            rujukan_t.nama_perujuk,
            rujukan_t.tanggal_rujukan,
            rujukan_t.kodediagnosa_rujukan,
            asalrujukan_m.asalrujukan_id,
            asalrujukan_m.asalrujukan_nama,
            penanggungjawab_m.penanggungjawab_id,
            penanggungjawab_m.pengantar,
            penanggungjawab_m.hubungankeluarga,
            penanggungjawab_m.penanggungjawab_nama,
            ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
            jeniskasuspenyakit_m.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            kelaspelayanan_m.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pasienadmisi_t.pasienadmisi_id,
            pasienadmisi_t.tgl_admisi,
            pasienadmisi_t.tgl_pulang,
            pasienadmisi_t.kunjungan,
            pasienadmisi_t.status_keluar,
            pasienadmisi_t.rawat_gabung,
            kamarruangan_m.kamarruangan_id,
            pegawai_m.gelardepan,
            pegawai_m.nama_pegawai,
            pegawai_m.gelarbelakang,
            asuransipasien_m.status_konfirmasi,
            asuransipasien_m.tgl_konfirmasi,
            pasienadmisi_t.pegawai_id,
            pasien_m.rhesus,
            pasien_m.anakke,
            pasien_m.jumlah_bersaudara,
            pasien_m.no_telepon_pasien,
            pasien_m.no_mobile_pasien,
            pasien_m.warga_negara,
            suku_m.suku_id,
            suku_m.suku_nama,
            pendidikan_m.pendidikan_id,
            pendidikan_m.pendidikan_nama,
            pasien_m.nama_ibu,
            pasien_m.nama_ayah,
            asuransipasien_m.nopeserta,
            asuransipasien_m.tglcetakkartuasuransi,
            asuransipasien_m.kodefeskestk1,
            asuransipasien_m.nama_feskestk1,
            asuransipasien_m.masaberlakukartu,
            asuransipasien_m.nokartukeluarga,
            asuransipasien_m.nopassport,
            asuransipasien_m.is_active,
            pendaftaran_t.keterangan_pendaftaran,
            pegawai_m.kelompokpegawai_id,
            pasien_m.is_deleted,
            fgetnamalookup(pasienadmisi_t.status_ranap) AS status_periksa,
            kamarruangan_m.kamarruangan_nokamar,
            kamartempattidur_m.no_tempattidur,
            golonganumur_m.golonganumur_nama,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
            pasienadmisi_t.created_by,
            kamartempattidur_m.kamartempattidur_id,
            pasienadmisi_t.bpjs_id,
            pasienadmisi_t.status_ranap AS status_periksa_id,
            bpjs_t.nosep,
            kelaspelayanan_m.urutankelas,
            kelaspelayanan_m.bpjs_kelas,
            bpjs_t.klsrawat,
            pasienadmisi_t.is_aps,
            pasienadmisi_t.is_pasientitipan,
            CASE
            WHEN (pindah_kamar.pindahkamar_id IS NULL) THEN pasienadmisi_t.kelas_ditagihkan_id
            ELSE pindah_kamar.kelas_ditagihkan_id
            END AS kelas_ditagihkan_id,
            CASE
            WHEN (pindah_kamar.pindahkamar_id IS NULL) THEN kelas_ditagihkan.kelaspelayanan_nama
            ELSE pindah_kamar.kelas_ditagihkan
            END AS kelas_ditagihkan_nama,
            pasienadmisi_t.kamar_titipan_id,
            kamar_ditagihkan.kamarruangan_nokamar AS kamar_titipan_nama,
            pasienadmisi_t.ruangan_titipan_id,
            ruangan_ditagihkan.ruangan_nama AS ruangan_titipan_nama,
            pasienadmisi_t.is_stoptitipan,
            pindah_kamar.pindahkamar_id,
            CASE
            WHEN ((stop_titipan.pindahkamar_id IS NULL) AND (pasienadmisi_t.is_stoptitipan IS FALSE)) THEN false
            WHEN ((stop_titipan.pindahkamar_id IS NULL) AND (pasienadmisi_t.is_stoptitipan IS TRUE)) THEN true
            WHEN ((stop_titipan.is_pasientitipan IS FALSE) AND (stop_titipan.is_stoptitipan IS FALSE)) THEN true
            WHEN ((stop_titipan.is_pasientitipan IS TRUE) AND (stop_titipan.is_stoptitipan IS TRUE)) THEN true
            ELSE false
            END AS is_stoppasientitipan,
            stop_titipan.is_pasientitipan AS is_pasientitipan_pk,
            carakeluar_m.carakeluar_nama,
            fgetnamalookup((pasien_m.agama)::integer) AS agama_nama,
            pendaftaran_t.last_modified_date AS tgl_update_terakhir,
            petugas_pemakai.nama_pegawai AS petugas_nama,
            pendaftaran_t.created_date AS tgl_pembuatan,
            petugas_pembuat.nama_pegawai AS pembuat_nama,
            pasien_m.additional_pasien,
            pendaftaran_t.is_stopakomodasi,
            carabayar_m.carabayar_kode_warna,
            CASE
            WHEN (antrian_poli.jenisantrian_id = 312) THEN (antrian_poli.no_antrian)::text
            ELSE '-'::text
            END AS no_antrian_poli,
            pasien_m.catatanpenting_pasien,
            penanggungjawab_m.penanggungjawab_alamat,
            penanggungjawab_m.penanggungjawab_notelp,
            penanggungjawab_m.pj_pekerjaan_id,
            pj_kerja.pekerjaan_nama AS pj_pekerjaan_nama,
            penanggungjawab_m.pj_propinsi_id,
            pj_prop.propinsi_nama AS pj_propinsi_nama,
            penanggungjawab_m.pj_kabupaten_id,
            pj_kab.kabupaten_nama AS pj_kabupaten_nama,
            penanggungjawab_m.pj_kecamatan_id,
            pj_kec.kecamatan_nama AS pj_kecamatan_nama,
            penanggungjawab_m.pj_kelurahan_id,
            pj_kel.kelurahan_nama AS pj_kelurahan_nama,
            pasien_m.bahasa_sehari,
            fgetnamalookup((pasien_m.bahasa_sehari)::integer) AS bahasa_sehari_nama,
            penanggungjawab_m.pj_namadepan,
            fgetnamalookup((penanggungjawab_m.pj_namadepan)::integer) AS pj_namadepan_nama,
            pasienadmisi_t.limit_tagihan,
            perujuk_m.namaperujuk AS rujukan_dari,
            resumemedisri_t.resumemedisri_id,
            pendaftaran_t.tgl_stopakomodasi,
            pasien_m.nopeserta_bpjs,
            pendaftaran_t.petugas_id AS pegpetugas_id,
            pegawai_petugas.nama_pegawai AS pegpetugas_nama,
            pendaftaran_t.petugas_tgl_pembuat,
            pendaftaran_t.additional_data,
            pendaftaran_t.is_multipayer
            FROM (((((((((((((((((((((((((((((((((((((((((pendaftaran_t
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
            LEFT JOIN perujuk_m ON ((rujukan_t.rujukandari_id = perujuk_m.perujuk_id)))
            LEFT JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
            LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            LEFT JOIN caramasuk_m ON ((pasienadmisi_t.caramasuk_id = caramasuk_m.caramasuk_id)))
            JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
            JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
            JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            LEFT JOIN antrian_t ON ((antrian_t.antrian_id = pendaftaran_t.antrian_id)))
            LEFT JOIN antrian_t antrian_poli ON (((pendaftaran_t.pendaftaran_id = antrian_poli.pendaftaran_id) AND (antrian_poli.jenisantrian_id = 312))))
            LEFT JOIN pegawai_m pegawai_petugas ON ((pendaftaran_t.petugas_id = pegawai_petugas.pegawai_id)))
            LEFT JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN loginpemakai_k petugas ON ((pendaftaran_t.last_modified_by = petugas.loginpemakai_id)))
            LEFT JOIN pegawai_m petugas_pemakai ON ((petugas.pegawai_id = petugas_pemakai.pegawai_id)))
            LEFT JOIN loginpemakai_k pembuat ON ((pendaftaran_t.created_by = pembuat.loginpemakai_id)))
            LEFT JOIN pegawai_m petugas_pembuat ON ((pembuat.pegawai_id = petugas_pembuat.pegawai_id)))
            LEFT JOIN pekerjaan_m pj_kerja ON ((penanggungjawab_m.pj_pekerjaan_id = pj_kerja.pekerjaan_id)))
            LEFT JOIN propinsi_m pj_prop ON ((penanggungjawab_m.pj_propinsi_id = pj_prop.propinsi_id)))
            LEFT JOIN kabupaten_m pj_kab ON ((penanggungjawab_m.pj_kabupaten_id = pj_kab.kabupaten_id)))
            LEFT JOIN kecamatan_m pj_kec ON ((penanggungjawab_m.pj_kecamatan_id = pj_kec.kecamatan_id)))
            LEFT JOIN kelurahan_m pj_kel ON ((penanggungjawab_m.pj_kelurahan_id = pj_kel.kelurahan_id)))
            LEFT JOIN suku_m ON ((pasien_m.suku_id = suku_m.suku_id)))
            LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
            LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
            LEFT JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
            JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
            LEFT JOIN bpjs_t ON ((pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id)))
            LEFT JOIN kelaspelayanan_m kelas_ditagihkan ON ((pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id)))
            LEFT JOIN kamarruangan_m kamar_ditagihkan ON ((pasienadmisi_t.kamar_titipan_id = kamar_ditagihkan.kamarruangan_id)))
            LEFT JOIN ruangan_m ruangan_ditagihkan ON ((pasienadmisi_t.ruangan_titipan_id = ruangan_ditagihkan.ruangan_id)))
            LEFT JOIN ( SELECT pindahkamar_t.pindahkamar_id,
            pindahkamar_t.pasienadmisi_id,
            pindahkamar_t.kelas_ditagihkan_id,
            kelas_ditagihkan_1.kelaspelayanan_nama AS kelas_ditagihkan,
            pindahkamar_t.is_stoptitipan
            FROM ((pindahkamar_t
            JOIN ( SELECT max(pk.pindahkamar_id) AS pindahkamar_id,
            pk.pasienadmisi_id
            FROM pindahkamar_t pk
            GROUP BY pk.pasienadmisi_id) max_pk ON (((pindahkamar_t.pindahkamar_id = max_pk.pindahkamar_id) AND (pindahkamar_t.pasienadmisi_id = max_pk.pasienadmisi_id))))
            LEFT JOIN kelaspelayanan_m kelas_ditagihkan_1 ON ((pindahkamar_t.kelas_ditagihkan_id = kelas_ditagihkan_1.kelaspelayanan_id)))
            WHERE ((pindahkamar_t.is_deleted = false) AND (pindahkamar_t.is_pasientitipan = true))) pindah_kamar ON ((pasienadmisi_t.pasienadmisi_id = pindah_kamar.pasienadmisi_id)))
            LEFT JOIN ( SELECT pindahkamar_t.pindahkamar_id,
            pindahkamar_t.pasienadmisi_id,
            pindahkamar_t.is_pasientitipan,
            pindahkamar_t.is_stoptitipan
            FROM (pindahkamar_t
            JOIN ( SELECT max(pk.pindahkamar_id) AS pindahkamar_id,
            pk.pasienadmisi_id
            FROM pindahkamar_t pk
            GROUP BY pk.pasienadmisi_id) max_pk ON (((pindahkamar_t.pindahkamar_id = max_pk.pindahkamar_id) AND (pindahkamar_t.pasienadmisi_id = max_pk.pasienadmisi_id))))
            WHERE (pindahkamar_t.is_deleted = false)) stop_titipan ON ((pasienadmisi_t.pasienadmisi_id = stop_titipan.pasienadmisi_id)))
            LEFT JOIN pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienadmisi_id)))
            LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
            LEFT JOIN resumemedisri_t ON (((pendaftaran_t.pendaftaran_id = resumemedisri_t.pendaftaran_id) AND (pasienadmisi_t.pasienadmisi_id = resumemedisri_t.pasienadmisi_id))))
            WHERE ((pendaftaran_t.is_active = true) AND (pendaftaran_t.is_deleted = false))
            ;");
        $this->execute('
            ALTER TABLE public.infokunjunganri_v OWNER TO postgres;
            ');

        /**
        * TA314 Penyesuaian Trigger pendaftaran_t dan pendaftaran_inap
        */
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"pendaftaran_t\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
    vPrefix VARCHAR;
  vNumber VARCHAR;
    -- Penambahan Triger Dari Yaya
    -- Tanggal 08-08-2019
    dataAntrian VARCHAR;
    dataKarcis VARCHAR;
    dataRujukan VARCHAR;
    dataPenanggung VARCHAR;
    dataAsuransi VARCHAR;
    dataPasien VARCHAR;
        dataOrderMcu VARCHAR;
    
    paramJson VARCHAR;
    
    -- Get New Pasien
    pasienId INTEGER;
    
    noAsuransi VARCHAR;
    idAsuransi INTEGER;
    idPasienAsuransi INTEGER;
    -- Generate Id
    rujukanId INTEGER;
    antrianId INTEGER;
    penanggungId INTEGER;
    
    -- Antrian Active
    isActive BOOLEAN;
    
    -- Count Tagihan
    countTagihan INTEGER;
    countPenunjang INTEGER;
    --antrian
    v_konfigantrian INTEGER;
    
    -- Kebutuhan untuk penunjang
    vPenunjang VARCHAR;
    noAntrian VARCHAR;
    idPenunjang INTEGER;
    
    -- Pendaftaran Online
    
    -- Update untuk pasienadmisi ranap
    vadmisi VARCHAR;
    vmasukkamar VARCHAR;
    vpasienadmisi_id int4;
    vuser_id int4;
    vpendaftaran_id int4;
    vcarabayar_id int4;
    vpenjamin_id int4;
    vkettempattidur_id int4; 
    vkelahiran_id int4;
    vpendaftaranasal_id int4;
        
        -- Update kebutuhan BSL
        noRekamMedik VARCHAR;
        dataRekamMedik VARCHAR;
        
        -- Kebutuhan antrian
        vnourut BOOLEAN;
        
        -- Kebutuhan SY Penanggung Biaya
        dataPenanggungBiaya VARCHAR;
        idPenanggungBiaya INTEGER;
        penanggungbiayaId INTEGER;
        
        -- Kebutuhan SY Keluarga Pasien
        dataKeluargaPasien VARCHAR;
        idKeluargaPasien INTEGER;
        keluargapasienId INTEGER;
        
        -- Multi Payer 3462
        dataAdditionalPayer VARCHAR;
        isMultiPayer BOOLEAN;
        
        noAsuransi1 VARCHAR;
    idAsuransi1 INTEGER;
    idPasienAsuransi1 INTEGER;
        
        noAsuransi2 VARCHAR;
    idAsuransi2 INTEGER;
    idPasienAsuransi2 INTEGER;
        
BEGIN
    vcarabayar_id := NEW.carabayar_id;
    vpenjamin_id := NEW.penjamin_id;
        
    
--         SELECT 
--             CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(substring(no_pendaftaran FROM '[0-9]+')), 6) AS INT), 0) + 1 AS                VARCHAR(6)), 6, '0')) last_no
--         INTO 
--              vNumber
--         FROM pendaftaran_t where instalasi_id = NEW.instalasi_id;
    
                    SELECT 
                        (RIGHT('0' || date_part('YEAR',now()),2) ||
                        RIGHT('0' || date_part('month',now()),2) ||
                        RIGHT('0' || date_part('DAY',now()),2) ||
                        CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(no_pendaftaran), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0'))) last_no
                    INTO 
                        vNumber
                    FROM pendaftaran_t
                    WHERE instalasi_id = NEW.instalasi_id 
                    AND pendaftaran_t.tgl_pendaftaran::DATE = CURRENT_DATE;
        
                    SELECT 
                instalasi_singkatan 
                    INTO 
                        vPrefix 
                    FROM instalasi_m WHERE instalasi_id = NEW.instalasi_id;
        
        
        NEW.no_pendaftaran := TRIM(vPrefix) || vNumber;
        
        -- Generate Form Pendaftaran
         paramJson := NEW.additional_data;  
         dataKarcis := paramJson::json->>'tarif';
         dataRujukan := paramJson::json->>'rujukan';
         dataAntrian := paramJson::json->>'antrian';
         dataPenanggung := paramJson::json->>'penanggung_jawab';
                 dataOrderMcu = paramJson::json->>'order_mcu';
         dataAsuransi := paramJson::json->>'asuransi';
         dataPasien := paramJson::json->>'pasien';
                 dataRekamMedik := paramJson::json->>'no_rekam_medik';
         vPenunjang := paramJson::json->>'tarif_penunjang';
         vadmisi := paramJson::json->>'pasien_admisi';
       vmasukkamar := paramJson::json->>'masuk_kamar';
         countTagihan := json_array_length(dataKarcis::json);
         countPenunjang := json_array_length(vPenunjang::json);
         vkelahiran_id := (paramJson::json->>'kelahiran_id')::int4;
         vpendaftaranasal_id := (paramJson::json->>'pendaftaranasal_id')::int4;
                 dataPenanggungBiaya := paramJson::json->>'penanggungbiaya';
                 dataKeluargaPasien := paramJson::json->>'keluargapasien';
                 
                 dataAdditionalPayer := paramJson::json->>'additional_payer';
         
         IF (dataPasien::json->>'nama_pasien' IS NOT NULL AND NEW.pasien_id IS NULL) THEN
                    noRekamMedik := dataRekamMedik::json->>'no_rekam_medik';
  
                    IF NOT EXISTS (
                        SELECT 1 FROM pasien_m
                        WHERE no_rekam_medik = noRekamMedik
                        LIMIT 1
                    )
                    THEN
                        INSERT INTO pasien_m (
                            tgl_rekam_medik,
                            jenisidentitas,
                            no_identitas_pasien,
                            namadepan,
                            nama_pasien,
                            nama_bin,
                            jeniskelamin,
                            tempat_lahir,
                            tanggal_lahir,
                            golonganumur_id,
                            alamat_pasien,
                            rt,
                            rw,
                            propinsi_id,
                            kabupaten_id,
                            kecamatan_id,
                            kelurahan_id,
                            pendidikan_id,
                            pekerjaan_id,
                            suku_id,
                            statusperkawinan,
                            agama,
                            golongandarah,
                            rhesus,
                            anakke,
                            jumlah_bersaudara,
                            no_telepon_pasien,
                            no_mobile_pasien,
                            warga_negara,
                            photopasien,
                            alamatemail,
                            nama_ibu,
                            nama_ayah,
                            statusrekammedis,
                            alamat_sekarang,
                            is_aps,
                            created_by,
                                                        additional_pasien,
                                                        bahasa_sehari,
                                                        catatanpenting_pasien,
                                                        departemen,
                                                        posisi_bagian,
                                                        nama_perusahaan,
                                                        nomorindukpegawai
                        ) VALUES (
                            (dataPasien::json->>'tgl_rekam_medik')::DATE,
                            
                            (CASE
                                dataPasien::json->>'jenisidentitas'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'jenisidentitas')::INTEGER END),
                            
                            dataPasien::json->>'no_identitas_pasien',
                            
                            (CASE
                                dataPasien::json->>'namadepan'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'namadepan')::INTEGER END),
                            
                            dataPasien::json->>'nama_pasien',
                            dataPasien::json->>'nama_bin',
                            
                            (CASE
                                dataPasien::json->>'jeniskelamin'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'jeniskelamin')::INTEGER END),
                            
                            dataPasien::json->>'tempat_lahir',
                            (dataPasien::json->>'tanggal_lahir')::DATE,
                            (dataPasien::json->>'golonganumur_id')::INTEGER,
                            (dataPasien::json->>'alamat_pasien'),
                            (CASE
                                dataPasien::json->>'rt'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'rt')::INTEGER END),
                            (CASE
                                dataPasien::json->>'rw'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'rw')::INTEGER END),
                            (CASE
                                dataPasien::json->>'propinsi_id'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'propinsi_id')::INTEGER END),
                            (CASE
                                dataPasien::json->>'kabupaten_id'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'kabupaten_id')::INTEGER END),
                            (CASE
                                dataPasien::json->>'kecamatan_id'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'kecamatan_id')::INTEGER END),
                            (CASE
                                dataPasien::json->>'kelurahan_id'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'kelurahan_id')::INTEGER END),
                            (CASE
                                dataPasien::json->>'pendidikan_id'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'pendidikan_id')::INTEGER END),
                            (CASE
                                dataPasien::json->>'pekerjaan_id'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'pekerjaan_id')::INTEGER END),
                            (CASE
                                dataPasien::json->>'suku_id'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'suku_id')::INTEGER END),
                            (CASE
                                dataPasien::json->>'statusperkawinan'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'statusperkawinan')::INTEGER END),
                            (CASE
                                dataPasien::json->>'agama'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'agama')::INTEGER END),
                            (CASE
                                dataPasien::json->>'golongandarah'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'golongandarah')::INTEGER END),
                            (CASE
                                dataPasien::json->>'rhesus'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'rhesus')::INTEGER END),
                            (CASE
                                dataPasien::json->>'anakke'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'anakke')::INTEGER END),
                            (CASE
                                dataPasien::json->>'jumlah_bersaudara'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'jumlah_bersaudara')::INTEGER END),
                            dataPasien::json->>'no_telepon_pasien',
                            dataPasien::json->>'no_mobile_pasien',
                            (CASE
                                dataPasien::json->>'warga_negara'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'warga_negara')::INTEGER END),
                            
                            dataPasien::json->>'photopasien',
                            dataPasien::json->>'alamatemail',
                            dataPasien::json->>'nama_ibu',
                            dataPasien::json->>'nama_ayah',
                            336,
                            dataPasien::json->>'alamat_sekarang',
                            (dataPasien::json->>'is_aps')::BOOLEAN,
                            NEW.created_by,
                                                        dataPasien::json->>'additional_identitas',
                                                        (dataPasien::json->>'bahasa_sehari')::INTEGER,
                                                        dataPasien::json->>'catatanpenting_pasien',
                                                        dataPasien::json->>'departemen',
                                                        dataPasien::json->>'posisi_bagian',
                                                        dataPasien::json->>'nama_perusahaan',
                                                        dataPasien::json->>'nomorindukpegawai'
                        ) RETURNING pasien_id INTO pasienId;
                        NEW.pasien_id := pasienId;
                                                ELSE
                                SELECT 
                    pasien_id
                INTO
                    pasienId
                FROM pasien_m
                                WHERE no_rekam_medik = noRekamMedik;
                NEW.pasien_id := pasienId;
    UPDATE pasien_m
    SET 
            --tgl_rekam_medik = (dataPasien::json->>'tgl_rekam_medik')::DATE,
            jenisidentitas = (CASE
                                dataPasien::json->>'jenisidentitas'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'jenisidentitas')::INTEGER END),
      no_identitas_pasien = (dataPasien::json->>'no_identitas_pasien'),
            namadepan = (CASE
                                dataPasien::json->>'namadepan'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'namadepan')::INTEGER END),
            nama_pasien = (dataPasien::json->>'nama_pasien'),
            nama_bin = (dataPasien::json->>'nama_bin'),
            jeniskelamin = (CASE
                                dataPasien::json->>'jeniskelamin'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'jeniskelamin')::INTEGER END),
            tempat_lahir = (dataPasien::json->>'tempat_lahir'),
            tanggal_lahir = (dataPasien::json->>'tanggal_lahir')::DATE,
            golonganumur_id = (dataPasien::json->>'golonganumur_id')::INTEGER,
            alamat_pasien = (dataPasien::json->>'alamat_pasien'),
            rt = (CASE
                                dataPasien::json->>'rt'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'rt')::INTEGER END),
            rw = (CASE
                                dataPasien::json->>'rw'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'rw')::INTEGER END),
            propinsi_id = (CASE
                                dataPasien::json->>'propinsi_id'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'propinsi_id')::INTEGER END),
            kabupaten_id = (CASE
                                dataPasien::json->>'kabupaten_id'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'kabupaten_id')::INTEGER END),
            kecamatan_id = (CASE
                                dataPasien::json->>'kecamatan_id'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'kecamatan_id')::INTEGER END),
            kelurahan_id = (CASE
                                dataPasien::json->>'kelurahan_id'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'kelurahan_id')::INTEGER END),
            pendidikan_id = (CASE
                                dataPasien::json->>'pendidikan_id'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'pendidikan_id')::INTEGER END),
            pekerjaan_id = (CASE
                                dataPasien::json->>'pekerjaan_id'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'pekerjaan_id')::INTEGER END),
            suku_id = (CASE
                                dataPasien::json->>'suku_id'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'suku_id')::INTEGER END),
            statusperkawinan = (CASE
                                dataPasien::json->>'statusperkawinan'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'statusperkawinan')::INTEGER END),
            agama = (CASE
                                dataPasien::json->>'agama'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'agama')::INTEGER END),
            golongandarah = (CASE
                                dataPasien::json->>'golongandarah'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'golongandarah')::INTEGER END),
            rhesus = (CASE
                                dataPasien::json->>'rhesus'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'rhesus')::INTEGER END),
            anakke = (CASE
                                dataPasien::json->>'anakke'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'anakke')::INTEGER END),
            jumlah_bersaudara = (CASE
                                dataPasien::json->>'jumlah_bersaudara'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'jumlah_bersaudara')::INTEGER END),
            no_telepon_pasien = (dataPasien::json->>'no_telepon_pasien'),
            no_mobile_pasien = (dataPasien::json->>'no_mobile_pasien'),
            warga_negara = (CASE
                                dataPasien::json->>'warga_negara'
                            WHEN NULL 
                            THEN NULL 
                            ELSE (dataPasien::json->>'warga_negara')::INTEGER END),
            photopasien = (dataPasien::json->>'photopasien'),
            alamatemail = (dataPasien::json->>'alamatemail'),
            nama_ibu = (dataPasien::json->>'nama_ibu'),
            nama_ayah = (dataPasien::json->>'nama_ayah'),
            statusrekammedis = 366,
            alamat_sekarang = (dataPasien::json->>'alamat_sekarang'),
            is_aps =  (dataPasien::json->>'is_aps')::BOOLEAN,
            --created_by = (NEW.created_by),
            last_modified_by = (NEW.created_by),
            additional_pasien = (dataPasien::json->>'additional_identitas'),
            bahasa_sehari = (dataPasien::json->>'bahasa_sehari'),
            catatanpenting_pasien = (dataPasien::json->>'catatanpenting_pasien')
    WHERE no_rekam_medik = noRekamMedik;
  END IF;
END IF;
         
         IF (dataAsuransi::json->>'nokartuasuransi' IS NOT NULL) THEN
                noAsuransi := dataAsuransi::json->>'nokartuasuransi';
                SELECT 
                    asuransipasien_id,
                    pasien_id
                INTO
                    idAsuransi,
                    idPasienAsuransi
                FROM asuransipasien_m
                WHERE nokartuasuransi = noAsuransi
                AND penjamin_id = NEW.penjamin_id
                AND pasien_id = NEW.pasien_id
                AND carabayar_id = NEW.carabayar_id;
                
--              IF (idPasienAsuransi != NEW.pasien_id) THEN
-- -- Sementara case asuranasi
-- --                   RAISE EXCEPTION 'Duplicate No Asuransi: %', dataAsuransi::json->>'nokartuasuransi' 
-- --                           USING HINT = 'No Asuransi Sudah digunakan';
--              ELSE
                IF (idAsuransi IS NULL) THEN
                    INSERT INTO asuransipasien_m (
                        kelastanggunganasuransi_id,
                        namapemilikasuransi,
                        namaperusahaan,
                        nokartuasuransi,
                        nomorpokokperusahaan,
                        status_konfirmasi,
                        tgl_konfirmasi,
                        created_by,
                        pasien_id,
                        penjamin_id,
                        carabayar_id,
                                                masaberlakukartu,
                                                nama_asuransi
                    ) VALUES (
                        (CASE dataAsuransi::json->>'kelastanggungan_id'
                                WHEN '' THEN
                                    NULL
                                ELSE
                                    (dataAsuransi::json->>'kelastanggungan_id')::INTEGER
                        END),
                        dataAsuransi::json->>'namapemilikasuransi',
                        dataAsuransi::json->>'namaperusahaan',
                        dataAsuransi::json->>'nokartuasuransi',
                        dataAsuransi::json->>'nomorpokokperusahaan',
                        dataAsuransi::json->>'status_konfirmasi',
                        (dataAsuransi::json->>'tgl_konfirmasi')::TIMESTAMP,
                        NEW.created_by,
                        NEW.pasien_id,
                        NEW.penjamin_id,
                        NEW.carabayar_id,
                                                (dataAsuransi::json->>'masaberlakukartu')::DATE,
                                                dataAsuransi::json->>'nama_asuransi'
                    ) RETURNING asuransipasien_id INTO idAsuransi;
                    
                    NEW.asuransipasien_id = idAsuransi;
                ELSE 
                        UPDATE asuransipasien_m SET 
                            kelastanggunganasuransi_id = (CASE dataAsuransi::json->>'kelastanggungan_id'
                                WHEN '' THEN
                                    NULL
                                ELSE
                                    (dataAsuransi::json->>'kelastanggungan_id')::INTEGER
                            END), 
                            namapemilikasuransi = dataAsuransi::json->>'namapemilikasuransi',
                            namaperusahaan = dataAsuransi::json->>'namaperusahaan',
                            nomorpokokperusahaan = dataAsuransi::json->>'nomorpokokperusahaan',
                            status_konfirmasi = dataAsuransi::json->>'status_konfirmasi',
                            tgl_konfirmasi = (dataAsuransi::json->>'tgl_konfirmasi')::TIMESTAMP,
                            last_modified_by = NEW.created_by
                        WHERE asuransipasien_id = idAsuransi;           
                        
                        NEW.asuransipasien_id = idAsuransi;
                END IF;
--              END IF;
         END IF;

         IF (dataPenanggung::json->>'pj_pengantar' IS NOT NULL) THEN
                INSERT INTO penanggungjawab_m (
                    pengantar,
                    jenisidentitas,
                    no_identitas,
                    hubungankeluarga,
                    penanggungjawab_nama,
                    penanggungjawab_tempatlahir,
                    penanggungjawab_tgllahir,
                    penanggungjawab_jeniskelamin,
                    penanggungjawab_alamat,
                    penanggungjawab_notelp,
                    penanggungjawab_nohp,
                    pasien_id,
                    created_by,
                                        pj_namadepan,
                                        pj_propinsi_id,
                                        pj_kabupaten_id,
                                        pj_kecamatan_id,
                                        pj_kelurahan_id,
                                        pj_pekerjaan_id,
                                        pj_rt,
                                        pj_rw
                ) VALUES (
                    dataPenanggung::json->>'pj_pengantar',
                    dataPenanggung::json->>'pj_jenis_identitas',
                    dataPenanggung::json->>'pj_no_identitas',
                    dataPenanggung::json->>'pj_hubungan',
                    dataPenanggung::json->>'pj_nama',
                    dataPenanggung::json->>'pj_tempat_lahir',
                    (dataPenanggung::json->>'pj_tanggal_lahir')::DATE,
                    dataPenanggung::json->>'pj_jk',
                    dataPenanggung::json->>'pj_alamat',
                    dataPenanggung::json->>'pj_no_telepon',
                    dataPenanggung::json->>'pj_no_telepon',
                    NEW.pasien_id,
                    NEW.created_by,
                                        (dataPenanggung::json->>'pj_namadepan')::INTEGER,
                                        (dataPenanggung::json->>'pj_propinsi_id')::INTEGER,
                                        (dataPenanggung::json->>'pj_kabupaten_id')::INTEGER,
                                        (dataPenanggung::json->>'pj_kecamatan_id')::INTEGER,
                                        (dataPenanggung::json->>'pj_kelurahan_id')::INTEGER,
                                        (dataPenanggung::json->>'pj_pekerjaan_id')::INT,
--                                      (CASE
--                             dataPenanggung::json->>'pj_pekerjaan_id'
--                             WHEN NULL 
--                             THEN NULL 
--                             ELSE (dataPenanggung::json->>'pj_pekerjaan_id')::INTEGER END),
                                        dataPenanggung::json->>'pj_rt',
                                        dataPenanggung::json->>'pj_rw'
                ) RETURNING penanggungjawab_id INTO penanggungId;
                                NEW.penanggungjawab_id = penanggungId;
         END IF;
                 
                 -- Kebutuhan Santo Yusup penanggungbiaya_t
                 IF (dataPenanggungBiaya::json->>'carabayar_id' IS NOT NULL) THEN
                INSERT INTO penanggungbiaya_t (
                   -- penanggungbiaya_id,
                    pasien_id,
                    carabayar_id,
                    penanggungbiaya_nama,
                    namabagian,
                    noindukkaryawan,
                    jpkm,
                    instansi
                ) VALUES (
                    --dataPenanggungBiaya::json->>'penanggungbiaya_id',
                    --dataPenanggungBiaya::json->>'pasien_id',
                                        --NEW.penanggungbiaya_id,
                                        NEW.pasien_id,
                                        --(dataAntrian::json->>'ruangan_id')::INTEGER,
                    (dataPenanggungBiaya::json->>'carabayar_id')::INTEGER,
                    dataPenanggungBiaya::json->>'penanggungbiaya_nama',
                    dataPenanggungBiaya::json->>'namabagian',
                    dataPenanggungBiaya::json->>'noindukkaryawan',
                    dataPenanggungBiaya::json->>'jpkm',
                    dataPenanggungBiaya::json->>'instansi'
                ) RETURNING penanggungbiaya_id INTO penanggungbiayaId;
                                NEW.penanggungbiaya_id = penanggungbiayaId;
--                              ELSE 
--                         UPDATE penanggungbiaya_t SET 
--                             penanggungbiaya_nama = dataPenanggungBiaya::json->>'penanggungbiaya_nama',
--                             namabagian = dataPenanggungBiaya::json->>'namabagian',
--                             noindukkaryawan = dataPenanggungBiaya::json->>'noindukkaryawan',
--                             jpkm = dataPenanggungBiaya::json->>'jpkm',
--                                                      instansi = dataPenanggungBiaya::json->>'instansi'
--                         WHERE penanggungbiaya_id = idPenanggungBiaya;           
--                         
--                         NEW.penanggungbiaya_id = idPenanggungBiaya;
--                 END IF;
         END IF;
                 
                 -- Kebutuhan Santo Yusup keluargapasien_t
                 IF (dataKeluargaPasien::json->>'keluarga_nama' IS NOT NULL) THEN
                INSERT INTO keluargapasien_t (
                                        keluarga_nama,
                                        keluarga_jk,
                                        keluarga_hubungan,
                                        keluarga_alamat,
                                        keluarga_no_telepon,
                                        keluarga_namadepan,
                                        keluarga_propinsi_id,
                                        keluarga_kabupaten_id,
                                        keluarga_kecamatan_id,
                                        keluarga_kelurahan_id,
                                        keluarga_pekerjaan_id,
                                        keluarga_rt,
                                        keluarga_rw,
                    pasien_id
                ) VALUES (
                    dataKeluargaPasien::json->>'keluarga_nama',
                    dataKeluargaPasien::json->>'keluarga_jk',
                    dataKeluargaPasien::json->>'keluarga_hubungan',
                                        dataKeluargaPasien::json->>'keluarga_alamat',
                                        dataKeluargaPasien::json->>'keluarga_no_telepon',
                                        dataKeluargaPasien::json->>'keluarga_namadepan',
                                        (dataKeluargaPasien::json->>'keluarga_propinsi_id')::INTEGER,
                                        (dataKeluargaPasien::json->>'keluarga_kabupaten_id')::INTEGER,
                                        (dataKeluargaPasien::json->>'keluarga_kecamatan_id')::INTEGER,
                                        (dataKeluargaPasien::json->>'keluarga_kelurahan_id')::INTEGER,
                                        (dataKeluargaPasien::json->>'keluarga_pekerjaan_id')::INTEGER,
                    dataKeluargaPasien::json->>'keluarga_rt',
                    dataKeluargaPasien::json->>'keluarga_rw',
                                        NEW.pasien_id
                ) ;
--                              RETURNING keluargapasien_id INTO keluargapasienId;
--                                 NEW.keluargapasien_id = keluargapasienId;
                    END IF;
                    
                    -- MultiPayer 1--
--                              SELECT pendaftaran_t.is_multipayer INTO isMultiPayer
--                                      FROM pendaftaran_t
--                                      LIMIT 1; 
                                        
                          IF (dataAdditionalPayer::json->>'add_nokartuasuransi_1' IS NOT NULL) THEN
                noAsuransi1 := dataAdditionalPayer::json->>'add_nokartuasuransi_1';
                SELECT 
                    asuransipasien_id,
                    pasien_id
                INTO
                    idAsuransi1,
                    idPasienAsuransi1
                FROM asuransipasien_m
                WHERE nokartuasuransi = noAsuransi1
                AND penjamin_id = NEW.penjamin_id
                AND pasien_id = NEW.pasien_id
                AND carabayar_id = NEW.carabayar_id;
                                
                IF (idAsuransi1 IS NULL) THEN
                    INSERT INTO asuransipasien_m (
                        kelastanggunganasuransi_id,
                        namapemilikasuransi,
                        namaperusahaan,
                        nokartuasuransi,
                        nomorpokokperusahaan,
                        status_konfirmasi,
                        tgl_konfirmasi,
                        created_by,
                        pasien_id,
                        penjamin_id,
                        carabayar_id
                    ) VALUES (
                        (CASE dataAdditionalPayer::json->>'add_kelastanggungan_id_1'
                                WHEN '' THEN
                                    NULL
                                ELSE
                                    (dataAdditionalPayer::json->>'add_kelastanggungan_id_1')::INTEGER
                        END),
                        dataAdditionalPayer::json->>'add_namapemilikasuransi_1',
                        dataAdditionalPayer::json->>'add_namaperusahaan_1',
                        dataAdditionalPayer::json->>'add_nokartuasuransi_1',
                        dataAdditionalPayer::json->>'add_nomorpokokperusahaan_1',
                        dataAdditionalPayer::json->>'add_status_konfirmasi_1',
                        (dataAdditionalPayer::json->>'add_tgl_konfirmasi_1')::DATE,
                        NEW.created_by,
                        NEW.pasien_id,
                        NEW.penjamin_id,
                        NEW.carabayar_id
                    ) RETURNING asuransipasien_id INTO idAsuransi1;
                    
                    NEW.asuransipasien_id = idAsuransi1;
                ELSE 
                        UPDATE asuransipasien_m SET 
                            kelastanggunganasuransi_id = (CASE dataAdditionalPayer::json->>'add_kelastanggungan_id_1'
                                WHEN '' THEN
                                    NULL
                                ELSE
                                    (dataAdditionalPayer::json->>'add_kelastanggungan_id_1')::INTEGER
                            END), 
                            namapemilikasuransi = dataAdditionalPayer::json->>'add_namapemilikasuransi_1',
                            namaperusahaan = dataAdditionalPayer::json->>'add_namaperusahaan_1',
                            nomorpokokperusahaan = dataAdditionalPayer::json->>'add_nomorpokokperusahaan_1',
                            status_konfirmasi = dataAdditionalPayer::json->>'add_status_konfirmasi_1',
                            tgl_konfirmasi = (dataAdditionalPayer::json->>'add_tgl_konfirmasi_1')::DATE,
                            last_modified_by = NEW.created_by
                        WHERE asuransipasien_id = idAsuransi1;           
                        
                        NEW.asuransipasien_id = idAsuransi1;
                END IF;
         END IF;
                 
                                -- MultiPayer 2 --
--                              SELECT pendaftaran_t.is_multipayer INTO isMultiPayer
--                                      FROM pendaftaran_t
--                                      LIMIT 1; 
                                        
                          IF (dataAdditionalPayer::json->>'add_nokartuasuransi_2' IS NOT NULL) THEN
                noAsuransi2 := dataAdditionalPayer::json->>'add_nokartuasuransi_2';
                SELECT 
                    asuransipasien_id,
                    pasien_id
                INTO
                    idAsuransi2,
                    idPasienAsuransi2
                FROM asuransipasien_m
                WHERE nokartuasuransi = noAsuransi2
                AND penjamin_id = NEW.penjamin_id
                AND pasien_id = NEW.pasien_id
                AND carabayar_id = NEW.carabayar_id;
                                
                IF (idAsuransi2 IS NULL) THEN
                    INSERT INTO asuransipasien_m (
                        kelastanggunganasuransi_id,
                        namapemilikasuransi,
                        namaperusahaan,
                        nokartuasuransi,
                        nomorpokokperusahaan,
                        status_konfirmasi,
                        tgl_konfirmasi,
                        created_by,
                        pasien_id,
                        penjamin_id,
                        carabayar_id
                    ) VALUES (
                        (CASE dataAdditionalPayer::json->>'add_kelastanggungan_id_2'
                                WHEN '' THEN
                                    NULL
                                ELSE
                                    (dataAdditionalPayer::json->>'add_kelastanggungan_id_2')::INTEGER
                        END),
                        dataAdditionalPayer::json->>'add_namapemilikasuransi_2',
                        dataAdditionalPayer::json->>'add_namaperusahaan_2',
                        dataAdditionalPayer::json->>'add_nokartuasuransi_2',
                        dataAdditionalPayer::json->>'add_nomorpokokperusahaan_2',
                        dataAdditionalPayer::json->>'add_status_konfirmasi_2',
                        (dataAdditionalPayer::json->>'add_tgl_konfirmasi_2')::DATE,
                        NEW.created_by,
                        NEW.pasien_id,
                        NEW.penjamin_id,
                        NEW.carabayar_id
                    ) RETURNING asuransipasien_id INTO idAsuransi2;
                    
                    NEW.asuransipasien_id = idAsuransi2;
                ELSE 
                        UPDATE asuransipasien_m SET 
                            kelastanggunganasuransi_id = (CASE dataAdditionalPayer::json->>'add_kelastanggungan_id_2'
                                WHEN '' THEN
                                    NULL
                                ELSE
                                    (dataAdditionalPayer::json->>'add_kelastanggungan_id_2')::INTEGER
                            END), 
                            namapemilikasuransi = dataAdditionalPayer::json->>'add_namapemilikasuransi_2',
                            namaperusahaan = dataAdditionalPayer::json->>'add_namaperusahaan_2',
                            nomorpokokperusahaan = dataAdditionalPayer::json->>'add_nomorpokokperusahaan_2',
                            status_konfirmasi = dataAdditionalPayer::json->>'add_status_konfirmasi_2',
                            tgl_konfirmasi = (dataAdditionalPayer::json->>'add_tgl_konfirmasi_2')::DATE,
                            last_modified_by = NEW.created_by
                        WHERE asuransipasien_id = idAsuransi2;           
                        
                        NEW.asuransipasien_id = idAsuransi2;
                END IF;
         END IF;
                    
                                -- Multi Payer 1
                 IF (dataAdditionalPayer::json->>'add_carabayar_id_1' IS NOT NULL) THEN
                INSERT INTO pendaftaran_multipayer_t (
                                        asuransipasien_id,
                                        pendaftaran_id,
                                        pasien_id,
                                        penjamin_id,
                                        carabayar_id
                ) VALUES (
                                        idAsuransi1,
                                        NEW.pendaftaran_id,
                                        NEW.pasien_id,
                                        --NEW.penjamin_id,
                                        (dataAdditionalPayer::json->>'add_penjamin_id_1')::INTEGER,
                                        --NEW.carabayar_id
                                        (dataAdditionalPayer::json->>'add_carabayar_id_1')::INTEGER
                ) ;
                    END IF;
                    
                    -- Multi Payer 2
                 IF (dataAdditionalPayer::json->>'add_carabayar_id_2' IS NOT NULL) THEN
                INSERT INTO pendaftaran_multipayer_t (
                                        asuransipasien_id,
                                        pendaftaran_id,
                                        pasien_id,
                                        penjamin_id,
                                        carabayar_id
                ) VALUES (
                                        idAsuransi2,
                                        NEW.pendaftaran_id,
                                        NEW.pasien_id,
                                        --NEW.penjamin_id,
                                        (dataAdditionalPayer::json->>'add_penjamin_id_2')::INTEGER,
                                        --NEW.carabayar_id
                                        (dataAdditionalPayer::json->>'add_carabayar_id_2')::INTEGER
                ) ;
                    END IF;
         
         
                 -- Order MCU
                     IF (dataOrderMcu::json->>'penunjang' IS NOT NULL) THEN
                            IF (json_array_length((dataOrderMcu::json->>'penunjang')::json) > 0) THEN
                                    INSERT INTO pasienmasukpenunjang_t (
                                                                    kelaspelayanan_id,
                                                                    jeniskasuspenyakit_id,
                                                                    pasienadmisi_id,
                                                                    pegawai_id,
                                                                    ruangan_id,
                                                                    pasien_id,
                                                                    pendaftaran_id,
                                                                    ruanganasal_id,
                                                                    tglmasukpenunjang,
                                                                    kunjungan,
                                                                    status_periksa,
                                                                    is_bayar,
                                                                    instalasiasal_id,
                                                                    no_antrian,
                                                                    created_by
                             ) SELECT 
                                                                    kelaspelayanan_id,
                                                                    jeniskasuspenyakit_id,
                                                                    NEW.pasienadmisi_id,
                                                                    pegawai_id,
                                                                    ruangan_id,
                                                                    NEW.pasien_id,
                                                                    NEW.pendaftaran_id,
                                                                    ruanganasal_id,
                                                                    tglmasukpenunjang,
                                                                    kunjungan,
                                                                    status_periksa,
                                                                    is_bayar,
                                                                    instalasiasal_id,
                                                                    no_antrian,
                                                                    NEW.created_by as created_by
                                    FROM json_populate_recordset(null::pasienmasukpenunjang_t,(dataOrderMcu::json->>'penunjang')::json);
                            END IF;
                     END IF;
                     
                     -- Konsul Poli
                     IF (dataOrderMcu::json->>'konsul' IS NOT NULL) THEN
                            IF (json_array_length((dataOrderMcu::json->>'konsul')::json) > 0) THEN
                                    INSERT INTO konsulpoli_t (
                                                                    ruangan_id,
                                                                    pegawai_id,
                                                                    tindakanpelayanan_id,
                                                                    pendaftaran_id,
                                                                    pasien_id,
                                                                    tgl_konsulpoli,
                                                                    asalpoliklinikkonsul_id,
                                                                    status_periksa,
                                                                    no_antriankonsul,
                                                                    created_by,
                                                                                                                                        pendaftaranbaru_id,
                                                                                                                                        status_approve
                             ) SELECT 
                                                                    ruangan_id,
                                                                    pegawai_id,
                                                                    tindakanpelayanan_id,
                                                                    NEW.pendaftaran_id,
                                                                    NEW.pasien_id,
                                                                    tgl_konsulpoli,
                                                                    asalpoliklinikkonsul_id,
                                                                    status_periksa,
                                                                    no_antriankonsul,
                                                                    NEW.created_by as created_by,
                                                                                                                                        NEW.pendaftaran_id,
                                                                                                                                        565
                                    FROM json_populate_recordset(null::konsulpoli_t,(dataOrderMcu::json->>'konsul')::json);
                            END IF;
                     END IF;                
                     
         -- Set Antrian 
         IF (dataAntrian::json->>'jenisantrian_id' IS NOT NULL AND NEW.antrian_id IS NULL) THEN
                     --get konfigantrian
                    SELECT konfigantrian_id INTO v_konfigantrian
                    from konfigantrian_m
                    WHERE jenisantrian_id = (dataAntrian::json->>'jenisantrian_id')::INTEGER 
                    and konfigantrian_m.is_deleted=FALSE 
                    and konfigantrian_m.is_active=true
                    limit 1;
                                        
                                        SELECT konfigsystem_k.is_nourut INTO vnourut
                                        FROM konfigsystem_k
                                        LIMIT 1; 
                    
                    IF (vnourut=TRUE) THEN
                INSERT INTO antrian_t (
                                pasien_id,
                                ruangan_id,
                                carabayar_id,
                                pendaftaran_id,
                                tgl_antrian,
                                penjamin_id,
                                pegawai_id,
                                status_pasien,
                                jenisantrian_id,
                                is_active,
                                created_by,
                                konfigantrian_id,
                                                                no_antrian
             ) VALUES (
                                NEW.pasien_id,
                                (dataAntrian::json->>'ruangan_id')::INTEGER,
                                (dataAntrian::json->>'carabayar_id')::INTEGER,
                                NEW.pendaftaran_id,
                                (dataAntrian::json->>'tgl_antrian')::TIMESTAMP,
                                (dataAntrian::json->>'penjamin_id')::INTEGER,
                                (dataAntrian::json->>'pegawai_id')::INTEGER,
                                (dataAntrian::json->>'status_pasien')::INTEGER,
                                (dataAntrian::json->>'jenisantrian_id')::INTEGER,
                                FALSE,
                                NEW.created_by,
                                v_konfigantrian,
                                                                (dataAntrian::json->>'no_antrian')::VARCHAR
             ) RETURNING antrian_id INTO antrianId;
                         ELSE
                         INSERT INTO antrian_t (
                                pasien_id,
                                ruangan_id,
                                carabayar_id,
                                pendaftaran_id,
                                tgl_antrian,
                                penjamin_id,
                                pegawai_id,
                                status_pasien,
                                jenisantrian_id,
                                is_active,
                                created_by,
                                konfigantrian_id
             ) VALUES (
                                NEW.pasien_id,
                                (dataAntrian::json->>'ruangan_id')::INTEGER,
                                (dataAntrian::json->>'carabayar_id')::INTEGER,
                                NEW.pendaftaran_id,
                                (dataAntrian::json->>'tgl_antrian')::TIMESTAMP,
                                (dataAntrian::json->>'penjamin_id')::INTEGER,
                                (dataAntrian::json->>'pegawai_id')::INTEGER,
                                (dataAntrian::json->>'status_pasien')::INTEGER,
                                (dataAntrian::json->>'jenisantrian_id')::INTEGER,
                                FALSE,
                                NEW.created_by,
                                v_konfigantrian
             ) RETURNING antrian_id INTO antrianId;
                         END IF;
                         
                NEW.antrian_id = antrianId;
                
                --- Ini Kondisi Penunjang GET nomor antrian untuk pasien masuk penunjang
                IF (NEW.carabayar_id != 5 AND countPenunjang > 0) THEN
                        SELECT 
                            no_antrian
                        INTO 
                            noAntrian
                        FROM antrian_t WHERE antrian_id = antrianId;
                END IF;
         ELSE
                -- Update Antrian Pendaftaran
                UPDATE antrian_t SET 
                    pendaftaran_id = NEW.pendaftaran_id, 
                    pasien_id = NEW.pasien_id
                WHERE antrian_id = NEW.antrian_id;
                                

                
                IF (countPenunjang > 0) THEN
                        IF (NEW.carabayar_id != 5) THEN
                            SELECT 
                                no_antrian
                            INTO 
                                noAntrian
                            FROM antrian_t WHERE antrian_id = NEW.antrian_id;
                        END IF;
                ELSE
                    -- Validasi Untuk Non Penunjang 
                    isActive := false;
                    IF (NEW.carabayar_id != 5 OR countTagihan <= 0) THEN
                        isActive := true;
                    END IF;
                                        
                    -- Update Pendaftan Poli
                                        
                                        SELECT konfigsystem_k.is_nourut INTO vnourut
                                        FROM konfigsystem_k
                                        LIMIT 1; 
                    
                    IF (vnourut=TRUE) THEN
                    UPDATE antrian_t SET 
                        pendaftaran_id = NEW.pendaftaran_id, 
                        pasien_id = NEW.pasien_id, 
                        ruangan_id = NEW.ruangan_id,
                        carabayar_id = NEW.carabayar_id,
                        is_active = isActive,
                                        no_antrian = (dataAntrian::json->>'no_antrian')::VARCHAR
                    WHERE antrianasal_id = NEW.antrian_id; 
                    ELSE
                                        UPDATE antrian_t SET 
                        pendaftaran_id = NEW.pendaftaran_id, 
                        pasien_id = NEW.pasien_id, 
                        ruangan_id = NEW.ruangan_id,
                        carabayar_id = NEW.carabayar_id,
                        is_active = isActive
                    WHERE antrianasal_id = NEW.antrian_id; 
                                        END IF;
                END IF;
         END IF;
         
         -- Set Rujukan Jika Ada
         IF (dataRujukan::json->>'rujukandari_id' IS NOT NULL) THEN
                INSERT INTO rujukan_t (
                                asalrujukan_id,
                                rujukandari_id,
                                diagnosa_id,
                                no_rujukan,
                                nama_perujuk,
                                tanggal_rujukan,
                                created_by
             ) VALUES (
                                (dataRujukan::json->>'asalrujukan_id')::INTEGER,
                                (dataRujukan::json->>'rujukandari_id')::INTEGER,
                                (CASE
                                    dataRujukan::json->>'diagnosa_id'
                                WHEN NULL 
                                THEN NULL 
                                ELSE (dataPasien::json->>'diagnosa_id')::INTEGER END),
                                dataRujukan::json->>'no_rujukan',
                                dataRujukan::json->>'nama_perujuk',
                                (dataRujukan::json->>'tanggal_rujukan')::TIMESTAMP,
                                NEW.created_by
             ) RETURNING rujukan_id INTO rujukanId;
                NEW.rujukan_id = rujukanId;
         END IF;
         
    --- Kondisi Pendaftaran Penunjang
        IF (countPenunjang > 0) then
                INSERT INTO pasienmasukpenunjang_t (
                        kelaspelayanan_id,
                        jeniskasuspenyakit_id,
                        pegawai_id,
                        ruangan_id,
                        pasien_id,
                        pendaftaran_id,
                        tglmasukpenunjang,
                        no_antrian,
                        status_periksa,
                        ruanganasal_id,
                        instalasiasal_id,
                        created_by,
                                                pasienadmisi_id
                ) VALUES (
                        NEW.kelaspelayanan_id,
                        NEW.jeniskasuspenyakit_id,
                        NEW.pegawai_id,
                        NEW.ruangan_id,
                        NEW.pasien_id,
                        NEW.pendaftaran_id,
                        NEW.tgl_pendaftaran,
                        noAntrian,
                        CASE new.instalasi_id::int4
                            WHEN 12 THEN 488
                            ELSE 477
                        end,
--                        477,
                        NEW.ruangan_id,
                        new.instalasi_id,
                        NEW.created_by,
                                                NEW.pasienadmisi_id
                ) RETURNING pasienmasukpenunjang_id INTO idPenunjang;
                
--                              INSERT INTO tindakanpelayanan_t (
--                                 pasienmasukpenunjang_id,
--                                 kelaspelayanan_id,
--                                 pasien_id,
--                                 instalasi_id,
--                                 daftartindakan_id,
--                                 tipepaket_id,
--                                 carabayar_id,
--                                 pendaftaran_id,
--                                 jeniskasuspenyakit_id,
--                                 ruangan_id,
--                                 penjamin_id,
--                                 tgl_tindakan,
--                                 dokterpenanggungjawab_id,
--                                 tarif_satuan,
--                                 qty_tindakan,
--                                 tarif_tindakan,
--                                 tarifcyto_tindakan,
--                                 cyto_tindakan,
--                                 discount_tindakan,
--                                 additional_data,
--                                 created_by
--              ) SELECT 
--                                 idPenunjang as pasienmasukpenunjang_id,
--                                 kelaspelayanan_id,
--                                 NEW.pasien_id as pasien_id,
--                                 instalasi_id,
--                                 daftartindakan_id,
--                                 tipepaket_id,
--                                 carabayar_id,
--                                 NEW.pendaftaran_id as pendaftaran_id,
--                                 jeniskasuspenyakit_id,
--                                 ruangan_id,
--                                 penjamin_id,
--                                 tgl_tindakan,
--                                 dokterpenanggungjawab_id,
--                                 tarif_satuan,
--                                 qty_tindakan,
--                                 tarif_tindakan,
--                                 tarifcyto_tindakan,
--                                 cyto_tindakan,
--                                 discount_tindakan,
--                                 additional_data,
--                                 NEW.created_by as created_by
--                 FROM json_populate_recordset(null::tindakanpelayanan_t,vPenunjang::json);
        END IF;
    
        -- Pendafatran Online
        IF (paramJson::json->>'pendaftaranol_id' IS NOT NULL) THEN
            UPDATE pendaftaranol_t
                SET pendaftaran_id = NEW.pendaftaran_id,
                status_daftar_ol = 565
            WHERE pendaftaranol_id = (paramJson::json->>'pendaftaranol_id')::INTEGER;           
        END IF;
    
    -- Insert Admisi
     IF (vadmisi::json->>'ruangan_id' IS NOT NULL) THEN
            INSERT INTO pasienadmisi_t (
                            pendaftaran_id,
                            carabayar_id,
                            penjamin_id,
                            ruangan_id,
                            pasien_id,
                            kamarruangan_id,
                            kamartempattidur_id,
                            kelaspelayanan_id,
                            pegawai_id,
                            tgl_admisi,
                            tgl_pendaftaran,
                            kunjungan,
                            bpjs_id,
                            status_ranap,
                            status_verifikasi,
                            is_skd,
                            is_pasientitipan,
                            created_by,
                            is_aps,
                            asuransipasien_id,
                                                        kamar_titipan_id,
                                                        kelas_ditagihkan_id,
                                                        ruangan_titipan_id,
                                                        limit_tagihan,
                                                        hakkelas_id,
                                                        kelaspermintaan_id,
                                                        dokterpengirim_id,
                                                        dokterkonsul_id
                    
         ) VALUES (
                            NEW.pendaftaran_id,
                            vcarabayar_id,
                            vpenjamin_id,
                            (vadmisi::json->>'ruangan_id')::INTEGER,
                            NEW.pasien_id,
                            (vadmisi::json->>'kamarruangan_id')::INTEGER,
                            (vadmisi::json->>'kamartempattidur_id')::INTEGER,
                            (vadmisi::json->>'kelaspelayanan_id')::INTEGER,
                            (vadmisi::json->>'pegawai_id')::INTEGER,
                            (vadmisi::json->>'tgl_admisi')::TIMESTAMP,
                            (vadmisi::json->>'tgl_pendaftaran')::TIMESTAMP,
                            (vadmisi::json->>'kunjungan')::INTEGER,
                            (vadmisi::json->>'bpjs_id')::INTEGER,
                            (vadmisi::json->>'status_ranap')::INTEGER,
                            549,
                            (vadmisi::json->>'is_skd')::BOOL,
                            (vadmisi::json->>'is_pasientitipan')::BOOL,
                            NEW.created_by,
                            (vadmisi::json->>'is_aps')::BOOL,
                            idAsuransi,
                                                        (vadmisi::json->>'kamar_titipan_id')::INTEGER,
                                                         (vadmisi::json->>'kelas_ditagihkan_id')::INTEGER,
                                                         (vadmisi::json->>'ruangan_titipan_id')::INTEGER,
                                                         (vadmisi::json->>'limit_tagihan')::float8,
                                                         (vadmisi::json->>'hakkelas_id')::INTEGER,
                                                         (vadmisi::json->>'kelaspermintaan_id')::INTEGER,
                             (vadmisi::json->>'dokterpengirim_id')::INTEGER,
                                                         (vadmisi::json->>'dokterkonsul_id')
                            
                    
                            
         ) RETURNING pasienadmisi_id INTO vpasienadmisi_id;
            
            NEW.pasienadmisi_id = vpasienadmisi_id;
        

            UPDATE pendaftaran_t
            SET is_ranap = TRUE
            WHERE pendaftaran_id = vpendaftaranasal_id;
             -- Insert Masuk Kamar
            IF (vadmisi::json->>'ruangan_id' IS NOT NULL) THEN
                INSERT INTO masukkamar_t (
                                pasienadmisi_id,
                                carabayar_id,
                                penjamin_id,
                                ruangan_id,
                                pegawai_id,
                                kelaspelayanan_id,
                                kamartempattidur_id,
                                kamarruangan_id,
                                tgl_masukkamar,
                                jam_masukkamar,
                                created_by
             ) VALUES (
                                vpasienadmisi_id,
                                vcarabayar_id,
                                vpenjamin_id,
                                (vmasukkamar::json->>'ruangan_id')::INTEGER,
                                (vmasukkamar::json->>'pegawai_id')::INTEGER,
                                (vmasukkamar::json->>'kelaspelayanan_id')::INTEGER,
                                (vmasukkamar::json->>'kamartempattidur_id')::INTEGER,
                                (vmasukkamar::json->>'kamarruangan_id')::INTEGER,
                                (vmasukkamar::json->>'tgl_masukkamar')::DATE,
                                (vmasukkamar::json->>'jam_masukkamar')::TIME,
                                NEW.created_by
             ) ;
            END IF;
             
            SELECT 
                CASE pasien_m.jeniskelamin::int4
                    WHEN 15 THEN 4
                                        WHEN 16 THEN 3
                                    ELSE 6
                END INTO vkettempattidur_id
            FROM pasien_m
            WHERE pasien_id = (vadmisi::json->>'pasien_id')::INTEGER;

            UPDATE kamartempattidur_m
            SET status_isi = TRUE,
                        kettempattidur_id = vkettempattidur_id
            WHERE kamartempattidur_id = (vadmisi::json->>'kamartempattidur_id')::INTEGER;

            IF(COALESCE(vkelahiran_id,0) <> 0)
            THEN
                UPDATE kelahiranbayi_t
                SET pendaftaranbaru_id = NEW.pendaftaran_id,
                        last_modified_by = NEW.created_by,
                        last_modified_date = CURRENT_TIMESTAMP
                WHERE kelahiranbayi_id = vkelahiran_id;
            END IF;
            
     END IF;
        
        
         -- Tagihan Karcis
--          IF (countTagihan > 0) THEN
--                 INSERT INTO tindakanpelayanan_t (
--                                 kelaspelayanan_id,
--                                 pasien_id,
--                                 instalasi_id,
--                                 daftartindakan_id,
--                                                                 tipepaket_id,
--                                 carabayar_id,
--                                 pendaftaran_id,
--                                 jeniskasuspenyakit_id,
--                                 ruangan_id,
--                                 penjamin_id,
--                                 tgl_tindakan,
--                                 dokterpenanggungjawab_id,
--                                 tarif_satuan,
--                                 qty_tindakan,
--                                 tarif_tindakan,
--                                 tarifcyto_tindakan,
--                                 cyto_tindakan,
--                                 discount_tindakan,
--                                 pasienadmisi_id,
--                                 additional_data,
--                                 created_by
--              ) SELECT 
--                                 kelaspelayanan_id,
--                                 NEW.pasien_id as pasien_id,
--                                 instalasi_id,
--                                 daftartindakan_id,
--                                                                 tipepaket_id,
--                                 carabayar_id,
--                                 NEW.pendaftaran_id as pendaftaran_id,
--                                 jeniskasuspenyakit_id,
--                                 ruangan_id,
--                                 penjamin_id,
--                                 tgl_tindakan,
--                                 dokterpenanggungjawab_id,
--                                 tarif_satuan,
--                                 qty_tindakan,
--                                 tarif_tindakan,
--                                 tarifcyto_tindakan,
--                                 cyto_tindakan,
--                                 discount_tindakan,
--                 NEW.pasienadmisi_id,
--                                 additional_data,
--                                 NEW.created_by as created_by
--                 FROM json_populate_recordset(null::tindakanpelayanan_t,dataKarcis::json);
--          END IF;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
            ");

        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"pendaftaran_inap\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE

    vadmisi VARCHAR;
    vmasukkamar VARCHAR;
    paramJson VARCHAR;
    dataAsuransi VARCHAR;
    vpasienadmisi_id int4;
    vuser_id int4;
    vpendaftaran_id int4;
    vcarabayar_id int4;
    vpenjamin_id int4;
    vkettempattidur_id int4; 
    vkelahiran_id int4;
    vpendaftaranasal_id int4;
    noAsuransi VARCHAR;
    idAsuransi INTEGER;
    idPasienAsuransi INTEGER;
    -- Count Tagihan
    countTagihan INTEGER;
    dataKarcis VARCHAR;
    instalasiId INTEGER;
        -- Kebutuhan SY Penanggung Biaya
        dataPenanggungBiaya VARCHAR;
        -- Multi Payer US670
        dataAdditionalPayer VARCHAR;
        isMultiPayer BOOLEAN;
        
        noAsuransi1 VARCHAR;
    idAsuransi1 INTEGER;
    idPasienAsuransi1 INTEGER;
        
        noAsuransi2 VARCHAR;
    idAsuransi2 INTEGER;
    idPasienAsuransi2 INTEGER;
BEGIN
    paramJson := NEW.additional_data;
    vuser_id := NEW.last_modified_by;
    vpendaftaran_id := NEW.pendaftaran_id;
    vcarabayar_id := NEW.carabayar_id;
    vpenjamin_id := NEW.penjamin_id;
    instalasiId := NEW.instalasi_id;
  dataKarcis := paramJson::json->>'tarif';
    countTagihan := json_array_length(dataKarcis::json);
    vadmisi := paramJson::json->>'pasien_admisi';
    vmasukkamar := paramJson::json->>'masuk_kamar';
    dataAsuransi := paramJson::json->>'asuransi';
    vkelahiran_id := (paramJson::json->>'kelahiran_id')::int4;
    vpendaftaranasal_id := (paramJson::json->>'pendaftaranasal_id')::int4;
        dataPenanggungBiaya := paramJson::json->>'penanggungbiaya';
        dataAdditionalPayer := paramJson::json->>'additional_payer';
    
    IF (dataAsuransi::json->>'nokartuasuransi' IS NOT NULL) THEN
                noAsuransi := dataAsuransi::json->>'nokartuasuransi';
                SELECT 
                    asuransipasien_id,
                    pasien_id
                INTO
                    idAsuransi,
                    idPasienAsuransi
                FROM asuransipasien_m
                WHERE nokartuasuransi = noAsuransi
                AND penjamin_id = NEW.penjamin_id
                AND pasien_id = NEW.pasien_id
                AND carabayar_id = NEW.carabayar_id;
                
                IF (idAsuransi IS NULL) THEN
                    INSERT INTO asuransipasien_m (
                        kelastanggunganasuransi_id,
                        namapemilikasuransi,
                        namaperusahaan,
                        nokartuasuransi,
                        nomorpokokperusahaan,
                        status_konfirmasi,
                        tgl_konfirmasi,
                        created_by,
                        pasien_id,
                        penjamin_id,
                        carabayar_id,
                                                masaberlakukartu,
                                                nama_asuransi
                    ) VALUES (
                        (CASE dataAsuransi::json->>'kelastanggungan_id'
                                WHEN '' THEN
                                    NULL
                                ELSE
                                    (dataAsuransi::json->>'kelastanggungan_id')::INTEGER
                        END),
                        dataAsuransi::json->>'namapemilikasuransi',
                        dataAsuransi::json->>'namaperusahaan',
                        dataAsuransi::json->>'nokartuasuransi',
                        dataAsuransi::json->>'nomorpokokperusahaan',
                        dataAsuransi::json->>'status_konfirmasi',
                        (dataAsuransi::json->>'tgl_konfirmasi')::TIMESTAMP,
                        NEW.created_by,
                        NEW.pasien_id,
                        NEW.penjamin_id,
                        NEW.carabayar_id,
                                                (dataAsuransi::json->>'masaberlakukartu')::DATE,
                                                dataAsuransi::json->>'nama_asuransi'
                    ) RETURNING asuransipasien_id INTO idAsuransi;
                    
                    NEW.asuransipasien_id = idAsuransi;
                ELSE 
                        UPDATE asuransipasien_m SET 
                            kelastanggunganasuransi_id = (CASE dataAsuransi::json->>'kelastanggungan_id'
                                WHEN '' THEN
                                    NULL
                                ELSE
                                    (dataAsuransi::json->>'kelastanggungan_id')::INTEGER
                            END), 
                            namapemilikasuransi = dataAsuransi::json->>'namapemilikasuransi',
                            namaperusahaan = dataAsuransi::json->>'namaperusahaan',
                            nomorpokokperusahaan = dataAsuransi::json->>'nomorpokokperusahaan',
                            status_konfirmasi = dataAsuransi::json->>'status_konfirmasi',
                            tgl_konfirmasi = (dataAsuransi::json->>'tgl_konfirmasi')::TIMESTAMP,
                            last_modified_by = NEW.created_by
                        WHERE asuransipasien_id = idAsuransi;           
                        
                END IF;
         END IF;
 
 
     -- Insert Admisi
     IF (vadmisi::json->>'ruangan_id' IS NOT NULL) THEN
            instalasiId := 3;
            INSERT INTO pasienadmisi_t (
                            pendaftaran_id,
                            carabayar_id,
                            penjamin_id,
                            ruangan_id,
                            pasien_id,
                            kamarruangan_id,
                            kamartempattidur_id,
                            kelaspelayanan_id,
                            pegawai_id,
                            tgl_admisi,
                            tgl_pendaftaran,
                            kunjungan,
                            bpjs_id,
                            status_ranap,
                            status_verifikasi,
                            is_skd,
                            is_pasientitipan,
                            is_aps,
                            created_by,
                            asuransipasien_id,
                                                        kamar_titipan_id,
                                                        kelas_ditagihkan_id,
                                                        ruangan_titipan_id,
                                                        limit_tagihan
         ) VALUES (
                            vpendaftaran_id,
                            (vadmisi::json->>'carabayar_id')::INTEGER,
              (vadmisi::json->>'penjamin_id')::INTEGER,
                            (vadmisi::json->>'ruangan_id')::INTEGER,
                            (vadmisi::json->>'pasien_id')::INTEGER,
                            (vadmisi::json->>'kamarruangan_id')::INTEGER,
                            (vadmisi::json->>'kamartempattidur_id')::INTEGER,
                            (vadmisi::json->>'kelaspelayanan_id')::INTEGER,
                            (vadmisi::json->>'pegawai_id')::INTEGER,
                            (vadmisi::json->>'tgl_admisi')::TIMESTAMP,
                            (vadmisi::json->>'tgl_pendaftaran')::TIMESTAMP,
                            (vadmisi::json->>'kunjungan')::INTEGER,
                            (vadmisi::json->>'bpjs_id')::INTEGER,
                            (vadmisi::json->>'status_ranap')::INTEGER,
                            549,
                            (vadmisi::json->>'is_skd')::BOOL,
                            (vadmisi::json->>'is_pasientitipan')::BOOL,
                            (vadmisi::json->>'is_aps')::BOOL,
                            vuser_id,
                            idAsuransi,
                                                         (vadmisi::json->>'kamar_titipan_id')::INTEGER,
                                                         (vadmisi::json->>'kelas_ditagihkan_id')::INTEGER,
                                                         (vadmisi::json->>'ruangan_titipan_id')::INTEGER,
                                                         (vadmisi::json->>'limit_tagihan')::float8
                    
                            
         ) RETURNING pasienadmisi_id INTO vpasienadmisi_id;
    
--          UPDATE pendaftaran_t
--          SET pasienadmisi_id = vpasienadmisi_id
--          WHERE pendaftaran_id = vpendaftaran_id;
            
            NEW.pasienadmisi_id = vpasienadmisi_id;
            NEW.is_ranap = true;
            
--          UPDATE pendaftaran_t
--          SET is_ranap = TRUE 
--          WHERE pendaftaran_id = vpendaftaranasal_id;
            
     END IF;

         -- Tagihan Karcis
--          IF (countTagihan > 0) THEN
--                 INSERT INTO tindakanpelayanan_t (
--                                 kelaspelayanan_id,
--                                 pasien_id,
--                                 instalasi_id,
--                                 daftartindakan_id,
--                                 carabayar_id,
--                                 pendaftaran_id,
--                                 jeniskasuspenyakit_id,
--                                 ruangan_id,
--                                 penjamin_id,
--                                 tgl_tindakan,
--                                 dokterpenanggungjawab_id,
--                                 tarif_satuan,
--                                 qty_tindakan,
--                                 tarif_tindakan,
--                                 tarifcyto_tindakan,
--                                 cyto_tindakan,
--                                 discount_tindakan,
--                                 pasienadmisi_id,
--                                 additional_data,
--                                 created_by
--              ) SELECT 
--                                 kelaspelayanan_id,
--                                 NEW.pasien_id as pasien_id,
--                                 instalasiId,
--                                 daftartindakan_id,
--                                 carabayar_id,
--                                 NEW.pendaftaran_id as pendaftaran_id,
--                                 jeniskasuspenyakit_id,
--                                 ruangan_id,
--                                 penjamin_id,
--                                 tgl_tindakan,
--                                 dokterpenanggungjawab_id,
--                                 tarif_satuan,
--                                 qty_tindakan,
--                                 tarif_tindakan,
--                                 tarifcyto_tindakan,
--                                 cyto_tindakan,
--                                 discount_tindakan,
--                 NEW.pasienadmisi_id,
--                                 additional_data,
--                                 NEW.created_by as created_by
--                 FROM json_populate_recordset(null::tindakanpelayanan_t,dataKarcis::json);
--          END IF;

            IF (dataPenanggungBiaya::json->>'carabayar_id' IS NOT NULL) THEN
--          IF (dataPenanggungBiaya::json->>'pasien_id' IS NULL) THEN
                INSERT INTO penanggungbiaya_t (
                   -- penanggungbiaya_id,
                    pasien_id,
                    carabayar_id,
                    penanggungbiaya_nama,
                    namabagian,
                    noindukkaryawan,
                    jpkm,
                    instansi
                ) VALUES (
                    --dataPenanggungBiaya::json->>'penanggungbiaya_id',
                    --dataPenanggungBiaya::json->>'pasien_id',
                                        --NEW.penanggungbiaya_id,
                                        NEW.pasien_id,
                    (dataPenanggungBiaya::json->>'carabayar_id')::INTEGER,
                    dataPenanggungBiaya::json->>'penanggungbiaya_nama',
                    dataPenanggungBiaya::json->>'namabagian',
                    dataPenanggungBiaya::json->>'noindukkaryawan',
                    dataPenanggungBiaya::json->>'jpkm',
                    dataPenanggungBiaya::json->>'instansi'
                ) ;
         END IF;
                 
                 -- Multi Payer US670
                 -- MultiPayer 1--
--                              SELECT pendaftaran_t.is_multipayer INTO isMultiPayer
--                                      FROM pendaftaran_t
--                                      LIMIT 1; 
                                        
                          IF (dataAdditionalPayer::json->>'add_nokartuasuransi_1' IS NOT NULL) THEN
                noAsuransi1 := dataAdditionalPayer::json->>'add_nokartuasuransi_1';
                SELECT 
                    asuransipasien_id,
                    pasien_id
                INTO
                    idAsuransi1,
                    idPasienAsuransi1
                FROM asuransipasien_m
                WHERE nokartuasuransi = noAsuransi1
                AND penjamin_id = NEW.penjamin_id
                AND pasien_id = NEW.pasien_id
                AND carabayar_id = NEW.carabayar_id;
                                
                IF (idAsuransi1 IS NULL) THEN
                    INSERT INTO asuransipasien_m (
                        kelastanggunganasuransi_id,
                        namapemilikasuransi,
                        namaperusahaan,
                        nokartuasuransi,
                        nomorpokokperusahaan,
                        status_konfirmasi,
                        tgl_konfirmasi,
                        created_by,
                        pasien_id,
                        penjamin_id,
                        carabayar_id
                    ) VALUES (
                        (CASE dataAdditionalPayer::json->>'add_kelastanggungan_id_1'
                                WHEN '' THEN
                                    NULL
                                ELSE
                                    (dataAdditionalPayer::json->>'add_kelastanggungan_id_1')::INTEGER
                        END),
                        dataAdditionalPayer::json->>'add_namapemilikasuransi_1',
                        dataAdditionalPayer::json->>'add_namaperusahaan_1',
                        dataAdditionalPayer::json->>'add_nokartuasuransi_1',
                        dataAdditionalPayer::json->>'add_nomorpokokperusahaan_1',
                        dataAdditionalPayer::json->>'add_status_konfirmasi_1',
                        (dataAdditionalPayer::json->>'add_tgl_konfirmasi_1')::DATE,
                        NEW.created_by,
                        NEW.pasien_id,
                        NEW.penjamin_id,
                        NEW.carabayar_id
                    ) RETURNING asuransipasien_id INTO idAsuransi1;
                    
                    NEW.asuransipasien_id = idAsuransi1;
                ELSE 
                        UPDATE asuransipasien_m SET 
                            kelastanggunganasuransi_id = (CASE dataAdditionalPayer::json->>'add_kelastanggungan_id_1'
                                WHEN '' THEN
                                    NULL
                                ELSE
                                    (dataAdditionalPayer::json->>'add_kelastanggungan_id_1')::INTEGER
                            END), 
                            namapemilikasuransi = dataAdditionalPayer::json->>'add_namapemilikasuransi_1',
                            namaperusahaan = dataAdditionalPayer::json->>'add_namaperusahaan_1',
                            nomorpokokperusahaan = dataAdditionalPayer::json->>'add_nomorpokokperusahaan_1',
                            status_konfirmasi = dataAdditionalPayer::json->>'add_status_konfirmasi_1',
                            tgl_konfirmasi = (dataAdditionalPayer::json->>'add_tgl_konfirmasi_1')::DATE,
                            last_modified_by = NEW.created_by
                        WHERE asuransipasien_id = idAsuransi1;           
                        
                        NEW.asuransipasien_id = idAsuransi1;
                END IF;
         END IF;
                 
                                -- MultiPayer 2 --
--                              SELECT pendaftaran_t.is_multipayer INTO isMultiPayer
--                                      FROM pendaftaran_t
--                                      LIMIT 1; 
                                        
                          IF (dataAdditionalPayer::json->>'add_nokartuasuransi_2' IS NOT NULL) THEN
                noAsuransi2 := dataAdditionalPayer::json->>'add_nokartuasuransi_2';
                SELECT 
                    asuransipasien_id,
                    pasien_id
                INTO
                    idAsuransi2,
                    idPasienAsuransi2
                FROM asuransipasien_m
                WHERE nokartuasuransi = noAsuransi2
                AND penjamin_id = NEW.penjamin_id
                AND pasien_id = NEW.pasien_id
                AND carabayar_id = NEW.carabayar_id;
                                
                IF (idAsuransi2 IS NULL) THEN
                    INSERT INTO asuransipasien_m (
                        kelastanggunganasuransi_id,
                        namapemilikasuransi,
                        namaperusahaan,
                        nokartuasuransi,
                        nomorpokokperusahaan,
                        status_konfirmasi,
                        tgl_konfirmasi,
                        created_by,
                        pasien_id,
                        penjamin_id,
                        carabayar_id
                    ) VALUES (
                        (CASE dataAdditionalPayer::json->>'add_kelastanggungan_id_2'
                                WHEN '' THEN
                                    NULL
                                ELSE
                                    (dataAdditionalPayer::json->>'add_kelastanggungan_id_2')::INTEGER
                        END),
                        dataAdditionalPayer::json->>'add_namapemilikasuransi_2',
                        dataAdditionalPayer::json->>'add_namaperusahaan_2',
                        dataAdditionalPayer::json->>'add_nokartuasuransi_2',
                        dataAdditionalPayer::json->>'add_nomorpokokperusahaan_2',
                        dataAdditionalPayer::json->>'add_status_konfirmasi_2',
                        (dataAdditionalPayer::json->>'add_tgl_konfirmasi_2')::DATE,
                        NEW.created_by,
                        NEW.pasien_id,
                        NEW.penjamin_id,
                        NEW.carabayar_id
                    ) RETURNING asuransipasien_id INTO idAsuransi2;
                    
                    NEW.asuransipasien_id = idAsuransi2;
                ELSE 
                        UPDATE asuransipasien_m SET 
                            kelastanggunganasuransi_id = (CASE dataAdditionalPayer::json->>'add_kelastanggungan_id_2'
                                WHEN '' THEN
                                    NULL
                                ELSE
                                    (dataAdditionalPayer::json->>'add_kelastanggungan_id_2')::INTEGER
                            END), 
                            namapemilikasuransi = dataAdditionalPayer::json->>'add_namapemilikasuransi_2',
                            namaperusahaan = dataAdditionalPayer::json->>'add_namaperusahaan_2',
                            nomorpokokperusahaan = dataAdditionalPayer::json->>'add_nomorpokokperusahaan_2',
                            status_konfirmasi = dataAdditionalPayer::json->>'add_status_konfirmasi_2',
                            tgl_konfirmasi = (dataAdditionalPayer::json->>'add_tgl_konfirmasi_2')::DATE,
                            last_modified_by = NEW.created_by
                        WHERE asuransipasien_id = idAsuransi2;           
                        
                        NEW.asuransipasien_id = idAsuransi2;
                END IF;
         END IF;
                 
                 -- Multi Payer 1
                 IF (dataAdditionalPayer::json->>'add_carabayar_id_1' IS NOT NULL) THEN
                INSERT INTO pendaftaran_multipayer_t (
                                        asuransipasien_id,
                                        pendaftaran_id,
                                        pasien_id,
                                        penjamin_id,
                                        carabayar_id
                ) VALUES (
                                        idAsuransi1,
                                        NEW.pendaftaran_id,
                                        NEW.pasien_id,
                                        --NEW.penjamin_id,
                                        (dataAdditionalPayer::json->>'add_penjamin_id_1')::INTEGER,
                                        --NEW.carabayar_id
                                        (dataAdditionalPayer::json->>'add_carabayar_id_1')::INTEGER
                ) ;
                    END IF;
                    
                    -- Multi Payer 2
                 IF (dataAdditionalPayer::json->>'add_carabayar_id_2' IS NOT NULL) THEN
                INSERT INTO pendaftaran_multipayer_t (
                                        asuransipasien_id,
                                        pendaftaran_id,
                                        pasien_id,
                                        penjamin_id,
                                        carabayar_id
                ) VALUES (
                                        idAsuransi2,
                                        NEW.pendaftaran_id,
                                        NEW.pasien_id,
                                        --NEW.penjamin_id,
                                        (dataAdditionalPayer::json->>'add_penjamin_id_2')::INTEGER,
                                        --NEW.carabayar_id
                                        (dataAdditionalPayer::json->>'add_carabayar_id_2')::INTEGER
                ) ;
                    END IF;

     -- Insert Masuk Kamar
     IF (vadmisi::json->>'ruangan_id' IS NOT NULL) THEN
            INSERT INTO masukkamar_t (
                            pasienadmisi_id,
                            carabayar_id,
                            penjamin_id,
                            ruangan_id,
                            pegawai_id,
                            kelaspelayanan_id,
                            kamartempattidur_id,
                            kamarruangan_id,
                            tgl_masukkamar,
                            jam_masukkamar,
                            created_by
         ) VALUES (
                            vpasienadmisi_id,
                            vcarabayar_id,
                            vpenjamin_id,
                            (vmasukkamar::json->>'ruangan_id')::INTEGER,
                            (vmasukkamar::json->>'pegawai_id')::INTEGER,
                            (vmasukkamar::json->>'kelaspelayanan_id')::INTEGER,
                            (vmasukkamar::json->>'kamartempattidur_id')::INTEGER,
                            (vmasukkamar::json->>'kamarruangan_id')::INTEGER,
                            (vmasukkamar::json->>'tgl_masukkamar')::DATE,
                            (vmasukkamar::json->>'jam_masukkamar')::TIME,
                            vuser_id
         ) ;
     
     SELECT 
            CASE pasien_m.jeniskelamin::int4
                WHEN 15 THEN 4
                                WHEN 16 THEN 3
                ELSE 6
            END INTO vkettempattidur_id
        FROM pasien_m
        WHERE pasien_id = (vadmisi::json->>'pasien_id')::INTEGER;

        UPDATE kamartempattidur_m
        SET status_isi = TRUE,
                    kettempattidur_id = vkettempattidur_id
        WHERE kamartempattidur_id = (vadmisi::json->>'kamartempattidur_id')::INTEGER;
        
        IF(COALESCE(vkelahiran_id,0) <> 0)
        THEN
            UPDATE kelahiranbayi_t
            SET pendaftaranbaru_id = vpendaftaran_id,
                    last_modified_by = vuser_id,
                    last_modified_date = CURRENT_TIMESTAMP
            WHERE kelahiranbayi_id = vkelahiran_id;
        END IF;
     END IF;
    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
            ");


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210725_131932_feature_US670_pendaftaran_multipayer_ranap cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210725_131932_feature_US670_pendaftaran_multipayer_ranap cannot be reverted.\n";

        return false;
    }
    */
}

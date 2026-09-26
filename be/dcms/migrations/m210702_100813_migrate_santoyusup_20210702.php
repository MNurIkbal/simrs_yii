<?php

use yii\db\Migration;

/**
 * Class m210702_100813_migrate_santoyusup_20210702
 */
class m210702_100813_migrate_santoyusup_20210702 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."keluargapasien_t" 
          ADD COLUMN IF NOT EXISTS "alamatdepan" varchar(20) COLLATE "pg_catalog"."default";');
        $this->execute('COMMENT ON COLUMN "public"."keluargapasien_t"."alamatdepan" IS \'Keperluan Santo Yusup\';');

        $this->execute('ALTER TABLE "public"."pasien_m" 
          ADD COLUMN IF NOT EXISTS "alamatdepan" varchar(20) COLLATE "pg_catalog"."default";');
        $this->execute('COMMENT ON COLUMN "public"."pasien_m"."alamatdepan" IS \'Keperluan Santo Yusup\';');

        $this->execute('ALTER TABLE "public"."pendaftaran_t" 
          ADD COLUMN IF NOT EXISTS "dokterpengganti_id" int4;');
        $this->execute('COMMENT ON COLUMN "public"."pendaftaran_t"."dokterpengganti_id" IS \'Keperluan Santo Yusup\';');

        $this->execute('ALTER TABLE "public"."pasienadmisi_t" 
          ADD COLUMN IF NOT EXISTS "prosedurmasuk_id" varchar(32) COLLATE "pg_catalog"."default",
          ADD COLUMN IF NOT EXISTS "diagnosa_awal" varchar(200) COLLATE "pg_catalog"."default";');
        $this->execute('COMMENT ON COLUMN "public"."pasienadmisi_t"."prosedurmasuk_id" IS \'Keperluan Santo Yusup\';');
        $this->execute('COMMENT ON COLUMN "public"."pasienadmisi_t"."diagnosa_awal" IS \'Keperluan Santo Yusup\';');


        $this->execute('CREATE TABLE IF NOT EXISTS "public"."ruangcarabayar_m" (
          "ruangcarabayar_id" serial8,
          "ruangcarabayar_nama" varchar(50) COLLATE "pg_catalog"."default" NOT NULL,
          "ruangcarabayar_namalainnya" varchar(50) COLLATE "pg_catalog"."default",
          "ruangcarabayar_kode" varchar(10) COLLATE "pg_catalog"."default",
          "additional_data" text COLLATE "pg_catalog"."default",
          "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
          "created_by" int4,
          "modified_count" int4,
          "last_modified_date" timestamp(6),
          "last_modified_by" int4,
          "is_deleted" bool NOT NULL DEFAULT false,
          "is_active" bool NOT NULL DEFAULT true,
          "deleted_date" timestamp(6),
          "deleted_by" int4,
          CONSTRAINT "ruangcarabayar_m_pkey" PRIMARY KEY ("ruangcarabayar_id")
          )
          ;');

        $this->execute('ALTER TABLE "public"."ruangcarabayar_m" 
          OWNER TO "postgres";');

        $this->execute('ALTER TABLE "public"."penanggungjawab_m" 
          ADD COLUMN IF NOT EXISTS "alamatdepan" varchar(20) COLLATE "pg_catalog"."default";');


        $this->execute('CREATE TABLE IF NOT EXISTS "public"."ruangancarabayar_mp" (
          "ruangancarabayar_id" int4 NOT NULL,
          "carabayar_id" int4 NOT NULL,
          "additional_data" text COLLATE "pg_catalog"."default",
          "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
          "created_by" int4,
          "modified_count" int4,
          "last_modified_date" timestamp(6),
          "last_modified_by" int4,
          "is_deleted" bool NOT NULL DEFAULT false,
          "is_active" bool NOT NULL DEFAULT true,
          "deleted_date" timestamp(6),
          "deleted_by" int4
      )
        ;');

        $this->execute('ALTER TABLE "public"."ruangancarabayar_mp" 
        OWNER TO "postgres";');


        $this->execute('DROP VIEW if exists public.sy_asuransipasien_v;');
        $this->execute("
            CREATE VIEW \"public\".\"sy_asuransipasien_v\" AS
            SELECT asuransipasien_m.asuransipasien_id,
            asuransipasien_m.pasien_id,
            asuransipasien_m.jenispeserta_id,
            asuransipasien_m.penjamin_id,
            penjamin_m.penjamin_nama,
            asuransipasien_m.carabayar_id,
            carabayar_m.carabayar_nama,
            asuransipasien_m.nokartuasuransi,
            asuransipasien_m.nopeserta,
            asuransipasien_m.namapemilikasuransi,
            asuransipasien_m.tglcetakkartuasuransi,
            asuransipasien_m.kelastanggunganasuransi_id,
            kelaspelayanan_m.kelaspelayanan_nama AS kelastanggunganasuransi_nama,
            asuransipasien_m.kodefeskestk1,
            asuransipasien_m.nama_feskestk1,
            asuransipasien_m.kodefeskesgigi,
            asuransipasien_m.namafeskesgigi,
            asuransipasien_m.namaperusahaan,
            asuransipasien_m.nomorpokokperusahaan,
            asuransipasien_m.masaberlakukartu,
            asuransipasien_m.nokartukeluarga,
            asuransipasien_m.nopassport,
            CASE
            WHEN ((asuransipasien_m.status_konfirmasi)::text = '0'::text) THEN true
            ELSE false
            END AS status_konfirmasi,
            asuransipasien_m.tgl_konfirmasi,
            asuransipasien_m.hubkeluarga,
            asuransipasien_m.pendaftaran_id,
            asuransipasien_m.namabagian,
            asuransipasien_m.noindukkaryawan,
            asuransipasien_m.jpkm,
            asuransipasien_m.nama_asuransi
            FROM ((((asuransipasien_m
            JOIN pasien_m ON ((asuransipasien_m.pasien_id = pasien_m.pasien_id)))
            JOIN penjamin_m ON ((asuransipasien_m.penjamin_id = penjamin_m.penjamin_id)))
            JOIN carabayar_m ON ((asuransipasien_m.carabayar_id = carabayar_m.carabayar_id)))
            LEFT JOIN kelaspelayanan_m ON ((asuransipasien_m.kelastanggunganasuransi_id = kelaspelayanan_m.kelaspelayanan_id)))
            ;");
        $this->execute('
            ALTER TABLE public.sy_asuransipasien_v OWNER TO postgres;
            ');

        $this->execute('DROP VIEW if exists public.sy_bpjs_v;');
        $this->execute("
            CREATE VIEW \"public\".\"sy_bpjs_v\" AS
            SELECT 'RJ-RD'::text AS jenis,
            bpjs_t.bpjs_id,
            bpjs_t.tglsep,
            bpjs_t.nosep,
            bpjs_t.nokartuasuransi,
            bpjs_t.tglrujukan,
            bpjs_t.norujukan,
            bpjs_t.ppkrujukan,
            bpjs_t.ppkpelayanan,
            bpjs_t.jnspelayanan,
            bpjs_t.catatansep,
            bpjs_t.diagnosaawal,
            bpjs_t.politujuan,
            bpjs_t.klsrawat,
            bpjs_t.tglpulang,
            bpjs_t.nama_peserta,
            bpjs_t.lakalantas,
            bpjs_t.lokasilaka,
            bpjs_t.no_rekam_medik,
            bpjs_t.pendaftaran_id,
            pasien_m.nama_pasien,
            bpjs_t.pasienadmisi_id,
            bpjs_t.asal_rujukan,
            bpjs_t.cetakan_ke,
            bpjs_t.tgl_cetak,
            bpjs_t.kode_dpjp_melayani
            FROM ((bpjs_t
            JOIN pendaftaran_t ON ((bpjs_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            UNION ALL
            SELECT 'RI'::text AS jenis,
            bpjs_t.bpjs_id,
            bpjs_t.tglsep,
            bpjs_t.nosep,
            bpjs_t.nokartuasuransi,
            bpjs_t.tglrujukan,
            bpjs_t.norujukan,
            bpjs_t.ppkrujukan,
            bpjs_t.ppkpelayanan,
            bpjs_t.jnspelayanan,
            bpjs_t.catatansep,
            bpjs_t.diagnosaawal,
            bpjs_t.politujuan,
            bpjs_t.klsrawat,
            bpjs_t.tglpulang,
            bpjs_t.nama_peserta,
            bpjs_t.lakalantas,
            bpjs_t.lokasilaka,
            bpjs_t.no_rekam_medik,
            bpjs_t.pendaftaran_id,
            pasien_m.nama_pasien,
            bpjs_t.pasienadmisi_id,
            bpjs_t.asal_rujukan,
            bpjs_t.cetakan_ke,
            bpjs_t.tgl_cetak,
            bpjs_t.kode_dpjp_melayani
            FROM ((bpjs_t
            JOIN pasienadmisi_t ON ((bpjs_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN pasien_m ON ((pasienadmisi_t.pasien_id = pasien_m.pasien_id)))
            ;");
        $this->execute('
            ALTER TABLE public.sy_bpjs_v OWNER TO postgres;
            ');

        $this->execute('DROP VIEW if exists public.sy_cetakpenunjang_v;');
        $this->execute("
            CREATE VIEW \"public\".\"sy_cetakpenunjang_v\" AS
            SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.umur,
            pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai,
            pasien_m.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pasien_m.tanggal_lahir
            FROM ((pendaftaran_t
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            ;");
        $this->execute('
            ALTER TABLE public.sy_cetakpenunjang_v OWNER TO postgres;
            ');

        $this->execute('DROP VIEW if exists public.sy_keluargapasien_v;');
        $this->execute("
            CREATE VIEW \"public\".\"sy_keluargapasien_v\" AS
            SELECT keluargapasien_t.keluargapasien_id,
            keluargapasien_t.keluarga_nama,
            fgetnamalookup((keluargapasien_t.keluarga_jk)::integer) AS keluarga_jk,
            fgetnamalookup((keluargapasien_t.keluarga_hubungan)::integer) AS keluarga_hubungan,
            keluargapasien_t.keluarga_alamat,
            keluargapasien_t.keluarga_no_telepon,
            fgetnamalookup((keluargapasien_t.keluarga_namadepan)::integer) AS keluarga_namadepan,
            keluargapasien_t.keluarga_propinsi_id,
            propinsi_m.propinsi_nama,
            keluargapasien_t.keluarga_kabupaten_id,
            kabupaten_m.kabupaten_nama,
            kecamatan_m.kode_kecamatan AS keluarga_kecamatan_id,
            kecamatan_m.kecamatan_nama,
            kelurahan_m.kode_kelurahan AS keluarga_kelurahan_id,
            kelurahan_m.kelurahan_nama,
            keluargapasien_t.keluarga_pekerjaan_id,
            pekerjaan_m.pekerjaan_nama,
            pekerjaan_m.pekerjaan_kode,
            keluargapasien_t.keluarga_rt,
            keluargapasien_t.keluarga_rw,
            keluargapasien_t.pasien_id,
            pasien_m.nama_pasien,
            kode_keluarga.lookup_kode AS keluarga_hubungan_kode,
            look_alamatdpn.lookup_name AS alamat_depan,
            kelurahan_m.kode_pos
            FROM ((((((((keluargapasien_t
            LEFT JOIN propinsi_m ON ((keluargapasien_t.keluarga_propinsi_id = propinsi_m.propinsi_id)))
            LEFT JOIN kabupaten_m ON ((keluargapasien_t.keluarga_kabupaten_id = kabupaten_m.kabupaten_id)))
            LEFT JOIN kecamatan_m ON ((keluargapasien_t.keluarga_kecamatan_id = kecamatan_m.kecamatan_id)))
            LEFT JOIN kelurahan_m ON ((keluargapasien_t.keluarga_kelurahan_id = kelurahan_m.kelurahan_id)))
            LEFT JOIN pekerjaan_m ON ((keluargapasien_t.keluarga_pekerjaan_id = pekerjaan_m.pekerjaan_id)))
            JOIN pasien_m ON ((keluargapasien_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN lookup_m kode_keluarga ON (((keluargapasien_t.keluarga_hubungan)::integer = kode_keluarga.lookup_id)))
            LEFT JOIN lookup_m look_alamatdpn ON (((keluargapasien_t.alamatdepan)::text = ((look_alamatdpn.lookup_id)::character varying)::text)))
            ;");
        $this->execute('
            ALTER TABLE public.sy_keluargapasien_v OWNER TO postgres;
            ');

        $this->execute('DROP VIEW if exists public.sy_pasien_v;');
        $this->execute("
            CREATE VIEW \"public\".\"sy_pasien_v\" AS
            SELECT pasien_m.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.tgl_rekam_medik,
            fgetnamalookup((pasien_m.jenisidentitas)::integer) AS jenisidentitas,
            pasien_m.no_identitas_pasien,
            fgetnamalookup((pasien_m.namadepan)::integer) AS namadepan,
            pasien_m.nama_pasien,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jeniskelamin,
            pasien_m.tempat_lahir,
            pasien_m.tanggal_lahir,
            pasien_m.golonganumur_id,
            golonganumur_m.golonganumur_nama,
            pasien_m.alamat_pasien,
            pasien_m.rt,
            pasien_m.rw,
            pasien_m.propinsi_id,
            propinsi_m.propinsi_nama,
            pasien_m.kabupaten_id,
            kabupaten_m.kabupaten_nama,
            kecamatan_m.kode_kecamatan AS kecamatan_id,
            kecamatan_m.kecamatan_nama,
            kelurahan_m.kode_kelurahan AS kelurahan_id,
            kelurahan_m.kelurahan_nama,
            pasien_m.pendidikan_id,
            pendidikan_m.pendidikan_nama,
            pasien_m.pekerjaan_id,
            pekerjaan_m.pekerjaan_nama,
            kerja.kd_master AS pekerjaan_kode,
            pasien_m.suku_id,
            suku_m.suku_nama,
            suku.kd_master AS suku_kode,
            fgetnamalookup((pasien_m.statusperkawinan)::integer) AS statusperkawinan,
            fgetnamalookup((pasien_m.agama)::integer) AS agama_nama,
            agama.kd_master AS agama_kode,
            fgetnamalookup((pasien_m.golongandarah)::integer) AS golongandarah,
            pasien_m.rhesus,
            pasien_m.anakke,
            pasien_m.jumlah_bersaudara,
            pasien_m.no_telepon_pasien,
            pasien_m.no_mobile_pasien,
            fgetnamalookup((pasien_m.warga_negara)::integer) AS warga_negara,
            warganegara.kd_master AS warga_negara_kode,
            pasien_m.alamatemail,
            pasien_m.nama_ibu,
            pasien_m.nama_ayah,
            pasien_m.dokrekammedis_id,
            pasien_m.tgl_meninggal,
            pasien_m.pegawai_id,
            pegawai_m.nama_pegawai,
            pasien_m.loginpemakai_id,
            fgetnamalookup((pasien_m.statusrekammedis)::integer) AS statusrekammedis,
            pasien_m.catatanpenting_pasien,
            fgetnamalookup((pasien_m.bahasa_sehari)::integer) AS bahasa_sehari,
            bhs_sehari.kd_master AS bahasa_sehari_kode,
            pasien_m.is_aps,
            pasien_m.additional_data,
            pasien_m.additional_pasien,
            look_alamatdpn.lookup_name AS alamat_depan,
            kelurahan_m.kode_pos
            FROM ((((((((((((((((((pasien_m
            LEFT JOIN golonganumur_m ON ((pasien_m.golonganumur_id = golonganumur_m.golonganumur_id)))
            LEFT JOIN propinsi_m ON ((pasien_m.propinsi_id = propinsi_m.propinsi_id)))
            LEFT JOIN kabupaten_m ON ((pasien_m.kabupaten_id = kabupaten_m.kabupaten_id)))
            LEFT JOIN kecamatan_m ON ((pasien_m.kecamatan_id = kecamatan_m.kecamatan_id)))
            LEFT JOIN kelurahan_m ON ((pasien_m.kelurahan_id = kelurahan_m.kelurahan_id)))
            LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
            LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
            LEFT JOIN suku_m ON ((pasien_m.suku_id = suku_m.suku_id)))
            LEFT JOIN pegawai_m ON ((pasien_m.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN lookup_m look_agama ON (((pasien_m.agama)::integer = look_agama.lookup_id)))
            LEFT JOIN sy_masterlookup_t agama ON (((look_agama.lookup_kode)::text = (agama.kd_master)::text)))
            LEFT JOIN sy_masterlookup_t suku ON (((suku_m.suku_kode)::text = (suku.kd_master)::text)))
            LEFT JOIN lookup_m look_warga ON (((pasien_m.warga_negara)::integer = look_warga.lookup_id)))
            LEFT JOIN sy_masterlookup_t warganegara ON (((look_warga.lookup_kode)::text = (warganegara.kd_master)::text)))
            LEFT JOIN sy_masterlookup_t kerja ON (((pekerjaan_m.pekerjaan_kode)::text = (kerja.kd_master)::text)))
            LEFT JOIN lookup_m look_bahasa ON (((pasien_m.bahasa_sehari)::integer = look_bahasa.lookup_id)))
            LEFT JOIN sy_masterlookup_t bhs_sehari ON (((look_bahasa.lookup_kode)::text = (bhs_sehari.kd_master)::text)))
            LEFT JOIN lookup_m look_alamatdpn ON (((pasien_m.alamatdepan)::text = ((look_alamatdpn.lookup_id)::character varying)::text)))
            ;");
        $this->execute('
            ALTER TABLE public.sy_pasien_v OWNER TO postgres;
            ');

        $this->execute('DROP VIEW if exists public.sy_pasienmasukpenunjang_v;');
        $this->execute("
            CREATE VIEW \"public\".\"sy_pasienmasukpenunjang_v\" AS
            SELECT pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            pasienmasukpenunjang_t.jeniskasuspenyakit_id,
            pasienmasukpenunjang_t.pasienadmisi_id,
            pasienmasukpenunjang_t.pegawai_id AS nmdr_id,
            peg_penunjang.nama_pegawai AS nmdr,
            peg_penunjang.dokter_id AS kddr,
            pasienmasukpenunjang_t.ruangan_id,
            pasienmasukpenunjang_t.pasien_id,
            pasien_m.nama_pasien,
            pasienmasukpenunjang_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran AS noregasal,
            pendaftaran_t.instalasi_id AS nmbagian_id,
            ins_pendaftaran.instalasi_nama AS nmbagian,
            ins_pendaftaran.additional_data AS kdbagian,
            pendaftaran_t.dokterpengirim_id,
            peg_pendaftaran.nama_pegawai,
            peg_pendaftaran.additional_data AS rjkndrdari,
            pendaftaran_t.styrujukaninstalasi_id,
            ruangasal_penunjang.additional_data AS rjkndaribagian,
            pasienmasukpenunjang_t.ruanganasal_id,
            ruangasal_penunjang.ruangan_nama AS ruanganasal_nama,
            insasal_penunjang.additional_data AS rjkndari,
            pasienmasukpenunjang_t.no_masukpenunjang AS noreg,
            pasienmasukpenunjang_t.tglmasukpenunjang,
            pasienmasukpenunjang_t.kunjungan AS kunjungan_id,
            CASE
            WHEN ((pasienmasukpenunjang_t.kunjungan)::integer = 180) THEN 'B'::text
            WHEN ((pasienmasukpenunjang_t.kunjungan)::integer = 181) THEN 'L'::text
            WHEN ((pasienmasukpenunjang_t.kunjungan)::integer = 310) THEN 'B'::text
            WHEN ((pasienmasukpenunjang_t.kunjungan)::integer = 311) THEN 'L'::text
            ELSE '-'::text
            END AS kunjungan_nama,
            pasienmasukpenunjang_t.instalasiasal_id,
            pasienmasukpenunjang_t.catatan AS cttnrp,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            kelaspelayanan_m.additional_data AS kelasasal,
            peg_pengganti.additional_data AS dokterpengganti
            FROM ((((((((((pasienmasukpenunjang_t
            JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
            JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
            JOIN ruangan_m ruangasal_penunjang ON ((pendaftaran_t.ruangan_id = ruangasal_penunjang.ruangan_id)))
            JOIN instalasi_m insasal_penunjang ON ((pendaftaran_t.styrujukaninstalasi_id = insasal_penunjang.instalasi_id)))
            JOIN instalasi_m ins_pendaftaran ON ((pendaftaran_t.instalasi_id = ins_pendaftaran.instalasi_id)))
            LEFT JOIN ruangan_m ruang_pendaftaran_rujukan ON ((pendaftaran_t.ruangan_id = ruang_pendaftaran_rujukan.ruangan_id)))
            LEFT JOIN pegawai_m peg_penunjang ON ((pasienmasukpenunjang_t.pegawai_id = peg_penunjang.pegawai_id)))
            LEFT JOIN pegawai_m peg_pendaftaran ON ((pendaftaran_t.dokterpengirim_id = peg_pendaftaran.pegawai_id)))
            LEFT JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            LEFT JOIN pegawai_m peg_pengganti ON ((pendaftaran_t.dokterpengganti_id = peg_pengganti.pegawai_id)))
            ;");
        $this->execute('
            ALTER TABLE public.sy_pasienmasukpenunjang_v OWNER TO postgres;
            ');

        $this->execute('DROP VIEW if exists public.sy_penanggungbiaya_v;');
        $this->execute("
            CREATE VIEW \"public\".\"sy_penanggungbiaya_v\" AS
            SELECT penanggungbiaya_t.penanggungbiaya_id,
            penanggungbiaya_t.pasien_id,
            pasien_m.nama_pasien,
            penanggungbiaya_t.carabayar_id,
            carabayar_m.carabayar_nama,
            penanggungbiaya_t.penanggungbiaya_nama,
            penanggungbiaya_t.namabagian,
            ruangcarabayar_m.ruangcarabayar_kode AS namabagian_kode,
            penanggungbiaya_t.noindukkaryawan,
            penanggungbiaya_t.jpkm,
            penanggungbiaya_t.instansi
            FROM (((penanggungbiaya_t
            JOIN pasien_m ON ((penanggungbiaya_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN carabayar_m ON ((penanggungbiaya_t.carabayar_id = carabayar_m.carabayar_id)))
            LEFT JOIN ruangcarabayar_m ON ((penanggungbiaya_t.carabayar_id = ruangcarabayar_m.ruangcarabayar_id)))
            ;");
        $this->execute('
            ALTER TABLE public.sy_penanggungbiaya_v OWNER TO postgres;
            ');

        $this->execute('DROP VIEW if exists public.sy_penanggungjawab_v;');
        $this->execute("
            CREATE VIEW \"public\".\"sy_penanggungjawab_v\" AS
            SELECT penanggungjawab_m.penanggungjawab_id,
            penanggungjawab_m.pengantar,
            look_pengantar.lookup_name AS pengantar_nama,
            penanggungjawab_m.jenisidentitas,
            look_jnsident.lookup_name AS jenisidentitas_nama,
            penanggungjawab_m.no_identitas,
            penanggungjawab_m.hubungankeluarga,
            look_hubkel.lookup_name AS hubungankeluarga_nama,
            penanggungjawab_m.penanggungjawab_nama,
            penanggungjawab_m.penanggungjawab_tempatlahir,
            penanggungjawab_m.penanggungjawab_tgllahir,
            penanggungjawab_m.penanggungjawab_jeniskelamin,
            penanggungjawab_m.penanggungjawab_alamat,
            penanggungjawab_m.penanggungjawab_notelp,
            penanggungjawab_m.penanggungjawab_nohp,
            penanggungjawab_m.pasien_id,
            penanggungjawab_m.pj_namadepan,
            look_pjnadep.lookup_name AS pj_namadepan_nama,
            penanggungjawab_m.pj_propinsi_id,
            propinsi_m.propinsi_nama,
            penanggungjawab_m.pj_kabupaten_id,
            kabupaten_m.kabupaten_nama,
            kecamatan_m.kode_kecamatan AS pj_kecamatan_id,
            kecamatan_m.kecamatan_nama,
            kelurahan_m.kode_kelurahan AS pj_kelurahan_id,
            kelurahan_m.kelurahan_nama,
            penanggungjawab_m.pj_pekerjaan_id,
            pekerjaan_m.pekerjaan_nama,
            penanggungjawab_m.pj_rt,
            penanggungjawab_m.pj_rw,
            look_hubkel.lookup_kode AS hubungankeluarga_kode,
            pekerjaan_m.pekerjaan_kode,
            look_alamatdpn.lookup_name AS alamat_depan,
            kelurahan_m.kode_pos
            FROM ((((((((((penanggungjawab_m
            LEFT JOIN lookup_m look_pengantar ON (((penanggungjawab_m.pengantar)::text = ((look_pengantar.lookup_id)::character varying)::text)))
            LEFT JOIN lookup_m look_jnsident ON (((penanggungjawab_m.jenisidentitas)::text = ((look_jnsident.lookup_id)::character varying)::text)))
            LEFT JOIN lookup_m look_hubkel ON (((penanggungjawab_m.hubungankeluarga)::text = ((look_hubkel.lookup_id)::character varying)::text)))
            LEFT JOIN lookup_m look_pjnadep ON (((penanggungjawab_m.pj_namadepan)::text = ((look_pjnadep.lookup_id)::character varying)::text)))
            LEFT JOIN propinsi_m ON ((penanggungjawab_m.pj_propinsi_id = propinsi_m.propinsi_id)))
            LEFT JOIN kabupaten_m ON ((penanggungjawab_m.pj_kabupaten_id = kabupaten_m.kabupaten_id)))
            LEFT JOIN kecamatan_m ON ((penanggungjawab_m.pj_kecamatan_id = kecamatan_m.kecamatan_id)))
            LEFT JOIN kelurahan_m ON ((penanggungjawab_m.pj_kelurahan_id = kelurahan_m.kelurahan_id)))
            LEFT JOIN pekerjaan_m ON ((penanggungjawab_m.pj_pekerjaan_id = pekerjaan_m.pekerjaan_id)))
            LEFT JOIN lookup_m look_alamatdpn ON (((penanggungjawab_m.alamatdepan)::text = ((look_alamatdpn.lookup_id)::character varying)::text)))
            ;");
        $this->execute('
            ALTER TABLE public.sy_penanggungjawab_v OWNER TO postgres;
            ');

        $this->execute('DROP VIEW if exists public.sy_pendaftaran_v;');
        $this->execute("
            CREATE VIEW \"public\".\"sy_pendaftaran_v\" AS
            SELECT 'RDRJPENUNJANG'::text AS tipe,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.pasienpulang_id,
            pendaftaran_t.pasienbatalperiksa_id,
            pendaftaran_t.penanggungjawab_id,
            penanggungjawab_m.penanggungjawab_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.pasien_id,
            pasien_m.nama_pasien,
            pendaftaran_t.pegawai_id AS dokterrj_id,
            pegawai_m.nama_pegawai AS nama_dokterrj,
            pegawai_m.additional_data AS kode_dokterrj,
            pasienadmisi_t.pegawai_id AS dokterri_id,
            pegawai_rd.nama_pegawai AS nama_dokterri,
            pegawai_rd.additional_data AS kode_dokterri,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.caramasuk_id,
            asalrujukan_m.asalrujukan_nama AS caramasuk_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.no_pembayaran,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.golonganumur_id,
            golonganumur_m.golonganumur_nama,
            pendaftaran_t.antrian_id,
            antrian_t.no_antrian,
            pendaftaran_t.karcis_id,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pendaftaran_t.no_urutantri,
            fgetnamalookup((pendaftaran_t.transportasi)::integer) AS transportasi,
            fgetnamalookup((pendaftaran_t.keadaan_masuk)::integer) AS keadaan_masuk,
            fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa,
            fgetnamalookup((pendaftaran_t.status_pasien)::integer) AS status_pasien,
            fgetnamalookup((pendaftaran_t.kunjungan)::integer) AS kunjungan_nama,
            kunj.lookup_kode AS kunjungan,
            fgetnamalookup((pendaftaran_t.status_masuk)::integer) AS status_masuk,
            pendaftaran_t.umur,
            pendaftaran_t.tgl_selesaiperiksa,
            pendaftaran_t.keterangan_pendaftaran,
            fgetnamalookup((pendaftaran_t.status_konfirmasi)::integer) AS status_konfirmasi,
            pendaftaran_t.asuransipasien_id,
            asuransipasien_m.nama_asuransi,
            pendaftaran_t.tgl_akandilayani,
            fgetnamalookup((pendaftaran_t.statusdok_rekammedik)::integer) AS statusdok_rekammedik,
            pendaftaran_t.bpjs_id,
            bpjs_t.nokartuasuransi,
            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
            fgetnamalookup(pendaftaran_t.status_verifikasi) AS status_verifikasi,
            pendaftaran_t.limit_tagihan,
            pendaftaran_t.additional_data,
            ruangan_m.additional_data AS instalasi_singkatan,
            kelaspelayanan_m.additional_data AS kelaspelayanan_singkatan,
            kamarruangan_m.additional_data AS kdruangri,
            kamartempattidur_m.no_tempattidur AS nobed,
            asalrujukan_m.asalrujukan_kode AS cara_masuk,
            carabayar_m.additional_data AS klppng,
            carabayar_m.carabayar_singkatan AS vkkelreg,
            rmed.ruangan_nama AS kdbagianrm,
            bpjs_t.nokartuasuransi AS nobpjs,
            look_status.lookup_kode AS status_kode,
            NULL::character varying AS prosedurmasuk,
            NULL::text AS hakkelas,
            NULL::text AS kelaspermintaan,
            NULL::text AS dokterkonsul,
            NULL::character varying AS diagnosa_awal,
            NULL::text AS dokterpengirim
            FROM ((((((((((((((((((((((pendaftaran_t
            JOIN ruangan_m rmed ON ((rmed.ruangan_id = 16)))
            LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
            LEFT JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN pegawai_m pegawai_rd ON ((pasienadmisi_t.pegawai_id = pegawai_rd.pegawai_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN asalrujukan_m ON ((pendaftaran_t.caramasuk_id = asalrujukan_m.asalrujukan_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            LEFT JOIN pembayaranpelayanan_t ON ((pendaftaran_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
            LEFT JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            LEFT JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
            LEFT JOIN antrian_t ON ((pendaftaran_t.antrian_id = antrian_t.antrian_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
            LEFT JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
            LEFT JOIN lookup_m kunj ON (((pendaftaran_t.kunjungan)::integer = kunj.lookup_id)))
            LEFT JOIN kamarruangan_m ON ((pendaftaran_t.ruangan_id = kamarruangan_m.ruangan_id)))
            LEFT JOIN kamartempattidur_m ON ((kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id)))
            LEFT JOIN lookup_m look_status ON (((pendaftaran_t.status_periksa)::text = ((look_status.lookup_id)::character varying)::text)))
            WHERE (pendaftaran_t.instalasi_id <> 3)
            UNION ALL
            SELECT 'RI'::text AS tipe,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pasienadmisi_t.tgl_pendaftaran,
            pendaftaran_t.pasienpulang_id,
            pendaftaran_t.pasienbatalperiksa_id,
            pendaftaran_t.penanggungjawab_id,
            penanggungjawab_m.penanggungjawab_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.pasien_id,
            pasien_m.nama_pasien,
            pendaftaran_t.pegawai_id AS dokterrj_id,
            pegawai_m.nama_pegawai AS nama_dokterrj,
            pegawai_m.additional_data AS kode_dokterrj,
            pasienadmisi_t.pegawai_id AS dokterri_id,
            pegawai_rd.nama_pegawai AS nama_dokterri,
            pegawai_rd.additional_data AS kode_dokterri,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.caramasuk_id,
            asalrujukan_m.asalrujukan_nama AS caramasuk_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.pembayaranpelayanan_id,
            pembayaranpelayanan_t.no_pembayaran,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.golonganumur_id,
            golonganumur_m.golonganumur_nama,
            pendaftaran_t.antrian_id,
            antrian_t.no_antrian,
            pendaftaran_t.karcis_id,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pendaftaran_t.no_urutantri,
            fgetnamalookup((pendaftaran_t.transportasi)::integer) AS transportasi,
            fgetnamalookup((pendaftaran_t.keadaan_masuk)::integer) AS keadaan_masuk,
            fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa,
            fgetnamalookup((pendaftaran_t.status_pasien)::integer) AS status_pasien,
            fgetnamalookup((pendaftaran_t.kunjungan)::integer) AS kunjungan_nama,
            kunj.lookup_kode AS kunjungan,
            fgetnamalookup((pendaftaran_t.status_masuk)::integer) AS status_masuk,
            pendaftaran_t.umur,
            pendaftaran_t.tgl_selesaiperiksa,
            pendaftaran_t.keterangan_pendaftaran,
            fgetnamalookup((pendaftaran_t.status_konfirmasi)::integer) AS status_konfirmasi,
            pendaftaran_t.asuransipasien_id,
            asuransipasien_m.nama_asuransi,
            pendaftaran_t.tgl_akandilayani,
            fgetnamalookup((pendaftaran_t.statusdok_rekammedik)::integer) AS statusdok_rekammedik,
            pendaftaran_t.bpjs_id,
            bpjs_t.nokartuasuransi,
            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
            fgetnamalookup(pendaftaran_t.status_verifikasi) AS status_verifikasi,
            pendaftaran_t.limit_tagihan,
            pendaftaran_t.additional_data,
            ruangan_m.additional_data AS instalasi_singkatan,
            kelaspelayanan_m.additional_data AS kelaspelayanan_singkatan,
            kamarruangan_m.additional_data AS kdruangri,
            kamartempattidur_m.no_tempattidur AS nobed,
            asalrujukan_m.asalrujukan_kode AS cara_masuk,
            carabayar_m.additional_data AS klppng,
            carabayar_m.carabayar_singkatan AS vkkelreg,
            rmed.ruangan_nama AS kdbagianrm,
            bpjs_t.nokartuasuransi AS nobpjs,
            look_status.lookup_kode AS status_kode,
            pasienadmisi_t.prosedurmasuk_id AS prosedurmasuk,
            hakkelas.additional_data AS hakkelas,
            kelaspermintaan.additional_data AS kelaspermintaan,
            pasienadmisi_t.dokterkonsul_id AS dokterkonsul,
            pasienadmisi_t.diagnosa_awal,
            dokterpengirim.additional_data AS dokterpengirim
            FROM ((((((((((((((((((((((((((pendaftaran_t
            JOIN ruangan_m rmed ON ((rmed.ruangan_id = 16)))
            JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
            LEFT JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN pegawai_m pegawai_rd ON ((pasienadmisi_t.pegawai_id = pegawai_rd.pegawai_id)))
            LEFT JOIN pegawai_m dokterpengirim ON ((pasienadmisi_t.dokterpengirim_id = dokterpengirim.pegawai_id)))
            LEFT JOIN asalrujukan_m ON ((pendaftaran_t.caramasuk_id = asalrujukan_m.asalrujukan_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            LEFT JOIN pembayaranpelayanan_t ON ((pendaftaran_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
            LEFT JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            LEFT JOIN kelaspelayanan_m hakkelas ON ((pasienadmisi_t.hakkelas_id = hakkelas.kelaspelayanan_id)))
            LEFT JOIN kelaspelayanan_m kelaspermintaan ON ((pasienadmisi_t.kelaspermintaan_id = kelaspermintaan.kelaspelayanan_id)))
            LEFT JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
            LEFT JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
            LEFT JOIN antrian_t ON ((pendaftaran_t.antrian_id = antrian_t.antrian_id)))
            JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
            LEFT JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
            LEFT JOIN lookup_m kunj ON (((pendaftaran_t.kunjungan)::integer = kunj.lookup_id)))
            LEFT JOIN masukkamar_t ON ((pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id)))
            LEFT JOIN kamartempattidur_m ON ((masukkamar_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
            LEFT JOIN kamarruangan_m ON ((masukkamar_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
            LEFT JOIN lookup_m look_status ON (((pendaftaran_t.status_periksa)::text = ((look_status.lookup_id)::character varying)::text)))
            WHERE (pendaftaran_t.instalasi_id = 3)
            ;");
        $this->execute('
            ALTER TABLE public.sy_pendaftaran_v OWNER TO postgres;
            ');

        $this->execute('DROP VIEW if exists public.sy_penjamin_v;');
        $this->execute("
            CREATE VIEW \"public\".\"sy_penjamin_v\" AS
            SELECT penjamin_m.penjamin_id,
            penjamin_m.carabayar_id,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            penjamin_m.penjamin_namalainnya,
            penjamin_m.alamat_penjamin,
            penjamin_m.s_kode,
            penjamin_m.penjamin_kode,
            penjamin_m.additional_data
            FROM (penjamin_m
            JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
            ;");
        $this->execute('
            ALTER TABLE public.sy_penjamin_v OWNER TO postgres;
            ');


        $this->execute('DROP VIEW if exists public.sy_ruangancarabayar_v;');
        $this->execute("
            CREATE VIEW \"public\".\"sy_ruangancarabayar_v\" AS
            SELECT ruangancarabayar_mp.ruangancarabayar_id,
            ruangancarabayar_mp.carabayar_id,
            ruangcarabayar_m.ruangcarabayar_nama,
            ruangcarabayar_m.ruangcarabayar_kode,
            carabayar_m.carabayar_nama,
            carabayar_m.carabayar_singkatan AS carabayar_kode
            FROM ((ruangancarabayar_mp
            JOIN ruangcarabayar_m ON ((ruangancarabayar_mp.ruangancarabayar_id = ruangcarabayar_m.ruangcarabayar_id)))
            JOIN carabayar_m ON ((ruangancarabayar_mp.carabayar_id = carabayar_m.carabayar_id)))
            WHERE ((ruangcarabayar_m.is_active = true) AND (carabayar_m.is_active = true))
            ;");
        $this->execute('
            ALTER TABLE public.sy_ruangancarabayar_v OWNER TO postgres;
            ');

        $this->execute('DROP VIEW if exists public.sy_suratreferal_v;');
        $this->execute("
            CREATE VIEW \"public\".\"sy_suratreferal_v\" AS
            SELECT
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.tgl_pendaftaran
            ELSE pasienadmisi_t.tgl_pendaftaran
            END AS tgl_pendaftaran,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.umur,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.carabayar_id
            ELSE pasienadmisi_t.carabayar_id
            END AS carabayar_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pend_cabar.carabayar_nama
            ELSE adm_cabar.carabayar_nama
            END AS carabayar_nama,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.kelaspelayanan_id
            ELSE pasienadmisi_t.kelaspelayanan_id
            END AS kelaspelayanan_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pend_kepel.kelaspelayanan_nama
            ELSE adm_kepel.kelaspelayanan_nama
            END AS kelaspelayanan_nama,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.ruangan_id
            ELSE pasienadmisi_t.ruangan_id
            END AS ruangan_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pend_ruang.ruangan_nama
            ELSE adm_ruang.ruangan_nama
            END AS ruangan_nama,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.instalasi_id
            ELSE adm_ins.instalasi_id
            END AS instalasi_id,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pend_ins.instalasi_nama
            ELSE adm_ins.instalasi_nama
            END AS instalasi_nama,
            pasien_m.pasien_id,
            pasien_m.nama_pasien,
            fgetnamalookup((pasien_m.namadepan)::integer) AS namadepan,
            pasien_m.no_rekam_medik,
            pasien_m.alamat_pasien,
            pasien_m.tanggal_lahir,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS pasienjeniskelamin,
            pasien_m.no_telepon_pasien,
            pasien_m.no_mobile_pasien,
            fgetnamalookup((keluargapasien_t.keluarga_namadepan)::integer) AS keluarga_namadepan,
            keluargapasien_t.keluarga_nama,
            fgetnamalookup((keluargapasien_t.keluarga_hubungan)::integer) AS keluarga_hubungan,
            keluargapasien_t.keluarga_alamat
            FROM ((((((((((((pendaftaran_t
            LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            LEFT JOIN carabayar_m pend_cabar ON ((pendaftaran_t.carabayar_id = pend_cabar.carabayar_id)))
            LEFT JOIN carabayar_m adm_cabar ON ((pasienadmisi_t.carabayar_id = adm_cabar.carabayar_id)))
            LEFT JOIN kelaspelayanan_m pend_kepel ON ((pendaftaran_t.kelaspelayanan_id = pend_kepel.kelaspelayanan_id)))
            LEFT JOIN kelaspelayanan_m adm_kepel ON ((pasienadmisi_t.kelaspelayanan_id = adm_kepel.kelaspelayanan_id)))
            LEFT JOIN ruangan_m pend_ruang ON ((pendaftaran_t.ruangan_id = pend_ruang.ruangan_id)))
            LEFT JOIN ruangan_m adm_ruang ON ((pasienadmisi_t.ruangan_id = adm_ruang.ruangan_id)))
            LEFT JOIN instalasi_m pend_ins ON ((pendaftaran_t.instalasi_id = pend_ins.instalasi_id)))
            LEFT JOIN instalasi_m adm_ins ON ((adm_ruang.instalasi_id = adm_ins.instalasi_id)))
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN keluargapasien_t ON ((pasien_m.pasien_id = keluargapasien_t.pasien_id)))
            ;");
        $this->execute('
            ALTER TABLE public.sy_suratreferal_v OWNER TO postgres;
            ');

        $this->execute('DROP VIEW if exists public.sy_sync_v;');
        $this->execute("
            CREATE VIEW \"public\".\"sy_sync_v\" AS
            SELECT syncsantoyusup_r.syncsantoyusup_id AS id,
            syncsantoyusup_r.is_sync,
            pdftrn.pendaftaran_id,
            pdftrn.no_pendaftaran,
            pasien.pasien_id,
            pasien.nama_pasien,
            pasien.no_rekam_medik,
            ruangan.ruangan_nama,
            dokter.nama_pegawai,
            syncsantoyusup_r.additional_data,
            syncsantoyusup_r.created_date,
            syncsantoyusup_r.last_modified_date,
            pdftrn.instalasi_id
            FROM ((((syncsantoyusup_r
            LEFT JOIN pendaftaran_t pdftrn ON ((pdftrn.pendaftaran_id = syncsantoyusup_r.pendaftaran_id)))
            LEFT JOIN pasien_m pasien ON ((pasien.pasien_id = pdftrn.pasien_id)))
            JOIN ruangan_m ruangan ON ((ruangan.ruangan_id = pdftrn.ruangan_id)))
            JOIN pegawai_m dokter ON ((dokter.pegawai_id = pdftrn.pegawai_id)))
            ;");
        $this->execute('
            ALTER TABLE public.sy_sync_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210702_100813_migrate_santoyusup_20210702 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210702_100813_migrate_santoyusup_20210702 cannot be reverted.\n";

        return false;
    }
    */
}

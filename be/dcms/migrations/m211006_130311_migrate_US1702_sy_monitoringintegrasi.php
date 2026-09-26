<?php

use yii\db\Migration;

/**
 * Class m211006_130311_migrate_US1702_sy_monitoringintegrasi
 */
class m211006_130311_migrate_US1702_sy_monitoringintegrasi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."syncsantoyusup_r" 
            ADD COLUMN IF NOT EXISTS "count_sync" int4,
            ADD COLUMN IF NOT EXISTS "payload" text COLLATE "pg_catalog"."default";');

        $this->execute('CREATE TABLE IF NOT EXISTS "public"."synceditsantoyusup_r" (
          "synceditsantoyusup_id" serial8,
          "pendaftaran_id" int4,
          "pasien_id" int4,
          "is_sync" bool DEFAULT false,
          "additional_data" text COLLATE "pg_catalog"."default",
          "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
          "last_modified_date" timestamp(6),
          "count_sync" int4,
          "payload" text COLLATE "pg_catalog"."default",
          "state" text COLLATE "pg_catalog"."default",
          CONSTRAINT "synceditsantoyusup_r_pkey" PRIMARY KEY ("synceditsantoyusup_id"));');

        $this->execute('ALTER TABLE "public"."synceditsantoyusup_r" 
          OWNER TO "postgres";');

        $this->execute('DROP VIEW if exists public.syncsantoyusup_v;');
        $this->execute("
            CREATE VIEW \"public\".\"syncsantoyusup_v\" AS
            SELECT a.syncsantoyusup_id AS id,
            b.no_pendaftaran,
            a.additional_data,
            a.created_date AS tgl_pendaftaran,
            a.payload
            FROM (syncsantoyusup_r a
            JOIN pendaftaran_t b ON ((b.pendaftaran_id = a.pendaftaran_id)))
            WHERE (a.is_sync = false)
            ;");
        $this->execute('
            ALTER TABLE public.syncsantoyusup_v OWNER TO postgres;
            ');

        $this->execute('DROP VIEW if exists public.synceditsantoyusup_v;');
        $this->execute("
            CREATE VIEW \"public\".\"synceditsantoyusup_v\" AS
            SELECT a.synceditsantoyusup_id AS id,
            b.no_pendaftaran,
            a.additional_data,
            a.created_date AS tgl_pendaftaran,
            a.payload,
            a.state AS type
            FROM (synceditsantoyusup_r a
            JOIN pendaftaran_t b ON ((b.pendaftaran_id = a.pendaftaran_id)))
            WHERE (a.is_sync = false)
            ;");
        $this->execute('
            ALTER TABLE public.synceditsantoyusup_v OWNER TO postgres;
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
            CASE
            WHEN (pendaftaran_t.dokterpengganti_id IS NOT NULL) THEN dokter_pengganti.additional_data
            ELSE pegawai_m.additional_data
            END AS kode_dokterrj,
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
            NULL::text AS dokterpengirim,
            pasienbatalperiksa_t.alasan_batal,
            pendaftaran_t.created_date,
            CASE
            WHEN (date_part('hour'::text, pendaftaran_t.created_date) >= (14)::double precision) THEN 'S'::text
            ELSE 'P'::text
            END AS shift
            FROM ((((((((((((((((((((((((pendaftaran_t
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
            LEFT JOIN pasienbatalperiksa_t ON ((pendaftaran_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id)))
            LEFT JOIN pegawai_m dokter_pengganti ON ((pendaftaran_t.dokterpengganti_id = dokter_pengganti.pegawai_id)))
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
            dokterpengirim.additional_data AS dokterpengirim,
            pasienbatalperiksa_t.alasan_batal,
            pendaftaran_t.created_date,
            CASE
            WHEN (date_part('hour'::text, pendaftaran_t.created_date) >= (14)::double precision) THEN 'S'::text
            ELSE 'P'::text
            END AS shift
            FROM (((((((((((((((((((((((((((pendaftaran_t
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
            LEFT JOIN pasienbatalperiksa_t ON ((pendaftaran_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id)))
            WHERE (pendaftaran_t.instalasi_id = 3)
            ;");
        $this->execute('
            ALTER TABLE public.sy_pendaftaran_v OWNER TO postgres;
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
            kelurahan_m.kode_pos,
            pendidikan_m.pendidikan_kode
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
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211006_130311_migrate_US1702_sy_monitoringintegrasi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211006_130311_migrate_US1702_sy_monitoringintegrasi cannot be reverted.\n";

        return false;
    }
    */
}

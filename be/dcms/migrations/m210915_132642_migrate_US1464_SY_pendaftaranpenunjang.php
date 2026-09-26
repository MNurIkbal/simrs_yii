<?php

use yii\db\Migration;

/**
 * Class m210915_132642_migrate_US1464_SY_pendaftaranpenunjang
 */
class m210915_132642_migrate_US1464_SY_pendaftaranpenunjang extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.inforegistrasi_v;');
        $this->execute("
            CREATE VIEW \"public\".\"inforegistrasi_v\" AS
            SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pasien_m.catatanpenting_pasien,
            dok_utama.pegawai_id AS dokterutama_id,
            dok_utama.nama_pegawai AS dokterutama_nama,
            dok_pengirim.pegawai_id AS dokterpengirim_id,
            dok_pengirim.nama_pegawai AS dokterpengirim_nama,
            dok_pengganti.pegawai_id AS dokterpengganti_id,
            dok_pengganti.nama_pegawai AS dokterpengganti_nama
            FROM ((((((pendaftaran_t
            JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik,
            a.catatanpenting_pasien
            FROM pasien_m a) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) dok_utama ON ((pendaftaran_t.pegawai_id = dok_utama.pegawai_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) dok_pengirim ON ((pendaftaran_t.dokterpengirim_id = dok_pengirim.pegawai_id)))
            LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
            FROM pegawai_m a) dok_pengganti ON ((pendaftaran_t.dokterpengganti_id = dok_pengganti.pegawai_id)))
            ;");
        $this->execute('
            ALTER TABLE public.inforegistrasi_v OWNER TO postgres;
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
            pasienbatalperiksa_t.alasan_batal
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
            pasienbatalperiksa_t.alasan_batal
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

        $this->execute('DROP VIEW if exists public.infokunjunganrs_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infokunjunganrs_v\" AS
            SELECT 'RJRDPENUNJANG'::text AS ket,
            pasien_m.pasien_id,
            pasien_m.jenisidentitas,
            pasien_m.no_identitas_pasien,
            fgetnamalookup((pasien_m.namadepan)::integer) AS namadepan,
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
            pasien_m.propinsi_id,
            fgetnamaarea(pasien_m.propinsi_id, NULL::integer, NULL::integer, NULL::integer) AS propinsi_nama,
            pasien_m.kabupaten_id,
            fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
            pasien_m.kecamatan_id,
            fgetnamaarea(NULL::integer, NULL::integer, pasien_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
            pasien_m.kelurahan_id,
            fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pasien_m.kelurahan_id) AS kelurahan_nama,
            pendaftaran_t.pendaftaran_id,
            pekerjaan_m.pekerjaan_id,
            pekerjaan_m.pekerjaan_nama,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_urutantri,
            pendaftaran_t.transportasi,
            pendaftaran_t.keadaan_masuk,
            pendaftaran_t.status_pasien,
            pendaftaran_t.kunjungan,
            pendaftaran_t.alih_status,
            pendaftaran_t.by_phone,
            pendaftaran_t.kunjungan_rumah,
            pendaftaran_t.status_masuk,
            pendaftaran_t.umur,
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
            golonganumur_m.golonganumur_id,
            golonganumur_m.golonganumur_nama,
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
            pegawai_m.gelardepan,
            pegawai_m.nama_pegawai,
            pegawai_m.gelarbelakang,
            pendaftaran_t.rujukan_id,
            pendaftaran_t.pasienpulang_id,
            asuransipasien_m.status_konfirmasi,
            asuransipasien_m.tgl_konfirmasi,
            pendaftaran_t.pegawai_id,
            pendaftaran_t.pembayaranpelayanan_id,
            pasien_m.rhesus,
            pasien_m.anakke,
            pasien_m.jumlah_bersaudara,
            pasien_m.no_telepon_pasien,
            pasien_m.no_mobile_pasien,
            pasien_m.warga_negara,
            pasien_m.nama_ibu,
            pasien_m.nama_ayah,
            suku_m.suku_id,
            suku_m.suku_nama,
            pendidikan_m.pendidikan_id,
            pendidikan_m.pendidikan_nama,
            carakeluar_m.carakeluar_id,
            carakeluar_m.carakeluar_nama AS carakeluar,
            kondisikeluar_m.kondisikeluar_id,
            kondisikeluar_m.kondisikeluar_nama AS kondisipulang,
            asuransipasien_m.nopeserta,
            asuransipasien_m.tglcetakkartuasuransi,
            asuransipasien_m.kodefeskestk1,
            asuransipasien_m.nama_feskestk1,
            asuransipasien_m.masaberlakukartu,
            asuransipasien_m.nokartukeluarga,
            asuransipasien_m.nopassport,
            asuransipasien_m.is_active,
            pendaftaran_t.keterangan_pendaftaran,
            NULL::integer AS konsulpoli_id,
            pasien_m.is_deleted,
            fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
            pendaftaran_t.created_by,
            antrian_t.no_antrian,
            antrian_t.jenisantrian_id,
            CASE
            WHEN (antrian_t.jenisantrian_id = 312) THEN 't'::text
            ELSE 'f'::text
            END AS is_poliklinik,
            pendaftaran_t.is_karcis,
            (pendaftaran_t.status_periksa)::integer AS status_periksa_id,
            pendaftaran_t.pasienpulang_id AS pulang_rj_rd,
            NULL::integer AS pulang_ri,
            NULL::integer AS pasienadmisi_id,
            pendaftaran_t.is_ranap,
            pendaftaran_t.bpjs_id,
            fgetnamalookup((pegawai_m.gelardepan)::integer) AS gelardepan_nama,
            fgetnamalookup((pegawai_m.gelarbelakang)::integer) AS gelarbelakang_nama,
            pendaftaran_t.pendaftaranibu_id,
            pasienpulang_t.tglpasienpulang,
            carakeluar_m.carakeluar_nama,
            bpjs_t.nosep,
            pendaftaran_t.status_konfirmasi AS status_konfirmasirm_id,
            fgetnamalookup((pendaftaran_t.status_konfirmasi)::integer) AS status_konfirmasirm,
            fgetnamalookup((pendaftaran_t.status_pasien)::integer) AS status_pasien_nama,
            NULL::character varying AS kamar,
            NULL::character varying AS no_tempattidur,
            NULL::boolean AS is_pasientitipan,
            NULL::integer AS kelas_ditagihkan_id,
            NULL::character varying AS kelas_ditagihkan_nama,
            NULL::integer AS kamar_titipan_id,
            NULL::character varying AS kamar_titipan_nama,
            NULL::integer AS ruangan_titipan_id,
            NULL::character varying AS ruangan_titipan_nama,
            false AS is_stoptitipan,
            pasien_m.additional_pasien,
            pasien_m.catatanpenting_pasien,
            penanggungjawab_m.penanggungjawab_alamat,
            penanggungjawab_m.penanggungjawab_notelp,
            pj_kerja.pekerjaan_id AS pj_pekerjaan_id,
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
            penanggungbiaya_t.penanggungbiaya_id,
            penanggungbiaya_t.penanggungbiaya_nama
            FROM ((((((((((((((((((((((((((((pendaftaran_t
            LEFT JOIN antrian_t ON ((antrian_t.antrian_id = pendaftaran_t.antrian_id)))
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
            LEFT JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
            LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
            LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
            LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
            LEFT JOIN pekerjaan_m pj_kerja ON ((penanggungjawab_m.pj_pekerjaan_id = pj_kerja.pekerjaan_id)))
            LEFT JOIN propinsi_m pj_prop ON ((penanggungjawab_m.pj_propinsi_id = pj_prop.propinsi_id)))
            LEFT JOIN kabupaten_m pj_kab ON ((penanggungjawab_m.pj_kabupaten_id = pj_kab.kabupaten_id)))
            LEFT JOIN kecamatan_m pj_kec ON ((penanggungjawab_m.pj_kecamatan_id = pj_kec.kecamatan_id)))
            LEFT JOIN kelurahan_m pj_kel ON ((penanggungjawab_m.pj_kelurahan_id = pj_kel.kelurahan_id)))
            LEFT JOIN suku_m ON ((pasien_m.suku_id = suku_m.suku_id)))
            LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
            LEFT JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
            LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
            LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
            LEFT JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
            LEFT JOIN penanggungbiaya_t ON ((pendaftaran_t.penanggungbiaya_id = penanggungbiaya_t.penanggungbiaya_id)))
            WHERE (pendaftaran_t.instalasi_id <> 3)
            UNION ALL
            SELECT 'RI'::text AS ket,
            pasien_m.pasien_id,
            pasien_m.jenisidentitas,
            pasien_m.no_identitas_pasien,
            fgetnamalookup((pasien_m.namadepan)::integer) AS namadepan,
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
            pasien_m.propinsi_id,
            fgetnamaarea(pasien_m.propinsi_id, NULL::integer, NULL::integer, NULL::integer) AS propinsi_nama,
            pasien_m.kabupaten_id,
            fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
            pasien_m.kecamatan_id,
            fgetnamaarea(NULL::integer, NULL::integer, pasien_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
            pasien_m.kelurahan_id,
            fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pasien_m.kelurahan_id) AS kelurahan_nama,
            pendaftaran_t.pendaftaran_id,
            pekerjaan_m.pekerjaan_id,
            pekerjaan_m.pekerjaan_nama,
            pendaftaran_t.no_pendaftaran,
            pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
            pendaftaran_t.no_urutantri,
            pendaftaran_t.transportasi,
            pendaftaran_t.keadaan_masuk,
            pendaftaran_t.status_pasien,
            pendaftaran_t.kunjungan,
            pendaftaran_t.alih_status,
            pendaftaran_t.by_phone,
            pendaftaran_t.kunjungan_rumah,
            pendaftaran_t.status_masuk,
            pendaftaran_t.umur,
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
            golonganumur_m.golonganumur_id,
            golonganumur_m.golonganumur_nama,
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
            pegawai_m.gelardepan,
            pegawai_m.nama_pegawai,
            pegawai_m.gelarbelakang,
            pendaftaran_t.rujukan_id,
            pasienadmisi_t.pasienpulang_id,
            asuransipasien_m.status_konfirmasi,
            asuransipasien_m.tgl_konfirmasi,
            pasienadmisi_t.pegawai_id,
            pendaftaran_t.pembayaranpelayanan_id,
            pasien_m.rhesus,
            pasien_m.anakke,
            pasien_m.jumlah_bersaudara,
            pasien_m.no_telepon_pasien,
            pasien_m.no_mobile_pasien,
            pasien_m.warga_negara,
            pasien_m.nama_ibu,
            pasien_m.nama_ayah,
            suku_m.suku_id,
            suku_m.suku_nama,
            pendidikan_m.pendidikan_id,
            pendidikan_m.pendidikan_nama,
            carakeluar_m.carakeluar_id,
            carakeluar_m.carakeluar_nama AS carakeluar,
            kondisikeluar_m.kondisikeluar_id,
            kondisikeluar_m.kondisikeluar_nama AS kondisipulang,
            asuransipasien_m.nopeserta,
            asuransipasien_m.tglcetakkartuasuransi,
            asuransipasien_m.kodefeskestk1,
            asuransipasien_m.nama_feskestk1,
            asuransipasien_m.masaberlakukartu,
            asuransipasien_m.nokartukeluarga,
            asuransipasien_m.nopassport,
            asuransipasien_m.is_active,
            pendaftaran_t.keterangan_pendaftaran,
            NULL::integer AS konsulpoli_id,
            pasien_m.is_deleted,
            fgetnamalookup(pasienadmisi_t.status_ranap) AS status_periksa,
            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
            pendaftaran_t.created_by,
            antrian_t.no_antrian,
            antrian_t.jenisantrian_id,
            CASE
            WHEN (antrian_t.jenisantrian_id = 312) THEN 't'::text
            ELSE 'f'::text
            END AS is_poliklinik,
            pendaftaran_t.is_karcis,
            pasienadmisi_t.status_ranap AS status_periksa_id,
            NULL::integer AS pulang_rj_rd,
            pasienadmisi_t.pasienpulang_id AS pulang_ri,
            pasienadmisi_t.pasienadmisi_id,
            pendaftaran_t.is_ranap,
            pasienadmisi_t.bpjs_id,
            fgetnamalookup((pegawai_m.gelardepan)::integer) AS gelardepan_nama,
            fgetnamalookup((pegawai_m.gelarbelakang)::integer) AS gelarbelakang_nama,
            pendaftaran_t.pendaftaranibu_id,
            pasienpulang_t.tglpasienpulang,
            carakeluar_m.carakeluar_nama,
            bpjs_t.nosep,
            pendaftaran_t.status_konfirmasi AS status_konfirmasirm_id,
            fgetnamalookup((pendaftaran_t.status_konfirmasi)::integer) AS status_konfirmasirm,
            fgetnamalookup((pendaftaran_t.status_pasien)::integer) AS status_pasien_nama,
            kamarruangan_m.kamarruangan_nokamar AS kamar,
            kamartempattidur_m.no_tempattidur,
            pasienadmisi_t.is_pasientitipan,
            pasienadmisi_t.kelas_ditagihkan_id,
            kelas_ditagihkan.kelaspelayanan_nama AS kelas_ditagihkan_nama,
            pasienadmisi_t.kamar_titipan_id,
            kamar_ditagihkan.kamarruangan_nokamar AS kamar_titipan_nama,
            pasienadmisi_t.ruangan_titipan_id,
            ruangan_ditagihkan.ruangan_nama AS ruangan_titipan_nama,
            NULL::boolean AS is_stoptitipan,
            pasien_m.additional_pasien,
            pasien_m.catatanpenting_pasien,
            penanggungjawab_m.penanggungjawab_alamat,
            penanggungjawab_m.penanggungjawab_notelp,
            pj_kerja.pekerjaan_id AS pj_pekerjaan_id,
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
            penanggungbiaya_t.penanggungbiaya_id,
            penanggungbiaya_t.penanggungbiaya_nama
            FROM ((((((((((((((((((((((((((((((((((pendaftaran_t
            JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            LEFT JOIN antrian_t ON ((antrian_t.antrian_id = pendaftaran_t.antrian_id)))
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
            LEFT JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
            LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
            LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
            JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
            LEFT JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
            JOIN jeniskasuspenyakit_m ON ((kamarruangan_m.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            LEFT JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
            LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
            LEFT JOIN pekerjaan_m pj_kerja ON ((penanggungjawab_m.pj_pekerjaan_id = pj_kerja.pekerjaan_id)))
            LEFT JOIN propinsi_m pj_prop ON ((penanggungjawab_m.pj_propinsi_id = pj_prop.propinsi_id)))
            LEFT JOIN kabupaten_m pj_kab ON ((penanggungjawab_m.pj_kabupaten_id = pj_kab.kabupaten_id)))
            LEFT JOIN kecamatan_m pj_kec ON ((penanggungjawab_m.pj_kecamatan_id = pj_kec.kecamatan_id)))
            LEFT JOIN kelurahan_m pj_kel ON ((penanggungjawab_m.pj_kelurahan_id = pj_kel.kelurahan_id)))
            LEFT JOIN suku_m ON ((pasien_m.suku_id = suku_m.suku_id)))
            LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
            LEFT JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
            LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
            LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
            LEFT JOIN bpjs_t ON ((pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id)))
            LEFT JOIN kelaspelayanan_m kelas_ditagihkan ON ((pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id)))
            LEFT JOIN kamarruangan_m kamar_ditagihkan ON ((pasienadmisi_t.kamar_titipan_id = kamar_ditagihkan.kamarruangan_id)))
            LEFT JOIN ruangan_m ruangan_ditagihkan ON ((pasienadmisi_t.ruangan_titipan_id = ruangan_ditagihkan.ruangan_id)))
            LEFT JOIN penanggungbiaya_t ON ((pendaftaran_t.penanggungbiaya_id = penanggungbiaya_t.penanggungbiaya_id)))
            ;");
        $this->execute('
            ALTER TABLE public.infokunjunganrs_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210915_132642_migrate_US1464_SY_pendaftaranpenunjang cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210915_132642_migrate_US1464_SY_pendaftaranpenunjang cannot be reverted.\n";

        return false;
    }
    */
}

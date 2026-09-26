<?php

use yii\db\Migration;

/**
 * Class m210308_050329_migrate_20210308_3395_view_santoyusup
 */
class m210308_050329_migrate_20210308_3395_view_santoyusup extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
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
            keluargapasien_t.keluarga_kecamatan_id,
            kecamatan_m.kecamatan_nama,
            keluargapasien_t.keluarga_kelurahan_id,
            kelurahan_m.kelurahan_nama,
            keluargapasien_t.keluarga_pekerjaan_id,
            pekerjaan_m.pekerjaan_nama,
            kerja.kd_master AS pekerjaan_kode,
            keluargapasien_t.keluarga_rt,
            keluargapasien_t.keluarga_rw,
            keluargapasien_t.pasien_id,
            pasien_m.nama_pasien
            FROM (((((((keluargapasien_t
            LEFT JOIN propinsi_m ON ((keluargapasien_t.keluarga_propinsi_id = propinsi_m.propinsi_id)))
            LEFT JOIN kabupaten_m ON ((keluargapasien_t.keluarga_kabupaten_id = kabupaten_m.kabupaten_id)))
            LEFT JOIN kecamatan_m ON ((keluargapasien_t.keluarga_kecamatan_id = kecamatan_m.kecamatan_id)))
            LEFT JOIN kelurahan_m ON ((keluargapasien_t.keluarga_kelurahan_id = kelurahan_m.kelurahan_id)))
            LEFT JOIN pekerjaan_m ON ((keluargapasien_t.keluarga_pekerjaan_id = pekerjaan_m.pekerjaan_id)))
            JOIN pasien_m ON ((keluargapasien_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN sy_masterlookup_t kerja ON ((keluargapasien_t.keluarga_pekerjaan_id = kerja.lookup_id)))
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
            pasien_m.kecamatan_id,
            kecamatan_m.kecamatan_nama,
            pasien_m.kelurahan_id,
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
            pasien_m.additional_pasien
            FROM ((((((((((((((pasien_m
            JOIN golonganumur_m ON ((pasien_m.golonganumur_id = golonganumur_m.golonganumur_id)))
            LEFT JOIN propinsi_m ON ((pasien_m.propinsi_id = propinsi_m.propinsi_id)))
            LEFT JOIN kabupaten_m ON ((pasien_m.kabupaten_id = kabupaten_m.kabupaten_id)))
            LEFT JOIN kecamatan_m ON ((pasien_m.kecamatan_id = kecamatan_m.kecamatan_id)))
            LEFT JOIN kelurahan_m ON ((pasien_m.kelurahan_id = kelurahan_m.kelurahan_id)))
            LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
            LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
            LEFT JOIN suku_m ON ((pasien_m.suku_id = suku_m.suku_id)))
            LEFT JOIN pegawai_m ON ((pasien_m.pegawai_id = pegawai_m.pegawai_id)))
            LEFT JOIN sy_masterlookup_t agama ON (((pasien_m.agama)::integer = agama.lookup_id)))
            LEFT JOIN sy_masterlookup_t suku ON ((pasien_m.suku_id = suku.lookup_id)))
            LEFT JOIN sy_masterlookup_t warganegara ON (((pasien_m.warga_negara)::integer = warganegara.lookup_id)))
            LEFT JOIN sy_masterlookup_t kerja ON ((pasien_m.pekerjaan_id = kerja.lookup_id)))
            LEFT JOIN sy_masterlookup_t bhs_sehari ON (((pasien_m.bahasa_sehari)::integer = bhs_sehari.lookup_id)))
            ;");
            $this->execute('
                ALTER TABLE public.sy_pasien_v OWNER TO postgres;
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
            penanggungbiaya_t.noindukkaryawan,
            penanggungbiaya_t.jpkm,
            penanggungbiaya_t.instansi
            FROM ((penanggungbiaya_t
            JOIN pasien_m ON ((penanggungbiaya_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN carabayar_m ON ((penanggungbiaya_t.carabayar_id = carabayar_m.carabayar_id)))
            ;");
            $this->execute('
                ALTER TABLE public.sy_penanggungbiaya_v OWNER TO postgres;
            ');

            $this->execute('DROP VIEW if exists public.sy_pendaftaran_v;');
        $this->execute("
            CREATE VIEW \"public\".\"sy_pendaftaran_v\" AS
            SELECT pendaftaran_t.pendaftaran_id,
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
            pendaftaran_t.pegawai_id,
            pegawai_m.nama_pegawai,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.caramasuk_id,
            caramasuk_m.caramasuk_nama,
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
            pendaftaran_t.additional_data
            FROM ((((((((((((((((pendaftaran_t
            LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
            LEFT JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            LEFT JOIN pembayaranpelayanan_t ON ((pendaftaran_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
            JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
            LEFT JOIN antrian_t ON ((pendaftaran_t.antrian_id = antrian_t.antrian_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
            LEFT JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
            LEFT JOIN lookup_m kunj ON (((pendaftaran_t.kunjungan)::integer = kunj.lookup_id)))
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
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210308_050329_migrate_20210308_3395_view_santoyusup cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210308_050329_migrate_20210308_3395_view_santoyusup cannot be reverted.\n";

        return false;
    }
    */
}

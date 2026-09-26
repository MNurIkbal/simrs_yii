<?php

use yii\db\Migration;

/**
 * Class m220717_143936_migrate_MHG2867_laporankunjunganrd_v
 */
class m220717_143936_migrate_MHG2867_laporankunjunganrd_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."laporankunjunganrd_v";');
        $this->execute("CREATE OR REPLACE VIEW public.laporankunjunganrd_v
        AS SELECT pasien_m.pasien_id,
            pasien_m.no_identitas_pasien,
            pasien_m.nama_pasien,
            pasien_m.nama_bin,
            pasien_m.jeniskelamin,
            pasien_m.tempat_lahir,
            pasien_m.tanggal_lahir,
            pasien_m.alamat_pasien,
            pasien_m.rt,
            pasien_m.rw,
            pasien_m.photopasien,
            pasien_m.alamatemail,
            pasien_m.statusrekammedis,
            pasien_m.statusperkawinan,
            pasien_m.no_rekam_medik,
            pasien_m.tgl_rekam_medik,
            pasien_m.propinsi_id,
            propinsi_m.propinsi_nama,
            pasien_m.kabupaten_id,
            kabupaten_m.kabupaten_nama,
            pasien_m.kecamatan_id,
            kecamatan_m.kecamatan_nama,
            pasien_m.kelurahan_id,
            kelurahan_m.kelurahan_nama,
            pendaftaran_t.pendaftaran_id,
            pekerjaan_m.pekerjaan_id,
            pekerjaan_m.pekerjaan_nama,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_urutantri,
            pendaftaran_t.transportasi,
            pendaftaran_t.keadaan_masuk,
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
            pegawai_m.nama_pegawai,
            pendaftaran_t.rujukan_id,
            pendaftaran_t.pasienpulang_id,
            asuransipasien_m.status_konfirmasi,
            asuransipasien_m.tgl_konfirmasi,
            pendaftaran_t.pegawai_id,
            pendaftaran_t.pembayaranpelayanan_id,
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
            pendaftaran_t.status_periksa AS status_periksa_id,
            look_status_periksa.status_periksa,
            look_jeniskelamin.jeniskelamin AS jenis_kelamin,
            look_agama.agama,
            look_statusperkawinan.statusperkawinan AS status_perkawinan,
            look_jenisidentitas.jenisidentitas,
            look_namadepan.namadepan,
            look_golongandarah.golongandarah,
            look_status_pasien.status_pasien,
            look_kunjungan.kunjungan,
            look_gelardepan.gelardepan,
            gelarbelakang.gelarbelakang_nama,
            look_rhesus.rhesus,
            bpjs_t.nosep,
            bpjs_t.bpjs_id,
            pendaftaran_t.asuransipasien_id,
            pendaftaran_t.last_modified_date AS tgl_update_terakhir,
            petugas_pemakai.nama_pegawai AS petugas_nama,
            pendaftaran_t.created_date AS tgl_pembuatan,
            petugas_pembuat.nama_pegawai AS pembuat_nama,
            pendaftaran_t.penanggungbiaya_id,
            carabayar_m.carabayar_kode_warna,
                CASE
                    WHEN antrian_poli.jenisantrian_id = 312 THEN antrian_poli.no_antrian::text
                    ELSE '-'::text
                END AS no_antrian_poli,
            pendaftaran_t.limit_tagihan,
            dokter_pengganti.nama_pegawai AS dokter_pengganti,
            pendaftaran_t.dokterpengganti_id,
            pendaftaran_t.status_bayar,
            bpjs_t.nokartuasuransi,
            carabayar_m.groupcarabayar_id,
            look_groupcarabayar.lookup_name AS groupcarabayar_nama
           FROM pendaftaran_t
             JOIN ( SELECT a.pasien_id,
                    a.no_identitas_pasien,
                    a.nama_pasien,
                    a.nama_bin,
                    a.jeniskelamin,
                    a.tempat_lahir,
                    a.tanggal_lahir,
                    a.alamat_pasien,
                    a.rt,
                    a.rw,
                    a.photopasien,
                    a.alamatemail,
                    a.statusrekammedis,
                    a.statusperkawinan,
                    a.no_rekam_medik,
                    a.tgl_rekam_medik,
                    a.propinsi_id,
                    a.kabupaten_id,
                    a.kecamatan_id,
                    a.kelurahan_id,
                    a.anakke,
                    a.jumlah_bersaudara,
                    a.no_telepon_pasien,
                    a.no_mobile_pasien,
                    a.warga_negara,
                    a.nama_ibu,
                    a.nama_ayah,
                    a.is_deleted,
                    a.pekerjaan_id,
                    a.suku_id,
                    a.pendidikan_id,
                    a.agama,
                    a.jenisidentitas,
                    a.namadepan,
                    a.golongandarah,
                    a.rhesus
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama,
                    a.carabayar_kode_warna,
                    a.groupcarabayar_id
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.caramasuk_id,
                    a.caramasuk_nama
                   FROM caramasuk_m a) caramasuk_m ON pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id
             LEFT JOIN ( SELECT a.golonganumur_id,
                    a.golonganumur_nama
                   FROM golonganumur_m a) golonganumur_m ON pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id
             LEFT JOIN ( SELECT a.rujukan_id,
                    a.rujukandari_id,
                    a.asalrujukan_id,
                    a.no_rujukan,
                    a.nama_perujuk,
                    a.tanggal_rujukan,
                    a.kodediagnosa_rujukan
                   FROM rujukan_t a) rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
             LEFT JOIN ( SELECT a.penanggungjawab_id,
                    a.penanggungjawab_nama,
                    a.pengantar,
                    a.hubungankeluarga
                   FROM penanggungjawab_m a) penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             LEFT JOIN ( SELECT a.antrian_id
                   FROM antrian_t a) antrian_t ON pendaftaran_t.antrian_id = antrian_t.antrian_id
             LEFT JOIN ( SELECT a.antrian_id,
                    a.pendaftaran_id,
                    a.jenisantrian_id,
                    a.no_antrian
                   FROM antrian_t a) antrian_poli ON pendaftaran_t.pendaftaran_id = antrian_poli.pendaftaran_id AND antrian_poli.jenisantrian_id = 312
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai,
                    a.gelarbelakang,
                    a.gelardepan
                   FROM pegawai_m a) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) dokter_pengganti ON pendaftaran_t.dokterpengganti_id = dokter_pengganti.pegawai_id
             LEFT JOIN ( SELECT loginpemakai_k.loginpemakai_id,
                    loginpemakai_k.pegawai_id
                   FROM loginpemakai_k) petugas ON pendaftaran_t.last_modified_by = petugas.loginpemakai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) petugas_pemakai ON petugas.pegawai_id = petugas_pemakai.pegawai_id
             LEFT JOIN ( SELECT a.loginpemakai_id,
                    a.pegawai_id
                   FROM loginpemakai_k a) pembuat ON pendaftaran_t.created_by = pembuat.loginpemakai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) petugas_pembuat ON pembuat.pegawai_id = petugas_pembuat.pegawai_id
             LEFT JOIN ( SELECT pasienpulang_t_1.pasienpulang_id,
                    pasienpulang_t_1.carakeluar_id,
                    pasienpulang_t_1.kondisikeluar_id
                   FROM pasienpulang_t pasienpulang_t_1) pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN ( SELECT a.asuransipasien_id,
                    a.nokartuasuransi,
                    a.namapemilikasuransi,
                    a.nomorpokokperusahaan,
                    a.status_konfirmasi,
                    a.tgl_konfirmasi,
                    a.nopeserta,
                    a.tglcetakkartuasuransi,
                    a.kodefeskestk1,
                    a.nama_feskestk1,
                    a.masaberlakukartu,
                    a.nokartukeluarga,
                    a.nopassport,
                    a.is_active
                   FROM asuransipasien_m a) asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id
             LEFT JOIN ( SELECT a.pekerjaan_id,
                    a.pekerjaan_nama
                   FROM pekerjaan_m a) pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
             LEFT JOIN ( SELECT a.suku_id,
                    a.suku_nama
                   FROM suku_m a) suku_m ON pasien_m.suku_id = suku_m.suku_id
             LEFT JOIN ( SELECT a.pendidikan_id,
                    a.pendidikan_nama
                   FROM pendidikan_m a) pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
             LEFT JOIN ( SELECT a.asalrujukan_id,
                    a.asalrujukan_nama
                   FROM asalrujukan_m a) asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
             LEFT JOIN ( SELECT a.carakeluar_id,
                    a.carakeluar_nama
                   FROM carakeluar_m a) carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
             LEFT JOIN ( SELECT a.kondisikeluar_id,
                    a.kondisikeluar_nama
                   FROM kondisikeluar_m a) kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
             LEFT JOIN ( SELECT a.gelarbelakang_id,
                    a.gelarbelakang_nama
                   FROM gelarbelakang_m a) gelarbelakang ON pegawai_m.gelarbelakang::integer = gelarbelakang.gelarbelakang_id
             LEFT JOIN ( SELECT a.bpjs_id,
                    a.is_deleted,
                    a.nosep,
                    a.nokartuasuransi
                   FROM bpjs_t a) bpjs_t ON pendaftaran_t.bpjs_id = bpjs_t.bpjs_id AND bpjs_t.is_deleted = false
             LEFT JOIN ( SELECT a.propinsi_id,
                    a.propinsi_nama
                   FROM propinsi_m a) propinsi_m ON pasien_m.propinsi_id = propinsi_m.propinsi_id
             LEFT JOIN ( SELECT a.kabupaten_id,
                    a.kabupaten_nama
                   FROM kabupaten_m a) kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
             LEFT JOIN ( SELECT a.kelurahan_id,
                    a.kelurahan_nama
                   FROM kelurahan_m a) kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
             LEFT JOIN ( SELECT a.kecamatan_id,
                    a.kecamatan_nama
                   FROM kecamatan_m a) kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS status_periksa
                   FROM lookup_m a) look_status_periksa ON pendaftaran_t.status_periksa::integer = look_status_periksa.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS jeniskelamin
                   FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS agama
                   FROM lookup_m a) look_agama ON pasien_m.agama::integer = look_agama.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS statusperkawinan
                   FROM lookup_m a) look_statusperkawinan ON pasien_m.statusperkawinan::integer = look_statusperkawinan.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS jenisidentitas
                   FROM lookup_m a) look_jenisidentitas ON pasien_m.jenisidentitas::integer = look_jenisidentitas.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS namadepan
                   FROM lookup_m a) look_namadepan ON pasien_m.namadepan::integer = look_namadepan.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS golongandarah
                   FROM lookup_m a) look_golongandarah ON pasien_m.golongandarah::integer = look_golongandarah.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS status_pasien
                   FROM lookup_m a) look_status_pasien ON pendaftaran_t.status_pasien::integer = look_status_pasien.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS kunjungan
                   FROM lookup_m a) look_kunjungan ON pendaftaran_t.kunjungan::integer = look_kunjungan.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS gelardepan
                   FROM lookup_m a) look_gelardepan ON pegawai_m.gelardepan::integer = look_gelardepan.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name AS rhesus
                   FROM lookup_m a) look_rhesus ON pasien_m.rhesus::integer = look_rhesus.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_groupcarabayar ON carabayar_m.groupcarabayar_id = look_groupcarabayar.lookup_id
          WHERE instalasi_m.instalasi_id = 2 AND (pendaftaran_t.status_periksa::text <> ALL (ARRAY['402'::text, '411'::text, '628'::text, '1058'::text]));");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220717_143936_migrate_MHG2867_laporankunjunganrd_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220717_143936_migrate_MHG2867_laporankunjunganrd_v cannot be reverted.\n";

        return false;
    }
    */
}

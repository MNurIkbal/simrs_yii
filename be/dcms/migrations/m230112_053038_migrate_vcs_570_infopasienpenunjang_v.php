<?php

use yii\db\Migration;

/**
 * Class m230112_053038_migrate_vcs_570_infopasienpenunjang_v
 */
class m230112_053038_migrate_vcs_570_infopasienpenunjang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infopasienpenunjang_v";
        ');

        $this->execute("
            CREATE OR REPLACE VIEW public.infopasienpenunjang_v
            AS SELECT 'pasienpenunjang'::text AS ket,
                pasienmasukpenunjang_t.pasienmasukpenunjang_id,
                pasienmasukpenunjang_t.pendaftaran_id,
                pendaftaran_t.tgl_pendaftaran,
                pendaftaran_t.pasien_id,
                pasien_m.no_rekam_medik,
                look_namadepan.lookup_name AS nama_depan,
                pasien_m.nama_pasien,
                pasien_m.alamat_pasien,
                look_jeniskelamin.lookup_name AS jeniskelamin,
                ruangan_m.ruangan_id,
                ruangan_m.ruangan_nama AS ruangan_penunjang,
                ruangasal.ruangan_nama AS ruangan_asal,
                pasienmasukpenunjang_t.no_masukpenunjang,
                pasienmasukpenunjang_t.tglmasukpenunjang,
                kelaspelayanan_m.kelaspelayanan_nama,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                pasienmasukpenunjang_t.status_periksa,
                ruangan_m.instalasi_id,
                pendaftaran_t.created_by,
                ruangan_m.ruangan_nama,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.umur,
                look_jeniskelamin.lookup_name AS jenis_kelamin,
                pegawai_m.nama_pegawai,
                carabayar_m.carabayar_id,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_id,
                penjamin_m.penjamin_nama,
                pasienadmisi_t.pasienadmisi_id,
                pasienadmisi_t.is_pasientitipan,
                pasienadmisi_t.kelas_ditagihkan_id,
                kelas_ditagihkan.kelaspelayanan_nama AS kelas_ditagihkan_nama,
                pasienadmisi_t.kamar_titipan_id,
                kamar_ditagihkan.kamarruangan_nokamar AS kamar_titipan_nama,
                pasienadmisi_t.ruangan_titipan_id,
                ruangan_ditagihkan.ruangan_nama AS ruangan_titipan_nama,
                pasienadmisi_t.is_stoptitipan,
                look_statusperiksa_pendaftaran.lookup_name AS nama_status_periksa,
                pasien_m.tanggal_lahir,
                pendaftaran_t.keterangan_pendaftaran,
                jeniskasuspenyakit_m.jeniskasuspenyakit_id,
                pegawai_m.pegawai_id,
                kelaspelayanan_m.kelaspelayanan_id,
                pendaftaran_t.asuransipasien_id,
                pendaftaran_t.status_periksa AS status_periksa_id,
                pendaftaran_t.instalasi_id AS instalasiasal_id,
                pendaftaran_t.last_modified_date AS tgl_update_terakhir,
                petugas_pemakai.nama_pegawai AS petugas_nama,
                pendaftaran_t.created_date AS tgl_pembuatan,
                petugas_pembuat.nama_pegawai AS pembuat_nama,
                pasien_m.additional_pasien,
                pendaftaran_t.penanggungbiaya_id,
                carabayar_m.carabayar_kode_warna,
                    CASE
                        WHEN antrian_poli.jenisantrian_id = 312 THEN antrian_poli.no_antrian::text
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
                lookup_bahasasehari.lookup_name AS bahasa_sehari_nama,
                penanggungjawab_m.pj_namadepan,
                lookup_pjnamadepan.lookup_name AS pj_namadepan_nama,
                pendaftaran_t.limit_tagihan,
                pendaftaran_t.dokterpengganti_id,
                dokterpengganti.nama_pegawai AS dokterpengganti_nama,
                pendaftaran_t.diagnosa AS pemeriksaan,
                dokter_pengganti.nama_pegawai AS dokter_pengganti,
                pendaftaran_t.styrujukaninstalasi_id,
                instalasi_m.instalasi_nama,
                pendaftaran_t.dokterpengirim_id,
                pendaftaran_t.status_bayar,
                COALESCE(bpjs_admisi.nokartuasuransi, bpjs_pendaftaran.nokartuasuransi) AS nokartuasuransi,
                carabayar_m.groupcarabayar_id,
                look_groupcarabayar.lookup_name AS groupcarabayar_nama,
                pasienmasukpenunjang_t.is_exception,
                asuransipasien_m.nokartuasuransi AS no_asuransi,
                asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
                asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan
            FROM pasienmasukpenunjang_t
                LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id
                    FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id
                JOIN ( SELECT a.tgl_pendaftaran,
                        a.pasien_id,
                        a.created_by,
                        a.no_pendaftaran,
                        a.umur,
                        a.keterangan_pendaftaran,
                        a.asuransipasien_id,
                        a.status_periksa,
                        a.instalasi_id,
                        a.last_modified_date,
                        a.created_date,
                        a.penanggungbiaya_id,
                        a.limit_tagihan,
                        a.dokterpengganti_id,
                        a.diagnosa,
                        a.styrujukaninstalasi_id,
                        a.dokterpengirim_id,
                        a.status_bayar,
                        a.pendaftaran_id,
                        a.antrian_id,
                        a.pasienadmisi_id,
                        a.penanggungjawab_id,
                        a.last_modified_by,
                        a.carabayar_id,
                        a.penjamin_id,
                        a.bpjs_id
                    FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                JOIN ( SELECT a.no_rekam_medik,
                        a.nama_pasien,
                        a.alamat_pasien,
                        a.tanggal_lahir,
                        a.additional_pasien,
                        a.catatanpenting_pasien,
                        a.bahasa_sehari,
                        a.namadepan,
                        a.jeniskelamin,
                        a.pasien_id
                    FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                JOIN ( SELECT a.ruangan_id,
                        a.ruangan_nama,
                        a.instalasi_id
                    FROM ruangan_m a) ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
                JOIN ( SELECT a.ruangan_id,
                        a.ruangan_nama
                    FROM ruangan_m a) ruangasal ON pasienmasukpenunjang_t.ruanganasal_id = ruangasal.ruangan_id
                JOIN ( SELECT a.kelaspelayanan_id,
                        a.kelaspelayanan_nama
                    FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienmasukpenunjang_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                JOIN ( SELECT a.jeniskasuspenyakit_id,
                        a.jeniskasuspenyakit_nama
                    FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pasienmasukpenunjang_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
                LEFT JOIN ( SELECT a.antrian_id
                    FROM antrian_t a) antrian_t ON pendaftaran_t.antrian_id = antrian_t.antrian_id
                LEFT JOIN ( SELECT a.jenisantrian_id,
                        a.no_antrian,
                        a.pendaftaran_id
                    FROM antrian_t a) antrian_poli ON pendaftaran_t.pendaftaran_id = antrian_poli.pendaftaran_id AND antrian_poli.jenisantrian_id = 312
                LEFT JOIN ( SELECT a.pasienadmisi_id,
                        a.is_pasientitipan,
                        a.kelas_ditagihkan_id,
                        a.kamar_titipan_id,
                        a.ruangan_titipan_id,
                        a.is_stoptitipan,
                        a.bpjs_id
                    FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                    FROM pegawai_m a) pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
                LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                    FROM pegawai_m a) dokter_pengganti ON pendaftaran_t.dokterpengganti_id = dokter_pengganti.pegawai_id
                LEFT JOIN ( SELECT a.penanggungjawab_alamat,
                        a.penanggungjawab_notelp,
                        a.pj_propinsi_id,
                        a.pj_kabupaten_id,
                        a.pj_kecamatan_id,
                        a.pj_kelurahan_id,
                        a.pj_namadepan,
                        a.pj_pekerjaan_id,
                        a.penanggungjawab_id
                    FROM penanggungjawab_m a) penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
                LEFT JOIN ( SELECT a.pekerjaan_id,
                        a.pekerjaan_nama
                    FROM pekerjaan_m a) pj_kerja ON penanggungjawab_m.pj_pekerjaan_id = pj_kerja.pekerjaan_id
                LEFT JOIN ( SELECT a.propinsi_id,
                        a.propinsi_nama
                    FROM propinsi_m a) pj_prop ON penanggungjawab_m.pj_propinsi_id = pj_prop.propinsi_id
                LEFT JOIN ( SELECT a.kabupaten_id,
                        a.kabupaten_nama
                    FROM kabupaten_m a) pj_kab ON penanggungjawab_m.pj_kabupaten_id = pj_kab.kabupaten_id
                LEFT JOIN ( SELECT a.kecamatan_id,
                        a.kecamatan_nama
                    FROM kecamatan_m a) pj_kec ON penanggungjawab_m.pj_kecamatan_id = pj_kec.kecamatan_id
                LEFT JOIN ( SELECT a.kelurahan_id,
                        a.kelurahan_nama
                    FROM kelurahan_m a) pj_kel ON penanggungjawab_m.pj_kelurahan_id = pj_kel.kelurahan_id
                LEFT JOIN ( SELECT a.loginpemakai_id,
                        a.pegawai_id
                    FROM loginpemakai_k a) petugas ON pendaftaran_t.last_modified_by = petugas.loginpemakai_id
                LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                    FROM pegawai_m a) petugas_pemakai ON petugas.pegawai_id = petugas_pemakai.pegawai_id
                LEFT JOIN ( SELECT a.loginpemakai_id,
                        a.pegawai_id
                    FROM loginpemakai_k a) pembuat ON pendaftaran_t.created_by = pembuat.loginpemakai_id
                LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                    FROM pegawai_m a) petugas_pembuat ON pembuat.pegawai_id = petugas_pembuat.pegawai_id
                LEFT JOIN ( SELECT a.carabayar_id,
                        a.carabayar_nama,
                        a.carabayar_kode_warna,
                        a.groupcarabayar_id
                    FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
                LEFT JOIN ( SELECT a.penjamin_id,
                        a.penjamin_nama
                    FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
                LEFT JOIN ( SELECT a.kelaspelayanan_id,
                        a.kelaspelayanan_nama
                    FROM kelaspelayanan_m a) kelas_ditagihkan ON pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id
                LEFT JOIN ( SELECT a.kamarruangan_id,
                        a.kamarruangan_nokamar
                    FROM kamarruangan_m a) kamar_ditagihkan ON pasienadmisi_t.kamar_titipan_id = kamar_ditagihkan.kamarruangan_id
                LEFT JOIN ( SELECT a.ruangan_id,
                        a.ruangan_nama
                    FROM ruangan_m a) ruangan_ditagihkan ON pasienadmisi_t.ruangan_titipan_id = ruangan_ditagihkan.ruangan_id
                LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                    FROM pegawai_m a) dokterpengganti ON pendaftaran_t.dokterpengganti_id = pegawai_m.pegawai_id
                LEFT JOIN ( SELECT a.instalasi_id,
                        a.instalasi_nama
                    FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
                LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name
                    FROM lookup_m a) look_namadepan ON pasien_m.namadepan::integer = look_namadepan.lookup_id
                LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name
                    FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
                LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name
                    FROM lookup_m a) look_statusperiksa_penunjang ON pasienmasukpenunjang_t.status_periksa::integer = look_statusperiksa_penunjang.lookup_id
                LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name
                    FROM lookup_m a) lookup_bahasasehari ON pasien_m.bahasa_sehari::integer = lookup_bahasasehari.lookup_id
                LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name
                    FROM lookup_m a) lookup_pjnamadepan ON penanggungjawab_m.pj_namadepan::integer = lookup_pjnamadepan.lookup_id
                LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name
                    FROM lookup_m a) look_statusperiksa_pendaftaran ON pendaftaran_t.status_periksa::integer = look_statusperiksa_pendaftaran.lookup_id
                LEFT JOIN ( SELECT a.bpjs_id,
                        a.nokartuasuransi
                    FROM bpjs_t a) bpjs_pendaftaran ON pendaftaran_t.bpjs_id = bpjs_pendaftaran.bpjs_id
                LEFT JOIN ( SELECT a.bpjs_id,
                        a.nokartuasuransi
                    FROM bpjs_t a) bpjs_admisi ON pasienadmisi_t.bpjs_id = bpjs_admisi.bpjs_id
                LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name
                    FROM lookup_m a) look_groupcarabayar ON carabayar_m.groupcarabayar_id = look_groupcarabayar.lookup_id
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
            WHERE pasienmasukpenunjang_t.is_active = true AND pasienmasukpenunjang_t.is_deleted = false AND pendaftaran_t.instalasi_id <> 7
            UNION ALL
            SELECT 'fisioterapi'::text AS ket,
                pendaftaran_t.pendaftaran_id AS pasienmasukpenunjang_id,
                pendaftaran_t.pendaftaran_id,
                pendaftaran_t.tgl_pendaftaran,
                pendaftaran_t.pasien_id,
                pasien_m.no_rekam_medik,
                look_namadepan.lookup_name AS nama_depan,
                pasien_m.nama_pasien,
                pasien_m.alamat_pasien,
                look_jeniskelamin.lookup_name AS jeniskelamin,
                ruangan_m.ruangan_id,
                ruangan_m.ruangan_nama AS ruangan_penunjang,
                ruangasal.ruangan_nama AS ruangan_asal,
                pendaftaran_t.no_pendaftaran AS no_masukpenunjang,
                pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
                kelaspelayanan_m.kelaspelayanan_nama,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                pendaftaran_t.status_periksa,
                ruangan_m.instalasi_id,
                pendaftaran_t.created_by,
                ruangan_m.ruangan_nama,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.umur,
                look_jeniskelamin.lookup_name AS jenis_kelamin,
                pegawai_m.nama_pegawai,
                carabayar_m.carabayar_id,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_id,
                penjamin_m.penjamin_nama,
                pasienadmisi_t.pasienadmisi_id,
                pasienadmisi_t.is_pasientitipan,
                pasienadmisi_t.kelas_ditagihkan_id,
                kelas_ditagihkan.kelaspelayanan_nama AS kelas_ditagihkan_nama,
                pasienadmisi_t.kamar_titipan_id,
                kamar_ditagihkan.kamarruangan_nokamar AS kamar_titipan_nama,
                pasienadmisi_t.ruangan_titipan_id,
                ruangan_ditagihkan.ruangan_nama AS ruangan_titipan_nama,
                pasienadmisi_t.is_stoptitipan,
                look_statusperiksa.lookup_name AS nama_status_periksa,
                pasien_m.tanggal_lahir,
                pendaftaran_t.keterangan_pendaftaran,
                jeniskasuspenyakit_m.jeniskasuspenyakit_id,
                pegawai_m.pegawai_id,
                kelaspelayanan_m.kelaspelayanan_id,
                pendaftaran_t.asuransipasien_id,
                pendaftaran_t.status_periksa AS status_periksa_id,
                pendaftaran_t.instalasi_id AS instalasiasal_id,
                pendaftaran_t.last_modified_date AS tgl_update_terakhir,
                petugas_pemakai.nama_pegawai AS petugas_nama,
                pendaftaran_t.created_date AS tgl_pembuatan,
                petugas_pembuat.nama_pegawai AS pembuat_nama,
                pasien_m.additional_pasien,
                pendaftaran_t.penanggungbiaya_id,
                carabayar_m.carabayar_kode_warna,
                    CASE
                        WHEN antrian_poli.jenisantrian_id = 312 THEN antrian_poli.no_antrian::text
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
                lookup_bahasasehari.lookup_name AS bahasa_sehari_nama,
                penanggungjawab_m.pj_namadepan,
                lookup_pjnamadepan.lookup_name AS pj_namadepan_nama,
                pendaftaran_t.limit_tagihan,
                pendaftaran_t.dokterpengganti_id,
                dokterpengganti.nama_pegawai AS dokterpengganti_nama,
                pendaftaran_t.diagnosa AS pemeriksaan,
                dokter_pengganti.nama_pegawai AS dokter_pengganti,
                pendaftaran_t.styrujukaninstalasi_id,
                instalasi_m.instalasi_nama,
                pendaftaran_t.dokterpengirim_id,
                pendaftaran_t.status_bayar,
                COALESCE(bpjs_admisi.nokartuasuransi, bpjs_pendaftaran.nokartuasuransi) AS nokartuasuransi,
                carabayar_m.groupcarabayar_id,
                look_groupcarabayar.lookup_name AS groupcarabayar_nama,
                NULL::boolean AS is_exception,
                asuransipasien_m.nokartuasuransi AS no_asuransi,
                asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
                asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan
            FROM pendaftaran_t
                JOIN ( SELECT a.pasien_id,
                        a.no_rekam_medik,
                        a.nama_pasien,
                        a.alamat_pasien,
                        a.tanggal_lahir,
                        a.additional_pasien,
                        a.catatanpenting_pasien,
                        a.bahasa_sehari,
                        a.namadepan,
                        a.jeniskelamin
                    FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                JOIN ( SELECT a.ruangan_id,
                        a.ruangan_nama,
                        a.instalasi_id
                    FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
                JOIN ( SELECT a.ruangan_id,
                        a.ruangan_nama
                    FROM ruangan_m a) ruangasal ON pendaftaran_t.ruangan_id = ruangasal.ruangan_id
                JOIN ( SELECT a.kelaspelayanan_id,
                        a.kelaspelayanan_nama
                    FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                JOIN ( SELECT a.jeniskasuspenyakit_id,
                        a.jeniskasuspenyakit_nama
                    FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
                LEFT JOIN ( SELECT a.antrian_id
                    FROM antrian_t a) antrian_t ON pendaftaran_t.antrian_id = antrian_t.antrian_id
                LEFT JOIN ( SELECT a.pendaftaran_id,
                        a.jenisantrian_id,
                        a.no_antrian
                    FROM antrian_t a) antrian_poli ON pendaftaran_t.pendaftaran_id = antrian_poli.pendaftaran_id AND antrian_poli.jenisantrian_id = 312
                LEFT JOIN ( SELECT a.pasienadmisi_id,
                        a.is_pasientitipan,
                        a.kelas_ditagihkan_id,
                        a.kamar_titipan_id,
                        a.ruangan_titipan_id,
                        a.is_stoptitipan,
                        a.bpjs_id
                    FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                    FROM pegawai_m a) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
                LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                    FROM pegawai_m a) dokter_pengganti ON pendaftaran_t.dokterpengganti_id = dokter_pengganti.pegawai_id
                LEFT JOIN ( SELECT a.penanggungjawab_alamat,
                        a.penanggungjawab_notelp,
                        a.pj_pekerjaan_id,
                        a.pj_propinsi_id,
                        a.pj_kabupaten_id,
                        a.pj_kecamatan_id,
                        a.pj_kelurahan_id,
                        a.pj_namadepan,
                        a.penanggungjawab_id
                    FROM penanggungjawab_m a) penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
                LEFT JOIN ( SELECT a.pekerjaan_id,
                        a.pekerjaan_nama
                    FROM pekerjaan_m a) pj_kerja ON penanggungjawab_m.pj_pekerjaan_id = pj_kerja.pekerjaan_id
                LEFT JOIN ( SELECT a.propinsi_id,
                        a.propinsi_nama
                    FROM propinsi_m a) pj_prop ON penanggungjawab_m.pj_propinsi_id = pj_prop.propinsi_id
                LEFT JOIN ( SELECT a.kabupaten_id,
                        a.kabupaten_nama
                    FROM kabupaten_m a) pj_kab ON penanggungjawab_m.pj_kabupaten_id = pj_kab.kabupaten_id
                LEFT JOIN ( SELECT a.kecamatan_id,
                        a.kecamatan_nama
                    FROM kecamatan_m a) pj_kec ON penanggungjawab_m.pj_kecamatan_id = pj_kec.kecamatan_id
                LEFT JOIN ( SELECT a.kelurahan_id,
                        a.kelurahan_nama
                    FROM kelurahan_m a) pj_kel ON penanggungjawab_m.pj_kelurahan_id = pj_kel.kelurahan_id
                LEFT JOIN ( SELECT a.loginpemakai_id,
                        a.pegawai_id
                    FROM loginpemakai_k a) petugas ON pendaftaran_t.last_modified_by = petugas.loginpemakai_id
                LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                    FROM pegawai_m a) petugas_pemakai ON petugas.pegawai_id = petugas_pemakai.pegawai_id
                LEFT JOIN ( SELECT a.loginpemakai_id,
                        a.pegawai_id
                    FROM loginpemakai_k a) pembuat ON pendaftaran_t.created_by = pembuat.loginpemakai_id
                LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                    FROM pegawai_m a) petugas_pembuat ON pembuat.pegawai_id = petugas_pembuat.pegawai_id
                LEFT JOIN ( SELECT a.carabayar_id,
                        a.carabayar_nama,
                        a.carabayar_kode_warna,
                        a.groupcarabayar_id
                    FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
                LEFT JOIN ( SELECT a.penjamin_id,
                        a.penjamin_nama
                    FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
                LEFT JOIN ( SELECT a.kelaspelayanan_id,
                        a.kelaspelayanan_nama
                    FROM kelaspelayanan_m a) kelas_ditagihkan ON pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id
                LEFT JOIN ( SELECT a.kamarruangan_id,
                        a.kamarruangan_nokamar
                    FROM kamarruangan_m a) kamar_ditagihkan ON pasienadmisi_t.kamar_titipan_id = kamar_ditagihkan.kamarruangan_id
                LEFT JOIN ( SELECT a.ruangan_id,
                        a.ruangan_nama
                    FROM ruangan_m a) ruangan_ditagihkan ON pasienadmisi_t.ruangan_titipan_id = ruangan_ditagihkan.ruangan_id
                LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                    FROM pegawai_m a) dokterpengganti ON pendaftaran_t.dokterpengganti_id = pegawai_m.pegawai_id
                LEFT JOIN ( SELECT a.instalasi_id,
                        a.instalasi_nama
                    FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
                LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name
                    FROM lookup_m a) look_namadepan ON pasien_m.namadepan::integer = look_namadepan.lookup_id
                LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name
                    FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
                LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name
                    FROM lookup_m a) look_statusperiksa ON pendaftaran_t.status_periksa::integer = look_statusperiksa.lookup_id
                LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name
                    FROM lookup_m a) lookup_bahasasehari ON pasien_m.bahasa_sehari::integer = lookup_bahasasehari.lookup_id
                LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name
                    FROM lookup_m a) lookup_pjnamadepan ON penanggungjawab_m.pj_namadepan::integer = lookup_pjnamadepan.lookup_id
                LEFT JOIN ( SELECT a.bpjs_id,
                        a.nokartuasuransi
                    FROM bpjs_t a) bpjs_pendaftaran ON pendaftaran_t.bpjs_id = bpjs_pendaftaran.bpjs_id
                LEFT JOIN ( SELECT a.bpjs_id,
                        a.nokartuasuransi
                    FROM bpjs_t a) bpjs_admisi ON pasienadmisi_t.bpjs_id = bpjs_admisi.bpjs_id
                LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name
                    FROM lookup_m a) look_groupcarabayar ON carabayar_m.groupcarabayar_id = look_groupcarabayar.lookup_id
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
            WHERE pendaftaran_t.is_active = true AND pendaftaran_t.is_deleted = false AND pendaftaran_t.instalasi_id = 7;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230112_053038_migrate_vcs_570_infopasienpenunjang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230112_053038_migrate_vcs_570_infopasienpenunjang_v cannot be reverted.\n";

        return false;
    }
    */
}

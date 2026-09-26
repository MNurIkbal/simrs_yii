<?php

use yii\db\Migration;

/**
 * Class m230307_060307_migrate_infopasienpenunjang_v
 */
class m230307_060307_migrate_infopasienpenunjang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infopasienpenunjang_v";
        ');

        $this->execute('
        CREATE VIEW "public"."infopasienpenunjang_v" AS SELECT
        \'pasienpenunjang\' :: TEXT AS ket,
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
        look_statusperiksa_penunjang.lookup_name AS nama_status_periksa,
        pasien_m.tanggal_lahir,
        pendaftaran_t.keterangan_pendaftaran,
        jeniskasuspenyakit_m.jeniskasuspenyakit_id,
        pegawai_m.pegawai_id,
        kelaspelayanan_m.kelaspelayanan_id,
        pendaftaran_t.asuransipasien_id,
        pasienmasukpenunjang_t.status_periksa AS status_periksa_id,
        pendaftaran_t.instalasi_id AS instalasiasal_id,
        pendaftaran_t.last_modified_date AS tgl_update_terakhir,
        petugas_pemakai.nama_pegawai AS petugas_nama,
        pendaftaran_t.created_date AS tgl_pembuatan,
        petugas_pembuat.nama_pegawai AS pembuat_nama,
        pasien_m.additional_pasien,
        pendaftaran_t.penanggungbiaya_id,
        carabayar_m.carabayar_kode_warna,
    CASE
            
            WHEN antrian_poli.jenisantrian_id = 312 THEN
            antrian_poli.no_antrian :: TEXT ELSE \'-\' :: TEXT 
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
        COALESCE ( bpjs_admisi.nokartuasuransi, bpjs_pendaftaran.nokartuasuransi ) AS nokartuasuransi,
        carabayar_m.groupcarabayar_id,
        look_groupcarabayar.lookup_name AS groupcarabayar_nama,
        pasienmasukpenunjang_t.is_exception,
        asuransipasien_m.nokartuasuransi AS no_asuransi,
        asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
        asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan 
    FROM
        pasienmasukpenunjang_t
        LEFT JOIN ( SELECT A.pasienkirimkeunitlain_id FROM pasienkirimkeunitlain_t A ) pasienkirimkeunitlain_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id
        JOIN (
        SELECT A
            .tgl_pendaftaran,
            A.pasien_id,
            A.created_by,
            A.no_pendaftaran,
            A.umur,
            A.keterangan_pendaftaran,
            A.asuransipasien_id,
            A.status_periksa,
            A.instalasi_id,
            A.last_modified_date,
            A.created_date,
            A.penanggungbiaya_id,
            A.limit_tagihan,
            A.dokterpengganti_id,
            A.diagnosa,
            A.styrujukaninstalasi_id,
            A.dokterpengirim_id,
            A.status_bayar,
            A.pendaftaran_id,
            A.antrian_id,
            A.pasienadmisi_id,
            A.penanggungjawab_id,
            A.last_modified_by,
            A.carabayar_id,
            A.penjamin_id,
            A.bpjs_id 
        FROM
            pendaftaran_t A 
        ) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
        JOIN (
        SELECT A
            .no_rekam_medik,
            A.nama_pasien,
            A.alamat_pasien,
            A.tanggal_lahir,
            A.additional_pasien,
            A.catatanpenting_pasien,
            A.bahasa_sehari,
            A.namadepan,
            A.jeniskelamin,
            A.pasien_id 
        FROM
            pasien_m A 
        ) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
        JOIN ( SELECT A.ruangan_id, A.ruangan_nama, A.instalasi_id FROM ruangan_m A ) ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
        JOIN ( SELECT A.ruangan_id, A.ruangan_nama FROM ruangan_m A ) ruangasal ON pasienmasukpenunjang_t.ruanganasal_id = ruangasal.ruangan_id
        JOIN ( SELECT A.kelaspelayanan_id, A.kelaspelayanan_nama FROM kelaspelayanan_m A ) kelaspelayanan_m ON pasienmasukpenunjang_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
        JOIN ( SELECT A.jeniskasuspenyakit_id, A.jeniskasuspenyakit_nama FROM jeniskasuspenyakit_m A ) jeniskasuspenyakit_m ON pasienmasukpenunjang_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
        LEFT JOIN ( SELECT A.antrian_id FROM antrian_t A ) antrian_t ON pendaftaran_t.antrian_id = antrian_t.antrian_id
        LEFT JOIN ( SELECT A.jenisantrian_id, A.no_antrian, A.pendaftaran_id FROM antrian_t A ) antrian_poli ON pendaftaran_t.pendaftaran_id = antrian_poli.pendaftaran_id 
        AND antrian_poli.jenisantrian_id = 312
        LEFT JOIN (
        SELECT A
            .pasienadmisi_id,
            A.is_pasientitipan,
            A.kelas_ditagihkan_id,
            A.kamar_titipan_id,
            A.ruangan_titipan_id,
            A.is_stoptitipan,
            A.bpjs_id 
        FROM
            pasienadmisi_t A 
        ) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
        LEFT JOIN ( SELECT A.pegawai_id, A.nama_pegawai FROM pegawai_m A ) pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
        LEFT JOIN ( SELECT A.pegawai_id, A.nama_pegawai FROM pegawai_m A ) dokter_pengganti ON pendaftaran_t.dokterpengganti_id = dokter_pengganti.pegawai_id
        LEFT JOIN (
        SELECT A
            .penanggungjawab_alamat,
            A.penanggungjawab_notelp,
            A.pj_propinsi_id,
            A.pj_kabupaten_id,
            A.pj_kecamatan_id,
            A.pj_kelurahan_id,
            A.pj_namadepan,
            A.pj_pekerjaan_id,
            A.penanggungjawab_id 
        FROM
            penanggungjawab_m A 
        ) penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
        LEFT JOIN ( SELECT A.pekerjaan_id, A.pekerjaan_nama FROM pekerjaan_m A ) pj_kerja ON penanggungjawab_m.pj_pekerjaan_id = pj_kerja.pekerjaan_id
        LEFT JOIN ( SELECT A.propinsi_id, A.propinsi_nama FROM propinsi_m A ) pj_prop ON penanggungjawab_m.pj_propinsi_id = pj_prop.propinsi_id
        LEFT JOIN ( SELECT A.kabupaten_id, A.kabupaten_nama FROM kabupaten_m A ) pj_kab ON penanggungjawab_m.pj_kabupaten_id = pj_kab.kabupaten_id
        LEFT JOIN ( SELECT A.kecamatan_id, A.kecamatan_nama FROM kecamatan_m A ) pj_kec ON penanggungjawab_m.pj_kecamatan_id = pj_kec.kecamatan_id
        LEFT JOIN ( SELECT A.kelurahan_id, A.kelurahan_nama FROM kelurahan_m A ) pj_kel ON penanggungjawab_m.pj_kelurahan_id = pj_kel.kelurahan_id
        LEFT JOIN ( SELECT A.loginpemakai_id, A.pegawai_id FROM loginpemakai_k A ) petugas ON pendaftaran_t.last_modified_by = petugas.loginpemakai_id
        LEFT JOIN ( SELECT A.pegawai_id, A.nama_pegawai FROM pegawai_m A ) petugas_pemakai ON petugas.pegawai_id = petugas_pemakai.pegawai_id
        LEFT JOIN ( SELECT A.loginpemakai_id, A.pegawai_id FROM loginpemakai_k A ) pembuat ON pendaftaran_t.created_by = pembuat.loginpemakai_id
        LEFT JOIN ( SELECT A.pegawai_id, A.nama_pegawai FROM pegawai_m A ) petugas_pembuat ON pembuat.pegawai_id = petugas_pembuat.pegawai_id
        LEFT JOIN ( SELECT A.carabayar_id, A.carabayar_nama, A.carabayar_kode_warna, A.groupcarabayar_id FROM carabayar_m A ) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
        LEFT JOIN ( SELECT A.penjamin_id, A.penjamin_nama FROM penjamin_m A ) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
        LEFT JOIN ( SELECT A.kelaspelayanan_id, A.kelaspelayanan_nama FROM kelaspelayanan_m A ) kelas_ditagihkan ON pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id
        LEFT JOIN ( SELECT A.kamarruangan_id, A.kamarruangan_nokamar FROM kamarruangan_m A ) kamar_ditagihkan ON pasienadmisi_t.kamar_titipan_id = kamar_ditagihkan.kamarruangan_id
        LEFT JOIN ( SELECT A.ruangan_id, A.ruangan_nama FROM ruangan_m A ) ruangan_ditagihkan ON pasienadmisi_t.ruangan_titipan_id = ruangan_ditagihkan.ruangan_id
        LEFT JOIN ( SELECT A.pegawai_id, A.nama_pegawai FROM pegawai_m A ) dokterpengganti ON pendaftaran_t.dokterpengganti_id = pegawai_m.pegawai_id
        LEFT JOIN ( SELECT A.instalasi_id, A.instalasi_nama FROM instalasi_m A ) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
        LEFT JOIN ( SELECT A.lookup_id, A.lookup_name FROM lookup_m A ) look_namadepan ON pasien_m.namadepan :: INTEGER = look_namadepan.lookup_id
        LEFT JOIN ( SELECT A.lookup_id, A.lookup_name FROM lookup_m A ) look_jeniskelamin ON pasien_m.jeniskelamin :: INTEGER = look_jeniskelamin.lookup_id
        LEFT JOIN ( SELECT A.lookup_id, A.lookup_name FROM lookup_m A ) look_statusperiksa_penunjang ON pasienmasukpenunjang_t.status_periksa :: INTEGER = look_statusperiksa_penunjang.lookup_id
        LEFT JOIN ( SELECT A.lookup_id, A.lookup_name FROM lookup_m A ) lookup_bahasasehari ON pasien_m.bahasa_sehari :: INTEGER = lookup_bahasasehari.lookup_id
        LEFT JOIN ( SELECT A.lookup_id, A.lookup_name FROM lookup_m A ) lookup_pjnamadepan ON penanggungjawab_m.pj_namadepan :: INTEGER = lookup_pjnamadepan.lookup_id
        LEFT JOIN ( SELECT A.lookup_id, A.lookup_name FROM lookup_m A ) look_statusperiksa_pendaftaran ON pendaftaran_t.status_periksa :: INTEGER = look_statusperiksa_pendaftaran.lookup_id
        LEFT JOIN ( SELECT A.bpjs_id, A.nokartuasuransi FROM bpjs_t A ) bpjs_pendaftaran ON pendaftaran_t.bpjs_id = bpjs_pendaftaran.bpjs_id
        LEFT JOIN ( SELECT A.bpjs_id, A.nokartuasuransi FROM bpjs_t A ) bpjs_admisi ON pasienadmisi_t.bpjs_id = bpjs_admisi.bpjs_id
        LEFT JOIN ( SELECT A.lookup_id, A.lookup_name FROM lookup_m A ) look_groupcarabayar ON carabayar_m.groupcarabayar_id = look_groupcarabayar.lookup_id
        LEFT JOIN (
        SELECT A
            .asuransipasien_id,
            A.nokartuasuransi,
            A.namapemilikasuransi,
            A.nomorpokokperusahaan,
            A.status_konfirmasi,
            A.tgl_konfirmasi,
            A.nopeserta,
            A.tglcetakkartuasuransi,
            A.kodefeskestk1,
            A.nama_feskestk1,
            A.masaberlakukartu,
            A.nokartukeluarga,
            A.nopassport,
            A.is_active 
        FROM
            asuransipasien_m A 
        ) asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id 
    WHERE
        pasienmasukpenunjang_t.is_active = TRUE 
        AND pasienmasukpenunjang_t.is_deleted = FALSE 
        AND pendaftaran_t.instalasi_id <> 7 
        AND pasienmasukpenunjang_t.pegawai_id IS NOT NULL UNION ALL
    SELECT
        \'fisioterapi\' :: TEXT AS ket,
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
            
            WHEN antrian_poli.jenisantrian_id = 312 THEN
            antrian_poli.no_antrian :: TEXT ELSE \'-\' :: TEXT 
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
        COALESCE ( bpjs_admisi.nokartuasuransi, bpjs_pendaftaran.nokartuasuransi ) AS nokartuasuransi,
        carabayar_m.groupcarabayar_id,
        look_groupcarabayar.lookup_name AS groupcarabayar_nama,
        NULL :: BOOLEAN AS is_exception,
        asuransipasien_m.nokartuasuransi AS no_asuransi,
        asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
        asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan 
    FROM
        pendaftaran_t
        JOIN (
        SELECT A
            .pasien_id,
            A.no_rekam_medik,
            A.nama_pasien,
            A.alamat_pasien,
            A.tanggal_lahir,
            A.additional_pasien,
            A.catatanpenting_pasien,
            A.bahasa_sehari,
            A.namadepan,
            A.jeniskelamin 
        FROM
            pasien_m A 
        ) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
        JOIN ( SELECT A.ruangan_id, A.ruangan_nama, A.instalasi_id FROM ruangan_m A ) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
        JOIN ( SELECT A.ruangan_id, A.ruangan_nama FROM ruangan_m A ) ruangasal ON pendaftaran_t.ruangan_id = ruangasal.ruangan_id
        JOIN ( SELECT A.kelaspelayanan_id, A.kelaspelayanan_nama FROM kelaspelayanan_m A ) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
        JOIN ( SELECT A.jeniskasuspenyakit_id, A.jeniskasuspenyakit_nama FROM jeniskasuspenyakit_m A ) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
        LEFT JOIN ( SELECT A.antrian_id FROM antrian_t A ) antrian_t ON pendaftaran_t.antrian_id = antrian_t.antrian_id
        LEFT JOIN ( SELECT A.pendaftaran_id, A.jenisantrian_id, A.no_antrian FROM antrian_t A ) antrian_poli ON pendaftaran_t.pendaftaran_id = antrian_poli.pendaftaran_id 
        AND antrian_poli.jenisantrian_id = 312
        LEFT JOIN (
        SELECT A
            .pasienadmisi_id,
            A.is_pasientitipan,
            A.kelas_ditagihkan_id,
            A.kamar_titipan_id,
            A.ruangan_titipan_id,
            A.is_stoptitipan,
            A.bpjs_id 
        FROM
            pasienadmisi_t A 
        ) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
        LEFT JOIN ( SELECT A.pegawai_id, A.nama_pegawai FROM pegawai_m A ) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
        LEFT JOIN ( SELECT A.pegawai_id, A.nama_pegawai FROM pegawai_m A ) dokter_pengganti ON pendaftaran_t.dokterpengganti_id = dokter_pengganti.pegawai_id
        LEFT JOIN (
        SELECT A
            .penanggungjawab_alamat,
            A.penanggungjawab_notelp,
            A.pj_pekerjaan_id,
            A.pj_propinsi_id,
            A.pj_kabupaten_id,
            A.pj_kecamatan_id,
            A.pj_kelurahan_id,
            A.pj_namadepan,
            A.penanggungjawab_id 
        FROM
            penanggungjawab_m A 
        ) penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
        LEFT JOIN ( SELECT A.pekerjaan_id, A.pekerjaan_nama FROM pekerjaan_m A ) pj_kerja ON penanggungjawab_m.pj_pekerjaan_id = pj_kerja.pekerjaan_id
        LEFT JOIN ( SELECT A.propinsi_id, A.propinsi_nama FROM propinsi_m A ) pj_prop ON penanggungjawab_m.pj_propinsi_id = pj_prop.propinsi_id
        LEFT JOIN ( SELECT A.kabupaten_id, A.kabupaten_nama FROM kabupaten_m A ) pj_kab ON penanggungjawab_m.pj_kabupaten_id = pj_kab.kabupaten_id
        LEFT JOIN ( SELECT A.kecamatan_id, A.kecamatan_nama FROM kecamatan_m A ) pj_kec ON penanggungjawab_m.pj_kecamatan_id = pj_kec.kecamatan_id
        LEFT JOIN ( SELECT A.kelurahan_id, A.kelurahan_nama FROM kelurahan_m A ) pj_kel ON penanggungjawab_m.pj_kelurahan_id = pj_kel.kelurahan_id
        LEFT JOIN ( SELECT A.loginpemakai_id, A.pegawai_id FROM loginpemakai_k A ) petugas ON pendaftaran_t.last_modified_by = petugas.loginpemakai_id
        LEFT JOIN ( SELECT A.pegawai_id, A.nama_pegawai FROM pegawai_m A ) petugas_pemakai ON petugas.pegawai_id = petugas_pemakai.pegawai_id
        LEFT JOIN ( SELECT A.loginpemakai_id, A.pegawai_id FROM loginpemakai_k A ) pembuat ON pendaftaran_t.created_by = pembuat.loginpemakai_id
        LEFT JOIN ( SELECT A.pegawai_id, A.nama_pegawai FROM pegawai_m A ) petugas_pembuat ON pembuat.pegawai_id = petugas_pembuat.pegawai_id
        LEFT JOIN ( SELECT A.carabayar_id, A.carabayar_nama, A.carabayar_kode_warna, A.groupcarabayar_id FROM carabayar_m A ) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
        LEFT JOIN ( SELECT A.penjamin_id, A.penjamin_nama FROM penjamin_m A ) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
        LEFT JOIN ( SELECT A.kelaspelayanan_id, A.kelaspelayanan_nama FROM kelaspelayanan_m A ) kelas_ditagihkan ON pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id
        LEFT JOIN ( SELECT A.kamarruangan_id, A.kamarruangan_nokamar FROM kamarruangan_m A ) kamar_ditagihkan ON pasienadmisi_t.kamar_titipan_id = kamar_ditagihkan.kamarruangan_id
        LEFT JOIN ( SELECT A.ruangan_id, A.ruangan_nama FROM ruangan_m A ) ruangan_ditagihkan ON pasienadmisi_t.ruangan_titipan_id = ruangan_ditagihkan.ruangan_id
        LEFT JOIN ( SELECT A.pegawai_id, A.nama_pegawai FROM pegawai_m A ) dokterpengganti ON pendaftaran_t.dokterpengganti_id = pegawai_m.pegawai_id
        LEFT JOIN ( SELECT A.instalasi_id, A.instalasi_nama FROM instalasi_m A ) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
        LEFT JOIN ( SELECT A.lookup_id, A.lookup_name FROM lookup_m A ) look_namadepan ON pasien_m.namadepan :: INTEGER = look_namadepan.lookup_id
        LEFT JOIN ( SELECT A.lookup_id, A.lookup_name FROM lookup_m A ) look_jeniskelamin ON pasien_m.jeniskelamin :: INTEGER = look_jeniskelamin.lookup_id
        LEFT JOIN ( SELECT A.lookup_id, A.lookup_name FROM lookup_m A ) look_statusperiksa ON pendaftaran_t.status_periksa :: INTEGER = look_statusperiksa.lookup_id
        LEFT JOIN ( SELECT A.lookup_id, A.lookup_name FROM lookup_m A ) lookup_bahasasehari ON pasien_m.bahasa_sehari :: INTEGER = lookup_bahasasehari.lookup_id
        LEFT JOIN ( SELECT A.lookup_id, A.lookup_name FROM lookup_m A ) lookup_pjnamadepan ON penanggungjawab_m.pj_namadepan :: INTEGER = lookup_pjnamadepan.lookup_id
        LEFT JOIN ( SELECT A.bpjs_id, A.nokartuasuransi FROM bpjs_t A ) bpjs_pendaftaran ON pendaftaran_t.bpjs_id = bpjs_pendaftaran.bpjs_id
        LEFT JOIN ( SELECT A.bpjs_id, A.nokartuasuransi FROM bpjs_t A ) bpjs_admisi ON pasienadmisi_t.bpjs_id = bpjs_admisi.bpjs_id
        LEFT JOIN ( SELECT A.lookup_id, A.lookup_name FROM lookup_m A ) look_groupcarabayar ON carabayar_m.groupcarabayar_id = look_groupcarabayar.lookup_id
        LEFT JOIN (
        SELECT A
            .asuransipasien_id,
            A.nokartuasuransi,
            A.namapemilikasuransi,
            A.nomorpokokperusahaan,
            A.status_konfirmasi,
            A.tgl_konfirmasi,
            A.nopeserta,
            A.tglcetakkartuasuransi,
            A.kodefeskestk1,
            A.nama_feskestk1,
            A.masaberlakukartu,
            A.nokartukeluarga,
            A.nopassport,
            A.is_active 
        FROM
            asuransipasien_m A 
        ) asuransipasien_m ON pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id 
    WHERE
        pendaftaran_t.is_active = TRUE 
        AND pendaftaran_t.is_deleted = FALSE 
        AND pendaftaran_t.instalasi_id = 7
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230307_060307_migrate_infopasienpenunjang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230307_060307_migrate_infopasienpenunjang_v cannot be reverted.\n";

        return false;
    }
    */
}

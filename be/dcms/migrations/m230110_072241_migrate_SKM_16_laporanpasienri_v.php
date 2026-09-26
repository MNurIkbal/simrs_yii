<?php

use yii\db\Migration;

/**
 * Class m230110_072241_migrate_SKM_16_laporanpasienri_v
 */
class m230110_072241_migrate_SKM_16_laporanpasienri_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
           DROP VIEW IF EXISTS "public"."laporanpasienri_v";
        ');

        $this->execute('

		    CREATE VIEW public.laporanpasienri_v AS  SELECT pendaftaran_t.pendaftaran_id,
		       pendaftaran_t.no_pendaftaran,
		       pendaftaran_t.carabayar_id,
		       pendaftaran_t.penjamin_id,
		       pendaftaran_t.pasien_id,
		       pendaftaran_t.jeniskasuspenyakit_id,
		       pendaftaran_t.ruangan_id,
		       pasienadmisi_t.pegawai_id,
		       pasienadmisi_t.pasienpulang_id,
		       pasienadmisi_t.tgl_admisi AS "Tanggal Masuk",
		       pasienpulang_t.tglpasienpulang AS "Tanggal Keluar",
		       pasien_m.no_rekam_medik AS "No. Rekam Medik",
		       pendaftaran_t.no_pendaftaran AS "No. Pendaftaran",
		       pasien_m.nama_pasien AS "Nama Pasien",
		       lookup_kelamin.lookup_name AS "Jenis Kelamin",
		       pegawai_m.nama_pegawai AS "Dokter",
		       carabayar_m.carabayar_nama AS "Cara Bayar",
		       penjamin_m.penjamin_nama AS "Penjamin",
		       kelaspelayanan_m.kelaspelayanan_nama AS "Kelas Pelayanan",
		       jeniskasuspenyakit_m.jeniskasuspenyakit_nama AS "Jenis Kasus Penyakit",
		       penanggungjawab_m.penanggungjawab_nama,
		       ruangan_m.ruangan_nama AS "Ruangan",
		       kamarruangan_m.kamarruangan_nokamar AS kamar,
		       kamartempattidur_m.no_tempattidur,
		       pasienpulang_t.lama_rawat AS "Lama Rawat",
		       pasienbatalperiksa_t.alasan_batal,
		       pasienadmisi_t.status_ranap,
		       status_ranap.lookup_name AS status_ranap_nama,
		       pasienadmisi_t.is_stoptitipan,
		       pasienadmisi_t.is_pasientitipan,
		       pasienadmisi_t.kelas_ditagihkan_id,
		       kelas_ditagihkan.kelaspelayanan_nama AS kelas_ditagihkan_nama,
		       pindah_kamar.is_stoptitipan AS is_stoptitipan_pk,
		       pindah_kamar.is_pasientitipan AS is_pasientitipan_pk,
		       pindah_kamar.kelas_ditagihkan_id AS kelas_ditagihkan_id_pk,
		       pindah_kamar.kelaspelayanan_nama AS kelas_ditagihkan_nama_pk,
		       pindah_kamar.pindahkamar_id
		      FROM pendaftaran_t
		        JOIN ( SELECT a.pasienadmisi_id,
		               a.pegawai_id,
		               a.pasienpulang_id,
		               a.tgl_admisi,
		               a.status_ranap,
		               a.is_stoptitipan,
		               a.is_pasientitipan,
		               a.kelas_ditagihkan_id,
		               a.carabayar_id,
		               a.kelaspelayanan_id,
		               a.kamarruangan_id,
		               a.kamartempattidur_id,
		               a.pasienbatalperiksa_id
		              FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
		        LEFT JOIN ( SELECT a.pasienpulang_id,
		               a.tglpasienpulang,
		               a.lama_rawat
		              FROM pasienpulang_t a) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
		        JOIN ( SELECT a.pasien_id,
		               a.no_rekam_medik,
		               a.nama_pasien,
		               a.jeniskelamin
		              FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
		        LEFT JOIN ( SELECT a.lookup_id,
		               a.lookup_name
		              FROM lookup_m a) lookup_kelamin ON lookup_kelamin.lookup_id = pasien_m.jeniskelamin::integer
		        LEFT JOIN ( SELECT a.pegawai_id,
		               a.nama_pegawai
		              FROM pegawai_m a) pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
		        JOIN ( SELECT a.carabayar_id,
		               a.carabayar_nama
		              FROM carabayar_m a) carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
		        JOIN ( SELECT a.penjamin_id,
		               a.penjamin_nama
		              FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
		        JOIN ( SELECT a.kelaspelayanan_id,
		               a.kelaspelayanan_nama
		              FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
		        JOIN ( SELECT a.jeniskasuspenyakit_id,
		               a.jeniskasuspenyakit_nama
		              FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
		        JOIN ( SELECT a.ruangan_id,
		               a.ruangan_nama
		              FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
		        JOIN ( SELECT a.kamarruangan_id,
		               a.kamarruangan_nokamar
		              FROM kamarruangan_m a) kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
		        JOIN ( SELECT a.kamartempattidur_id,
		               a.no_tempattidur
		              FROM kamartempattidur_m a) kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
		        LEFT JOIN ( SELECT a.pasienbatalperiksa_id,
		               a.alasan_batal
		              FROM pasienbatalperiksa_t a) pasienbatalperiksa_t ON pasienadmisi_t.pasienbatalperiksa_id = pasienbatalperiksa_t.pasienbatalperiksa_id
		        LEFT JOIN ( SELECT a.penanggungjawab_id,
		               a.penanggungjawab_nama
		              FROM penanggungjawab_m a) penanggungjawab_m ON pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id
		        LEFT JOIN ( SELECT a.kelaspelayanan_id,
		               a.kelaspelayanan_nama
		              FROM kelaspelayanan_m a) kelas_ditagihkan ON pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id
		        LEFT JOIN ( SELECT pindahkamar_t.pindahkamar_id,
		               pindahkamar_t.pasienadmisi_id,
		               pindahkamar_t.is_pasientitipan,
		               pindahkamar_t.is_stoptitipan,
		               pindahkamar_t.kelas_ditagihkan_id,
		               kelaspelayanan_m_1.kelaspelayanan_nama
		              FROM pindahkamar_t
		                JOIN ( SELECT max(pk.pindahkamar_id) AS pindahkamar_id,
		                       pk.pasienadmisi_id
		                      FROM pindahkamar_t pk
		                     GROUP BY pk.pasienadmisi_id) max_pk ON pindahkamar_t.pindahkamar_id = max_pk.pindahkamar_id AND pindahkamar_t.pasienadmisi_id = max_pk.pasienadmisi_id
		                LEFT JOIN kelaspelayanan_m kelaspelayanan_m_1 ON pindahkamar_t.kelas_ditagihkan_id = kelaspelayanan_m_1.kelaspelayanan_id
		             WHERE pindahkamar_t.is_deleted = false) pindah_kamar ON pasienadmisi_t.pasienadmisi_id = pindah_kamar.pasienadmisi_id
		        LEFT JOIN ( SELECT a.lookup_id,
		               a.lookup_name
		              FROM lookup_m a) status_ranap ON status_ranap.lookup_id = pasienadmisi_t.status_ranap;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230110_072241_migrate_SKM_16_laporanpasienri_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230110_072241_migrate_SKM_16_laporanpasienri_v cannot be reverted.\n";

        return false;
    }
    */
}

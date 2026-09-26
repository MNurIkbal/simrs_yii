<?php

use yii\db\Migration;

/**
 * Class m221130_074132_hotfix_VCS548_laporanhasillab_v
 */
class m221130_074132_hotfix_VCS548_laporanhasillab_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."laporanhasillab_v";');
        $this->execute("CREATE OR REPLACE VIEW public.laporanhasillab_v
        AS SELECT 'rujukan'::text AS tipe,
            hasil_lab.hasilpemeriksaanlab_wynacom_id,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            pasienmasukpenunjang_t.no_masukpenunjang,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir AS dateofbirth,
            pendaftaran_t.umur,
            jk.lookup_name AS jeniskelamin_nama,
            pasien_m.alamat_pasien,
            ruangan_m.ruangan_id AS lokasi_id,
            ruangan_m.ruangan_nama AS lokasi_nama,
            instalasi_m.instalasi_nama,
            dokter_pengirim.nama_pegawai AS dokter_pengirim,
            pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_transaksi,
            pegawai_m.pegawai_id AS dokter_penunjangid,
            pegawai_m.nama_pegawai AS dokter_penunjang,
            permintaankepenunjang_t.is_cyto,
            permintaankepenunjang_t.tglpermintaankepenunjang AS tglpenunjang,
            permintaankepenunjang_t.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            hasil_lab.test_group,
            hasil_lab.test_name AS test_nama_lis,
            hasil_lab.result AS hasil,
            hasil_lab.reference_value AS nilai_rujukan,
            hasil_lab.test_units_name AS satuan,
            hasil_lab.authorization_date AS tgl_pemeriksaan,
            pasienmasukpenunjang_t.catatan,
            hasil_lab.authorization_user AS petugas_pemeriksaan,
            hasil_lab.test_method,
            pasienmasukpenunjang_t.is_hasil,
            hasil_last.authorization_user,
            hasil_lab.authorization_date AS tgl_hasil,
            CURRENT_TIMESTAMP AS tgl_cetak,
            hasil_lab.is_print,
            pemeriksaanlab_m.is_exception,
            hasil_lab.lis_test_id,
            hasil_lab.test_flag_sign
           FROM pendaftaran_t
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.tanggal_lahir,
                    a.jeniskelamin,
                    a.alamat_pasien
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.pasienmasukpenunjang_id,
                    a.no_masukpenunjang,
                    a.tglmasukpenunjang,
                    a.catatan,
                    a.is_hasil,
                    a.pendaftaran_id,
                    a.ruangan_id,
                    a.pegawai_id
                   FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT a.pasienmasukpenunjang_id,
                    a.pasienkirimkeunitlain_id
                   FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = pasienkirimkeunitlain_t.pasienmasukpenunjang_id
             JOIN ( SELECT a.pasienkirimkeunitlain_id,
                    a.is_cyto,
                    a.tglpermintaankepenunjang,
                    a.daftartindakan_id
                   FROM permintaankepenunjang_t a) permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
             JOIN ( SELECT a.daftartindakan_id,
                    a.daftartindakan_kode,
                    a.daftartindakan_nama,
                    a.kelompoktindakan_id
                   FROM daftartindakan_m a) daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN ( SELECT a.kelompoktindakan_id,
                    a.kelompoktindakan_nama
                   FROM kelompoktindakan_m a) kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) dokter_pengirim ON pendaftaran_t.pegawai_id = dokter_pengirim.pegawai_id
             JOIN ( SELECT a.hasilpemeriksaanlab_wynacom_id,
                    a.his_reg_no,
                    a.test_group,
                    a.test_name,
                    a.result,
                    a.reference_value,
                    a.test_units_name,
                    a.authorization_date,
                    a.authorization_user,
                    a.test_method,
                    a.is_print,
                    a.lis_test_id,
                    a.test_flag_sign,
                    a.his_test_id,
                    a.is_deleted
                   FROM hasilpemeriksaanlab_wynacom_t a
                  WHERE a.is_deleted = false) hasil_lab ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasil_lab.his_reg_no::text AND daftartindakan_m.daftartindakan_kode::text = hasil_lab.his_test_id::text
             LEFT JOIN ( SELECT hasilpemeriksaanlab_wynacom_t.his_reg_no,
                    hasilpemeriksaanlab_wynacom_t.authorization_date,
                    hasilpemeriksaanlab_wynacom_t.authorization_user
                   FROM hasilpemeriksaanlab_wynacom_t
                  ORDER BY hasilpemeriksaanlab_wynacom_t.hasilpemeriksaanlab_wynacom_id DESC
                 LIMIT 1) hasil_last ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasil_last.his_reg_no::text
             LEFT JOIN ( SELECT a.daftartindakan_id,
                    a.pemeriksaanlab_id,
                    a.is_exception
                   FROM pemeriksaanlab_m a) pemeriksaanlab_m ON daftartindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
        UNION ALL
         SELECT 'APS'::text AS tipe,
            hasil_lab.hasilpemeriksaanlab_wynacom_id,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            pasienmasukpenunjang_t.no_masukpenunjang,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.tanggal_lahir AS dateofbirth,
            pendaftaran_t.umur,
            jk.lookup_name AS jeniskelamin_nama,
            pasien_m.alamat_pasien,
            ruangan_m.ruangan_id AS lokasi_id,
            ruangan_m.ruangan_nama AS lokasi_nama,
            instalasi_m.instalasi_nama,
            dokter_pengirim.nama_pegawai AS dokter_pengirim,
            pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_transaksi,
            pegawai_m.pegawai_id AS dokter_penunjangid,
            pegawai_m.nama_pegawai AS dokter_penunjang,
            tindakanpelayanan_t.cyto_tindakan AS is_cyto,
            pasienmasukpenunjang_t.tglmasukpenunjang AS tglpenunjang,
            daftartindakan_m.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            hasil_lab.test_group,
            hasil_lab.test_name AS test_nama_lis,
            hasil_lab.result AS hasil,
            hasil_lab.reference_value AS nilai_rujukan,
            hasil_lab.test_units_name AS satuan,
            hasil_lab.authorization_date AS tgl_pemeriksaan,
            pasienmasukpenunjang_t.catatan,
            hasil_lab.authorization_user AS petugas_pemeriksaan,
            hasil_lab.test_method,
            pasienmasukpenunjang_t.is_hasil,
            hasil_last.authorization_user,
            hasil_lab.authorization_date AS tgl_hasil,
            CURRENT_TIMESTAMP AS tgl_cetak,
            hasil_lab.is_print,
            pemeriksaanlab_m.is_exception,
            hasil_lab.lis_test_id,
            hasil_lab.test_flag_sign
           FROM pendaftaran_t
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.tanggal_lahir,
                    a.jeniskelamin,
                    a.alamat_pasien
                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.pasienmasukpenunjang_id,
                    a.pasienkirimkeunitlain_id,
                    a.no_masukpenunjang,
                    a.tglmasukpenunjang,
                    a.catatan,
                    a.is_hasil,
                    a.pendaftaran_id,
                    a.ruangan_id,
                    a.pegawai_id
                   FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT a.pasienmasukpenunjang_id,
                    a.cyto_tindakan,
                    a.daftartindakan_id
                   FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id
             LEFT JOIN ( SELECT a.daftartindakan_id,
                    a.daftartindakan_kode,
                    a.daftartindakan_nama,
                    a.kelompoktindakan_id
                   FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             LEFT JOIN ( SELECT a.kelompoktindakan_id,
                    a.kelompoktindakan_nama
                   FROM kelompoktindakan_m a) kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.instalasi_id,
                    a.instalasi_nama
                   FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) dokter_pengirim ON pendaftaran_t.pegawai_id = dokter_pengirim.pegawai_id
             LEFT JOIN ( SELECT a.hasilpemeriksaanlab_wynacom_id,
                    a.his_reg_no,
                    a.test_group,
                    a.test_name,
                    a.result,
                    a.reference_value,
                    a.test_units_name,
                    a.authorization_date,
                    a.authorization_user,
                    a.test_method,
                    a.is_print,
                    a.lis_test_id,
                    a.test_flag_sign,
                    a.his_test_id,
                    a.is_deleted
                   FROM hasilpemeriksaanlab_wynacom_t a
                  WHERE a.is_deleted = false) hasil_lab ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasil_lab.his_reg_no::text AND daftartindakan_m.daftartindakan_kode::text = hasil_lab.his_test_id::text
             LEFT JOIN ( SELECT hasilpemeriksaanlab_wynacom_t.his_reg_no,
                    hasilpemeriksaanlab_wynacom_t.authorization_date,
                    hasilpemeriksaanlab_wynacom_t.authorization_user
                   FROM hasilpemeriksaanlab_wynacom_t
                  ORDER BY hasilpemeriksaanlab_wynacom_t.hasilpemeriksaanlab_wynacom_id DESC
                 LIMIT 1) hasil_last ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasil_last.his_reg_no::text
             LEFT JOIN ( SELECT a.daftartindakan_id,
                    a.pemeriksaanlab_id,
                    a.is_exception
                   FROM pemeriksaanlab_m a) pemeriksaanlab_m ON daftartindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
          WHERE pasienmasukpenunjang_t.pasienkirimkeunitlain_id IS NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221130_074132_hotfix_VCS548_laporanhasillab_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221130_074132_hotfix_VCS548_laporanhasillab_v cannot be reverted.\n";

        return false;
    }
    */
}

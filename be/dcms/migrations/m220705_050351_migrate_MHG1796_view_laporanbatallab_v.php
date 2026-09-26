<?php

use yii\db\Migration;

/**
 * Class m220705_050351_migrate_MHG1796_view_laporanbatallab_v
 */
class m220705_050351_migrate_MHG1796_view_laporanbatallab_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."laporanbatallab_v";
        ');

        $this->execute('
            CREATE VIEW "public"."laporanbatallab_v" AS  
            SELECT \'Approve LAB\'::text AS tipe,
                COALESCE(tindakanpelayanan_t.deleted_date, tindakanpelayanan_t.tgl_tindakan) AS tgl_batal,
                COALESCE(pasienkirimkeunitlain_t.tgl_kirimpasien, pasienmasukpenunjang_t.tglmasukpenunjang, tindakanpelayanan_t.tgl_tindakan) AS tgl_rujukan,
                pendaftaran_t.no_pendaftaran,
                COALESCE(pasienkirimkeunitlain_t.no_orderkeunitlain, pasienmasukpenunjang_t.no_masukpenunjang) AS no_rujukan,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien, 
                jk.lookup_name AS jenis_kelamin,
                pasien_m.tanggal_lahir,
                asalruangan.ruangan_nama AS asalrujukan_nama,
                dokter.nama_pegawai,
                penjamin_m.penjamin_nama,
                carabayar_m.carabayar_nama,
                tindakanpelayanan_t.alasan_batal,
                pegawai_input.nama_pegawai AS disetujui_oleh,
                pegawai_hapus.nama_pegawai AS dibatalkan_oleh,
                daftartindakan_m.daftartindakan_nama AS pemeriksaan
               FROM tindakanpelayanan_t
                 JOIN ( SELECT a.pasienmasukpenunjang_id,
                        a.tglmasukpenunjang,
                        a.no_masukpenunjang,
                        a.pasienkirimkeunitlain_id,
                        a.ruanganasal_id
                       FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
                 LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
                        a.no_orderkeunitlain,
                        a.tgl_kirimpasien
                       FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
                 JOIN ( SELECT a.pendaftaran_id,
                        a.no_pendaftaran,
                        a.pasien_id
                       FROM pendaftaran_t a) pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                 JOIN ( SELECT a.pasien_id,
                        a.nama_pasien,
                        a.no_rekam_medik,
                        a.jeniskelamin,
                        a.tanggal_lahir
                       FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                 LEFT JOIN ( SELECT lookup_m.lookup_id,
                        lookup_m.lookup_name
                       FROM lookup_m) jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
                 JOIN ( SELECT a.daftartindakan_id,
                        a.daftartindakan_nama
                       FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                 LEFT JOIN ( SELECT a.ruangan_id,
                        a.ruangan_nama
                       FROM ruangan_m a) asalruangan ON pasienmasukpenunjang_t.ruanganasal_id = asalruangan.ruangan_id
                 LEFT JOIN ( SELECT a.penjamin_id,
                        a.penjamin_nama
                       FROM penjamin_m a) penjamin_m ON tindakanpelayanan_t.penjamin_id = penjamin_m.penjamin_id
                 LEFT JOIN ( SELECT a.carabayar_id,
                        a.carabayar_nama
                       FROM carabayar_m a) carabayar_m ON tindakanpelayanan_t.carabayar_id = carabayar_m.carabayar_id
                 LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                       FROM pegawai_m a) dokter ON tindakanpelayanan_t.dokterpenanggungjawab_id = dokter.pegawai_id
                 LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai,
                        loginpemakai_k.loginpemakai_id
                       FROM pegawai_m a
                         JOIN ( SELECT a_1.loginpemakai_id,
                                a_1.pegawai_id
                               FROM loginpemakai_k a_1) loginpemakai_k ON a.pegawai_id = loginpemakai_k.pegawai_id) pegawai_input ON tindakanpelayanan_t.created_by = pegawai_input.loginpemakai_id
                 LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai,
                        loginpemakai_k.loginpemakai_id
                       FROM pegawai_m a
                         JOIN ( SELECT a_1.loginpemakai_id,
                                a_1.pegawai_id
                               FROM loginpemakai_k a_1) loginpemakai_k ON a.pegawai_id = loginpemakai_k.pegawai_id) pegawai_hapus ON tindakanpelayanan_t.deleted_by = pegawai_hapus.loginpemakai_id
              WHERE tindakanpelayanan_t.instalasi_id = 4 AND tindakanpelayanan_t.is_deleted IS TRUE
            UNION ALL
             SELECT \'Order IGD\'::text AS tipe,
                permintaankepenunjang_t.deleted_date AS tgl_batal,
                pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
                pendaftaran_t.no_pendaftaran,
                pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                jk.lookup_name AS jenis_kelamin,
                pasien_m.tanggal_lahir,
                asalruangan.ruangan_nama AS asalrujukan_nama,
                dokter.nama_pegawai,
                carabayar_m.carabayar_nama AS penjamin_nama,
                penjamin_m.penjamin_nama AS carabayar_nama,
                COALESCE(batalorderpenunjang_t.alasan_batal, permintaankepenunjang_t.alasan_batal) AS alasan_batal,
                COALESCE(petugas_menyetujui.nama_pegawai, pegawai_input.nama_pegawai) AS disetujui_oleh,
                pegawai_hapus.nama_pegawai AS dibatalkan_oleh,
                daftartindakan_m.daftartindakan_nama AS pemeriksaan
               FROM permintaankepenunjang_t
                 LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
                        a.no_orderkeunitlain,
                        a.tgl_kirimpasien,
                        a.pendaftaran_id,
                        a.ruangan_id,
                        a.instalasi_id
                       FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON permintaankepenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
                 LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
                        a.alasan AS alasan_batal,
                        a.peg_menyetujui_id,
                        a.created_by,
                        a.tgl_batalorder
                       FROM batalorderpenunjang_t a) batalorderpenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = batalorderpenunjang_t.pasienkirimkeunitlain_id
                 JOIN ( SELECT a.pendaftaran_id,
                        a.no_pendaftaran,
                        a.pasien_id,
                        a.carabayar_id,
                        a.penjamin_id,
                        a.pegawai_id,
                        a.ruangan_id
                       FROM pendaftaran_t a) pendaftaran_t ON pasienkirimkeunitlain_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                 JOIN ( SELECT a.pasien_id,
                        a.nama_pasien,
                        a.no_rekam_medik,
                        a.jeniskelamin,
                        a.tanggal_lahir
                       FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                 LEFT JOIN ( SELECT lookup_m.lookup_id,
                        lookup_m.lookup_name
                       FROM lookup_m) jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
                 JOIN ( SELECT a.daftartindakan_id,
                        a.daftartindakan_nama
                       FROM daftartindakan_m a) daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                 LEFT JOIN ( SELECT a.ruangan_id,
                        a.ruangan_nama
                       FROM ruangan_m a) asalruangan ON pendaftaran_t.ruangan_id = asalruangan.ruangan_id
                 LEFT JOIN ( SELECT a.penjamin_id,
                        a.penjamin_nama
                       FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
                 LEFT JOIN ( SELECT a.carabayar_id,
                        a.carabayar_nama
                       FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
                 LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                       FROM pegawai_m a) dokter ON COALESCE(permintaankepenunjang_t.dokter_id, pendaftaran_t.pegawai_id) = dokter.pegawai_id
                 LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai,
                        loginpemakai_k.loginpemakai_id
                       FROM pegawai_m a
                         JOIN ( SELECT a_1.loginpemakai_id,
                                a_1.pegawai_id
                               FROM loginpemakai_k a_1) loginpemakai_k ON a.pegawai_id = loginpemakai_k.pegawai_id) pegawai_input ON permintaankepenunjang_t.created_by = pegawai_input.loginpemakai_id
                 LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                       FROM pegawai_m a) petugas_menyetujui ON batalorderpenunjang_t.peg_menyetujui_id = petugas_menyetujui.pegawai_id
                 LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai,
                        loginpemakai_k.loginpemakai_id
                       FROM pegawai_m a
                         JOIN ( SELECT a_1.loginpemakai_id,
                                a_1.pegawai_id
                               FROM loginpemakai_k a_1) loginpemakai_k ON a.pegawai_id = loginpemakai_k.pegawai_id) pegawai_hapus ON permintaankepenunjang_t.deleted_by = pegawai_hapus.loginpemakai_id
              WHERE pasienkirimkeunitlain_t.instalasi_id = 4 AND permintaankepenunjang_t.is_deleted IS TRUE
            UNION ALL
             SELECT \'Order Ranap\'::text AS tipe,
                permintaankepenunjang_t.deleted_date AS tgl_batal,
                pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
                pendaftaran_t.no_pendaftaran,
                pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                jk.lookup_name AS jenis_kelamin,
                pasien_m.tanggal_lahir,
                asalruangan.ruangan_nama AS asalrujukan_nama,
                dokter.nama_pegawai,
                carabayar_m.carabayar_nama AS penjamin_nama,
                penjamin_m.penjamin_nama AS carabayar_nama,
                COALESCE(batalorderpenunjang_t.alasan_batal, permintaankepenunjang_t.alasan_batal) AS alasan_batal,
                COALESCE(petugas_menyetujui.nama_pegawai, pegawai_input.nama_pegawai) AS disetujui_oleh,
                pegawai_hapus.nama_pegawai AS dibatalkan_oleh,
                daftartindakan_m.daftartindakan_nama AS pemeriksaan
               FROM permintaankepenunjang_t
                 LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
                        a.no_orderkeunitlain,
                        a.tgl_kirimpasien,
                        a.pendaftaran_id,
                        a.ruangan_id,
                        a.instalasi_id,
                        a.pasienadmisi_id
                       FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON permintaankepenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
                 LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
                        a.alasan AS alasan_batal,
                        a.peg_menyetujui_id,
                        a.created_by,
                        a.tgl_batalorder
                       FROM batalorderpenunjang_t a) batalorderpenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = batalorderpenunjang_t.pasienkirimkeunitlain_id
                 JOIN ( SELECT a.pasienadmisi_id,
                        a.pendaftaran_id,
                        a.carabayar_id,
                        a.penjamin_id,
                        a.ruangan_id,
                        a.pegawai_id
                       FROM pasienadmisi_t a) pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                 JOIN ( SELECT a.pendaftaran_id,
                        a.no_pendaftaran,
                        a.pasien_id,
                        a.carabayar_id,
                        a.penjamin_id,
                        a.pegawai_id,
                        a.ruangan_id
                       FROM pendaftaran_t a) pendaftaran_t ON pasienadmisi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                 JOIN ( SELECT a.pasien_id,
                        a.nama_pasien,
                        a.no_rekam_medik,
                        a.jeniskelamin,
                        a.tanggal_lahir
                       FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                 LEFT JOIN ( SELECT lookup_m.lookup_id,
                        lookup_m.lookup_name
                       FROM lookup_m) jk ON pasien_m.jeniskelamin::integer = jk.lookup_id
                 JOIN ( SELECT a.daftartindakan_id,
                        a.daftartindakan_nama
                       FROM daftartindakan_m a) daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                 LEFT JOIN ( SELECT a.ruangan_id,
                        a.ruangan_nama
                       FROM ruangan_m a) asalruangan ON COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id) = asalruangan.ruangan_id
                 LEFT JOIN ( SELECT a.penjamin_id,
                        a.penjamin_nama
                       FROM penjamin_m a) penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
                 LEFT JOIN ( SELECT a.carabayar_id,
                        a.carabayar_nama
                       FROM carabayar_m a) carabayar_m ON pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id
                 LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                       FROM pegawai_m a) dokter ON COALESCE(permintaankepenunjang_t.dokter_id, pasienadmisi_t.pegawai_id) = dokter.pegawai_id
                 LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai,
                        loginpemakai_k.loginpemakai_id
                       FROM pegawai_m a
                         JOIN ( SELECT a_1.loginpemakai_id,
                                a_1.pegawai_id
                               FROM loginpemakai_k a_1) loginpemakai_k ON a.pegawai_id = loginpemakai_k.pegawai_id) pegawai_input ON permintaankepenunjang_t.created_by = pegawai_input.loginpemakai_id
                 LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai
                       FROM pegawai_m a) petugas_menyetujui ON batalorderpenunjang_t.peg_menyetujui_id = petugas_menyetujui.pegawai_id
                 LEFT JOIN ( SELECT a.pegawai_id,
                        a.nama_pegawai,
                        loginpemakai_k.loginpemakai_id
                       FROM pegawai_m a
                         JOIN ( SELECT a_1.loginpemakai_id,
                                a_1.pegawai_id
                               FROM loginpemakai_k a_1) loginpemakai_k ON a.pegawai_id = loginpemakai_k.pegawai_id) pegawai_hapus ON permintaankepenunjang_t.deleted_by = pegawai_hapus.loginpemakai_id
              WHERE pasienkirimkeunitlain_t.instalasi_id = 4 AND permintaankepenunjang_t.is_deleted IS TRUE;
        '); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220705_050351_migrate_MHG1796_view_laporanbatallab_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220705_050351_migrate_MHG1796_view_laporanbatallab_v cannot be reverted.\n";

        return false;
    }
    */
}

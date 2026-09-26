<?php

use yii\db\Migration;

/**
 * Class m221213_091726_migrate_infopasienpulangrjrd_v
 */
class m221213_091726_migrate_infopasienpulangrjrd_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."infopasienpulangrjrd_v";');
        $this->execute("CREATE OR REPLACE VIEW public.infopasienpulangrjrd_v
        AS SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.carabayar_id,
            pendaftaran_t.tgl_pendaftaran,
            pasienpulang_t.tglpasienpulang,
            pasien_m.no_rekam_medik,
            pendaftaran_t.no_pendaftaran,
            pasien_m.nama_pasien,
            look_jeniskelamin.lookup_name AS jenis_kelamin,
            kelaspelayanan_m.kelaspelayanan_nama,
            pasienpulang_t.ruanganakhir_id,
            ruangan_m.ruangan_nama,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter,
            carakeluar_m.carakeluar_nama,
            pendaftaran_t.instalasi_id,
            pasienpulang_t.kondisikeluar_id,
            kondisikeluar_m.kondisikeluar_nama,
            pasienpulang_t.pasienpulang_id,
            pendaftaran_t.umur,
            pasien_m.tanggal_lahir,
            pendaftaran_t.status_bayar,
            look_statusbayar.lookup_name AS stat_bayar,
            pendaftaran_t.status_periksa,
            look_statusperiksa.lookup_name AS status_periksa_nama,
            pasienadmisi_t.status_ranap,
            petugas_pemulang.pegawai_id AS petugas_pemulang_id,
            petugas_pemulang.nama_pegawai AS petugas_pemulang_nama,
            carakeluar_m.carakeluar_id,
            COALESCE(pasien_m.no_telepon_pasien, pasien_m.no_mobile_pasien) AS no_telepon_pasien,
            carabayar_m.carabayar_nama
           FROM pendaftaran_t
             JOIN ( SELECT a.pasienpulang_id,
                    a.pendaftaran_id,
                    a.tglpasienpulang,
                    a.ruanganakhir_id,
                    a.kondisikeluar_id,
                    a.pasien_id,
                    a.carakeluar_id,
                    a.pasienbatalpulang_id,
                    a.created_by
                   FROM pasienpulang_t a) pasienpulang_t ON pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id AND pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.tanggal_lahir,
                    a.jeniskelamin,
                    a.no_telepon_pasien,
                    a.no_mobile_pasien
                   FROM pasien_m a) pasien_m ON pasienpulang_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a.carakeluar_id,
                    a.carakeluar_nama
                   FROM carakeluar_m a) carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.kondisikeluar_id,
                    a.kondisikeluar_nama
                   FROM kondisikeluar_m a) kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.status_ranap
                   FROM pasienadmisi_t a) pasienadmisi_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_statusbayar ON pendaftaran_t.status_bayar = look_statusbayar.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_statusperiksa ON pendaftaran_t.status_periksa::integer = look_statusperiksa.lookup_id
             LEFT JOIN ( SELECT a.loginpemakai_id,
                    a.pegawai_id
                   FROM loginpemakai_k a) loginpemakai_k ON pasienpulang_t.created_by = loginpemakai_k.loginpemakai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) petugas_pemulang ON loginpemakai_k.pegawai_id = petugas_pemulang.pegawai_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
          WHERE pasienpulang_t.pasienbatalpulang_id IS NULL
        UNION
         SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.carabayar_id,
            pendaftaran_t.tgl_pendaftaran,
            pasienpulang_t.tglpasienpulang,
            pasien_m.no_rekam_medik,
            pendaftaran_t.no_pendaftaran,
            pasien_m.nama_pasien,
            look_jeniskelamin.lookup_name AS jenis_kelamin,
            kelaspelayanan_m.kelaspelayanan_nama,
            pasienpulang_t.ruanganakhir_id,
            ruangan_m.ruangan_nama,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai AS dokter,
            carakeluar_m.carakeluar_nama,
            pendaftaran_t.instalasi_id,
            pasienpulang_t.kondisikeluar_id,
            kondisikeluar_m.kondisikeluar_nama,
            pasienpulang_t.pasienpulang_id,
            pendaftaran_t.umur,
            pasien_m.tanggal_lahir,
            pendaftaran_t.status_bayar,
            look_statusbayar.lookup_name AS stat_bayar,
            konsulpoli_t.status_periksa,
            look_statusperiksa.lookup_name AS status_periksa_nama,
            pasienadmisi_t.status_ranap,
            petugas_pemulang.pegawai_id AS petugas_pemulang_id,
            petugas_pemulang.nama_pegawai AS petugas_pemulang_nama,
            carakeluar_m.carakeluar_id,
            pasien_m.no_telepon_pasien,
            carabayar_m.carabayar_nama
           FROM konsulpoli_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.carabayar_id,
                    a.tgl_pendaftaran,
                    a.no_pendaftaran,
                    a.penjamin_id,
                    a.instalasi_id,
                    a.umur,
                    a.status_bayar,
                    a.kelaspelayanan_id,
                    a.jeniskasuspenyakit_id,
                    a.pasienadmisi_id
                   FROM pendaftaran_t a) pendaftaran_t ON konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT a.pasienpulang_id,
                    a.pendaftaran_id,
                    a.konsulpoli_id,
                    a.tglpasienpulang,
                    a.ruanganakhir_id,
                    a.kondisikeluar_id,
                    a.pasien_id,
                    a.carakeluar_id,
                    a.pasienbatalpulang_id,
                    a.created_by
                   FROM pasienpulang_t a) pasienpulang_t ON konsulpoli_t.pendaftaran_id = pasienpulang_t.pendaftaran_id AND konsulpoli_t.pasienpulang_id = pasienpulang_t.pasienpulang_id AND konsulpoli_t.konsulpoli_id = pasienpulang_t.konsulpoli_id
             JOIN ( SELECT a.pasien_id,
                    a.no_rekam_medik,
                    a.nama_pasien,
                    a.tanggal_lahir,
                    a.jeniskelamin,
                    a.no_telepon_pasien
                   FROM pasien_m a) pasien_m ON pasienpulang_t.pasien_id = pasien_m.pasien_id
             JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
             JOIN ( SELECT a.jeniskasuspenyakit_id,
                    a.jeniskasuspenyakit_nama
                   FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON konsulpoli_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ( SELECT a.carakeluar_id,
                    a.carakeluar_nama
                   FROM carakeluar_m a) carakeluar_m ON pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id
             JOIN ( SELECT a.penjamin_id,
                    a.penjamin_nama
                   FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN ( SELECT a.kondisikeluar_id,
                    a.kondisikeluar_nama
                   FROM kondisikeluar_m a) kondisikeluar_m ON pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.status_ranap
                   FROM pasienadmisi_t a) pasienadmisi_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::integer = look_jeniskelamin.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_statusbayar ON pendaftaran_t.status_bayar = look_statusbayar.lookup_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_statusperiksa ON konsulpoli_t.status_periksa::integer = look_statusperiksa.lookup_id
             LEFT JOIN ( SELECT a.loginpemakai_id,
                    a.pegawai_id
                   FROM loginpemakai_k a) loginpemakai_k ON pasienpulang_t.created_by = loginpemakai_k.loginpemakai_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) petugas_pemulang ON loginpemakai_k.pegawai_id = petugas_pemulang.pegawai_id
             JOIN ( SELECT a.carabayar_id,
                    a.carabayar_nama
                   FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
          WHERE pasienpulang_t.pasienbatalpulang_id IS NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221213_091726_migrate_infopasienpulangrjrd_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221213_091726_migrate_infopasienpulangrjrd_v cannot be reverted.\n";

        return false;
    }
    */
}

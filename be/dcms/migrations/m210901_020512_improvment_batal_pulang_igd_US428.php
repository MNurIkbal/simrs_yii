<?php

use yii\db\Migration;

/**
 * Class m210901_020512_improvment_batal_pulang_igd_US428
 */
class m210901_020512_improvment_batal_pulang_igd_US428 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infopasienpulangrjrd_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infopasienpulangrjrd_v" AS  SELECT pendaftaran_t.pendaftaran_id,
                pendaftaran_t.carabayar_id,
                pendaftaran_t.tgl_pendaftaran,
                pasienpulang_t.tglpasienpulang,
                pasien_m.no_rekam_medik,
                pendaftaran_t.no_pendaftaran,
                pasien_m.nama_pasien, 
                fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
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
                fgetnamalookup(pendaftaran_t.status_bayar) AS stat_bayar,
                pendaftaran_t.status_periksa,
                fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa_nama,
                pasienadmisi_t.status_ranap
               FROM ((((((((((pendaftaran_t
                 JOIN pasienpulang_t ON (((pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id) AND (pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id))))
                 JOIN pasien_m ON ((pasienpulang_t.pasien_id = pasien_m.pasien_id)))
                 JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 JOIN ruangan_m ON ((pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id)))
                 JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                 JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
                 LEFT JOIN pasienadmisi_t ON ((pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id)))
              WHERE (pasienpulang_t.pasienbatalpulang_id IS NULL)
            UNION
             SELECT pendaftaran_t.pendaftaran_id,
                pendaftaran_t.carabayar_id,
                pendaftaran_t.tgl_pendaftaran,
                pasienpulang_t.tglpasienpulang,
                pasien_m.no_rekam_medik,
                pendaftaran_t.no_pendaftaran,
                pasien_m.nama_pasien,
                fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
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
                fgetnamalookup(pendaftaran_t.status_bayar) AS stat_bayar,
                konsulpoli_t.status_periksa,
                fgetnamalookup((konsulpoli_t.status_periksa)::integer) AS status_periksa_nama,
                pasienadmisi_t.status_ranap
               FROM (((((((((((konsulpoli_t
                 JOIN pendaftaran_t ON ((konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                 JOIN pasienpulang_t ON (((konsulpoli_t.pendaftaran_id = pasienpulang_t.pendaftaran_id) AND (konsulpoli_t.pasienpulang_id = pasienpulang_t.pasienpulang_id) AND (konsulpoli_t.konsulpoli_id = pasienpulang_t.konsulpoli_id))))
                 JOIN pasien_m ON ((pasienpulang_t.pasien_id = pasien_m.pasien_id)))
                 JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                 JOIN ruangan_m ON ((pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id)))
                 JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                 JOIN pegawai_m ON ((konsulpoli_t.pegawai_id = pegawai_m.pegawai_id)))
                 JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
                 JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                 LEFT JOIN kondisikeluar_m ON ((pasienpulang_t.kondisikeluar_id = kondisikeluar_m.kondisikeluar_id)))
                 LEFT JOIN pasienadmisi_t ON ((pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id)))
              WHERE (pasienpulang_t.pasienbatalpulang_id IS NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210901_020512_improvment_batal_pulang_igd_US428 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210901_020512_improvment_batal_pulang_igd_US428 cannot be reverted.\n";

        return false;
    }
    */
}

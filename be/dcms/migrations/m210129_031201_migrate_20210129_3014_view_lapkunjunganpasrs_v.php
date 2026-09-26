<?php

use yii\db\Migration;

/**
 * Class m210129_031201_migrate_20210129_3014_view_lapkunjunganpasrs_v
 */
class m210129_031201_migrate_20210129_3014_view_lapkunjunganpasrs_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.lapkunjunganpasrs_v;');
        $this->execute("
            CREATE VIEW \"public\".\"lapkunjunganpasrs_v\" AS
            SELECT kunjungan.pendaftaran_id,
            kunjungan.pasienadmisi_id,
            kunjungan.tgl_pendaftaran,
            kunjungan.no_pendaftaran,
            kunjungan.no_rekam_medik,
            kunjungan.nama_pasien,
            kunjungan.jenis_kelamin,
            kunjungan.tanggal_lahir,
            kunjungan.umur,
            kunjungan.alamat_pasien,
            kunjungan.carabayar_id,
            kunjungan.carabayar_nama,
            kunjungan.penjamin_id,
            kunjungan.penjamin_nama,
            kunjungan.jeniskasuspenyakit_id,
            kunjungan.jeniskasuspenyakit_nama,
            kunjungan.instalasi_id,
            kunjungan.instalasi_nama,
            kunjungan.instalasi_asal,
            kunjungan.ruangan_id,
            kunjungan.ruangan_nama,
            kunjungan.dokterdpjp_id,
            kunjungan.dokterdpjp_nama,
            kunjungan.diagnosa_utama_id,
            kunjungan.diagnosa_utama,
            joined_penyerta.diagnosa_penyerta_id,
            joined_penyerta.diagnosa_penyerta,
            kunjungan.status_periksa,
            kunjungan.id_status_periksa,
            kunjungan.is_status_periksa,
            kunjungan.jeniskelamin
            FROM (lapkunjunganpasienrs_v kunjungan
            LEFT JOIN ( SELECT penyerta.pendaftaran_id,
            penyerta.pasienadmisi_id,
            string_agg((penyerta.diagnosa_penyerta_id)::text, '$'::text) AS diagnosa_penyerta_id,
            string_agg(penyerta.diagnosa_penyerta, '$'::text) AS diagnosa_penyerta
            FROM lapkunjunganpasienrs_v penyerta
            GROUP BY penyerta.pendaftaran_id, penyerta.pasienadmisi_id) joined_penyerta ON (((joined_penyerta.pendaftaran_id = kunjungan.pendaftaran_id) AND (joined_penyerta.pasienadmisi_id = kunjungan.pasienadmisi_id))))
            GROUP BY kunjungan.pendaftaran_id, kunjungan.pasienadmisi_id, kunjungan.tgl_pendaftaran, kunjungan.no_pendaftaran, kunjungan.no_rekam_medik, kunjungan.nama_pasien, kunjungan.jenis_kelamin, kunjungan.tanggal_lahir, kunjungan.umur, kunjungan.alamat_pasien, kunjungan.carabayar_id, kunjungan.carabayar_nama, kunjungan.penjamin_id, kunjungan.penjamin_nama, kunjungan.jeniskasuspenyakit_id, kunjungan.jeniskasuspenyakit_nama, kunjungan.instalasi_id, kunjungan.instalasi_nama, kunjungan.instalasi_asal, kunjungan.ruangan_id, kunjungan.ruangan_nama, kunjungan.dokterdpjp_id, kunjungan.dokterdpjp_nama, kunjungan.diagnosa_utama_id, kunjungan.diagnosa_utama, joined_penyerta.diagnosa_penyerta_id, joined_penyerta.diagnosa_penyerta, kunjungan.status_periksa, kunjungan.id_status_periksa, kunjungan.is_status_periksa, kunjungan.jeniskelamin
            ;");
            $this->execute('
                ALTER TABLE public.lapkunjunganpasrs_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210129_031201_migrate_20210129_3014_view_lapkunjunganpasrs_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210129_031201_migrate_20210129_3014_view_lapkunjunganpasrs_v cannot be reverted.\n";

        return false;
    }
    */
}

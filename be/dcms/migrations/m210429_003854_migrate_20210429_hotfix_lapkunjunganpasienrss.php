<?php

use yii\db\Migration;

/**
 * Class m210429_003854_migrate_20210429_hotfix_lapkunjunganpasienrss
 */
class m210429_003854_migrate_20210429_hotfix_lapkunjunganpasienrss extends Migration
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
    kunjungan.jeniskelamin,
    kunjungan.no_telepon_pasien,
    kunjungan.no_mobile_pasien,
    kunjungan.kunjungan_id,
    kunjungan.kunjungan_nama,
    kunjungan.kondisikeluar_id,
    kunjungan.kondisikeluar_nama,
    kunjungan.carakeluar_id,
    kunjungan.carakeluar_nama
   FROM (lapkunjunganpasienrs_v kunjungan
     JOIN ( SELECT penyerta.pendaftaran_id,
            penyerta.pasienadmisi_id,
            string_agg((penyerta.diagnosa_penyerta_id)::text, '$'::text) AS diagnosa_penyerta_id,
            string_agg(penyerta.diagnosa_penyerta, '$'::text) AS diagnosa_penyerta
           FROM lapkunjunganpasienrs_v penyerta
          GROUP BY penyerta.pendaftaran_id, penyerta.pasienadmisi_id) joined_penyerta ON (((joined_penyerta.pendaftaran_id = kunjungan.pendaftaran_id) AND (joined_penyerta.pasienadmisi_id = kunjungan.pasienadmisi_id))))
  GROUP BY kunjungan.pendaftaran_id, kunjungan.pasienadmisi_id, kunjungan.tgl_pendaftaran, kunjungan.no_pendaftaran, kunjungan.no_rekam_medik, kunjungan.nama_pasien, kunjungan.jenis_kelamin, kunjungan.tanggal_lahir, kunjungan.umur, kunjungan.alamat_pasien, kunjungan.carabayar_id, kunjungan.carabayar_nama, kunjungan.penjamin_id, kunjungan.penjamin_nama, kunjungan.jeniskasuspenyakit_id, kunjungan.jeniskasuspenyakit_nama, kunjungan.instalasi_id, kunjungan.instalasi_nama, kunjungan.instalasi_asal, kunjungan.ruangan_id, kunjungan.ruangan_nama, kunjungan.dokterdpjp_id, kunjungan.dokterdpjp_nama, kunjungan.diagnosa_utama_id, kunjungan.diagnosa_utama, joined_penyerta.diagnosa_penyerta_id, joined_penyerta.diagnosa_penyerta, kunjungan.status_periksa, kunjungan.id_status_periksa, kunjungan.is_status_periksa, kunjungan.jeniskelamin, kunjungan.no_telepon_pasien, kunjungan.no_mobile_pasien, kunjungan.kunjungan_id, kunjungan.kunjungan_nama, kunjungan.kondisikeluar_id, kunjungan.kondisikeluar_nama, kunjungan.carakeluar_id, kunjungan.carakeluar_nama
UNION ALL
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
    kunjungan.jeniskelamin,
    kunjungan.no_telepon_pasien,
    kunjungan.no_mobile_pasien,
    kunjungan.kunjungan_id,
    kunjungan.kunjungan_nama,
    kunjungan.kondisikeluar_id,
    kunjungan.kondisikeluar_nama,
    kunjungan.carakeluar_id,
    kunjungan.carakeluar_nama
   FROM (lapkunjunganpasienrs_v kunjungan
     JOIN ( SELECT penyerta.pendaftaran_id,
            penyerta.pasienadmisi_id,
            string_agg((penyerta.diagnosa_penyerta_id)::text, '$'::text) AS diagnosa_penyerta_id,
            string_agg(penyerta.diagnosa_penyerta, '$'::text) AS diagnosa_penyerta
           FROM lapkunjunganpasienrs_v penyerta
          WHERE (penyerta.pasienadmisi_id IS NULL)
          GROUP BY penyerta.pendaftaran_id, penyerta.pasienadmisi_id) joined_penyerta ON ((joined_penyerta.pendaftaran_id = kunjungan.pendaftaran_id)))
  WHERE (kunjungan.instalasi_id = 2)
  GROUP BY kunjungan.pendaftaran_id, kunjungan.pasienadmisi_id, kunjungan.tgl_pendaftaran, kunjungan.no_pendaftaran, kunjungan.no_rekam_medik, kunjungan.nama_pasien, kunjungan.jenis_kelamin, kunjungan.tanggal_lahir, kunjungan.umur, kunjungan.alamat_pasien, kunjungan.carabayar_id, kunjungan.carabayar_nama, kunjungan.penjamin_id, kunjungan.penjamin_nama, kunjungan.jeniskasuspenyakit_id, kunjungan.jeniskasuspenyakit_nama, kunjungan.instalasi_id, kunjungan.instalasi_nama, kunjungan.instalasi_asal, kunjungan.ruangan_id, kunjungan.ruangan_nama, kunjungan.dokterdpjp_id, kunjungan.dokterdpjp_nama, kunjungan.diagnosa_utama_id, kunjungan.diagnosa_utama, joined_penyerta.diagnosa_penyerta_id, joined_penyerta.diagnosa_penyerta, kunjungan.status_periksa, kunjungan.id_status_periksa, kunjungan.is_status_periksa, kunjungan.jeniskelamin, kunjungan.no_telepon_pasien, kunjungan.no_mobile_pasien, kunjungan.kunjungan_id, kunjungan.kunjungan_nama, kunjungan.kondisikeluar_id, kunjungan.kondisikeluar_nama, kunjungan.carakeluar_id, kunjungan.carakeluar_nama
UNION ALL
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
    kunjungan.jeniskelamin,
    kunjungan.no_telepon_pasien,
    kunjungan.no_mobile_pasien,
    kunjungan.kunjungan_id,
    kunjungan.kunjungan_nama,
    kunjungan.kondisikeluar_id,
    kunjungan.kondisikeluar_nama,
    kunjungan.carakeluar_id,
    kunjungan.carakeluar_nama
   FROM (lapkunjunganpasienrs_v kunjungan
     JOIN ( SELECT penyerta.pendaftaran_id,
            penyerta.pasienadmisi_id,
            string_agg((penyerta.diagnosa_penyerta_id)::text, '$'::text) AS diagnosa_penyerta_id,
            string_agg(penyerta.diagnosa_penyerta, '$'::text) AS diagnosa_penyerta
           FROM lapkunjunganpasienrs_v penyerta
          WHERE (penyerta.pasienadmisi_id IS NULL)
          GROUP BY penyerta.pendaftaran_id, penyerta.pasienadmisi_id) joined_penyerta ON ((joined_penyerta.pendaftaran_id = kunjungan.pendaftaran_id)))
  WHERE (kunjungan.instalasi_id = 1)
  GROUP BY kunjungan.pendaftaran_id, kunjungan.pasienadmisi_id, kunjungan.tgl_pendaftaran, kunjungan.no_pendaftaran, kunjungan.no_rekam_medik, kunjungan.nama_pasien, kunjungan.jenis_kelamin, kunjungan.tanggal_lahir, kunjungan.umur, kunjungan.alamat_pasien, kunjungan.carabayar_id, kunjungan.carabayar_nama, kunjungan.penjamin_id, kunjungan.penjamin_nama, kunjungan.jeniskasuspenyakit_id, kunjungan.jeniskasuspenyakit_nama, kunjungan.instalasi_id, kunjungan.instalasi_nama, kunjungan.instalasi_asal, kunjungan.ruangan_id, kunjungan.ruangan_nama, kunjungan.dokterdpjp_id, kunjungan.dokterdpjp_nama, kunjungan.diagnosa_utama_id, kunjungan.diagnosa_utama, joined_penyerta.diagnosa_penyerta_id, joined_penyerta.diagnosa_penyerta, kunjungan.status_periksa, kunjungan.id_status_periksa, kunjungan.is_status_periksa, kunjungan.jeniskelamin, kunjungan.no_telepon_pasien, kunjungan.no_mobile_pasien, kunjungan.kunjungan_id, kunjungan.kunjungan_nama, kunjungan.kondisikeluar_id, kunjungan.kondisikeluar_nama, kunjungan.carakeluar_id, kunjungan.carakeluar_nama
UNION ALL
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
    kunjungan.jeniskelamin,
    kunjungan.no_telepon_pasien,
    kunjungan.no_mobile_pasien,
    kunjungan.kunjungan_id,
    kunjungan.kunjungan_nama,
    kunjungan.kondisikeluar_id,
    kunjungan.kondisikeluar_nama,
    kunjungan.carakeluar_id,
    kunjungan.carakeluar_nama
   FROM (lapkunjunganpasienrs_v kunjungan
     JOIN ( SELECT penyerta.pendaftaran_id,
            penyerta.pasienadmisi_id,
            string_agg((penyerta.diagnosa_penyerta_id)::text, '$'::text) AS diagnosa_penyerta_id,
            string_agg(penyerta.diagnosa_penyerta, '$'::text) AS diagnosa_penyerta
           FROM lapkunjunganpasienrs_v penyerta
          WHERE (penyerta.pasienadmisi_id IS NULL)
          GROUP BY penyerta.pendaftaran_id, penyerta.pasienadmisi_id) joined_penyerta ON ((joined_penyerta.pendaftaran_id = kunjungan.pendaftaran_id)))
  WHERE (kunjungan.instalasi_id <> ALL (ARRAY[1, 2, 3]))
  GROUP BY kunjungan.pendaftaran_id, kunjungan.pasienadmisi_id, kunjungan.tgl_pendaftaran, kunjungan.no_pendaftaran, kunjungan.no_rekam_medik, kunjungan.nama_pasien, kunjungan.jenis_kelamin, kunjungan.tanggal_lahir, kunjungan.umur, kunjungan.alamat_pasien, kunjungan.carabayar_id, kunjungan.carabayar_nama, kunjungan.penjamin_id, kunjungan.penjamin_nama, kunjungan.jeniskasuspenyakit_id, kunjungan.jeniskasuspenyakit_nama, kunjungan.instalasi_id, kunjungan.instalasi_nama, kunjungan.instalasi_asal, kunjungan.ruangan_id, kunjungan.ruangan_nama, kunjungan.dokterdpjp_id, kunjungan.dokterdpjp_nama, kunjungan.diagnosa_utama_id, kunjungan.diagnosa_utama, joined_penyerta.diagnosa_penyerta_id, joined_penyerta.diagnosa_penyerta, kunjungan.status_periksa, kunjungan.id_status_periksa, kunjungan.is_status_periksa, kunjungan.jeniskelamin, kunjungan.no_telepon_pasien, kunjungan.no_mobile_pasien, kunjungan.kunjungan_id, kunjungan.kunjungan_nama, kunjungan.kondisikeluar_id, kunjungan.kondisikeluar_nama, kunjungan.carakeluar_id, kunjungan.carakeluar_nama
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
        echo "m210429_003854_migrate_20210429_hotfix_lapkunjunganpasienrss cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210429_003854_migrate_20210429_hotfix_lapkunjunganpasienrss cannot be reverted.\n";

        return false;
    }
    */
}

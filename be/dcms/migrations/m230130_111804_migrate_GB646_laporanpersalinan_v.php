<?php

use yii\db\Migration;

/**
 * Class m230130_111804_migrate_GB646_laporanpersalinan_v
 */
class m230130_111804_migrate_GB646_laporanpersalinan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."laporanpersalinan_v";');
        $this->execute("CREATE OR REPLACE VIEW public.laporanpersalinan_v
        AS SELECT kelahiranbayi_t.pendaftaran_id AS pendaftaranibu_id,
            pendaftaran_ibu.no_pendaftaran AS no_pendaftaran_ibu,
            pasien_ibu.nama_pasien AS nama_ibu,
            pasien_ibu.no_rekam_medik AS no_rekam_medik_ibu,
            kelahiranbayi_t.pendaftaranbaru_id AS pendaftaranbayi_id,
            pasien_bayi.nama_pasien AS nama_bayi,
            pasien_bayi.no_rekam_medik AS no_rekam_medik_bayi,
            kelahiranbayi_t.tgl_lahir AS tgl_lahir_bayi,
            kelahiranbayi_t.berat_badan,
            persalinan_t.jenis_persalinan AS jenis_persalinan_id,
            look_jeniskelamin.lookup_name AS jenis_persalinan_nama,
            COALESCE(admisi_ibu.pegawai_id, pendaftaran_ibu.pegawai_id) AS dokter_dpjp_id,
            COALESCE(dpjp_ranap_ibu.nama_pegawai, dokter_dpjp.nama_pegawai) AS dokter_dpjp_nama
           FROM kelahiranbayi_t
             JOIN ( SELECT a.pendaftaran_id,
                    a.pasienadmisi_id,
                    a.no_pendaftaran,
                    a.pegawai_id,
                    a.pasien_id
                   FROM pendaftaran_t a) pendaftaran_ibu ON kelahiranbayi_t.pendaftaran_id = pendaftaran_ibu.pendaftaran_id
             JOIN ( SELECT a.pasien_id,
                    a.nama_pasien,
                    a.no_rekam_medik
                   FROM pasien_m a) pasien_ibu ON pendaftaran_ibu.pasien_id = pasien_ibu.pasien_id
             JOIN ( SELECT a.pendaftaran_id,
                    a.pasien_id
                   FROM pendaftaran_t a) pendaftaran_bayi ON kelahiranbayi_t.pendaftaranbaru_id = pendaftaran_bayi.pendaftaran_id
             JOIN ( SELECT a.pasien_id,
                    a.nama_pasien,
                    a.no_rekam_medik
                   FROM pasien_m a) pasien_bayi ON pendaftaran_bayi.pasien_id = pasien_bayi.pasien_id
             LEFT JOIN ( SELECT a.pendaftaran_id,
                    a.jenis_persalinan
                   FROM persalinan_t a) persalinan_t ON kelahiranbayi_t.pendaftaran_id = persalinan_t.pendaftaran_id
             LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                   FROM lookup_m a) look_jeniskelamin ON persalinan_t.jenis_persalinan = look_jeniskelamin.lookup_id
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) dokter_dpjp ON pendaftaran_ibu.pegawai_id = dokter_dpjp.pegawai_id
             LEFT JOIN ( SELECT a.pasienadmisi_id,
                    a.pegawai_id
                   FROM pasienadmisi_t a) admisi_ibu ON pendaftaran_ibu.pasienadmisi_id = admisi_ibu.pasienadmisi_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) dpjp_ranap_ibu ON admisi_ibu.pegawai_id = dpjp_ranap_ibu.pegawai_id
          ORDER BY kelahiranbayi_t.pendaftaranbaru_id DESC;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230130_111804_migrate_GB646_laporanpersalinan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230130_111804_migrate_GB646_laporanpersalinan_v cannot be reverted.\n";

        return false;
    }
    */
}

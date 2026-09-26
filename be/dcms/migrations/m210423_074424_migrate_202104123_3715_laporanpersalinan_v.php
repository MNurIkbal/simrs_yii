<?php

use yii\db\Migration;

/**
 * Class m210423_074424_migrate_202104123_3715_laporanpersalinan_v
 */
class m210423_074424_migrate_202104123_3715_laporanpersalinan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporanpersalinan_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporanpersalinan_v\" AS
            SELECT kelahiranbayi_t.pendaftaran_id AS pendaftaranibu_id,
            pendaftaran_ibu.no_pendaftaran AS no_pendaftaran_ibu,
            pasien_ibu.nama_pasien AS nama_ibu,
            pasien_ibu.no_rekam_medik AS no_rekam_medik_ibu,
            kelahiranbayi_t.pendaftaranbaru_id AS pendaftaranbayi_id,
            pasien_bayi.nama_pasien AS nama_bayi,
            pasien_bayi.no_rekam_medik AS no_rekam_medik_bayi,
            kelahiranbayi_t.tgl_lahir AS tgl_lahir_bayi,
            kelahiranbayi_t.berat_badan,
            persalinan_t.jenis_persalinan AS jenis_persalinan_id,
            fgetnamalookupkeperawatan(persalinan_t.jenis_persalinan) AS jenis_persalinan_nama
            FROM (((((kelahiranbayi_t
            JOIN pendaftaran_t pendaftaran_ibu ON ((kelahiranbayi_t.pendaftaran_id = pendaftaran_ibu.pendaftaran_id)))
            JOIN pasien_m pasien_ibu ON ((pendaftaran_ibu.pasien_id = pasien_ibu.pasien_id)))
            JOIN pendaftaran_t pendaftaran_bayi ON ((kelahiranbayi_t.pendaftaranbaru_id = pendaftaran_bayi.pendaftaran_id)))
            JOIN pasien_m pasien_bayi ON ((pendaftaran_bayi.pasien_id = pasien_bayi.pasien_id)))
            LEFT JOIN persalinan_t ON ((kelahiranbayi_t.pendaftaran_id = persalinan_t.pendaftaran_id)))
            ORDER BY kelahiranbayi_t.pendaftaranbaru_id DESC
            ;");
            $this->execute('
                ALTER TABLE public.laporanpersalinan_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210423_074424_migrate_202104123_3715_laporanpersalinan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210423_074424_migrate_202104123_3715_laporanpersalinan_v cannot be reverted.\n";

        return false;
    }
    */
}

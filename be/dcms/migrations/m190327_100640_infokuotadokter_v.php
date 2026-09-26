<?php

use yii\db\Migration;

/**
 * Class m190327_100640_infokuotadokter_v
 */
class m190327_100640_infokuotadokter_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW infokuotadokter_v;
        ');

        $this->execute('
            CREATE OR REPLACE VIEW infokuotadokter_v AS 
             SELECT kuotadokter_r.kuotadokter_id,
                kuotadokter_r.jadwaldokter_id,
                NULL::integer AS jadwalbukapoli_id,
                jadwaldokter_m.pegawai_id,
                jadwaldokter_m.instalasi_id,
                jadwaldokter_m.ruangan_id,
                jadwaldokter_m.shift_id,
                instalasi_m.instalasi_nama,
                ruangan_m.ruangan_nama,
                pegawai_m.nama_pegawai,
                jadwalbukapoli_m.hari,
                fgetnamalookup(jadwalbukapoli_m.hari) AS nama_hari,
                jadwaldokter_m.jadwaldokter_mulai,
                jadwaldokter_m.jadwaldokter_tutup,
                kuotadokter_r.kuota_real,
                kuotadokter_r.kuota_masuk,
                kuotadokter_r.kuota_keluar,
                kuotadokter_r.kuota_tersedia,
                kuotadokter_r.is_online,
                jadwaldokter_m.is_active
               FROM kuotadokter_r
                 JOIN jadwaldokter_m ON kuotadokter_r.jadwaldokter_id = jadwaldokter_m.jadwaldokter_id AND jadwaldokter_m.is_deleted = false
                 JOIN ruangan_m ON jadwaldokter_m.ruangan_id = ruangan_m.ruangan_id
                 JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                 LEFT JOIN pegawai_m ON jadwaldokter_m.pegawai_id = pegawai_m.pegawai_id
                 LEFT JOIN jadwaldoktertambahan_m ON jadwaldokter_m.jadwaldokter_id = jadwaldoktertambahan_m.jadwaldokter_id
                 LEFT JOIN jadwalbukapoli_m ON jadwaldokter_m.jadwalbukapoli_id = jadwalbukapoli_m.jadwalbukapoli_id AND jadwalbukapoli_m.is_deleted = false
            UNION ALL
             SELECT kuotadokter_r.kuotadokter_id,
                NULL::integer AS jadwaldokter_id,
                kuotadokter_r.jadwalbukapoli_id,
                NULL::integer AS pegawai_id,
                instalasi_m.instalasi_id,
                jadwalbukapoli_m.ruangan_id,
                jadwalbukapoli_m.shift_id,
                instalasi_m.instalasi_nama,
                ruangan_m.ruangan_nama,
                NULL::character varying AS nama_pegawai,
                jadwalbukapoli_m.hari,
                fgetnamalookup(jadwalbukapoli_m.hari) AS nama_hari,
                jadwalbukapoli_m.jam_mulai AS jadwaldokter_mulai,
                jadwalbukapoli_m.jam_tutup AS jadwaldokter_tutup,
                kuotadokter_r.kuota_real,
                kuotadokter_r.kuota_masuk,
                kuotadokter_r.kuota_keluar,
                kuotadokter_r.kuota_tersedia,
                kuotadokter_r.is_online,
                jadwalbukapoli_m.is_active
               FROM kuotadokter_r
                 JOIN jadwalbukapoli_m ON kuotadokter_r.jadwalbukapoli_id = jadwalbukapoli_m.jadwalbukapoli_id AND jadwalbukapoli_m.is_deleted = false
                 JOIN ruangan_m ON jadwalbukapoli_m.ruangan_id = ruangan_m.ruangan_id
                 JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id;
        ');

        $this->execute('
            ALTER TABLE infokuotadokter_v
              OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190327_100640_infokuotadokter_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190327_100640_infokuotadokter_v cannot be reverted.\n";

        return false;
    }
    */
}

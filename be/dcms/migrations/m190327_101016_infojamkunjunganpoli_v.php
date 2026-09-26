<?php

use yii\db\Migration;

/**
 * Class m190327_101016_infojamkunjunganpoli_v
 */
class m190327_101016_infojamkunjunganpoli_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW infojamkunjunganpoli_v;
        ');

        $this->execute('
            CREATE OR REPLACE VIEW infojamkunjunganpoli_v AS 
             SELECT jadwalbukapoli_id,
                instalasi_id,
                instalasi_nama,
                ruangan_id,
                ruangan_nama,
                hari_id,
                hari,
                waktu,
                jam_mulai,
                jam_tutup,
                shift_id,
                shift_nama,
                kuota_offline,
                y.kuota_tersedia_offline,
                kuota_online,
                x.kuota_tersedia_online,
                x.is_active
               FROM ( SELECT jadwalbukapoli_m.jadwalbukapoli_id,
                        ruangan_m.instalasi_id,
                        instalasi_m.instalasi_nama,
                        jadwalbukapoli_m.ruangan_id,
                        ruangan_m.ruangan_nama,
                        jadwalbukapoli_m.waktu_pelayanan AS waktu,
                        jadwalbukapoli_m.jam_mulai,
                        jadwalbukapoli_m.jam_tutup,
                        jadwalbukapoli_m.hari AS hari_id,
                        lookup_m.lookup_name AS hari,
                        jadwalbukapoli_m.is_active,
                        COALESCE(shift_m.shift_id, 0) AS shift_id,
                        COALESCE(shift_m.shift_nama, \'\'::character varying) AS shift_nama,
                        jadwalbukapoli_m.maxantrian_poli AS kuota_offline,
                        jadwalbukapoli_m.kuota_online,
                        kuotadokter_r.kuota_tersedia AS kuota_tersedia_online
                       FROM jadwalbukapoli_m
                         JOIN ruangan_m ON jadwalbukapoli_m.ruangan_id = ruangan_m.ruangan_id
                         JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                         JOIN lookup_m ON jadwalbukapoli_m.hari = lookup_m.lookup_id
                         LEFT JOIN shift_m ON jadwalbukapoli_m.shift_id = shift_m.shift_id
                         JOIN kuotadokter_r ON jadwalbukapoli_m.jadwalbukapoli_id = kuotadokter_r.jadwalbukapoli_id AND kuotadokter_r.is_online IS TRUE
                      WHERE jadwalbukapoli_m.is_deleted IS FALSE) x
                 FULL JOIN ( SELECT jadwalbukapoli_m.jadwalbukapoli_id,
                        ruangan_m.instalasi_id,
                        instalasi_m.instalasi_nama,
                        jadwalbukapoli_m.ruangan_id,
                        ruangan_m.ruangan_nama,
                        jadwalbukapoli_m.waktu_pelayanan AS waktu,
                        jadwalbukapoli_m.jam_mulai,
                        jadwalbukapoli_m.jam_tutup,
                        jadwalbukapoli_m.hari AS hari_id,
                        lookup_m.lookup_name AS hari,
                        jadwalbukapoli_m.is_active,
                        COALESCE(shift_m.shift_id, 0) AS shift_id,
                        COALESCE(shift_m.shift_nama, \'\'::character varying) AS shift_nama,
                        jadwalbukapoli_m.maxantrian_poli AS kuota_offline,
                        jadwalbukapoli_m.kuota_online,
                        kuotadokter_r.kuota_tersedia AS kuota_tersedia_offline
                       FROM jadwalbukapoli_m
                         JOIN ruangan_m ON jadwalbukapoli_m.ruangan_id = ruangan_m.ruangan_id
                         JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                         JOIN lookup_m ON jadwalbukapoli_m.hari = lookup_m.lookup_id
                         LEFT JOIN shift_m ON jadwalbukapoli_m.shift_id = shift_m.shift_id
                         JOIN kuotadokter_r ON jadwalbukapoli_m.jadwalbukapoli_id = kuotadokter_r.jadwalbukapoli_id AND kuotadokter_r.is_online IS FALSE
                      WHERE jadwalbukapoli_m.is_deleted IS FALSE) y USING (jadwalbukapoli_id, instalasi_id, instalasi_nama, ruangan_id, ruangan_nama, hari_id, hari, waktu, jam_mulai, jam_tutup, shift_id, shift_nama, kuota_offline, kuota_online);
        ');

        $this->execute('
            ALTER TABLE infojamkunjunganpoli_v
              OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190327_101016_infojamkunjunganpoli_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190327_101016_infojamkunjunganpoli_v cannot be reverted.\n";

        return false;
    }
    */
}

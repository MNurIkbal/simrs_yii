<?php

use yii\db\Migration;

/**
 * Class m210901_070529_migrate_US1160_daerah_v
 */
class m210901_070529_migrate_US1160_daerah_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.daerah_v;');
        $this->execute("
            CREATE VIEW \"public\".\"daerah_v\" AS
            SELECT propinsi_m.propinsi_id,
            propinsi_m.kode_propinsi,
            propinsi_m.propinsi_nama,
            kabupaten_m.kabupaten_id,
            kabupaten_m.kabupaten_nama,
            kabupaten_m.kode_kabupaten,
            kecamatan_m.kecamatan_id,
            kecamatan_m.kecamatan_nama,
            kecamatan_m.kode_kecamatan,
            kelurahan_m.kelurahan_id,
            kelurahan_m.kelurahan_nama,
            kelurahan_m.kode_kelurahan,
            propinsi_m.kode_propinsi AS kode_kemendag_propinsi,
            concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten) AS kode_kemendag_kabupaten,
            concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten, kecamatan_m.kode_kecamatan) AS kode_kemendag_kecamatan,
            concat(propinsi_m.kode_propinsi, kabupaten_m.kode_kabupaten, kecamatan_m.kode_kecamatan, kelurahan_m.kode_kelurahan) AS kode_kemendag_kelurahan
            FROM (((propinsi_m
            JOIN kabupaten_m ON ((propinsi_m.propinsi_id = kabupaten_m.propinsi_id)))
            JOIN kecamatan_m ON ((kabupaten_m.kabupaten_id = kecamatan_m.kabupaten_id)))
            JOIN kelurahan_m ON ((kecamatan_m.kecamatan_id = kelurahan_m.kecamatan_id)))
            WHERE ((propinsi_m.is_deleted = false) AND (propinsi_m.is_active = true) AND (kabupaten_m.is_deleted = false) AND (kabupaten_m.is_active = true) AND (kecamatan_m.is_deleted = false) AND (kecamatan_m.is_active = true) AND (kelurahan_m.is_deleted = false) AND (kelurahan_m.is_active = true))
            ORDER BY propinsi_m.propinsi_id
            ;");
        $this->execute('
            ALTER TABLE public.daerah_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210901_070529_migrate_US1160_daerah_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210901_070529_migrate_US1160_daerah_v cannot be reverted.\n";

        return false;
    }
    */
}

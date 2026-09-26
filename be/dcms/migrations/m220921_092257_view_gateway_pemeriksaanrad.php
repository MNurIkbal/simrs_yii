<?php

use yii\db\Migration;

/**
 * Class m220921_092257_view_gateway_pemeriksaanrad
 */
class m220921_092257_view_gateway_pemeriksaanrad extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."gt_pemeriksaanrad_v";
        ');

        $this->execute("
        CREATE OR REPLACE VIEW \"public\".\"gt_pemeriksaanrad_v\"
        AS SELECT pemeriksaanrad_m.pemeriksaanradiologi_id,
            pemeriksaanrad_m.pemeriksaanrad_kode AS kode_pemeriksaanrad,
            pemeriksaanrad_m.pemeriksaanrad_nama AS nama_pemeriksaanrad,
            jenispemeriksaanrad_m.jenispemeriksaanrad_id,
            jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS jenis_pemeriksaanrad,
            pemeriksaanrad_m.kelompokpemeriksaanrad_id,
            kelompokpemeriksaanrad_m.nama_kelompok AS kelompok_pemeriksaanrad
        FROM pemeriksaanrad_m
            LEFT JOIN ( SELECT a.jenispemeriksaanrad_id,
                    a.jenispemeriksaanrad_kode,
                    a.jenispemeriksaanrad_nama
                FROM jenispemeriksaanrad_m a) jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
            LEFT JOIN ( SELECT a.kelompokpemeriksaanrad_id,
                    a.nama_kelompok
                FROM kelompokpemeriksaanrad_m a) kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id
        WHERE pemeriksaanrad_m.is_deleted = false;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220921_092257_view_gateway_pemeriksaanrad cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220921_092257_view_gateway_pemeriksaanrad cannot be reverted.\n";

        return false;
    }
    */
}

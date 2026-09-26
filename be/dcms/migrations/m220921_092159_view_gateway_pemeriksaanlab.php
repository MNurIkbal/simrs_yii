<?php

use yii\db\Migration;

/**
 * Class m220921_092159_view_gateway_pemeriksaanlab
 */
class m220921_092159_view_gateway_pemeriksaanlab extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."gt_pemeriksaanlab_v";
        ');

        $this->execute("
        CREATE OR REPLACE VIEW \"public\".\"gt_pemeriksaanlab_v\"
        AS SELECT pemeriksaanlab_m.pemeriksaanlab_id,
            pemeriksaanlab_m.pemeriksaanlab_kode AS kode_pemeriksaanlab,
            pemeriksaanlab_m.pemeriksaanlab_nama AS nama_pemeriksaanlab,
            jenispemeriksaanlab_m.jenispemeriksaanlab_id,
            jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS jenis_pemeriksaanlab,
            pemeriksaanlab_m.kelompokpemeriksaanlab_id,
            kelompokpemeriksaanlab_m.nama_kelompok AS kelompok_pemeriksaanlab
           FROM pemeriksaanlab_m
             LEFT JOIN ( SELECT a.jenispemeriksaanlab_id,
                    a.jenispemeriksaanlab_kode,
                    a.jenispemeriksaanlab_nama
                   FROM jenispemeriksaanlab_m a) jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
             LEFT JOIN ( SELECT a.kelompokpemeriksaanlab_id,
                    a.nama_kelompok
                   FROM kelompokpemeriksaanlab_m a) kelompokpemeriksaanlab_m ON pemeriksaanlab_m.kelompokpemeriksaanlab_id = kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id
          WHERE pemeriksaanlab_m.is_deleted = false;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220921_092159_view_gateway_pemeriksaanlab cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220921_092159_view_gateway_pemeriksaanlab cannot be reverted.\n";

        return false;
    }
    */
}

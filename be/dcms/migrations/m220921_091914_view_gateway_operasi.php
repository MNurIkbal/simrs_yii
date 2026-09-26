<?php

use yii\db\Migration;

/**
 * Class m220921_091914_view_gateway_operasi
 */
class m220921_091914_view_gateway_operasi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."gt_operasi_v";
        ');

        $this->execute("
        CREATE OR REPLACE VIEW \"public\".\"gt_operasi_v\"
        AS SELECT operasi_m.operasi_id,
            operasi_m.operasi_kode AS kode_operasi,
            operasi_m.operasi_nama AS nama_operasi,
            golonganoperasi_m.golonganoperasi_id,
            golonganoperasi_m.golonganoperasi_nama AS golongan_operasi,
            operasi_m.kegiatanoperasi_id,
            kegiatanoperasi_m.kegiatanoperasi_nama AS kegiatan_operasi
        FROM operasi_m
            LEFT JOIN ( SELECT a.golonganoperasi_id,
                    a.golonganoperasi_kode,
                    a.golonganoperasi_nama
                FROM golonganoperasi_m a) golonganoperasi_m ON operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id
            LEFT JOIN ( SELECT a.kegiatanoperasi_id,
                    a.kegiatanoperasi_nama
                FROM kegiatanoperasi_m a) kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id
        WHERE operasi_m.is_deleted = false;
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220921_091914_view_gateway_operasi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220921_091914_view_gateway_operasi cannot be reverted.\n";

        return false;
    }
    */
}

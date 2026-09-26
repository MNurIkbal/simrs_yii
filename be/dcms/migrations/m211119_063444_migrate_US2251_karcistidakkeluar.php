<?php

use yii\db\Migration;

/**
 * Class m211119_063444_migrate_US2251_karcistidakkeluar
 */
class m211119_063444_migrate_US2251_karcistidakkeluar extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."lookuptransaksi_m" 
          ADD COLUMN IF NOT EXISTS "kode_nama" varchar(255) COLLATE "pg_catalog"."default",
          ADD COLUMN IF NOT EXISTS "kode_singkatan" varchar(255) COLLATE "pg_catalog"."default";
        ');

        $this->execute("update lookuptransaksi_m set kode_nama = 'pendaftaran rajal', kode_singkatan = 'RJ'  where additional_value = 'rajal' ;
            ");

        $this->execute("update lookuptransaksi_m set kode_nama = 'pendaftaran ranap', kode_singkatan = 'RI'  where additional_value = 'ranap' ;
            ");

        $this->execute("update lookuptransaksi_m set kode_nama = 'pendaftaran igd', kode_singkatan = 'IGD'  where additional_value = 'igd' ;
            ");

        $this->execute("update lookuptransaksi_m set kode_nama = 'pendaftaran mcu', kode_singkatan = 'MCU'  where additional_value = 'mcu' ;
            ");

        $this->execute("update lookuptransaksi_m set kode_nama = 'pendaftaran penunjang', kode_singkatan = 'RJ'  where additional_value = 'penunjang' ;
            ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211119_063444_migrate_US2251_karcistidakkeluar cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211119_063444_migrate_US2251_karcistidakkeluar cannot be reverted.\n";

        return false;
    }
    */
}

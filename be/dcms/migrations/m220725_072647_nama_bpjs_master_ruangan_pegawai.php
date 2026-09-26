<?php

use yii\db\Migration;

/**
 * Class m220725_072647_nama_bpjs_master_ruangan_pegawai
 */
class m220725_072647_nama_bpjs_master_ruangan_pegawai extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."ruangan_m" 
                ADD COLUMN IF NOT EXISTS "nama_ruangan_bpjs" varchar(50);
        ');
                                            
        $this->execute('
            ALTER TABLE "public"."pegawai_m" 
                ADD COLUMN IF NOT EXISTS "nama_dokter_bpjs" varchar(50);
        ');
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220725_072647_nama_bpjs_master_ruangan_pegawai cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220725_072647_nama_bpjs_master_ruangan_pegawai cannot be reverted.\n";

        return false;
    }
    */
}

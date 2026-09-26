<?php

use yii\db\Migration;

/**
 * Class m251128_071030_migration_alter_rujukanbantaran_t
 */
class m251128_071030_migration_alter_rujukanbantaran_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE public.rujukanbantaran_t ADD IF NOT EXISTS no_tahanan varchar(100) NULL;
        ');
        
        $this->execute('
            ALTER TABLE public.rujukanbantaran_t ADD IF NOT EXISTS no_telepon varchar(20) NULL;
        ');
        
        $this->execute('
            ALTER TABLE public.rujukanbantaran_t ADD IF NOT EXISTS instalasi_nama varchar(100) NULL;  
        ');
        
        $this->execute('
            ALTER TABLE public.rujukanbantaran_t ADD IF NOT EXISTS ruangan_nama varchar(100) NULL;
        ');
            
        $this->execute('
            ALTER TABLE public.rujukanbantaran_t ADD IF NOT EXISTS dokter_nama varchar(100) NULL;
        ');     
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251128_071030_migration_alter_rujukanbantaran_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251128_071030_migration_alter_rujukanbantaran_t cannot be reverted.\n";

        return false;
    }
    */
}

<?php

use yii\db\Migration;

/**
 * Class m230304_022523_alter_klaimgroup_eklaim
 */
class m221213_022523_alter_klaimgroup_eklaim extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE klaimgroup_t ADD IF NOT EXISTS add_jenazah text NULL;
        ');
        $this->execute('
            ALTER TABLE klaimgroup_t ADD IF NOT EXISTS cbg text NULL;
        ');
        $this->execute('
            ALTER TABLE klaimgroup_t ADD IF NOT EXISTS special_group text NULL;
        ');
        $this->execute('
            ALTER TABLE klaimgroup_t ADD IF NOT EXISTS group_tarif float8 NULL;
        ');
        $this->execute('
            ALTER TABLE klaimgroup_t ADD IF NOT EXISTS sp_procedure_kode varchar(100) NULL;
        ');
        $this->execute('
            ALTER TABLE klaimgroup_t ADD IF NOT EXISTS sp_prosthesis_kode varchar(100) NULL;
        ');
        $this->execute('
            ALTER TABLE klaimgroup_t ADD IF NOT EXISTS sp_investigation_kode varchar(100) NULL;
        ');
        $this->execute('
            ALTER TABLE klaimgroup_t ADD IF NOT EXISTS sp_drug_kode varchar(100) NULL;
        ');
        $this->execute('
            ALTER TABLE klaimgroup_t ADD IF NOT EXISTS sp_procedure_nama varchar(255) NULL;
        ');
        $this->execute('
            ALTER TABLE klaimgroup_t ADD IF NOT EXISTS sp_prosthesis_nama varchar(255) NULL;
        ');
        $this->execute('
            ALTER TABLE klaimgroup_t ADD IF NOT EXISTS sp_investigation_nama varchar(255) NULL;
        ');
        $this->execute('
            ALTER TABLE klaimgroup_t ADD IF NOT EXISTS sp_drug_nama varchar(255) NULL;
        ');
        $this->execute('
            ALTER TABLE klaimgroup_t ADD IF NOT EXISTS add_episode text NULL;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230304_022523_alter_klaimgroup_eklaim cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230304_022523_alter_klaimgroup_eklaim cannot be reverted.\n";

        return false;
    }
    */
}

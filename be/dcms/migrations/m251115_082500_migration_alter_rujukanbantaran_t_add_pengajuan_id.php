<?php

use yii\db\Migration;

/**
 * Class m251115_082500_migration_alter_rujukanbantaran_t_add_pengajuan_id
 */
class m251115_082500_migration_alter_rujukanbantaran_t_add_pengajuan_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE public.rujukanbantaran_t ADD pengajuan_id int4 NULL;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251115_082500_migration_alter_rujukanbantaran_t_add_pengajuan_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251115_082500_migration_alter_rujukanbantaran_t_add_pengajuan_id cannot be reverted.\n";

        return false;
    }
    */
}

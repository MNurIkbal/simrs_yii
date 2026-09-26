<?php

use yii\db\Migration;

/**
 * Class m240516_105355_migrate_kasir_invalidbills_fn_add_union
 */
class m240516_105355_migrate_kasir_invalidbills_fn_add_union extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP FUNCTION public.invalidbills_fn(date, date);");
        $invalidbills_fn = file_get_contents(__DIR__ . '/definitions/invalidbills.fn.sql');
        $this->execute($invalidbills_fn);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240516_105355_migrate_kasir_invalidbills_fn_add_union cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240516_105355_migrate_kasir_invalidbills_fn_add_union cannot be reverted.\n";

        return false;
    }
    */
}

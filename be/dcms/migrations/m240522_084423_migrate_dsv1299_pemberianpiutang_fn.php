<?php

use yii\db\Migration;

/**
 * Class m240522_084423_migrate_dsv1299_pemberianpiutang_fn
 */
class m240522_084423_migrate_dsv1299_pemberianpiutang_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DROP FUNCTION IF EXISTS public.pemberianpiutang_fn(varchar, int4, int4);");
        $this->execute("CREATE INDEX IF NOT EXISTS pemberianpiutan_pendaftaran_idx ON public.pemberianpiutang_t USING btree (pendaftaran_id);");
        $this->execute("CREATE INDEX IF NOT EXISTS bayaruangmuka_pendaftaran_id ON public.bayaruangmuka_t USING btree (pendaftaran_id);");
        $pemberianpiutang_fn = file_get_contents(__DIR__ . '/definitions/pemberianpiutang.fn.sql');
        $this->execute($pemberianpiutang_fn);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("DROP INDEX IF EXISTS pemberianpiutan_pendaftaran_idx;");
        $this->execute("DROP INDEX IF EXISTS bayaruangmuka_pendaftaran_id;");
        $this->execute("DROP FUNCTION IF EXISTS public.pemberianpiutang_fn(varchar, int4, int4);");
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240522_084423_migrate_dsv1299_pemberianpiutang_fn cannot be reverted.\n";

        return false;
    }
    */
}

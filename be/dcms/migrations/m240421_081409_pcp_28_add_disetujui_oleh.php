<?php

use yii\db\Migration;

/**
 * Class m240421_081409_pcp_28_add_disetujui_oleh
 */
class m240421_081409_pcp_28_add_disetujui_oleh extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."permintaankonsul_t" ADD COLUMN if not exists "disetujui_oleh" int4 null;');

        $this->execute('ALTER TABLE "public"."konsulpoli_t" ADD COLUMN if not exists "disetujui_oleh" int4 null;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240421_081409_pcp_28_add_disetujui_oleh cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240421_081409_pcp_28_add_disetujui_oleh cannot be reverted.\n";

        return false;
    }
    */
}

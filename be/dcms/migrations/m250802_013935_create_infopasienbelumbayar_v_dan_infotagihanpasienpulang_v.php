<?php

use yii\db\Migration;

/**
 * Class m250802_013935_create_infopasienbelumbayar_v_dan_infotagihanpasienpulang_v
 */
class m250802_013935_create_infopasienbelumbayar_v_dan_infotagihanpasienpulang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."infopasienbelumbayar_v";');

        $infopasienbelumbayar_v = file_get_contents(__DIR__ . '/definitions/infopasienbelumbayar_v_02082025.sql');
        $this->execute($infopasienbelumbayar_v);
        $this->execute('ALTER TABLE "public"."infopasienbelumbayar_v" OWNER TO "postgres";');

        $this->execute('DROP VIEW IF EXISTS "public"."infotagihanpasienpulang_v";');

        $infotagihanpasienpulang_v = file_get_contents(__DIR__ . '/definitions/infotagihanpasienpulang_v_02082025.sql');
        $this->execute($infotagihanpasienpulang_v);
        $this->execute('ALTER TABLE "public"."infotagihanpasienpulang_v" OWNER TO "postgres";');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250802_013935_create_infopasienbelumbayar_v_dan_infotagihanpasienpulang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250802_013935_create_infopasienbelumbayar_v_dan_infotagihanpasienpulang_v cannot be reverted.\n";

        return false;
    }
    */
}

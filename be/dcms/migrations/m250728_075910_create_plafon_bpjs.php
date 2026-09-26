<?php

use yii\db\Migration;

/**
 * Class m250728_075910_create_plafon_bpjs
 */
class m250728_075910_create_plafon_bpjs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $plafonbpjs_m = file_get_contents(__DIR__ . '/definitions/plafonbpjs_m_migrations.sql');
        $this->execute($plafonbpjs_m);
        $this->execute('ALTER TABLE "public"."plafonbpjs_m" OWNER TO "postgres";');

        $historiplafonbpjs_r = file_get_contents(__DIR__ . '/definitions/historiplafonbpjs_r_migrations.sql');
        $this->execute($historiplafonbpjs_r);
        $this->execute('ALTER TABLE "public"."historiplafonbpjs_r" OWNER TO "postgres";');

        $this->execute('DROP FUNCTION IF EXISTS trigger_log_plafonbpjs_changes ;');
        $trigger_log_plafonbpjs_changes_migrations = file_get_contents(__DIR__ . '/definitions/trigger_log_plafonbpjs_changes_migrations.sql');
        $this->execute($trigger_log_plafonbpjs_changes_migrations);
        $this->execute('DROP TRIGGER IF EXISTS trigger_log_plafonbpjs_changes ON plafonbpjs_m;');
        $this->execute('CREATE TRIGGER trigger_log_plafonbpjs_changes AFTER INSERT OR UPDATE OR DELETE ON plafonbpjs_m FOR EACH ROW EXECUTE PROCEDURE log_plafonbpjs_changes();');

        $this->execute('DROP VIEW IF EXISTS plafonbpjs_v;');
        $plafonbpjs_v = file_get_contents(__DIR__ . '/definitions/plafonbpjs_v_migrations.sql');
        $this->execute($plafonbpjs_v);
        $this->execute('ALTER TABLE "public"."plafonbpjs_v" OWNER TO "postgres";');

        $histori_plafon_bpjs_pasien_r = file_get_contents(__DIR__ . '/definitions/histori_plafon_bpjs_pasien_r_migrations.sql');
        $this->execute($histori_plafon_bpjs_pasien_r);
        $this->execute('ALTER TABLE "public"."histori_plafon_bpjs_pasien_r" OWNER TO "postgres";');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250728_075910_create_plafon_bpjs cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250728_075910_create_plafon_bpjs cannot be reverted.\n";

        return false;
    }
    */
}

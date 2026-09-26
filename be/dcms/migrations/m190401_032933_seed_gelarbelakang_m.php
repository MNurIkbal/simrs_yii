<?php

use yii\db\Migration;

/**
 * Class m190401_032933_seed_gelarbelakang_m
 */
class m190401_032933_seed_gelarbelakang_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            TRUNCATE TABLE gelarbelakang_m RESTART IDENTITY;
        ');

        $this->execute('
                INSERT INTO "public"."gelarbelakang_m"("gelarbelakang_id", "gelarbelakang_nama", "gelarbelakang_namalainnya", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES 
                (1, \'M.Pd.\', \'M.Pd.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (2, \'MT.\', \'MT.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (3, \'MH.\', \'MH.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (4, \'M.Hum.\', \'M.Hum.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (5, \'MTI.\', \'MTI.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (6, \'M.Ak.\', \'M.Ak.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (7, \'MARS.\', \'MARS.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (8, \'M.Kn.\', \'M.Kn.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (9, \'SKM.\', \'SKM.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (10, \'SE.\', \'SE.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (11, \'SH.\', \'SH.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (12, \'STP.\', \'STP.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (13, \'S.Ag.\', \'S.Ag.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (14, \'S.Pd.\', \'S.Pd.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (15, \'S.Kom.\', \'S.Kom.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (16, \'S.Sos.\', \'S.Sos.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (17, \'S.PdI.\', \'S.PdI.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (18, \'S.Th.\', \'S.Th.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (19, \'AMKeb\', \'AMKeb\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (20, \'AMK\', \'AMK\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (21, \'Sp.OG.\', \'Sp.OG.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (22, \'S.IP.\', \'S.IP.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (23, \'Sp.PD.\', \'Sp.PD.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (24, \'Sp.M.\', \'Sp.M.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (25, \'Sp.A.\', \'Sp.A.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (26, \'Sp.B.\', \'Sp.B.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (27, \'Sp.OT.\', \'Sp.OT.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (28, \'S.Kep.Ners.\', \'S.Kep.Ners.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (29, \'Sp.KJ.\', \'Sp.KJ.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (30, \'Sp.PK.\', \'Sp.PK.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (31, \'Sp.R.\', \'Sp.R.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (32, \'Sp.An.\', \'Sp.An.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (33, \'Sp.S.\', \'Sp.S.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (34, \'Sp.THT.\', \'Sp.THT.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (35, \'Sp.KK.\', \'Sp.KK.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (36, \'M.Kes.\', \'M.Kes.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (37, \'S.Si.Apt\', \'S.Si.Apt\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (38, \'Amd.Kom\', \'Amd.Kom\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (39, \'Amd.AK\', \'Amd.AK\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (40, \'Sp. Rad\', \'Sp. Rad\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (41, \'S.Kep\', \'S.Kep\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (42, \'SpPed\', \'SpPed\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (43, \'M.kes\', \'M.kes\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (44, \'S.Sos\', \'S.Sos\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (45, \'S.Sos\', \'S.Sos\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (46, \'SpA,M.Kes\', \'SpA,M.Kes\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (47, \'S.Sos, M.Kes\', \'S.Sos, M.Kes\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (48, \'SKM,MSi\', \'SKM,MSi\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (49, \'AMKG\', \'AMKG\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (50, \'Amd. Rad\', \'Amd. Rad\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (51, \'Sp.THT-KL\', \'Sp.THT-KL\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (52, \'SpPA,M.KES\', \'SpPA,M.KES\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (53, \'SpF\', \'SpF\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (54, \'SE, MSi\', \'SE, MSi\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (55, \'SpJP\', \'SpJP\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (56, \'SpJP\', \'SpJP\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (57, \'AMd.Ft\', \'AMd.Ft\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (58, \'s.si\', \'s.si\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (59, \'SKM, MA, MSE\', \'SKM, MA, MSE\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (60, \'SpOT.Spine\', \'SpOT.Spine\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (61, \'SP\', \'SP\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (62, \'AMd.Gz\', \'AMd.Gz\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (63, \'PS\', \'PS\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (64, \'SpOG, M.Kes\', \'SpOG, M.Kes\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (65, \'S.Kep.Ns\', \'S.Kep.Ns\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (66, \'Amd.ET\', \'Amd.ET\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (67, \'AMK\', \'AMK\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (68, \'AMd\', \'AMd\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (69, \'AMD\', \'AMD\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (70, \'SP.,AMK\', \'SP.,AMK\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (71, \'S.Pd\', \'S.Pd\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (72, \'Bd\', \'Bd\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (73, \'MM.\', \'MM.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (74, \'SJM\', \'SJM\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (75, \'M.Si.\', \'M.Si.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (76, \'S.ST.\', \'S.ST.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (77, \'S.Kep,MSi\', \'S.Kep,MSi\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (78, \'S.T.\', \'S.T.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (79, \'S.Gz\', \'S.Gz\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (80, \'Sp.BS\', \'Sp.BS\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (81, \'Sp.PD\', \'Sp.PD\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (82, \'SpM\', \'SpM\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (83, \'SpP\', \'SpP\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (84, \'SpS\', \'SpS\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (85, \'M.MSI.\', \'M.MSI.\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (86, \'S.Psi\', \'S.Psi\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (87, \'S.Farm.,Apt\', \'S.Farm.,Apt\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (88, \'A.Md.PK\', \'A.Md.PK\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_032933_seed_gelarbelakang_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_032933_seed_gelarbelakang_m cannot be reverted.\n";

        return false;
    }
    */
}

<?php

use yii\db\Migration;

/**
 * Class m191001_031657_trigger_reseptur_t
 */
class m191001_031657_trigger_reseptur_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
/*Trigger: trigger_update_reseptur_t on public.reseptur_t*/
        $this->execute('DROP TRIGGER if exists trigger_update_reseptur_t ON public.reseptur_t;');

        $this->execute('DROP FUNCTION if exists public.reseptur_t_update();');

        $this->execute("
            CREATE OR REPLACE FUNCTION public.reseptur_t_update()
  RETURNS trigger AS
\$BODY\$
DECLARE
vpenjualanresep_id int4;
vcreated_id int4;

BEGIN
    vpenjualanresep_id := NEW.penjualanresep_id;
    vcreated_id := NEW.last_modified_by;
    
    IF (NEW.pasienadmisi_id IS NOT NULL)
    THEN
        INSERT INTO stokobatpasien_r(
            obatalkespasien_id, obatalkes_id, nama_obat, 
            satuan_kecil, stok_layak, stok_sisa, stok_dipakai, stok_pending, 
            stok_retur, stok_retur_sisa, is_retur, created_date, 
            created_by, is_deleted, is_active) 
        SELECT 
            obatalkespasien_t.obatalkespasien_id, obatalkespasien_t.obatalkes_id, obatalkes_m.obatalkes_nama ,
            satuanunit_m.satuanunit_nama, obatalkespasien_t.qty_oa, obatalkespasien_t.qty_oa, 0, 0, 
            0, 0, false, CURRENT_TIMESTAMP, 
            vcreated_id, FALSE, TRUE
        FROM obatalkespasien_t
        JOIN (
            SELECT obatalkes_m.obatalkes_id, obatalkes_m.obatalkes_nama , obatalkes_m.satuankecil_id
            FROM obatalkes_m
        ) AS obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
        LEFT JOIN (
            SELECT satuanunit_m.satuanunit_id, satuanunit_m.satuanunit_nama 
            FROM satuanunit_m
        ) AS satuanunit_m ON obatalkes_m.satuankecil_id = satuanunit_m.satuanunit_id
        WHERE penjualanresep_id = vpenjualanresep_id;
        
    END IF;
    
    RETURN NEW;
END;
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");
        
        $this->execute('ALTER FUNCTION public.reseptur_t_update()
  OWNER TO postgres;');
        
        $this->execute('CREATE TRIGGER trigger_update_reseptur_t
  AFTER UPDATE OF penjualanresep_id
  ON public.reseptur_t
  FOR EACH ROW
  EXECUTE PROCEDURE public.reseptur_t_update();');

/*Trigger: update_reseptur on public.reseptur_t*/

    $this->execute('DROP TRIGGER if exists update_reseptur ON public.reseptur_t;');

    $this->execute('DROP FUNCTION if exists public.update_stokobatalkes_r_batalreseptur();');

    $this->execute("
        CREATE OR REPLACE FUNCTION public.update_stokobatalkes_r_batalreseptur()
  RETURNS trigger AS
\$BODY\$
-- trigger batal reseptur
-- @Rizqi Febian
DECLARE 
var_status_reseptur INTEGER;
var_ruangan_id INTEGER;
var_obatalkes_id INTEGER;
var_reseptur_id INTEGER;
var_qty_before INTEGER;
var_stokobatr_id INTEGER;
var_qty_tersedia INTEGER;
var_qty_dipesan INTEGER;
var_qty_tersedia_count INTEGER;
var_qty_dipesan_count INTEGER;
var_total_detail INTEGER;

BEGIN
var_reseptur_id := new.reseptur_id;
var_status_reseptur := new.status_reseptur;
var_ruangan_id := NEW.ruangan_id;
IF (var_reseptur_id  IS NOT NULL)
    THEN                                
            IF (var_status_reseptur = 432)
                THEN                                        
--                      UPDATE stokobatalkes_r
--                      SET qty_tersedia = (qty_tersedia + resepturdetail.total_qty), qty_dipesan = (qty_dipesan - resepturdetail.total_qty)
--                      FROM (
--                              SELECT reseptur_id as resid,obatalkes_id, SUM(qty_reseptur) as total_qty from resepturdetail_t where reseptur_id = var_reseptur_id and is_deleted = false GROUP BY reseptur_id, obatalkes_id
--                      ) as resepturdetail
--                      WHERE resepturdetail.resid = var_reseptur_id AND (stokobatalkes_r.obatalkes_id, stokobatalkes_r.ruangan_id) = (resepturdetail.obatalkes_id, var_ruangan_id);
            END IF;
            -- Sudah Di Proses
            IF (var_status_reseptur = 347)
                THEN                                        
                        UPDATE stokobatalkes_r
                        SET qty_dipesan = (qty_dipesan - resepturdetail.total_qty  ), qty_tersedia = (qty_sisa - (qty_dipesan- resepturdetail.total_qty ))
                        FROM (
                                SELECT reseptur_id as resid,obatalkes_id, SUM(qty_konversi) as total_qty from resepturdetail_t where reseptur_id = var_reseptur_id and is_deleted = false GROUP BY reseptur_id, obatalkes_id
                        ) as resepturdetail
                        WHERE resepturdetail.resid = var_reseptur_id AND (stokobatalkes_r.obatalkes_id, stokobatalkes_r.ruangan_id) = (resepturdetail.obatalkes_id, var_ruangan_id);
            END IF;
END IF;

RETURN NEW;
END;
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

    $this->execute('ALTER FUNCTION public.update_stokobatalkes_r_batalreseptur()
  OWNER TO postgres;');

    $this->execute('CREATE TRIGGER update_reseptur
  AFTER UPDATE
  ON public.reseptur_t
  FOR EACH ROW
  EXECUTE PROCEDURE public.update_stokobatalkes_r_batalreseptur();');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191001_031657_trigger_reseptur_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191001_031657_trigger_reseptur_t cannot be reverted.\n";

        return false;
    }
    */
}

<?php

/**
 * @Author: Sigit
 * @Date:   2018-06-11 11:38:37
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2018-07-10 17:53:09
 */

namespace app\modules\ranap\components\helpers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

// use yii\base\DynamicModel;
// use app\modules\ranap\models\AsesmenAwalForm;
// use app\modules\ranap\models\AsesmenAwalDynamic;
use app\modules\ranap\components\AsesmenDynamicModel;
use kartik\widgets\ActiveForm;
use app\modules\ranap\components\AsesmenWizardWidget;
use app\modules\ranap\components\AsesmenHtml;
use app\modules\ranap\components\widget\SkoringWidget;
use app\modules\ranap\components\widget\RadioTextWidget;
use app\modules\ranap\components\widget\CheckboxTextWidget;
use app\modules\ranap\components\widget\TextWithPlusWidget;

class DynamicFormHelpers
{
	/** Private variables */
	public $_restRanap;
	public $_dynmodels;
	protected static $dyn_model;
	protected static $test_model;
	protected static $temp_data;
	protected static $temp_data_init;
	protected static $temp_tree;
	protected static $datawithkey;
	protected static $html_form = '';
	protected static $datainpanel;
	protected static $_model;
	protected static $_form;

	/**
	 * Constructor
	 */
	public function __construct() {
		// Set values
		$this->_restRanap = Yii::$app->docoRest->ranap;
		// $this->_dynmodels = new DynamicModel;
		self::$dyn_model = new AsesmenDynamicModel;
		// self::$test_model = new AsesmenAwalDynamic;
		// self::$dyn_model->formName() = 'asd';
	}

	/**
	 * Fungsi untuk mengenerate form dinamis
	 *
	 * @param string $instalasi
	 * @param string $menu_header
	 * @param string $tabulasi
	 * @param string $wizard
	 */
	public static function generateForm($id,$data,$form,$model,$datainit) 
	{
		// Try catch
		try {
			// Membuat object
			$module = new DynamicFormHelpers;
			self::$temp_data = $data;
			self::$_form = $form;
			self::$_model = $model;
			self::$temp_data_init = $datainit;
			$newArray = $listlevel2 = $listlevel2 = array();
			foreach ($data as $key => $value) {
				$newArray[$value['asesmen_id']] = $value;
				if($value['level'] == 2){
					$listlevel2[$value['asesmen_id']] = $value;
				}elseif ($value['level'] == 1) {
					$listlevel1[$value['asesmen_id']] = $value;
					$keylevel1 = $value['asesmen_id'];
					$valuelevel1 = $value;
				}
			}
			self::$datawithkey = $newArray;
			self::$datainpanel = array_diff_key(self::$datawithkey,$listlevel1);
			// $html = '';
			$dynamicname = $id;
			echo "<div id='$dynamicname' class='panel panel-default $dynamicname'>";
			echo '<div class="panel-heading">';
			echo '<h5 class="panel-title">'.self::changeString($valuelevel1['asesmen_nama']).'</h5>';
			echo '</div>';

			echo '<div class="panel-body">';

			$tree = self::buildTree($keylevel1,self::$datainpanel,'parent_id','asesmen_id');
			self::$temp_tree = $tree;
			array_walk_recursive($tree,'self::print_input');
			echo '</div>';
			echo "</div>"."</div>";
		} catch (\Exception $e) {
			var_dump($e);exit;
			// Throw new exception
			throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
		} catch (RequestException $e) {
			// Throw new exception
			throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
		}
	}

	private static function print_input($item,$key){
		if($key =='asesmen_id'){
			if(isset(self::$datawithkey[$item])){
				echo self::generateComponent($item);
			}
		}
	}

	private static function generateComponent($key)
	{
		$module = new DynamicFormHelpers;
		$componentdata = self::$datawithkey[$key];
		$componentdataparent = isset(self::$datawithkey[$componentdata['parent_id']]) ? self::$datawithkey[$componentdata['parent_id']] : null;
		$form = self::$_form;
		$model = self::$dyn_model;
		$unparsedListOpsi = json_decode($componentdata['opsi'], true);
		$listOpsi = [];
		$fieldname = $componentdata['asesmen_nama'];
		// $fieldname_clean = self::clean_input_name($componentdata['asesmen_nama']);
		$fieldname_clean = 'field_'.$componentdata['asesmen_id'];
		$model->defineAttribute($fieldname_clean);
		if(isset(self::$temp_data_init[$fieldname_clean])){
			$model->{$fieldname_clean} = self::$temp_data_init[$fieldname_clean];
		}
		if(is_array($unparsedListOpsi)){
			foreach ($unparsedListOpsi as $opsi) {
				$listOpsi[$opsi['id']] = $opsi['text'];
			}
		}
		$kondisi = 'null';
		if($componentdata['kondisi']){
			$okondisi = json_decode($componentdata['kondisi'],true);
			$kondisi = isset($okondisi[0]['value']) ? $okondisi[0]['value'] : 'null';
		}
		$addedclass = 'nohide';
		if($componentdataparent!= null){
			if($componentdataparent['jenis_input'] != null) {
				if(!isset($model->{$fieldname_clean})){
					$addedclass = 'hidden-level';
				}else{
					$addedclass = '';
				}
			}
		}
		switch ($componentdata['jenis_input']) {
			case 'radio':
				echo $form->field($model, $fieldname_clean,['options' => ['class' => 'form-group  dynamiclevel-'.$componentdata['level'].' '.$addedclass]])
                    ->radioList(
                        $listOpsi,
                        [
                        	'data-id' => $componentdata['asesmen_id'],
                        	'data-parent' =>$componentdata['parent_id'],
                        	'data-dependent' => $kondisi,
							'inline' => true,
							'class'  => 'bs-radio dynamicfield-radio dynamicfieldclass'
						]
                    )->label(self::changeString($componentdata['asesmen_nama']));
				break;
			case 'dropdown':
				echo $form->field($model, $fieldname_clean,['options' => ['class' => 'form-group dynamiclevel-'.$componentdata['level'].' '.$addedclass]])
                    ->dropDownList(
                        $listOpsi,
                        [
                        	'data-id' => $componentdata['asesmen_id'],
                        	'data-parent' =>$componentdata['parent_id'],
                        	'data-dependent' => $kondisi,
							'class'  => 'dynamicfield-dropdown dynamicfieldclass select2'
                        ]
                    )->label(self::changeString($componentdata['asesmen_nama']));
				break;
			case 'radiowithscore':
				$model->defineAttribute($fieldname_clean.'_textfield');
				if(isset(self::$temp_data_init[$fieldname_clean.'_textfield'])){
					$model->{$fieldname_clean.'_textfield'} = self::$temp_data_init[$fieldname_clean.'_textfield'];
				}
				echo $form->field($model, $fieldname_clean,['options' => ['class' => 'form-group dynamiclevel-'.$componentdata['level'].' '.$addedclass]])
				->widget(RadioTextWidget::className(), [
						'textfieldAttribute' => $fieldname_clean.'_textfield',
						'textfieldDepends' => false,
						'textfieldOptions' => [],
						'data' => $listOpsi,
						'widgetOptions' => [
							'textLabel' => self::changeString($componentdata['asesmen_nama']),
							'class'=>'dynamicfield-radiowithscore dynamicfieldclass',
                        	'data-id' => $componentdata['asesmen_id'],
                        	'data-parent' =>$componentdata['parent_id'],
                        	'data-dependent' => $kondisi,
						]
					    // configure additional widget properties here
					])->label(self::changeString($componentdata['asesmen_nama']));
				break;
			case 'summary':
				echo $form->field($model, $fieldname_clean,['options' => ['class' => 'form-group dynamiclevel-'.$componentdata['level'].' '.$addedclass]])->textInput([
                        	'data-id' => $componentdata['asesmen_id'],
                        	'data-parent' =>$componentdata['parent_id'],
                        	'data-dependent' => $kondisi,
							'class'  => 'dynamicfield-summary dynamicfieldclass'
                        ])->label(self::changeString($componentdata['asesmen_nama']));
				break;
			default:
				if($componentdata['jenis_input'] == null){
					echo '<h5>'.self::changeString($componentdata['asesmen_nama']).'</h5>';
				}
				break;
		}
	}

	private static function clean_input_name($string) 
	{
	   $string = str_replace('-', '_', $string); // Replaces all spaces with hyphens.
	   $string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens.
	   $string = preg_replace('/[^A-Za-z0-9\-]/', '#sym#', $string); // Removes special chars.
	   $string = str_replace('#sym#', '_', $string); // Replaces .

	   return preg_replace('/_+/', '_', $string); // Replaces multiple hyphens with single one.
	}

	/**
	 * Fungsi untuk get child
	 *
	 * @param string $str
	 */
	public function getChild($parent_id) {
		// Membuat object
		$module = new DynamicFormHelpers;
		// Get reqeust
		$request = $module->_restRanap->get('asesmen/get-asesmen-child?parent_id='.$parent_id);
		$response = json_decode($request->getBody(), true);
		$data = $response["response"];

		return $data;
	}

	

	public static function buildTree($prt,$flat, $pidKey, $idKey = null)
	{
	    $grouped = array();
	    foreach ($flat as $sub){
	    	if($sub[$pidKey] == null){
	    		$sub[$pidKey] = 0;
	    	}
	        $grouped[$sub[$pidKey]][] = $sub;
	    }

	    $fnBuilder = function($siblings) use (&$fnBuilder, $grouped, $idKey) {
	        foreach ($siblings as $k => $sibling) {
	            $id = $sibling[$idKey];
	            if(isset($grouped[$id])) {
	                $sibling['children'] = $fnBuilder($grouped[$id]);
	            }
	            $siblings[$k] = $sibling;
	        }

	        return $siblings;
	    };

	    $tree = $fnBuilder($grouped[$prt]);

	    return $tree;
	}

	/**
	 * Fungsi untuk merubah string dengan underscore menjadi spasi
	 *
	 * @param string $str
	 */
	public function changeString($str) {
		// Replace
		return ucwords(str_replace("_", " ", $str));
	}

	/**
	 * Fungsi rekursive
	 *
	 * @param array $data
	 */
	public function createForm($data) {
		// Deklarasi html
		$html = '';

		// Cek array
		if ($data) {
			// Looping level 1
			foreach ($data as $key => $value) {
				// Cek level
				if ($value['level'] == 1) {
					// Membuat panel
					$html .= '<div class="panel panel-default">';
					$html .= '<div class="panel-heading">';
					$html .= '<h5 class="panel-title">'.self::changeString($value['asesmen_nama']).'</h5>';
					$html .= '</div>';
				}

				// Cek asesmen id
				if ($value['asesmen_id'] != '') {
					// Get child
					$data = self::getChild($value['asesmen_id']);

					// Cek data
					if (!empty($data)) {
						// Looping level 2
						foreach ($data as $key => $value) {
							// Cek level
							if ($value['level'] == 2) {
								// Membuat label
								$html .= '<label>'.self::changeString($value['asesmen_nama']).'</label>';
								$html .= '<div class="panel-body">';

								// Cek asesmen id
								if ($value['asesmen_id'] != '') {
									// Get child
									$data = self::getChild($value['asesmen_id']);

									// Cek data
									if (!empty($data)) {
										// Looping level 3
										foreach ($data as $key => $value) {
											// Generate input
											$html .= self::generateInput($value);

											// Cek asesmen id
											if ($value['asesmen_id'] != '') {
												// Get child
												$data = self::getChild($value['asesmen_id']);

												// Cek data
												if (!empty($data)) {
													// Looping level 4
													foreach ($data as $key => $value) {
														// Generate input
														$html .= self::generateInput($value);

														// Cek asesmen id
														if ($value['asesmen_id'] != '') {
															// Get child
															$data = self::getChild($value['asesmen_id']);

															// Cek data
															if (!empty($data)) {
																// Looping level 5
																foreach ($data as $key => $value) {
																	// Generate input
																	$html .= self::generateInput($value);

																	// Cek asesmen id
																	if ($value['asesmen_id'] != '') {
																		// Get child
																		$data = self::getChild($value['asesmen_id']);

																		// Cek data
																		if (!empty($data)) {
																			// Looping level 6
																			foreach ($data as $key => $value) {
																				// Generate input
																				$html .= self::generateInput($value);
																			}
																		}
																	}
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}

						// Set end tag level 2
						$html .= '</div>';
					}
				}

				// Set end tag level 1
				$html .= '</div>';
			}

			// Return
			return $html;
		}
	}

	/**
	 * Fungsi untuk mengenerate input
	 *
	 * @param array $data
	 */
	public function generateInput($data) {
		// Deklarasi html
		$html = '';

		// Deklarasi variabel
		$options = [];
		$name = $data['asesmen_nama'];
		$text = self::changeString($data['asesmen_nama']);

		// Generate json
		$tempOptions = json_decode($data['opsi'], true);

		// Cek type
		if ($data['jenis_input'] == 'radio' || $data['jenis_input'] == 'radiowithscore') {
			// Cek show label
			if ($data['show_label'] != '') {
				// Set html
				$html .= '<div class="form-group"><label class="control-label">'.$text.'</label><input type="hidden" name="'.$name.'" value><div id="'.$name.'">';
			}
			else {
				// Set html
				$html .= '<div class="form-group"><input type="hidden" name="'.$name.'" value><div id="'.$name.'">';
			}

			// Cek options
			if (is_array($tempOptions) && !empty($tempOptions)) {
				// Looping options
				foreach ($tempOptions as $value) {
					// Set html
					$html .= '<label class="radio-inline"><input type="radio" name="'.$name.'" value="'.$value['id'].'"> '.$value['text'].'</label>';
				}
			}

			// End tag
			$html .= '</div></div>';
		}
		elseif ($data['jenis_input'] == 'dropdown') {
			// Set html
			$html .= '<div class="form-group"><label class="control-label">'.$text.'</label>';
			$html .= Html::dropDownList($name, '', ArrayHelper::map($tempOptions, 'id', 'text'), ['class' => 'form-control select2']);
			
			// End tag
			$html .= '</div>';
		}

		// Return
		return $html;
	}
}
?>
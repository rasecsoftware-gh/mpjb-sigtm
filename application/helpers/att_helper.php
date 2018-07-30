<?php
	if (!defined('BASEPATH')) exit('No direct script access allowed');

	if (!function_exists('att_dbconnect')) {
	    //formateamos la fecha y la hora, función de cesarcancino.com
		function att_dbconnect()
		{
			$dbName = 'C:\Program Files (x86)\Att$\att2000.mdb';
			$conn = new COM("ADODB.Connection");
			$conn->Open("Provider=Microsoft.Jet.OLEDB.4.0; Data Source={$dbName}");
			return $conn;
		}
	}

	if (!function_exists('att_row')) {
		function att_row($fields) {
			$row = array();
			foreach ($fields as $f) {
				$name = strtolower($f->name);
				try {

					if (gettype($f->value) == 'string') {
						$row[$name] = trim(utf8_encode($f->value));
					} elseif(is_null($f->value)) {
						$row[$name] = NULL;
					} elseif(is_bool($f->value)) {
						$row[$name] = $f->value;
					} else {
						//var 
						if ( array_search(gettype($f->value), array('boolean','integer','double','string')) !== false) {
							$row[$name] = $f->value;
						} else {
							switch (variant_get_type($f->value)) {
								case VT_DECIMAL: case VT_R4: case VT_R8:
									$row[$name] = floatval($f->value);
									break;
								case VT_I1: case VT_I2: case VT_I4: case VT_INT: 
								case VT_UI1: case VT_UI2: case VT_UI4: case VT_UINT:
									$row[$name] = intval($f->value);
									break;
								case VT_BOOL:
									$row[$name] = (boolean) $f->value;
									break;
								case VT_NULL:
									$row[$name] = NULL;
									break;
								default:
									$row[$name] = (string) $f->value;
							}
						}
					}
				} catch (Exception $e) {
					$row[$name] = '<!--ERROR-->';
					//echo "error: ".$e;
				}
			}
			return $row;
		}
	}
	/**
	 * RecordSet to Array
	 * @param $rs recordSet
	 */
	if (!function_exists('att_to_array')) {
		function att_to_array($rs) {
			$a = array();
			if (!$rs->BOF)
				$rs->MoveFirst();
			while (!$rs->EOF) {
				$a[] = att_row($rs->Fields);
				$rs->MoveNext();
			}
			return $a;
		}
	}

	if (!function_exists('att_list')) {
		function att_list ($desde, $hasta, $traba_dni='') {		
			$conn = att_dbconnect();
			$userid = -1; // all
			//echo $traba_dni;
			if ($traba_dni != '') {
				$n_dni = ltrim($traba_dni, '0');
				$rs = $conn->Execute("
				SELECT 
					*
				FROM userinfo 
				WHERE 
					badgenumber = '{$traba_dni}' OR badgenumber = '{$n_dni}';
				");

				$rows = att_to_array($rs);
				//var_dump($rows);
				if (count($rows) > 0) {
					$userid = $rows[0]['userid'];
				} else {
					$userid = 0; // no existe
				}
			}
			//echo $userid;
			//echo $desde.'.'.$hasta;

			$sql = "
			SELECT 
				m.*,
				u.name AS user_name,
				u.badgenumber AS user_dni
			FROM checkinout AS m
			INNER JOIN userinfo AS u ON u.userid = m.userid
			WHERE 
				m.userid = {$userid}
				AND m.checktime BETWEEN #{$desde}# AND #{$hasta}#
			ORDER BY m.checktime;
			";
			//echo $sql;
			$rs = $conn->Execute($sql);

			$rows = att_to_array($rs);
			return $rows;
		}
	}

	if (!function_exists('att_det_oc_by_meta_list')) {
		function att_det_oc_by_meta_list ($ctarea, $filter) {		
			$conn = att_dbconnect();
			$db_path = att_DB_PATH;
			$anio = substr($ctarea, 0, 4);
			$bs_version = (intval($anio)>=2014)?'2014':'2009';
			
			$rs = $conn->Execute("
			SELECT 
				d.cano AS oc_anio,
				d.cordcom AS oc_numero,
				d.cbienes AS bs_cod,
				d.ncantid AS cantidad,
				d.npreuni AS precio,
				d.ntotal AS total,
				d.obs AS oc_det_obs,
				b.ddescri AS bs_desc, 
				b.dunimed AS bs_unimed,
				o.ffecha AS oc_fecha
			FROM {$db_path}\\dataa\\pbiecom.dbf AS d
			JOIN {$db_path}\\dataa\\mordcom.dbf AS o ON o.cano = d.cano AND o.cordcom = d.cordcom
			JOIN {$db_path}\\data\\mbienes.dbf b ON b.version_cb='{$bs_version}' AND b.cgrubie+b.csubbie+b.cbienes=d.cbienes
			WHERE 
				d.ctareas LIKE '$ctarea' 
				AND b.ddescri LIKE '%{$filter}%'
				AND NOT o.lanulad
			ORDER BY d.cano, d.cordcom, b.ddescri, d.obs;
			");

			$rows = att_to_array($rs);
			return $rows;
		}
	}

	if (!function_exists('att_det_oc_row')) {
		function att_det_oc_row ($oc_anio, $oc_numero, $bs_cod, $obs) {
			$conn = att_dbconnect();
			$db_path = att_DB_PATH;
			
			$bs_version = ($oc_anio>='2014')?'2014':'2009';

			//$obs = pg_escape_string(utf8_decode(trim($obs)));
			$obs = pg_escape_string((trim($obs)));
			$sql = "
			SELECT 
				d.cano AS orden_anio,
				d.cordcom AS orden_numero,
				d.cbienes AS bs_cod,
				d.ncantid AS cantidad,
				d.npreuni AS precio,
				d.ntotal AS total,
				d.obs AS obs,
				b.ddescri AS bs_desc, 
				b.dunimed AS bs_unimed
			FROM {$db_path}\\dataa\\pbiecom.dbf AS d
			JOIN {$db_path}\\data\\mbienes.dbf b ON b.version_cb='{$bs_version}' AND b.cgrubie+b.csubbie+b.cbienes=d.cbienes
			WHERE 
				d.cano = '$oc_anio' 
				AND d.cordcom = '$oc_numero'
				AND d.cbienes = '$bs_cod'
				
			"; // usa LIKE pote el operador = funciona como like '%obs%' o.O
			// AND ALLTRIM(d.obs) LIKE '{$obs}'
			//echo $sql;
			$rs = $conn->Execute($sql);

			$rows = att_to_array($rs);
			// la observacion se compara en PHP por motivos de codificacion
			if (count($rows)>0) {
				foreach ($rows as $r) {
					if (trim($r['obs']) == $obs) {
						return $r;
					}
				}
				return null;
			}
			return null;
		}
	}
?>
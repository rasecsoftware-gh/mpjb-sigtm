<?php
	if (!defined('BASEPATH')) exit('No direct script access allowed');

	define('SIAF_DB_PATH', 'D:\\siaf_gl$\\siaf_presupuesto\\data');

	if (!function_exists('siaf_dbconnect')) {
	    //formateamos la fecha y la hora, función de cesarcancino.com
		function siaf_dbconnect()
		{
			$conn = new COM("ADODB.Connection");
			$conn->Open('Provider=vfpoledb.1;Data Source='.SIAF_DB_PATH.'\\siaf.dbc;Collating Sequence=Machine');
			$conn->Execute("SET EXCLUSIVE OFF;");
			return $conn;
		}
	}

	if (!function_exists('siaf_row')) {
		function siaf_row($fields) {
			$row = array();
			foreach ($fields as $f) {
				try {
					if (gettype($f->value) == 'string') {
						$row[$f->name] = trim(utf8_encode($f->value));
					} elseif(is_null($f->value)) {
						$row[$f->name] = NULL;
					} elseif(is_bool($f->value)) {
						$row[$f->name] = $f->value;
					} else {
						switch (variant_get_type($f->value)) {
							case VT_DECIMAL: case VT_R4: case VT_R8:
								$row[$f->name] = floatval($f->value);
								break;
							case VT_I1: case VT_I2: case VT_I4: case VT_INT: 
							case VT_UI1: case VT_UI2: case VT_UI4: case VT_UINT:
								$row[$f->name] = intval($f->value);
								break;
							case VT_BOOL:
								$row[$f->name] = (boolean) $f->value;
								break;
							case VT_NULL:
								$row[$f->name] = NULL;
								break;
							default:
								$row[$f->name] = (string) $f->value;
						}
					}
				} catch (Exception $e) {
					$row[$f->name] = '<!--ERROR-->';
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
	if (!function_exists('siaf_to_array')) {
		function siaf_to_array($rs) {
			$a = array();
			if (!$rs->BOF)
				$rs->MoveFirst();
			while (!$rs->EOF) {
				$a[] = siaf_row($rs->Fields);
				$rs->MoveNext();
			}
			return $a;
		}
	}

	if (!function_exists('siaf_expediente_secuencia_girado_cheque_list')) {
		function siaf_expediente_secuencia_girado_cheque_list ($ano_eje, $filter) {		
			$conn = siaf_dbconnect();
			$db_path = SIAF_DB_PATH;

			$sql = "
			SELECT 
				es.ano_eje+es.sec_ejec+es.expediente+es.ciclo+es.fase+es.secuencia+es.correlativo AS expediente_secuencia_id,
				es.*,
				ef.ruc,
				p.nombre AS proveedor
			FROM {$db_path}\\expediente_secuencia.dbf AS es
			LEFT JOIN {$db_path}\\expediente_fase.dbf AS ef ON ef.ano_eje+ef.sec_ejec+ef.expediente+ef.ciclo+ef.fase+ef.secuencia = es.ano_eje+es.sec_ejec+es.expediente+es.ciclo+es.fase+es.secuencia
			LEFT JOIN {$db_path}\\persona.dbf AS p ON p.ruc = ef.ruc
			WHERE 
				es.ano_eje = '$ano_eje' 
				AND es.ciclo = 'G'
				AND es.fase = 'G'
				AND es.cod_doc = '009'
				AND es.cod_doc_b = '065'
				AND es.expediente LIKE '%{$filter}'
			ORDER BY es.secuencia, es.correlativo
			";
			//echo $sql;
			$rs = $conn->Execute($sql);

			$rows = siaf_to_array($rs);
			return $rows;
		}
	}

	if (!function_exists('siaf_expediente_secuencia_row')) {
		function siaf_expediente_secuencia_row($expediente_secuencia_id) {		
			$conn = siaf_dbconnect();
			$db_path = SIAF_DB_PATH;

			$sql = "
			SELECT 
				es.ano_eje+es.sec_ejec+es.expediente+es.ciclo+es.fase+es.secuencia+es.correlativo AS expediente_secuencia_id,
				es.*,
				ef.ruc,
				p.nombre AS proveedor
			FROM {$db_path}\\expediente_secuencia.dbf AS es
			LEFT JOIN {$db_path}\\expediente_fase.dbf AS ef ON ef.ano_eje+ef.sec_ejec+ef.expediente+ef.ciclo+ef.fase+ef.secuencia = es.ano_eje+es.sec_ejec+es.expediente+es.ciclo+es.fase+es.secuencia
			LEFT JOIN {$db_path}\\persona.dbf AS p ON p.ruc = ef.ruc
			WHERE 
				es.ano_eje+es.sec_ejec+es.expediente+es.ciclo+es.fase+es.secuencia+es.correlativo = '$expediente_secuencia_id'
			";
			//echo $sql;
			$rs = $conn->Execute($sql);

			$rows = siaf_to_array($rs);
			if (count($rows)>0) {
				return $rows[0];
			}
			return null;
		}
	}

	if (!function_exists('siaf_expediente_documento_cheque_list')) {
		function siaf_expediente_documento_cheque_list ($ano_eje, $filter) {		
			$conn = siaf_dbconnect();
			$db_path = SIAF_DB_PATH;

			$sql = "
			SELECT 
				ed.ano_eje+ed.sec_ejec+ed.expediente+ed.ciclo+ed.fase+ed.secuencia+ed.correlativo+ed.cod_doc+ed.num_doc AS expediente_documento_id,
				ed.*,
				es.num_doc AS num_doc_cp,
				ef.ruc,
				p.nombre AS proveedor
			FROM {$db_path}\\expediente_documento.dbf AS ed
			JOIN {$db_path}\\expediente_secuencia.dbf AS es 
				ON es.ano_eje+es.sec_ejec+es.expediente+es.ciclo+es.fase+es.secuencia+es.correlativo = ed.ano_eje+ed.sec_ejec+ed.expediente+ed.ciclo+ed.fase+ed.secuencia+ed.correlativo
			JOIN {$db_path}\\expediente_fase.dbf AS ef 
				ON ef.ano_eje+ef.sec_ejec+ef.expediente+ef.ciclo+ef.fase+ef.secuencia = es.ano_eje+es.sec_ejec+es.expediente+es.ciclo+es.fase+es.secuencia
			JOIN {$db_path}\\persona.dbf AS p ON p.ruc = ef.ruc
			WHERE 
				ed.ano_eje = '$ano_eje' 
				AND ed.ciclo = 'G'
				AND ed.fase = 'G'
				AND es.cod_doc = '009'
				AND ed.cod_doc = '065'
				AND ed.expediente LIKE '%{$filter}'
			ORDER BY ed.secuencia, ed.correlativo, ed.num_doc
			";
			//echo $sql;
			$rs = $conn->Execute($sql);

			$rows = siaf_to_array($rs);
			return $rows;
		}
	}

	if (!function_exists('siaf_expediente_documento_row')) {
		function siaf_expediente_documento_row ($expediente_documento_id) {		
			$conn = siaf_dbconnect();
			$db_path = SIAF_DB_PATH;

			$sql = "
			SELECT 
				ed.ano_eje+ed.sec_ejec+ed.expediente+ed.ciclo+ed.fase+ed.secuencia+ed.correlativo+ed.cod_doc+ed.num_doc AS expediente_documento_id,
				ed.*,
				es.num_doc AS num_doc_cp,
				ef.ruc,
				p.nombre AS proveedor
			FROM {$db_path}\\expediente_documento.dbf AS ed
			JOIN {$db_path}\\expediente_secuencia.dbf AS es 
				ON es.ano_eje+es.sec_ejec+es.expediente+es.ciclo+es.fase+es.secuencia+es.correlativo = ed.ano_eje+ed.sec_ejec+ed.expediente+ed.ciclo+ed.fase+ed.secuencia+ed.correlativo
			JOIN {$db_path}\\expediente_fase.dbf AS ef 
				ON ef.ano_eje+ef.sec_ejec+ef.expediente+ef.ciclo+ef.fase+ef.secuencia = es.ano_eje+es.sec_ejec+es.expediente+es.ciclo+es.fase+es.secuencia
			JOIN {$db_path}\\persona.dbf AS p ON p.ruc = ef.ruc
			WHERE 
				ed.ano_eje+ed.sec_ejec+ed.expediente+ed.ciclo+ed.fase+ed.secuencia+ed.correlativo+ed.cod_doc+ed.num_doc = '{$expediente_documento_id}'
			";
			//echo $sql;
			$rs = $conn->Execute($sql);

			$rows = siaf_to_array($rs);
			if (count($rows)>0) {
				return $rows[0];
			}
			return null;
		}
	}

?>
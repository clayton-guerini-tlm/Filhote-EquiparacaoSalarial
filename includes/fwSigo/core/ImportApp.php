<?php
class ImportApp {

	static $noCache;

	/**
	 * Gera html para importar arquivos js e css
	 * @param array $apps: DataTable, HChart, Select2
	 */
	public static function import(Array $apps, $caminho = '../') {

		self::$noCache = microtime(true);

		$htmlInclude = array();
		$root = $caminho . 'includes/fwSigo/';
		$paths = array();

		foreach($apps as $app) {
			/**
			 * Tabelas
			 */
			if($app == 'DataTable') {
				$paths[] = $root . 'library/datatables/1.10/js/jquery.dataTables.min.js';
				$paths[] = $root . 'library/datatables/1.10/js/dataTables.buttons.min.js';
				$paths[] = $root . 'library/datatables/1.10/js/jszip.js';
				$paths[] = $root . 'library/datatables/1.10/js/pdfmake.js';
				$paths[] = $root . 'library/datatables/1.10/js/buttons.html5.min.js';
				$paths[] = $root . 'library/datatables/1.10/js/vfs_fonts.js';
				$paths[] = $root . 'library/HDataTable.js';
				$paths[] = $root . 'library/datatables/1.10/css/jquery.dataTables.min.css';
				$paths[] = $root . 'library/datatables/1.10/css/buttons.dataTables.min.css';
			}
			/**
			 * Gráfico
			 */
			else if($app == 'HChart') {
				$paths[] = $root . 'library/chartjs/2.7.2/Chart.min.js';
				$paths[] = $root . 'library/HChart.js';
			}
			/**
			 * Select box
			 */
			else if($app == 'Select2') {
				$paths[] = $root . 'library/select2/select2.min.js';
				$paths[] = $root . 'library/select2/select2.min.css';
			}
			/**
			 * Framework SIGO
			 */
			else if($app == 'FwSigo') {
				$paths[] = $root . 'library/jquery-3.3.1.min.js';
				$paths[] = $root . 'library/jquery-confirm.min.js';
				$paths[] = $root . 'library/modal/janela.js';
				$paths[] = $root . 'core/helper/js/HAjax.js';
				$paths[] = $root . 'core/helper/js/HUrl.js';
				$paths[] = $root . 'core/helper/js/HMensagem.js';
				$paths[] = $root . 'core/helper/js/HHtml.js';
				$paths[] = $root . 'core/helper/js/HString.js';
				$paths[] = $root . 'core/helper/js/HCsv.js';
				$paths[] = $root . 'core/helper/js/HFormat.js';
				$paths[] = $root . 'core/helper/js/HNumero.js';
				$paths[] = $root . 'library/mensagem.js';
				$paths[] = $root . 'library/string.js';
				$paths[] = $root . 'library/modal/redmon-jquery-ui-1.12.1.custom/jquery-ui.min.css';
				$paths[] = $root . 'library/modal/redmon-jquery-ui-1.12.1.custom/jquery-ui.min.js';
				$paths[] = $root . 'library/jquery-confirm.min.css';
				$paths[] = $root . 'library/mask/jquery-mask-1.14.16/jquery.mask.js';
			}
			/**
			 * EXCEL
			 */
			else if($app == 'Excel') {
				$paths[] = $root . 'core/helper/js/HExcel.js';
			}
			/**
			 * HELPER
			 */
			else if($app == 'Helper') {
				$paths[] = $root . 'core/helper/js/HExcel.js';
				$paths[] = $root . 'core/helper/js/HAjax.js';
				$paths[] = $root . 'core/helper/js/HUrl.js';
				$paths[] = $root . 'core/helper/js/HMensagem.js';
				$paths[] = $root . 'core/helper/js/HHtml.js';
				$paths[] = $root . 'core/helper/js/HString.js';
				$paths[] = $root . 'core/helper/js/HCsv.js';
				$paths[] = $root . 'core/helper/js/HFormat.js';
				$paths[] = $root . 'core/helper/js/HNumero.js';
			}
			/**
			 * Bootstrap 3.4.1
			 */
			else if(strtolower($app) == 'bootstrap-3.4.1') {
				$paths[] = $root . 'library/bootstrap/bootstrap-3.4.1/js/bootstrap.js';
				$paths[] = $root . 'library/bootstrap/bootstrap-3.4.1/js/bootstrap.min.js';
				$paths[] = $root . 'library/bootstrap/bootstrap-3.4.1/js/npm.js';
				$paths[] = $root . 'library/bootstrap/bootstrap-3.4.1/css/bootstrap.css';
				$paths[] = $root . 'library/bootstrap/bootstrap-3.4.1/css/bootstrap.min.css';
			}
			/**
			 * bootstrap-4.2.1
			 */
			elseif (strtolower($app) == 'bootstrap-4.2.1') {

				$paths[] = $root . 'library/bootstrap/bootstrap-4.2.1/css/bootstrap.min.css';
				$paths[] = $root . 'library/bootstrap/bootstrap-4.2.1/js/popper.min.js';
				$paths[] = $root . 'library/bootstrap/bootstrap-4.2.1/js/bootstrap.min.js';

			}

			/**
			 * Datepicker
			 */
			else if($app == 'Datepicker') {
				$paths[] = $root . 'library/datepicker/bootstrap-datepicker.mim.js';
				$paths[] = $root . 'library/datepicker/bootstrap-datepicker.pt-BR.min.js';
				$paths[] = $root . 'library/datepicker/bootstrap-datepicker.standalone.min.css';
			}
		}

		foreach($paths as $path) {
			$ext = substr($path, -3);
			switch($ext) {
				case '.js':
					$htmlInclude[] = self::importJs($path); break;
				case 'css':
					$htmlInclude[] = self::importCss($path); break;
				default:
					$htmlInclude[] = "<!-- Importação da extensão '{$ext}' não implementada.-->"; break;
			}

		}

		return implode(PHP_EOL, $htmlInclude);
	}

	public static function importJs($path) {
		return  "<script type='text/javascript' src='{$path}' ></script>";
	}

	public static function importCss($path) {
		return  "<link type='text/css' rel='stylesheet' href='{$path}'>";
	}

}
?>

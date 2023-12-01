<?php
class HAnexo {

	public static function validarAnexo($anexo, $extensoes = array(), $tamanhoMaximo = 10) {

		/**
		 * Verificar erro no upload
		 */
		if($anexo['name'] != "" && $anexo['error'] != 0) {

			switch($arquivo['error']) {

				case 1:
					return Controller::response(false, 'O TAMANHO DO ARQUIVO É MAIOR QUE O PERMITIDO.', 'v'); break;

				case 2:
					return Controller::response(false, 'O TAMANHO DO ARQUIVO É MAIOR QUE O PERMITIDO.', 'v'); break;

				case 3:
					return Controller::response(false, 'O ARQUIVO FOI ENVIADO PARCIALMENTE AO SERVIDOR. TENTE NOVAMENTE.', 'v'); break;

				case 4:
					return Controller::response(false, 'O ARQUIVO NÃO FOI RECEBIDO PELO SERVIDOR. TENTE NOVAMENTE.', 'v'); break;

				default:
					return Controller::response(false, 'ERRO NÃO IDENTIFICADO. TENTE NOVAMENTE.', 'v'); break;

			}

		}

		/**
		 * Verificar existência de arquivo
		 */
		if($anexo['name'] == "") {
			return Controller::response(false, 'Não há arquivo.', 'v');
		}

		/**
		 * Verificar extensão do arquivo
		 */
		$extensaoArquivo = strtolower(end(@explode('.', $anexo['name'])));

		$extensoesBloqueadas = array('php', 'js');

		if((!empty($extensoes) && !in_array($extensaoArquivo, $extensoes)) ||
		   (empty($extensoes) && in_array($extensaoArquivo, $extensoesBloqueadas))
		  ){
			return Controller::response(false, 'Extensão inválida.', 'v');
		}

		/**
		 * Verificar tamanho do arquivo
		 */
		$tamanhoPermitido = $tamanhoMaximo * 1024 * 1024;

		if($anexo['size'] > $tamanhoPermitido) {
			return Controller::response(false, 'O arquivo excedeu o tamanho permitido.', 'v');
		}

		return Controller::response(true);

	}

	public static function salvarAnexo($anexo, $caminho) {

		/**
		 * Enviar arquivo para o destino no servidor
		 */
		$caminhoArquivo = $caminho . uniqid(time()).'.'.$extensaoArquivo;
		$destinoArquivo = '../includes/'. $caminhoArquivo;

		if(!move_uploaded_file($anexo['tmp_name'], $destinoArquivo)) {
			return Controller::response(false, 'Não foi possível salvar o arquivo', 'v');
		}

		return Controller::response(true, null, $caminhoArquivo);

	}

	public static function deletarAnexo($caminho) {

		$caminho = '../includes/' . $caminho;

		chmod($caminho, 0666);
		unlink($caminho);

	}


}
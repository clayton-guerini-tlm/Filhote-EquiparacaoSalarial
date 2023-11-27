<?php

/**
 *
 */
final class HPadrao
{

	public static function removeCaracteresEspCNPJ($cnpj){

		return str_replace('-','',str_replace('/','',str_replace('.', '', $cnpj)));

	}


	public static function geraToken($usuario){

		$dataHora = date('d/m/Y H:i:s');
		return md5($dataHora. $usuario);

	}


	public static function selectBox($rs, $selecionado = null, $padrao = 'Selecione') {
		$option = !!$padrao ? "<option value=''>{$padrao}</option>" : "";
		foreach($rs as $id => $texto) {
			$selected = $selecionado == $id ? 'selected' : '';
			$option .= "<option value='{$id}' {$selected}>{$texto}</option>";
		}
		return $option;
	}

	public static function formatarAnexo($file){

		$anexo = array();

        if(!empty($file)){
            for ($i=0; $i < count($file['name']); $i++) {
                if(!empty($file['name'][$i])){
                	$anexo[$i]['name'] 		= $file['name'][$i];
                	$anexo[$i]['type'] 		= $file['type'][$i];
                	$anexo[$i]['tmp_name'] 	= $file['tmp_name'][$i];
                	$anexo[$i]['error'] 	= $file['error'][$i];
                	$anexo[$i]['size'] 		= $file['size'][$i];
                }
            }
        }
        return $anexo;
	}

	public static function formatarArquivoTxt($file, $nameFile, $extencao){

		$arquivo = array();

        $arquivo['name'] = $nameFile;
        $arquivo['type'] = 'txt';
        $arquivo['tmp_name'] = $nameFile;
        $arquivo['error'] = 0;
        $arquivo['size'] = filesize($nameFile);
        
        return $arquivo;
	}

	public static function formataExtensoes($rs){

		$extensoes = array();
		foreach ($rs as $key => $value) {
			if (strpos($value->extensao, '|')) {
				foreach (explode('|', $value->extensao) as $key => $value) {
					array_push($extensoes, '.'.trim($value));
				 }
			}else{
				array_push($extensoes, '.'.trim($value->extensao));
			}
		}

		return $extensoes;


	}

	public static function validarFormatoExtensoes($file, $extensoes){
		$erro = "";

		if(!empty($file)){
        	if (!in_array(strrchr($file['name'], "."), $extensoes)) {
                $erro .= " A extensão do arquivo " . $file['name'] . " não é válida";
                return array('situacao'=> false, 'message'=> $erro);
            }
			return array('situacao'=> true, 'message'=> 'ok');
        }
       	return array('situacao'=> false, 'message'=> 'Arquivo não encontrado!');
	}

	public static function validarFormatoExtensoesMult($file, $extensoes){
		$erro = "";
		if(!empty($file)){

			for ($i=0; $i < count($file['name']); $i++) {
	        	if (!in_array(strrchr($file['name'][$i], "."), $extensoes)) {
	                $erro .= " A extensão do arquivo " . $file['name'][$i] . " não é válida";
	                return array('situacao'=> false, 'message'=> $erro);
	            }
			}
			return array('situacao'=> true, 'message'=> 'ok');
        }
       	return array('situacao'=> false, 'message'=> 'Arquivo não encontrado!');
	}

	public static function removeCaracteresString($str) {

	    $str = preg_replace('/[áàãâä]/ui', 'a', $str);
	    $str = preg_replace('/[éèêë]/ui', 'e', $str);
	    $str = preg_replace('/[íìîï]/ui', 'i', $str);
	    $str = preg_replace('/[óòõôö]/ui', 'o', $str);
	    $str = preg_replace('/[úùûü]/ui', 'u', $str);
	    $str = preg_replace('/[ç]/ui', 'c', $str);
	    // $str = preg_replace('/[,(),;:|!"#$%&/=?~^><ªº-]/', '_', $str);
	    $str = preg_replace('/[^a-z0-9]/i', '_', $str);
	    $str = preg_replace('/_+/', '_', $str);

	    return strtoupper($str);
	}

}
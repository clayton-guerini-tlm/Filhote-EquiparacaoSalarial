<?php

include '/includes/fwSigo/core/biblioteca/phpmailer/PHPMailer.php';

final class Email {

    public static function enviarEmail($nome, $email, $assunto, $texto, $anexo = "", $email_admin = "sigo@telemont.com.br", $nome_admin = "SIGO TELEMONT", $copia_oculta_para = "", $copia_para = "") {


	 	$param = array('nome'				=> $nome,
                       'email'				=> $email,
                       'assunto'			=> $assunto,
                       'texto'				=> $texto,
                       'copia_oculta_para'	=> $copia_oculta_para,
                       'anexo' 				=> $anexo,
                       'email_admin' 		=> $email_admin,
                       'nome_admin' 		=> $nome_admin,
                       'copia_para' 		=> $copia_para
        );

	 	$ws52   = new ConexaoSigoWs('ws52');
    	$rsEmail= $ws52->invoke('geral/enviarEmail/',$param);

    	return $rsEmail;


	    $mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

	    $mail->IsSMTP(); // telling the class to use SMTP

	    try {
	        $mail->SMTPDebug = 0;              // enables SMTP debug information (for testing)
	        $mail->SMTPAuth = true;            // enable SMTP authentication
	        $mail->Host = "smtp.office365.com";     // sets the SMTP server
	        $mail->Port = 587;                 // set the SMTP port for the GMAIL server
	        $mail->Username = "telemont.05@telemont.com.br"; // SMTP account username
	        $mail->Password = "tmt@dmin2021"; // SMTP account password
	        $mail->SMTPSecure = 'tls';
	        $mail->AddReplyTo($email_admin, $nome_admin);

	        $nomes = explode(",", $nome);
	        $emails = explode(",", $email);

	        if(!empty($email)) {

		        if (sizeof($emails) > 0) {
		            foreach ($emails as $key => $em) {
		                if (sizeof($nomes) > 0) {
		                    $mail->AddAddress($em, $nomes[$key]);
		                } else {
		                    $mail->AddAddress($em, $nome);
		                }
		            }
		        } else {
		            $mail->AddAddress($email, $nome);
		        }

		    }

	        //Permite múltiplos destinatários como cópia (CC)
	        if ($copia_para != null) {
	            $emails_cc = explode(",", $copia_para);

	            if (sizeof($emails_cc) > 0) {
	                foreach ($emails_cc as $cc) {
	                    $mail->AddCC($cc);
	                }
	            }
	        }

	        //Faz o mesmo para cópia oculta (BCC)
	        if ($copia_oculta_para != null) {
	            $emails_bcc = explode(",", $copia_oculta_para);

	            if (sizeof($emails_bcc) > 0) {
	                foreach ($emails_bcc as $bcc) {
	                    $mail->AddBCC($bcc);
	                }
	            }
	        }

	        if (is_string($anexo) && !empty($anexo)) {
	            $mail->AddAttachment($anexo);
	        } else if (is_array($anexo) && !empty($anexo)) {
	            foreach ($anexo AS $key => $arquivo) {
	                $mail->AddAttachment($arquivo);
	            }
	        }

	        //O EXCHANGE NÃO PERMITE QUE O USUARIO SEJA DIFERENTE DO SetFrom
	        $mail->SetFrom($mail->Username, $mail->Username);
	        $mail->Subject = utf8_decode($assunto);
	        //$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
	        $mail->MsgHTML(utf8_decode($texto));
	        $mail->Send();
	        return true;
	    } catch (phpmailerException $e) {
	        $e->errorMessage(); //Pretty error messages from PHPMailer
	        return false;
	    } catch (Exception $e) {
	        $e->getMessage(); //Boring error messages from anything else!
	        return false;
	    }
	}
}
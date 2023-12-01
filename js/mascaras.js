/**
 *	ARQUIVO DESTINADO À CRIAÇÃO DE MÁSCARAS DE CAMPOS DE FORMULÁRIOS
 */
$(function () {

    inicializaMascaras();
});

function inicializaMascaras() {
    $('.cnpj').mask("99.999.999/9999-99");
    $('.cep').mask("99999-999");
    $('.data').mask('99/99/9999');
    $('.chapa').mask('999999');
    $('.telefone').mask('(99) 9999-9999');
    $('.monetario').maskMoney({symbol: "", decimal: ",", thousands: "."});
    $('.numero').mask('99999999999999999999');

    $(".dri_primario").datepicker({
        buttonImage: "./imagens/calendario.png",
        buttonImageOnly: true,
        dateFormat: "dd/mm/yy",
        monthNames: [
            "Janeiro", "Fevereiro", "Março",
            "Abril", "Maio", "Junho",
            "Julho", "Agosto", "Setembro",
            "Outubro", "Novembro", "Dezembro"
        ],
        dayNamesShort: [
            "Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"
        ],
        dayNames: [
            "Domingo", "Segunda-feira", "Terça-feira", "Quarta-feira", "Quinta-feira", "Sexta-feira", "Sábado"
        ],
        dayNamesMin: [
            "Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"
        ],
        numberOfMonths: 2,
        minDate: "+0D",
        onClose: function (selectedDate) {
            if (selectedDate != "") {
                $(".drf_primario").datepicker("option", "minDate", selectedDate);
                $(".dri_secundario, .drf_secundario").datepicker("option", "minDate", selectedDate);
            }
        }
    });

    $(".drf_primario").datepicker({
        buttonImage: "./imagens/calendario.png",
        buttonImageOnly: true,
        dateFormat: "dd/mm/yy",
        monthNames: [
            "Janeiro", "Fevereiro", "Março",
            "Abril", "Maio", "Junho",
            "Julho", "Agosto", "Setembro",
            "Outubro", "Novembro", "Dezembro"
        ],
        dayNamesShort: [
            "Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"
        ],
        dayNames: [
            "Domingo", "Segunda-feira", "Terça-feira", "Quarta-feira", "Quinta-feira", "Sexta-feira", "Sábado"
        ],
        dayNamesMin: [
            "Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"
        ],
        numberOfMonths: 2,
        minDate: "+0D",
        onClose: function (selectedDate) {
            if (selectedDate != "") {
                $(".dri_primario").datepicker("option", "maxDate", selectedDate);
                $(".dri_secundario, .drf_secundario").datepicker("option", "maxDate", selectedDate);
            }
        }
    });

    $(".dri_secundario").datepicker({
        buttonImage: "./imagens/calendario.png",
        buttonImageOnly: true,
        dateFormat: "dd/mm/yy",
        monthNames: [
            "Janeiro", "Fevereiro", "Março",
            "Abril", "Maio", "Junho",
            "Julho", "Agosto", "Setembro",
            "Outubro", "Novembro", "Dezembro"
        ],
        dayNamesShort: [
            "Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"
        ],
        dayNames: [
            "Domingo", "Segunda-feira", "Terça-feira", "Quarta-feira", "Quinta-feira", "Sexta-feira", "Sábado"
        ],
        dayNamesMin: [
            "Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"
        ],
        numberOfMonths: 2,
        onClose: function (selectedDate) {
            if (selectedDate != "") {
                $(".drf_secundario").datepicker("option", "minDate", selectedDate);
                $(".dri_terciario, .drf_terciario").datepicker("option", "minDate", selectedDate);
            }
        }
    });

    $(".drf_secundario").datepicker({
        buttonImage: "./imagens/calendario.png",
        buttonImageOnly: true,
        dateFormat: "dd/mm/yy",
        monthNames: [
            "Janeiro", "Fevereiro", "Março",
            "Abril", "Maio", "Junho",
            "Julho", "Agosto", "Setembro",
            "Outubro", "Novembro", "Dezembro"
        ],
        dayNamesShort: [
            "Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"
        ],
        dayNames: [
            "Domingo", "Segunda-feira", "Terça-feira", "Quarta-feira", "Quinta-feira", "Sexta-feira", "Sábado"
        ],
        dayNamesMin: [
            "Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"
        ],
        numberOfMonths: 2,
        onClose: function (selectedDate) {
            if (selectedDate != "") {
                $(".dri_secundario").datepicker("option", "maxDate", selectedDate);
                $(".dri_terciario, .drf_terciario").datepicker("option", "maxDate", selectedDate);
            }
        }
    });

    $(".dri_terciario").datepicker({
        buttonImage: "./imagens/calendario.png",
        buttonImageOnly: true,
        dateFormat: "dd/mm/yy",
        monthNames: [
            "Janeiro", "Fevereiro", "Março",
            "Abril", "Maio", "Junho",
            "Julho", "Agosto", "Setembro",
            "Outubro", "Novembro", "Dezembro"
        ],
        dayNamesShort: [
            "Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"
        ],
        dayNames: [
            "Domingo", "Segunda-feira", "Terça-feira", "Quarta-feira", "Quinta-feira", "Sexta-feira", "Sábado"
        ],
        dayNamesMin: [
            "Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"
        ],
        numberOfMonths: 2,
        onClose: function (selectedDate) {
            if (selectedDate != "") {
                $(".drf_terciario").datepicker("option", "minDate", selectedDate);
            }
        }
    });

    $(".drf_terciario").datepicker({
        buttonImage: "./imagens/calendario.png",
        buttonImageOnly: true,
        dateFormat: "dd/mm/yy",
        monthNames: [
            "Janeiro", "Fevereiro", "Março",
            "Abril", "Maio", "Junho",
            "Julho", "Agosto", "Setembro",
            "Outubro", "Novembro", "Dezembro"
        ],
        dayNamesShort: [
            "Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"
        ],
        dayNames: [
            "Domingo", "Segunda-feira", "Terça-feira", "Quarta-feira", "Quinta-feira", "Sexta-feira", "Sábado"
        ],
        dayNamesMin: [
            "Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"
        ],
        numberOfMonths: 2,
//		onClose: function( selectedDate ) {
//			if( selectedDate != "" ){
//				$( ".dri_terciario" ).datepicker( "option", "maxDate", selectedDate );
//			}
//		}
    });
}

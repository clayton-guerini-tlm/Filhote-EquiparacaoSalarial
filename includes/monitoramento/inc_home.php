
<!DOCTYPE html >
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="chrome=1">

    <title>Monitoramento das Rotinas do Sigo Integrado</title>
    
    <meta name="keywords" content="rgraph html5 canvas example bar charts" />
    <meta name="description" content="An example of the type of Bar chart that RGraph can produce" />
    <meta name="googlebot" content="NOODP">

    
    <meta property="og:title" content="RGraph: HTML5 Javascript charts library" />
    <meta property="og:description" content="A javascript charts library based on the HTML5 canvas tag" />
    <meta property="og:image" content="http://www.rgraph.net/images/logo.png"/>

    <link rel="stylesheet" href="css/website.css" type="text/css" media="screen" />
    
    <!-- Place this tag in your head or just before your close body tag -->
    <!--<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>-->

    <script src="js/jquery-1.7.1.min.js"></script>
    <script src="libraries/RGraph.common.core.js"></script>
    <script src="libraries/RGraph.common.tooltips.js"></script>
    <script src="libraries/RGraph.common.key.js" ></script>
    <script src="libraries/RGraph.common.dynamic.js"></script>
    <script src="libraries/RGraph.common.effects.js"></script>
	<script src="libraries/RGraph.hbar.js"></script>
    <!--[if lt IE 9]><script src="../excanvas/excanvas.original.js"></script><![endif]-->
    <script>
	
        
		var qtd =  <?php echo json_encode($qtd);?>;
                var labels = <?php echo json_encode($labels)?>;
                var tamanho_grafico = 30;
                
                
		function myClick (e, bar)
		{
			var obj = bar[0];
			var x   = bar[1];
			var y   = bar[2];
			var w   = bar[3];
			var h   = bar[4];
			var idx = bar[5];
			var qtd_acesso_rotina_selecionada = qtd[idx] * 10;
			
			alert('Quantidade de acessos: ' + qtd_acesso_rotina_selecionada);
		}
		function carregaGrafico ()
		{      
                        
			var hbar1 = new RGraph.HBar('hbar1', qtd);
                        RGraph.Reset(hbar1.canvas);
                        var grad = hbar1.context.createLinearGradient(512,0,900, 0);
			grad.addColorStop(0, 'white');
			grad.addColorStop(1, 'blue');

			hbar1.Set('chart.strokestyle', 'rgba(0,0,0,0)');
			hbar1.Set('chart.gutter.left', 512);
			hbar1.Set('chart.gutter.right', 1);
			hbar1.Set('chart.background.grid.autofit', true);
			hbar1.Set('chart.title', 'Monitor Sigo Integrado');
			hbar1.Set('chart.labels', labels );// ['<?php //echo implode("','",$labels)?>']
			hbar1.Set('chart.shadow', true);
			hbar1.Set('chart.shadow.color','gray');
			hbar1.Set('chart.shadow.offsetx', 0);
			hbar1.Set('chart.shadow.offsety', 0);
			hbar1.Set('chart.shadow.blur', 15);
			hbar1.Set('chart.colors', [grad]);
			hbar1.Set('chart.events.click', myClick);
			hbar1.Draw();
	
		}
                function geraMenu(carrega_menu){
                    
                    if(carrega_menu == "rotina"){
                        var aplicacao = $('#aplicacao').find('option').filter(':selected').val();
                        
                    }
                    //var rotina = $('#rotina').find('option').filter(':selected').val();
                    var id_menu = $('#id_menu').find('option').filter(':selected').val();
                    
                    
                   
                   $.post(
                        "http://192.168.5.51/SIGO_INTEGRADO_3/monitoramento/controller.php",
                        {
                            "acao":"geraMenu",
                            "id_menu":id_menu,
                            "aplicacao":aplicacao,
                            "rotina": rotina
                        },function(r){
                            if(r){
                                id_menu = JSON.parse(r);
                                var options = "<option value=\"\">"+carrega_menu+"</option>";
                                for(var option in id_menu){
                                    options += "<option value="+id_menu[option]+">"+id_menu[option]+"</option>";
                                }
                                $("#"+carrega_menu).attr("disabled",false);
                                $("#"+carrega_menu).html(options);
                            }else{
                                alert("Erro na busca do menu - Favor contatar o administrador do sistema")
                            }
                        }
                    );

                }
                function BuscaDadosGrafico(select){
                    var id_menu = $('#id_menu').find('option').filter(':selected').val();
                    var aplicacao = $('#aplicacao').find('option').filter(':selected').val();
                    var rotina = $('#rotina').find('option').filter(':selected').val();
                   $.post(
                        "http://192.168.5.51/SIGO_INTEGRADO_3/monitoramento/controller.php",
                        {
                            "acao":"BuscaDadosGrafico",
                            "id_menu":id_menu,
                            "aplicacao":aplicacao,
                            "rotina": rotina
                        },function(r){
                            var r_json = JSON.parse(r);
                            qtd = r_json.qtd;
                            labels = r_json.aplicacao;
                            //$("#hbar1").animate({ "height": (qtd.length*30) }); 
                            carregaGrafico();
                        }
                    );
                }
                geraMenu("id_menu");
            function IdMenu( nome ) {
               this.nome = nome;
            }
            function aplicacao( nome ) {
               this.nome = nome;
            }
            function rotina( nome ) {
               this.nome = nome;
            }
           

           // window.onload = carregaGrafico;
           
    </script>
    <style>
        #gera-grafico{
            height:50px;
        }
    </style>
    </head>
<body>


</div>

    <h1>Monitor <span>Sigo Integrado</span></h1>

    <script>
        if (RGraph.isOld()) {
            document.write('<div style="background-color: #fee; border: 2px dashed red; padding: 5px"><b>Important</b><br /><br /> Internet Explorer does not natively support the HTML5 canvas tag, so if you want to see the charts, you can either:<ul><li>Install <a href="http://code.google.com/chrome/chromeframe/">Google Chrome Frame</a></li><li>Use ExCanvas. This is provided in the RGraph Archive.</li><li>Use another browser entirely. Your choices are Firefox 3.5+, Chrome 2+, Safari 4+ or Opera 10.5+. </li></ul> <b>Note:</b> Internet Explorer 9 fully supports the canvas tag.</div>');
        }
    </script>
	<div id="menu">
                <form method="POST" action="#">
                    Período inicial:
                    <input value="" type="text" id="txtDtInicialT" onKeyPress="return false;" size="10" maxlength="10" />
                    <input type="button" onClick="displayCalendar(document.getElementById('txtDtInicialT'),'dd/mm/yyyy',this)" class="calendario" style="cursor:pointer;" /><br />
                    Período final:
                    <input value="" type="text" id="txtDtFinalT" onKeyPress="return false;" size="10" maxlength="10" />
                    <br />
                    <strong>Selecione: </strong>
                    <br />
                    <select id="id_menu" name="id_menu" onchange="geraMenu('aplicacao')">
                            <option value="">Identificador do Menu</option>
                    </select>
                    <select id="aplicacao" name="aplicacao" disabled="disabled" onchange="geraMenu('rotina')">
                            <option value="">Aplicacao</option>
                    </select>
                    <select id="rotina" name="rotina" disabled="disabled">
                            <option value="">Rotina</option>
                    </select>
		</form>
                <br />
                <input type="button" name="gera-grafico" id="gera-grafico" value="Gerar Grafico" onclick="BuscaDadosGrafico();" />
	</div>
	<div>
    
        <div style="text-align: center">
            <canvas id="hbar1" width="1024" height="<?php echo 206*30; ?>">[No canvas support]</canvas>
        </div>
    </div>

</body>
</html>
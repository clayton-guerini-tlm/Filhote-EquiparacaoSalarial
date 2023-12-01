<div class="sg-title div-titulo">EQUIPARAÇÃO SALARIAL</div>

<!-- EQUIPARACAO SALARIAL -->
<div id="div-equiparacao-salarial">
	<div class="sg-container-form">
	    <form id="formFiltro" name="formFiltro" method="POST">
			<div class="row">
				<div class="col-sm-3">
					<label for="codColigada">
						Coligada:
					</label>
					<div class='sg-group-form'>
						<select id="codColigada" name="codColigada">
							<option value="2">TELEMONT</option>
							<option value="3">PERSONAL</option>
						</select>
					</div>
				</div>
				<div class="col-sm-3">
					<label for="chapaDiretor">
						Diretoria:
					</label>
					<div class='sg-group-form'>
						<select id="chapaDiretor" name="chapaDiretor"></select>
					</div>
				</div>	
				<div class="col-sm-3">
					<label for="idFilial">
						Filial:
					</label>
					<div class='sg-group-form'>
						<select id="idFilial" name="idFilial"></select>
					</div>
				</div>
				<div class="col-sm-3">
					<label for="chapaLider">
						Gerente:
					</label>
					<div class='sg-group-form'>
						<select id="chapaLider" name="chapaLider"></select>
					</div>
				</div>
			</div>


			<div class="row">
				<div class="col-sm-3">
					<label for="codSecao">
						Seção:
					</label>
					<div class='sg-group-form'>
						<select id="codSecao" name="codSecao" disabled="disabled"></select>
					</div>
				</div>
				<div class="col-sm-3">
					<label for="funcao">
						Função:
					</label>
					<div class='sg-group-form'>
						<input type="text" id="funcao" name="funcao" placeholder="FUNÇÃO"  />
					</div>
				</div>
				<div class="col-sm-3">
					<label for="nomeFuncionario">
						Funcionário:
					</label>
					<div class='sg-group-form'>
						<input type="text" id="nomeFuncionario" name="nomeFuncionario" placeholder="FUNCIONÁRIO" />
					</div>
				</div>
			</div>
	       
	        <div class="div-sg-btn-center">
	            <input type="submit" class="confirmar" value='Buscar'>
	            <input type="button" class="cancelar" value='Cancelar' onclick="window.location.reload();">
	        </div>
	    </form>
	</div>
	<div class="sg-grid">
	    <table id="tblEquiparacao">
	        <thead>
	            <tr>
	                <th width="5%">FILIAL</th>
	                <th width="5%">CHAPA</th>
	                <th width="10%">NOME</th>
	                <th width="10%">FUNÇÃO</th>
	                <th width="10%">GERENTE</th>
	                <th width="10%">SEÇÃO</th>
	                <th width="10%">ADMISSÃO</th>
	                <th width="10%">INICIO FUNÇÃO</th>
	                <th width="10%">SALÁRIO</th>
					<th width="10%">MOTIVO</th>
	                <th width="20%">AÇÕES</th>
	                <th width="20%">HISTORICO</th>
	    
	            </tr>
	        </thead>
	        <tbody>
	        </tbody>
	    </table>
	</div>

	<div id="idModelNaoEquiparacao"></div>

	<!-- Modal -->
	<div class="modal fade" id="justificativaModal" tabindex="-1" role="dialog" aria-labelledby="justificativaModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="justificativaModalLabel">Não Equiparação</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			    <input type="hidden" id="idEquiparacao" name="idEquiparacao"/>
				<input type="hidden" id="chapa" name="chapa"/>
			    <input type="hidden" id="nomeFuncionario" name="nomeFuncionario"/>
				<div>
					<label for="validado">
						Motivo:
					</label>
					<div class='sg-group-form'>
						<select id="validado" name="validado">
							<option value="1">APROVADO PELA DIRETORIA</option>
							<option value="2">EM ANÁLISE PELA DIRETORIA</option>
							<option value="3">DESLIGAMENTO</option>
							<option value="4">ESCALONAMENTO</option>
							<option value="5">PROMOÇÃO</option>
							<option value="8">REFERÊNCIA DE EQUIPARAÇÃO</option>
							<option value="6">REPRESENTANTE SINDICAL</option>
							<option value="7">RESTRIÇÃO MÉDICA</option>
							<?php  $grupos = explode('|', $_SESSION['SIGO']['ACESSO']['ID_GRUPO']);

								if (in_array(1444, $grupos) || in_array(2, $grupos) || in_array(3, $grupos) || in_array(875, $grupos)) {
							?>
								<option value="9">APROVADO</option>
							<?php			
								}
							?>
						</select>
					</div>
				</div>
			
				<textarea id="justificativa" name="justificativa" rows="5" cols="50" maxlength="500" required></textarea>
			
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-primary naoEquiparacao">Corfirmar</button>
			</div>
			</div>
		</div>
	</div>
	<div class="modal fade bd-example-modal-lg" id="motivosModal" tabindex="-1" role="dialog" aria-labelledby="motivosModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="motivosModalLabel">Motivos Equiparação</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">			
			    <input type="hidden" id="chapa" name="chapa"/>
			
				<div id="divMotivo">
				<fieldset class="motivo">
					<div id="divTblMotivo" name="divTblMotivo">
						<table id="tblMotivo" name="tblMotivo" class="sg-grid" style="height: auto; margin-bottom: 30px;">
							<thead align="center">
								<th style="width: 50%;">CHAPA</th>
								<th style="width: 30%;">NOME</th>
								<th style="width: 20%;">JUSTIFICATIVA</th>
								<th style="width: 20%;">MOTIVO</th>
								<th style="width: 20%;">DATA CADASTRO</th>
					
							</thead>
							<tbody align="center" id="tbodyMotivo">
							</tbody>
						</table>
					</div>
				</fieldset>
			
			</div>
		
			</div>
		</div>
	</div>
</div>
</div>
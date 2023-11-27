<div class="sg-title div-titulo">PERMISSÕES EQUIPARAÇÃO SALARIAL</div>

<!-- EQUIPARACAO SALARIAL -->
<div id="div-equiparacao-salarial">
	<div class="sg-container-form">
	    <form id="formFiltro" name="formFiltro" method="POST">
			<div class="row">
				<div class="col-sm-3">
					<label for="idFilial">
						Filial:
					</label>
					<div class='sg-group-form'>
						<select id="idFilial" name="idFilial"></select>
					</div>
				</div>
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
					<label for="chapa">
						Chapa:
					</label>
					<div class='sg-group-form'>
						<input type="text" id="chapaFuncionario" name="chapaFuncionario" placeholder="CHAPA" />
					</div>
				</div>
				<div class="col-sm-3">
					<label for="nomeFuncionario">
						Nome:
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
	    <table id="tblFuncionario">
	        <thead>
	            <tr>
	                <th width="5%">CHAPA</th>
	                <th width="10%">NOME</th>
	                <th width="10%">FILIAL</th>
	                <th width="5%">AÇÕES</th>
	            </tr>
	        </thead>
	        <tbody>
	        </tbody>
	    </table>
	</div>


	<!-- Modal -->
	<div class="modal fade" id="modalPermissoes" role="dialog" tabindex="-1" aria-labelledby="modalPermissoesLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<form id="formPermissoes" name="formPermissoes" method="POST">
					<div class="modal-header">
						<h5 class="modal-title" id="modalPermissoesLabel">Permissões</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="col-sm-12">
							<div class="row">
								<div class="col-sm-6">
									<input type="hidden" id="chapa" name="chapa"/>
							
									<div class="col-sm-12">
										<label for="nome">
											Nome:
										</label>
										<div class='sg-group-form'>
											<input type="text" id="nome" name="nome" placeholder="Nome"  />
										</div>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="sg-grid" style="overflow:auto; max-height: 300px; margin-top: 9%;">
										<table id="tblFiliais">
											<thead>
												<tr>
													<th width="5%">
														<input type="checkbox" name="marcaTodos" id="marcaTodos" class="marcar-todos" value="1">
														<label class="form-check-label" for="marcaTodos"></label>
													</th>
													<th width="10%">Filial</th>
												</tr>
											</thead>
											<tbody id="listaFiliais">
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
						<button type="button" class="btn btn-primary salvarPermissoes" id="salvarPermissoes">Confirmar</button>
					</div>
				</form>
			</div>
		</div>
	</div>

</div>
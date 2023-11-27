<div class="sg-title div-titulo">GRUPO DE MUNICÍPIOS</div>

<!-- GRUPO DE MUNICIPIOS -->
<div id="div-equiparacao-salarial">
	<div class="sg-container-form">
	    <form id="formFiltro" name="formFiltro" method="POST">
			<div class="row">
				<div class="col-sm-4">
					<label for="grupo">
						Grupo:
					</label>
						<div class='sg-group-form'>
							<input type="text" id="grupo" name="grupo" placeholder="GRUPO"  />
						</div>
				</div>
									
				<div class="col-sm-4">
					<label for="uf">
						UF:
					</label>
					<div class='sg-group-form'>
						<select id="uf" name="uf" placeholder="UF" placeholder="SELECIONE"></select>
					</div>
				</div>


				<div class="col-sm-3">
					<div style="margin-top: 9%;">
						<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalGrupo">
							Criar novo Grupo
						</button>
						<!--<span class="material-icons modal-solicitacao" data-toggle="modal" data-target="#modalGrupo" title="Novo Grupo" style="cursor: pointer;">add_box</span>-->
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
	    <table id="tblGrupo">
	        <thead>
	            <tr>
	                <th width="30%">GRUPO</th>
	                <th width="10%">UF</th>
	                <th width="50%">CIDADES</th>
					<th width="5%"></th>
	                <th width="5%"></th>
	            </tr>
	        </thead>
	        <tbody>
	        </tbody>
	    </table>
	</div>

	<div id="idModelNaoEquiparacao"></div>

	<!-- Modal -->
	<div class="modal fade" id="modalGrupo" role="dialog" tabindex="-1" aria-labelledby="modalGrupoLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<form id="formGrupo" name="formGrupo" method="POST">
					<div class="modal-header">
						<h5 class="modal-title" id="modalGrupoLabel">Grupo de Municipios</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="col-sm-12">
							<div class="row">
								<div class="col-sm-6">
									<input type="hidden" id="id" name="id"/>
							
									<div class="col-sm-12">
										<label for="nome">
											Grupo:
										</label>
										<div class='sg-group-form'>
											<input type="text" id="nome" name="nome" placeholder="GRUPO"  />
										</div>
									</div>
								
									<div class="col-sm-12">
										<label for="uf_id">
											UF:
										</label>
										<div class='sg-group-form'>
											<select id="uf_id" name="uf_id" placeholder="UF"></select>
										</div>
									</div>
								</div>
								<div class="col-sm-6">
									<div class="sg-grid" style="overflow:auto; max-height: 300px; margin-top: 9%;">
										<table id="tblCidade">
											<thead>
												<tr>
													<th width="5%">
														<input type="checkbox" name="marcaTodos" id="marcaTodos" value="1">
														<label class="form-check-label" for="marcaTodos"></label>
													</th>
													<th width="10%">CIDADE</th>
												</tr>
											</thead>
											<tbody id="listaCidades">
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
						<button type="button" class="btn btn-primary salvarGrupo" id="salvarGrupo">Confirmar</button>
					</div>
				</form>
			</div>
		</div>
	</div>

</div>
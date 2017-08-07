<section id="view-students" class="all-views">

	<ol class="breadcrumb">
		<li><a href="#">2017</a></li>
	</ol>
		<div class="flex center-left">
			<button class="ev-openModalAddStudent">Adicionar</button>
		</div>
			<div class="grow flex mb">
					<div class="space-left-sm grow">
							<div class="input-icon2 ml">
									<div class="icon">
										<i class="material-icons">&#xE8B6;</i>
									</div>
									<div class="input">
										<input type="search" name="search" class="form-control width-extra" placeholder="Pesquise pelo nome" />
									</div>
							</div>
						</div>
						<div class="space-left-sm mr">
							<button class="btn--transparent" type="button" data-action='searchProcedure'>Pesquisar</button>
						</div>
			</div>



	<table class="table table-striped">
		<thead class="bg-color-light">
			<tr>
				<th>Nome</th>
				<th>Matricula</th>
				<th>Data de Nascimento</th>
				<th></th>
			</tr>
		</thead>
		<tbody class="listStudents">
		</tbody>
	</table>

	<div class="modal fade" id="modalAddStudents" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="form-student" class="ev-saveStudent">
				<input type="text" name="student_id" hidden />
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="name" class="control-label">Nome do Aluno</label>
						<input class="form-control" name="name" type="text">
					</div>
					<div class="form-group">
						<label for="type" class="control-label">Matricula</label>
						<input class="form-control" name="" type="text">
					</div>
					<div class="form-group">
						<label for="modality" class="control-label">Data de nascimento</label>
						<input class="form-control" name="">
					</div>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-primary">Salvar</button>
				</div>
			</form>
		</div>
	</div>
</div>

</section>

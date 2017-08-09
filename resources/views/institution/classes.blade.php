<section id="view-classes" class="all-views">

	<div class="row">
		<div class="col-xs-6">
		</div>
		<div class="col-xs-6 text-right">
			<select name="course_filter" class="ev-filterByCourse">
			</select>
			<button class="btn btn-primary ev-openModalAddClasse">Adicionar</button>
		</div>
	</div>


	<ul class="list-group mt list-group-classes">
	</ul>
	
	<div class="alert alert-no-results callback-list-group-classes"></div>


	<div class="modal fade" id="modalAddClasse" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="form-period" class="ev-saveClasse">
				<input type="text" name="classe_id" hidden />
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<label class="control-label">Curso</label>
								<select name="course_id" class="form-control ev-getPeriods">
								</select>
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<label class="control-label">Período</label>
								<select name="period_id" class="form-control">
								</select>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label">Nome da turma</label>
						<span class="help-block">Ex: 1ª Série A - Matutino, 1º Ano A, 2017.1, 2017.2, Turma A, Turma B</span>
						<input class="form-control" name="name" type="text">
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

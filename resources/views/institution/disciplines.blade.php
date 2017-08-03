<section id="view-disciplines" class="all-views">

	<ol class="breadcrumb">
		<li>2017</li>
		<li><a href="#courses">Cursos</a></li>
		<li><a href="#courses">Períodos</a></li>
		<li class="active">Disciplinas</li>
	</ol>

	<button class="ev-openModalAddDiscipline">Adicionar</button>

	<div class="row disciplines-list mt">

	</div>

	<div class="modal fade" id="modalAddDiscipline" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="form-discipline" class="ev-saveDiscipline">
				<input type="text" name="discipline_id" hidden />
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body">

					<div class="form-group">
						<label for="name" class="control-label">Nome da disciplina</label>
						<span class="help-block">Ex: Português, Matemática, Física I, Física II, Metodologia Científica, etc.</span>
						<input class="form-control" name="name" type="text">
					</div>

					<div class="row">
						<div class="col-xs-12 col-sm-4">
							<div class="form-group">
								<label for="name" class="control-label">Carga horária</label>
								<input class="form-control" name="timetable" type="number">
							</div>
						</div>
					</div>

					<div class="">
						<div class="form-group">
							<label for="name" class="control-label">Ementa</label>
							<textarea name="syllabus" class="form-control editor" rows="5"></textarea>
						</div>
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

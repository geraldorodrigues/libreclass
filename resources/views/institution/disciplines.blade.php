<section id="view-disciplines" class="all-views">

	<ol class="breadcrumb">
		<li>2017</li>
		<li><a href="#courses">Cursos</a></li>
		<li><a href="#courses">Períodos</a></li>
		<li class="active">Disciplinas</li>
	</ol>

	<button class="ev-openModalAddDiscipline">Adicionar</button>

	<div class="row disciplines-list mt">
		<div class="col-xs-4 mb">
			<div class="card card--shadow item-discipline">
				<div class="card__header">
					<div class="flex">
						<span class="grow text-bold text-md">Português</span>
						<i class="material-icons icon ev-openModalAddDiscipline" edit>&#xE254;</i>
					</div>
				</div>
				<div class="card__body">

				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalAddDiscipline" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="form-discipline">
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
								<input class="form-control" name="timetable" type="text">
							</div>
						</div>
					</div>

					<div class="">
						<div class="form-group">
							<label for="name" class="control-label">Ementa</label>
							<text class="form-control editor" rows="5"></textarea>
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

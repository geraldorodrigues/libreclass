<section id="view-classes" class="all-views">

	<div class="row">
		<div class="col-xs-6">
			<button class="btn btn-primary ev-openModalAddClasse">Adicionar</button>
		</div>
		<div class="col-xs-6 text-right">
			<select>
				<option> Ensino Fundamental </option>
				<option> Curso 2 </option>
			</select>
		</div>
	</div>


	<ul class="list-group mt">
		<li class="mt">
			<div class="text-bold text-md text-upper"> 1ª Série </div>
			<div class="row classes-list mt">
				<div class="col-xs-4 mb">
					<div class="card card--shadow item-classe ev-redirectToOffers">
						<div class="card__header">
							<div class="flex">
								<span class="grow text-bold text-md">1ª Série A</span>
								<i class="material-icons icon ev-openModalAddClasse" edit>&#xE254;</i>
							</div>
						</div>
						<div class="card__body">

						</div>
					</div>
				</div>
			</div>
		</li>

		<li class="mt">
			<div class="text-bold text-md text-upper"> 2ª Série </div>
			<div class="row classes-list mt">
				<div class="col-xs-4 mb">
					<div class="card card--shadow item-classe ev-redirectToOffers">
						<div class="card__header">
							<div class="flex">
								<span class="grow text-bold text-md">2ª Série A - Matutino</span>
								<i class="material-icons icon ev-openModalAddClasse" edit>&#xE254;</i>
							</div>
						</div>
						<div class="card__body">

						</div>
					</div>
				</div>
				<div class="col-xs-4 mb">
					<div class="card card--shadow item-classe ev-redirectToOffers">
						<div class="card__header">
							<div class="flex">
								<span class="grow text-bold text-md">2ª Série B - Vespertino</span>
								<i class="material-icons icon ev-openModalAddClasse" edit>&#xE254;</i>
							</div>
						</div>
						<div class="card__body">

						</div>
					</div>
				</div>
			</div>
		</li>
	</ul>


	<div class="modal fade" id="modalAddClasse" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="form-period">
				<input type="text" name="classe_id" hidden />
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<label for="name" class="control-label">Curso</label>
								<select name="course_id" class="form-control">
									<option value="">Curso 1</option>
									<option value="">Curso 2</option>
								</select>
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<label for="name" class="control-label">Período</label>
								<select name="period_id" class="form-control">
									<option value="">1ª Série</option>
									<option value="">2ª Série</option>
								</select>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label for="name" class="control-label">Nome da turma</label>
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

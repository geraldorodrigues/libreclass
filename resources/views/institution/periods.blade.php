<section id="view-periods" class="all-views">
	<ol class="breadcrumb">
		<li>2017</li>
		<li><a href="#courses">Cursos</a></li>
		<li class="active">Periodos</li>
	</ol>

	<button class="btn btn-primary ev-openModalAddPeriod">Adicionar</button>

	<div class="row periods-list mt">
		<div class="col-xs-4 mb">
			<div class="card card--shadow item-period ev-redirectToDiscipline">
				<div class="card__header">
					<div class="flex">
						<span class="grow text-bold text-md">Módulo I</span>
						<i class="material-icons icon ev-openModalAddPeriod" edit>&#xE254;</i>
					</div>
				</div>
				<div class="card__body">

				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalAddPeriod" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="form-period">
				<input type="text" name="period_id" hidden />
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body">

					<div class="form-group">
						<label for="name" class="control-label">Nome do período</label>
						<span class="help-block">Ex: 1ª Série, 2ª Série, Módulo I, Módulo II, Nível básico, Nível intermediário, etc.</span>
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

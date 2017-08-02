<section id="view-courses" class="all-views">

	<ol class="breadcrumb">
		<li><a href="#">2017</a></li>
		<li class="active">Cursos</li>
	</ol>

	<button class="ev-openModalAddCourse">Adicionar</button>

	<div class="row courses-list mt">
	</div>

	<div class="modal fade" id="modalAddCourse" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="form-course" class="ev-saveCourse">
				<input type="text" name="course_id" hidden />
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="name" class="control-label">Nome do Curso</label>
						<input class="form-control" name="name" type="text">
					</div>
					<div class="form-group">
						<label for="type" class="control-label">Tipo de Ensino</label>
						<span class="help-block">Ex: (Ensino Superior, Ensino Profissional, Ensino Regular)</span>
						<input class="form-control" name="type" type="text">
					</div>
					<div class="form-group">
						<label for="modality" class="control-label">Modalidade</label>
						<span class="help-block">Ex: (Subsequente, Integrado)</span>
						<input class="form-control" name="modality" type="text">
					</div>

					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<label for="absent" class="control-label">Percentual para reprovação (%)</label>
								<span class="help-block"></span>
								<input class="form-control" name="absent_percent" type="number">
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<label for="average" class="control-label">Nota máxima</label>
								<input class="form-control" name="max_value" type="number">
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<label for="average" class="control-label">Média para aprovação</label>
								<span class="help-block">Valor da média de aprovação do seu curso</span>
								<input class="form-control" name="average" type="number">
							</div>
						</div>
						<div class="col-xs-12 col-sm-6">
							<div class="form-group">
								<label for="averageFinal" class="control-label">Média final</label>
								<span class="help-block">Valor da média da avaliação final</span>
								<input class="form-control" name="final_average" type="number">
							</div>
						</div>
					</div>

					<div class="form-group">
						<label for="curricularProfile" class="control-label">Perfil Curricular</label>
						<span class="help-block">Anexe o arquivo do perfil curricular do curso (PDF).</span>
						<input class="form-control" name="curricular_profile" type="file">
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

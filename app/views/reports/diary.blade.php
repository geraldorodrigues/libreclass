<!DOCTYPE html>
<html>

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		<style type="text/css" media="screen">
			.vertical {
				vertical-align: middle !important;
				height: 120px;
			}
			.vertical-align {
				vertical-align: middle !important;
			}
			td {
				white-space: nowrap;
			}
			tr, .panel {
				page-break-inside: avoid;
			}
			.limited-width {
				max-width: 3em;
			}
			.rotate {
			  text-align: center;
				white-space: nowrap;
				vertical-align: middle !important;
				min-width: 1.5em;
			}
			.rotate div {
				-moz-transform: rotate(-90.0deg);  /* FF3.5+ */
				-o-transform: rotate(-90.0deg);  /* Opera 10.5 */
				-webkit-transform: rotate(-90.0deg);  /* Saf3.1+, Chrome */
				margin-left: -10em;
				margin-right: -10em;
			}
			.page-break-inside {
				page-break-inside: avoid;
			}
			.page-break {
				page-break-before: always;
			}
			.box {
				padding: 15px;
				border: 1px solid #ddd;
				margin-bottom: 15px
			}

			.bg-muted {
				padding: 5px;
				background: #eee;
			}
		</style>
	</head>

	<body style="width: 100%">

		<header style="margin-bottom: 15px; width: 100%;">
			<table style="width: 100%">
				<tr style="width: 100%">
					<td style="width: 10%; padding-right: 15px;"></td>
					<td style="width: 80%;" class="text-center">
						<div style="width: 100%;">
							<h4>{{ $data['institution']->name }}</h4>
							<h5>{{ $data['institution']->street }}, {{ $data['institution']->local }}</h5>
							{{-- <h5>Código UEE: uee</h5> --}}
						</div>
					</td>
					<td style="width: 10%; padding-left: 15px;"></td>
				</tr>
			</table>
		</header>

		<div>
			<h5 class='text-center breadcrumb'>Informações do curso</h5>
		</div>

		<div>
			<div class='container small'>
				<div class="row">
					<div class="col-xs-4"><p><b>Disciplina:</b> {{ $data['disciplineName'] }}</p></div>
					<div class="col-xs-4"><p><b>Modalidade:</b> {{ $data['course']->modality }}</p></div>
					<div class="col-xs-4"><p><b>Submodalidade:</b> {{ $data['course']->name }}</p></div>
				</div>
				<div class="row">
					<div class="col-xs-4"><p><b>Turma:</b> {{ $data['classe']->name }}</p></div>
					<div class="col-xs-4"><p><b>Série:</b> {{ $data['period']->name }}</p></div>
					<div class="col-xs-4"><p><b>Período Letivo:</b> {{ $data['classe']->class }}</p></div>
				</div>
			</div>
		</div>

		<div>
			<h5 class='text-center breadcrumb'>Diário de Classe</h5>
		</div>

		<div class="small">
			<table class="table table-bordered table-condensed">
				<tr>
					<th class="small text-center vertical"><b>N#</b></th>
					<th class="small text-center vertical"><b>Aluno(a)</b></th>
					@foreach ($data['lessons'] as $lesson)
						<th class="small rotate vertical"><div>{{ $lesson }}</div></th>
					@endforeach
					<th class="small rotate vertical"><div>Faltas</div></th>
				</tr>
				@foreach ($data['students'] as $student)
					<tr>
						<td class="small text-center">{{ $student->number }}</td>
						<td class="small">{{ trim($student->name) }}</td>
						@foreach ($student->absences as $absence)
							<td class="small text-center">{{ $absence }}</td>
						@endforeach
						<td class="small text-center">{{ $student->countAbsences }}</td>
					</tr>
				@endforeach
			</table>
		</div>

		<div>
			<h5 class='page-break text-center breadcrumb'>Notas de aula</h5>
		</div>

		@foreach ($data['lessons_notes'] as $lessons_note)
			<div class="panel panel-default small">
			  <div class="small panel-heading"><b>{{ $lessons_note['description'] }}</b></div>
			  <div class="small panel-body">
			    <p><b>Título:</b> {{ $lessons_note['title'] }}</p>
			    <p><b>Nota de aula:</b> {{ $lessons_note['note'] }}</p>
			  </div>
			</div>
		@endforeach

		<div>
			<h5 class='page-break text-center breadcrumb'>Avaliações</h5>
		</div>

		<div class="small">
			<table class="table table-bordered table-condensed">
				<tr>
					<th class="small text-center vertical-align"><b>N#</b></th>
					<th class="small text-center vertical-align"><b>Aluno(a)</b></th>
					@foreach ($data['exams'] as $exam)
						<th class="small limited-width text-center vertical-align"><div>Avaliação {{ $exam->number }} ({{ $exam->date }})</div></th>
					@endforeach
					<th class="small limited-width text-center vertical-align">Média</th>
					<th class="small limited-width text-center vertical-align">Avaliação de recuperação</th>
				</tr>
				@foreach ($data['students'] as $student)
					<tr>
						<td class="small text-center">{{ $student->number }}</td>
						<td class="small">{{ trim($student->name) }}</td>
						@foreach ($student->exams as $exam)
							<td class="small text-center">{{ $exam }}</td>
						@endforeach
						<td class="small text-center">{{ $student->average }}</td>
						<td class="small text-center">{{ $student->finalAverage }}</td>
					</tr>
				@endforeach
			</table>
		</div>

	</body>

</html>

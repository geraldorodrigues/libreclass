@extends('social.master')

@section('css')
@parent
{{ HTML::style('css/blocks.css') }}
{{ HTML::style('css/forms.css') }}
@stop

@section('js')
@parent
{{ HTML::script('js/blocks.js') }}
{{ HTML::script('js/lessons.js') }}
{{ HTML::script('js/units.js') }}
{{-- HTML::script('http://rubaxa.github.io/Sortable/Sortable.js') --}}
@stop

@section('body')
@parent


<div class="row">
  <div class="col-md-8 col-xs-12 col-sm-12">

    <div class="panel panel-default">
      <div class="panel-body">
        <div class="row">
          <div class="col-sm-10">

            <div class="row">
              <div class="col-md-12">
                  {{-- Form::select("unit", $list_units, $unit_current->value-1, ["class" => "form-control", "disabled" => "disabled"]) --}}
                <h4 class="text-blue"><b>Unidade {{ $unit_current->value }}</b></h4>
              </div>
              <div class="col-md-12">
                <ol class="breadcrumb bg-white">
                  <li>{{ $unit_current->offer->discipline->period->course->institution->name }}</li>
                  <li>{{ $unit_current->offer->discipline->period->course->name }}</li>
                  <li>{{ $unit_current->offer->discipline->period->name }}</li>
                  <li class="active">{{ $unit_current->offer->getClass()->fullName() }}</li>
                </ol>
              </div>
            </div>

          </div>
          <div class="col-sm-2 text-right">
            <a href='{{ URL::to("lectures") }}' class="btn btn-default btn-block">Voltar</a>
          </div>
        </div>
      </div>
    </div>

  <div id="block-title" class="panel panel-default">
    <div class="panel-body">
      <div class="row">
        <div class="col-md-12">
          <h3 class="pull-left text-blue"><b>{{ $unit_current->offer->discipline->name }}</b></h3>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="alert alert-info" id="message" hidden>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-3 col-sm-3">
          <div class="form-group">
            {{ Form::open(["id" => "form-calculation"]) }}
              {{ Form::hidden("unit", Crypt::encrypt($unit_current->id)) }}
              {{ Form::label("calculation", "Cálculo da Média") }}
              {{ Form::select("calculation", ["S" => "Soma", "A" => "Média Aritmética", "W" => "Média Ponderada", "P" => "Parecer Descritivo"], $unit_current->calculation, ["class" => "form-control"]) }}
            {{ Form::close() }}
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="list-inline">

            <a href='{{ URL::to("/avaliable/finalunit/" . Crypt::encrypt($unit_current->id)) . "?edit=e" }}' class="btn btn-default">Recuperação da Unidade</a>
            <a target="_blank" href='{{ URL::to("/lectures/units/report-unit/" . Crypt::encrypt($unit_current->id)) }}' class="btn btn-default"><i class="fa fa-print"></i> Gerar Relatório</a>

          </div>

            <div id="view-users">

            </div>
        </div>
      </div>
    </div>
  </div>

    <div class="row">
      <div class="col-md-6">
        <div id="block-lesson" class="panel panel-default">
          <div class="panel-body">
            <div class="row">
              <div class="col-md-6 col-xs-6">
                <h4 class="">AULAS</h4>
              </div>

              <div class="col-md-6 col-xs-6">
                <a id="new-block" class="btn btn-default pull-right" href='{{"/lessons/new?unit=" . Crypt::encrypt($unit_current->id)}}'><i class="fa fa-plus"></i> Nova aula</a>
              </div>
            </div>
          </div>

        </div>

        <div id="lesson-process" class="text-center text-muted" hidden>
          <i class="fa fa-spin fa-2x fa-spinner"></i>
        </div>

        <ul class="list-unstyled" id="list-lessons"> <!--AULAS-->

          {{-- Item Aula oculta --}}
          <li class="panel panel-default panel-daily data" key="" id="hidden-lesson" hidden>
            <div class="panel-heading">
              <div class="row">
                <div class="col-xs-6 lesson-sort"></div>
                <div class="col-xs-6">
                  <i class="pull-right fa fa-gears icon-default click" data-toggle="dropdown" aria-expanded="false"></i>
                  <ul class="dropdown-menu dropdown-menu-right" role="menu">
                    <li><a class="lesson-edit" href=''><i class="fa fa-edit text-blue"></i> Editar</a></li>
                    <li class="lesson-copy click"><a><i class="fa fa-copy text-blue"></i> Duplicar </a></li>
                    <li class="lesson-copy-with click"><a><i class="fa fa-copy text-blue"></i> Duplicar com Frequência</a></li>
                    <li class="lesson-copy-for click"><a><i class="fa fa-exchange text-blue"></i> Duplicar para...</a></li>
                  </ul>
                  <i class="pull-right fa fa-file-text-o icon-default infolesson click"></i>
                </div>
              </div>
            </div>
            <div class="panel-body">
              <div class="row">
                <div class="col-md-12">
                  <p class="text-info lesson-date"><i class="fa fa-calendar"></i></p>
                  <p  class="text-md text-blue">
                    <a class="lesson-title" href=''></a>
                  </p>
                  <p class="lesson-description"></p>
                </div>
              </div>
            </div>
        </li>


        {{ ""; $i = count($lessons) }}
        @forelse( $lessons as $lesson )
          <li class="panel panel-default panel-daily data" key="{{Crypt::encrypt($lesson->id)}}">


            <div class="panel-heading">
              <div class="row">
                <div class="col-xs-6">
                  Aula {{ $i-- }}
                </div>

                <div class="col-xs-6">
                  <i class="pull-right fa fa-gears icon-default click" data-toggle="dropdown" aria-expanded="false"></i>
                  <ul class="dropdown-menu dropdown-menu-right" role="menu">
                    <li><a href='{{ URL::to("/lessons?l=" . Crypt::encrypt($lesson->id)) }}'><i class="fa fa-edit text-blue"></i> Editar</a></li>
                    <li class="lesson-copy click"><a><i class="fa fa-copy text-blue"></i> Duplicar </a></li>
                    <li class="lesson-copy-with click"><a><i class="fa fa-copy text-blue"></i> Duplicar com Frequência</a></li>
                    <li class="lesson-copy-for click"><a><i class="fa fa-exchange text-blue"></i> Duplicar para...</a></li>
                    <li><a href='{{ URL::to("/lessons/delete/") }}' class="trash click"><i class="fa fa-trash text-danger"></i> Deletar</a></li>
                  </ul>
                  <i class="pull-right fa fa-file-text-o icon-default infolesson click"></i>
                </div>
              </div>
            </div>

            <div class="panel-body">
              <div class="row">
                <div class="col-md-12">
                  <p class="text-info"><i class="fa fa-calendar"></i> {{ date("d/m/Y", strtotime($lesson->date)) }}</p>
                  <p  class="text-md text-blue">
                    <a href='{{ URL::to("/lessons?l=" . Crypt::encrypt($lesson->id)) }}'>{{ $lesson->title }}</a>
                  </p>

                  <p>{{ $lesson->description }}</p>
                </div>

              </div>
            </div>
          </li>

        @empty
        <div class="row">
          <div class="col-md-12">
            <h3 class="text-muted text-center">Você não possui aulas.</h3>
          </div>
        </div>
        @endforelse
        </ul>
      </div>

    <!--Fim coluna de Aulas -->
    <!--Inicío Coluna Avaliações -->

      <div class="col-md-6">
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="row">

              <div class="col-md-6 col-xs-6">
                <h4 class="">AVALIAÇÕES</h4>
              </div>

              <div class="col-md-6 col-xs-6">
                <a id="new-block" href='{{ URL::to("/avaliable/new?u=" . Crypt::encrypt($unit_current->id)) }}' class="btn btn-default pull-right"><i class="fa fa-plus"></i> Nova avaliação</a>
              </div>

            </div>
          </div>
        </div>

        <ul class="list-unstyled">
        @if($recovery)
          <li class="panel panel-default panel-daily">
                <div class="panel-heading">
                  <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-6">
                      Avaliação {{count($exams)+1}}
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6">

                      <i class="pull-right fa fa-gears icon-default click" data-toggle="dropdown" aria-expanded="false"></i>
                      <ul class="dropdown-menu dropdown-menu-right" role="menu">
                        <li><a href='{{ URL::to("/avaliable/finalunit/" . Crypt::encrypt($unit_current->id)) . "?edit=e" }}'><i class="fa fa-edit text-blue"></i> Editar</a></li>

                      </ul>

                      <!--<i class="pull-right fa fa-search icon-default click"></i>-->
                      <!--<a href='{{-- URL::to("/avaliable/finalunit/" . Crypt::encrypt($unit_current->id)) --}}'><i class="pull-right fa fa-file-text-o icon-default click"></i></a>-->
                    </div>
                  </div>
                </div>

                <div class="panel-body">
                  <div class="row">
                    <div class="col-md-12">
                      <p class="text-info"><i class="fa fa-calendar"></i> {{ date("d/m/Y", strtotime($recovery->date)) }}</p>
                      <p class="text-md text-info">
                        <a href='{{ URL::to("/avaliable/finalunit/" . Crypt::encrypt($unit_current->id)) }}'>
                        {{ $recovery->title }}
                        </a>
                      </p>
                      <p>{{ $recovery->comments }}</p>
                    </div>
                  </div>
                </div>
            </li>
        @endif




        {{ ""; $i = count($exams) }}
        @forelse( $exams as $exam )
          <li class="panel panel-default panel-daily data" key="{{Crypt::encrypt($exam->id)}}">
            <div class="panel-heading">
              <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-6">
                  Avaliação {{ $i-- }}
                </div>

                <div class="col-md-6 col-sm-6 col-xs-6">
                  <i class="pull-right fa fa-gears icon-default click" data-toggle="dropdown" aria-expanded="false"></i>
                  <ul class="dropdown-menu dropdown-menu-right" role="menu">
                    <li><a href='{{ URL::to("/avaliable?e=" . Crypt::encrypt($exam->id)) }}'><i class="fa fa-edit text-blue"></i> Editar</a></li>
                    <li><a href="{{ URL::to("/avaliable/delete") }}" class="trash click"><i class="fa fa-trash text-danger"></i> Deletar</a></li>
                  </ul>
                  <!--<i class="pull-right fa fa-search icon-default click"></i>-->
                  <!--<a href='{{-- URL::to("/avaliable/liststudentsexam/" . Crypt::encrypt($exam->id)) --}}'><i class="pull-right fa fa-file-text-o icon-default click"></i></a>-->
                </div>
              </div>
            </div>
            <div class="panel-body">
              <div class="row">
                <div class="col-md-12">
                  <p class="text-info"><i class="fa fa-calendar"></i> {{ date("d/m/Y", strtotime($exam->date)) }}</p>
                  <p class="text-md text-info">
                    <a href='{{ URL::to("/avaliable/liststudentsexam/" . Crypt::encrypt($exam->id)) }}'>
                    {{ $exam->title }}
                    </a>
                  </p>
                  <p>{{ $exam->comments }}</p>
                </div>
              </div>
            </div>
          </li>
        @empty
        <div class="row">
          <div class="col-md-12">
            <h3 class="text-muted text-center">Você não possui avaliações.</h3>
          </div>
        </div>
        @endforelse
        </ul>
      </div>
    </div>
  </div>
</div>

@include('modules.lessonCopyModal')
@include('modules.infolessons')
@stop

  <div class="modal fade" id="modalScholarReport" tabindex="-1" role="Modal Info Invite" aria-labelledby="modalInvite" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h3 class="modal-title"><b><i class="fa fa-file-o fa-fw"></i> Gerar boletim escolar</b></h3>
        </div>

        <div class="modal-body">
          <div class="row">
            <div class="col-xs-12">
              <div>
                <h4>Selecione o período que deseja obter o boletim</h4>
                {{ Form::select("class", $listclasses, null, ["class" => "form-control", "id" => "class-modal-change"]) }}
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary" data-url="{{ URL::to("/user/scholar-report") }}"> Gerar</button>
        </div>
      </div>
    </div>
  </div>

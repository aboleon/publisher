@extends('core::'.config('app.template').'.layouts.panel')

@section('content')
@if (!$data->isEmpty())
<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover dataTable">
        <thead>
            <tr>
                <th>Prénom / Nom</th>
                <th>E-mail</th>
                <th>Numéro de téléphone</th>
                <th>Sujet</th>
                <th>Message</th>
                <th>GDPR</th>
                <th>Créée</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            @foreach($data as $k=>$v)
            <tr>
                <td><?= $v->form_name; ?></td>
                <td><?= $v->form_email; ?></td>
                <td><?= $v->form_phone; ?></td>
                <td><?= $v->form_subject; ?></td>
                <td><?= $v->form_message; ?></td>
                <td><?= !is_null($v->consent) ? date('d/m/Y', strtotime($v->created_at)) : null; ?></td>
                <td><?= date('d/m/Y', strtotime($v->created_at)); ?></td>
                <td style="width:220px;" class="nowrap">
                    <div class="visible-md visible-lg hidden-sm hidden-xs action-buttons">
                        <a title="Supprimer " data-placement="top" href="#"  data-target="#myModal<?php
 echo $v->id;?>" data-bs-toggle="modal" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt bigger-120"></i></a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @foreach($data as $k=>$v)
    <div id="myModal<?= $v->id;?>" class="modal fade" tabindex="-1" >
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{!! url('panel/Publisher/mails/remove/'.$v->id) !!}">
                    {!! csrf_field() !!}
                    <div class="modal-header no-padding">
                        <div class="table-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                <span class="white">&times;</span>
                            </button>
                            Supprimer un enregistrement
                        </div>
                    </div>
                    <div class="modal-body">
                        <p>&Ecirc;tes-vous sur de vouloir supprimer cet enregistrement ?</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn" data-dismiss="modal" aria-hidden="true">Annuler</button>
                        <button type="submit" class="btn btn-primary">Confirmer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>

@else
{!! ResponseRenderers::warning("Il n'y a pas de messages pour le moment") !!}
@endif
@stop

@push('js')
<script src="{!! asset('Publisher/layouts/Ace/assets/js/jquery.dataTables.min.js') !!}"></script>
<script src="{!! asset('Publisher/layouts/Ace/assets/js/jquery.dataTables.bootstrap.min.js') !!}"></script>
<script src="{!! asset('Publisher/Core/js/dataTable.js') !!}"></script>
@endpush

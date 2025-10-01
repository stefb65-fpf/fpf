@extends('layouts.account')
@section('contentaccount')
    <div class="accountContent justify-start align-start mt10">
        Le tableau ci-dessous récapitule vos adhésions. Pour renouveler ces dernières:
        <ul class="ml40">
            <li>si la nature de carte est "Carte club", <b>vous ne pouvez pas ré-adhérer directement</b>. Il faut passer par le contact de votre club en cliquant sur le bouton "contacter mon club"</li>
            <li>si la nature de carte est "Carte individuelle", vous pouvez renouveler votre adhésion en cliquant sur le bouton "Ré-adhérer"</li>
        </ul>
        <div class="mt20">
            Si vous souhaitez souscrire une carte individuelle en sus de votre carte club ou une seconde carte individuelle, merci de <a class="blue" href="mailto:fpf@federation-photo.fr">contacter le secrétariat de la FPF</a>.
        </div>

    </div>

    @if(!$user->cartes)
        <div class="accountContent justify-start align-start mt10">
            Vous n'avez pas encore d'adhésion en cours.
        </div>
    @else
    <table class="styled-table">
        <thead>
            <tr>
                <th>Nature de carte</th>
                <th>Identifiant</th>
                <th>Statut</th>
                <th>Nom de club</th>
                <th>Type de carte</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($user->cartes as $carte)
            <tr>
                <td>{{ $carte->clubs_id ? "Carte club" : "Carte individuelle" }}</td>
                <td>{{ $carte->identifiant }}</td>
                <td>
                    @switch($carte->statut)
                        @case(0) <div class="sticker orange" title="Adhésion non renouvelée, active la saison passée"></div> @break
                        @case(1) <div class="sticker yellow" title="Adhésion en cours de renouvellement"></div> @break
                        @case(2) <div class="sticker green" title="Adhésion renouvelée"></div> @break
                        @case(3) <div class="sticker green" title="Adhésion renouvelée"></div> @break
                        @case(4) <div class="sticker" title="Adhésion inactive depuis au moins 2 ans"></div> @break
                    @endswitch
                </td>
                <td>{{ $carte->clubs_id ? $carte->club->nom : '' }}</td>
                <td>
                    @switch($carte->ct)
                        @case(2) > 25 ans @break
                        @case(3) 18 - 25 ans @break
                        @case(4) < 18 ans @break
                        @case(5) famille @break
                        @case(6) 2nd club @break
                        @case(7) > 25 ans @break
                        @case(8) 18 - 25 ans @break
                        @case(9) < 18 ans @break
                        @case('F') famille @break
                        @default &nbsp; @break
                    @endswitch
                </td>
                <td>
                        @if(!$carte->clubs_id)
                            @if(!in_array($carte->statut, [2,3]))
                                @if(date('Y-m-d') <= $finSaison)
                                    <a name="btnReadhesion" data-url="{{ route("adhesion.renouveler") }}" data-ref="{{ $carte->id }}" class="btnNormal accountColor" data-ref="">Ré-adhérer</a>
                                @else
                                    renouvellement possible à partir du 1er Septembre
                                @endif
                            @endif
                        @else
                            <a name="btnReadhesion" data-url="{{ route('adhesion.contactClub') }}" data-ref="{{ $carte->id }}" class="btnNormal accountColor">Contacter mon club</a>
                        @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
        <div class="d-flex">
            <div class="sticker orange" title="Adhésion non renouvelée, active la saison passée"></div> Adhésion non renouvelée, active la saison passée
            <div class="sticker yellow" title="Adhésion en cours de renouvellement"></div> Adhésion en cours de renouvellement
            <div class="sticker green" title="Adhésion renouvelée"></div> Adhésion renouvelée
            <div class="sticker" title="Adhésion inactive depuis au moins 2 ans"></div> Adhésion inactive depuis au moins 2 ans
        </div>
    @endif
@endsection

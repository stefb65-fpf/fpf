<div class="accountMenu">
    <a href="{{ env('APP_URL') }}mon-profil" class="accountMenuItem{{ Request::is('mon-profil')?" active":""}}">Profil</a>
    <a href="{{ env('APP_URL') }}adhesion" class="accountMenuItem{{ Request::is('adhesion')?" active":""}}">Adhésion</a>
    <a href="{{ env('APP_URL') }}abonnement" class="accountMenuItem{{ Request::is('abonnement')?" active":""}}">Abonnement</a>
    <a href="{{ env('APP_URL') }}mes-mails" class="accountMenuItem{{ Request::is('mes-mails')?" active":""}}">Mails reçus</a>
    <a href="{{ env('APP_URL') }}mes-actions" class="accountMenuItem{{ Request::is('mes-actions')?" active":""}}">Historique</a>
    <a href="{{ env('APP_URL') }}mes-formations" class="accountMenuItem{{ Request::is('mes-formations')?" active":""}}">Mes Formations</a>
    @if($florilege->active)
        <a href="{{ env('APP_URL') }}florilege" class="accountMenuItem{{ Request::is('florilege')?" active":""}}">Florilège</a>
    @endif
    @if($individual_invoices > 0)
        <a href="{{ route('factures') }}" class="accountMenuItem{{ Request::is('factures')?" active":""}}">Factures</a>
    @endif
</div>

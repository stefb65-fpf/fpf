<div class="accountMenu">
    <a href="mon-profil" class="accountMenuItem{{ Request::is('mon-profil')?" active":""}}">Profil</a>
    <a href="mes-mails" class="accountMenuItem{{ Request::is('mes-mails')?" active":""}}">Mails reçus</a>
    <a href="mes-actions" class="accountMenuItem{{ Request::is('mes-actions')?" active":""}}">Historique</a>
    <a href="mes-formations" class="accountMenuItem{{ Request::is('mes-formations')?" active":""}}">Formations</a>
    @if($florilege->active)
        <a href="florilege" class="accountMenuItem{{ Request::is('florilege')?" active":""}}">Florilège</a>
    @endif
    @if($individual_invoices > 0)
        <a href="{{ route('factures') }}" class="accountMenuItem{{ Request::is('factures')?" active":""}}">Factures</a>
    @endif
</div>

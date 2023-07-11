<div class="formBlock">


    <div class="formBlockTitle">Généralités</div>
    <div class="formBlockWrapper">
            @if($action == 'store')
                <form action="{{ route($route.'.store') }}" method="POST" enctype="multipart/form-data">
            @elseif($action == 'update')
            <form action="{{ route($route.'.update', $club) }}" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="_method" value="put">
            @endif
            <div class="formBlockWrapper">
                <div class="formLine center d-flex flex-column">
                    <label class="d-flex flex-column" for="file" style="cursor:pointer">
                        @if(isset($club))
                        <img class="clubLogo"
                             src="{{ env('APP_URL').'storage/app/public/uploads/clubs/'.$club->numero.'/'.$club->logo }}" alt="">
                        @else
                            <img class="clubLogo" src="{{env('APP_URL').'storage/app/public/FPF-default-image.jpg'}}" alt="" >
                        @endif
                        <span class="text underlineGrey grey relative"
                              style="width: 120px;margin: auto;">Changer de logo</span>
                    </label>
                    <input class="formValue d-none" type="file" id="file" accept=".png,.jpeg,.jpg"
                           name="logo"
                           disabled="true">
                </div>
            </div>
            {{--                @endif--}}
            <div class="formBlockWrapper inline">
                <div class="formUnit">
                    <div class="formLabel">Nom</div>
                    <input class="formValue capitalize" type="text" value="{{isset($club) && $club->nom?$club->nom:""}}"
                           disabled="true"
                           name="nom" maxlength="40" minlength="2" type="text"/>
                </div>
                <div class="formUnit">
                    <div class="formLabel">Courriel</div>
                    <input class="formValue" type="email" value="{{isset($club) && $club->courriel?:""}}"
                           disabled="true"
                           name="courriel" maxlength="250" minlength="2" type="email"/>
                </div>
                <div class="formUnit">
                    <div class="formLabel">Site web</div>
                    <input class="formValue" type="text" value="{{isset($club) && $club->web?:""}}"
                           disabled="true" name="web" minlength="4"/>
                </div>
                @if(isset($club))
                <div class="formUnit">
                    <div class="formLabel">Statut</div>
                    @switch($club->statut)
                        @case(0)
                        <div class="d-flex">
                            <div class="sticker orange"></div>
                            <div>Non renouvelé</div>
                        </div>
                        @break
                        @case(1)
                        <div class="d-flex">
                            <div class="sticker yellow"></div>
                            <div>Préinscrit</div>
                        </div>
                        @break
                        @case(2)
                        <div class="d-flex">
                            <div class="sticker green"></div>
                            <div>Validé</div>
                        </div>
                        @break
                        @case(3)
                        <div class="d-flex">
                            <div class="sticker"></div>
                            <div>Désactivé</div>
                        </div>
                        @break
                        @default
                        <div>Non renseigné</div>
                    @endswitch
                </div>
                @endif
                <div class="formUnit mr25">
                    <div class="formLabel">Nombre d'adhérents</div>
                    <div>{{isset($club) && $club->nbadherents?:""}}</div>
                </div>
            </div>
        </form>
        <div class="w100" data-formId="generaliteForm">
            <button class="formBtn mx16 relative d-none success" name="enableBtn">
                Valider
            </button>
            <button class="formBtn mx16 relative  primary" name="updateForm">Modifier</button>
        </div>
    </div>
</div>

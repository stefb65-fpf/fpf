@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Gestion Fédérale - Formations
            <a class="previousPage" title="Retour page précédente" href="{{ route('admin') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="cardContainer">
            <a class="card" href="{{ route('formations.index') }}">
                <div class="wrapper">
                    <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M8.75 17.5C11.5125 17.5 13.75 15.2625 13.75 12.5C13.75 9.7375 11.5125 7.5 8.75 7.5C5.9875 7.5 3.75 9.7375 3.75 12.5C3.75 15.2625 5.9875 17.5 8.75 17.5ZM8.75 8.75C10.8125 8.75 12.5 10.4375 12.5 12.5C12.5 14.5625 10.8125 16.25 8.75 16.25C6.6875 16.25 5 14.5625 5 12.5C5 10.4375 6.6875 8.75 8.75 8.75ZM17.5 23.125V30H16.25V23.125C16.25 22.0875 15.4125 21.25 14.375 21.25H3.125C2.0875 21.25 1.25 22.0875 1.25 23.125V30H0V23.125C0 21.4 1.4 20 3.125 20H14.375C16.1 20 17.5 21.4 17.5 23.125ZM25 8.3875L18.125 15.2625L16.2 13.3375C16.225 13.0625 16.25 12.7875 16.25 12.5C16.25 12.175 16.225 11.8625 16.1875 11.5625L18.125 13.5L24.1125 7.5125H18.75V6.2625H24.375C25.4125 6.2625 26.25 7.1 26.25 8.1375V13.7625H25V8.3875ZM30 3.125V22.5H19.875C19.7875 22.0625 19.65 21.65 19.4625 21.25H28.75V3.125C28.75 2.0875 27.9125 1.25 26.875 1.25H9.375C8.3375 1.25 7.5 2.0875 7.5 3.125V5.1125C7.075 5.1875 6.65 5.3 6.25 5.4375V3.125C6.25 1.4 7.65 0 9.375 0H26.875C28.6 0 30 1.4 30 3.125Z" fill="#2F2F2F"/>
                    </svg>
                    <div class="cardTitle">Liste des formations</div>
                </div>
            </a>
            <a class="card" href="{{ route('formations.parametrage') }}">
                <div class="wrapper">
                    <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.855 8.625L17.08 8.875L14.58 21.375L13.355 21.125L15.855 8.625ZM11.88 11.95L10.9925 11.0625L8.25498 13.8C7.52998 14.525 7.52998 15.7 8.25498 16.425L10.955 19.125L11.8425 18.2375L9.14248 15.5375C8.90498 15.3 8.90498 14.9125 9.14248 14.675L11.88 11.9375V11.95ZM19.4675 11.0125L18.58 11.9L21.305 14.625C21.5425 14.8625 21.5425 15.25 21.305 15.4875L18.58 18.2125L19.4675 19.1L22.1925 16.375C22.9175 15.65 22.9175 14.475 22.1925 13.75L19.4675 11.025V11.0125ZM26.205 17.375L29.955 19.6875L26.0175 26.075L22.2675 23.7625C21.2675 24.575 20.1675 25.1875 18.9675 25.6125V30H11.4675V25.6125C10.28 25.1875 9.16748 24.575 8.16748 23.7625L4.41748 26.075L0.47998 19.6875L4.22998 17.375C4.05498 16.575 3.96748 15.775 3.96748 15C3.96748 14.225 4.05498 13.425 4.22998 12.625L0.47998 10.3125L4.41748 3.925L8.16748 6.2375C9.16748 5.425 10.2675 4.8125 11.4675 4.3875V0H18.9675V4.3875C20.155 4.8125 21.2675 5.425 22.2675 6.2375L26.0175 3.925L29.955 10.3125L26.205 12.625C26.38 13.425 26.4675 14.225 26.4675 15C26.4675 15.775 26.38 16.575 26.205 17.375ZM24.7675 17.9625L24.88 17.5125C25.105 16.6625 25.2175 15.825 25.2175 15C25.2175 14.175 25.105 13.3375 24.88 12.4875L24.7675 12.0375L28.23 9.9L25.605 5.65L22.1425 7.7875L21.7925 7.4875C20.7175 6.55 19.4925 5.8625 18.155 5.45L17.7175 5.3125V1.25H12.7175V5.3L12.28 5.4375C10.9425 5.85 9.71748 6.5375 8.64248 7.475L8.29248 7.775L4.82998 5.6375L2.20498 9.9L5.66748 12.0375L5.55498 12.4875C5.32998 13.3375 5.21748 14.175 5.21748 15C5.21748 15.825 5.32998 16.6625 5.55498 17.5125L5.66748 17.9625L2.20498 20.1L4.82998 24.35L8.29248 22.2125L8.64248 22.5125C9.71748 23.45 10.9425 24.1375 12.28 24.55L12.7175 24.6875V28.7375H17.7175V24.6875L18.155 24.55C19.4925 24.1375 20.7175 23.45 21.7925 22.5125L22.1425 22.2125L25.605 24.35L28.23 20.1L24.7675 17.9625Z" fill="#2F2F2F"/>
                    </svg>
                    <div class="cardTitle">Paramétrage</div>
                </div>
            </a>
            <a class="card" href="{{ route('formateurs.index') }}">
                <div class="wrapper">
                    <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M15 0C6.72875 0 0 6.72875 0 15C0 23.2713 6.72875 30 15 30C23.2713 30 30 23.2713 30 15C30 6.72875 23.2713 0 15 0ZM8.75 27.2463V26.25C8.75 22.8038 11.5538 20 15 20C18.4462 20 21.25 22.8038 21.25 26.25V27.2463C19.3737 28.2075 17.2488 28.75 15 28.75C12.7512 28.75 10.6263 28.2075 8.75 27.2463ZM22.5 26.5188V26.25C22.5 22.1137 19.1363 18.75 15 18.75C10.8637 18.75 7.5 22.1137 7.5 26.25V26.5188C3.74 24.0625 1.25 19.8162 1.25 15C1.25 7.41875 7.41875 1.25 15 1.25C22.5813 1.25 28.75 7.41875 28.75 15C28.75 19.8162 26.26 24.0625 22.5 26.5188ZM15 6.25C12.2425 6.25 10 8.4925 10 11.25C10 14.0075 12.2425 16.25 15 16.25C17.7575 16.25 20 14.0075 20 11.25C20 8.4925 17.7575 6.25 15 6.25ZM15 15C12.9325 15 11.25 13.3175 11.25 11.25C11.25 9.1825 12.9325 7.5 15 7.5C17.0675 7.5 18.75 9.1825 18.75 11.25C18.75 13.3175 17.0675 15 15 15Z"
                            fill="#2F2F2F"/>
                    </svg>
                    <div class="cardTitle">Formateurs</div>
                </div>
            </a>
        </div>
    </div>
@endsection

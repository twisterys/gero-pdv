<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{env('APP_NAME')}} | Page n'existe pas </title>
    @include('layouts.head')
    <style>
        .title {
            font-weight: 900;
            font-size: 4rem;
            margin: 0;
            filter: url(#goo);
            letter-spacing: .1rem;
            position: relative;
            text-transform: uppercase;
        }

        .drop {
            width: .1em;
            height: .1em;
            border-radius: 0 100% 100% 100%;
            background-color: currentColor;
            position: absolute;
            top: 72%;
            animation: drop 5s infinite both;
        }

        .drop:nth-child(1) {
            left: 9%;
        }

        .drop:nth-child(2) {
            left: 33%;
            animation-delay: -.4s;
        }

        .drop:nth-child(3) {
            left: 50.5%;
            animation-delay: -1.5s;
        }

        .drop:nth-child(4) {
            left: 79%;
            animation-delay: -.8s;
        }

        .drop:nth-child(5) {
            left: 93.6%;
            animation-delay: -1.3s;
        }

        @keyframes drop {
            0% {
                transform: translateY(0) scaleX(.85) rotate(45deg);
                animation-timing-function: ease-out;
            }
            60% {
                transform: translateY(120%) scaleX(.85) rotate(45deg);
                animation-timing-function: ease-in;
            }
            80%, 100% {
                transform: translateY(60vh) scaleX(.85) rotate(45deg);
            }
        }
    </style>
</head>
<svg xmlns="http://www.w3.org/2000/svg" version="1.1" style="width: 0;height: 0;">
    <defs>
        <filter id="goo">
            <feGaussianBlur in="SourceGraphic" stdDeviation="4" result="blur"/>
            <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 18 -7" result="goo"/>
            <feBlend in="SourceGraphic" in2="goo"/>
        </filter>
    </defs>
</svg>
<body class="row m-0 p-4 align-items-center justify-content-center vh-100 ">

<div style="width: fit-content;max-width: min(600px , 80vw);padding:1rem 5rem " class="bg-white rounded rounded-3 d-flex flex-column align-items-center overflow-hidden" >
    <img style="max-width: 230px" class="mb-3" src="{{asset('images/error.svg')}}" alt="">
    <h1  class="title text-primary"  >Oups!
        <span class="drop"></span>
        <span class="drop"></span>
        <span class="drop"></span>
        <span class="drop"></span>
        <span class="drop"></span></h1>
    <h3>Erreur 404</h3>
    <h5 class="text-center"> La page que vous demandez n'existe pas ou plus. Retournez à la page d'accueil</h5>
    <a href="{{route('tableau_bord.liste')}}" class="btn btn-soft-primary"> <i class="mdi mdi-calendar me-2"></i>
        Tableau de bord</a>
</div>
</body>
</html>

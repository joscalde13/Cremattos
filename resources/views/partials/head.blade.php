<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

<!-- Favicon personalizado: logo.jpg en todos los formatos -->
<link rel="icon" type="image/jpeg" href="{{ asset('img/logo.jpg') }}" sizes="32x32">
<link rel="icon" type="image/jpeg" href="{{ asset('img/logo.jpg') }}" sizes="48x48">
<link rel="shortcut icon" href="{{ asset('img/logo.jpg') }}">
<link rel="apple-touch-icon" href="{{ asset('img/logo.jpg') }}">
<meta name="msapplication-TileImage" content="{{ asset('img/logo.jpg') }}">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance

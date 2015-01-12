<!DOCTYPE html>
<html class="bg-primary">
<head>
    @yield('css')
    {{ HTML::style('css/bootstrap.css') }}
</head>
<body>

<div class="container-fluid">

    @yield('content')


</div>
@yield('footer')

@yield('js')

</body>

</html>
<!DOCTYPE html>
<html>
<head>
    @yield('css')
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet"/>
</head>
<body>

    <div class="container-fluid">
    <div style="margin-top: 80px"></div>
        <div class="row">
        <div class="col-lg-2"></div>
            <div class="col-lg-8">
                @yield('content')
            </div>
    </div>

</div>
@yield('footer')

@yield('js')

</body>

</html>
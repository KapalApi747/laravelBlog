<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('layouts.partials.head')
<body class="sb-nav-fixed">
@include('layouts.partials.navbar')
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        @include('layouts.partials.sidebar')
    </div>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">@yield('title', 'Dashboard')</h1>
                <ol class="breadcrumb mb-4">
                    @yield('breadcrumb')
                </ol>
                {{--               @yield('cards', View::make('layouts.partials.cards'))--}}
                {{--               @yield('charts', View::make('layouts.partials.charts'))--}}
                @yield('content')
            </div>
        </main>
        @include('layouts.partials.footer')
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="js/scripts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script src="assets/demo/chart-area-demo.js"></script>
<script src="assets/demo/chart-bar-demo.js"></script>
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
<script src="js/datatables-simple-demo.js"></script>

<script>
    //10 seconden de alert laten staan
    window.setTimeout(function(){
        document.querySelector(".alert").style.display='none';
    },10000);
</script>

</body>
</html>

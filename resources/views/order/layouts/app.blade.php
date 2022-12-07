<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Laravel Midtrans</title>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-fQybjgWLrvvRgtW6bFlB7jaZrFsaBXjsOMm/tB9LTS58ONXgqbR9W8oWht/amnpF" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css"
        integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
       <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        html {
            font-size: 14px;
        }
        @media (min-width: 768px) {
            html {
                font-size: 16px;
            }
        }
        .container {
            max-width: 960px;
        }
        .pricing-header {
            max-width: 700px;
        }
        .card-deck .card {
            min-width: 220px;
        }
        .border-top {
            border-top: 1px solid #e5e5e5;
        }
        .border-bottom {
            border-bottom: 1px solid #e5e5e5;
        }
        .box-shadow {
            box-shadow: 0 .25rem .75rem rgba(0, 0, 0, .05);
        }
        .link-active {
            background-color: aliceblue;
            border-color: 5px;
        }
    </style>
</head>

<body>

    <div class="d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom box-shadow">
        <h5 class="my-0 mr-md-auto font-weight-normal">Laravel Midtrans</h5>
        <nav class="my-2 my-md-0 mr-md-3">
            <a class="p-2 text-dark" target="_blank" href="https://simulator.sandbox.midtrans.com/bca/va/index">Midtrans Payment Simulator</a>
        </nav>
    </div>

    @yield('content')

</body>

<script>
    $(() => {
        $('body').on('click', 'a.btn-delete-table', function(e) {
            let that = $(e.currentTarget);
            e.preventDefault()
            Swal.fire({
                title: 'Are you sure you want to delete this data?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: "Yes, I'm sure!"
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: that.attr('href'),
                        type: 'DELETE'
                    })
                    .done(() => {
                        Swal.fire({
                            title: 'Data deleted successfully',
                            icon: 'success'
                        }).then((nextResult) => {
                            window.location.reload(true)
                        })
                    })
                    .fail((err) => {
                        console.log(err)
                        Swal.fire({
                            title: 'An error occured...',
                            icon: 'warning'
                        })
                    })
                }
            })
        })
    })
</script>
</html>

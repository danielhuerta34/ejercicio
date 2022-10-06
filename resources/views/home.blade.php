<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Bitcoin</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/alertify.min.css') }}" />
    <script src="{{ asset('js/jquery-3.6.1.js') }}"></script>
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/alertify.min.js') }}"></script>
</head>

<body>
    <h5 class="alert alert-info text-center">Precio actual de Bitcoin: <br>
        <span></span>
    </h5>

    <div class="card m-auto" style="width: 18rem;">
        <div class="card-body m-auto">
            <p>
                <span class="bitcoin">{{ "$". $btc }} USD</span>
            </p>
        </div>
    </div>
    <div>
        <select hidden name="currency" id="currency">
            <option value="USD">USD</option>
        </select>
    </div>


    <div class="loading"></div>


    <h5 class="alert alert-info text-center mt-5">Historial: <br>
        <span></span>
    </h5>

    <div class="table-responsive mt-4">
        <table class="table table-striped table-bordered" id="myTable">
            <thead>
                <tr>
                    <th scope="col">USD</th>
                    <th scope="col">Variación</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Hora</th>
                </tr>
            </thead>
            <tbody>
                @foreach($history as $data)
                <tr>
                    <td>{{ $data->usd }}</td>
                    <td><?= round($data->variation, 3) ?> %</td>
                    <td><?= date("d/m/Y", strtotime($data->date)); ?></td>
                    <td>{{ $data->hour }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="toastr"></div>

</body>
<script>
    $(document).ready(function() {
        $('#myTable').DataTable({
            "pageLength": 10,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
            }
        });
    });
    setInterval(function() {
        let bitcoin = $('.bitcoin').text();

        $.ajax({
            type: "POST",
            url: "{{ route('obtenerBitcoin') }}",
            data: {
                _token: "{{ csrf_token() }}"
            },
            dataType: "json",
            beforeSend: function() {
                $('.loading').html(`<button class="btn btn-primary mt-3" type="button" disabled>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Cargando...
                </button>`);
            },
            success: function(data) {
                $('.loading').html("");
                $('.bitcoin').html("$" + data['bitcoin'] + " USD");
                $('#myTable').DataTable().destroy();
                $('#myTable').find('tbody').append(`
                    <tr>
                        <td>${data['bitcoin']}</td>
                        <td>${data['variacion']} %</td>
                        <td>${data['fecha']}</td>
                        <td>${data['hora']}</td>
                    </tr>`);
                $('#myTable').DataTable({
                    "pageLength": 10,
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
                    }
                });
                var notification = alertify.notify("Nuevo valor guardado USD " + data['bitcoin'], 'success', 5, function() {});
            }
        });
    }, 10000);
</script>

</html>
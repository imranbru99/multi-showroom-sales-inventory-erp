<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Temp Report</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="{{ asset('/public/admin-elite/assets/node_modules/jquery/jquery-3.2.1.min.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="{{ asset('/public/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <script src="{{ asset('/public/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>


</head>

<body>


    <div class="container">

        <form action="{{ route('dateWiseCollection') }}" method="get">

            <div class="row mt-5">

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="text" value="{{ $start_date }}" class="form-control datepicker" id="start_date"
                            name="start_date" autocomplete="off">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="text" class="form-control datepicker" value="{{ $end_date }}" id="end_date"
                            name="end_date" autocomplete="off">
                    </div>
                </div>

                <div class="col-md-4 text-right">
                    <div class="form-group">
                        <button class="btn btn-primary mt-4" type="submit">Search</button>
                    </div>
                </div>


            </div>

        </form>


        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Collection</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalCollection = 0;
                        @endphp
                        @foreach ($data as $d)
                        @php
                            $totalCollection += $d['collection'];
                        @endphp
                            <tr>
                                <td>{{ $d['date'] }}</td>
                                <td>{{ $d['collection'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tbody>
                        <tfoot>
                            <tr>
                                <td>Total</td>
                                <td>{{ $totalCollection }}</td>
                            </tr>
                        </tfoot>
                    </tbody>
                </table>
            </div>
        </div>

    </div>






    <script>
        $(".datepicker").datepicker({
            format: 'dd-mm-yyyy',
            changeMonth: true,
            changeYear: true,

        });

    </script>
</body>

</html>

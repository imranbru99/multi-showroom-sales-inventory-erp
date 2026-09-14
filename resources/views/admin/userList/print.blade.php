@extends('admin.layouts.masterPrint')

@section('content')
<table id="report-header">
    <tr>
        <td>{{ $title }}</td>
    </tr>
</table>

<div id="pad-bottom"></div>

<table id="report-table">
    <thead>
        <tr>
            <th width="20px">Sl</th>
            <th>Showroom Name</th>
            <th>Name</th>
            <th>Username</th>
        </tr>
    </thead>

    <tbody>
        @foreach($users as $user)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
                <?php
                $branchs = [];
                if ($user->showroomId) {
                    $branchs = explode(',', $user->showroomId);
                }
                $branchs = \App\ShowroomSetup::whereIn('id', $branchs)->get();
                ?>
                @foreach($branchs as $branc)
                {{ $branc->name }} <br>
                @endforeach
            </td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->username }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="row">
    <div class="col-md-12 text-right">
        <?php
        date_default_timezone_set("Asia/Dhaka");
        ?>
        <p>Print Date & Time : <?php echo date("d-m-Y h:i:sa"); ?></p>
    </div>
</div>
@endsection

@extends('admin.layouts.masterPrint')

@section('content')
    <table id="report-header">
        <tr>
        	<td>Chart OF Account List</td>
        </tr>
    </table>

    <div id="pad-bottom"></div>

    <table id="report-table">
    	<thead>
    		<tr>
    			<th>Head Code</th>
    			<th>Head Name</th>
    		</tr>
    	</thead>
    	
        <tbody>
            @foreach ($coaLists as $coaList)
                <tr>
                    <td>{{ $coaList->head_code }}</td>
                    <td>{{ $coaList->head_name }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

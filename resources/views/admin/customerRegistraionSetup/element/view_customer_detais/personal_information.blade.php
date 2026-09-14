<table class="table table-borderless table-sm">
    <thead class="thead-dark">
        <tr>
            <th colspan="6">Personal Information</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <th width="40%">Applicant's Code</th>
            <td width="10%">:</td>
            <td>{{ $customer->code }}</td>
        </tr>
        <tr>
            <th width="40%">Applicant's Name</th>
            <td width="10%">:</td>
            <td>{{ $customer->name }}</td>
        </tr>

        <tr>
            <th width="40%">Nick Name</th>
            <td width="10%">:</td>
            <td>{{ $customer->nick_name }}</td>
        </tr>

        <tr>
            <th width="40%">Age</th>
            <td width="10%">:</td>
            <td>{{ $customer->age }}</td>
        </tr>

        <tr>
            <th width="40%">Phone No</th>
            <td width="10%">:</td>
            <td>{{ $customer->phone_no }}</td>
        </tr>
        
        <tr>
            <th width="40%">National ID</th>
            <td width="10%">:</td>
            <td>{{ $customer->nid }}</td>
        </tr>

        <tr>
            <th width="40%">Marital Status</th>
            <td width="10%">:</td>
            <td>{{ $customer->marital_status }}</td>
        </tr>

        <tr>
            <th width="40%">Spouse Name</th>
            <td width="10%">:</td>
            <td>{{ $customer->spouse_name }}</td>
        </tr>

        <tr>
            <th width="40%">Father's Name</th>
            <td width="10%">:</td>
            <td>{{ $customer->fathers_name }}</td>
        </tr>

        <tr>
            <th width="40%">Mother's Name</th>
            <td width="10%">:</td>
            <td>{{ $customer->mothers_name }}</td>
        </tr>

        <tr>
            <th width="40%">Gender</th>
            <td width="10%">:</td>
            <td>{{ $customer->gender }}</td>
        </tr> 
        <tr>
            <td colspan="3">
                <a href="{{ route('customerRegistraionSetup.editCustomer',$customer->id) }}" class="btn btn-info btn-md" style="width: 100%;text-transform: uppercase;">Edit Information</a>
            </td>
        </tr>
    </tbody>
</table>
<table class="table table-borderless table-sm">
    <thead class="thead-dark">
        <tr>
            <th colspan="6">Professional Information</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <th width="40%">Profession Name</th>
            <td width="10%">:</td>
            <td>{{ $customer->profession_name }}</td>
        </tr>
        <tr>
            <th width="40%">Profession Duration</th>
            <td width="10%">:</td>
            <td>{{ $customer->profession_duration }}</td>
        </tr>

        <tr>
            <th width="40%">Total Earnign Member</th>
            <td width="10%">:</td>
            <td>{{ $customer->total_earning_member }}</td>
        </tr>

        <tr>
            <th width="40%">Designtion</th>
            <td width="10%">:</td>
            <td>{{ $customer->designation }}</td>
        </tr>
        <tr>
            <th width="40%">Monthly Income</th>
            <td width="10%">:</td>
            <td>{{ $customer->monthly_income }}</td>
        </tr>

         <tr>
            <th width="40%">Work Place Address</th>
            <td width="10%">:</td>
            <td>{{ $customer->work_place_address }}</td>
        </tr>
    </tbody>
</table>

<table class="table table-borderless table-sm">
    <thead class="thead-dark">
        <tr>
            <th colspan="6">Other's Information</th>
        </tr>
    </thead>

    <tbody>
         <tr>
            <th width="40%">Current Residence</th>
            <td width="10%">:</td>
            <td>{{ $customer->current_residence }}</td>
        </tr>

        <tr>
            <th width="40%">Residence Duration</th>
            <td width="10%">:</td>
            <td>{{ $customer->residence_duration }}</td>
        </tr>

         <tr>
            <th width="40%">Present Address</th>
            <td width="10%">:</td>
            <td>{{ $customer->present_address }}</td>
        </tr>

        <tr>
            <th width="40%">Permananet Address</th>
            <td width="10%">:</td>
            <td>{{ $customer->permanent_address }}</td>
        </tr>

    </tbody>
</table>
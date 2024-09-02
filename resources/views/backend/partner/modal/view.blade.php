<div class="card">
	<div class="card-body p-2">
	    <table class="table table-bordered">
			<tr><td>{{ _lang('First Name') }}</td><td>{{ $partner->first_name }}</td></tr>
			<tr><td>{{ _lang('Last Name') }}</td><td>{{ $partner->last_name }}</td></tr>
			<tr><td>{{ _lang('Email') }}</td><td>{{ $partner->email }}</td></tr>
			<tr><td>{{ _lang('Phone') }}</td><td>{{ $partner->mobile }}</td></tr>	
			<tr><td>{{ _lang('City') }}</td><td>{{ $partner->city }}</td></tr>
			<tr><td>{{ _lang('State') }}</td><td>{{ $partner->state }}</td></tr>
			<tr><td>{{ _lang('Zip') }}</td><td>{{ $partner->zip_code }}</td></tr>
			<tr><td>{{ _lang('Company') }}</td><td>{{ $partner->company_name }}</td></tr>
	    </table>
	</div>
</div>

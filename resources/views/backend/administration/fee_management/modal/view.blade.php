<div class="card">
	<div class="card-body p-2">
	    <table class="table table-bordered">
			<tr><td>{{ _lang('Fee name') }}</td><td>{{ $fee->name }}</td></tr>
			<tr><td>{{ _lang('Top up amount') }}</td><td>{{ $fee->top_up_amount }}</td></tr>
			<tr><td>{{ _lang('Levels') }}</td><td>{{ $fee->levels }}</td></tr>
			<tr><td>{{ _lang('Partners') }}</td><td>{{ $fee->partners }}</td></tr>	
			<tr><td>{{ _lang('Minimum') }}</td><td>{{ $fee->minimum }}</td></tr>
			<tr><td>{{ _lang('Maximum') }}</td><td>{{ $fee->maximum }}</td></tr>
			<tr><td>{{ _lang('Fixed fee') }}</td><td>{{ $fee->fixed_fee }}</td></tr>
			<tr><td>{{ _lang('Percentage fee') }}</td><td>{{ $fee->percentage_fee }}</td></tr>
			<tr><td>{{ _lang('Total fee') }}</td><td>{{ $fee->total_fee }}</td></tr>
	    </table>
	</div>
</div>
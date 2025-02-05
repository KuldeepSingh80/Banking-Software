<div class="card">
	<div class="card-body p-2">
	    <table class="table table-bordered">
			<tr><td>{{ _lang('Fee Id') }}</td><td>{{ $fee->fees_id }}</td></tr>
			<tr><td>{{ _lang('Fee Name') }}</td><td>{{ $fee->name }}</td></tr>
			<tr><td>{{ _lang('Description') }}</td><td>{{ $fee->description }}</td></tr>
			<tr><td>{{ _lang('Charge Type') }}</td><td>{{ $fee->charges_type }}</td></tr>	
			<tr><td>{{ _lang('Transaction Category') }}</td><td>{{ @$fee->transactionCategory->name }}</td></tr>
			<tr><td>{{ _lang('Payer') }}</td><td>{{ $fee->payer }}</td></tr>
			<tr><td>{{ _lang('Unit Of Measure') }}</td><td>{{ $fee->unit_of_measure === 'per_transaction'? 'Per Transaction': 'Per Month'  }}</td></tr>
	    </table>
	</div>
</div>
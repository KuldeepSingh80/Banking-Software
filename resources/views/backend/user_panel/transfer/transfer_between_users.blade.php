@extends('layouts.user')

@section('content')
<div class="container-xl">
	<h1 class="app-page-title text-center">{{ _lang('Transfer Between Users') }}</h1>
	<div class="row">
		<div class="col-lg-8 offset-lg-2">

			@if(Session::has('success'))
				<div class="alert alert-success alert-dismissible fade show">
	                <strong>{{ session('success') }}</strong>
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>	
			@endif

			<div class="card">
				<div class="card-body p-4">
					<form method="post" class="validate" autocomplete="off" action="{{ url('user/transfer_between_users') }}">
						{{ csrf_field() }}
						<div class="row">
							<div class="col-md-12 mb-3">
								<div class="form-group">
									<label class="control-label">{{ _lang('Debit Account') }}</label>
									<select class="form-control auto-select" name="debit_account" data-selected="{{ old('debit_account') }}" required>
										<option value="">{{ _lang('Select Account') }}</option>
										@foreach(\App\Account::where('user_id',Auth::id())->where('status',1)->get() as $debit_account )
											<option value="{{ $debit_account->id }}">{{ $debit_account->account_number }}</option>
										@endforeach
									</select>
								</div>
							</div>
							
							<div class="col-md-6 mb-3">
								<div class="form-group">
									<label class="control-label">{{ _lang('User Email') }}</label>						
									<input type="email" class="form-control" name="user_email" value="{{ old('user_email') }}" required>
								</div>
							</div>
							
							<div class="col-md-6 mb-3">
								<div class="form-group">
									<label class="control-label">{{ _lang('Account Number') }}</label>						
									<input type="text" class="form-control" name="credit_account" value="{{ old('credit_account') }}" required>
								</div>
							</div>

							<div class="col-lg-4 col-md-12 col-sm-12 mb-3">
								<div class="form-group">
									<label class="control-label">{{ _lang('Amount') }}</label>						
									<input type="text" class="form-control float-field" id="amount" oninput="chargeAmount(this)" data-payer="<?= $fee->payer; ?>" name="amount" data-senderpay= "<?= $fee->sender_pay; ?>" data-receiverpay="<?= $fee->receiver_pay ?>" value="{{ old('amount') }}" required>
								</div>
							</div>

							<?php 
								if($fee && $fee->top_up_amount) {
							?>
								<div class="col-lg-3 col-md-12 col-sm-12 mb-3">
									<div class="form-group">
										<label class="control-label">{{ _lang('Fee') }}</label>						
										<input type="text" class="form-control float-field" id="fee" data-value="{{ $fee->top_up_amount }}" name="fee" value="{{ $fee->top_up_amount }}{{ $fee->getChargeSign() }}" disabled required>
										<input type="hidden" id="charges_type" value="<?= $fee->charges_type; ?>">
									</div>
								</div>
								<div class="col-lg-5 col-md-12 col-sm-12 mb-3">
									<div class="form-group">
										<label class="control-label">{{ _lang('Total Payable Amount') }}</label>						
										<input type="text" class="form-control float-field" id="total_amount" name="fee" disabled required>
									</div>
								</div>
							<?php
								}
							?>

							<div class="col-md-12 mb-3">
								<div class="form-group">
									<label class="control-label">{{ _lang('Note') }}</label>						
									<textarea class="form-control" name="note">{{ old('note') }}</textarea>
								</div>
							</div>

							<div class="col-md-12 mb-3">
								<div class="form-group text-center">
									<button type="submit" class="btn app-btn-primary">{{ _lang('Make Transfer') }}</button>
								</div>
							</div>

						</div>			
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

<script>
	function chargeAmount(row) {
		var val = $(row).val();
		var fee = $('#fee').attr('data-value');
		var charges_type = $('#charges_type').val();
		var payer = $(row).attr('data-payer');
		var totalPayableAmount = 0;

		if(charges_type !== 'fixed') {
			fee = (fee * val) / 100;
		}

		if(payer == 'sender') {
			$('#total_amount').val(parseFloat(fee) + parseFloat(val));
		} else if(payer == 'receiver') {
			$('#total_amount').val(parseFloat(val));
		} else {
			var senderPayPercentage = $(row).attr('data-senderpay');
			var receiverPayPercentage = $(row).attr('data-receiverpay');

			// Calculate sender and receiver's share of the fee
			var senderFee = (fee * senderPayPercentage) / 100;
			var receiverFee = (fee * receiverPayPercentage) / 100;

			// Sender pays their share of the fee plus the value amount
			var senderPayableAmount = parseFloat(val) + parseFloat(senderFee);

			// var splitVal = parseFloat(val) + (fee / 2);
			$('#total_amount').val(parseFloat(senderPayableAmount));
		}
	}
</script>

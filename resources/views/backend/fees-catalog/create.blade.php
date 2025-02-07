@extends('layouts.app')

@section('content')
<div class="sb-page-header pb-10 sb-page-header-dark bg-gradient-primary-to-secondary">
	<div class="container-fluid">
		<div class="sb-page-header-content py-5">
			<h1 class="sb-page-header-title">
				<div class="sb-page-header-icon"><i data-feather="user"></i></div>
				<span>{{ $title }}</span>
			</h1>
		</div>
	</div>
</div>

<div class="container-fluid mt-n10">
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">		 
                    <form id="feeForm">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="fee_id">Fee Id <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="fee_id" placeholder="Enter fee id" required>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="fee_name">Fee name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="fee_name" placeholder="Enter fee name" required>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="fee_description">Fee Description <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="fee_description" placeholder="Enter detailed fee description" required>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="charge_type">Charge Type <span class="text-danger">*</span></label>
                                    <select name="charge_type" id="charge_type" class="form-control">
                                        <option value="fixed">Fixed</option>
                                        <option value="percentage">Percentage</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="fee_type">Transaction Category <span class="text-danger">*</span></label>
                                    <select name="fee_type" id="fee_type" class="form-control">
                                        <option value="" >Select Transaction Category</option>
                                        @foreach($transCategory as $t)
                                        <option value="{{$t->id}}">{{$t->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="payer">Payer <span class="text-danger">*</span></label>
                                    <select name="payer" id="payer" class="form-control">
                                        <option value="">Select payer</option>
                                        <option value="user">User</option>
                                        <option value="sender">Sender</option>
                                        <option value="receiver">Receiver</option>
                                        <option value="split">Split</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group row d-none" id="split_payer">
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <label for="sender_pay">Sender Pay <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="sender_pay" id="sender_pay" oninput="divideSplitPay(this, 'sender')" placeholder="Enter sender percentage">
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12">
                                        <label for="receiver_pay">Receiver Pay <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="receiver_pay" id="receiver_pay" oninput="divideSplitPay(this, 'receiver')" placeholder="Enter receiver percentage">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label for="unit_of_measure">Unit Of Measure <span class="text-danger">*</span></label>
                                    <select name="unit_of_measure" id="unit_of_measure" class="form-control">
                                        <option value="">Select Unit of measure</option>
                                        <option value="per_transaction">Per Transaction</option>
                                        <option value="per_month">Per Month</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="saveForm" class="btn btn-primary">Save</button>
                    </form>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
@section('js-script')
<script>

    $('#saveForm').click(function() {
        const feeId = $('#fee_id').val().trim();
        const feeName = $('#fee_name').val().trim();
        const feeDescription = $('#fee_description').val().trim();
        const charge_type = $('#charge_type').val().trim();
        const fee_type = $('#fee_type').val().trim();
        const payer = $('#payer').val().trim();
        const sender_pay = $('#sender_pay').val().trim();
        const receiver_pay = $('#receiver_pay').val().trim();
        const unit_of_measure = $('#unit_of_measure').val().trim();

        const data = {
            fee_id: feeId,
            feeName: feeName,
            feeDescription: feeDescription,
            charge_type: charge_type,
            fee_type: fee_type,
            sender_pay: sender_pay,
            receiver_pay: receiver_pay,
            payer: payer,
            unit_of_measure:unit_of_measure
        };

        $.ajax({
            type: "POST",
            url: '{{ route("fees-catalog.store") }}',
            data:data,
            success:function(response){
                toastr.success('Fees catalog saved successfully!')
                location.reload();
            },
            error:function(error){ 
                const response = error.responseJSON;
                toastr.error(response?.data);              
            }
        })
    });
</script>

<script>
    $('#payer').on('change', function() {
        var splitPayer = $('#split_payer');

        if ($(this).val() === 'split') {
            splitPayer.removeClass('d-none');
        } else {
            splitPayer.addClass('d-none');
        }
    });

    function divideSplitPay(row, type) {
        var value = parseFloat($(row).val());
        
        if (isNaN(value) || value < 0 || value > 100) {
            toastr.error('The value should be a number between 0 and 100!');
            $('#sender_pay').val('');
            $('#receiver_pay').val('');
            return;
        }

        var otherValue = 100 - value;

        if (type === 'sender') {
            $('#receiver_pay').val(otherValue);
        } else if (type === 'receiver') {
            $('#sender_pay').val(otherValue);
        }
    }

</script>

@endsection
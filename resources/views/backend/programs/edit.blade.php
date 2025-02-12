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
                                    <label for="fee_name">Program name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="program_name" value="{{$program->name}}" placeholder="Enter Program name" required>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="merchant">Merchant <span class="text-danger">*</span></label>
                                    <select name="merchant" id="merchant" multiple required  class="form-control select2">
                                        @foreach($merchants as $m)
                                        <option value="{{$m->id}}" {{in_array($m->id, $program->merchants->pluck('id')->toArray())? 'selected': '' }}>{{$m->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="fee-catalog-table table-responsive my-3">
                                    <table class="table table-bordered data-table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Fees ID</th>
                                                <th>Fees Name</th>
                                                <th>Fees Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($feesCatalogs as $f)
                                            <tr>
                                                <td><input type="checkbox" {{in_array($f->id, $program->feesCatalogs->pluck('id')->toArray())? 'checked': '' }} name="fees_id" class="fees_id_checkbox" value="{{$f->id}}"></td>
                                                <td>{{$f->fees_id}}</td>
                                                <td>{{$f->name}}</td>
                                                <td>{{$f->description}}</td>
                                            </tr>   
                                            @endforeach
                                        </tbody>
                                    </table>
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
    let selectedFees = {!! json_encode($program->feesCatalogs->pluck('id')->toArray()) !!};

    $(".fees_id_checkbox").click(function () {
        let feeId = $(this).val();
        
        if ($(this).is(":checked")) { 
            if (!selectedFees.includes(feeId)) {
                selectedFees.push(feeId);
            }
        } else {
            // Remove unchecked fee ID from array
            selectedFees = selectedFees.filter(id => id != feeId);
        }
    });
    $('#saveForm').click(function() {
        const url = "{{ route('programs.update', $id) }}"
        const name = $('#program_name').val().trim();
        const merchant = $('#merchant').val();
        if(!name || !merchant){
            toastr.error("Please fill required fields!");
            return; 
        }
        if(selectedFees.length === 0){
            toastr.error("Select atleast one Fee catalog!"); 
            return;
        }
        const data = {
            name: name,
            merchants: merchant,
            fees_catalogs: selectedFees,
        };
        
        $.ajax({
            type: "PUT",
            url: url,
            data:data,
            success:function(response){
                toastr.success('Program updated successfully!')
                window.location.href = '{{ route("programs.index") }}';
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
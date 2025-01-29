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
                    <form onsubmit="generateFee(event)" id="feeForm">
                        @csrf
                        <div class="form-group row">
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <label for="feeName">Fee name</label>
                                <input type="text" class="form-control" id="feeName" placeholder="Enter fee name" required>
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <label for="top_up_amount">Top up amount</label>
                                <input type="text" class="form-control" id="top_up_amount" placeholder="Enter Top up amount" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <label for="charge_type">Charge Type</label>
                                <select name="charge_type" id="charge_type" class="form-control">
                                    <option value="fixed">Fixed</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <label for="levels">How many levels</label>
                                <input type="text" class="form-control" id="levels" placeholder="Enter Levels" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <label for="merchant">Merchant</label>
                                <select name="merchant" id="merchant" class="form-control select2">
                                    <option value="0" disabled>Select merchant</option>
                                    @foreach($merchants as $m)
                                    <option value="{{$m->id}}">{{$m->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <label for="partners">Partners</label>
                                <select name="partners[]" id="partners" class="form-control select2" multiple>
                                    <option value="0" disabled>Select partners</option>
                                    @foreach($partners as $p)
                                    <option value="{{$p->id}}">{{$p->first_name}} {{$p->last_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <label for="minimun">Minimum</label>
                                <input type="text" class="form-control" id="minimun" placeholder="Enter minimun amount" required>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <label for="maximum">Maximum</label>
                                <input type="text" class="form-control" id="maximum" placeholder="Enter maximum amount" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <label for="fee_type">Transaction Category</label>
                                <select name="fee_type" id="fee_type" class="form-control">
                                    <option value="">Select Fee Type</option>
                                    <option value="deposit">Deposit</option>
                                    <option value="withdraw">Withdraw</option>
                                </select>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <label for="payer">Payer</label>
                                <select name="payer" id="payer" class="form-control">
                                    <option value="">Select payer</option>
                                    <option value="sender">Sender</option>
                                    <option value="receiver">Receiver</option>
                                    <option value="split">Split</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row d-none" id="split_payer">
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <label for="sender_pay">Sender Pay</label>
                                <input type="text" class="form-control" name="sender_pay" id="sender_pay" oninput="divideSplitPay(this, 'sender')" placeholder="Enter sender percentage">
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12">
                                <label for="receiver_pay">Receiver Pay</label>
                                <input type="text" class="form-control" name="receiver_pay" id="receiver_pay" oninput="divideSplitPay(this, 'receiver')" placeholder="Enter receiver percentage">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <label for="fixedFee">
                                    Fixed Fee &nbsp<i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="The calculated fixed fee will be displayed here."></i>
                                </label>
                                <input type="text" class="form-control" id="fixedFee" placeholder="Fixed fee" disabled required>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <label for="percentageFee">
                                    % Fee &nbsp<i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="The calculated percentage fee will be displayed here."></i>
                                </label>
                                <input type="text" class="form-control" id="percentageFee" placeholder="Percentage fee" disabled required>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <label for="totalFee">
                                    Total Fee 
                                </label>
                                <input type="text" class="form-control" id="totalFee" placeholder="Total fee" disabled required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Generate</button>
                        <button type="button" id="saveForm" class="btn btn-primary btn-save-fee " disabled>Save</button>
                    </form>
				</div>
			</div>

            <div class="card mt-2 fee-management-module d-none">
                <div class="card-body">
                    <div class="row" id="sharing-levels-container">
                        
                    </div>
                </div>
            </div>
		</div>
	</div>
</div>

@endsection
@section('js-script')
<script>
    let partners = [];
    async function generateFee(e) {
        e.preventDefault();
        var levels = $('#levels').val().trim();
        const partnersIds = $('#partners').val();

        // Clear previous validation messages
        $('.validation-error').remove();

        // Check if levels or partners are empty
        if (levels === '') {
            $('#levels').after('<span class="validation-error text-danger">Levels field is required</span>');
            return false;
        }

        if(!Number.isInteger(Number(levels))) {
            $('#levels').after('<span class="validation-error text-danger">Invalid level input.</span>');
            return false;
        }

        if (partnersIds === '') {
            $('#partners').after('<span class="validation-error text-danger">Partners field is required</span>');
            return false;
        }

        // if(!Number.isInteger(Number(partners))) {
        //     $('#partners').after('<span class="validation-error text-danger">Invalid partners input.</span>');
        //     return false;
        // }

        $('#sharing-levels-container').empty();

        $('.fee-management-module').removeClass('d-none');
        partners = await getPartnersName(partnersIds);        
        var tableHtml = '';
        for (let levelIndex = 1; levelIndex <= levels; levelIndex++) {
            tableHtml = `
            <div class="col-lg-6 col-md-6 col-sm-12 mt-2">
                <div class="sharing-levels">
                    <h6 class="fee-management-header">Sharing Level ${levelIndex}</h6>
                    <table class="table table-bordered">
                        <tr>
                            <td class="text-center p-1" colspan="2">Base Cost ${levelIndex - 1}</td>
                        </tr>
                        <tr>
                            <td class="p-1">Fixed ($)</td>
                            <td class="p-1"><input type="text" class="fee-calc-input" id="base-fixed-${levelIndex}" placeholder="Enter fixed value"></td>
                        </tr>
                        <tr>
                            <td class="p-1">Percentage (%)</td>
                            <td class="p-1"><input type="text" class="fee-calc-input" id="base-percentage-${levelIndex}" placeholder="Enter percentage value"></td>
                        </tr>
                    </table>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="p-1"></th>
                                <th class="p-1">Markup ${levelIndex}</th>
                                <th class="p-1">Base Cost ${levelIndex}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="p-1">Fixed ($)</td>
                                <td class="p-1"><input type="text" class="fee-calc-input" id="fixed-markup-${levelIndex}" placeholder="Enter fixed value" onchange="updateFixedBaseCost(this)" data-level-index="${levelIndex}"/></td>
                                <td class="p-1"><span id="fixed-base-cost-${levelIndex}"></span></td>
                            </tr>
                            <tr>
                                <td class="p-1">Percentage (%)</td>
                                <td class="p-1"><input type="text" class="fee-calc-input" id="percentage-markup-${levelIndex}" placeholder="Enter percentage value" onchange="updatePercentageBaseCost(this)" data-level-index="${levelIndex}"/></td>
                                <td class="p-1"><span id="percentage-base-cost-${levelIndex}"></span></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="p-1"></th>
                                <th class="p-1">Sharing</th>
                                <th class="p-1">Fixed</th>
                                <th class="p-1">Percentage</th>
                            </tr>
                        </thead>
                        <tbody>`;
                        for (const partner of partners) {
                            tableHtml += `
                            <tr>
                                <td class="p-1">${partner.first_name} ${partner.last_name}</td>
                                <td class="p-1">
                                    <input type="text" class="fee-calc-input" placeholder="Add partner share" onchange="partnerValueUpdate(this)" id="partner${partner.id}-sharing-${levelIndex}" data-level-index="${levelIndex}" data-partner-index="${partner.id}">
                                </td>
                                <td class="p-1"><span id="partner${partner.id}-fixed-share-${levelIndex}">0.00</span></td>
                                <td class="p-1"><span id="partner${partner.id}-percentage-share-${levelIndex}">0.00</span></td>
                            </tr>`;
                        }
                        tableHtml += `
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="p-1">Total</th>
                                <th class="p-1" <span id="partner-sharing-total-${levelIndex}"></th>
                                <th class="p-1">$ <span id="partner-fixed-total-${levelIndex}">0.00</span></th>
                                <th class="p-1">% <span id="partner-percentage-total-${levelIndex}">0.00</span></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>`;

            $('#sharing-levels-container').append(tableHtml);
        }
    }

    function getPartnersName(ids) {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "POST",
                url: "{{ url('admin/partners/selected') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    ids: ids
                },
                success: function(response) {
                    if (response.success === true) {
                        resolve(response.data);
                    } else {
                        resolve([]); // Resolve with an empty array if the response is not successful
                    }
                },
                error: function(error) {
                    reject(error); // Reject the promise in case of an error
                }
            });
        });
    }


    function updateFixedBaseCost(record) {
        var levelIndex = $(record).data('level-index');

        var fixedMarkup = parseFloat($(record).val()) || 0;
        var baseFixed = parseFloat($(`#base-fixed-${levelIndex}`).val()) || 0;

        var result = fixedMarkup + baseFixed;
        $(`#fixed-base-cost-${levelIndex}`).text(result.toFixed(2));

        // Check if the next level exists before updating its value
        if ($(`#base-fixed-${levelIndex + 1}`).length > 0) {
            $(`#base-fixed-${levelIndex + 1}`).val(result.toFixed(2));
        }

        var levels = $('#levels').val().trim();
        
        if(levels == levelIndex) {
            $('#fixedFee').val(result);
            $('.btn-save-fee').removeAttr('disabled');
        }
    }

    function updatePercentageBaseCost(record) {
        var levelIndex = $(record).data('level-index');

        var percentageMarkup = parseFloat($(record).val()) || 0;
        var basePercentage = parseFloat($(`#base-percentage-${levelIndex}`).val()) || 0;

        var result = percentageMarkup + basePercentage;
        $(`#percentage-base-cost-${levelIndex}`).text(result.toFixed(2));

        // Check if the next level exists before updating its value
        if ($(`#base-percentage-${levelIndex + 1}`).length > 0) {
            $(`#base-percentage-${levelIndex + 1}`).val(result.toFixed(2));
        }

        var levels = $('#levels').val().trim();
        
        if(levels == levelIndex) {
            $('#percentageFee').val(result);

            var fixedFee = $('#fixedFee').val();
            var totalFee = (parseFloat(result / 100) + parseFloat(fixedFee)).toFixed(2);

            $('#totalFee').val(totalFee)

            $('.btn-save-fee').removeAttr('disabled');
        }
    }

    function partnerValueUpdate(record) {
        var levelIndex = $(record).data('level-index');
        var partnerIndex = $(record).data('partner-index');

        var fixedMarkupVal = $(`#fixed-markup-${levelIndex}`).val();
        var percentageMarkupVal = $(`#percentage-markup-${levelIndex}`).val();
        var sharingVal = parseFloat($(record).val()) || 0;
        var fixedPartnerSharing = 0;
        var percentagePartnerSharing = 0;

        if(sharingVal > 0) {
            fixedPartnerSharing = (sharingVal / 100) * fixedMarkupVal;
            percentagePartnerSharing = (sharingVal / 100) * percentageMarkupVal;

            $(`#partner${partnerIndex}-fixed-share-${levelIndex}`).text(fixedPartnerSharing.toFixed(3));
            $(`#partner${partnerIndex}-percentage-share-${levelIndex}`).text(percentagePartnerSharing.toFixed(3));
        } else {
            $(`#partner${partnerIndex}-fixed-share-${levelIndex}`).text(0.00);
            $(`#partner${partnerIndex}-percentage-share-${levelIndex}`).text(0.00);
        }

        var partnerSharingTotal = 0.00;
        var partnerFixedTotal = 0.00;
        var partnerPercentageTotal = 0.00;

        for (const partner of partners) {
            partnerSharingTotal += parseFloat($(`#partner${partner.id}-sharing-${levelIndex}`).val()) || 0;
            partnerFixedTotal += parseFloat($(`#partner${partner.id}-fixed-share-${levelIndex}`).text());
            partnerPercentageTotal += parseFloat($(`#partner${partner.id}-percentage-share-${levelIndex}`).text());
        }

        $(`#partner-sharing-total-${levelIndex}`).text(partnerSharingTotal.toFixed(3));
        $(`#partner-fixed-total-${levelIndex}`).text(partnerFixedTotal.toFixed(3));
        $(`#partner-percentage-total-${levelIndex}`).text(partnerPercentageTotal.toFixed(3));
    }

    $('#saveForm').click(function() {
        var feeName = $('#feeName').val().trim();
        var topUpAmount = $('#top_up_amount').val().trim();
        var levels = $('#levels').val().trim();
        var partnerIds = $('#partners').val();
        var merchant = $('#merchant').val();
        var minimum = $('#minimun').val().trim();
        var maximum = $('#maximum').val().trim();
        var fixedFee = $('#fixedFee').val().trim();
        var percentageFee = $('#percentageFee').val().trim();
        var totalFee = $('#totalFee').val().trim();
        var fee_type = $('#fee_type').val().trim();
        var payer = $('#payer').val().trim();
        var sender_pay = $('#sender_pay').val().trim();
        var receiver_pay = $('#receiver_pay').val().trim();
        var charge_type = $('#charge_type').val().trim();

        var data = {
            name: feeName,
            top_up_amount: topUpAmount,
            levels: levels,
            partners: partnerIds,
            minimum: minimum,
            maximum: maximum,
            fixed_fee: fixedFee,
            percentage_fee: percentageFee,
            total_fee: totalFee,
            transaction_category: fee_type,
            payer: payer,
            sender_pay: sender_pay,
            receiver_pay: receiver_pay,
            charges_type: charge_type,
            merchant_id: merchant,
            levels_data: []
        };

        // Collect data for each level
        for (var levelIndex = 1; levelIndex <= levels; levelIndex++) {
            var levelData = {
                level_index: levelIndex,
                base_fixed: parseFloat($(`#base-fixed-${levelIndex}`).val()),
                base_percentage: parseFloat($(`#base-percentage-${levelIndex}`).val()),
                fixed_markup_cost: parseFloat($(`#fixed-markup-${levelIndex}`).val()),
                fixed_markup_base_cost: parseFloat($(`#fixed-base-cost-${levelIndex}`).text()),
                percentage_markup_cost: parseFloat($(`#percentage-markup-${levelIndex}`).val()),
                percentage_markup_base_cost: parseFloat($(`#percentage-base-cost-${levelIndex}`).text())
            };

            levelData.partners = [];
            for (const partner of partners) {
                levelData.partners.push({
                    partner_id: partner.id,
                    sharing: parseFloat($(`#partner${partner.id}-sharing-${levelIndex}`).val()),
                    fixed_share: parseFloat($(`#partner${partner.id}-fixed-share-${levelIndex}`).text()),
                    percentage_share: parseFloat($(`#partner${partner.id}-percentage-share-${levelIndex}`).text())
                });
            }

            data.levels_data.push(levelData);
        }

        fetch('{{ route("fee.save_fee") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
           if(data.result == 'error') {
                toastr.error('Invalid Input!');
           } else {
                toastr.success('Fee saved successfully!')
                window.location.href = '{{ route("utility.fee_management") }}';
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
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
                                <label for="levels">How many levels</label>
                                <input type="text" class="form-control" id="levels" placeholder="Enter Levels" required>
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <label for="partners">How many partners</label>
                                <input type="text" class="form-control" id="partners" placeholder="Enter Partners" required>
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
                        <button type="button" id="saveForm" class="btn btn-primary btn-save-fee d-none">Save</button>
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
    function generateFee(e) {
        e.preventDefault();
        var levels = $('#levels').val().trim();
        var partners = $('#partners').val().trim();

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

        if (partners === '') {
            $('#partners').after('<span class="validation-error text-danger">Partners field is required</span>');
            return false;
        }

        if(!Number.isInteger(Number(partners))) {
            $('#partners').after('<span class="validation-error text-danger">Invalid partners input.</span>');
            return false;
        }

        $('#sharing-levels-container').empty();

        $('.fee-management-module').removeClass('d-none');

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
                        for (let partnerIndex = 1; partnerIndex <= partners; partnerIndex++) {
                            tableHtml += `
                            <tr>
                                <td class="p-1">Partner ${partnerIndex}</td>
                                <td class="p-1">
                                    <input type="text" class="fee-calc-input" placeholder="Add partner share" onchange="partnerValueUpdate(this)" id="partner${partnerIndex}-sharing-${levelIndex}" data-level-index="${levelIndex}" data-partner-index="${partnerIndex}">
                                </td>
                                <td class="p-1"><span id="partner${partnerIndex}-fixed-share-${levelIndex}">0.00</span></td>
                                <td class="p-1"><span id="partner${partnerIndex}-percentage-share-${levelIndex}">0.00</span></td>
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

            $('.btn-save-fee').removeClass('d-none');
        }
    }

    function partnerValueUpdate(record) {
        var levelIndex = $(record).data('level-index');
        var partnerIndex = $(record).data('partner-index');
        var partners = $('#partners').val().trim();

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

        for (let partner = 1; partner <= partners; partner++) {
            partnerSharingTotal += parseFloat($(`#partner${partner}-sharing-${levelIndex}`).val()) || 0;
            partnerFixedTotal += parseFloat($(`#partner${partner}-fixed-share-${levelIndex}`).text());
            partnerPercentageTotal += parseFloat($(`#partner${partner}-percentage-share-${levelIndex}`).text());
        }

        $(`#partner-sharing-total-${levelIndex}`).text(partnerSharingTotal.toFixed(3));
        $(`#partner-fixed-total-${levelIndex}`).text(partnerFixedTotal.toFixed(3));
        $(`#partner-percentage-total-${levelIndex}`).text(partnerPercentageTotal.toFixed(3));
    }

    $('#saveForm').click(function() {
        var feeName = $('#feeName').val().trim();
        var topUpAmount = $('#top_up_amount').val().trim();
        var levels = $('#levels').val().trim();
        var partners = $('#partners').val().trim();
        var minimum = $('#minimun').val().trim();
        var maximum = $('#maximum').val().trim();
        var fixedFee = $('#fixedFee').val().trim();
        var percentageFee = $('#percentageFee').val().trim();
        var totalFee = $('#totalFee').val().trim();

        var data = {
            name: feeName,
            top_up_amount: topUpAmount,
            levels: levels,
            partners: partners,
            minimum: minimum,
            maximum: maximum,
            fixed_fee: fixedFee,
            percentage_fee: percentageFee,
            total_fee: totalFee,
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
            for (var partnerIndex = 1; partnerIndex <= partners; partnerIndex++) {
                levelData.partners.push({
                    partner_index: partnerIndex,
                    sharing: parseFloat($(`#partner${partnerIndex}-sharing-${levelIndex}`).val()),
                    fixed_share: parseFloat($(`#partner${partnerIndex}-fixed-share-${levelIndex}`).text()),
                    percentage_share: parseFloat($(`#partner${partnerIndex}-percentage-share-${levelIndex}`).text())
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

@endsection
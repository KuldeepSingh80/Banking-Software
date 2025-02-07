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
                        <div class="row">
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="merchant">Merchant</label>
                                    <select name="merchant" id="merchant" required data-placeholder="Select a merchant" class="form-control select2">
                                        <option value="" ></option>
                                        @foreach($merchants as $m)
                                        <option value="{{$m->id}}">{{$m->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="fees_catalog">Fees Catalogs</label>
                                    <select name="fees_catalog[]" id="fees_catalog" required multiple class="form-control select2">
                                        @foreach($feesCatalog as $f)
                                        <option value="{{$f->id}}">{{$f->fees_id}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="levels">How many levels</label>
                                    <input type="text" class="form-control" id="levels" placeholder="Enter Levels" required>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="partners">Partners</label>
                                    <select name="partners[]" id="partners" class="form-control select2" multiple>
                                        @foreach($partners as $p)
                                        <option value="{{$p->id}}">{{$p->first_name}} {{$p->last_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="minimun">Minimum</label>
                                    <input type="text" class="form-control" id="minimun" placeholder="Enter minimun amount" required>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label for="maximum">Maximum</label>
                                    <input type="text" class="form-control" id="maximum" placeholder="Enter maximum amount" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Generate</button>
                        <button type="button" id="saveForm" class="btn btn-primary btn-save-fee ">Save</button>
                    </form>
				</div>
			</div>

            <div class="card mt-2 fee-management-module d-none">
                <div class="card-body">
                    <div class="fees-container">
                        <div class="tabs-container">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#tab1" data-toggle="tab">Setup Fee</a></li>
                                <li><a href="#tab2" data-toggle="tab">Total Fee</a></li>
                                <li><a href="#tab3" data-toggle="tab">New Fees</a></li>
                            </ul>
                        </div>
                        <div class="tab-content sharing-level-container">
                        </div>
                    </div>
                    <!-- <div class="row" id="sharing-levels-container">
                        
                    </div> -->
                </div>
            </div>
		</div>
	</div>
</div>

@endsection
@section('js-script')
<script>
    let partners = [];
    let feesCatalog = [];
    const generateFee = async (e) => {
        e.preventDefault();
        var levels = $('#levels').val().trim();
        const partnersIds = $('#partners').val();
        const feesCatalogId = $('#fees_catalog').val();

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
        $('.fee-management-module .tabs-container ul').empty();  
        $('.sharing-level-container').empty();

        $('.fee-management-module').removeClass('d-none');
        partners = await getPartnersName(partnersIds);        
        feesCatalog = await getFeesCatalog(feesCatalogId);  
        let feeCatalogTabs = ``;
        let feeCatalogContent = '';
        for (const  [index, fee] of feesCatalog.entries()) {
            feeCatalogTabs += `<li class="${index === 0? 'active': ''}"><a href="#${fee.fees_id}"  data-toggle="tab">${fee.fees_id}</a></li>`;     
            let tableHtml = '';
            for (let levelIndex = 1; levelIndex <= levels; levelIndex++) {
                tableHtml += `
                <div class="col-lg-6 col-md-6 col-sm-12 mt-2">
                    <div class="sharing-levels">
                        <h6 class="fee-management-header">Sharing Level ${levelIndex}</h6>
                        <table class="table table-bordered">
                            <tr>
                                <td class="text-center p-1" colspan="2">Base Cost ${levelIndex - 1}</td>
                            </tr>
                            <tr>
                                <td class="p-1">Fixed ($)</td>
                                <td class="p-1"><input type="text" class="fee-calc-input" id="${fee.fees_id}-base-fixed-${levelIndex}" placeholder="Enter fixed value"></td>
                            </tr>
                            <tr>
                                <td class="p-1">Percentage (%)</td>
                                <td class="p-1"><input type="text" class="fee-calc-input" id="${fee.fees_id}-base-percentage-${levelIndex}" placeholder="Enter percentage value"></td>
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
                                    <td class="p-1"><input type="text" class="fee-calc-input" id="${fee.fees_id}-fixed-markup-${levelIndex}" placeholder="Enter fixed value" onchange="updateFixedBaseCost(this)" data-fees-id="${fee.fees_id}" data-level-index="${levelIndex}"/></td>
                                    <td class="p-1"><span id="${fee.fees_id}-fixed-base-cost-${levelIndex}"></span></td>
                                </tr>
                                <tr>
                                    <td class="p-1">Percentage (%)</td>
                                    <td class="p-1"><input type="text" class="fee-calc-input" id="${fee.fees_id}-percentage-markup-${levelIndex}" placeholder="Enter percentage value" data-fees-id="${fee.fees_id}"  onchange="updatePercentageBaseCost(this)" data-level-index="${levelIndex}"/></td>
                                    <td class="p-1"><span id="${fee.fees_id}-percentage-base-cost-${levelIndex}"></span></td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="p-1"></th>
                                    <th class="p-1">Sharing (in %)</th>
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
                                        <input type="text" class="fee-calc-input"  placeholder="Add partner share" onchange="partnerValueUpdate(this)" data-fees-id="${fee.fees_id}" id="${fee.fees_id}-partner${partner.id}-sharing-${levelIndex}" data-level-index="${levelIndex}" data-partner-index="${partner.id}">
                                    </td>
                                    <td class="p-1"><span id="${fee.fees_id}-partner${partner.id}-fixed-share-${levelIndex}">0.00</span></td>
                                    <td class="p-1"><span id="${fee.fees_id}-partner${partner.id}-percentage-share-${levelIndex}">0.00</span></td>
                                </tr>`;
                            }
                            tableHtml += `
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="p-1">Total</th>
                                    <th class="p-1" <span id="${fee.fees_id}-partner-sharing-total-${levelIndex}"></th>
                                    <th class="p-1">$ <span id="${fee.fees_id}-partner-fixed-total-${levelIndex}">0.00</span></th>
                                    <th class="p-1">% <span id="${fee.fees_id}-partner-percentage-total-${levelIndex}">0.00</span></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>`;
            }
            feeCatalogContent += `
                <div class="tab-pane ${index === 0? 'active': ''}" id="${fee.fees_id}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="${fee.fees_id}-fee_name">Fee Name</label>
                                <input type="text" class="form-control" id="${fee.fees_id}-fee_name" value="${fee.name}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="${fee.fees_id}-charge_type">Charge Type</label>
                                <input type="text" class="form-control" id="${fee.fees_id}-charge_type" value="${fee.charges_type}"
                                    readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="${fee.fees_id}-transaction_category">Transaction Category</label>
                                <select name="transaction_category" id="${fee.fees_id}-transaction_category" class="form-control" readonly>
                                    <option value="${fee?.transaction_category?.id}" selected>${fee?.transaction_category?.name}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="${fee.fees_id}-payer">Payer</label>
                                <input type="text" class="form-control" id="${fee.fees_id}-payer" value="${fee.payer}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 ${fee.payer !== 'split'? 'd-none':'' }">
                            <div class="form-group">
                                <label for="${fee.fees_id}-sender_pay">Sender</label>
                                <input type="text" class="form-control" id="${fee.fees_id}-sender_pay" value="${fee.sender_pay}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 ${fee.payer !== 'split'? 'd-none':'' }">
                            <div class="form-group">
                                <label for="${fee.fees_id}-receiver_pay">Receiver</label>
                                <input type="text" class="form-control" id="${fee.fees_id}-receiver_pay" value="${fee.receiver_pay}" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <div class="form-group">
                                <label for="${fee.fees_id}-fixedFee">
                                    Fixed Fee &nbsp;<i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="" data-original-title="The calculated fixed fee will be displayed here."></i>
                                </label>
                                <input type="text" class="form-control" id="${fee.fees_id}-fixedFee" placeholder="Fixed fee" value="0" disabled="" required="">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <label for="${fee.fees_id}-percentageFee">
                                % Fee &nbsp;<i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="" data-original-title="The calculated percentage fee will be displayed here."></i>
                            </label>
                            <input type="text" class="form-control" id="${fee.fees_id}-percentageFee" placeholder="Percentage fee" value="0" disabled="" required="">
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <label for="${fee.fees_id}-totalFee">
                                Total Fee 
                            </label>
                            <input type="text" class="form-control" id="${fee.fees_id}-totalFee" placeholder="Total fee" value="0" disabled="" required="">
                        </div>
                    </div>
                    <div class="row">
                    ${tableHtml}
                    </div>
                </div>`
        } 
        $('.fee-management-module .tabs-container ul').html(feeCatalogTabs);  
        $('.sharing-level-container').html(feeCatalogContent);
        $('.fee-management-module ul li a').on("click", function (e) {
            e.preventDefault();
            $(".fee-management-module ul li").removeClass('active');
            const tabId = $(this).attr('href');
            $(this).parent().addClass('active');
            $('.sharing-level-container .tab-pane').removeClass('active')
            $(tabId).tab('show');
            // $('#0003').tab('show')
        });
    }

    const getPartnersName = (ids) => {
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

    const getFeesCatalog = (ids) => {
        return new Promise((resolve, reject) => {
            $.ajax({
                type: "POST",
                url: "{{ url('admin/fees-catalog/selected') }}",
                data: {
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


    const updateFixedBaseCost = (record) => {
        const levelIndex = $(record).data('level-index');
        const feesId = $(record).data('fees-id');

        const fixedMarkup = parseFloat($(record).val()) || 0;
        const baseFixed = parseFloat($(`#${feesId}-base-fixed-${levelIndex}`).val()) || 0;

        const result = fixedMarkup + baseFixed;
        $(`#${feesId}-fixed-base-cost-${levelIndex}`).text(result.toFixed(2));

        // Check if the next level exists before updating its value
        if ($(`#${feesId}-base-fixed-${levelIndex + 1}`).length > 0) {
            $(`#${feesId}-base-fixed-${levelIndex + 1}`).val(result.toFixed(2));
        }

        const levels = $('#levels').val().trim();
        
        if(levels == levelIndex) {
            $(`#${feesId}-fixedFee`).val(result);
        }
    }

    const updatePercentageBaseCost = (record) => {
        const levelIndex = $(record).data('level-index');
        const feesId = $(record).data('fees-id');

        const percentageMarkup = parseFloat($(record).val()) || 0;
        const basePercentage = parseFloat($(`#${feesId}-base-percentage-${levelIndex}`).val()) || 0;

        const result = percentageMarkup + basePercentage;
        $(`#${feesId}-percentage-base-cost-${levelIndex}`).text(result.toFixed(2));

        // Check if the next level exists before updating its value
        if ($(`#${feesId}-base-percentage-${levelIndex + 1}`).length > 0) {
            $(`#${feesId}-base-percentage-${levelIndex + 1}`).val(result.toFixed(2));
        }

        const levels = $('#levels').val().trim();
        
        if(levels == levelIndex) {
            $(`#${feesId}-percentageFee`).val(result);

            const fixedFee = $(`#${feesId}-fixedFee`).val();
            const totalFee = (parseFloat(result / 100) + parseFloat(fixedFee)).toFixed(2);

            $(`#${feesId}-totalFee`).val(totalFee)
        }
    }

    const partnerValueUpdate = (record) => {
        const levelIndex = $(record).data('level-index');
        const partnerIndex = $(record).data('partner-index');
        const feesId = $(record).data('fees-id');

        const fixedMarkupVal = $(`#${feesId}-fixed-markup-${levelIndex}`).val();
        const percentageMarkupVal = $(`#${feesId}-percentage-markup-${levelIndex}`).val();
        const sharingVal = parseFloat($(record).val()) || 0;
        const fixedBaseCost = parseFloat($(`#${feesId}-fixed-base-cost-${levelIndex}`).text())
        const percentageBaseCost = parseFloat($(`#${feesId}-percentage-base-cost-${levelIndex}`).text())
        let fixedPartnerSharing = 0;
        let percentagePartnerSharing = 0;
        
        if(sharingVal > 0) {
            // fixedPartnerSharing = fixedMarkupVal > 0? (sharingVal / 100) * fixedMarkupVal: (sharingVal / 100) * fixedBaseCost;
            // percentagePartnerSharing = percentageMarkupVal > 0? (sharingVal / 100) * percentageMarkupVal: (sharingVal / 100) * percentageBaseCost;

            
            fixedPartnerSharing = (sharingVal / 100) * fixedMarkupVal;
            percentagePartnerSharing = (sharingVal / 100) * percentageMarkupVal;

            $(`#${feesId}-partner${partnerIndex}-fixed-share-${levelIndex}`).text(fixedPartnerSharing.toFixed(3));
            $(`#${feesId}-partner${partnerIndex}-percentage-share-${levelIndex}`).text(percentagePartnerSharing.toFixed(3));
        } else {
            $(`#${feesId}-partner${partnerIndex}-fixed-share-${levelIndex}`).text(0.00);
            $(`#${feesId}-partner${partnerIndex}-percentage-share-${levelIndex}`).text(0.00);
        }

        let partnerSharingTotal = 0.00;
        let partnerFixedTotal = 0.00;
        let partnerPercentageTotal = 0.00;

        for (const partner of partners) {
            partnerSharingTotal += parseFloat($(`#${feesId}-partner${partner.id}-sharing-${levelIndex}`).val()) || 0;
            partnerFixedTotal += parseFloat($(`#${feesId}-partner${partner.id}-fixed-share-${levelIndex}`).text());
            partnerPercentageTotal += parseFloat($(`#${feesId}-partner${partner.id}-percentage-share-${levelIndex}`).text());
        }

        $(`#${feesId}-partner-sharing-total-${levelIndex}`).text(partnerSharingTotal.toFixed(3));
        $(`#${feesId}-partner-fixed-total-${levelIndex}`).text(partnerFixedTotal.toFixed(3));
        $(`#${feesId}-partner-percentage-total-${levelIndex}`).text(partnerPercentageTotal.toFixed(3));
    }
    
    $('input').on('input', function(){
        const val = $(this).val();
        if(val){
            $(this).removeClass('invalid-input');
        }else{
            $(this).addClass('invalid-input');
        }
    })
           
    let isValid = true;    
    function validateField(selector) {        
        if (!selector.val()) {
            selector.addClass('invalid-input');
            isValid = false;
        } else {
            selector.removeClass('invalid-input');
            isValid = true;
        }
    }

    $('#saveForm').click(async function() {
        const feesCatalogId = $('#fees_catalog').val(); 
        const feesCatalog = await getFeesCatalog(feesCatalogId);  
        let data = [];
        for (const fee of feesCatalog) {
            
            const feeName = $(`#${fee.fees_id}-fee_name`).val().trim();
            // const topUpAmount = $(`#${fee.fees_id}-top_up_amount`).val().trim();
            const levels = $('#levels').val().trim();
            const partnerIds = $('#partners').val();
            const merchant = $('#merchant').val();
            const minimum = $('#minimun').val().trim();
            const maximum = $('#maximum').val().trim();
            const fixedFee = $(`#${fee.fees_id}-fixedFee`).val().trim();
            const percentageFee = $(`#${fee.fees_id}-percentageFee`).val().trim();
            const totalFee = $(`#${fee.fees_id}-totalFee`).val().trim();
            const fee_type = $(`#${fee.fees_id}-transaction_category`).val().trim();
            const payer = $(`#${fee.fees_id}-payer`).val().trim();
            const sender_pay = $(`#${fee.fees_id}-sender_pay`).val().trim();
            const receiver_pay = $(`#${fee.fees_id}-receiver_pay`).val().trim();
            const charge_type = $(`#${fee.fees_id}-charge_type`).val().trim();
            let levels_data = [];
            validateField($(`#${fee.fees_id}-fee_name`));
            validateField($('#levels'));
            validateField($('#partners'));
            validateField($('#merchant'));
            validateField($('#minimun'));
            validateField($('#maximum'));
            validateField($(`#${fee.fees_id}-fixedFee`));
            validateField($(`#${fee.fees_id}-percentageFee`));
            validateField($(`#${fee.fees_id}-totalFee`));
            validateField($(`#${fee.fees_id}-transaction_category`));
            validateField($(`#${fee.fees_id}-payer`));
            validateField($(`#${fee.fees_id}-sender_pay`));
            validateField($(`#${fee.fees_id}-receiver_pay`));
            validateField($(`#${fee.fees_id}-charge_type`));
            // Collect data for each level
            for (var levelIndex = 1; levelIndex <= levels; levelIndex++) {
                var levelData = {
                    level_index: levelIndex,
                    base_fixed: parseFloat($(`#${fee.fees_id}-base-fixed-${levelIndex}`).val()),
                    base_percentage: parseFloat($(`#${fee.fees_id}-base-percentage-${levelIndex}`).val()),
                    fixed_markup_cost: parseFloat($(`#${fee.fees_id}-fixed-markup-${levelIndex}`).val()),
                    fixed_markup_base_cost: parseFloat($(`#${fee.fees_id}-fixed-base-cost-${levelIndex}`).text()),
                    percentage_markup_cost: parseFloat($(`#${fee.fees_id}-percentage-markup-${levelIndex}`).val()),
                    percentage_markup_base_cost: parseFloat($(`#${fee.fees_id}-percentage-base-cost-${levelIndex}`).text())
                };
                validateField($(`#${fee.fees_id}-base-fixed-${levelIndex}`));
                validateField($(`#${fee.fees_id}-base-percentage-${levelIndex}`));
                validateField($(`#${fee.fees_id}-fixed-markup-${levelIndex}`));
                validateField($(`#${fee.fees_id}-percentage-markup-${levelIndex}`));
    
                levelData.partners = [];
                for (const partner of partners) {
                    levelData.partners.push({
                        partner_id: partner.id,
                        sharing: parseFloat($(`#${fee.fees_id}-partner${partner.id}-sharing-${levelIndex}`).val()),
                        fixed_share: parseFloat($(`#${fee.fees_id}-partner${partner.id}-fixed-share-${levelIndex}`).text()),
                        percentage_share: parseFloat($(`#${fee.fees_id}-partner${partner.id}-percentage-share-${levelIndex}`).text())
                    });
                    validateField($(`#${fee.fees_id}-partner${partner.id}-sharing-${levelIndex}`));
                }
    
                levels_data.push(levelData);
            }
            data.push({
                fees_catalog_id: fee.id,
                fees_id: fee.fees_id,
                name: feeName,
                // top_up_amount: topUpAmount,
                levels: levels,
                partners: partnerIds,
                minimum: minimum,
                maximum: maximum,
                fixed_fee: fixedFee,
                percentage_fee: percentageFee,
                total_fee: totalFee,
                transaction_category_id: fee_type,
                payer: payer,
                sender_pay: sender_pay,
                receiver_pay: receiver_pay,
                charges_type: charge_type,
                merchant_id: merchant,
                levels_data: levels_data
            });
        }
        if (!isValid) {
            toastr.error('Please fill all required fields!')
            return;
        }
        console.log(data, "=======");
        $.ajax({
            type: "POST",
            url: `{{ route("fee.store") }}`,
            data: {
                detail: data 
            },
            success: function (response) {
                toastr.success('Fee saved successfully!')
                window.location.href = '{{ route("utility.fee_management") }}';
            },
            error: function (error) {
                toastr.error('Something went wrong!')
            }
        });
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
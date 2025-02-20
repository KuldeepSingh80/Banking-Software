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
                                    <input type="text" class="form-control" id="merchant" value="{{@$fee->merchant->name}}" readonly>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12 col-sm-12">
                                <div class="form-group">
                                        <label for="program">Program</label>
                                        <input type="text" class="form-control" id="program" value="{{@$fee->program->name}}" readonly>
                                    </div>
                            </div>
                            <div class="col-md-12">
                                <div class="fee-catalog-container">
                                </div>
                            </div>
                        </div>
                        <button type="button" id="saveForm" class="btn btn-primary btn-save-fee ">
                            Save
                            <div class="btn-loader"></div>
                        </button>
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
                        <div class="tab-content fees-catalog-container">
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
    let partners = @json($partners);
    let selectedProgramId = <?= @$fee->program->id ?>;
    let addedFeeInfo = <?= @$fee->feeInfo ?>;
    let selected_partners = @json(array_values($partnerIds));
    let sharingLevels = @json($sharingLevels);
    let levelCount = 0;
    let feeConfigId = <?= $fee->id ?>;
    
    $(document).ready(function(){
        programChange(selectedProgramId)        
    })
    
    let selectedFeesCatalogs = [];
    const generateFee = async () => {
        $(".generate-btn").on("click", async function(){
            const feeId = $(this).data('fee-id');
            const levels = $(`#${feeId}-levels`).val().trim();
            const partnersIds = $(`#${feeId}-partners`).val();
            const minimum = $(`#${feeId}-minimum`).val().trim();
            const maximum = $(`#${feeId}-maximum`).val().trim();
            
            validateField($(`#${feeId}-levels`));
            validateField($(`#${feeId}-partners`));
            validateField($(`#${feeId}-minimum`));
            validateField($(`#${feeId}-maximum`));
            
            $(`.${feeId}-sharing-level-container`).empty();
            const selectedPartners = await getPartnersName(partnersIds);
            // const selectedPartners = partnersIds;
               
            let sharingLevel = '';
            let indexLevel = 0;            
            for (let levelIndex = 1; levelIndex <= levels; levelIndex++) {
                const addedSharing = sharingLevels[levelCount][indexLevel];                             
                let partnerBaseAmount = ``
                if(levelIndex === 1){
                    partnerBaseAmount = `
                            <tr>
                                <td class="p-1">Partner </td>
                                <td class="p-1">
                                    <select name="partner" class="form-control custom-select-partner" id="${feeId}-base-cost-partner">
                                    <option value="">Select partner</option>
                                    ${partners.map(p => `<option value=${p.id} ${addedSharing?.base_cost_partner_id === p.id? "selected": ""}>${p.first_name} ${p.last_name}</option>`)}
                                    </select>
                                </td>
                            </tr>`
                }
                sharingLevel += `
                <div class="col-lg-6 col-md-6 col-sm-12 mt-2">
                    <div class="sharing-levels">
                        <h6 class="fee-management-header">Sharing Level ${levelIndex}</h6>
                        <table class="table table-bordered">
                            <tr>
                                <td class="text-center p-1" colspan="2">Base Cost ${levelIndex - 1}</td>
                            </tr>
                            <tr>
                                <td class="p-1">Fixed ($)</td>
                                <td class="p-1"><input type="text" class="fee-calc-input" id="${feeId}-base-fixed-${levelIndex}" value="${addedSharing?.fixed_base_cost}" placeholder="Enter fixed value"></td>
                            </tr>
                            <tr>
                                <td class="p-1">Percentage (%)</td>
                                <td class="p-1"><input type="text" class="fee-calc-input" value="${addedSharing?.percentage_base_cost}" id="${feeId}-base-percentage-${levelIndex}" placeholder="Enter percentage value"></td>
                            </tr>
                            ${partnerBaseAmount}
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
                                    <td class="p-1"><input type="text" class="fee-calc-input" value="${addedSharing?.fixed_markup}" id="${feeId}-fixed-markup-${levelIndex}" placeholder="Enter fixed value" oninput="updateFixedBaseCost(this)" data-fees-id="${feeId}" data-level-index="${levelIndex}"/></td>
                                    <td class="p-1"><span id="${feeId}-fixed-base-cost-${levelIndex}"></span></td>
                                </tr>
                                <tr>
                                    <td class="p-1">Percentage (%)</td>
                                    <td class="p-1"><input type="text" value="${addedSharing?.percentage_markup}" class="fee-calc-input" id="${feeId}-percentage-markup-${levelIndex}" placeholder="Enter percentage value" data-fees-id="${feeId}"  oninput="updatePercentageBaseCost(this)" data-level-index="${levelIndex}"/></td>
                                    <td class="p-1"><span id="${feeId}-percentage-base-cost-${levelIndex}"></span></td>
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
                            let totalSharing = 0;
                            const sharingTotal = addedSharing?.partner_fee_sharing?.map((p) => {
                                totalSharing += p.sharing
                                return totalSharing                                
                            })
                            console.log(addedSharing, "addedSharing");
                            
                            for (const [partnerIndex, partner] of selectedPartners.entries()) {
                                const partnerSharing = addedSharing?.partner_fee_sharing[partnerIndex];
                                
                                sharingLevel += `
                                <tr>
                                    <td class="p-1">${partner.first_name} ${partner.last_name}</td>
                                    <td class="p-1">
                                        <input type="text" class="fee-calc-input" value="${partnerSharing?.sharing}"  placeholder="Add partner share" oninput="partnerValueUpdate(this)" data-fees-id="${feeId}" id="${feeId}-partner${partner.id}-sharing-${levelIndex}" data-level-index="${levelIndex}" data-partner-index="${partner.id}">
                                    </td>
                                    <td class="p-1"><span id="${feeId}-partner${partner.id}-fixed-share-${levelIndex}">0.00</span></td>
                                    <td class="p-1"><span id="${feeId}-partner${partner.id}-percentage-share-${levelIndex}">0.00</span></td>
                                </tr>`;
                            }
                            sharingLevel += `
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="p-1">Total</th>
                                    <th class="p-1" <span id="${feeId}-partner-sharing-total-${levelIndex}"></th>
                                    <th class="p-1">$ <span id="${feeId}-partner-fixed-total-${levelIndex}">0.00</span></th>
                                    <th class="p-1">% <span id="${feeId}-partner-percentage-total-${levelIndex}">0.00</span></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>`;
                indexLevel++;
            }            
            $(".fee-calc-input").trigger('input');
            $(`.${feeId}-sharing-level-container`).html(sharingLevel);
            levelCount++;
        })
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

        const levels = $(`#${feesId}-levels`).val().trim();
        
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

        const levels = $(`#${feesId}-levels`).val().trim();
        
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
            if($(`#${feesId}-partner${partner.id}-sharing-${levelIndex}`).length > 0 && $(`#${feesId}-partner${partner.id}-fixed-share-${levelIndex}`).length > 0
            && $(`#${feesId}-partner${partner.id}-percentage-share-${levelIndex}`).length > 0){
                partnerSharingTotal += parseFloat($(`#${feesId}-partner${partner.id}-sharing-${levelIndex}`).val()) || 0;
                partnerFixedTotal += parseFloat($(`#${feesId}-partner${partner.id}-fixed-share-${levelIndex}`).text());
                partnerPercentageTotal += parseFloat($(`#${feesId}-partner${partner.id}-percentage-share-${levelIndex}`).text());
            }
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
        if(Array.isArray(selector.val())){     
            if (selector.val().length === 0) {
                selector.addClass('invalid-input');
                isValid = false;
            } else {
                selector.removeClass('invalid-input');
                isValid = true;
            }
        }else{
            if(selector){
                if (!selector.val()) {
                    selector.addClass('invalid-input');
                    isValid = false;
                } else {
                    selector.removeClass('invalid-input');
                    isValid = true;
                }
            }else{
                selector.addClass('invalid-input');
                isValid = false;
            }
        } 
            
    }

    $('#saveForm').click(async function() {
        let data = [];
        const merchant = "{{@$fee->merchant_id}}";
        const programSelected = "{{@$fee->program_id}}";
        let feeConfig = {
            merchant_id: merchant,
            programSelected: programSelected
        };
        for (const fee of selectedFeesCatalogs) {
            
            const feeName = $(`#${fee.fees_id}-fee_name`).val().trim();
            // const topUpAmount = $(`#${fee.fees_id}-top_up_amount`).val().trim();
            const levels = $(`#${fee.fees_id}-levels`).val().trim();
            const partnerIds = $(`#${fee.fees_id}-partners`).val();
            const minimum = $(`#${fee.fees_id}-minimum`).val().trim();
            const maximum = $(`#${fee.fees_id}-maximum`).val().trim();
            const fixedFee = $(`#${fee.fees_id}-fixedFee`).val().trim();
            const percentageFee = $(`#${fee.fees_id}-percentageFee`).val().trim();
            const totalFee = $(`#${fee.fees_id}-totalFee`).val().trim();
            const fee_type = $(`#${fee.fees_id}-transaction_category`).val().trim();
            const payer = $(`#${fee.fees_id}-payer`).val().trim();
            const sender_pay = $(`#${fee.fees_id}-sender_pay`).val().trim();
            const receiver_pay = $(`#${fee.fees_id}-receiver_pay`).val().trim();
            const charge_type = $(`#${fee.fees_id}-charge_type`).val().trim();
            const base_cost_partner = $(`#${fee.fees_id}-base-cost-partner`).val();
            // const selectedPartners = await getPartnersName(partnerIds);
            const selectedPartners = partnerIds;
            let levels_data = [];
            validateField($(`#${fee.fees_id}-fee_name`));
            validateField($(`#${fee.fees_id}-levels`));
            validateField($(`#${fee.fees_id}-partners`));
            validateField($('#merchant'));
            validateField($(`#${fee.fees_id}-minimum`));
            validateField($(`#${fee.fees_id}-maximum`));
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
                for (const partner of selectedPartners) {
                    levelData.partners.push({
                        partner_id: partner,
                        sharing: parseFloat($(`#${fee.fees_id}-partner${partner}-sharing-${levelIndex}`).val()),
                        fixed_share: parseFloat($(`#${fee.fees_id}-partner${partner}-fixed-share-${levelIndex}`).text()),
                        percentage_share: parseFloat($(`#${fee.fees_id}-partner${partner}-percentage-share-${levelIndex}`).text())
                    });
                    validateField($(`#${fee.fees_id}-partner${partner}-sharing-${levelIndex}`));
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
                levels_data: levels_data,
                base_cost_partner: base_cost_partner,
            });
        }
        isValid = data.map((d) => {
            if(!d.levels || d.levels_data.length === 0 || d.partners.length === 0){
                return false;
            }else{
                return true
            }
        })
        if (!isValid) {
            toastr.error('Please fill all required fields!')
            return;
        }
        console.log(data, "=======");
        $.ajax({
            type: "PUT",
            url: `{{ url("admin/fee") }}/${feeConfigId}`,
            data: {
                detail: data,
                feeConfig: feeConfig
            },
            beforeSend: function() {
                $("#saveForm").prop("disabled", true);
                $("#saveForm .btn-loader").show();
            },
            complete: function() {
                $("#saveForm").prop("disabled", false);
                $("#saveForm .btn-loader").hide();
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

    $(".merchant-select").on("change", function(){
        const merchantId = $(this).val()
        $.ajax({
            type: "GET",
            url: `{{url('admin/merchants')}}/${merchantId}/programs`,
            success:function(response){
                $("#programs-select").empty().append('<option value=""></option>');
                if(response.success){
                    const programs = response.data

                    programs.forEach(program => {
                        $("#programs-select").append($("<option></option>")
                        .attr("value", program.id)
                        .text(program.name));                         
                    });
                    // $("#programs-select").trigger('change');
                }else{
                    toastr.error(response.data)
                }
            },
            error:function(response){
                toastr.error('Something went wrong!')
            }
        })
    })

    $(".programs-select").on("change", function(){
        const programId = $(this).val()
        programChange(programId)
    })

    function programChange(programId){
        $('.fee-management-module').removeClass('d-none');
        $('.fee-management-module .tabs-container ul').empty();  
        $.ajax({
            type: "GET",
            url: `{{url('admin/programs')}}/${programId}/fee-catalog`,
            success:function(response){
                if(!response.success){
                    toastr.error(response.data)
                }
                $(".fee-catalog-container").html("")
                let feeCatalogs = ''
                let feeCatalogTabs = ``;
                let feeCatalogContent = ``;
                selectedFeesCatalogs = response.data;
                for (const [index, fee] of response.data.entries()) {
                    feeCatalogTabs += `<li class="${index === 0? 'active': ''}"><a href="#${fee.fees_id}" class="fees_tabs" data-toggle="tab">${fee.name}</a></li>`;
                    let partnerOption = ``;
                    for (const partner of partners) {
                        const selected = selected_partners[index].includes(partner.id)? 'selected': '';
                        partnerOption += `<option value=${partner.id} ${selected}>${partner.first_name} ${partner.last_name}</option>`
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
                                        <input type="text" class="form-control" value=${addedFeeInfo[index].fixed_fee} id="${fee.fees_id}-fixedFee" placeholder="Fixed fee"  disabled="" required="">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12">
                                    <label for="${fee.fees_id}-percentageFee">
                                        % Fee &nbsp;<i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="" data-original-title="The calculated percentage fee will be displayed here."></i>
                                    </label>
                                    <input type="text" class="form-control" id="${fee.fees_id}-percentageFee" placeholder="Percentage fee" value=${addedFeeInfo[index].percentage_fee} disabled="" required="">
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12">
                                    <label for="${fee.fees_id}-totalFee">
                                        Total Fee 
                                    </label>
                                    <input type="text" class="form-control" value=${addedFeeInfo[index].total_fee} id="${fee.fees_id}-totalFee" placeholder="Total fee" value="0" disabled="" required="">
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="${fee.fees_id}-levels">How many levels</label>
                                        <input type="text" class="form-control" readonly value=${addedFeeInfo[index].levels} id="${fee.fees_id}-levels" placeholder="Enter Levels"  required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="${fee.fees_id}-partner">Partners</label>
                                        <select class="form-control select2" readonly id="${fee.fees_id}-partners" multiple>
                                            ${partnerOption}
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="${fee.fees_id}-minimum">Minimum</label>
                                        <input type="text" class="form-control"  value=${addedFeeInfo[index].minimum} id="${fee.fees_id}-minimum" placeholder="Enter minimum fee"  required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="${fee.fees_id}-maximum">Maximum</label>
                                        <input type="text" class="form-control" value=${addedFeeInfo[index].maximum} id="${fee.fees_id}-maximum" placeholder="Enter maximum fee"  required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button type="button" data-fee-id=${fee.fees_id} class="btn btn-primary generate-btn">Generate</button>
                                </div>
                            </div>
                            <div class="row ${fee.fees_id}-sharing-level-container">
                            </div>
                        </div>`
                }
                $('.fee-management-module .tabs-container ul').html(feeCatalogTabs);  
                $('.fees-catalog-container').html(feeCatalogContent);
                $('.select2').select2()
                generateFee();
                $(".generate-btn").trigger("click")
                // levelCount = 0;
                $('.fee-management-module ul li a').on("click", function (e) {
                    e.preventDefault();
                    $(".fee-management-module ul li").removeClass('active');
                    const tabId = $(this).attr('href');
                    $(this).parent().addClass('active');
                    $('.fees-catalog-container .tab-pane').removeClass('active')
                    $(tabId).tab('show');
                    $(".fee-calc-input").trigger('input'); // Trigger input event when tab becomes visible
                });
            },
            error:function(response){
                toastr.error('Something went wrong!')
            }
        })
    }

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
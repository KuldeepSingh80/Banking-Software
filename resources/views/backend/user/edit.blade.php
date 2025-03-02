@extends('layouts.app')

@section('content')
<div class="sb-page-header pb-10 sb-page-header-dark bg-gradient-primary-to-secondary">
	<div class="container-fluid">
		<div class="sb-page-header-content py-5">
			<h1 class="sb-page-header-title">
				<div class="sb-page-header-icon"><i data-feather="user"></i></div>
				<span>{{ _lang('Update User Information') }}</span>
			</h1>
		</div>
	</div>
</div>

<div class="container-fluid mt-n10">
	<div class="row">
		<div class="col-12">
			<div class="card">		
				<div class="card-body">
					<h4 class="card-title panel-title">{{ _lang('Update User Information') }}</h4>

					<form method="post" class="validate" autocomplete="off" action="{{ action('UserController@update', $id) }}" enctype="multipart/form-data">
						<div class="row">
							<div class="col-md-6">
								{{ csrf_field() }}
								<input name="_method" type="hidden" value="PATCH">	
							    <div class="row">
							    	<div class="col-md-12">
										<div class="form-group">
											<label class="control-label">{{ _lang('Account Type') }}</label>						
											<select class="form-control" id="account_type" name="account_type" required>
												<option value="personal">{{ _lang('Personal') }}</option>
												<option value="business">{{ _lang('Business') }}</option>
											</select>
										</div>
									</div>

									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label">{{ _lang('First Name') }}</label>						
											<input type="text" class="form-control" name="first_name" value="{{ $user->first_name }}" required>
										</div>
									</div>
									
									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label">{{ _lang('Last Name') }}</label>						
											<input type="text" class="form-control" name="last_name" value="{{ $user->last_name }}" required>
										</div>
									</div>
									
									<div class="col-md-6{{ $user->account_type == 'business' ? '' : ' d-none' }}" id="business_name">
										<div class="form-group">
											<label class="control-label">{{ _lang('Business Name') }}</label>						
											<input type="text" class="form-control" name="business_name" value="{{ $user->business_name }}">
										</div>
									</div>

									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label">{{ _lang('Email') }}</label>						
											<input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
										</div>
									</div>
									
									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label">{{ _lang('Phone') }}</label>						
											<input type="tel" class="form-control telephone" name="phone" value="{{ $user->phone }}" required>
										</div>
									</div>

									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label">{{ _lang('Password') }}</label>						
											<input type="password" class="form-control" name="password" value="">
										</div>
									</div>
									
									<div class="col-md-6">
										<div class="form-group">
										<label class="control-label">{{ _lang('Confirm Password') }}</label>						
										<input type="password" class="form-control" name="password_confirmation">
										</div>
									</div>

									<div class="col-md-6">
									  <div class="form-group">
										<label class="control-label">{{ _lang('Date Of Birth') }}</label>						
										<input type="text" class="form-control datepicker" name="date_of_birth" value="{{ $user->user_information->date_of_birth }}">
									  </div>
									</div>

									<div class="col-md-6">
									  <div class="form-group">
										<label class="control-label">{{ _lang('Passport') }}</label>						
										<input type="text" class="form-control" name="passport" value="{{ $user->user_information->passport }}">
									  </div>
									</div>

									<div class="col-md-6">
									  <div class="form-group">
										<label class="control-label">{{ _lang('Country Of Residence') }}</label>						
										<select class="form-control" name="country_of_residence">
											<option value="">{{ _lang('Country Of Residence') }}</option>
							                {{ get_country_list($user->user_information->country_of_residence) }}
										</select>
									  </div>
									</div>

									<div class="col-md-6">
									  <div class="form-group">
										<label class="control-label">{{ _lang('Country Of Citizenship') }}</label>						
										<select class="form-control" name="country_of_citizenship">
											<option value="">{{ _lang('Country Of Citizenship') }}</option>
							                {{ get_country_list($user->user_information->country_of_citizenship) }}
										</select>
									  </div>
									</div>


									<div class="col-md-12">
									  <div class="form-group">
										<label class="control-label">{{ _lang('Address') }}</label>						
										<textarea class="form-control" name="address">{{ $user->user_information->address }}</textarea>
									  </div>
									</div>

									<div class="col-md-12">
										<div class="form-group">
											<button type="submit" class="btn btn-primary">{{ _lang('Update') }}</button>
										</div>
									</div>

								</div>
							</div>
							
							<div class="col-md-6">
								<div class="row">
									
                                    <div class="col-md-6">
										<div class="form-group">
											<label class="control-label">{{ _lang('Status') }}</label>						
											<select class="form-control" id="status" name="status" required>
												<option value="1">{{ _lang('Active') }}</option>
												<option value="0">{{ _lang('In-Active') }}</option>
											</select>
										</div>
									</div>
									
									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label">{{ _lang('Account Status') }}</label>						
											<select class="form-control" id="account_status" name="account_status" required>
												<option value="Unverified">{{ _lang('Unverified') }}</option>
												<option value="Verified">{{ _lang('Verified') }}</option>
											</select>
										</div>
									</div>

									<div class="col-md-12">
									  <div class="form-group">
										<label class="control-label">{{ _lang('City') }}</label>						
										<input type="text" class="form-control" name="city" value="{{ $user->user_information->city }}">
									  </div>
									</div>

									<div class="col-md-12">
									  <div class="form-group">
										<label class="control-label">{{ _lang('State') }}</label>						
										<input type="text" class="form-control" name="state" value="{{ $user->user_information->state }}">
									  </div>
									</div>

									<div class="col-md-12">
									  <div class="form-group">
										<label class="control-label">{{ _lang('Zip') }}</label>						
										<input type="text" class="form-control" name="zip" value="{{ $user->user_information->zip }}">
									  </div>
									</div>
								
								
									<div class="col-md-12">					 
										<div class="form-group">
											<label class="control-label">{{ _lang('Profile Picture') }} ( 300 X 300 {{ _lang('for better view') }} )</label>						
											<input type="file" class="dropify" name="profile_picture" data-allowed-file-extensions="png jpg jpeg PNG JPG JPEG" data-default-file="{{ $user->profile_picture != "" ? asset('public/uploads/profile/'.$user->profile_picture) : '' }}">
										</div>
									</div>	
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

@section('js-script')
<script>
$("#account_type").val("{{ $user->account_type }}");
$("#status").val("{{ $user->status }}");
$("#account_status").val("{{ $user->account_status }}");
$(document).on('change','#account_type',function(){
	if($(this).val() == 'business'){
		$("#business_name").removeClass('d-none');
	}else{
		$("#business_name").addClass('d-none');
	}
});
</script>
@endsection


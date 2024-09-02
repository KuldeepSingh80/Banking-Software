@extends('layouts.app')

@section('content')
<div class="sb-page-header pb-10 sb-page-header-dark bg-gradient-primary-to-secondary">
	<div class="container-fluid">
		<div class="sb-page-header-content py-5">
			<h1 class="sb-page-header-title">
				<div class="sb-page-header-icon"><i data-feather="user"></i></div>
				<span>{{ _lang('Edit Partner') }}</span>
			</h1>
		</div>
	</div>
</div>

<div class="container-fluid mt-n10">
	<div class="row">
		<div class="col-12">
		<div class="card">
			<div class="card-body">
			    <h4 class="card-title panel-title">{{ _lang('Edit Partner') }}</h4>
                <form method="post" class="validate" autocomplete="off" action="{{ action('PartnerController@update', $id) }}">
                    {{ csrf_field() }}
                    <input name="_method" type="hidden" value="PATCH">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('First Name') }}</label>						
                                <input type="text" class="form-control" name="first_name" value="{{ $partner->first_name }}" required>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Last Name') }}</label>						
                                <input type="text" class="form-control" name="last_name" value="{{ $partner->last_name }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Email') }}</label>						
                                <input type="text" class="form-control" name="email" value="{{ $partner->email }}" required>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Mobile') }}</label>						
                                <input type="text" class="form-control" name="mobile" value="{{ $partner->mobile }}" required>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Company name') }}</label>						
                                <input type="text" class="form-control" name="company_name" value="{{ $partner->company_name }}" required>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('City') }}</label>						
                                <input type="text" class="form-control" name="city" value="{{ $partner->city }}" required>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('State') }}</label>						
                                <input type="text" class="form-control" name="state" value="{{ $partner->state }}" required>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Zip') }}</label>						
                                <input type="text" class="form-control" name="zip_code" value="{{ $partner->zip_code }}" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">{{ _lang('Save') }}</button>
                                <button type="reset" class="btn btn-danger">{{ _lang('Reset') }}</button>	
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
@extends('layouts.app')

@section('content')
<div class="sb-page-header pb-10 sb-page-header-dark bg-gradient-primary-to-secondary">
	<div class="container-fluid">
		<div class="sb-page-header-content py-5">
			<h1 class="sb-page-header-title">
				<div class="sb-page-header-icon"><i data-feather="user"></i></div>
				<span>{{ _lang('Edit Merchant') }}</span>
			</h1>
		</div>
	</div>
</div>

<div class="container-fluid mt-n10">
	<div class="row">
		<div class="col-12">
		<div class="card">
			<div class="card-body">
			    <h4 class="card-title panel-title">{{ _lang('Edit Merchant') }}</h4>
                <form method="post" class="validate" autocomplete="off" action="{{ action('MerchantController@update', $id) }}">
                    {{ csrf_field() }}
                    <input name="_method" type="hidden" value="PATCH">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Name') }}</label>						
                                <input type="text" class="form-control" name="name" value="{{ $merchant->name }}" required>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Key') }}</label>						
                                <input type="text" class="form-control" name="key" value="{{ $merchant->key }}" required>
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
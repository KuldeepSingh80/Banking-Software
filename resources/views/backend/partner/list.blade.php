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
					<h4 class="card-title"><span class="panel-title">{{ $title }}</span>
						<a class="btn btn-primary btn-sm float-right" href="{{ route('partners.create') }}">{{ _lang('Add New') }}</a>
					</h4>

					<table class="table table-bordered data-table">
						<thead>
						<tr>
							<th>{{ _lang('First Name') }}</th>
							<th>{{ _lang('Last Name') }}</th>
							<th>{{ _lang('Email') }}</th>
							<th>{{ _lang('Mobile') }}</th>
							<th>{{ _lang('Company Name') }}</th>
							<th>{{ _lang('City') }}</th>
							<th>{{ _lang('State') }}</th>
							<th>{{ _lang('Zip code') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
						</tr>
						</thead>
						<tbody>
						
						@foreach($partners as $partner)
							<tr id="row_{{ $partner->id }}">
								<td class='first_name'>{{ $partner->first_name }}</td>
								<td class='last_name'>{{ $partner->last_name }}</td>
								<td class='email'>{{ $partner->email }}</td>									
								<td class='mobile'>{{ $partner->mobile }}</td>									
								<td class='company_name'>{{ $partner->company_name }}</td>									
								<td class='city'>{{ $partner->city }}</td>									
								<td class='state'>{{ $partner->state }}</td>									
								<td class='zip_code'>{{ $partner->zip_code }}</td>									
								<td class="text-center">
									<div class="dropdown">
									<button class="btn btn-primary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									{{ _lang('Action') }}
									</button>
									<form action="{{ action('PartnerController@destroy', $partner->id) }}" method="post">
										{{ csrf_field() }}
										<input name="_method" type="hidden" value="DELETE">
										
										<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
											<a href="{{ action('PartnerController@edit', $partner->id) }}" class="dropdown-item dropdown-edit"><i class="mdi mdi-pencil"></i> {{ _lang('Edit') }}</a>
											<button data-href="{{ action('PartnerController@show', $partner->id) }}" data-title="{{ _lang('View Partner') }}" class="dropdown-item dropdown-view ajax-modal"><i class="mdi mdi-eye"></i> {{ _lang('View') }}</button>
											<button class="btn-remove dropdown-item" type="submit"><i class="mdi mdi-delete"></i> {{ _lang('Delete') }}</button>
										</div>
									</form>
									</div>
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection



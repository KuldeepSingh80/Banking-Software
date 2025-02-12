@extends('layouts.app')

@section('content')
<div class="sb-page-header pb-10 sb-page-header-dark bg-gradient-primary-to-secondary">
	<div class="container-fluid">
		<div class="sb-page-header-content py-5">
			<h1 class="sb-page-header-title">
				<div class="sb-page-header-icon"><i data-feather="pie-chart"></i></div>
				<span>{{ $title }}</span>
			</h1>
		</div>
	</div>
</div>


<div class="container-fluid mt-n10">
	<div class="row">
		<div class="col-12">
			<div class="card overflow-auto no-export">
				<div class="card-body">
					<h4 class="card-title"><span class="panel-title">{{ $title }}</span>
                        <a class="btn btn-primary btn-sm float-right" href="{{ route('programs.create') }}">{{ _lang('Add New') }}</a>
					</h4>
					<table class="table table-bordered data-table">
						<thead>
							<tr>
								<th>{{ _lang('Date') }}</th>
								<th>{{ _lang('Name') }}</th>
								<th class="text-center">{{ _lang('Action') }}</th>
							</tr>
						</thead>
						<tbody>
							@foreach($programs as $p)
							<tr id="row_{{ $p->id }}">
								<td class='created_at'>{{ $p->created_at }}</td>
								<td class='name'>{{ $p->name }}</td>
								<td class="text-center">
									<div class="dropdown">
										<button class="btn btn-primary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											{{ _lang('Action') }}
										</button>
										<form action="{{ action('ProgramController@destroy', $p['id']) }}" method="post">
											{{ csrf_field() }}
											<input name="_method" type="hidden" value="DELETE">
											<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <!-- <a href="#" class="dropdown-item dropdown-edit"><i class="mdi mdi-pencil"></i> {{ _lang('Edit') }}</a> -->
                                                <a href="{{ action('ProgramController@edit', $p->id) }}" class="dropdown-item dropdown-edit"><i class="mdi mdi-pencil"></i> {{ _lang('Edit') }}</a>
												<button data-href="{{ action('ProgramController@show', $p['id']) }}" data-title="{{ _lang('View Program') }}" class="dropdown-item dropdown-view ajax-modal"><i class="mdi mdi-eye"></i> {{ _lang('View') }}</button>
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

@section('js-script')

<script>
</script>

@endsection



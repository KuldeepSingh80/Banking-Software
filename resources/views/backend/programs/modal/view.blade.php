<div class="card">
	<div class="card-body p-2">
		<div class="main-content">
			<p>Program Name: {{$program->name}}</p>
			<p>Merchant Name: {{implode(', ', $program->merchants->pluck("name")->toArray())}}</p>
		</div>
	    <div class="fee-catalog-table table-responsive my-3">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>#</th>
						<th>Fees ID</th>
						<th>Fees Name</th>
						<th>Fees Description</th>
					</tr>
				</thead>
				<tbody>
					@foreach($program->feesCatalogs as $f)
					<tr>
						<td>{{ $loop->iteration }}</td>
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
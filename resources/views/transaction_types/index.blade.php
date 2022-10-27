@extends('layouts.app')
@section('content')

<section class="content">
    <div class="container-fluid">

        <div class="row">
            <section class="col-lg-12 connectedSortable">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-table mr-1"></i>
                            List
                        </h3>

                        @include('transaction_types.includes.toolbar')
                    </div>

                    <div class="card-body table-responsive">
                    	<table id="table" class="table table-hover">
                    		<thead>
                    			<tr>
                    				<th>ID</th>
                    				<th>Type</th>
                    				<th>Unit</th>
                    				<th>Demand Multiplier</th>
                    				<th>Rate</th>
                    				<th>Actions</th>
                    			</tr>
                    		</thead>

                    		<tbody>
                    		</tbody>
                    	</table>
                    </div>
                </div>
            </section>
        </div>
    </div>

</section>

@endsection

@push('styles')
	<link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
	<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
	{{-- <link rel="stylesheet" href="{{ asset('css/datatables-jquery.min.css') }}"> --}}
@endpush

@push('scripts')
	<script src="{{ asset('js/datatables.min.js') }}"></script>
	<script src="{{ asset('js/select2.min.js') }}"></script>
	{{-- <script src="{{ asset('js/datatables-jquery.min.js') }}"></script> --}}

	<script>
		$(document).ready(()=> {
			var table = $('#table').DataTable({
				ajax: {
					url: "{{ route('datatable.transactionType') }}",
                	dataType: "json",
                	dataSrc: "",
					data: {
						table: 'transaction_types',
						select: "*",
						where: ['admin_id', {{ auth()->user()->id }}]
					}
				},
				columns: [
					{data: 'id'},
					{data: 'type'},
					{data: 'operator'},
					{data: 'demand'},
					{data: 'rate'},
					{data: 'actions'},
				],
        		pageLength: 25,
        		columnDefs: [
        			{
        				targets: [3],
        				render: demand => {
        					return demand + "%";
        				}
        			},
        		],
				// drawCallback: function(){
				// 	init();
				// }
			});
		});

		function view(id){
			$.ajax({
				url: "{{ route('transactionType.get') }}",
				data: {
					select: '*',
					where: ['id', id],
				},
				success: rhu => {
					rhu = JSON.parse(rhu)[0];
					showDetails(rhu);
				}
			})
		}

		function create(){
			Swal.fire({
				html: `
	                ${input("type", "Type", null, 3, 9)}
	                ${input("operator", "Unit", null, 3, 9)}
	                ${input("demand", "Demand Multiplier", null, 3, 9)}
	                ${input("rate", "Rate", null, 3, 9)}
				`,
				width: '600px',
				confirmButtonText: 'Add',
				showCancelButton: true,
				cancelButtonColor: errorColor,
				cancelButtonText: 'Cancel',
				preConfirm: () => {
				    swal.showLoading();
				    return new Promise(resolve => {
				    	let bool = true;

			            if($('.swal2-container input:placeholder-shown').length){
			                Swal.showValidationMessage('Fill all fields');
			            }
			            else{
			            	let bool = false;
			            }

			            bool ? setTimeout(() => {resolve()}, 500) : "";
				    });
				},
			}).then(result => {
				if(result.value){
					swal.showLoading();
					$.ajax({
						url: "{{ route('transactionType.store') }}",
						type: "POST",
						data: {
							operator: $("[name='operator']").val(),
							type: $("[name='type']").val(),
							demand: $("[name='demand']").val(),
							rate: $("[name='rate']").val(),
							_token: $('meta[name="csrf-token"]').attr('content')
						},
						success: () => {
							ss("Success");
							reload();
						}
					})
				}
			});
		}

		function showDetails(transactionType){
			Swal.fire({
				html: `
	                ${input("id", "", transactionType.id, 3, 9, 'hidden')}
	                ${input("type", "Type", transactionType.type, 3, 9)}
	                ${input("operator", "Unit", transactionType.operator, 3, 9)}
	                ${input("demand", "Demand Multiplier", transactionType.demand, 3, 9)}
	                ${input("rate", "Rate", transactionType.rate, 3, 9)}
				`,
				width: '800px',
				confirmButtonText: 'Update',
				showCancelButton: true,
				cancelButtonColor: errorColor,
				cancelButtonText: 'Cancel',
				preConfirm: () => {
				    swal.showLoading();
				    return new Promise(resolve => {
				    	let bool = true;

			            if($('.swal2-container input:placeholder-shown').length){
			                Swal.showValidationMessage('Fill all fields');
			            }
			            else{
			            	let bool = false;
			            }

			            bool ? setTimeout(() => {resolve()}, 500) : "";
				    });
				},
			}).then(result => {
				if(result.value){
					swal.showLoading();
					update({
						url: "{{ route('transactionType.update') }}",
						data: {
							id: $("[name='id']").val(),
							type: $("[name='type']").val(),
							operator: $("[name='operator']").val(),
							demand: $("[name='demand']").val(),
							rate: $("[name='rate']").val(),
						},
						message: "Success"
					},	() => {
						reload();
					});
				}
				else if(result.isDenied){
					changePassword($("[name='id']").val());
				}
			});
		}

		function del(id){
			sc("Confirmation", "Are you sure you want to delete?", result => {
				if(result.value){
					swal.showLoading();
					update({
						url: "{{ route('transactionType.delete') }}",
						data: {id: id},
						message: "Success"
					}, () => {
						reload();
					})
				}
			});
		}
	</script>
@endpush
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
                    				<th>Operator</th>
                    				<th>Dashboard Visibility</th>
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
	{{-- <link rel="stylesheet" href="{{ asset('css/datatables-jquery.min.css') }}"> --}}
@endpush

@push('scripts')
	<script src="{{ asset('js/datatables.min.js') }}"></script>
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
					}
				},
				columns: [
					{data: 'id'},
					{data: 'type'},
					{data: 'operator'},
					{data: 'inDashboard'},
					{data: 'actions'},
				],
        		pageLength: 25,
        		columnDefs: [
        			{
        				targets: [2,3,4],
        				className: "center"
        			},
        			{
        				targets: 3,
        				render: (value, display, row) => {
        					let title = value ? "Turn off" : "Turn On";
        					let btn = value ? "danger" : "success";

        					return `
        						<a class="btn btn-${btn} btn-sm" data-toggle="tooltip" title="${title}" onclick="create()">
        						    <i class="fa-solid fa-power-off"></i>
        						</a>
        					`;
        				}
        			}
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
	                ${input("operator", "Operator", null, 3, 9)}
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
            				$.ajax({
            					url: "{{ route('transactionType.get') }}",
            					data: {
            						select: "id",
            						where: ["type", $("[name='type']").val()]
            					},
            					success: result => {
            						result = JSON.parse(result);
            						if(result.length){
            			    			Swal.showValidationMessage('Transaction Type Already Exists');
	            						setTimeout(() => {resolve()}, 500);
            						}
            					}
            				});
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
							type: $("[name='type']").val(),
							operator: $("[name='operator']").val(),
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
	                ${input("operator", "Type", transactionType.operator, 3, 9)}
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
            				$.ajax({
            					url: "{{ route('transactionType.get') }}",
            					data: {
            						select: "id",
            						where: ["type", $("[name='type']").val()]
            					},
            					success: result => {
            						result = JSON.parse(result);
            						if(result.length){
            			    			Swal.showValidationMessage('Transaction Type Already Exists');
	            						setTimeout(() => {resolve()}, 500);
            						}
            					}
            				});
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
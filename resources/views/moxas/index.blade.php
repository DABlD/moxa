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

                        @include('moxas.includes.toolbar')
                    </div>

                    <div class="card-body table-responsive">
                    	<table id="table" class="table table-hover">
                    		<thead>
                    			<tr>
                    				<th>ID</th>
                    				<th>Name</th>
                    				<th>Serial</th>
                    				<th>Location</th>
                    				<th>Floor</th>
                    				<th>Utility</th>
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
					url: "{{ route('datatable.moxa') }}",
                	dataType: "json",
                	dataSrc: "",
					data: {
						table: 'Moxa',
						select: "*",
						where: ['user_id', {{ auth()->user()->id }}]
					}
				},
				columns: [
					{data: 'id'},
					{data: 'serial'},
					{data: 'name'},
					{data: 'location'},
					{data: 'floor'},
					{data: 'utility'},
					{data: 'actions'},
				],
        		pageLength: 25,
				// drawCallback: function(){
				// 	init();
				// }
			});
		});

		function view(id){
			$.ajax({
				url: "{{ route('moxa.get') }}",
				data: {
					select: '*',
					where: ['id', id],
					load: ['user']
				},
				success: moxa => {
					moxa = JSON.parse(moxa)[0];
					showDetails(moxa);
				}
			})
		}

		function create(){
			Swal.fire({
				html: `
	                ${input("serial", "Serial", null, 3, 9)}
	                ${input("name", "Name", null, 3, 9)}
	                ${input("location", "Location", null, 3, 9)}
	                ${input("floor", "Floor", null, 3, 9)}
					<div class="row iRow">
					    <div class="col-md-3 iLabel">
					        Utility
					    </div>
					    <div class="col-md-9 iInput">
					        <select name="utility" class="form-control">
					        	<option value=""></option>
					        </select>
					    </div>
					</div>
				`,
				width: '800px',
				confirmButtonText: 'Add',
				showCancelButton: true,
				cancelButtonColor: errorColor,
				cancelButtonText: 'Cancel',
				didOpen: () => {
					$.ajax({
						url: "{{ route('transactionType.get') }}",
						data: {
							select: "*",
							where: ['admin_id', {{ auth()->user()->id }}]
						},
						success: moxas => {
							moxas = JSON.parse(moxas);
							moxaString = "";

							moxas.forEach(moxa => {
								moxaString += `
									<option value="${moxa.type}">${moxa.type}</option>
								`;
							});

							$("[name='utility']").append(moxaString);
							$("[name='utility']").select2({
								placeholder: "Select Utility"
							});
						}
					})
				},
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
						url: "{{ route('moxa.store') }}",
						type: "POST",
						data: {
							serial: $("[name='serial']").val(),
							name: $("[name='name']").val(),
							location: $("[name='location']").val(),
							floor: $("[name='floor']").val(),
							utility: $("[name='utility']").val(),
							_token: $('meta[name="csrf-token"]').attr('content')
						},
						success: () => {
							reload();
							ss("Success");
						}
					})
				}
			});
		}

		function showDetails(moxa){
			Swal.fire({
				html: `
	                ${input("id", "", moxa.user.id, 3, 9, 'hidden')}
	                ${input("serial", "Name", moxa.serial, 3, 9)}
	                ${input("name", "Name", moxa.name, 3, 9)}
	                ${input("location", "Location", moxa.location, 3, 9)}
	                ${input("floor", "Floor", moxa.floor, 3, 9)}
					<div class="row iRow">
					    <div class="col-md-3 iLabel">
					        Utility
					    </div>
					    <div class="col-md-9 iInput">
					        <select name="utility" class="form-control">
					        	<option value=""></option>
					        </select>
					    </div>
					</div>
				`,
				width: '800px',
				confirmButtonText: 'Update',
				showCancelButton: true,
				cancelButtonColor: errorColor,
				cancelButtonText: 'Cancel',
				didOpen: () => {
					$.ajax({
						url: "{{ route('transactionType.get') }}",
						data: {
							select: "*",
							where: ['admin_id', {{ auth()->user()->id }}]
						},
						success: moxas => {
							moxas = JSON.parse(moxas);
							moxaString = "";

							moxas.forEach(moxa => {
								moxaString += `
									<option value="${moxa.type}">${moxa.type}</option>
								`;
							});

							$("[name='utility']").append(moxaString);
							$("[name='utility']").select2({
								placeholder: "Select Utility"
							});

							$("[name='utility']").val(moxa.utility).trigger('change');
						}
					})
				},
                // showDenyButton: true,
                // denyButtonColor: successColor,
                // denyButtonText: 'Change Password',
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
					
					let serial = $("[name='serial']").val();
					let name = $("[name='name']").val();
					let location = $("[name='location']").val();
					let floor = $("[name='floor']").val();
					let utility = $("[name='utility']").val();
					let id = $("[name='id']").val();
					
					update({
						url: "{{ route('moxa.update') }}",
						data: {
							id: id,
							serial: $("[name='serial']").val(),
							name: $("[name='name']").val(),
							location: $("[name='location']").val(),
							floor: $("[name='floor']").val(),
							utility: $("[name='utility']").val(),
						},
						message: false
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
						url: "{{ route('moxa.delete') }}",
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
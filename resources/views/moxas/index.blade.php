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
                    				<th>Area</th>
                    				<th>Subscriber</th>
                    				<th>Serial</th>
                    				<th>Location</th>
                    				<th>Unit Number</th>
                    				<th>Utility</th>
                    				<th>In Dashboard</th>
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
						table: 'Device',
						select: "devices.*",
						load: ['building', 'subscriber']
					}
				},
				columns: [
					{data: 'id'},
					{data: 'building.name', visible: false},
					{data: 'subscriber.name'},
					{data: 'serial'},
					{data: 'location'},
					{data: 'floor'},
					{data: 'utility'},
					{data: 'inDashboard'},
					{data: 'actions'},
				],
				columnDefs: [
        			{
        				targets: 7,
        				render: (value, display, row) => {
        					let btn = value ? "success" : "danger";
        					let slash = value ? "" : "-slash";
        					return `
        						<a class="btn btn-${btn} btn-sm" data-toggle="tooltip" title="Toggle" onclick="updateVisibility(${row.id},${value})">
        						    <i class="fa-solid fa-eye${slash}"></i>
        						</a>
        					`;
        				}
        			}
				],
        		pageLength: 25,
        		order: [[1, 'asc']],
        		rowCallback: function( row, data, index ) {
				    if (data['id'] == null) {
				        $(row).hide();
				    }
				},
		        drawCallback: function (settings) {
		            let api = this.api();
		            let rows = api.rows({ page: 'current' }).nodes();
		            let last = null;
		 
		            api.column(1, { page: 'current' })
		                .data()
		                .each(function (building, i) {
		                    if (last !== building) {
		                        $(rows)
		                            .eq(i)
		                            .before(`
		                            	<tr class="group">
		                            		<td colspan="8">
		                            			${building}
		                            		</td>
		                            	</tr>
		                            `);
		 
		                        last = building;
		                    }
		                });
				}
				// drawCallback: function(){
				// 	init();
				// }
			});
		});

		function userDetails(id){
			$.ajax({
				url: "{{ route('user.get') }}",
				data: {
					select: '*',
					where: ['id', id]
				},
				success: user => {
					user = JSON.parse(user)[0];

					console.log(user);

					Swal.fire({
						title: 'Subscriber Details',
						html: `
			                ${input("name", "Name", user.name, 3, 9, 'text', 'disabled')}
			                ${input("email", "Email", user.email, 3, 9, 'text', 'disabled')}
			                ${input("address", "Address", user.address, 3, 9, 'text', 'disabled')}
			                ${input("contact", "Contact", user.contact, 3, 9, 'text', 'disabled')}
						`,
						width: '800px',
						confirmButtonText: 'Exit',
					});
				}
			})
		}

		function view(id){
			$.ajax({
				url: "{{ route('device.get') }}",
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
					<div class="row iRow">
					    <div class="col-md-3 iLabel">
					        Subscriber
					    </div>
					    <div class="col-md-9 iInput">
					        <select name="name" class="form-control">
					        	<option value=""></option>
					        </select>
					    </div>
					</div>
					<div class="row iRow">
					    <div class="col-md-3 iLabel">
					        Area
					    </div>
					    <div class="col-md-9 iInput">
					        <select name="category_id" class="form-control">
					        	<option value=""></option>
					        </select>
					    </div>
					</div>
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
	                ${input("serial", "Serial", null, 3, 9)}
	                ${input("location", "Location", null, 3, 9)}
	                ${input("floor", "Unit #", null, 3, 9)}
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
						success: utys => {
							utys = JSON.parse(utys);
							utyString = "";

							utys.forEach(uty => {
								utyString += `
									<option value="${uty.type}">${uty.type}</option>
								`;
							});

							$("[name='utility']").append(utyString);
							$("[name='utility']").select2({
								placeholder: "Select Utility",
							});
						}
					})

					$.ajax({
						url: "{{ route('building.getCategories') }}",
						data: {
							select: "*",
							where: ['admin_id', {{ auth()->user()->id }}]
						},
						success: buildings => {
							buildings = JSON.parse(buildings);
							buildingString = "";

							buildings.forEach(building => {
								buildingString += `
									<option value="${building.id}">${building.name}</option>
								`;
							});

							$("[name='category_id']").append(buildingString);
							$("[name='category_id']").select2({
								placeholder: "Select Area"
							});
						}
					})

					$.ajax({
						url: "{{ route('user.get') }}",
						data: {
							select: "*",
							where: ['role', 'Subscriber']
						},
						success: users => {
							users = JSON.parse(users);
							userString = "";

							users.forEach(user => {
								userString += `
									<option value="${user.id}">${user.name}</option>
								`;
							});

							$("[name='name']").append(userString);
							$("[name='name']").select2({
								placeholder: "Select Subscriber"
							});
						}
					})
				},
				preConfirm: () => {
				    swal.showLoading();
				    return new Promise(resolve => {
				    	let bool = true;

			            if($('.swal2-container input:placeholder-shown').length || $('.select2-selection__placeholder').length){
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
						url: "{{ route('device.store') }}",
						type: "POST",
						data: {
							category_id: $("[name='category_id']").val(),
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
					<div class="row iRow">
					    <div class="col-md-3 iLabel">
					        Subscriber
					    </div>
					    <div class="col-md-9 iInput">
					        <select name="name" class="form-control">
					        	<option value=""></option>
					        </select>
					    </div>
					</div>
					<div class="row iRow">
					    <div class="col-md-3 iLabel">
					        Area
					    </div>
					    <div class="col-md-9 iInput">
					        <select name="category_id" class="form-control">
					        	<option value=""></option>
					        </select>
					    </div>
					</div>
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
	                ${input("id", "", moxa.user.id, 3, 9, 'hidden')}
	                ${input("serial", "Serial #", moxa.serial, 3, 9)}
	                ${input("location", "Location", moxa.location, 3, 9)}
	                ${input("floor", "Unit #", moxa.floor, 3, 9)}

	                <br>
	                <br>

					<div class="row iRow">
					    <div class="col-md-3 iLabel">
					        Current Reading
					    </div>
					    <div class="col-md-9 iInput" id="curReading" style="font-weight: bold;">
					        N/A
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

					$.ajax({
						url: "{{ route('building.getCategories') }}",
						data: {
							select: "*",
							where: ['admin_id', {{ auth()->user()->id }}]
						},
						success: buildings => {
							buildings = JSON.parse(buildings);
							buildingString = "";

							buildings.forEach(building => {
								buildingString += `
									<option value="${building.id}">${building.name}</option>
								`;
							});

							$("[name='category_id']").append(buildingString);
							$("[name='category_id']").select2({
								placeholder: "Select Area"
							});

							$("[name='category_id']").val(moxa.category_id).trigger('change');
						}
					})

					$.ajax({
						url: "{{ route('user.get') }}",
						data: {
							select: "*",
							where: ['role', 'Subscriber']
						},
						success: users => {
							users = JSON.parse(users);
							userString = "";

							users.forEach(user => {
								userString += `
									<option value="${user.id}">${user.name}</option>
								`;
							});

							$("[name='name']").append(userString);
							$("[name='name']").select2({
								placeholder: "Select Subscriber"
							});

							$("[name='name']").val(moxa.name).trigger('change');
						}
					})

					$.ajax({
						url: '{{ route('reading.get') }}',
						data: {
							select: '*',
							where: ['moxa_id', moxa.id],
							order: ['datetime', 'desc']
						},
						success: result => {
							result = JSON.parse(result);

							if(result.length){
								result = result[0].total;
							}
							else{
								result = 0;
							}

							$('#curReading').html(result);
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

			            if($('.swal2-container input:placeholder-shown').length || $('.select2-selection__placeholder').length){
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
						url: "{{ route('device.update') }}",
						data: {
							id: moxa.id,
							category_id: $("[name='category_id']").val(),
							serial: $("[name='serial']").val(),
							name: $("[name='name']").val(),
							location: $("[name='location']").val(),
							floor: $("[name='floor']").val(),
							utility: $("[name='utility']").val(),
						},
						message: "Success"
					}, () => {
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
						url: "{{ route('device.delete') }}",
						data: {id: id},
						message: "Success"
					}, () => {
						reload();
					})
				}
			});
		}

		function updateVisibility(id, inDashboard){
			swal.showLoading();
			update({
				url: "{{ route('device.update') }}",
				data: {
					id: id,
					inDashboard: inDashboard ? 0 : 1
				},
				message: "Success"
			},	() => {
				reload();
			});
		}
	</script>
@endpush
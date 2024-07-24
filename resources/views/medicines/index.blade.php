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

                        @if(auth()->user()->role == "Admin")
                        	@include('medicines.includes.toolbar')
                        @endif
                    </div>

                    <div class="card-body table-responsive">
                    	<table id="table" class="table table-hover">
                    		<thead>
                    			<tr>
                    				<th>ID</th>
                    				<th>Site</th>
                    				<th>Name</th>
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
@endpush

@push('scripts')
	<script src="{{ asset('js/datatables.min.js') }}"></script>
	<script src="{{ asset('js/select2.min.js') }}"></script>

	<script>
		$('.m-0, .breadcrumb-item.active').html('Areas');
		var site_id = "%%";

		$(document).ready(()=> {
			var table = $('#table').DataTable({
				ajax: {
					url: "{{ route('datatable.category') }}",
                	dataType: "json",
                	dataSrc:'',
					data: f => {
						f.select = ["buildings.*"];
						f.load = ['site'];
						f.site_id = site_id;
					}
				},
				columns: [
					{data: 'id'},
					{data: 'site.name', visible: false},
					{data: 'name'},
					{data: 'actions'}
				],
        		order: [[1, 'asc']],
        		pageLength: 25,
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
		                .each(function (medicine, i) {
		                    if (last !== medicine) {
		                        $(rows)
		                            .eq(i)
		                            .before(`
		                            	<tr class="group">
		                            		<td colspan="3">
		                            			${medicine}
		                            		</td>
		                            	</tr>
		                            `);
		 
		                        last = medicine;
		                    }
		                });
				}
			});

			$.ajax({
				url: '{{ route('site.get') }}',
				data: {
					select: ['id', 'name'],
					where: ['admin_id', {{ auth()->user()->id }}]
				},
				success: sites => {
					sites = JSON.parse(sites);

					siteString = "";
					sites.forEach(site => {
						siteString += `
							<option value="${site.id}">${site.name}</option>
						`;
					});

					$('#user_id').append(siteString);
					$('#user_id').select2();

					$('#user_id').on('change', e => {
						site_id = e.target.value;
						reload();
					});
				}
			})
		});

		function create(selectedCategory = null){
			Swal.fire({
				html: `
	                ${input("name", "Name", null, 3, 9)}
	                ${input("serial", "Serial", null, 3, 9)}
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
						url: "{{ route('device.store') }}",
						type: "POST",
						data: {
							category: selectedCategory,
							name: $("[name='name']").val(),
							serial: $("[name='serial']").val(),
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

		function createCategory(){
			Swal.fire({
				html: `
					<div class="row iRow">
					    <div class="col-md-3 iLabel">
					        Site
					    </div>
					    <div class="col-md-9 iInput">
					        <select name="site_id" class="form-control">
					        	<option value=""></option>
					        </select>
					    </div>
					</div>
	                ${input("name", "Name", null, 3, 9)}
				`,
				width: '400px',
				confirmButtonText: 'Add',
				showCancelButton: true,
				cancelButtonColor: errorColor,
				cancelButtonText: 'Cancel',
				didOpen: () => {
					$.ajax({
						url: "{{ route('site.get') }}",
						data: {
							select: "*",
						},
						success: sites => {
							sites = JSON.parse(sites);
							siteString = "";

							sites.forEach(site => {
								siteString += `
									<option value="${site.id}">${site.name}</option>
								`;
							});

							$("[name='site_id']").append(siteString);
							$("[name='site_id']").select2({
								placeholder: "Select Site"
							});
						}
					})
				},
				preConfirm: () => {
				    swal.showLoading();
				    return new Promise(resolve => {
				    	let bool = true;
			            if($('.swal2-container input:placeholder-shown').length || $("[name='site_id']").val() == ""){
			                Swal.showValidationMessage('Fill all fields');
			            }
			            else{
			            	let bool = false;

				            setTimeout(() => {resolve()}, 500);
			            }

			            bool ? setTimeout(() => {resolve()}, 500) : "";
				    });
				},
			}).then(result => {
				if(result.value){
					$.ajax({
						url: "{{ route('building.storeCategory') }}",
						type: "POST",
						data: {
							site_id: $("[name='site_id']").val(),
							name: $("[name='name']").val(),
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

		function editCategory(category){
			Swal.fire({
				html: `
	                ${input("name", "Name", category, 3, 9)}
				`,
				width: '400px',
				confirmButtonText: 'Update',
				showCancelButton: true,
				cancelButtonColor: errorColor,
				cancelButtonText: 'Cancel',
				preConfirm: () => {
				    swal.showLoading();
				    return new Promise(resolve => {
				    	let bool = true;
			            if($('.swal2-container input:placeholder-shown').length || $("[name='rhu_id']").val() == ""){
			                Swal.showValidationMessage('Fill all fields');
			            }
			            else{
			            	let bool = false;

				            setTimeout(() => {resolve()}, 500);
			            }

			            bool ? setTimeout(() => {resolve()}, 500) : "";
				    });
				},
			}).then(result => {
				if(result.value){
					$.ajax({
						url: "{{ route('building.updateCategory') }}",
						type: "POST",
						data: {
							where: ["name", category],
							name: $("[name='name']").val(),
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

		function view(id){
			$.ajax({
				url: "{{ route('device.get') }}",
				data: {
					select: '*',
					where: ['id', id],
					load: ["category"]
				},
				success: medicine => {
					medicine = JSON.parse(medicine)[0];
					showDetails(medicine);
				}
			})
		}

		function showDetails(moxa){
			Swal.fire({
				html: `
	                ${input("id", "", moxa.id, 3, 9, 'hidden')}
	                ${input("name", "Name", moxa.name, 3, 9)}
	                ${input("serial", "Serial", moxa.serial, 3, 9)}
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
						url: "{{ route('device.update') }}",
						data: {
							id: moxa.id,
							name: $("[name='name']").val(),
							serial: $("[name='serial']").val(),
							location: $("[name='location']").val(),
							floor: $("[name='floor']").val(),
							utility: $("[name='utility']").val(),
						},
						message: "Success"
					}, () => {
						reload();
					});
				}
			});
		}

		function del(id){
			sc("Confirmation", "Are you sure you want to delete?", result => {
				if(result.value){
					swal.showLoading();
					update({
						url: "{{ route('building.deleteCategory') }}",
						data: {id: id},
						message: "Success"
					}, () => {
						reload();
					});
				}
			});
		}
	</script>
@endpush
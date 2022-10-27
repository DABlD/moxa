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

                        @include('sites.includes.toolbar')
                    </div>

                    <div class="card-body table-responsive">
                    	<table id="table" class="table table-hover">
                    		<thead>
                    			<tr>
                    				<th>ID</th>
                    				<th>Name</th>
                    				<th>Site Location</th>
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
					url: "{{ route('datatable.site') }}",
                	dataType: "json",
                	dataSrc: "",
					data: {
						table: 'sites',
						select: "*",
					}
				},
				columns: [
					{data: 'id'},
					{data: 'name'},
					{data: 'site_location'},
					{data: 'actions'}
				],
        		pageLength: 25,
				// drawCallback: function(){
				// 	init();
				// }
			});
		});

		function create(){
			Swal.fire({
				html: `
	                ${input("name", "Name", null, 3, 9)}
	                ${input("site_location", "Site Location", null, 3, 9)}
	                </br>
	                ${input("username", "Username", null, 3, 9, 'text', 'autocomplete="new-password"')}
                    ${input("password", "Password", null, 3, 9, 'password', 'autocomplete="new-password"')}
                    ${input("password_confirmation", "Confirm Password", null, 3, 9, 'password', 'autocomplete="new-password"')}
				`,
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
			            else if($("[name='password']").val().length < 8){
			                Swal.showValidationMessage('Password must at least be 8 characters');
			            }
			            else if($("[name='password']").val() != $("[name='password_confirmation']").val()){
			                Swal.showValidationMessage('Password do not match');
			            }
			            else{
			            	let bool = false;
			            	$.ajax({
			            		url: "{{ route('user.get') }}",
			            		data: {
			            			select: "id",
			            			where: ["username", $("[name='username']").val()]
			            		},
			            		success: result => {
			            			result = JSON.parse(result);
			            			if(result.length){
			                			Swal.showValidationMessage('Username already exists');
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
						url: "{{ route('site.store') }}",
						type: "POST",
						data: {
							name: $("[name='name']").val(),
							site_location: $("[name='site_location']").val(),
							username: $("[name='username']").val(),
							password: $("[name='password']").val(),
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
				url: "{{ route('site.get') }}",
				data: {
					select: '*',
					where: ['id', id],
					load: ['user']
				},
				success: site => {
					site = JSON.parse(site)[0];
					showDetails(site);
				}
			})
		}

		function showDetails(site){
			Swal.fire({
				html: `
	                ${input("id", "", site.id, 3, 9, 'hidden')}
	                ${input("name", "Name", site.name, 3, 9)}
	                ${input("site_location", "Site Location", site.site_location, 3, 9)}
	                </br>
	                ${input("username", "Username", site.user.username, 3, 9, 'text', 'autocomplete="new-password"')}
				`,
				width: '800px',
				confirmButtonText: 'Update',
				showCancelButton: true,
				cancelButtonColor: errorColor,
				cancelButtonText: 'Cancel',
                showDenyButton: true,
                denyButtonColor: successColor,
                denyButtonText: 'Change Password',
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
			            		url: "{{ route('user.get') }}",
			            		data: {
			            			select: "id",
			            			where: ["username", $("[name='username']").val()]
			            		},
			            		success: result => {
			            			result = JSON.parse(result);
			            			if(result.length && result[0].id != site.user.id){
			                			Swal.showValidationMessage('Username already exists');
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
						url: "{{ route('user.update') }}",
						data: {
							id: site.user.id,
							username: $("[name='username']").val(),
						},
						message: false
					},	() => {
						update({
							url: "{{ route('site.update') }}",
							data: {
								id: $("[name='id']").val(),
								name: $("[name='name']").val(),
								site_location: $("[name='site_location']").val(),
							},
							message: "Success"
						},	() => {
							reload();
						});
					});
				}
				else if(result.isDenied){
					changePassword($("[name='id']").val());
				}
			});
		}

		function changePassword(id){
			Swal.fire({
			    html: `
			        ${input("password", "Password", null, 5, 7, 'password')}
			        ${input("password_confirmation", "Confirm Password", null, 5, 7, 'password')}
			    `,
			    confirmButtonText: 'Update',
			    showCancelButton: true,
			    cancelButtonColor: errorColor,
			    cancelButtonText: 'Exit',
			    width: "500px",
			    preConfirm: () => {
			        swal.showLoading();
			        return new Promise(resolve => {
			            setTimeout(() => {
			                if($('.swal2-container input:placeholder-shown').length){
			                    Swal.showValidationMessage('Fill all fields');
			                }
			                else if($("[name='password']").val().length < 8){
			                    Swal.showValidationMessage('Password must at least be 8 characters');
			                }
			                else if($("[name='password']").val() != $("[name='password_confirmation']").val()){
			                    Swal.showValidationMessage('Password do not match');
			                }
			            resolve()}, 500);
			        });
			    },
			}).then(result => {
				if(result.value){
					swal.showLoading();
					update({
						url: "{{ route('user.updatePassword') }}",
						data: {
							id: id,
							password: $("[name='password']").val(),
						}
					}, () => {
						ss("Success");
					});
				}
			});
		}

		function del(id){
			sc("Confirmation", "Are you sure you want to delete?", result => {
				if(result.value){
					swal.showLoading();
					update({
						url: "{{ route('site.delete') }}",
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
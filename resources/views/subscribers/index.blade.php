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

                        @include('subscribers.includes.toolbar')
                    </div>

                    <div class="card-body table-responsive">
                    	<table id="table" class="table table-hover">
                    		<thead>
                    			<tr>
                    				<th>ID</th>
                    				<th>Name</th>
                    				<th>Email</th>
                    				<th>Contact</th>
                    				<th>Address</th>
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
					url: "{{ route('datatable.subscriber') }}",
                	dataType: "json",
                	dataSrc: "",
					data: {
						table: 'users',
						select: "*",
						where: ["Role", "Subscriber"]
					}
				},
				columns: [
					{data: 'id'},
					{data: 'name'},
					{data: 'email'},
					{data: 'contact'},
					{data: 'address'},
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
				url: "{{ route('subscriber.getSubscriberDetails') }}",
				data: {
					id: id
				},
				success: subscriber => {
					subscriber = JSON.parse(subscriber);
					showDetails(subscriber);
				}
			})
		}

		function edit(id){
			$.ajax({
				url: "{{ route('subscriber.getSubscriberDetails') }}",
				data: {
					id: id
				},
				success: subscriber => {
					subscriber = JSON.parse(subscriber);
					showDetails2(subscriber);
				}
			})
		}

		function create(){
			Swal.fire({
				html: `
	                ${input("name", "Name", null, 3, 9)}
					${input("email", "Email", null, 3, 9, 'email')}
	                ${input("contact", "Contact #", null, 3, 9)}
	                ${input("address", "Address", null, 3, 9)}
				`,
				width: '800px',
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

			            bool ? setTimeout(() => {resolve()}, 500) : "";
				    });
				},
			}).then(result => {
				if(result.value){
					swal.showLoading();
					$.ajax({
						url: "{{ route('user.store') }}",
						type: "POST",
						data: {
							name: $("[name='name']").val(),
							email: $("[name='email']").val(),
							contact: $("[name='contact']").val(),
							address: $("[name='address']").val(),
							role: "Subscriber",
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

		function showDetails(subscriber){
			Swal.fire({
				title: 'Subscriber Details',
				html: `

					<br>

        			<div class="row">
		                <section class="col-lg-4 connectedSortable">
		                    <div class="card">
		                        <div class="card-header row">
		                            <div class="col-md-12">
		                                <h3 class="card-title" style="width: 100%; text-align: left;">
		                                    <i class="fas fa-user mr-1"></i>

		                                    User Details

		                                </h3>
		                            </div>
		                        </div>

		                        <div class="card-body">

		                            <div class="row iRow">
		                                <div class="col-md-3 iLabel">
											Name:
		                                </div>
		                                <div class="col-md-9 iInput">
		                                	${subscriber.user.name}
		                                </div>
		                            </div>
		                        
		                            <div class="row iRow">
		                                <div class="col-md-3 iLabel">
											Email:
		                                </div>
		                                <div class="col-md-9 iInput">
		                                	${subscriber.user.email}
		                                </div>
		                            </div>
		                        
		                            <div class="row iRow">
		                                <div class="col-md-3 iLabel">
											Contact:
		                                </div>
		                                <div class="col-md-9 iInput">
		                                	${subscriber.user.contact}
		                                </div>
		                            </div>
		                        
		                            <div class="row iRow">
		                                <div class="col-md-3 iLabel">
											Address:
		                                </div>
		                                <div class="col-md-9 iInput">
		                                	${subscriber.user.address}
		                                </div>
		                            </div>

		                        </div>
		                    </div>
		                </section>

		                <section class="col-lg-8 connectedSortable">
		                    <div class="card">
		                        <div class="card-header row">
		                            <div class="col-md-12">
		                                <h3 class="card-title" style="width: 100%; text-align: left;">
		                                    <i class="fas fa-file-invoice-dollar mr-1"></i>

		                                    Billings

		                                </h3>
		                            </div>
		                        </div>

		                        <div class="card-body">
			                    	<table class="table table-hover">
			                    		<thead>
			                    			<tr>
			                    				<th>Serial</th>
			                    				<th>From</th>
			                    				<th>To</th>
			                    				<th>Reading</th>
			                    				<th>Total</th>
			                    				<th>Status</th>
			                    			</tr>
			                    		</thead>
			                    		<tbody id="billingsTable">

			                    		</tbody>
			                    	</table>
		                        </div>
		                    </div>
		                </section>

		                <section class="col-lg-4 connectedSortable">
		                    <div class="card">
		                        <div class="card-header row">
		                            <div class="col-md-12">
		                                <h3 class="card-title" style="width: 100%; text-align: left;">
		                                    <i class="fas fa-bolt-lightning mr-1"></i>

		                                    Devices

		                                </h3>
		                            </div>
		                        </div>

		                        <div class="card-body">
			                    	<table class="table table-hover">
			                    		<thead>
			                    			<tr>
			                    				<th>Serial</th>
			                    				<th>Type</th>
			                    				<th>Classification</th>
			                    			</tr>
			                    		</thead>
			                    		<tbody id="devicesTable">

			                    		</tbody>
			                    	</table>
		                        </div>
		                    </div>
		                </section>

		                <section class="col-lg-8 connectedSortable">
		                    <div class="card">
		                        <div class="card-header row">
		                            <div class="col-md-12">
		                                <h3 class="card-title" style="width: 100%; text-align: left;">
		                                    <i class="fas fa-dollar mr-1"></i>

		                                    Transactions

		                                </h3>
		                            </div>
		                        </div>

		                        <div class="card-body">
			                    	<table class="table table-hover">
			                    		<thead>
			                    			<tr>
			                    				<th>Mode of Payment</th>
			                    				<th>Reference No</th>
			                    				<th>Invoice</th>
			                    				<th>Date Paid</th>
			                    			</tr>
			                    		</thead>
			                    		<tbody id="transactionsTable">

			                    		</tbody>
			                    	</table>
		                        </div>
		                    </div>
		                </section>
		            </div>
				`,
				width: '80%',
				confirmButtonText: 'Ok',
				didOpen: () => {
					let devicesTable = "";
					let devices = subscriber.devices;

					if(devices.length){
						devices.forEach(device => {
							devicesTable += `
								<tr>
									<td>${device.serial}</td>
									<td>${device.category.type}</td>
									<td>${device.category.classification}</td>
								</tr>
							`;
						});
					}
					else{
						devicesTable = `
							<tr>
								<td colspan="3">N/A</td>
							</tr>
						`;
					}

					let billingsTable = "";
					let billings = subscriber.billings;

					let paidSize = 0;

					if(billings.length){
						billings.forEach(billing => {
							billingsTable += `
								<tr>
									<td>${billing.device.serial}</td>
									<td>${moment(billing.from).format(dateFormat2)}</td>
									<td>${moment(billing.to).format(dateFormat2)}</td>
									<td>${numeral(billing.reading).format('0,0')}</td>
									<td>${"₱" + numeral(billing.total).format('0,0.00')}</td>
									<td>${billing.status}</td>
								</tr>
							`;

							if(billing.status == "Paid"){
								paidSize++;
							}
						});
					}
					else{
						billingsTable = `
							<tr>
								<td colspan="6">N/A</td>
							</tr>
						`;
					}

					let transactionsTable = "";

					if(paidSize){
						billings.forEach(billing => {
							if(billing.status == "Paid"){
								transactionsTable += `
									<tr>
										<td>${billing.mop}</td>
										<td>${billing.refno}</td>
										<td>${billing.invoice}</td>
										<td>${moment(billing.date_paid).format(dateFormat2)}</td>
									</tr>
								`;

							}
						});
					}
					else{
						transactionsTable = `
							<tr>
								<td colspan="4">N/A</td>
							</tr>
						`;
					}

					$('#transactionsTable').append(transactionsTable);
					$('#billingsTable').append(billingsTable);
					$('#devicesTable').append(devicesTable);

					$('#swal2-html-container .card-header').css('margin', "1px");
					$('#swal2-html-container .card-header').css('background-color', "#83c8e5");
				}
			});
		}

		function showDetails2(subscriber){
			subscriber = subscriber.user;
			Swal.fire({
				html: `
	                ${input("id", "", subscriber.id, 3, 9, 'hidden')}
	                ${input("name", "Name", subscriber.name, 3, 9)}
					${input("email", "Email", subscriber.email, 3, 9, 'email')}
	                ${input("contact", "Contact #", subscriber.contact, 3, 9)}
	                ${input("address", "Address", subscriber.address, 3, 9)}
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

			            bool ? setTimeout(() => {resolve()}, 500) : "";
				    });
				},
			}).then(result => {
				if(result.value){
					swal.showLoading();
					update({
						url: "{{ route('user.update') }}",
						data: {
							id: $("[name='id']").val(),
							name: $("[name='name']").val(),
							email: $("[name='email']").val(),
							contact: $("[name='contact']").val(),
							address: $("[name='address']").val(),
						},
						message: "Success"
					},	() => {
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
						url: "{{ route('user.delete') }}",
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
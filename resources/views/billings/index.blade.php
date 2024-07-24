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
                            Billing
                        </h3>

                        @include('billings.includes.toolbar')
                    </div>

                    <div class="card-body table-responsive">
                    	<table id="table" class="table table-hover">
                    		<thead>
                    			<tr>
                    				<th>ID</th>
                    				<th>User</th>
                    				<th>Device</th>
                    				<th>Reading</th>
                    				<th>Rate</th>
                    				<th>Total</th>
                    				<th>Status</th>
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

	<style>
		#table td, #table th{
			text-align: center;
		}
	</style>
@endpush

@push('scripts')
	<script src="{{ asset('js/datatables.min.js') }}"></script>
	<script src="{{ asset('js/select2.min.js') }}"></script>
	{{-- <script src="{{ asset('js/datatables-jquery.min.js') }}"></script> --}}

	<script>
		$(document).ready(()=> {
			var table = $('#table').DataTable({
				ajax: {
					url: "{{ route('datatable.billing') }}",
                	dataType: "json",
                	dataSrc: "",
					data: {
						table: 'billings',
						select: "*",
						load: ['user', 'device']
					}
				},
				columns: [
					{data: 'id'},
					{data: 'user.name'},
					{data: 'device.serial'},
					{data: 'reading'},
					{data: 'rate'},
					{data: 'total'},
					{data: 'status'},
					{data: 'actions'},
				],
				columnDefs: [
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
					<div class="row iRow">
					    <div class="col-md-3 iLabel">
					        Subscriber
					    </div>
					    <div class="col-md-9 iInput">
					        <select name="user_id" class="form-control">
					        	<option value=""></option>
					        </select>
					    </div>
					</div>
					<div class="row iRow">
					    <div class="col-md-3 iLabel">
					        Device
					    </div>
					    <div class="col-md-9 iInput">
					        <select name="moxa_id" class="form-control">
					        	<option value=""></option>
					        </select>
					    </div>
					</div>

					${input("reading", "Current Reading", null, 3, 9, 'number', 'min=0')}

					<div class="row iRow">
					    <div class="col-md-3 iLabel">
					        Last Billing
					    </div>
					    <div class="col-md-9 iInput">
					        <div id="last_billing">N/A</div>
					    </div>
					</div>

					<div class="row iRow">
					    <div class="col-md-3 iLabel">
					        Reading
					    </div>
					    <div class="col-md-9 iInput">
					        <div id="last_reading" data-value="0">N/A</div>
					    </div>
					</div>

					<div class="row iRow">
					    <div class="col-md-3 iLabel">
					        Rate
					    </div>
					    <div class="col-md-9 iInput">
					        <div id="rate" data-value="0">0</div>
					    </div>
					</div>

					<div class="row iRow">
					    <div class="col-md-3 iLabel">
					        Total Amount
					    </div>
					    <div class="col-md-9 iInput">
					        <div id="total">₱0.00</div>
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
						url: "{{ route('user.get') }}",
						data: {
							select: "*",
							where: ['role', 'Subscriber']
						},
						success: subscribers => {
							subscribers = JSON.parse(subscribers);
							subscriberString = "";

							subscribers.forEach(subscriber => {
								subscriberString += `
									<option value="${subscriber.id}">${subscriber.name}</option>
								`;
							});

							$("[name='user_id']").append(subscriberString);
							$("[name='user_id']").select2({
								placeholder: "Select Subscriber",
							});
						}
					});

					$("[name='moxa_id']").select2({
						placeholder: "Select Subscriber First"
					});

					$("[name='user_id']").on('change', e => {
						$("[name='moxa_id']").html('<option value=""></option>');

						$.ajax({
							url: "{{ route('device.get') }}",
							data: {
								select: "*",
								where: ['name', e.target.value]
							},
							success: devices => {
								devices = JSON.parse(devices);
								deviceString = "";

								devices.forEach(device => {
									deviceString += `
										<option value="${device.id}">${device.serial}</option>
									`;
								});

								$("[name='moxa_id']").append(deviceString);
								$("[name='moxa_id']").select2({
									placeholder: "Select Device"
								});
							}
						})
					});

					$("[name='moxa_id']").on('change', e => {
						$.ajax({
							url: "{{ route('billing.getDetails') }}",
							data: {
								select: "*",
								id: e.target.value
							},
							success: data => {
								data = JSON.parse(data);

								if(data.billing.length){
									$('#last_billing').html(moment(data.billing[0].created_at).format(dateTimeFormat2 + " A"));
									$('#last_reading').html(data.billing[0].reading);
									$('#last_reading').data("value", data.billing[0].reading);
								}
								else{
									$('#last_billing').html("N/A");
									$('#last_reading').html("N/A");
								}

								if(data.device.serial){
									$('#rate').html(data.device.category.rate + "/" + data.device.category.operator);
									$('#rate').data('value', data.device.category.rate);
								}
								else{
									$('#rate').html("0");
								}
							}
						})
					});

					$("[name='reading']").on('keyup', e => {
						let lr = $('#last_reading').data('value');
						let r = $('#rate').data('value');

						$('#total').html("₱" + numeral((e.target.value - lr) * r).format('0,0.00'));
					});
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

					let cr = $("[name='reading']").val();
					let lr = $('#last_reading').data('value');
					let r = $('#rate').data('value');

					$.ajax({
						url: "{{ route('billing.store') }}",
						type: "POST",
						data: {
							moxa_id: $("[name='moxa_id']").val(),
							reading: cr,
							rate: r,
							total: (cr - lr) * r,
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
	</script>
@endpush
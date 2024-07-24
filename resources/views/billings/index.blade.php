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

                    </div>

                    <div class="card-body table-responsive">
                        @include('billings.includes.toolbar')

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
		var fUser_id = "%%";
		var fMoxa_id = "%%";
		var fStatus = "%%";

		$(document).ready(()=> {
			$("[name='user_id']").on('change', e => {
				fUser_id = e.target.value;
				filter();
				reload();
			});

			$("[name='moxa_id']").on('change', e => {
				fMoxa_id = e.target.value;
				filter();
				reload();
			});

			$("[name='status']").on('change', e => {
				fStatus = e.target.value;
				filter();
				reload();
			});

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

					$("[name='fUser_id']").append(subscriberString);
					$("[name='fUser_id']").select2({
						placeholder: "Select Subscriber / All"
					});

					$("[name='fUser_id']").change(e => {
						fUser_id = e.target.value;
						fMoxa_id = "%%";
						getDevices();
						$("[name='moxa_id']").html(`
								<option value="%%">Select Device / All</option>
							`);
						filter();
					});

					getDevices();
				}
			});
			
			createTable();
		});

		function getDevices(){
			$.ajax({
				url: "{{ route('device.get') }}",
				data: {
					select: "*",
					like: ['name', fUser_id]
				},
				success: moxas => {
					moxas = JSON.parse(moxas);
					moxaString = "";

					moxas.forEach(moxa => {
						moxaString += `
							<option value="${moxa.id}">${moxa.serial}</option>
						`;
					});

					$("[name='fMoxa_id']").append(moxaString);
					$("[name='fMoxa_id']").select2({
						placeholder: "Select Device / All"
					});
					$("[name='fMoxa_id']").change(e => {
						fMoxa_id = e.target.value;
						filter();
					});
				}
			});
		}

		function filter(){
			$('#table').DataTable().clear().destroy();
			createTable();
		}

		function createTable(){
			var table = $('#table').DataTable({
				ajax: {
					url: "{{ route('datatable.billing') }}",
                	dataType: "json",
                	dataSrc: "",
					data: f => {
						f.table = 'billings';
						f.select = "*";
						f.load = ['user', 'device'];
						f.user_id = fUser_id;
						f.moxa_id = fMoxa_id;
						f.status = fStatus;
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
        		order: [[0, 'desc']],
				// drawCallback: function(){
				// 	init();
				// }
			});
		}

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

			            if($('.swal2-container input:placeholder-shown').length || $('#swal2-html-container .select2-selection__placeholder').length){
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

		function pay(id){
			Swal.fire({
				title: 'Input Details',
				html: `
					${input("mop", "Mode of Payment", null, 3, 9)}
					${input("refno", "Reference No.", null, 3, 9)}
				`,
				preConfirm: () => {
				    swal.showLoading();
				    return new Promise(resolve => {
				    	let bool = true;

			            if($('.swal2-container input:placeholder-shown').length || $('#swal2-html-container .select2-selection__placeholder').length){
			                Swal.showValidationMessage('Fill all fields');
			            }
			            else{
			            	let bool = false;
			            }

			            bool ? setTimeout(() => {resolve()}, 500) : "";
				    });
				},
				width: '50%'
			}).then(result => {
				if(result.value){
					$.ajax({
						url: "{{ route('billing.pay') }}",
						type: "POST",
						data: {
							id: id,
							refno: $("[name='refno']").val(),
							mop: $("[name='mop']").val(),
							_token: $('meta[name="csrf-token"]').attr('content')
						},
						success: () => {
							reload();
							ss("Success");
						}
					})
				}
			})
		}

		function viewPayment(id){
			swal.showLoading();
			$.ajax({
				url: '{{ route('billing.get') }}',
				data: {
					select: '*',
					where: ['id', id]
				},
				success: bill => {
					bill = JSON.parse(bill)[0];

					Swal.fire({
						title: 'Payment Details',
						html: `
							${input("mop", "Mode of Payment", bill.mop, 3, 9, null, 'disabled')}
							${input("refno", "Reference No.", bill.refno, 3, 9, null, 'disabled')}
							${input("invoice", "Invoice No.", bill.invoice, 3, 9, null, 'disabled')}
							${input("date_paid", "Date Paid", bill.date_paid, 3, 9, null, 'disabled')}
						`,
						width: '50%',
						didOpen: () => {
							$('[name="date_paid"]').flatpickr({
								altInput: true,
								altFormat: "F j, Y h:i:s K",
								dateFormat: "Y-m-d",
							});
						}
					})
				}
			})
		}
	</script>
@endpush
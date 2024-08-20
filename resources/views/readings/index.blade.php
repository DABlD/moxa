@extends('layouts.app')
@section('content')

<section class="content">
    <div class="container-fluid">

        <div class="row">
            <section class="col-lg-12 connectedSortable">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-pen-to-square mr-1"></i>
                            Reading
                        </h3>
                    </div>

                    <div class="card-body table-responsive">
                        
                        @include('readings.includes.toolbar')

                        <br>
                    	<table id="table" class="table table-hover">
                    		<thead>
                    			<tr>
                    				<th>ID</th>
                    				<th>Device</th>
                    				<th>Serial</th>
                    				<th>Utility</th>
                    				<th>Payload</th>
                    				<th>Datetime</th>
                    				<th>Reading Date</th>
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
		td, th{
			text-align: center;
		}

		.fa-2xl{
			line-height: -0.96875em !important;
		}
	</style>
@endpush

@push('scripts')
	<script src="{{ asset('js/datatables.min.js') }}"></script>
	<script src="{{ asset('js/select2.min.js') }}"></script>
	{{-- <script src="{{ asset('js/datatables-jquery.min.js') }}"></script> --}}

	<script>
		var building = "%%";
		var trueBuilding = "%%";
		var from = moment().subtract(14, 'days').format(dateFormat);
		var to = dateNow();

		let settings = {
			altInput: true,
			altFormat: "F j, Y",
			dateFormat: "Y-m-d",
		};

		$(document).ready(()=> {
			$('#from').append(input('from', 'From', from, 4, 8));
			$('#to').append(input('to', 'To', to, 4, 8));

			$("[name='from']").flatpickr(settings);
			$("[name='to']").flatpickr(settings);

			$("[name='from']").on('change', e => {
				from = e.target.value;
				filter();
				reload();
			});

			$("[name='to']").on('change', e => {
				to = e.target.value;
				filter();
				reload();
			});

			$.ajax({
				url: "{{ route('building.getCategories') }}",
				data: {
					select: "buildings.*",
				},
				success: categoriess => {
					categoriess = JSON.parse(categoriess);
					categoriesString = "";

					categoriess.forEach(categories => {
						categoriesString += `
							<option value="${categories.id}">${categories.name}</option>
						`;
					});

					$("[name='bldg']").append(categoriesString);
					$("[name='bldg']").select2({
						placeholder: "Select Building / All"
					});
					$("[name='bldg']").change(e => {
						trueBuilding = e.target.value;
						building = "%%";
						getDevices();
						$("[name='outlet']").html(`
								<option value="%%">Select Device / All</option>
							`);
						filter();
					});

					getDevices();
				}
			});

			// CREATE TABLE
			createTable();
		});

		function getDevices(){
			$.ajax({
				url: "{{ route('device.get') }}",
				data: {
					select: "devices.*",
					like: ['category_id', trueBuilding]
				},
				success: moxas => {
					moxas = JSON.parse(moxas);
					moxaString = "";

					moxas.forEach(moxa => {
						moxaString += `
							<option value="${moxa.id}">${moxa.serial}</option>
						`;
					});

					$("[name='outlet']").append(moxaString);
					$("[name='outlet']").select2({
						placeholder: "Select Device / All"
					});
					$("[name='outlet']").change(e => {
						building = e.target.value;
						filter();
					});
				}
			});
		}

		function createTable(){
			table = $('#table').DataTable({
				ajax: {
					url: "{{ route('datatable.reading') }}",
                	dataType: "json",
                	dataSrc: "",
					data: f => {
						f.where = ['moxa_id', building];
						f.where2 = ['m.category_id', trueBuilding];
						f.from = from;
						f.to = to;
						f.load = ['device.subscriber']
						f.select = ['readings.*']
					}
				},
				columns: [
					{ data: 'id' },
					{ data: 'device.subscriber.name', visible: false},
					{ data: 'device.serial'},
					{ data: 'device.utility'},
					{ data: 'total' },
					{ data: 'datetime' },
					{ data: 'created_at' },
				],
				columnDefs: [
					{
						targets: [5,6],
						render: date => {
							return moment(date).format("MMM DD, YYYY hh:mm A");
						}
					}
				],
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
		                            		<td colspan="8" style="text-align: left !important;">
		                            			${building}
		                            		</td>
		                            	</tr>
		                            `);
		 
		                        last = building;
		                    }
		                });
				},
        		// scrollX: true,
        		pageLength: 25,
        		// ordering: false,
        		order: [0, 'desc']
			});
			
			$('#table_filter').remove();
			$('#table').css('width', '100%');
		}

		function filter(){
			$('#table').DataTable().clear().destroy();
			// $('#table thead').html('');
			createTable();
		}

		function add(){
			Swal.fire({
				title: "Input Reading",
				html: `
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
	                ${input("datetime", "Date", null, 3, 9)}
	                ${input("total", "Payload", null, 3, 9, 'number')}
				`,
				width: '800px',
				confirmButtonText: 'Add',
				showCancelButton: true,
				cancelButtonColor: errorColor,
				cancelButtonText: 'Cancel',
				didOpen: () => {
					$("[name='datetime']").flatpickr({
						altInput: true,
						altFormat: "F j, Y H:i K",
						dateFormat: "Y-m-d H:i:ss",
						enableTime: true,
					});

					$.ajax({
						url: "{{ route('device.get') }}",
						data: {
							select: "*",
							load: ["building", "category", "subscriber"]
						},
						success: moxas => {
							moxas = JSON.parse(moxas);
							moxaString = "";

							moxas.forEach(moxa => {
								moxaString += `
									<option value="${moxa.id}">${moxa.subscriber.name} #${moxa.serial} (${moxa.utility})</option>
								`;
							});

							$("[name='moxa_id']").append(moxaString);
							$("[name='moxa_id']").select2({
								placeholder: "Select Device"
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
						url: "{{ route('reading.store') }}",
						type: "POST",
						data: {
							moxa_id: $("[name='moxa_id']").val(),
							datetime: $("[name='datetime']").val(),
							total: $("[name='total']").val(),
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

		function create(){
			Swal.fire({
				title: 'SOA Generator',
				html: `
					<div class="row iRow">
					    <div class="col-md-3 iLabel">
					        Serial
					    </div>
					    <div class="col-md-9 iInput">
					        <select name="moxa_id" class="form-control">
					        	<option value=""></option>
					        </select>
					    </div>
					</div>

					<br>

					${input("from", "From", null, 3, 9, 'text')}
					${input("to", "To", null, 3, 9, 'text')}

					<br>

					${input("reading", "Current Reading", null, 3, 9, 'number', 'min=0')}

					<br>

					<div class="row iRow">
					    <div class="col-md-3 iLabel">
					        Latest Reading
					    </div>
					    <div class="col-md-9 iInput">
					        <div id="last_reading" data-value="0">N/A</div>
					    </div>
					</div>
				`,
				width: '600px',
				confirmButtonText: 'Add',
				showCancelButton: true,
				cancelButtonColor: errorColor,
				cancelButtonText: 'Cancel',
				didOpen: () => {
					$.ajax({
						url: "{{ route('device.get') }}",
						data: {
							select: "*"
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
					});

					$("[name='moxa_id']").on('change', e => {
						$.ajax({
							url: "{{ route('reading.getLatestReading') }}",
							data: {
								select: "*",
								id: e.target.value
							},
							success: data => {
								if(data){
									data = JSON.parse(data);

									$('#last_reading').html(numeral(data.total).format('0,0'));
									$('#last_reading').data("value", data.total);
								}
								else{
									$('#last_reading').html("N/A");
								}
							}
						})
					});

					$('[name="from"], [name="to"]').flatpickr({
						altInput: true,
						altFormat: "F j, Y",
						dateFormat: "Y-m-d",
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

					$.ajax({
						url: "{{ route('billing.store') }}",
						type: "POST",
						data: {
							moxa_id: $("[name='moxa_id']").val(),
							from: $("[name='from']").val(),
							to: $("[name='to']").val(),
							reading: $("[name='reading']").val(),
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

		function create2(){
			// f.where = ['moxa_id', building];
			// f.where2 = ['m.category_id', trueBuilding];
			// f.from = from;
			// f.to = to;
			// f.load = ['device.subscriber']
			// f.select = ['readings.*']

			Swal.fire({

				title: "Generate SOA cutoff",
				html: `
					<div class="row iRow">
					    <div class="col-md-6 iLabel">
					        Building
					    </div>
					    <div class="col-md-6 iInput">
					        <div id="building">${building == "%%" ? "All" : $('#bldg option:selected').html()}</div>
					    </div>
					</div>
					<div class="row iRow">
					    <div class="col-md-6 iLabel">
					        Device
					    </div>
					    <div class="col-md-6 iInput">
					        <div id="trueBuilding">${trueBuilding == "%%" ? "All" : $('#outlet option:selected').html()}</div>
					    </div>
					</div>
					<div class="row iRow">
					    <div class="col-md-6 iLabel">
					        From
					    </div>
					    <div class="col-md-6 iInput">
					        <div id="from">${moment(from).format(dateFormat2)}</div>
					    </div>
					</div>
					<div class="row iRow">
					    <div class="col-md-6 iLabel">
					        To
					    </div>
					    <div class="col-md-6 iInput">
					        <div id="to">${moment(to).format(dateFormat2)}</div>
					    </div>
					</div>
				`,
				width: '600px',
				confirmButtonText: 'Generate',
				showCancelButton: true,
				cancelButtonColor: errorColor,
				cancelButtonText: 'Cancel',
			}).then(result => {
				if(result.value){
					swal.showLoading();

					$.ajax({
						url: "{{ route('billing.createBillings') }}",
						// type: "POST",
						data: {
							building: trueBuilding,
							trueBuilding: building,
							from: from,
							to: to,
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
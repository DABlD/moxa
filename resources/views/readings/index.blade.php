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
	</style>
@endpush

@push('scripts')
	<script src="{{ asset('js/datatables.min.js') }}"></script>
	<script src="{{ asset('js/select2.min.js') }}"></script>
	{{-- <script src="{{ asset('js/datatables-jquery.min.js') }}"></script> --}}

	<script>
		var building = "%%";
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
				url: "{{ route('moxa.get') }}",
				data: {
					select: "*",
				},
				success: moxas => {
					moxas = JSON.parse(moxas);
					moxaString = "";

					moxas.forEach(moxa => {
						moxaString += `
							<option value="${moxa.id}">${moxa.name}</option>
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

			// CREATE TABLE
			createTable();
		});

		function createTable(){
			table = $('#table').DataTable({
				ajax: {
					url: "{{ route('datatable.reading') }}",
                	dataType: "json",
                	dataSrc: "",
					data: f => {
						f.where = ['moxa_id', building];
						f.from = from;
						f.to = to;
						f.load = ['moxa']
						f.select = ['*']
					}
				},
				columns: [
					{ data: 'id' },
					{ data: 'moxa.name' },
					{ data: 'moxa.utility' },
					{ data: 'total' },
					{ data: 'datetime' },
					{ data: 'created_at' },
				],
				columnDefs: [
					{
						targets: [4,5],
						render: date => {
							return moment(date).format("MMM DD, YYYY hh:mm A");
						}
					}
				],
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
						url: "{{ route('moxa.get') }}",
						data: {
							select: "*",
							load: ["category"]
						},
						success: moxas => {
							moxas = JSON.parse(moxas);
							moxaString = "";

							moxas.forEach(moxa => {
								moxaString += `
									<option value="${moxa.id}">${moxa.name} #${moxa.serial} (${moxa.utility}) (${moxa.category.name})</option>
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
	</script>
@endpush
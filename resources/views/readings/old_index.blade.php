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
		table td{
			text-align: center;
		}

		th{
			padding-left: 9px !important;
			padding-right: 9px !important;
			font-size: 13px !important;
			vertical-align: middle !important;
		}
	</style>
@endpush

@push('scripts')
	<script src="{{ asset('js/datatables.min.js') }}"></script>
	<script src="{{ asset('js/select2.min.js') }}"></script>
	{{-- <script src="{{ asset('js/datatables-jquery.min.js') }}"></script> --}}

	<script>
		var columns = [];
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
				url: "{{ route('medicine.getCategories') }}",
				data: {
					select: "*",
				},
				success: categorys => {
					categorys = JSON.parse(categorys);
					categoryString = "";

					categorys.forEach(category => {
						categoryString += `
							<option value="${category.id}">${category.name}</option>
						`;
					});

					$("[name='outlet']").append(categoryString);
					$("[name='outlet']").select2({
						placeholder: "Select Building / All"
					});
					$("[name='outlet']").change(e => {
						console.log(e.target.value);
						building = e.target.value;
						filter();
					});
				}
			});

			// CREATE TABLE
			createTable();
		});

		function createTable(){
			getColumns();
			table = $('#table').DataTable({
				ajax: {
					url: "{{ route('reading.getReading') }}",
                	dataType: "json",
                	dataSrc: "",
					data: f => {
						f.building = building;
						f.from = from;
						f.to = to;
					}
				},
        		scrollX: true,
				columns: columns,
        		pageLength: 25,
        		ordering: false,
        		order: []
			});
			
			$('#table_filter').remove();
			$('#table').css('width', '100%');
		}

		function getColumns(){
			columns = [];
			columns.push(
				{
					data: 'item',
					title: 'Device'
				},
				{
					data: 'utility',
					title: 'Type'
				},
			);

			let temp = from;
			while(temp <= to){
				let temp2 = moment(temp).format("MMM DD");
				let temp3 = moment(temp).format("MMM DD (ddd)");

				columns.push({
					data: temp2,
					title: temp3
				});

				temp = moment(temp).add('1', 'day').format(dateFormat);
			}

			columns.push({
				data: 'total',
				title: 'Total'
			});
		}

		function filter(){
			$('#table').DataTable().clear().destroy();
			$('#table thead').html('');
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
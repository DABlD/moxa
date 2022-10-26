@extends('layouts.app')
@section('content')

<section class="content">
    <div class="container-fluid">

    	<div class="row">
            <section class="col-lg-12">
    			@include('datas.includes.toolbar')
    		</section>
    	</div>

        <div class="row">
            <section class="col-lg-4 connectedSortable">
                <div class="card">
                    <div class="card-header center">
                        <h3 class="card-title">
                            SELECTION
                        </h3>
                    </div>

                    <div class="card-body table-responsive selection">
                    	<div class="row">
	        			    <div class="col-md-4 iLabel">
	        			        Category
	        			    </div>
				            <div class="col-md-8 iInput">
				                <select name="category" class="form-control">
				                	<option value=""></option>
				                </select>
				            </div>
                    	</div>
                    	<br>
                    	<div class="row">
                    		<table id="table" class="table table-hover">
                    			<thead>
                    				<tr>
                    					<th>CATEGORY</th>
                    				</tr>
                    			</thead>

                    			<tbody>
                    			</tbody>
                    		</table>
                    	</div>
                    </div>
                </div>
            </section>

            <section class="col-lg-8 connectedSortable">
                <div class="card">
                    <div class="card-header center">
                        <h3 class="card-title">
                            DETAILS
                        </h3>
                    </div>

                    <div class="card-body table-responsive selection">

                    	<div class="row">
                    		<div class="col-md-6">
                    			
                    			<div class="row">
                    			    <div class="col-md-3 iLabel">
                    			        Transaction Type
                    			    </div>
            			            <div class="col-md-9 iInput">
            			                <select name="type" class="form-control">
            			                	<option value=""></option>
            			                </select>
            			            </div>
                    			</div>

                    		</div>

                    		<div class="col-md-6">
                    			
                    			<div class="row">
                    			    <div class="col-md-3 iLabel">
                    			        Transaction Date
                    			    </div>
            			            <div class="col-md-9 iInput">
            			                <input type="text" name="transaction_date" placeholder="Select Transaction Date" class="form-control">
            			            </div>
                    			</div>

                    		</div>
                    	</div>

                    	<br>
                    	<div class="row">
                    		<div class="col-md-6">
                    			
                    			<div class="row">
                    			    <div class="col-md-3 iLabel">
                    			    	Reference No
                    			    </div>
            			            <div class="col-md-9 iInput">
            			                <input type="text" name="reference" placeholder="Enter Reference No." class="form-control">
            			            </div>
                    			</div>

                    		</div>

                    		<div class="col-md-6">
                    			
                    			<div class="row">
                    			    <div class="col-md-3 iLabel">
                    			        Particulars
                    			    </div>
            			            <div class="col-md-9 iInput">
            			                <textarea name="particulars" placeholder="Enter Particulars" class="form-control" cols="10" rows="2"></textarea>
            			            </div>
                    			</div>

                    		</div>
                    	</div>

                    	<br>
                    	<div class="row">
                    		<div class="col-md-6">
                    			@if(auth()->user()->role != "Admin")
                    			<div class="row">
                    			    <div class="col-md-3 iLabel">
                    			    	BHC
                    			    </div>
            			            <div class="col-md-9 iInput">
            			                <select name="location" class="form-control">
            			                	<option value=""></option>
            			                </select>
            			            </div>
                    			</div>
                    			@endif
                    		</div>

                    		<div class="col-md-6"></div>
                    	</div>

                    	<br>
                    	<div class="row">
                    		<div class="col-md-12">
                    			<table id="table2" class="table table-hover">
                    				<thead>
                    					<tr>
                    						<th>Item</th>
                    						<th>Lot Number</th>
                    						<th>Supplier</th>
                    						<th>Expiry Date</th>
                    						<th>Qty</th>
                    						<th>Price</th>
                    						<th>Amount</th>
                    						<th>Actions</th>
                    					</tr>
                    				</thead>

                    				<tbody>
                    				</tbody>
                    			</table>
                    		</div>
                    	</div>

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
	<link rel="stylesheet" href="{{ asset('css/flatpickr.min.css') }}">
	<style type="text/css">
		.card-header{
			background-color: #66b966;
		}
		.card-title{
			font-weight: bold;
			color: white;
			margin: auto;
			float: none;
		}
		.row .col-md-6, .iLabel, .iInput{
			margin: auto;
		}
		#table_wrapper, #table_filter, #table_filter label, [type|="search"] {
			width: 100%;
		}
		[type|="search"]{
			margin-left: 0 !important;
		}
		#table2 tbody td{
			vertical-align: middle;
		}
		#table2 thead th{
			text-align: center;
		}
	</style>
	{{-- <link rel="stylesheet" href="{{ asset('css/datatables-jquery.min.css') }}"> --}}
@endpush

@push('scripts')
	<script src="{{ asset('js/datatables.min.js') }}"></script>
	<script src="{{ asset('js/select2.min.js') }}"></script>
	<script src="{{ asset('js/flatpickr.min.js') }}"></script>
	{{-- <script src="{{ asset('js/datatables-jquery.min.js') }}"></script> --}}

	<script>
		var category = 0;

		$(document).ready(()=> {

			var table = $('#table').DataTable({
				ajax: {
					url: "{{ route('datatable.medicine2') }}",
                	dataType: "json",
                	dataSrc: "",
					data: d => {
					   d.table = 'medicines';
					   d.select = '*';
					   d.where = ["category_id", category];
					}
				},
				columns: [
					{
						render: (id, display, row) => {
							return `
								<div class="row">
									<div class="col-md-8" style="font-weight: bold;">
										${row.code}
									</div>
									<div class="col-md-4" style="text-align: right;">
										<a class="btn btn-success btn-sm" data-toggle="tooltip" title="Add" onclick="add(${row.id})">
										    <i class="fas fa-plus fa-2xl"></i>
										</a>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12">
										${row.name} ${row.packaging}
									</div>
								</div>
								<div class="row">
									<div class="col-md-8">
										${row.brand}
									</div>
									<div class="col-md-4" style="text-align: right;">
										
									</div>
								</div>
							`;
						}
					},
				],
        		pageLength: 1000,
        		lengthChange: false,
        		paging: false,
        		info: false,
        		language: {
        			search: "",
        			emptyTable: "No Selected Category"
        		},
				drawCallback: () => {
					//150 = height elements above it
					$('.selection ').css("height", window.outerHeight - 150);
					$('[type="search"]input').attr("placeholder", "Search");
				}
			});

			initStyles();
			initCategories();
			initTransactionType();
			initTransactionDate();
			@if(auth()->user()->role != "Admin")
			initLocation();
			@endif
			addFooter();
			computeTotal();
		});

		function initStyles(){
			$('.iInput').css('margin', 'auto');
		}

		function viewDataEntry(){
			Swal.fire({
				title: 'Data Entries',
				width: '95%',
				showClass: {
					backdrop: 'swal2-noanimation',
					popup: '',
					icon: ''
				},
				showCloseButton: true,
				html: `
					<table id="table4" style="width: '100%' !important;">
						<thead>
							<tr>
								<th>ID</th>
								<th>Date</th>
								<th>User</th>
								<th>Medicine</th>
								<th>Tx Type</th>
								<th>Reference</th>
								<th>Particulars</th>
								<th>Lot #</th>
								<th>Supplier</th>
								<th>Expiry</th>
								<th>Qty</th>
								<th>Price</th>
								<th>Amount</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				`,
				didOpen: () => {
					var table4 = $('#table4').DataTable({
		        		pageLength: 25,
		        		ordering: false,
						ajax: {
							url: "{{ route('datatable.data') }}",
		                	dataType: "json",
		                	dataSrc: "",
							data: {
								select: '*',
								order: ['transaction_date', 'desc'],
								load: ['reorder.medicine', 'user', 'transaction_type'],
								where: ['user_id', {{ auth()->user()->id }}]
							},
						},
						columns: [
							{data: 'id'},
							{
								data: 'transaction_date',
								render: date => {
									return moment(date).format('MMM DD, YYYY')
								}
							},
							{data: 'user.name'},
							{data: 'reorder.medicine.name'},
							{data: 'transaction_type.type'},
							{data: 'reference'},
							{data: 'particulars'},
							{data: 'lot_number'},
							{data: 'supplier'},
							{
								data: 'expiry_date',
								render: date => {
									return moment(date).format('MMM DD, YYYY')
								}
							},
							{data: 'qty'},
							{data: 'unit_price'},
							{data: 'amount'},
						],
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
				                .each(function (transaction_date, i, row) {
				                	transaction_date = moment(transaction_date).format('MMM DD, YYYY');
				                    if (last !== transaction_date) {
				                        $(rows)
				                            .eq(i)
				                            .before(`
				                            	<tr class="group">
				                            		<td colspan="13">
				                            			${transaction_date}
				                            		</td>
				                            	</tr>
				                            `);
				 
				                        last = transaction_date;
				                    }
				                });

				            
				        	let groups = $('tr.group td');
				        	let grps = $('[class="group"]');
				        	grps.each((index, group) => {
				        		if(!$(group).next().is(':visible')){
				        			$(group).remove();
				        		}
				        	});
				        },
					});
				}
			})
		}

		function initTransactionType(){
			$.ajax({
				url: "{{ route('transactionType.get') }}",
				data: {
					select: "transaction_types.*",
					@if(auth()->user()->role == "Admin")
						where: ['admin_id', {{ auth()->user()->id }}]
					@else(auth()->user()->role == "RHU")
						join: true,
						where: ['r.user_id', {{ auth()->user()->id }}]
					@endif
				},
				success: types => {
					types = JSON.parse(types);
					typeString = "";
					types.forEach(type => {
						console.log(type);
						typeString += `
							<option value="${type.id}">${type.type}</option>
						`;
					});

					$("[name='type']").append(typeString);
					$("[name='type']").select2({
						placeholder: "Select Type"
					});
				}
			})
		}

		function initCategories(){
			$.ajax({
				url: "{{ route('building.getCategories') }}",
				data: {
					select: "buildings.*",
					@if(auth()->user()->role == "Admin")
						where: ['admin_id', {{ auth()->user()->id }}]
					@else(auth()->user()->role == "RHU")
						join: true,
						where: ['r.user_id', {{ auth()->user()->id }}]
					@endif
				},
				success: categories => {
					categories = JSON.parse(categories);
					let categoryString = "";
					console.log(categories);

					categories.forEach(temp => {
						categoryString += `
							<option value="${temp.id}">${temp.name}</option>
						`;
					});

					$('[name="category"]').append(categoryString);
					$('[name="category"]').select2({
						placeholder: "Select Category"
					});
					$('[name="category"]').on('change', temp => {
						category = temp.target.value;
						reload();
					});
				}
			});
		}

		function initLocation(){
			$.ajax({
				url: "{{ route('bhc.get2') }}",
				data: {
					select: "*",
				},
				success: locations => {
					locations = JSON.parse(locations);
					console.log(locations);
					locationString = "";

					locations.forEach(location => {
						locationString += `
							<option value="${location.id}">${location.name}</option>
						`;
					});

					$("[name='location']").append(locationString);
					$("[name='location']").select2({
						placeholder: "Select Location"
					});
				}
			})
		}

		function initTransactionDate(){
			$("[name='transaction_date']").flatpickr({
				altInput: true,
				altFormat: "F j, Y",
				dateFormat: "Y-m-d",
				defaultDate: moment().format("YYYY-MM-DD")
			})
		}

		let footer = `
			<tr style="text-align: right; font-weight: bold;" class="footer">
				<td colspan="5">
					Total
				</td>
				<td id="total" class="center">
					0
				</td>
				<td></td>
			</tr>
			<tr style="text-align: right;" class="footer">
				<td colspan="6">
					<a class="btn btn-success" data-toggle="tooltip" onclick="submit()">
					    SUBMIT
					</a>
				</td>
				<td></td>
			</tr>
		`;

		function addFooter(){
			$("#table2 tbody").append(footer);
		};

		function computeTotal(){
			let items = $('.total');
			let total = 0;

			items.each((index, item) => {
				let parent = $(item).parent().parent();
				let price = parent.find(".price")[0].value;
				let qty = parent.find(".qty")[0].value;

				item.value = toFloat(price * qty);
				total += price * qty;
			});
			
			$('#total').html(toFloat(total));
		}

		// ACTIONS
		// ACTIONS
		// ACTIONS
		// ACTIONS

		function add(id){
			$.ajax({
				url: "{{ route('building.getReorder') }}",
				data: {
					select: "medicines.*",
					where: ["r.id", id]
				},
				success: medicine => {
					medicine = JSON.parse(medicine)[0];
					$('.footer').remove();
					$("#table2 tbody").append(`
						<tr class="item" data-id="${id}">
							<td>${medicine.name}</td>
							<td>
								<input type="text" class="form-control lot_number">
							</td>
							<td>
								<input type="text" class="form-control supplier">
							</td>
							<td>
								<input type="text" class="form-control exp">
							</td>
							<td>
								<input type="number" name="qty${id}" class="form-control qty" value="1" data-id=${id}>
							</td>
							<td>
								<input type="number" name="price${id}" class="form-control price" data-id=${id}>
							</td>
							<td>
								<input type="number" class="form-control total" readonly>
							</td>
							<td>
								<a class="btn btn-danger btn-sm" data-toggle="tooltip" title="Remove" onclick="remove(${id})">
								    <i class="fas fa-trash"></i>
								</a>
							</td>
						</tr>
					`);
					addFooter();
					initListener();
					computeTotal();
				}
			})
		}

		function initListener(){
			$('.qty').unbind('change');
			$('.qty').on("change", qty => {
				qty = $(qty.target);

				if(qty.val() == 0){
					$(qty).parent().parent().remove();
				}
				computeTotal();
			});

			$('.price').unbind('change');
			$('.price').on("change", price => {
				price = $(price.target);

				if(price.val() == 0){
					$(price).parent().parent().remove();
				}
				computeTotal();
			});

			$(".exp").flatpickr({
				altInput: true,
				altFormat: "M j, Y",
				dateFormat: "Y-m-d",
			})
		}

		function remove(id){
			$(`[name="qty${id}"]`).val(0);
			$(`[name="qty${id}"]`).trigger('change');
			$(`[name="price${id}"]`).val(0);
			$(`[name="price${id}"]`).trigger('change');
		}

		function submit(){
			let items = $('.item');

			if(items.length){
				let data = [];
				let bool = false;

				items.each((index, item) => {
					let id = $(item).data('id');

					let temp = {
						medicine_id: id,
						transaction_types_id: $("[name='type']").val(),
						reference: $("[name='reference']").val(),
						particulars: $("[name='particulars']").val(),
						lot_number: $(item).find(".lot_number").val(),
						supplier: $(item).find(".supplier").val(),
						expiry_date: $(item).find(".exp").val(),
						qty: $(item).find(".qty").val(),
						unit_price: $(item).find(".price").val(),
						amount: $(item).find(".total").val(),
						transaction_date: $("[name='transaction_date']").val(),
						@if(auth()->user()->role != "Admin")
						bhc_id: $("[name='location']").val()
						@endif
					};

					console.log(temp);
					Object.keys(temp).forEach(key => {
					    if(temp[key] == null || temp[key] == ""){
					    	bool = true;
					    	return true;
					    }
					})

					data.push(temp);
				});

				if(bool){
					se("Fill all fields");
				}
				else{
					swal.showLoading();
					$.ajax({
						url: "{{ route('data.store') }}",
						data: {
							data: data,
							_token: $('meta[name="csrf-token"]').attr('content')
						},
						type: "POST",
						success: () => {
							ss("Success");
							$('#table2 tbody').html("");
							$('#table2 tbody').append(footer);
							$("[name='type']").val(null).trigger('change');
							$("[name='reference']").val(null).trigger('change');
							$("[name='particulars']").val(null).trigger('change');
							initTransactionDate();
							@if(auth()->user()->role != "Admin")
							$("[name='location']").val(null).trigger('change');
							@endif
							reload();
						}
					});
				}
			}
			else{
				se("No item selected");
			}
		}

		function view(id){
			$.ajax({
				url: '{{ route('data.get') }}',
				data: {
					where: ['id', id],
					select: '*'
				},
				success: tt => {
					tt = JSON.parse(tt)[0];
					showDetails(tt);
				}
			})
		}

		function showDetails(tt){
			Swal.fire({
				html: `
	                ${input("id", "", tt.id, 3, 9, 'hidden')}
	                ${input("transaction_date2", "Date", tt.transaction_date, 3, 9)}
					${input("reference2", "Ref", tt.reference, 3, 9)}
	                ${input("particulars2", "Particulars", tt.particulars, 3, 9)}
	                ${input("qty", "Qty", tt.qty, 3, 9)}
	                ${input("unit_price", "", tt.unit_price, 3, 9, 'hidden')}
				`,
				width: '800px',
				confirmButtonText: 'Update',
				showCancelButton: true,
				cancelButtonColor: errorColor,
				cancelButtonText: 'Cancel',
				didOpen: () => {
					$("[name='transaction_date2']").flatpickr({
						altInput: true,
						altFormat: "F j, Y",
						dateFormat: "Y-m-d",
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
							setTimeout(() => {resolve()}, 500);
			            }

			            bool ? setTimeout(() => {resolve()}, 500) : "";
				    });
				},
			}).then(result => {
				if(result.value){
					swal.showLoading();
					update({
						url: "{{ route('data.update') }}",
						data: {
							id: $("[name='id']").val(),
							transaction_date: $("[name='transaction_date2']").val(),
							reference: $("[name='reference2']").val(),
							particulars: $("[name='particulars2']").val(),
							qty: $("[name='qty']").val(),
							amount: $("[name='qty']").val() * $("[name='unit_price']").val()
						},
						message: "Success"
					},	() => {
						setTimeout(() => {
							viewDataEntry();
						}, 800);
					});
				}
			});
		}
	</script>
@endpush
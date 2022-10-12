@extends('layouts.app')
@section('content')

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <section class="col-lg-12 connectedSortable">

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-1"></i>
                            Building Consumption for the last 15 days
                        </h3>

                        <div style="float: right !important;">
                            Filter By: &nbsp;
                            <select id="building_id" style="width: 200px;">
                                <option value="%%">Select Building / All</option>
                                @foreach($buildings as $building)
                                    <option value="{{ $building->id }}">{{ $building->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="card-body">
                        <canvas id="sales" width="100%"></canvas>
                    </div>

                    <div class="card-footer">
                        <table id="table" class="table table-hover table-">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Start</th>
                                    <th>Payload</th>
                                    <th>End</th>
                                    <th>Payload</th>
                                    <th>Consumption</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td colspan="6" style="text-align: center;">No Data</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-1"></i>
                            Moxa Per Building Consumption for the last 15 days
                        </h3>

                        <div style="float: right !important;">
                            Filter By: &nbsp;
                            <select id="building_id" style="width: 200px;">
                                <option value="%%">Select Building / All</option>
                                @foreach($buildings as $building)
                                    <option value="{{ $building->id }}">{{ $building->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="card-body">
                        <canvas id="deliveredRequests" width="100%"></canvas>
                    </div>
                </div> --}}

            </section>
        </div>
    </div>
</section>

@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <style>
        #table{
            border-bottom: 1px solid;
        }

        #table th, #table td{
            text-align: center !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('js/chart.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>

    <script>
        var ctx, myChart, ctx2, myChart2;
        var building_id = "%%";

        $(document).ready(() => {
            Swal.fire('Loading Data');
            swal.showLoading();

            chart1(building_id);
            // chart2(building_id);
        });

        $('#building_id').select2();
        $('#building_id').change(e => {
            myChart.destroy();
            building_id = e.target.value;
            chart1(building_id);
        });

        function chart1(bid){
            $.ajax({
                url: '{{ route('reading.perBuilding') }}',
                data: {
                    building_id: bid
                },
                success: result =>{
                    result = JSON.parse(result);
                    
                    ctx = document.getElementById('sales').getContext('2d');
                    myChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: result.labels,
                            datasets: result.dataset
                        },
                        options: {
                            scales: {
                                y: {
                                    suggestedMax: result.dataset[0] ? (Math.max.apply(null,result.dataset[0].data) * 1.20) : 0
                                }
                            }
                        }
                    });

                    if(bid != "%%" && result.dataset.length > 0){
                        let values = result.dataset.filter(data => {
                            return data.bid == bid;
                        });
                        values = values[0].values;

                        let string = "";
                        for(i = 0; i < values.length - 1; i++){
                            string += `
                                <tr>
                                    <td>${i+1}</td>
                                    <td>${moment(values[i].date).format('MMM DD, YYYY')}</td>
                                    <td>${values[i].payload}</td>
                                    <td>${moment(values[i+1].date).format('MMM DD, YYYY')}</td>
                                    <td>${values[i+1].payload}</td>
                                    <td>${values[i+1].payload - values[i].payload}</td>
                                </tr>
                            `;
                        }
                        $('#table tbody').html(string);
                    }
                    else{
                        $('#table tbody').html(`
                            <td colspan="6" style="text-align: center;">No Data</td>
                        `);
                    }

                    swal.close();
                }
            })
        }

        function chart2(bid){
            $.ajax({
                url: '{{ route('reading.moxaPerBuilding') }}',
                data: {
                    building_id: bid
                },
                success: result =>{
                    result = JSON.parse(result);
                    
                    ctx2 = document.getElementById('deliveredRequests').getContext('2d');
                    myChart2 = new Chart(ctx2, {
                        type: 'line',
                        data: {
                            labels: result.labels,
                            datasets: result.dataset
                        }
                    });

                    swal.close();
                }
            })
        }
    </script>
@endpush
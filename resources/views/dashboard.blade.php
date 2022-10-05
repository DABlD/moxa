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
                    </div>

                    <div class="card-body">
                        <canvas id="sales" width="100%"></canvas>
                    </div>
                </div>

                <div class="card">
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
                </div>

            </section>
        </div>
    </div>
</section>

@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
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

            chart1();
            chart2(building_id);
        });

        $('#building_id').select2();
        $('#building_id').change(e => {
            myChart2.destroy();
            building_id = e.target.value;
            chart2(building_id);
        });

        function chart1(){
            $.ajax({
                url: '{{ route('reading.perBuilding') }}',
                success: result =>{
                    result = JSON.parse(result);
                    
                    ctx = document.getElementById('sales').getContext('2d');
                    myChart = new Chart(ctx, {
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
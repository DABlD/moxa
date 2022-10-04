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
                            Sales Per RHU
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
                            Delivered Requests
                        </h3>
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
@endpush

@push('scripts')
    <script src="{{ asset('js/chart.min.js') }}"></script>

    <script>
        $(document).ready(() => {
            var ctx, myChart, ctx2, myChart2;

            Swal.fire('Loading Data');
            swal.showLoading();

            // $.ajax({
            //     url: '',
            //     success: result =>{
            //         result = JSON.parse(result);
                    
            //         ctx = document.getElementById('sales').getContext('2d');
            //         myChart = new Chart(ctx, {
            //             type: 'line',
            //             data: {
            //                 labels: result.labels,
            //                 datasets: result.dataset
            //             }
            //         });
            //         swal.close();
            //     }
            // })

            // $.ajax({
            //     url: '',
            //     success: result =>{
            //         result = JSON.parse(result);
                    
            //         ctx = document.getElementById('deliveredRequests').getContext('2d');
            //         myChart = new Chart(ctx, {
            //             type: 'line',
            //             data: {
            //                 labels: result.labels,
            //                 datasets: result.dataset
            //             }
            //         });

            //         swal.close();
            //     }
            // })
        });
    </script>
@endpush
@extends('layouts.app')
@section('content')

<section class="content">
    <div class="container-fluid">
        <div class="row">

            <section class="col-lg-12 connectedSortable">
                <div class="row">
                    <div class="col-md-2">
                        Filter By:
                        <select id="fby" class="form-control">
                            <option value="Daily">Daily</option>
                            <option value="Hourly">Hourly</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        From:<input type="text" id="from">
                    </div>
                    <div class="col-md-2">
                        To:<input type="text" id="to">
                    </div>
                </div>
            </section>

            @foreach($moxas as $moxa)
                <section class="col-lg-6 connectedSortable">
                    <div class="card" data-id="{{ $moxa->id }}">
                        <div class="card-header row">
                            <div class="col-md-6">
                                <h3 class="card-title">
                                    <i class="fas fa-chart-line mr-1"></i>
                                    {{ $moxa->name }} #{{ $moxa->serial }} ({{ $moxa->utility }})
                                </h3>
                            </div>

                            <div class="col-md-2">
                            </div>
                        </div>

                        <div class="card-body">
                            <canvas id="sales{{ $moxa->id }}" width="100%"></canvas>
                        </div>

                        <div class="card-footer">
                            <table id="table{{ $moxa->id }}" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Reading Date</th>
                                        <th>Start</th>
                                        <th>Start<br>Reading</th>
                                        <th>End</th>
                                        <th>End<br>Reading</th>
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
                </section>
            @endforeach
        </div>
    </div>
</section>

@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/flatpickr.min.css') }}">
    <style>
        #table{
            border-bottom: 1px solid;
        }

        #table th, #table td{
            text-align: center !important;
        }

        td{
            padding-top: 3px !important;
            padding-bottom: 3px !important;
            text-align: center !important;
        }

        td, th{
            font-size: 12px !important;
        }

        th{
            vertical-align: middle !important;
            text-align: center;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('js/chart.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/flatpickr.min.js') }}"></script>

    <script>
        var from = moment().subtract(14, 'days').format("YYYY-MM-DD");
        var to = moment().add(1, "day").format("YYYY-MM-DD");
        var fby = "Daily";

        @foreach($moxas as $moxa)
            var ctx{{ $moxa->id }};
            var myChart{{ $moxa->id }};
        @endforeach

        $(document).ready(() => {
            Swal.fire('Loading Data');
            swal.showLoading();

            $("#from").flatpickr({
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "Y-m-d",
                defaultDate: from
            });

            $("#to").flatpickr({
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "Y-m-d",
                defaultDate: to
            });

            refreshCharts();
        });

        $('#from').change(e => {
            if(e.target.value != ""){
                @foreach($moxas as $moxa)
                    myChart{{ $moxa->id }}.destroy();
                @endforeach
                from = $('#from').val();
                refreshCharts();
            }
        });

        $('#to').change(e => {
            if(e.target.value != ""){
                @foreach($moxas as $moxa)
                    myChart{{ $moxa->id }}.destroy();
                @endforeach
                to = $('#to').val();
                refreshCharts();
            }
        });

        $('#fby').change(e => {
            @foreach($moxas as $moxa)
                myChart{{ $moxa->id }}.destroy();
            @endforeach
            fby = $('#fby').val();
            refreshCharts();
        });

        function refreshCharts(){
            @foreach($moxas as $moxa)
                createChart({{ $moxa->id }});
            @endforeach
        }

        function createChart(id){
            $.ajax({
                url: '{{ route('reading.perBuilding') }}',
                data: {
                    moxa_id: id,
                    from: from,
                    to: to,
                    fby: fby
                },
                success: result =>{
                    result = JSON.parse(result);
                    
                    window[`ctx${id}`] = document.getElementById(`sales${id}`).getContext('2d');
                    window[`myChart${id}`] = new Chart(window[`ctx${id}`], {
                        type: 'line',
                        data: {
                            labels: result.labels,
                            datasets: result.dataset
                        },
                        options: {
                            scales: {
                                y: {
                                    suggestedMax: result.dataset[0] ? (Math.max.apply(null,result.dataset[0].data) * 1.10) : 0
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false,
                                }
                            }
                        }
                    });

                    if(result.dataset.length > 0){
                        let values = result.dataset[0].values;
                        let string = "";
                        // MMM DD, YYYY

                        for(i = 0; i < values.length - 2; i++){
                            let consumption = values[i+1].payload - values[i].payload;
                            string += `
                                <tr>
                                    <td>${i+1}</td>
                                    <td>${moment(values[i].created_at).format("MMM DD, YYYY hh:mm A")}</td>
                                    <td>${moment(values[i].date).format('MMM DD, YYYY hh:mm A')}</td>
                                    <td>${values[i].payload}</td>
                                    <td>${moment(values[i+1].date).format('MMM DD, YYYY hh:mm A')}</td>
                                    <td>${values[i+1].payload}</td>
                                    <td>${consumption > 0 ? consumption : 0}</td>
                                </tr>
                            `;
                        }
                        $(`#table${id} tbody`).html(string);
                    }

                    swal.close();
                }
            })
        }
    </script>
@endpush
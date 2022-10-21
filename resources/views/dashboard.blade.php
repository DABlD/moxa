@extends('layouts.app')
@section('content')

<section class="content">
    <div class="container-fluid">
        <div class="row">

            <section class="col-lg-12 connectedSortable">
                <div class="row">
                    <div class="col-md-2">
                        Type:
                        <select id="type" class="form-control">
                            <option value="consumption">Consumption</option>
                            <option value="demand">Demand</option>
                        </select>
                    </div>
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
                        <span class="to">To:</span>
                        <input class="to" type="text" id="to">
                    </div>
                    <div class="col-md-2"></div>
                    <div class="col-md-2" style="text-align: right;">
                        <a class="btn btn-success" onclick="exportReading()">
                            <i class="fas fa-file-excel"></i>
                        </a>
                    </div>
                </div>
            </section>

            @foreach($moxas as $moxa)
                <section class="col-lg-6 connectedSortable">
                    <div class="card" data-id="{{ $moxa->id }}">
                        <div class="card-header row">
                            <div class="col-md-12">
                                <h3 class="card-title" style="width: 100%;">
                                    <i class="fas fa-chart-line mr-1"></i>
                                    {{ $moxa->name }} #{{ $moxa->serial }} ({{ $moxa->utility }})

                                    <div style="float: right;">
                                        <a class="btn btn-danger" {!! $moxa->inDashboard ? "" : "style='display: none;' " !!} id="rmv{{ $moxa->id }}" onclick="hide(this)">
                                            <i class="fas fa-eye-slash"></i>
                                        </a>
                                        <a class="btn btn-success" {!! $moxa->inDashboard ? "style='display: none;' " : "" !!} id="shw{{ $moxa->id }}" onclick="show(this)">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </h3>
                            </div>
                        </div>

                        <div class="card-body" {!! $moxa->inDashboard ? "" : "style='display: none;' " !!}>
                            <canvas id="sales{{ $moxa->id }}" width="100%"></canvas>
                        </div>

                        <div class="card-footer" {!! $moxa->inDashboard ? "" : "style='display: none;' " !!}>
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
        var type = "consumption";

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
            if(fby == "Hourly"){
                $('.to').hide();
            }
            else{
                $('.to').show();
            }
            refreshCharts();
        });

        $('#type').change(e => {
            @foreach($moxas as $moxa)
                myChart{{ $moxa->id }}.destroy();
            @endforeach
            type = $('#type').val();
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
                    fby: fby,
                    type: type
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

                    console.log(result.dataset);
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
                                    <td>${consumption > 0 ? (Math.round(consumption * 100) / 100).toFixed(4) : 0}</td>
                                </tr>
                            `;
                        }
                        $(`#table${id} tbody`).html(string);
                    }

                    swal.close();
                }
            })
        }

        function exportReading(){
            let data = {
                from: from,
                to: to,
                fby: fby,
                type: type
            };

            window.open("/reading/exportPerBuilding?" + $.param(data), "_blank");
        }

        function hide(btn){
            $(btn).parent().find('.btn-success').show();
            $(btn).hide();
            let pDiv = $(btn).parent().parent().parent().parent().parent();
            pDiv.find('.card-body').hide();
            pDiv.find('.card-footer').hide();
        }

        function show(btn){
            $(btn).parent().find('.btn-danger').show();
            $(btn).hide();
            let pDiv = $(btn).parent().parent().parent().parent().parent();
            pDiv.find('.card-body').show();
            pDiv.find('.card-footer').show();
        }
    </script>
@endpush
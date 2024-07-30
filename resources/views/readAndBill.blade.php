<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>{{ "READnBILL " . " | " . $title }}</title>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        <link rel="stylesheet" href="{{ asset('fonts/fontawesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/ionicons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/temposdusmus-bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/icheck-boostrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/overlayScrollbar.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
        <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/flatpickr.min.css') }}">

        <style>
            .sidebar nav-link.active{
                color: black !important;
            }
            .brand-link .brand-image{
                float:  none !important;
            }
        </style>

        <link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
        {{-- <link rel="stylesheet" href="{{ asset('css/datatables-jquery.min.css') }}"> --}}

        <style>
            #table td, #table th{
                text-align: center;
            }

            .container-fluid{
                padding-top: 30vh;
                display: flex;
                justify-content: center;
                align-items: center;
                width: 80%;
            }

            .card-header{
                background-color: #83c8e5;
            }

            #rowasd{
                width: 50%;
            }

            @media (max-width: 479px) {
                .container-fluid{
                    width: 100%;
                }

                #rowasd{
                    width: 100%;
                }
            }
        </style>
    </head>

    <body class="hold-transition sidebar-mini layout-fixed">
        <div class="wrapper">
            <div class="preloader"></div>

            <div class="content-wrapper" style="margin-left: 0px;">
                <section class="content">
                    <div class="container-fluid">

                        <div class="row" id="rowasd">

                            <section class="col-lg-12 connectedSortable">
                                <div class="card">
                                    <div class="card-header" style="text-align: center;">
                                        <h3>
                                            <b>
                                                Read and Bill
                                            </b>
                                        </h3>
                                    </div>

                                    <div class="card-body table-responsive">
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

                                        <div class="row iRow">
                                            <div class="col-md-3 iLabel">
                                                Current Reading
                                            </div>
                                            <div class="col-md-9 iInput">
                                                <input type="text" name="reading" placeholder="Enter Current Reading" class="form-control" value="">
                                            </div>
                                        </div>

                                        <br>

                                        <div class="row iRow">
                                            <div class="col-md-3 iLabel">
                                                Latest Reading
                                            </div>
                                            <div class="col-md-9 iInput">
                                                <div id="last_reading" data-value="0">Select Device First</div>
                                            </div>
                                        </div>

                                        <br>

                                        <div class="row iRow">
                                            <div class="col-md-3 iLabel">
                                                Subscriber Name
                                            </div>
                                            <div class="col-md-9 iInput">
                                                <div id="sName">-</div>
                                            </div>
                                        </div>

                                        <div class="row iRow">
                                            <div class="col-md-3 iLabel">
                                                Subscriber Email
                                            </div>
                                            <div class="col-md-9 iInput">
                                                <div id="sEmail">-</div>
                                            </div>
                                        </div>

                                        <div class="row iRow">
                                            <div class="col-md-3 iLabel">
                                                Subscriber Contact
                                            </div>
                                            <div class="col-md-9 iInput">
                                                <div id="sContact">-</div>
                                            </div>
                                        </div>

                                        <div class="row iRow">
                                            <div class="col-md-3 iLabel">
                                                Subscriber Address
                                            </div>
                                            <div class="col-md-9 iInput">
                                                <div id="sAddress">-</div>
                                            </div>
                                        </div>

                                    </div>


                                    <div class="card-footer">

                                        <div class="row">
                                            <div class="col-md-5"></div>
                                            <div class="col-md-5">
                                                <div class="btn btn-success" onclick="createBilling()">SAVE</div>
                                            </div>
                                            <div class="col-md-2"></div>
                                        </div>
                                    </div>

                                </div>


                            </section>

                        </div>
                    </div>

                </section>
            </div>
        </div>

        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
        <script>$.widget.bridge('uibutton', $.ui.button)</script>
        <script src="{{ asset('js/bootstrap-bundle.min.js') }}"></script>
        <script src="{{ asset('js/moment.min.js') }}"></script>
        <script src="{{ asset('js/temposdusmus-bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/overlayScrollbar.min.js') }}"></script>
        <script src="{{ asset('js/adminlte.min.js') }}"></script>
        <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
        <script src="{{ asset('js/custom.js') }}"></script>
        <script src="{{ asset('js/numeral.min.js') }}"></script>


        <script src="{{ asset('js/datatables.min.js') }}"></script>
        <script src="{{ asset('js/select2.min.js') }}"></script>
        <script src="https://smart-amr.dtic.com.ph/js/flatpickr.min.js"></script>
        {{-- <script src="{{ asset('js/datatables-jquery.min.js') }}"></script> --}}

        <script>
            $(document).ready(()=> {
                $.ajax({
                    url: "{{ route('readAndBill.getDevices') }}",
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
                        url: "{{ route('readAndBill.getLatestReading') }}",
                        data: {
                            select: "*",
                            id: e.target.value
                        },
                        success: data => {
                            if(data != "null"){
                                data = JSON.parse(data);

                                $('#last_reading').html(numeral(data.total).format('0,0'));
                                $('#last_reading').data("value", data.total);

                                $('#sName').html(data.device.subscriber.name);
                                $('#sEmail').html(data.device.subscriber.email);
                                $('#sContact').html(data.device.subscriber.contact);
                                $('#sAddress').html(data.device.subscriber.address);
                            }
                            else{
                                $('#last_reading').html("N/A");

                                $('#sName').html('-');
                                $('#sEmail').html('-');
                                $('#sContact').html('-');
                                $('#sAddress').html('-');
                            }
                        }
                    })
                });
            });

            function createBilling(){
                swal.showLoading();

                let cr = $("[name='reading']").val();

                $.ajax({
                    url: "{{ route('readAndBill.store') }}",
                    type: "POST",
                    data: {
                        moxa_id: $("[name='moxa_id']").val(),
                        total: $("[name='reading']").val(),
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: () => {
                        Swal.fire({
                            icon: "success",
                            title: "Success",
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $("[name='moxa_id']").val('').change();
                        });
                    }
                })
            }
        </script>
    </body>
</html>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ url("/") }}" class="brand-link">
        <center>
            <img src="{{ asset("images/moxa_loga2.jpg") }}" alt="AMR" class="brand-image elevation-3" style="opacity: .8; width: 140px;">
            <span class="brand-text font-weight-light">ㅤ</span>
        </center>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('images/default_avatar.png') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block" id="profile">
                    {{ auth()->user()->name }}
                </a>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @php 
                    $routes = Route::getRoutes();
                    $group = null;
                @endphp

                @foreach($routes as $route)
                    @if(isset($route->defaults['sidebar']))
                        @if(in_array(Auth::user()->role, $route->defaults['roles']) || (isset($route->defaults['sped']) && in_array(auth()->user()->id, $route->defaults['sped'])))
                            
                            @if(isset($route->defaults['group']))
                                @if($group != null && $group != $route->defaults['group'])
                                        </ul>
                                    </li>
                                    @php
                                        $group = null;
                                    @endphp
                                @endif
                                @if($group == null && $group != $route->defaults['group'])
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            <p>
                                                {{ $route->defaults['group'] }}
                                                @php
                                                    $group = $route->defaults['group'];
                                                @endphp
                                                <i class="fas fa-angle-left right"></i>
                                            </p>
                                        </a>
                                        
                                        <ul class="nav nav-treeview">
                                @endif
                            @endif

                            <li class="nav-item">
                                <a class="nav-link {{ request()->path() == $route->uri ? 'active' : '' }}" href="{{ url($route->defaults['href']) }}">
                                    <i class="nav-icon {{ $route->defaults['icon'] }}"></i> 
                                    <p>{{ $route->defaults['name'] }}</p>
                                </a>
                            </li>
                        @endif
                    @endif
                @endforeach
                {{-- <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-table"></i>
                        <p>
                            Tables
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Simple Tables</p>
                            </a>
                        </li>
                    </ul>
                </li> --}}
            </ul>
        </nav>
    </div>
</aside>

@push('scripts')
    <script src="{{ asset('js/flatpickr.min.js') }}"></script>
    <script>
        $('#profile').on('click', () => {
            $.ajax({
                url: "{{ route('user.get') }}",
                data: {
                    select: 'users.*',
                    where: ['id', {{ auth()->user()->id }}]
                },
                success: user => {
                    user = JSON.parse(user)[0];

                    Swal.fire({
                        html: `
                            <div class="row" style="margin-top: 30px;">
                                <div class="col-md-3">
                                    <img src="${user.avatar}" alt="User Avatar" width="200px" height="200px">
                                </div>
                                <div class="col-md-9">
                                    ${input("name", "Name", "{{ auth()->user()->name }}", 2, 10)}
                                    ${input("email", "Email", "{{ auth()->user()->email }}", 2, 10)}
                                    ${input("address", "Address", "{{ auth()->user()->address }}", 2, 10)}
                                    ${input("contact", "Contact #", "{{ auth()->user()->contact }}", 2, 10, "number")}
                                </div>
                            </div"
                        `,
                        width: "1000px",
                        confirmButtonText: 'Update',
                        @if(auth()->user()->name == null && auth()->user()->contact == null)
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                        @else
                            showCancelButton: true,
                            cancelButtonColor: errorColor,
                            cancelButtonText: 'Exit',

                            showDenyButton: true,
                            denyButtonColor: successColor,
                            denyButtonText: 'Change Password',
                        @endif
                    }).then(result => {
                        if(result.value){
                            let data = {
                                id: "{{ auth()->user()->id }}",
                                name: $("[name='name']").val(),
                                email: $("[name='email']").val(),
                                address: $("[name='address']").val(),
                                contact: $("[name='contact']").val(),
                            };

                            data._token = $('meta[name="csrf-token"]').attr('content');

                            $.ajax({
                                url: "{{ route('user.update') }}",
                                data: data,
                                type: "POST",
                                success: result => {
                                    window.location.reload();
                                }
                            });
                        }
                        else if(result.isDenied){
                            Swal.fire({
                                html: `
                                    ${input("password", "Password", null, 5, 7, 'password')}
                                    ${input("password_confirmation", "Confirm Password", null, 5, 7, 'password')}
                                `,
                                confirmButtonText: 'Update',
                                showCancelButton: true,
                                cancelButtonColor: errorColor,
                                cancelButtonText: 'Exit',
                                width: "500px",
                                preConfirm: () => {
                                    swal.showLoading();
                                    return new Promise(resolve => {
                                        setTimeout(() => {
                                            if($('.swal2-container input:placeholder-shown').length){
                                                Swal.showValidationMessage('Fill all fields');
                                            }
                                            else if($("[name='password']").val().length < 8){
                                                Swal.showValidationMessage('Password must at least be 8 characters');
                                            }
                                            else if($("[name='password']").val() != $("[name='password_confirmation']").val()){
                                                Swal.showValidationMessage('Password do not match');
                                            }
                                        resolve()}, 500);
                                    });
                                },
                            }).then(() => {
                                $.ajax({
                                    url: "{{ route('user.updatePassword') }}",
                                    data: {
                                        id: "{{ auth()->user()->id }}",
                                        password: $("[name='password']").val(),
                                        _token: $('meta[name="csrf-token"]').attr('content')
                                    },
                                    type: "POST",
                                    success: result => {
                                        ss("Successfully changed password");
                                    }
                                });
                            });
                        }
                    });
                }
            });
        });

        @if(auth()->user()->name == null && auth()->user()->contact == null)
            $('#profile').click();
        @endif
    </script>
@endpush
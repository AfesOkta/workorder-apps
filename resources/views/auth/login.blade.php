@extends('layouts.master-without-nav')
@section('title')
    Login
@endsection

@section('content')
    <div class="mt-5 mb-4 account-pages pt-sm-5">
        <div class="container">

            <div class="row align-items-center justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card">
                        <div class="p-4 card-body">
                            <div class="mt-2 text-center">
                                <h5 class="text-primary">Welcome Back !</h5>
                                <p class="text-muted">Sign in to continue to {{ config('app.name', 'Erevenue')}} {{date('Y')}}.</p>
                            </div>
                            <div class="p-2 mt-4">
                                <form method="POST" action="{{ route('user.login') }}">
                                    @csrf

                                    <div class="form-group">
                                        <label for="username">{{ __('Username') }}</label>
                                        <input id="username" type="text"
                                            class="form-control @error('username') is-invalid @enderror" name="username"
                                            value="{{ old('username') }}" @if (old('username')) value="{{ old('username') }}" @else value="admin" @endif required autocomplete="username" autofocus placeholder="Enter username">
                                        @error('username')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <div class="float-right">
                                            @if (Route::has('password.request'))
                                                <a class="text-muted" href="{{ route('password.request') }}">
                                                    Forgot password?
                                                </a>
                                            @endif
                                        </div>
                                        <label for="password">{{ __('Password') }}</label>
                                        <input id="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror" name="password"
                                            required autocomplete="current-password" value="123456" placeholder="Enter password">
                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>

                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input form-check-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="remember">Remember me</label>
                                    </div>

                                    <div class="mt-3 text-right">
                                        <button class="btn btn-primary btn-block waves-effect waves-light" type="submit"><i class="mr-1 icon-xs icon" data-feather="log-in"></i> Log In</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                    <div class="mt-5 text-center">
                        <p>© <script>document.write(new Date().getFullYear())</script> {{config('app.name','Erevenue')}}</p>
                    </div>

                </div>
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
@endsection

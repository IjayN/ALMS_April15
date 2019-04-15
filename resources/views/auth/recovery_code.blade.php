<!DOCTYPE html>
<html lang="en">


<head>
	<meta charset="UTF-8">
	<meta content="width=device-width, initial-scale=1" name="viewport" />
	<title>Adrian || Recovery Code</title>
	<!-- Favicon-->
	<link rel="icon" href="{{ asset('assets/images/logo.png') }}" type="image/x-icon">

	<!-- Plugins Core Css -->
	<link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet">

	<!-- Custom Css -->
	<link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" />
	<link href="{{ asset('assets/css/pages/extra_pages.css') }}" rel="stylesheet" />
</head>

<body class="login-page">
<div class="limiter">
	<div class="container-login100">
		<div class="wrap-login101">

			<form class="login100-form validate-form" method="post" action="{{ route('verify-code',$employee->id)}}">
					<span class="login100-form-logo">
						<img alt="" src="{{ asset('assets/images/loading.png') }}">
					</span>
				<span class="login100-form-title p-b-34 p-t-27">
					Enter Recovery Code
					</span>
					{{ csrf_field() }}


					<div class="wrap-input100 validate-input{{ $errors->has('reset_code') ? ' has-error' : '' }}">
						<div class="">
							<input id="text" type="text" class="input100{{ $errors->has('reset_code') ? ' is-invalid' : '' }}" placeholder="Enter Recovery Code" name="reset_code" value="{{ old('reset_code') }}" required autofocus>
							<i class="material-icons focus-input1001">insert_emoticon</i>
							@if ($errors->has('reset_code'))
							<span class="help-block">
								<strong>{{ $errors->first('reset_code') }}</strong>
							</span>
							@endif
						</div>
					</div>

					<div class="wrap-input100 validate-input{{ $errors->has('password') ? ' has-error' : '' }}">
						<div class="">
							<input id="password" type="password" class="input100" placeholder="Password" name="password" required>
							<i class="material-icons focus-input1001">lock</i>
							@if ($errors->has('password'))
							<span class="help-block">
								<strong>{{ $errors->first('password') }}</strong>
							</span>
							@endif
						</div>
					</div>

				<div class="wrap-input100 validate-input" data-validate="Enter password again">
					<input id="password-confirm" name="password_confirmation" required class="input100" type="password" placeholder="Confirm password">
					<i class="material-icons focus-input1001">lock</i>
				</div>



				<div class="container-login100-form-btn">
					<button class="login100-form-btn">
						Reset Password
					</button>
				</div>

				<div class="text-center p-t-50">
					<p class="txt1">
						Contact Administrator if you require assistance
					</p>
				</div>

			</form>


		</div>
	</div>
</div>

<!-- Plugins Js -->

<script src="{{ asset('assets/js/app.min.js') }}"></script>

<!-- Extra page Js -->
<script src="{{ asset('assets/js/pages/examples/pages.js') }}"></script>

</body>


</html>

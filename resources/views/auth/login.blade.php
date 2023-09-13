@extends('layouts.app')
@section('content')
<div class="container">
<div class="row justify-content-center">
<div class="col-md-8">
<div class="card">
<div class="card-header">{{ __('Login') }}</div>
<div class="card-body">
 <form method="POST" action="{{ route('login') }}">
     @csrf
    <div class="form-group row">
    <div class="col-md-6 offset-md-3">
      <a href="{{route('login.google')}}" class="btn btn-danger btn-block">Login with Google</a>
      <a href="{{route('login.facebook')}}" class="btn btn-primary btn-block">Login with Facebook</a>
      <a href="{{route('login.github')}}" class="btn btn-dark btn-block">Login with Github</a>
   </div>
   </div>   
</form>
</div>
</div>
</div>
</div>
</div>
@endsection
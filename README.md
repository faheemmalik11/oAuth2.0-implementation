# oAuth2.0-implementation

* [What Is OAuth?](#what-is-oauth)
* [How does OAuth Work?](#how-does-oauth-work)
* [Setup](#setup)
* [Create Social Login with Socialite](#create-social-login-with-socialite)

## What Is OAuth?
OAuth stands for Open Authorization. OAuth is an open standard for delegating access, commonly utilized as a way for Web clients to allow websites or applications to get to their data on other websites without giving them passwords.

It is a verification convention that permits you to endorse one application interacting with another application on your behalf without giving away your password.

## How does OAuth Work?
OAuth fundamentally grants access tokens to be allotted to third-party clients by an authorization server, with the endorsement of the resource owner, and in turn, the clients use these token to access secured data, facilitated by the resource server.

Let’s look at the scenario of trying to sign in with Google(a service provider) on GitHub(a consumer application). First, GitHub seeks permission from Google by requesting a Client key and Secret. This helps Google verify that GitHub has access to request resources. Once its verified, the user is redirected to Google to decide whether to log in. If the user logs in, he or she gives GitHub permission to use his or her resources stored by the service provider(Google). Thereafter, GitHub (the consumer application) obtains an access token for use in making a request to the protected resource on Google(the service provider). Tokens come with access authorization for the API. These authorizations are called scopes, and each token has an authorized scope for each API. GitHub can access Google only to the degree defined in the scope.

## Setup

### Step 1: Create a new Laravel Project
You can create a new Laravel project via the Composer command or the Laravel installer:

`laravel new project_name  `
or
`composer create-project laravel/laravel project_name`


### Step 2: Connect to your database
Change db credentials in .env file.

### Step 3: Install Laravel/UI
`composer require laravel/ui`
### Step 4 : Create a Bootstrap AUTH Scaffold
A bootstrap authentication scaffold provides a UI and basic authentication for registration and login. You can install it with the Artisan command:
`php artisan ui bootstrap --auth`
###  Step 5: Install NPM Packages
`npm install`
### Step 6: Run NPM environment
`npm run dev`
### Step 7: Update the Login page
Provide the buttons for users to log in with their social accounts on resources\views\auth\login.blade.php.

Note that we put the URL routes in the login buttons.

```sh
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
```
### Step 8: Run the application
You can serve the Laravel application with the following Artisan Command:

`php artisan serve`

### Step 9: Hit this URL on your browser
http://localhost:8000/login
You should be able to view the login page:

Login View

## Create Social Login with Socialite
### Step 1: Install the Laravel Socialite package
Use the Composer package manager to add the package to your project's dependencies:

`composer require laravel/socialite`
### Step 2: Set up routes
Since we are dealing with views, our routes will be in routes\web.php.

To authenticate users using an OAuth provider, you will need two routes: one for redirecting the user to the OAuth provider and another for receiving the callback from the provider after authentication.

```sh
//Google
Route::get('/login/google', [App\Http\Controllers\Auth\LoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/login/google/callback', [App\Http\Controllers\Auth\LoginController::class, 'handleGoogleCallback']);
//Facebook
Route::get('/login/facebook', [App\Http\Controllers\Auth\LoginController::class, 'redirectToFacebook'])->name('login.facebook');
Route::get('/login/facebook/callback', [App\Http\Controllers\Auth\LoginController::class, 'handleFacebookCallback']);
//Github
Route::get('/login/github', [App\Http\Controllers\Auth\LoginController::class, 'redirectToGithub'])->name('login.github');
Route::get('/login/github/callback', [App\Http\Controllers\Auth\LoginController::class, 'handleGithubCallback']);

```

### Step 3: Configure service providers
You will need to add credentials for the OAuth providers your application utilizes. These credentials should be in your application's config/services.php configuration file before using socialite.

```sh
'google' => [
   'client_id' => env('GOOGLE_CLIENT_ID'),
   'client_secret' => env('GOOGLE_CLIENT_SECRET'),
   'redirect' => 'your_redirect_url',
],
'facebook' => [
   'client_id' => env('FACEBOOK_CLIENT_ID'),
   'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
   'redirect' => 'your_redirect_url',
],
'github' => [
   'client_id' => env('GITHUB_CLIENT_ID'),
   'client_secret' => env('GITHUB_CLIENT_SECRET'),
   'redirect' => 'your_redirect_url',
],
```

Note that the redirect value should be the callback URL you set at the Service Providers and at your routes.

### Step 4: Set CLIENT_ID and CLIENT_SECRET in the .env file
CLIENT_ID and CLIENT_SECRET should not be in any public directory, so they should be stored as environment variables.
```sh
  GOOGLE_CLIENT_ID=
  GOOGLE_CLIENT_SECRET=

  FACEBOOK_CLIENT_ID=
  FACEBOOK_CLIENT_SECRET=

  GITHUB_CLIENT_ID=
  GITHUB_CLIENT_SECRET=   
```

Note that after creating each App on the various Service Providers, this is where the CLIENT_ID and CLIENT_SECRET values should be placed.

### Step 5: Set up LoginController
LoginController is at the app\Http\Controllers\Auth\LoginController.php directory. First , let’s define a _registerOrLoginMethod(), which will log in returning users or register new users. This takes place when they are redirected to callback from a successful authentication on the Service Provider.

```sh
protected function _registerOrLoginUser($data){
$user = User::where('email',$data->email)->first();
  if(!$user){
     $user = new User();
     $user->name = $data->name;
     $user->email = $data->email;
     $user->provider_id = $data->id;
     $user->avatar = $data->avatar;
     $user->password = $data->token;
     $user->save();
  }
Auth::login($user);
}
```

Furthermore, we can now define the different methods we set in the routes for the different Service Providers:

```sh

//Google Login
public function redirectToGoogle(){
return Socialite::driver('google')->stateless()->redirect();
}

//Google callback  
public function handleGoogleCallback(){

$user = Socialite::driver('google')->stateless()->user();

  $this->_registerorLoginUser($user);
  return redirect()->route('home');
}

//Facebook Login
public function redirectToFacebook(){
return Socialite::driver('facebook')->stateless()->redirect();
}

//facebook callback  
public function handleFacebookCallback(){

$user = Socialite::driver('facebook')->stateless()->user();

  $this->_registerorLoginUser($user);
  return redirect()->route('home');
}

//Github Login
public function redirectToGithub(){
return Socialite::driver('github')->stateless()->redirect();
}

//github callback  
public function handleGithubCallback(){

$user = Socialite::driver('github')->stateless()->user();

  $this->_registerorLoginUser($user);
  return redirect()->route('home');
}
```

The redirect method provided by the Socialite façade takes care of redirecting the user to the OAuth provider, while the user method will read the incoming request and retrieve the user's information from the provider after they are authenticated.

### Step 6: Create an App for Google, Facebook, and GitHub
Here are few clips that briefly describe how to create an App on the different Service Providers, obtain the OAuth Client_ID and Client_Secret, and put them in an .env file.

Google
Facebook
Twitter
Thereafter , we need to clear the configuration cache since we updated our environment variables.

Use the following Artisan command:

`php artisan config:cache`

### Step 7: Update migrations for the users table
The migrations file is at database\migrations\2014_10_12_000000_create_users_table.php the directory. We need to add columns for provider_id and avatar in the up() method if the user logged in with one of his or her social accounts.
```sh
public function up()
{
  Schema::create('users', function (Blueprint $table) {
  $table->id();
  $table->string('name');
  $table->string('email')->unique();
  $table->string('provider_id')->nullable();
  $table->string('avatar')->nullable();
  $table->timestamp('email_verified_at')->nullable();
  $table->string('password')->nullable();
  $table->rememberToken();
  $table->timestamps();
});
}   
```
        
Now, run the migrations again with this Artisan command:

`php artisan migrate:fresh`

### Step 8: Add the users’ avatars beside their name in the navbar
When a user logs in with his or her social account, we obtain their username and avatar, along with other credentials.

We need to display the avatar beside the username after the user logs in successfully. The nav-item dropdown in the resources\views\layouts\app.blade.php directory will be updated like this:
```sh

<li class="nav-item dropdown">
 <img src="{{Auth::user()->avatar}}" alt="{{Auth::user()->name}}">
   <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" 
   role="button" data-toggle="dropdown" 
   aria-haspopup="true" aria-expanded="false" v-pre>
              {{ Auth::user()->name }}
  </a>

 <div class="dropdown-menu dropdown-menu-right" 
       aria-labelledby="navbarDropdown">
  <a class="dropdown-item" href="{{ route('logout') }}" 
     onclick="event.preventDefault();  
       document.getElementById('logout-form').submit();">
            {{ __('Logout') }}
      </a>
  <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                               @csrf
  </form>
</div>
</li>    
```    
We are done building 👍.
@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Setup POS PIN</h2>
    <form method="POST">
        @csrf
        <input type="password" name="pin" placeholder="Enter 4-digit PIN" maxlength="4" required>
        <input type="password" name="confirm_pin" placeholder="Confirm PIN" maxlength="4" required>
        <button type="submit">Setup PIN</button>
    </form>
</div>
@endsection
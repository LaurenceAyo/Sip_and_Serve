@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Verify POS PIN</h2>
    <form method="POST">
        @csrf
        <input type="password" name="pin" placeholder="Enter your PIN" maxlength="4" required>
        <button type="submit">Verify PIN</button>
    </form>
</div>
@endsection
<h2>Tweestapsverificatie vereist</h2>
<p>Open je authenticator app en voer de code in.</p>

@if ($errors->any())
    <p style="color: red;">{{ $errors->first() }}</p>
@endif

<form method="POST" action="{{ route('2fa.login') }}">
    @csrf
    <input type="text" name="code" placeholder="123456" required autofocus>
    <button type="submit">Inloggen</button>
</form>

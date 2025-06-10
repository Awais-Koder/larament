<h2>New User Registered</h2>
<p>A new user has just registered:</p>

<p><strong>Name:</strong> {{ $user->name }}</p>
<p><strong>Email:</strong> {{ $user->email }}</p>

<p>To review and approve this user please log in as administrator.</p>
<p><a href="{{ route('login') }}" class="text-blue-500 hover:underline">Login as Administrator</a></p>
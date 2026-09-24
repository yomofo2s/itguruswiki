<x-layouts.auth title="Check your inbox" subtitle="One last step before you can write guides.">
    <div class="space-y-5 text-sm text-slate-600">
        <p>We sent a verification link to <strong class="text-slate-900">{{ auth()->user()->email }}</strong>. Click it to activate your account. Don't see it? Check your spam folder.</p>
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="btn-primary w-full">Resend verification email</button>
        </form>
        <form method="POST" action="{{ route('logout') }}" class="text-center">
            @csrf
            <button class="text-sm font-medium text-slate-500 underline hover:text-slate-800">Log out</button>
        </form>
    </div>
</x-layouts.auth>

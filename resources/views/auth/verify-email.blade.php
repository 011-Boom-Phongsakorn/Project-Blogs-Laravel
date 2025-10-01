<x-guest-layout>
    <!-- Icon -->
    <div class="flex justify-center mb-6">
        <div class="w-20 h-20 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
            <svg class="w-10 h-10 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
        </div>
    </div>

    <!-- Title -->
    <h2 class="text-2xl font-bold text-center text-gray-900 dark:text-gray-100 mb-4">
        Verify Your Email
    </h2>

    <!-- Description -->
    <div class="mb-6 text-center text-gray-600 dark:text-gray-400">
        <p class="mb-2">Thanks for signing up!</p>
        <p>Before getting started, please verify your email address by clicking on the link we just emailed to you.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <p class="text-sm text-green-600 dark:text-green-400">
                    A new verification link has been sent to your email address!
                </p>
            </div>
        </div>
    @endif

    <!-- Buttons -->
    <div class="space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-150">
                Resend Verification Email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full px-4 py-2.5 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-all duration-150">
                Log Out
            </button>
        </form>
    </div>

    <!-- Help Text -->
    <div class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
        <p>Didn't receive the email? Check your spam folder or click "Resend".</p>
    </div>
</x-guest-layout>

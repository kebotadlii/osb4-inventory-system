<section>
    <header>
        <h2 class="text-lg font-medium text-red-600">
            Delete Account
        </h2>
    </header>

    <form method="post" action="{{ route('profile.destroy') }}" class="mt-6 space-y-6">
        @csrf
        @method('delete')

        <div>
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" name="password" type="password"
                class="mt-1 block w-full" required />
        </div>

        <x-danger-button>
            Delete Account
        </x-danger-button>
    </form>
</section>

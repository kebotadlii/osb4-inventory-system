<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Update Profile Information
        </h2>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="Name" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                value="{{ old('name', $user->name) }}" required autofocus />
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                value="{{ old('email', $user->email) }}" required />
        </div>

        <x-primary-button>Save</x-primary-button>
    </form>
</section>

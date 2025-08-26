<x-admin-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Jokes Admin') }}
        </h2>
    </x-slot>


    <section class="py-12 mx-12 space-y-4">

        <nav class="flex flex-row justify-between">
            <x-link-primary-button class="bg-gray-500!" href="{{ route('admin.jokes.index') }}">
                All Jokes
            </x-link-primary-button>

        </nav>

        <form class="flex flex-col gap-4"
            method="post"
            action="{{ route('admin.jokes.store') }}"
        >
            @csrf

            <h3>New Jokes Details</h3>
            <div class="flex flex-col gap-1">
                <x-input-label for="title" :value="__('Title')" />

                <x-text-input id="title" class="block mt-1 w-full"
                              type="text"
                              name="title"
                              value="{{ old('title') }}"
                               />

                <x-input-error :messages="$errors->get('title')" class="mt-2" />
            </div>

            <div class="flex flex-col gap-1">
                <x-input-label for="content" :value="__('Content')" />

                <x-text-input id="content" class="block mt-1 w-full"
                              type="text"
                              name="content"
                              value="{{ old('content') }}"  />

                <x-input-error :messages="$errors->get('content')" class="mt-2" />
            </div>

            <div class="flex flex-row justify-start">
                <x-input-label></x-input-label>
                <x-primary-button type="submit" class="mr-6 px-12">
                    <i class="fa-solid fa-save pr-2 text-lg"></i>
                    Save
                </x-primary-button>
                <x-link-secondary-button href="{{ route('admin.jokes.index')}}" class="px-12">
                    <i class="fa-solid fa-cancel pr-2 text-lg"></i>
                    Cancel
                </x-link-secondary-button>
            </div>

        </form>

    </section>

</x-admin-layout>

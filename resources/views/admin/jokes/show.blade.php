<x-admin-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Jokes Admin') }}
        </h2>
    </x-slot>


    <section class="py-12 mx-12 space-y-4">

        <form class="flex flex-col gap-4"
              method="post"
              action="{{ route('admin.jokes.update', $joke) }}"
        >
            @csrf
            @method('delete')

            <h3 class="text-xl my-6">Joke Details</h3>

            <div class="grid grid-cols-12 gap-8">
                <p class="col-span-2 bg-gray-200 p-2">
                    {{ __('Title') }}
                </p>
                <p class="col-span-9 p-2">
                    {{ $joke->title }}
                </p>
            </div>

            <div class="grid grid-cols-12 gap-8">
                <p class="col-span-2 bg-gray-200 p-2">
                    {{ __('Content') }}
                </p>
                <p class="col-span-9 p-2">
                    {{ $joke->content ?? "-"  }}
                </p>
            </div>

            <div class="grid grid-cols-12 gap-8">
                <p class="col-span-2 bg-gray-200 p-2">
                    {{ __('Total Categories') }}
                </p>
                <p class="col-span-9 p-2">
                    {{ $jokes->category_count ?? "-" }}
                </p>
            </div>

            <div class="flex flex-row justify-start">
                <x-link-primary-button href="{{ route('admin.jokes.edit', $joke) }}"
                                       class="mr-6 px-12">
                    <i class="fa-solid fa-edit pr-2 text-lg"></i>
                    {{ __(' Edit') }}
                </x-link-primary-button>

                <x-link-secondary-button href="{{ route('admin.jokes.index') }}"
                                         class="mr-6">
                    {{ __(' All Categories') }}
                </x-link-secondary-button>

                <x-secondary-button type="submit"
                                    class="mr-6">
                    <i class="fa-solid fa-delete-left pr-2 text-lg"></i>
                    {{ __('Delete') }}
                </x-secondary-button>
            </div>

            <h4 class="mt-6 col-span-2 p-2 text-lg">
                {{ __('Categories the Joke belongs to') }}
            </h4>

            <div class="flex-col gap-2">
                @foreach($categories as $category)
                    <div class="px-2 flex flex-col gap-0">
                        <p>
                            <i class="fa-solid fa-comment-dots text-sm pr-1"></i>
                            {{ $category->title }}
                        </p>
                        <p class="text-sm text-gray-500 px-6">
                            {{ Str::limit(strip_tags(html_entity_decode($category->description)), 40) }}
                        </p>
                    </div>
                @endforeach
            </div>

        </form>

    </section>

</x-admin-layout>

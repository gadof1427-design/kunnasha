{{-- Profile photo, or the first letter of the name when there is none. --}}
@props(['user', 'size' => 'h-10 w-10 text-base'])

<span {{ $attributes->merge(['class' => "inline-flex shrink-0 items-center justify-center overflow-hidden rounded-full bg-[#7c0221] font-bold text-[#fff8cf] {$size}"]) }}>
    @if ($user->profile_photo_path)
        <img src="{{ $user->profilePhotoUrl() }}" alt="" class="h-full w-full object-cover" loading="lazy">
    @else
        {{ mb_substr($user->name, 0, 1) }}
    @endif
</span>

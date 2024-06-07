<div>
    <x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
        <div class="bg-white shadow" style="padding: 1rem 18px; aspect-ratio: 210 / 297;" contenteditable="true">
            {{ $soal ?? '' }}
        </div>
    </x-dynamic-component>
</div>

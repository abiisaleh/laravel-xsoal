<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div>
        <ol style="margin-left: 1rem">
            @foreach ($getState() as $item)
                @if ($item['pertanyaan'])
                    <li type="1" style="margin-top: .5rem">{{ $item['pertanyaan'] }}</li>
                    <img src="{{ '/storage/' . $item['gambar'] }}" alt="{{ $item['gambar'] }}" width="250">
                    @if (isset($item['opsi']))
                        <ol style="margin-left: 1.2rem; margin-top: .15rem">
                            @foreach ($item['opsi'] as $opsi)
                                @if ($opsi)
                                    <li type="a">{{ $opsi }}</li>
                                @endif
                            @endforeach
                        </ol>
                    @endif
                @endif
            @endforeach
        </ol>
    </div>
</x-dynamic-component>

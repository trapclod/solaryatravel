@extends('layouts.admin')

@section('title', 'Modifica ' . $catamaran->name)

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        {{-- Header --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.catamarans.show', $catamaran) }}" 
               class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Modifica {{ $catamaran->name }}</h1>
                <p class="text-gray-600">Aggiorna le informazioni del catamarano</p>
            </div>
        </div>

        {{-- Images Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Immagini</h2>
            
            @if($catamaran->images->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    @foreach($catamaran->images as $image)
                        <div class="relative group aspect-video bg-gray-100 rounded-lg overflow-hidden">
                            <img src="{{ Storage::url($image->path) }}" 
                                 alt="{{ $image->filename }}"
                                 class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <form action="{{ route('admin.catamarans.images.delete', [$catamaran, $image]) }}" 
                                      method="POST"
                                      onsubmit="return confirm('Eliminare questa immagine?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-500 text-white rounded-full hover:bg-red-600">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('admin.catamarans.images.upload', $catamaran) }}" 
                  method="POST" 
                  enctype="multipart/form-data"
                  class="border-2 border-dashed border-gray-200 rounded-lg p-6 text-center hover:border-primary-300 transition-colors">
                @csrf
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="text-gray-500 mb-3">Trascina le immagini qui o clicca per selezionarle</p>
                <input type="file" 
                       name="images[]" 
                       multiple 
                       accept="image/jpeg,image/png,image/jpg,image/webp"
                       class="hidden" 
                       id="images-input"
                       onchange="this.form.submit()">
                <label for="images-input" 
                       class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors cursor-pointer">
                    Seleziona Immagini
                </label>
                <p class="text-xs text-gray-400 mt-2">JPEG, PNG, WebP - Max 5MB</p>
            </form>
        </div>

        <form action="{{ route('admin.catamarans.update', $catamaran) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            @include('admin.catamarans._form')

            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <form action="{{ route('admin.catamarans.destroy', $catamaran) }}" 
                      method="POST"
                      onsubmit="return confirm('Sei sicuro di voler eliminare questo catamarano?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2 text-red-600 hover:text-red-700 font-medium">
                        Elimina Catamarano
                    </button>
                </form>

                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.catamarans.show', $catamaran) }}" 
                       class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Annulla
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                        Salva Modifiche
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

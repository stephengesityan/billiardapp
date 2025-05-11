@extends('layouts.super-admin')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Edit Venue') }}</div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('superadmin.venue.update', $venue->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="form-group row mb-3">
                                <label for="name"
                                    class="col-md-4 col-form-label text-md-right">{{ __('Nama Venue') }}</label>
                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                        name="name" value="{{ old('name', $venue->name) }}" required autocomplete="name"
                                        autofocus>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="address"
                                    class="col-md-4 col-form-label text-md-right">{{ __('Alamat') }}</label>
                                <div class="col-md-6">
                                    <textarea id="address" class="form-control @error('address') is-invalid @enderror"
                                        name="address" required>{{ old('address', $venue->address) }}</textarea>
                                    @error('address')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="phone"
                                    class="col-md-4 col-form-label text-md-right">{{ __('Nomor Telepon') }}</label>
                                <div class="col-md-6">
                                    <input id="phone" type="text" class="form-control @error('phone') is-invalid @enderror"
                                        name="phone" value="{{ old('phone', $venue->phone) }}" required>
                                    @error('phone')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="description"
                                    class="col-md-4 col-form-label text-md-right">{{ __('Deskripsi') }}</label>
                                <div class="col-md-6">
                                    <textarea id="description"
                                        class="form-control @error('description') is-invalid @enderror" name="description"
                                        rows="4" required>{{ old('description', $venue->description) }}</textarea>
                                    @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="open_time"
                                    class="col-md-4 col-form-label text-md-right">{{ __('Jam Buka') }}</label>
                                <div class="col-md-6">
                                    <input id="open_time" type="time"
                                        class="form-control @error('open_time') is-invalid @enderror" name="open_time"
                                        value="{{ old('open_time', $venue->open_time_formatted) }}" required>
                                    @error('open_time')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="close_time"
                                    class="col-md-4 col-form-label text-md-right">{{ __('Jam Tutup') }}</label>
                                <div class="col-md-6">
                                    <input id="close_time" type="time"
                                        class="form-control @error('close_time') is-invalid @enderror" name="close_time"
                                        value="{{ old('close_time', $venue->close_time_formatted) }}" required>
                                    @error('close_time')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="image"
                                    class="col-md-4 col-form-label text-md-right">{{ __('Gambar Venue') }}</label>
                                <div class="col-md-6">
                                    @if($venue->image)
                                        <div class="mb-2">
                                            <img src="{{ $venue->image_url }}" alt="{{ $venue->name }}" class="img-thumbnail"
                                                style="max-height: 150px;">
                                        </div>
                                    @endif
                                    <input id="image" type="file" class="form-control @error('image') is-invalid @enderror"
                                        name="image" accept="image/*">
                                    <small class="form-text text-muted">Format: JPG, PNG, GIF. Ukuran maksimal: 2MB. Biarkan
                                        kosong jika tidak ingin mengubah gambar.</small>
                                    @error('image')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <label for="status" class="col-md-4 col-form-label text-md-right">{{ __('Status') }}</label>
                                <div class="col-md-6">
                                    <select id="status" class="form-control @error('status') is-invalid @enderror"
                                        name="status" required>
                                        <option value="active" {{ old('status', $venue->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                                        <option value="inactive" {{ old('status', $venue->status) == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                    </select>
                                    @error('status')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Perbarui') }}
                                    </button>
                                    <a href="{{ route('superadmin.venue.index') }}" class="btn btn-secondary">
                                        {{ __('Batal') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
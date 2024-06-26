@extends('layout.main')
@section('nama-bengkel'){{ $bengkel_name }}@endsection
@section('container')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Form Uji Emisi</h1>
    </div>

    <div class="col-lg-11">
        <form method="post" action="/dashboard/ujiemisi/insert" id="formKendaraanUjiEmisi">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3 row">
                        <label for="nopol" class="col-sm-4 col-form-label" >No Polisi<span class="text-danger">*</span></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control @error('nopol') is-invalid @enderror" id="nopol" name="nopol" required autofocus value="{{ old('nopol', $kendaraan->nopol ?? "") }}">
                            @error('nopol')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="merk" class="col-sm-4 col-form-label">Merk<span class="text-danger">*</span></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control @error('merk') is-invalid @enderror" id="merk" name="merk" required value="{{ old('merk', $kendaraan->merk ?? "") }}">
                            @error('merk')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>
                      </div>
                    <div class="mb-3 row">
                      <label for="tipe" class="col-sm-4 col-form-label">Tipe<span class="text-danger">*</span></label>
                      <div class="col-sm-8">
                          <input type="text" class="form-control @error('tipe') is-invalid @enderror" id="tipe" name="tipe" required value="{{ old('tipe', $kendaraan->tipe ?? "") }}">
                          @error('tipe')
                              <div class="invalid-feedback">
                                  {{ $message }}
                              </div>
                          @enderror

                      </div>
                    </div>
                    <div class="mb-3 row">
                      <label for="tahun" class="col-sm-4 col-form-label">Tahun<span class="text-danger">*</span></label>
                      <div class="col-sm-8">
                          <input type="number" class="form-control @error('tahun') is-invalid @enderror" id="tahun" name="tahun" required value="{{ old('tahun', $kendaraan->tahun ?? "") }}">
                          @error('tahun')
                              <div class="invalid-feedback">
                                  {{ $message }}
                              </div>
                          @enderror

                      </div>

                    </div>
                    <!-- Add other fields for the left column -->
                </div>
                <div class="col-md-6">
                    <div class="mb-3 row">
                        <label for="cc" class="col-sm-4 col-form-label">CC<span class="text-danger">*</span></label>
                        <div class="col-sm-8">

                            <input type="number" class="form-control @error('cc') is-invalid @enderror" id="cc" name="cc" required value="{{ old('cc', $kendaraan->cc ?? "") }}">
                            @error('cc')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="no_rangka" class="col-sm-4 col-form-label">No Rangka (VIN)</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control @error('no_rangka') is-invalid @enderror" id="no_rangka" name="no_rangka" value="{{ old('no_rangka', $kendaraan->no_rangka ?? "") }}">
                            @error('no_rangka')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    



                    <div class="mb-3 row">
                        <label for="no_mesin" class="col-sm-4 col-form-label" focus>No Mesin</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control @error('no_mesin') is-invalid @enderror" id="no_mesin" name="no_mesin" value="{{ old('no_mesin', $kendaraan->no_mesin ?? "") }}">
                            @error('no_mesin')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            
                        </div>
                    </div>

                    <div class="row mb-3">

                        <div class="col-sm-4">
                            <label class="form-label">Kategori<span class="text-danger">*</span></label>
                        </div>
                        <div class="col-sm-8">
                            <select class="form-select" name="kendaraan_kategori">
                                @php
                                    $counter = 1;
                                @endphp
                                @foreach(['Angkutan Orang', 'Angkutan Barang', 'Angkutan Gandengan', 'Sepeda Motor 2 Tak', 'Sepeda Motor 4 Tak'] as $kategori)
                                    @if (old('kendaraan_kategori') == $counter || ((isset($kendaraan) && isset($kendaraan->kendaraan_kategori) && $kendaraan->kendaraan_kategori == $kategori)))
                                        <option selected value="{{ $counter }}">{{ $kategori }}</option>    
                                    @else
                                        <option value="{{ $counter }}">{{ $kategori }}</option>
                                    @endif
                                    @php
                                        $counter++;
                                    @endphp
                                @endforeach
                            </select>
                        </div>
                    </div>



                    
                    <div class="mb-3 row">
                        <div class="col-sm-4">
                            <label class="form-label">Bahan Bakar<span class="text-danger">*</span></label>
                        </div>
                        <div class="col-sm-8">
                            @foreach(['Bensin', 'Solar', 'Gas'] as $bahan_bakar)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input @error('bahan_bakar') is-invalid @enderror" type="radio" name="bahan_bakar" id="{{ $bahan_bakar }}" value="{{ $bahan_bakar }}" {{ old('bahan_bakar', $kendaraan->bahan_bakar ?? '') == $bahan_bakar ? 'checked' : '' }}>
                                    <label class="form-check-label" for="{{ $bahan_bakar }}">{{ $bahan_bakar }}</label>
                                </div>
                            @endforeach
                        </div>                        
                        @error('bahan_bakar')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                </div>
            </div>

        <hr class="hr mt-0" />

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3 row">
                        <label for="odometer" class="col-sm-4 col-form-label">Odometer (KM)<span class="text-danger">*</span></label>
                        <div class="col-sm-8">
                            <input type="number" class="form-control @error('odometer') is-invalid @enderror" id="odometer" name="odometer" required value="{{ old('odometer') }}">
                            @error('odometer')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="co" class="col-sm-4 col-form-label">CO (%)<span class="text-danger">*</span></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control @error('co') is-invalid @enderror" id="co" name="co" required value="{{ old('co') }}">
                            @error('co')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>
                    </div>
                    <div class="mb-3 row">
                      <label for="hc" class="col-sm-4 col-form-label">HC (ppm)<span class="text-danger">*</span></label>
                      <div class="col-sm-8">
                          <input type="text" class="form-control @error('hc') is-invalid @enderror" id="hc" name="hc" required value="{{ old('hc') }}">
                          @error('hc')
                              <div class="invalid-feedback">
                                  {{ $message }}
                              </div>
                          @enderror

                      </div>
                    </div>
                    <div class="mb-3 row">
                      <label for="opasitas" class="col-sm-4 col-form-label">Opasitas</label>
                      <div class="col-sm-8">
                          <input type="text" class="form-control @error('opasitas') is-invalid @enderror" id="opasitas" name="opasitas" value="{{ old('opasitas') }}">
                          @error('opasitas')
                              <div class="invalid-feedback">
                                  {{ $message }}
                              </div>
                          @enderror

                      </div>
                    </div>
                    <div class="mb-3 row">
                      <label for="co2" class="col-sm-4 col-form-label">CO2</label>
                      <div class="col-sm-8">
                          <input type="text" class="form-control @error('co2') is-invalid @enderror" id="co2" name="co2" value="{{ old('co2') }}">
                          @error('co2')
                              <div class="invalid-feedback">
                                  {{ $message }}
                              </div>
                          @enderror

                      </div>
                    </div>
                    <!-- Add other fields for the left column -->
                </div>
                <div class="col-md-6">
                    <div class="mb-3 row">
                        <label for="co_koreksi" class="col-sm-4 col-form-label">Co Koreksi (%)</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control @error('co_koreksi') is-invalid @enderror" id="co_koreksi" name="co_koreksi"  value="{{ old('co_koreksi') }}">
                            @error('co_koreksi')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="o2" class="col-sm-4 col-form-label" focus>O2 (%)</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control @error('o2') is-invalid @enderror" id="o2" name="o2"  value="{{ old('o2') }}">
                            @error('o2')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="putaran" class="col-sm-4 col-form-label">Putaran (putaran)</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control @error('putaran') is-invalid @enderror" id="putaran" name="putaran" value="{{ old('putaran') }}">
                            @error('putaran')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>
                    </div>
                    <div class="mb-3 row">
                      <label for="temperatur" class="col-sm-4 col-form-label">Temperatur (C)</label>
                      <div class="col-sm-8">
                          <input type="text" class="form-control @error('temperatur') is-invalid @enderror" id="temperatur" name="temperatur"value="{{ old('temperatur') }}">
                          @error('temperatur')
                              <div class="invalid-feedback">
                                  {{ $message }}
                              </div>
                          @enderror

                    </div>
                    </div>
                    <div class="mb-3 row">
                      <label for="lambda" class="col-sm-4 col-form-label">Lambda</label>
                      <div class="col-sm-8">
                          <input type="text" class="form-control @error('lambda') is-invalid @enderror" id="lambda" name="lambda" value="{{ old('lambda') }}">
                          @error('lambda')
                              <div class="invalid-feedback">
                                  {{ $message }}
                              </div>
                          @enderror

                      </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Insert Hasil Uji</button>
        </form>
    </div>

    <script>
        @if (isset($kendaraan) && $kendaraan)
            // Jika ada data kendaraan, atur fokus ke input odometer
            document.getElementById('odometer').focus();
        @else
            // Jika tidak ada data kendaraan, atur fokus ke input nopol
            document.getElementById('nopol').focus();
        @endif
    </script>
    
    {{-- <script>
        // pengkondisian disabled form berdasarkan input bahan bakar
        // Mendapatkan referensi ke input radio
        const bahanBakarInputs = document.querySelectorAll('input[name="bahan_bakar"]');
    
        // Mendengarkan perubahan pada input radio
        bahanBakarInputs.forEach(input => {
            input.addEventListener('change', function() {
                const bahanBakar = this.value;
                // Periksa nilai input radio yang dipilih dan atur status disabled pada input yang sesuai
                if (bahanBakar === 'Bensin') {
                    document.getElementById('opasitas').disabled = true;
                    document.getElementById('co').disabled = false;
                    document.getElementById('hc').disabled = false;
                } else if (bahanBakar === 'Solar') {
                    document.getElementById('opasitas').disabled = false;
                    document.getElementById('co').disabled = true;
                    document.getElementById('hc').disabled = true;
                } else if (bahanBakar === 'Gas') {
                    document.getElementById('opasitas').disabled = false;
                    document.getElementById('co').disabled = false;
                    document.getElementById('hc').disabled = false;
                }
            });
        });
    </script> --}}

    <script>
        // Mendapatkan referensi ke input radio
        const bahanBakarInputs = document.querySelectorAll('input[name="bahan_bakar"]');
        
        // Mendengarkan perubahan pada input radio
        bahanBakarInputs.forEach(input => {
            input.addEventListener('change', function() {
                const bahanBakar = this.value;
                aturStatusInput(bahanBakar);
            });
        });
    
        // Pengecekan bahan bakar awal saat halaman dimuat
        window.addEventListener('DOMContentLoaded', function() {
            // Mendapatkan bahan bakar yang sudah ada sebelumnya
            const bahanBakarTerpilih = document.querySelector('input[name="bahan_bakar"]:checked');
            if (bahanBakarTerpilih) {
                const bahanBakar = bahanBakarTerpilih.value;
                aturStatusInput(bahanBakar);
            }
        });
    
        // Fungsi untuk mengatur status input berdasarkan bahan bakar
        function aturStatusInput(bahanBakar) {
            // Periksa nilai input radio yang dipilih dan atur status disabled pada input yang sesuai
            if (bahanBakar === 'Bensin') {
                document.getElementById('opasitas').disabled = true;
                document.getElementById('co').disabled = false;
                document.getElementById('hc').disabled = false;
            } else if (bahanBakar === 'Solar') {
                document.getElementById('opasitas').disabled = false;
                document.getElementById('co').disabled = true;
                document.getElementById('hc').disabled = true;
            } else if (bahanBakar === 'Gas') {
                document.getElementById('opasitas').disabled = false;
                document.getElementById('co').disabled = false;
                document.getElementById('hc').disabled = false;
            }
        }
    </script>
    

@endsection




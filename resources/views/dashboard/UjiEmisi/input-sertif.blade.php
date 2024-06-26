@extends('layout.main')

{{-- @section('nama-bengkel'){{ $bengkel_name }}@endsection --}}

@section('container')

    @if (session()->has('success'))
    <div class="alert alert-success alert-dismissible fade show col-lg-11" role="alert" >
        {{ session('success') }}
        {{-- <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> --}}
    </div>
    @endif



    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Isi Nomor Seri Tanda Lulus</h1>
    </div>


    <div class="col-lg-6">
        <table class="table">
            <tbody>
                <tr>
                  <th>Nomor Polisi</th>
                    {{-- <td>{{ $kendaraan["nopol"] }}</td> --}}
    
                    <td>{{ $ujiemisi->kendaraan->nopol }}</td>
                </tr>
                <tr>
                  <th>Tanggal Uji</th>
                    <td>{{ $tanggal_uji }}</td>
                </tr>
                <tr>
                    <th>No Seri Tanda Lulus</th>
                      {{-- <td>{{ $kendaraan["tipe"] }}</td> --}}
                    <td>
                        <div class="row ms-1 px-0">
                            <form method="post" class="mx-0" action="/dashboard/ujiemisi/input-sertif/{{ $ujiemisi->id }}/input-nomor/submit-nomor">
                                @method('put')
                                @csrf
                                <div class="row">
                                    <div class="col-lg-7 px-0 my-0">
                                        <input type="text" class="px-2 form-control custom-placeholder2 rounded-left-bottom" name="no_sertifikat" placeholder="Contoh: AA123456">    
                                    </div>
                                    <div class="col-lg-5 px-0">
                                        <button type="submit" class="btn btn-primary rounded-right-bottom">Simpan <i class="fa fa-pencil-square-o"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

<script src="https://kit.fontawesome.com/467dee4ab4.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


@endsection
    
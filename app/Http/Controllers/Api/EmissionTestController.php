<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\UjiEmisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmissionTestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'meta' => [
                'code' => 200,
                'message' => 'Ok',
            ],
            'data' => UjiEmisi::where('user_id', auth()->id())->get()
        ];
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'nopol' => 'required',
            'merk' => '',
            'tipe' => '',
            'tahun' => 'gt:1900',
            'cc' => 'gt:100',
            'no_rangka' => '',
            'no_mesin' => '',
            'kendaraan_kategori' => '',
            'bahan_bakar' => '',
            'odometer' => 'required',
            'co' => 'numeric|between:0,9.99',
            'hc' => 'integer|between:0,9999',
            'opasitas' => 'integer|between:0,100',
            'co2' => 'nullable|numeric|between:0,19.9',
            'co_koreksi' => 'nullable|numeric|between:0,9.99',
            'o2' => 'nullable|numeric|between:0,25',
            'putaran' => 'nullable|integer|between:300,9990',
            'temperatur' => 'nullable|numeric|between:10,150',
            'lambda' => 'nullable|numeric|between:0.5,5',
        ], [
            'nopol.required' => 'Nopol harus diisi',
            'tahun.gt' => 'Tahun kendaraan harus lebih besar dari 1900',
            'cc.gt' => 'Kapasitas mesin (CC) harus lebih besar dari 100',
            'odometer.required' => 'Odometer kendaraan harus diisi',
            'co.numeric' => 'Nilai CO harus berupa angka',
            'co.between' => 'Nilai CO harus antara 0 sampai 9.99',
            'hc.integer' => 'HC harus berupa bilangan bulat',
            'hc.between' => 'HC harus berada di antara 0 sampai 9999',
            'opasitas.numeric' => 'Nilai opasitas harus berupa angka',
            'opasitas.between' => 'Nilai opasitas harus antara 0 sampai 9.99',
            'co2.numeric' => 'Nilai CO2 harus berupa angka',
            'co2.between' => 'Nilai CO2 harus antara 0 sampai 19.9',
            'co_koreksi.numeric' => 'Nilai CO koreksi harus berupa angka',
            'co_koreksi.between' => 'Nilai CO koreksi harus antara 0 sampai 9.99',
            'o2.numeric' => 'Nilai O2 harus berupa angka',
            'o2.between' => 'Nilai O2 harus antara 0 sampai 25',
            'putaran.integer' => 'Putaran mesin harus berupa bilangan bulat',
            'putaran.between' => 'Putaran mesin harus antara 300 sampai 9990',
            'temperatur.numeric' => 'Suhu oli harus berupa angka',
            'temperatur.between' => 'Suhu oli harus antara 10 sampai 150',
            'lambda.numeric' => 'Nilai Lambda harus berupa angka',
            'lambda.between' => 'Nilai Lambda harus antara 0.5 sampai 5',
        ]);
        $kendaraan = Kendaraan::where('nopol', $request->nopol)->first();
        if (is_null($kendaraan)) {
            $valid->addRules([
                'merk' => 'required',
                'tipe' => 'required',
                'cc' => 'required',
                'tahun' => 'required',
                'kendaraan_kategori' => 'required',
                'no_rangka' => 'required',
                'no_mesin' => 'required',
                'bahan_bakar' => 'required'
            ]);
        }

        if ($valid->fails()) return response()->json([
            'meta' => [
                'status' => 422,
                'message' => $valid->getException()
            ],
            'data' => $valid->messages()->toArray()
        ]);

        $valid = $valid->validate();
        // Jika kendaraan belum ada, buat baru
        if (!$kendaraan) {
            $valid['user_id'] = auth()->id();
            $kendaraan = Kendaraan::create($valid);
        }

        // Tambahkan data uji emisi dengan kendaraan yang ada (baik yang sudah ada atau baru dibuat)
        $ujiEmisiData = $valid;
        $ujiEmisiData['user_id'] = auth()->id();
        $ujiEmisiData['kendaraan_id'] = $kendaraan->id;
        $ujiemisi = UjiEmisi::create($ujiEmisiData);
        return response()->json([
            'meta' => [
                'status' => 200,
                'message' => 'Uji emisi berhasil ditambahkan'
            ],
            'data' => $ujiemisi
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function vehicle(Kendaraan $vehicle)
    {
        return response()->json([
            'meta' => [
                'status' => 200,
                'message' => 'Ok'
            ],
            'data' => $vehicle
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UjiEmisi $emissionTest)
    {
        $valid = Validator::make($request->all(), [
            'odometer' => 'required',
            'co' => 'numeric|between:0,9.99',
            'hc' => 'integer|between:0,9999',
            'opasitas' => 'integer|between:0,100',
            'co2' => 'nullable|numeric|between:0,19.9',
            'co_koreksi' => 'nullable|numeric|between:0,9.99',
            'o2' => 'nullable|numeric|between:0,25',
            'putaran' => 'nullable|integer|between:300,9990',
            'temperatur' => 'nullable|numeric|between:10,150',
            'lambda' => 'nullable|numeric|between:0.5,5',
        ], [
            'odometer.required' => 'Odometer kendaraan harus diisi',
            'co.numeric' => 'Nilai CO harus berupa angka',
            'co.between' => 'Nilai CO harus antara 0 sampai 9.99',
            'hc.integer' => 'HC harus berupa bilangan bulat',
            'hc.between' => 'HC harus berada di antara 0 sampai 9999',
            'opasitas.numeric' => 'Nilai opasitas harus berupa angka',
            'opasitas.between' => 'Nilai opasitas harus antara 0 sampai 9.99',
            'co2.numeric' => 'Nilai CO2 harus berupa angka',
            'co2.between' => 'Nilai CO2 harus antara 0 sampai 19.9',
            'co_koreksi.numeric' => 'Nilai CO koreksi harus berupa angka',
            'co_koreksi.between' => 'Nilai CO koreksi harus antara 0 sampai 9.99',
            'o2.numeric' => 'Nilai O2 harus berupa angka',
            'o2.between' => 'Nilai O2 harus antara 0 sampai 25',
            'putaran.integer' => 'Putaran mesin harus berupa bilangan bulat',
            'putaran.between' => 'Putaran mesin harus antara 300 sampai 9990',
            'temperatur.numeric' => 'Suhu oli harus berupa angka',
            'temperatur.between' => 'Suhu oli harus antara 10 sampai 150',
            'lambda.numeric' => 'Nilai Lambda harus berupa angka',
            'lambda.between' => 'Nilai Lambda harus antara 0.5 sampai 5',
        ]);

        if ($valid->fails()) return response()->json([
            'meta' => [
                'status' => 422,
                'message' => $valid->getException()
            ],
            'data' => $valid->messages()->toArray()
        ]);

        // Tambahkan data uji emisi dengan kendaraan yang ada (baik yang sudah ada atau baru dibuat)
        $ujiEmisiData = $valid->validate();
        $ujiEmisiData['user_id'] = auth()->id();
        $emissionTest->update($ujiEmisiData);
        return response()->json([
            'meta' => [
                'status' => 200,
                'message' => 'Uji emisi berhasil diubah'
            ],
            'data' => $emissionTest
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UjiEmisi $emissionTest)
    {
        $data = $emissionTest->toArray();
        $emissionTest->delete();
        return response()->json([
            'meta' => [
                'status' => 200,
                'message' => 'Uji emisi berhasil dihapus'
            ],
            'data' => $data
        ]);
    }
}

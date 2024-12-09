<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\UjiEmisi;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Support\Facades\Session;

// use Illuminate\Support\Facades\Session;
use Illuminate\Support\HtmlString;


class KendaraanUjiEmisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Kendaraan $kendaraan, UjiEmisi $ujiemisi)
    {
        // dd($ujiemisi);
        // dd($kendaraan);
        return view('dashboard.UjiEmisi.insert-uji', [
            "bengkel_name" => auth()->user()->bengkel_name,
            "kendaraan" => $kendaraan,
            "ujiemisi" => $ujiemisi,
        ]);
    }


    public function showCreateFormWithKendaraan($kendaraan_id)
    {

        $kendaraan = Kendaraan::findOrFail($kendaraan_id);
        // dd($kendaraan);

        return view('dashboard.UjiEmisi.insert-uji', [
            "bengkel_name" => auth()->user()->bengkel_name,
            'kendaraan' => $kendaraan
        ]);
    }

    public function showInputSertifikat($ujiemisi_id)
    {
        // $ujiemisi =
        $ujiemisiLulus = UjiEmisi::findOrFail($ujiemisi_id);
        // dd($ujiemisiLulus);
        $tanggal = Carbon::parse($ujiemisiLulus->tanggal_uji)->locale('id')->translatedFormat('l, d F Y');


        return view('dashboard.UjiEmisi.input-sertif', [
            "ujiemisi" => $ujiemisiLulus,
            "tanggal_uji" => $tanggal,
        ]);
    }

    public function inputSertifikat(Request $request, $ujiemisi_id)
    {
        // $ujiemisi = UjiEmisi::findOrFail($ujiemisi_id);
        $ujiemisi = UjiEmisi::findOrFail($ujiemisi_id);
        $validatedData = $request->validate([
            'no_sertifikat' => 'required|unique:uji_emisis,no_sertifikat,' . $ujiemisi_id,
        ], [
            'no_sertifikat.required' => 'Nomor seri tanda lulus harus diisi.',
            'no_sertifikat.unique' => 'Nomor seri tanda lulus telah digunakan.',
        ]);

        $ujiemisi->update($validatedData);

        $message = new HtmlString("Kendaraan dengan nomor polisi {$ujiemisi->kendaraan->nopol} dinyatakan <strong>lulus</strong> uji emisi");
        // return redirect('/dashboard/ujiemisi')->with('success', $message);

        Session::put('ujiemisi', $ujiemisi);

        // Memeriksa tombol yang ditekan
        $printType = $request->input('print_type');

        // Mengarahkan pengguna ke rute yang sesuai berdasarkan tombol yang ditekan
        if ($printType === 'dot_matrix') {
            return redirect('/dashboard/cetak/dotmatrix');
        } elseif ($printType === 'printer') {
            return redirect('/dashboard/cetak/printer');
        }


        // return redirect('/dashboard/cetak/dotmatrix');
        // return redirect('/dashboard/ujiemisi')->with('success', $message)->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');

    }

    public function getNopol(Request $request)
    {
        $kendaraan = Kendaraan::where('nopol', $request->nopol);
        if ($request->user()->is_admin == 0)
            $kendaraan->where('user_id', $request->user()->id);
        return response()->json($kendaraan->first());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nopol' => '',
            'merk' => '',
            'tipe' => '',
            'tahun' => '|gt:1900',
            'cc' => '|gt:100',
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

        $kendaraan = Kendaraan::where('nopol', $validatedData['nopol'])->first();

        // Jika kendaraan belum ada, buat baru
        if (!$kendaraan) {
            $validatedData['user_id'] = auth()->user()->id;
            $kendaraan = Kendaraan::create($validatedData);
        }

        // Tambahkan data uji emisi dengan kendaraan yang ada (baik yang sudah ada atau baru dibuat)
        $ujiEmisiData = $validatedData;
        $ujiEmisiData['user_id'] = auth()->user()->id;
        $ujiEmisiData['kendaraan_id'] = $kendaraan->id;
        $ujiemisi = UjiEmisi::create($ujiEmisiData);

        if ($this->checkIsLulus($ujiemisi)) {
            return redirect("/dashboard/ujiemisi/input-sertif/{$ujiemisi->id}/input-nomor")->with('success', "Kendaraan dinyatakan lulus uji emisi");
        } else {
            return redirect("/dashboard/ujiemisi")->with('error', 'Kendaraan dan Uji emisi berhasil ditambah tetapi kendaraan tidak lulus uji emisi');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Kendaraan $kendaraan, UjiEmisi $ujiemisi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kendaraan $kendaraan, UjiEmisi $ujiemisi)
    {
        // dd($ujiemisi);
        // return view('/dashboard/ujiemisi/edit-uji', [
        //     "bengkel_name" => auth()->user()->bengkel_name,
        //     'kendaraan' => $kendaraan,
        //     'ujiemisi' => $ujiemisi,
        // ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kendaraan $kendaraan, UjiEmisi $ujiemisi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kendaraan $kendaraan, UjiEmisi $ujiemisi)
    {
        //
    }

    private function checkIsLulus($ujiemisi)
    {
        $isLulus = false;
        // ini baru untuk bensin
        if ($ujiemisi->kendaraan->bahan_bakar == "Bensin") {
            switch ($ujiemisi->kendaraan->kendaraan_kategori) {
                case '1':
                    if ($ujiemisi->kendaraan->tahun < 2007) {
                        if ($ujiemisi->co <= 4 && $ujiemisi->hc <= 1000) {
                            $isLulus = true;
                        }
                    } elseif ($ujiemisi->kendaraan->tahun > 2018) {
                        if ($ujiemisi->co <= 0.5 && $ujiemisi->hc <= 100) {
                            $isLulus = true;
                        }
                    } else {
                        if ($ujiemisi->co <= 1 && $ujiemisi->hc <= 150) {
                            $isLulus = true;
                        }
                    }
                    break;

                case '2':
                    if ($ujiemisi->kendaraan->tahun < 2007) {
                        if ($ujiemisi->co <= 4 && $ujiemisi->hc <= 1100) {
                            $isLulus = true;
                        }
                    } elseif ($ujiemisi->kendaraan->tahun > 2018) {
                        if ($ujiemisi->co <= 0.5 && $ujiemisi->hc <= 150) {
                            $isLulus = true;
                        }
                    } else {
                        if ($ujiemisi->co <= 1 && $ujiemisi->hc <= 200) {
                            $isLulus = true;
                        }
                    }
                    break;

                case '3':
                    if ($ujiemisi->kendaraan->tahun < 2007) {
                        if ($ujiemisi->co <= 4 && $ujiemisi->hc <= 1100) {
                            $isLulus = true;
                        }
                    } elseif ($ujiemisi->kendaraan->tahun > 2018) {
                        if ($ujiemisi->co <= 0.5 && $ujiemisi->hc <= 150) {
                            $isLulus = true;
                        }
                    } else {
                        if ($ujiemisi->co <= 1 && $ujiemisi->hc <= 200) {
                            $isLulus = true;
                        }
                    }
                    break;

                case '4': // 2 tak
                    if ($ujiemisi->kendaraan->tahun < 2010) {
                        if ($ujiemisi->co <= 4.5 && $ujiemisi->hc <= 6000) {
                            $isLulus = true;
                        }
                    } elseif ($ujiemisi->kendaraan->tahun > 2016) {
                        if ($ujiemisi->co <= 3 && $ujiemisi->hc <= 1000) {
                            $isLulus = true;
                        }
                    } else {
                        if ($ujiemisi->co <= 4 && $ujiemisi->hc <= 1800) {
                            $isLulus = true;
                        }
                    }
                    break;

                case '5': // 4 tak
                    if ($ujiemisi->kendaraan->tahun < 2010) {
                        if ($ujiemisi->co <= 5.5 && $ujiemisi->hc <= 2200) {
                            $isLulus = true;
                        }
                    } elseif ($ujiemisi->kendaraan->tahun > 2016) {
                        if ($ujiemisi->co <= 3 && $ujiemisi->hc <= 1000) {
                            $isLulus = true;
                        }
                    } else {
                        if ($ujiemisi->co <= 4 && $ujiemisi->hc <= 1800) {
                            $isLulus = true;
                        }
                    }
                    break;

                default:
                    break;
            }
            # code...
        } elseif ($ujiemisi->kendaraan->bahan_bakar == "Solar") {
            if ($ujiemisi->kendaraan->tahun < 2010) {
                if ($ujiemisi->opasitas <= 65) {
                    $isLulus = true;
                }
            } elseif ($ujiemisi->kendaraan->tahun > 2021) {
                if ($ujiemisi->opasitas <= 30) {
                    $isLulus = true;
                }
            } else {
                if ($ujiemisi->opasitas <= 40) {
                    $isLulus = true;
                }
            }
        }
        return $isLulus;
    }

    public function cetakPdfDotMatrix()
    {
        $ujiemisi = Session::get('ujiemisi');

        $formattedDate = Carbon::createFromFormat('Y-m-d H:i:s', $ujiemisi->tanggal_uji)->format('d-m-Y');
        $expirationDate = Carbon::createFromFormat('d-m-Y', $formattedDate)->addYear()->format('d-m-Y');
        $alamat = strtoupper($ujiemisi->kendaraan->user->jalan) . ' ' . $ujiemisi->kendaraan->user->kab_kota;

        $row = 24;
        $table = 155;
        $space_per_row = 4.3;
        $column = 35;

        // setting nama kepala bengkel
        $kepala_bengkel = $ujiemisi->user->kepala_bengkel;
        if (strlen($kepala_bengkel) > 16) {
            // Potong kata terakhir dan ambil huruf pertama
            $last_space_position = strrpos(substr($kepala_bengkel, 0, 16), ' ');
            $kepala_bengkel = substr($kepala_bengkel, 0, $last_space_position) . ' ' . substr($kepala_bengkel, $last_space_position + 1, 1) . '.';
        }

        $kepala_bengkel_baru_formatted = str_pad($kepala_bengkel, 16, ' ', STR_PAD_BOTH);

        $pdf = new FPDF('L', 'mm', array(203.2, 78)); //tambah 2 karena kepotong // original
        // $pdf = new FPDF('P','mm',array(203.2,203.2)); //tambah 2 karena kepotong // coba buat printer
        $pdf->AddPage();
        $pdf->SetFont('courier', '', 9);
        $pdf->Text($column, $row - 0.5, strtoupper($formattedDate));
        $pdf->Text($column, $row + $space_per_row - 0.5, strtoupper($ujiemisi->kendaraan->merk));
        $pdf->Text($column, $row + ($space_per_row * 2) - 0.5, strtoupper($ujiemisi->kendaraan->tipe));
        $pdf->Text($table, $row + ($space_per_row * 3) - 0.5, $ujiemisi->co);
        $pdf->Text($column, $row + ($space_per_row * 3) - 0.5, strtoupper($ujiemisi->kendaraan->tahun));
        $pdf->Text($table, $row + ($space_per_row * 4) - 0.5, $ujiemisi->hc);
        $pdf->Text($column, $row + ($space_per_row * 4), strtoupper($ujiemisi->kendaraan->cc));
        $pdf->Text($column, $row + ($space_per_row * 5) - 0.5, strtoupper($ujiemisi->kendaraan->no_rangka));
        $pdf->Text($table, $row + ($space_per_row * 6) - 0.5, $ujiemisi->opasitas);
        $pdf->Text($column, $row + ($space_per_row * 6) - 0.5, strtoupper($ujiemisi->kendaraan->no_mesin));
        $pdf->Text($column, $row + ($space_per_row * 7) - 0.5, strtoupper($ujiemisi->kendaraan->bahan_bakar));
        $pdf->Text($column, $row + ($space_per_row * 8), strtoupper($ujiemisi->odometer));
        $pdf->Text($column, $row + ($space_per_row * 9), strtoupper($ujiemisi->kendaraan->user->bengkel_name));
        $pdf->Text($column, $row + ($space_per_row * 10) + 0.5, strtoupper($ujiemisi->user->jalan));
        $pdf->Text($table - 26, $row + ($space_per_row * 11) + 0.5, strtoupper($kepala_bengkel_baru_formatted)); //setting maks 16 huruf

        $pdf->SetFont('courier', 'B', 9);
        $pdf->Text($column, $row + ($space_per_row * 11) + 1, strtoupper($ujiemisi->kendaraan->nopol));
        $pdf->SetFont('courier', '', 9);
        $pdf->Text($column, $row + ($space_per_row * 12) + 0.5, strtoupper($expirationDate));

        $fileName = $formattedDate . '_' . $ujiemisi->kendaraan->nopol . '_Dot Matrix' . '.pdf';

        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');

        $pdf->Output('I', $fileName);

        exit;
    }

    public function cetakPdfPrinter()
    {
        $ujiemisi = Session::get('ujiemisi');

        $formattedDate = Carbon::createFromFormat('Y-m-d H:i:s', $ujiemisi->tanggal_uji)->format('d-m-Y');
        $expirationDate = Carbon::createFromFormat('d-m-Y', $formattedDate)->addYear()->format('d-m-Y');
        $alamat = strtoupper($ujiemisi->kendaraan->user->jalan) . ' ' . $ujiemisi->kendaraan->user->kab_kota;

        $row = 21;
        $table = 155 + 18;
        $space_per_row = 4.1;
        $little_space = 0.2;
        $column = 35 + 26 - 7;

        // setting nama kepala bengkel
        $kepala_bengkel = $ujiemisi->user->kepala_bengkel;
        if (strlen($kepala_bengkel) > 16) {
            // Potong kata terakhir dan ambil huruf pertama
            $last_space_position = strrpos(substr($kepala_bengkel, 0, 16), ' ');
            $kepala_bengkel = substr($kepala_bengkel, 0, $last_space_position) . ' ' . substr($kepala_bengkel, $last_space_position + 1, 1) . '.';
        }

        $kepala_bengkel_baru_formatted = str_pad($kepala_bengkel, 16, ' ', STR_PAD_BOTH);

        $pdf = new FPDF('P', 'mm', array(203.2, 297));
        $pdf->AddPage();
        $pdf->SetFont('courier', '', 9);
        $pdf->Text($column, $row - 0.5, strtoupper($formattedDate));
        $pdf->Text($column, $row + $space_per_row - 0.5, strtoupper($ujiemisi->kendaraan->merk));
        $pdf->Text($column, $row + ($space_per_row * 2) - 0.5, strtoupper($ujiemisi->kendaraan->tipe));
        $pdf->Text($table, $row + ($space_per_row * 3) - 2, $ujiemisi->co);
        $pdf->Text($column, $row + ($space_per_row * 3) - 0.5, strtoupper($ujiemisi->kendaraan->tahun));
        $pdf->Text($table, $row + ($space_per_row * 4) - 2, $ujiemisi->hc);
        $pdf->Text($column, $row + ($space_per_row * 4) + ($little_space) - 0.5, strtoupper($ujiemisi->kendaraan->cc));
        $pdf->Text($column, $row + ($space_per_row * 5) + ($little_space * 2) - 0.5, strtoupper($ujiemisi->kendaraan->no_rangka));
        $pdf->Text($table, $row + ($space_per_row * 6) - 2, $ujiemisi->opasitas);
        $pdf->Text($column, $row + ($space_per_row * 6) + ($little_space * 3) - 0.5, strtoupper($ujiemisi->kendaraan->no_mesin));
        $pdf->Text($column, $row + ($space_per_row * 7) + ($little_space * 4) - 0.5, strtoupper($ujiemisi->kendaraan->bahan_bakar));
        $pdf->Text($column, $row + ($space_per_row * 8) + ($little_space * 5) - 0.5, strtoupper($ujiemisi->odometer));
        $pdf->Text($column, $row + ($space_per_row * 9) + ($little_space * 5) - 0.5, strtoupper($ujiemisi->kendaraan->user->bengkel_name));
        $pdf->Text($column, $row + ($space_per_row * 10) + ($little_space * 4), strtoupper($ujiemisi->user->jalan));
        $pdf->Text($table - 26, $row + ($space_per_row * 11) + 1, strtoupper($kepala_bengkel_baru_formatted)); //setting maks 16 huruf
        $pdf->SetFont('courier', 'B', 9);
        $pdf->Text($column, $row + ($space_per_row * 11) + 1, strtoupper($ujiemisi->kendaraan->nopol));
        $pdf->SetFont('courier', '', 9);
        $pdf->Text($column, $row + ($space_per_row * 12) + 0.5, strtoupper($expirationDate));

        $fileName = $formattedDate . '_' . $ujiemisi->kendaraan->nopol . '_Printer' . '.pdf';

        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');

        $pdf->Output('I', $fileName);

        exit;
    }
}

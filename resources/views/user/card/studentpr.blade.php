<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu RFID Siswa - {{ \Carbon\Carbon::now()->format('d-m-Y') }}</title>
    <link rel='shortcut icon' type='image/x-icon' href='{{ asset('logo.png') }}' />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        @page {
            size: A4;
            margin: 20mm;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f5f5f5;
            padding: 20px;
        }
        
        .cards-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            gap: 30px;
            max-width: 1200px;
            width: 100%;
        }
        
        .student-card-container {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            width: 730px;
        }
        
        .card-container {
            perspective: 1000px;
            width: 350px;
            height: 227px;
        }
        
        .card-front {
            position: relative;
            width: 340px;
            height: 227px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            background: linear-gradient(45deg, #0a3d62, #3498db);
            color: white;
        }
        
        .card-back {
            position: relative;
            width: 340px;
            height: 227px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            background: linear-gradient(45deg, #3498db, #0a3d62);
            color: white;
        }
        
        .card-graphic {
            position: absolute;
            top: 0;
            right: 0;
            width: 150px;
            height: 150px;
            background: radial-gradient(circle at top right, rgba(66, 175, 244, 0.8), rgba(66, 134, 244, 0) 70%);
            z-index: 1;
        }
        
        .card-circles {
            position: absolute;
            bottom: -50px;
            left: -50px;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle at bottom left, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0) 70%);
            border-radius: 50%;
            z-index: 1;
        }
        
        .card-pattern {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: repeating-linear-gradient(
                45deg,
                rgba(255, 255, 255, 0.05) 0px,
                rgba(255, 255, 255, 0.05) 2px,
                transparent 2px,
                transparent 4px
            );
            z-index: 2;
            opacity: 0.5;
        }
        
        .school-logo {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 3;
            display: flex;
            align-items: center;
        }
        
        .school-logo-img {
            width: 40px;
            height: 40px;
            margin-right: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .school-logo-img img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }
        
        .school-logo-text {
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.5px;
            max-width: 160px;
            line-height: 1.2;
        }
        
        .card-type {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 12px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9);
            z-index: 3;
            letter-spacing: 1px;
            background-color: rgba(255, 255, 255, 0.2);
            padding: 3px 8px;
            border-radius: 10px;
        }
        
        .chip-container {
            position: absolute;
            left: 20px;
            top: 80px;
            z-index: 3;
            display: flex;
            align-items: center;
        }
        
        .chip {
            width: 40px;
            height: 30px;
            border-radius: 5px;
            background: linear-gradient(135deg, #b8c6db 0%, #f5f7fa 100%);
            position: relative;
            overflow: hidden;
            margin-right: 12px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .chip::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            height: 1px;
            background-color: rgba(0, 0, 0, 0.2);
        }
        
        .chip::after {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            width: 1px;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.2);
        }
        
        .rfid-icon {
            display: inline-block;
            width: 20px;
            height: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            position: relative;
        }
        
        .rfid-icon::before,
        .rfid-icon::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            border: 2px solid #ffffff;
        }
        
        .rfid-icon::before {
            width: 12px;
            height: 12px;
        }
        
        .rfid-icon::after {
            width: 6px;
            height: 6px;
        }
        
        .student-id {
            position: absolute;
            left: 20px;
            top: 130px;
            font-size: 18px;
            font-weight: 600;
            letter-spacing: 2px;
            z-index: 3;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .student-info {
            position: absolute;
            left: 20px;
            bottom: 45px;
            z-index: 3;
        }
        
        .student-info-label {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.7);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        
        .student-name {
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }
        
        .student-class {
            position: absolute;
            left: 170px;
            bottom: 45px;
            z-index: 3;
        }
        
        .class-label {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.7);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        
        .class-name {
            font-size: 14px;
            font-weight: 500;
        }
        
        .card-valid {
            position: absolute;
            right: 20px;
            top: 184px;
            bottom: 20px;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.7);
            z-index: 3;
        }
        
        .school-emblem {
            position: absolute;
            right: 20px;
            bottom: 45px;
            z-index: 3;
        }
        
        .school-emblem img {
            width: 40px;
            height: 25px;
            object-fit: contain;
        }
        
        .student-status {
            position: absolute;
            left: 11px;
            bottom: 20px;
            font-size: 11px;
            color: #ffffff;
            font-weight: 500;
            padding: 3px 8px;
            border-radius: 10px;
            z-index: 3;
        }
        
        .photo-placeholder {
            position: absolute;
            right: 20px;
            top: 55px;
            width: 105px;
            height: 120px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #666;
            font-size: 10px;
            text-align: center;
            z-index: 3;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }
        
        .photo-placeholder img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .student-prodi {
            position: absolute;
            right: 30px;
            top: 150px;
            width: 80px;
            font-size: 9px;
            font-weight: 600;
            color: #ffffff;
            background-color: rgba(0, 0, 0, 0.3);
            padding: 2px 0;
            text-align: center;
            border-radius: 0 0 8px 8px;
            z-index: 4;
            text-transform: capitalize;
        }

        /* Back card specific styles */
        .qrcode-container {
            position: absolute;
            top: 30px;
            left: 20px;
            z-index: 3;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .qrcode {
            width: 100px;
            height: 100px;
            background-color: white;
            padding: 5px;
            border-radius: 8px;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        
        .qrcode img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .qrcode-label {
            font-size: 10px;
            margin-top: 5px;
            color: rgba(255, 255, 255, 0.8);
            text-align: center;
        }
        
        .card-back-info {
            position: absolute;
            top: 30px;
            left: 140px;
            right: 20px;
            z-index: 3;
        }
        
        .info-section {
            margin-bottom: 9px;
        }
        
        .info-section-title {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.7);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding-bottom: 2px;
        }
        
        .info-section-content {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .card-note {
            position: absolute;
            bottom: 15px;
            left: 20px;
            right: 20px;
            text-align: center;
            font-size: 10px;
            color: rgba(255, 255, 255, 0.7);
            font-style: italic;
            z-index: 3;
        }
        
        @media print {
            body {
                display: flex;
                flex-wrap: wrap;
            }

            .card-layout {
                display: flex;
                flex-wrap: wrap;
                justify-content: flex-start;
            }

            .card-container {
                page-break-inside: avoid;
                margin-bottom: 20px;
            }

            .card-front {
            background: linear-gradient(45deg, #0a3d62, #3498db) !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .card-back {
            background: linear-gradient(45deg, #3498db, #0a3d62) !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>
    <div class="cards-grid">
        @foreach ($students as $student)
            <div class="student-card-container">
                <div class="card-container">
                    <div class="card-front">
                        <div class="card-graphic"></div>
                        <div class="card-circles"></div>
                        <div class="card-pattern"></div>
                        
                        <div class="school-logo">
                            <div class="school-logo-img">
                                <img src="{{ asset('logo.png') }}" alt="Logo Sekolah">
                            </div>
                            <div class="school-logo-text">SMK WIYATA MANDALA</div>
                        </div>
                        
                        <div class="card-type">KARTU SISWA</div>
                        
                        <div class="photo-placeholder">
                            <img src="{{ asset('storage/' . $student->photo) }}" alt="Foto Siswa">
                        </div>

                        {{-- @foreach($student->classRoom as $class)
                            <div class="student-prodi">{{ $class->prodi }}</div>
                        @endforeach --}}
                        
                        <div class="chip-container">
                            <div class="chip"></div>
                            <div class="rfid-icon"></div>
                        </div>
                        
                        <div class="student-id">{{ $student->nisn }}</div>
                        
                        <div class="student-info">
                            <div class="student-name">{{ strtoupper($student->name) }}</div>
                        </div>

                        @foreach($student->classRoom as $class)
                            <div class="student-status">{{ $class->prodi }}</div>
                        @endforeach
                        <div class="card-valid">Berlaku s/d: {{ \Carbon\Carbon::parse($student->created_at)->addYears(3)->format('F Y') }}</div>
                    </div>
                </div>
                
                <div class="card-container">
                    <div class="card-back">
                        <div class="card-circles"></div>
                        <div class="card-pattern"></div>
                        
                        <div class="qrcode-container">
                            <div class="qrcode">
                                {!! $student->qr_code !!}
                            </div>
                            <div class="qrcode-label">SCAN UNTUK VERIFIKASI<br>{{ $student->no_kartu }}</div>
                        </div>
                        
                        <div class="card-back-info">
                            <div class="info-section">
                                <div class="info-section-title">Alamat Sekolah</div>
                                <div class="info-section-content">Jl. Pare Kandangan No.10, Kec. Kepung, Kab. Kediri</div>
                            </div>
                            
                            <div class="info-section">
                                <div class="info-section-title">Kontak Sekolah</div>
                                <div class="info-section-content">Telp: (021) 123-4567</div>
                            </div>
                            
                            <div class="info-section">
                                <div class="info-section-title">Website</div>
                                <div class="info-section-content">www.smkwiyatamandala.sch.id</div>
                            </div>
                        </div>
                        
                        <div class="card-note">
                            Kartu ini adalah milik siswa SMK Wiyata Mandala. Jika ditemukan, harap dikembalikan ke alamat sekolah.
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        // Auto print when loaded
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
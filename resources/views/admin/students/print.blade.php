<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student QR Pass - {{ $student->name }} ({{ $student->student_id }})</title>

    <!-- Google Fonts: Inter / Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --ub-maroon: #7B1113;
            --ub-maroon-dark: #580B0D;
            --ub-gold: #F5B800;
            --ub-gold-light: #FEF3C7;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px 15px;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Top Floating Print Controls (Screen Only) */
        .print-toolbar {
            width: 100%;
            max-width: 380px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            background: #ffffff;
            padding: 10px 16px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13.5px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            border: none;
        }

        .btn-print {
            background-color: var(--ub-maroon);
            color: #ffffff;
        }

        .btn-print:hover {
            background-color: var(--ub-maroon-dark);
            transform: translateY(-1px);
        }

        .btn-close-tab {
            background-color: #f1f5f9;
            color: #475569;
        }

        .btn-close-tab:hover {
            background-color: #e2e8f0;
            color: #1e293b;
        }

        /* Printable Card Wrapper */
        .card-container {
            width: 100%;
            max-width: 360px;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border: 1.5px solid #cbd5e1;
            position: relative;
        }

        /* Card Header */
        .card-header {
            background: linear-gradient(135deg, #7B1113 0%, #4a0709 100%);
            padding: 18px 16px;
            text-align: center;
            color: #ffffff;
            border-bottom: 3px solid var(--ub-gold);
            position: relative;
        }

        .university-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 4px;
        }

        .ub-seal-box {
            width: 28px;
            height: 28px;
            background: var(--ub-gold);
            color: var(--ub-maroon);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 13px;
        }

        .university-title {
            font-weight: 800;
            font-size: 13.5px;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        .system-subtitle {
            font-size: 10.5px;
            letter-spacing: 0.6px;
            opacity: 0.9;
            text-transform: uppercase;
            font-weight: 600;
            color: #fecdd3;
        }

        /* Card Body */
        .card-body {
            padding: 24px 20px 20px 20px;
            text-align: center;
            background: #ffffff;
        }

        /* Student Photo */
        .student-photo-wrapper {
            margin-bottom: 12px;
            position: relative;
            display: inline-block;
        }

        .student-photo {
            width: 86px;
            height: 86px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            outline: 2px solid var(--ub-maroon);
        }

        .student-initial-avatar {
            width: 86px;
            height: 86px;
            border-radius: 50%;
            background: linear-gradient(135deg, #7B1113 0%, #991B1E 100%);
            color: #ffffff;
            font-size: 32px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            outline: 2px solid var(--ub-maroon);
            margin: 0 auto;
        }

        .student-name {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 3px;
            line-height: 1.2;
        }

        .student-id-pill {
            display: inline-block;
            background: #f8fafc;
            border: 1.5px solid #cbd5e1;
            color: #1e293b;
            font-family: monospace;
            font-weight: 800;
            font-size: 13.5px;
            padding: 3px 12px;
            border-radius: 6px;
            margin-bottom: 8px;
            letter-spacing: 0.8px;
        }

        .student-department {
            font-size: 12px;
            color: #475569;
            font-weight: 600;
            margin-bottom: 14px;
        }

        /* Large Clean High-Contrast QR Code for Tapping */
        .qr-code-box {
            background: #ffffff;
            border: 2px solid #0f172a;
            border-radius: 12px;
            padding: 12px;
            display: inline-block;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 8px;
        }

        .qr-code-box svg {
            display: block;
            width: 170px !important;
            height: 170px !important;
        }

        .qr-token-label {
            font-family: monospace;
            font-size: 10px;
            color: #64748b;
            font-weight: 700;
            word-break: break-all;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .tap-instruction {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 700;
            color: var(--ub-maroon);
            background: rgba(123, 17, 19, 0.06);
            padding: 4px 10px;
            border-radius: 20px;
            margin-bottom: 4px;
        }

        /* Card Footer */
        .card-footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 10px 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 10px;
            color: #64748b;
            font-weight: 600;
        }

        /* Print Media Styles: Guarantees exact 1 page clean print */
        @media print {
            body {
                background: none !important;
                padding: 0 !important;
                margin: 0 !important;
                display: flex !important;
                justify-content: center !important;
                align-items: flex-start !important;
            }

            .print-toolbar {
                display: none !important;
            }

            .card-container {
                box-shadow: none !important;
                border: 2px solid #000000 !important;
                max-width: 340px !important;
                margin: 15px auto !important;
                page-break-inside: avoid !important;
                page-break-after: avoid !important;
            }

            @page {
                size: auto;
                margin: 5mm;
            }
        }
    </style>
</head>

<body>

    <!-- Screen Toolbar -->
    <div class="print-toolbar">
        <button type="button" class="btn-action btn-print" onclick="window.print();">
            <i class="fa-solid fa-print"></i> Print QR Pass
        </button>
        <button type="button" class="btn-action btn-close-tab" onclick="window.close();">
            <i class="fa-solid fa-xmark"></i> Close Tab
        </button>
    </div>

    <!-- Official Student QR Identity Pass Card -->
    <div class="card-container">
        <!-- University Card Header Banner -->
        <div class="card-header">
            <div class="university-badge">
                <div class="university-title">University of Batangas</div>
            </div>
            <div class="system-subtitle">Equipment Borrowing System <br> Student Pass</div>
        </div>

        <!-- Card Body -->
        <div class="card-body">
            <!-- Student Selfie Photo -->
            <div class="student-photo-wrapper">
                @if ($student->profile_photo_path && file_exists(public_path($student->profile_photo_path)))
                    <img src="{{ asset($student->profile_photo_path) }}" alt="{{ $student->name }}"
                        class="student-photo">
                @else
                    <div class="student-initial-avatar">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </div>
                @endif
            </div>

            <!-- Student Name -->
            <h1 class="student-name">{{ $student->name }}</h1>

            <!-- Student ID -->
            <div class="student-id-pill">
                ID: {{ $student->student_id ?? 'N/A' }}
            </div>

            <!-- Department -->
            <div class="student-department">
                {{ $student->department ?? 'General' }}
            </div>

            <!-- High-Contrast Big QR Code Box -->
            <div class="qr-code-box">
                {!! $qrSvg !!}
            </div>

            <!-- Token text -->
            <div class="qr-token-label">
                {{ $student->qr_code_token }}
            </div>

        </div>
    </div>

    <script>
        // Auto trigger print dialog when opened in new tab
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 350);
        });
    </script>
</body>

</html>

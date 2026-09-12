<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UB EBS - Email Verification</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; margin: 0; padding: 24px; color: #1e293b;">
    <div style="max-width: 540px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <div style="background-color: #7B1113; padding: 24px; text-align: center; border-bottom: 4px solid #F5B800;">
            <h1 style="color: #ffffff; margin: 0; font-size: 20px; font-weight: 700; letter-spacing: 0.5px;">UNIVERSITY OF BATANGAS</h1>
            <p style="color: #F5B800; margin: 4px 0 0 0; font-size: 13px; font-weight: 600; text-transform: uppercase;">Equipment Borrowing System (EBS)</p>
        </div>
        <div style="padding: 28px;">
            <h2 style="font-size: 18px; color: #7B1113; margin-top: 0;">Good day, {{ $studentName }}!</h2>
            <p style="font-size: 14px; line-height: 1.6; color: #475569;">
                Thank you for registering for the UB Equipment Borrowing System. Please use the following One-Time Password (OTP) to verify your <strong>@ub.edu.ph</strong> account and generate your official Student QR code:
            </p>
            
            <div style="text-align: center; margin: 24px 0;">
                <div style="display: inline-block; background: #FFF8E6; border: 2px dashed #F5B800; padding: 14px 32px; border-radius: 8px; font-size: 32px; font-weight: 800; letter-spacing: 6px; color: #7B1113;">
                    {{ $otpCode }}
                </div>
                <p style="font-size: 12px; color: #64748b; margin-top: 8px;">This verification code is valid for {{ $expiryMinutes }} minutes.</p>
            </div>

            <p style="font-size: 13px; line-height: 1.5; color: #64748b;">
                If you did not initiate this registration request, please disregard this email.
            </p>
        </div>
        <div style="background-color: #f1f5f9; padding: 16px; text-align: center; font-size: 11px; color: #64748b; border-top: 1px solid #e2e8f0;">
            &copy; {{ date('Y') }} University of Batangas - EBS. All Rights Reserved.
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Return Reminder - UB EBS</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; margin: 0; padding: 24px; color: #1e293b;">
    <div style="max-width: 580px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
        <div style="background-color: #7B1113; padding: 24px; text-align: center; border-bottom: 4px solid #F5B800;">
            <h1 style="color: #ffffff; margin: 0; font-size: 20px; font-weight: 700; letter-spacing: 0.5px;">UNIVERSITY OF BATANGAS</h1>
            <p style="color: #F5B800; margin: 4px 0 0 0; font-size: 13px; font-weight: 600; text-transform: uppercase;">Equipment Borrowing System (EBS)</p>
        </div>
        <div style="padding: 28px;">
            <div style="background-color: #FFFBEB; border-left: 4px solid #F5B800; padding: 14px 18px; border-radius: 6px; margin-bottom: 20px;">
                <h3 style="margin: 0 0 6px 0; color: #92400E; font-size: 15px; font-weight: 700;">📢 Return Reminder Tomorrow!</h3>
                <p style="margin: 0; font-size: 13px; color: #78350F; line-height: 1.5;">
                    Please return your borrowed equipment <strong>tomorrow</strong> before the due time to avoid penalty charges.
                </p>
            </div>

            <p style="font-size: 14px; color: #334155; line-height: 1.6;">
                Good day, <strong>{{ $transaction->student->name }}</strong>!
            </p>
            <p style="font-size: 13px; color: #475569; line-height: 1.6;">
                According to our records, you borrowed equipment under Reference No: <strong style="color: #7B1113;">{{ $transaction->reference_no }}</strong>.
            </p>

            <table style="width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 13px;">
                <thead>
                    <tr style="background-color: #f1f5f9; text-align: left;">
                        <th style="padding: 10px 12px; border: 1px solid #cbd5e1; color: #334155;">Item Name</th>
                        <th style="padding: 10px 12px; border: 1px solid #cbd5e1; color: #334155;">Location</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaction->items as $item)
                    <tr>
                        <td style="padding: 10px 12px; border: 1px solid #cbd5e1; font-weight: 600;">{{ $item->item_name }}</td>
                        <td style="padding: 10px 12px; border: 1px solid #cbd5e1; color: #64748b;">{{ $item->item_location }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; margin-bottom: 20px; font-size: 13px;">
                <p style="margin: 4px 0;"><strong>Borrowed Date & Time:</strong> {{ $transaction->borrow_date_time->format('M d, Y h:i A') }}</p>
                <p style="margin: 4px 0; color: #b91c1c;"><strong>Scheduled Due Date & Time:</strong> {{ $transaction->due_date_time->format('M d, Y h:i A') }}</p>
            </div>

            <div style="background-color: #FEF2F2; border: 1px solid #FECACA; border-radius: 8px; padding: 14px; font-size: 13px; color: #991B1B;">
                <strong>⚠️ Penalty Policy:</strong><br>
                Returning items after the scheduled due date incurs a penalty of <strong>₱5.00 per day</strong>. Future borrowing privileges will be restricted until the items are returned and outstanding penalties are settled.
            </div>
        </div>
        <div style="background-color: #f1f5f9; padding: 16px; text-align: center; font-size: 11px; color: #64748b; border-top: 1px solid #e2e8f0;">
            &copy; {{ date('Y') }} University of Batangas - EBS. All Rights Reserved.
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Vehicle Booking</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2 style="color: #0d6efd;">New Booking Request!</h2>
        <p>Hello {{ $owner->first_name ?? 'Owner' }},</p>

        <p>You have a new booking request for your vehicle <strong>{{ $vehicule->model ?? 'Vehicle' }}</strong>.</p>

        <ul>
            <li><strong>Pickup:</strong> {{ $booking->pickup_location }}</li>
            <li><strong>Dropoff:</strong> {{ $booking->dropoff_location }}</li>
            <li><strong>Scheduled Time:</strong> {{ \Carbon\Carbon::parse($booking->scheduled_time)->format('Y-m-d H:i') }}</li>
            <li><strong>Notes:</strong> {{ $booking->notes ?? 'None' }}</li>
        </ul>

        <p style="text-align: center; margin: 30px 0;">
          <a href="{{ route('vehicules.confirm', $vehicule->id) }}"
   style="background-color: #28a745; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">
   Confirm
</a>

        </p>

        <p>Thank you for using <strong>Green Tounsi</strong>!</p>
    </div>
</body>

</html>

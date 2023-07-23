<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Post Alert</title>
</head>

<body style="background-color: #f4f4f4; font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <img style="display: block; margin: 0 auto; width: 24px;" src="{{ asset('Logo.svg') }}" />
        <div
            style="background-color: #fff; border: 1px solid #ccc; border-radius: 4px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); padding: 6px; padding: 40px;">
            <h1 style="font-size: 1.75rem; font-weight: 700;">
                In case you missed,
            </h1>
            <div>
                <strong>Hi {{ $user->name }},</strong>
                <p>
                    {{ $content->thread }}
                </p>
            </div>
            <a style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 4px; transition: background-color 0.3s ease;"
                href="{{ $content->front_link }}">Check it out</a>
        </div>
        <img style="display: block; margin: 0 auto; margin-top: 10px; width: 40px;" src="{{ asset('Logo.svg') }}" />
        <div style="color: #888; text-align: center; margin-top: 6px;">
            Sent with &lt;3 from {{ env('APP_NAME') }}
        </div>
    </div>
</body>

</html>

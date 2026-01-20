<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Account Invitation</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0;">You're Invited!</h1>
    </div>
    
    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px;">
        <p>Hello,</p>
        
        <p>You have been invited by <strong>{{ $invitation->inviter->name }}</strong> to join <strong>{{ $invitation->account->name }}</strong> as a <strong>{{ ucfirst($invitation->role) }}</strong>.</p>
        
        <p>To accept this invitation, please click the button below to register and join the workspace:</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('register', ['token' => $invitation->token]) }}" 
               style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">
                Accept Invitation & Register
            </a>
        </div>
        
        <p style="font-size: 12px; color: #666;">
            Or copy and paste this link into your browser:<br>
            {{ route('register', ['token' => $invitation->token]) }}
        </p>
        
        <p style="font-size: 12px; color: #999; margin-top: 30px;">
            This invitation will expire on {{ $invitation->expires_at->format('F d, Y') }}.
        </p>
    </div>
</body>
</html>

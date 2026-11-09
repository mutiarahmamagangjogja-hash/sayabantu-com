$ErrorActionPreference = 'SilentlyContinue'
$base = 'http://localhost:8000'
$r = Invoke-WebRequest -Uri "$base/mitra/login" -SessionVariable s -UseBasicParsing
$token = [regex]::Match($r.Content, 'name="csrf-token" content="([^"]+)"').Groups[1].Value
$body = @{ identifier = 'SB-8239'; pin = '123456' } | ConvertTo-Json
$login = Invoke-WebRequest -Uri "$base/mitra/login" -Method Post -WebSession $s -UseBasicParsing `
    -Headers @{ 'X-CSRF-TOKEN' = $token; 'Accept' = 'application/json'; 'Content-Type' = 'application/json' } `
    -Body $body
Write-Output "LOGIN: $($login.StatusCode)"

$d = Invoke-WebRequest -Uri "$base/mitra/dashboard" -WebSession $s -UseBasicParsing -MaximumRedirection 0
Write-Output "DASHBOARD: $($d.StatusCode) len=$($d.Content.Length)"
$m = [regex]::Match($d.Content, 'window\.CURRENT_MITRA_DATA = \{[\s\S]{0,600}?\};')
Write-Output $m.Value

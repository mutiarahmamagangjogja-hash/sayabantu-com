$ErrorActionPreference = 'SilentlyContinue'
$base = 'http://localhost:8000'
$r = Invoke-WebRequest -Uri "$base/mitra/login" -SessionVariable s -UseBasicParsing
$token = [regex]::Match($r.Content, 'name="csrf-token" content="([^"]+)"').Groups[1].Value
Write-Output "CSRF: $token"
$body = @{ identifier = 'SB-8239'; pin = '123456' } | ConvertTo-Json
$resp = Invoke-WebRequest -Uri "$base/mitra/login" -Method Post -WebSession $s -UseBasicParsing `
    -Headers @{ 'X-CSRF-TOKEN' = $token; 'Accept' = 'application/json'; 'Content-Type' = 'application/json' } `
    -Body $body
Write-Output "STATUS: $($resp.StatusCode)"
Write-Output $resp.Content

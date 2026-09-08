[CmdletBinding()]
param(
    [Parameter(Mandatory = $true, Position = 0)]
    [ValidateSet('local-to-cloud')]
    [string] $Direction,

    [Parameter(Mandatory = $true, Position = 1)]
    [ValidateSet('full', 'code', 'database')]
    [string] $Scope
)

$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest

function Fail([string] $Message) {
    Write-Error $Message
    exit 1
}

function Require-Command([string] $Name) {
    if (-not (Get-Command $Name -ErrorAction SilentlyContinue)) {
        Fail "Không tìm thấy '$Name'. Hãy cài công cụ này và chạy lại."
    }
}

function Read-DotEnv([string] $Path) {
    $values = @{}
    if (-not (Test-Path $Path)) {
        return $values
    }

    foreach ($line in Get-Content -Path $Path -Encoding UTF8) {
        $trimmed = $line.Trim()
        if (-not $trimmed -or $trimmed.StartsWith('#')) {
            continue
        }

        if ($trimmed -match '^([A-Za-z_][A-Za-z0-9_]*)=(.*)$') {
            $key = $Matches[1]
            $value = $Matches[2].Trim()
            if ($value.Length -ge 2 -and (($value.StartsWith('"') -and $value.EndsWith('"')) -or ($value.StartsWith("'") -and $value.EndsWith("'")))) {
                $value = $value.Substring(1, $value.Length - 2)
            }
            $values[$key] = $value
        }
    }

    return $values
}

function Get-ValueOrPrompt([hashtable] $EnvValues, [string] $Key, [string] $Prompt, [switch] $Secret) {
    $existing = [Environment]::GetEnvironmentVariable($Key)
    if ($null -ne $existing -and $existing -ne '') {
        return $existing
    }

    if ($EnvValues.ContainsKey($Key) -and $EnvValues[$Key] -ne '') {
        return $EnvValues[$Key]
    }

    if ($Secret) {
        $secure = Read-Host -Prompt $Prompt -AsSecureString
        $ptr = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($secure)
        try {
            return [Runtime.InteropServices.Marshal]::PtrToStringBSTR($ptr)
        }
        finally {
            [Runtime.InteropServices.Marshal]::ZeroFreeBSTR($ptr)
        }
    }

    return Read-Host -Prompt $Prompt
}

function Invoke-External([string] $File, [string[]] $Arguments) {
    & $File @Arguments
    if ($LASTEXITCODE -ne 0) {
        Fail "Lệnh '$File' thất bại với mã $LASTEXITCODE."
    }
}

$ProjectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $ProjectRoot

$envFile = Join-Path $ProjectRoot '.env'
$localEnv = Read-DotEnv $envFile
$syncDir = Join-Path $ProjectRoot '.sync'
$dumpFile = Join-Path $syncDir 'local-database.sql'
New-Item -ItemType Directory -Path $syncDir -Force | Out-Null

Write-Host ''
Write-Host '=== Car Booking: local-to-cloud ===' -ForegroundColor Cyan
Write-Host "Phạm vi: $Scope"
Write-Host ''

Require-Command 'git'

if ($Scope -in @('full', 'code')) {
    Require-Command 'cloud'

    $branch = (git branch --show-current).Trim()
    if ($branch -ne 'main') {
        Fail "Script chỉ đồng bộ từ nhánh main. Nhánh hiện tại: '$branch'."
    }

    $status = @(git status --porcelain)
    if ($status.Count -gt 0) {
        Fail 'Working tree chưa sạch. Hãy commit hoặc stash thay đổi trước khi đồng bộ.'
    }

    Write-Host 'Kiểm tra Laravel Cloud CLI...' -ForegroundColor Yellow
    Invoke-External 'cloud' @('list')
}

if ($Scope -in @('full', 'database')) {
    Require-Command 'mysqldump'
    Require-Command 'mysql'

    $localHost = Get-ValueOrPrompt $localEnv 'DB_HOST' 'Local DB host'
    $localPort = Get-ValueOrPrompt $localEnv 'DB_PORT' 'Local DB port'
    $localName = Get-ValueOrPrompt $localEnv 'DB_DATABASE' 'Local DB name'
    $localUser = Get-ValueOrPrompt $localEnv 'DB_USERNAME' 'Local DB user'
    $localPassword = Get-ValueOrPrompt $localEnv 'DB_PASSWORD' 'Local DB password' -Secret

    $cloudHost = Get-ValueOrPrompt @{} 'CLOUD_DB_HOST' 'Cloud DB host'
    $cloudPort = Get-ValueOrPrompt @{} 'CLOUD_DB_PORT' 'Cloud DB port'
    $cloudName = Get-ValueOrPrompt @{} 'CLOUD_DB_DATABASE' 'Cloud DB name'
    $cloudUser = Get-ValueOrPrompt @{} 'CLOUD_DB_USERNAME' 'Cloud DB user'
    $cloudPassword = Get-ValueOrPrompt @{} 'CLOUD_DB_PASSWORD' 'Cloud DB password' -Secret

    Write-Host ''
    Write-Host "Local : $localUser@$localHost`:$localPort/$localName"
    Write-Host "Cloud : $cloudUser@$cloudHost`:$cloudPort/$cloudName"
    Write-Host ''
    Write-Warning 'FULL DATABASE SYNC sẽ ghi dữ liệu local vào Cloud và có thể thay thế dữ liệu hiện có.'
    $confirm = Read-Host 'Gõ SYNC để tiếp tục'
    if ($confirm -ne 'SYNC') {
        Fail 'Đã huỷ đồng bộ database.'
    }

    if (Test-Path $dumpFile) {
        Remove-Item $dumpFile -Force
    }

    Write-Host '1/3 Export database local...' -ForegroundColor Yellow
    $env:MYSQL_PWD = $localPassword
    try {
        Invoke-External 'mysqldump' @(
            '--host=' + $localHost,
            '--port=' + $localPort,
            '--user=' + $localUser,
            '--single-transaction',
            '--routines',
            '--triggers',
            '--events',
            '--default-character-set=utf8mb4',
            '--add-drop-table',
            $localName,
            '--result-file=' + $dumpFile
        )
    }
    finally {
        Remove-Item Env:MYSQL_PWD -ErrorAction SilentlyContinue
    }

    if (-not (Test-Path $dumpFile) -or (Get-Item $dumpFile).Length -eq 0) {
        Fail 'Không tạo được database dump local.'
    }

    Write-Host '2/3 Import database vào Cloud...' -ForegroundColor Yellow
    $env:MYSQL_PWD = $cloudPassword
    try {
        $mysqlArgs = @(
            '--host=' + $cloudHost,
            '--port=' + $cloudPort,
            '--user=' + $cloudUser,
            '--default-character-set=utf8mb4',
            $cloudName
        )
        $escapedArgs = ($mysqlArgs | ForEach-Object { '"' + $_.Replace('"', '\"') + '"' }) -join ' '
        $commandLine = 'mysql ' + $escapedArgs + ' < "' + $dumpFile + '"'
        Invoke-External 'cmd.exe' @('/d', '/s', '/c', $commandLine)
    }
    finally {
        Remove-Item Env:MYSQL_PWD -ErrorAction SilentlyContinue
    }

    Write-Host '3/3 Đồng bộ database hoàn tất.' -ForegroundColor Green
}

if ($Scope -in @('full', 'code')) {
    Write-Host 'Đẩy main lên GitHub...' -ForegroundColor Yellow
    Invoke-External 'git' @('push', 'origin', 'main')

    Write-Host 'Deploy main lên Laravel Cloud...' -ForegroundColor Yellow
    Invoke-External 'cloud' @('deploy')

    Write-Host 'Code deploy hoàn tất.' -ForegroundColor Green
}

if (Test-Path $dumpFile) {
    Remove-Item $dumpFile -Force
}

Write-Host ''
Write-Host '=== SYNC HOÀN TẤT ===' -ForegroundColor Green
Write-Host 'Local -> Cloud: thành công.' -ForegroundColor Green

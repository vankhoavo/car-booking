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
        Fail "Required command not found: $Name"
    }
}

function Read-DotEnv([string] $Path) {
    $values = @{}
    if (-not (Test-Path $Path)) { return $values }

    foreach ($line in Get-Content -Path $Path -Encoding UTF8) {
        $trimmed = $line.Trim()
        if (-not $trimmed -or $trimmed.StartsWith('#')) { continue }
        if ($trimmed -match '^([A-Za-z_][A-Za-z0-9_]*)=(.*)$') {
            $key = $Matches[1]
            $value = $Matches[2].Trim()
            if ($value.Length -ge 2) {
                $first = $value.Substring(0, 1)
                $last = $value.Substring($value.Length - 1, 1)
                if (($first -eq '"' -and $last -eq '"') -or ($first -eq "'" -and $last -eq "'")) {
                    $value = $value.Substring(1, $value.Length - 2)
                }
            }
            $values[$key] = $value
        }
    }

    return $values
}

function Get-ValueOrPrompt([hashtable] $EnvValues, [string] $Key, [string] $Prompt, [switch] $Secret) {
    $existing = [Environment]::GetEnvironmentVariable($Key)
    if ($null -ne $existing -and $existing -ne '') { return $existing }
    if ($EnvValues.ContainsKey($Key) -and $EnvValues[$Key] -ne '') { return $EnvValues[$Key] }

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
        Fail "Command failed: $File (exit code $LASTEXITCODE)"
    }
}

function Invoke-Cloud([string[]] $Arguments) {
    & 'cloud' @Arguments
    if ($LASTEXITCODE -ne 0) {
        Fail "Laravel Cloud command failed (exit code $LASTEXITCODE)"
    }
}

function Run-Cloud-Artisan([string] $Command) {
    Write-Host "Cloud Artisan: $Command" -ForegroundColor Yellow
    Invoke-Cloud @('command:run', 'production', ('--cmd=' + $Command))
}

$ProjectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $ProjectRoot

$envFile = Join-Path $ProjectRoot '.env'
$localEnv = Read-DotEnv $envFile
$syncDir = Join-Path $ProjectRoot '.sync'
$dumpFile = Join-Path $syncDir 'local-database.sql'
$phpIniDir = Join-Path $syncDir 'php-cli'
$phpIniFile = Join-Path $phpIniDir '99-cloud-memory.ini'
New-Item -ItemType Directory -Path $syncDir -Force | Out-Null

Write-Host ''
Write-Host '=== Car Booking: local-to-cloud ===' -ForegroundColor Cyan
Write-Host "Scope: $Scope"
Write-Host ''

Require-Command 'git'
Require-Command 'cloud'

if ($Scope -in @('full', 'code')) {
    $branch = (git branch --show-current).Trim()
    if ($branch -ne 'main') {
        Fail "This script only syncs from main. Current branch: $branch"
    }

    $status = @(git status --porcelain)
    if ($status.Count -gt 0) {
        Fail 'Working tree is not clean. Commit or stash changes before syncing.'
    }

    Write-Host 'Checking Laravel Cloud CLI...' -ForegroundColor Yellow
    $previousPhpIniScanDir = $env:PHP_INI_SCAN_DIR
    try {
        New-Item -ItemType Directory -Path $phpIniDir -Force | Out-Null
        Set-Content -Path $phpIniFile -Value 'memory_limit=512M' -Encoding ASCII
        $env:PHP_INI_SCAN_DIR = $phpIniDir
        Invoke-Cloud @('--version')
    }
    finally {
        if ($null -eq $previousPhpIniScanDir) {
            Remove-Item Env:PHP_INI_SCAN_DIR -ErrorAction SilentlyContinue
        } else {
            $env:PHP_INI_SCAN_DIR = $previousPhpIniScanDir
        }
    }
}

if ($Scope -in @('full', 'code')) {
    Write-Host '1/5 Push main to GitHub...' -ForegroundColor Yellow
    Invoke-External 'git' @('push', 'origin', 'main')

    Write-Host '2/5 Deploy main to Laravel Cloud...' -ForegroundColor Yellow
    $previousPhpIniScanDir = $env:PHP_INI_SCAN_DIR
    try {
        Set-Content -Path $phpIniFile -Value 'memory_limit=512M' -Encoding ASCII
        $env:PHP_INI_SCAN_DIR = $phpIniDir
        Invoke-Cloud @('deploy', '--no-interaction')
    }
    finally {
        if ($null -eq $previousPhpIniScanDir) {
            Remove-Item Env:PHP_INI_SCAN_DIR -ErrorAction SilentlyContinue
        } else {
            $env:PHP_INI_SCAN_DIR = $previousPhpIniScanDir
        }
    }
    Write-Host 'Code deployment completed.' -ForegroundColor Green
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
    Write-Host "Local: $localUser@$localHost`:$localPort/$localName"
    Write-Host "Cloud: $cloudUser@$cloudHost`:$cloudPort/$cloudName"
    Write-Host ''
    Write-Warning 'This database sync writes local data to Cloud and may replace existing Cloud data.'
    $confirm = Read-Host 'Type SYNC to continue'
    if ($confirm -ne 'SYNC') {
        Fail 'Database sync cancelled.'
    }

    if (Test-Path $dumpFile) { Remove-Item $dumpFile -Force }

    Write-Host '3/5 Export local database...' -ForegroundColor Yellow
    $env:MYSQL_PWD = $localPassword
    try {
        Invoke-External 'mysqldump' @(
            ('--host=' + $localHost),
            ('--port=' + $localPort),
            ('--user=' + $localUser),
            '--single-transaction',
            '--routines',
            '--triggers',
            '--events',
            '--default-character-set=utf8mb4',
            '--add-drop-table',
            $localName,
            ('--result-file=' + $dumpFile)
        )
    }
    finally {
        Remove-Item Env:MYSQL_PWD -ErrorAction SilentlyContinue
    }

    if (-not (Test-Path $dumpFile) -or (Get-Item $dumpFile).Length -eq 0) {
        Fail 'Local database dump was not created.'
    }

    Write-Host '4/5 Import database into Cloud...' -ForegroundColor Yellow
    $env:MYSQL_PWD = $cloudPassword
    try {
        $mysqlArgs = @(
            ('--host=' + $cloudHost),
            ('--port=' + $cloudPort),
            ('--user=' + $cloudUser),
            '--default-character-set=utf8mb4',
            $cloudName
        )

        $quote = [char]34
        $escapedArgs = ($mysqlArgs | ForEach-Object {
            $quote + $_.Replace($quote, ([char]92 + $quote)) + $quote
        }) -join ' '
        $commandLine = 'mysql ' + $escapedArgs + ' < ' + $quote + $dumpFile + $quote
        Invoke-External 'cmd.exe' @('/d', '/s', '/c', $commandLine)
    }
    finally {
        Remove-Item Env:MYSQL_PWD -ErrorAction SilentlyContinue
    }

    Write-Host 'Database import completed.' -ForegroundColor Green

    if ($Scope -eq 'full') {
        Write-Host '5/5 Run migrations and seed on Laravel Cloud...' -ForegroundColor Yellow
        Run-Cloud-Artisan 'php artisan migrate --force'
        Run-Cloud-Artisan 'php artisan db:seed --force'
        Write-Host 'Migrations and seed completed.' -ForegroundColor Green
    }
}

if (Test-Path $dumpFile) { Remove-Item $dumpFile -Force }
if (Test-Path $phpIniDir) { Remove-Item $phpIniDir -Recurse -Force }

Write-Host ''
Write-Host '=== SYNC COMPLETE ===' -ForegroundColor Green
Write-Host 'Local -> Cloud: code + database + migrate + seed.' -ForegroundColor Green

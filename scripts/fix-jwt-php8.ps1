# JWT PHP8 Compatibility Fix Script (PowerShell)
# Root Cause: thans/tp-jwt-auth v1.3.1 has three dynamic property issues
# 1. provider/JWT/Lcobucci.php: missing protected $signer;
# 2. claim/Factory.php: missing protected $request;
# 3. Manager.php: missing protected $provider;
# Solution: Add corresponding property declarations in the classes

$TARGET_FILE1 = "vendor\thans\tp-jwt-auth\src\provider\JWT\Lcobucci.php"
$TARGET_FILE2 = "vendor\thans\tp-jwt-auth\src\claim\Factory.php"
$TARGET_FILE3 = "vendor\thans\tp-jwt-auth\src\Manager.php"
$ErrorActionPreference = "Stop"

# Check if target files exist
$file1Exists = Test-Path $TARGET_FILE1
$file2Exists = Test-Path $TARGET_FILE2
$file3Exists = Test-Path $TARGET_FILE3

if (-not $file1Exists -and -not $file2Exists -and -not $file3Exists) {
    Write-Host "Warning: Target files do not exist, skipping fix" -ForegroundColor Yellow
    exit 0
}

# Check if already fixed
$fixed1 = $false
$fixed2 = $false
$fixed3 = $false

if ($file1Exists) {
    $content1 = Get-Content $TARGET_FILE1 -Raw
    if ($content1 -match 'class Lcobucci extends Provider\s*\{\s*protected \$signer;') {
        $fixed1 = $true
    }
}

if ($file2Exists) {
    $content2 = Get-Content $TARGET_FILE2 -Raw
    if ($content2 -match 'class Factory\s*\{\s*protected \$request;') {
        $fixed2 = $true
    }
}

if ($file3Exists) {
    $content3 = Get-Content $TARGET_FILE3 -Raw
    if ($content3 -match 'protected \$provider;') {
        $fixed3 = $true
    }
}

if ($fixed1 -and $fixed2 -and $fixed3) {
    Write-Host "JWT PHP8 compatibility already fully fixed" -ForegroundColor Green
    exit 0
}

# Apply fix
Write-Host "Fixing JWT PHP8 compatibility..." -ForegroundColor Cyan

# Fix Lcobucci.php
if ($file1Exists -and -not $fixed1) {
    try {
        $lines = Get-Content $TARGET_FILE1
        $newLines = @()
        $fixed = $false
        
        for ($i = 0; $i -lt $lines.Count; $i++) {
            $newLines += $lines[$i]
            
            if ($lines[$i] -match '^class Lcobucci extends Provider' -and $i+1 -lt $lines.Count -and $lines[$i+1] -match '^\{') {
                $newLines += $lines[$i+1]
                $newLines += "    protected `$signer;"
                $i++
                $fixed = $true
            }
        }
        
        if ($fixed) {
            $newLines | Set-Content $TARGET_FILE1 -Encoding UTF8
            Write-Host "Lcobucci.php fixed successfully" -ForegroundColor Green
        }
    } catch {
        Write-Host "Error fixing Lcobucci.php: $_" -ForegroundColor Red
    }
}

# Fix Factory.php
if ($file2Exists -and -not $fixed2) {
    try {
        $lines = Get-Content $TARGET_FILE2
        $newLines = @()
        $fixed = $false
        
        for ($i = 0; $i -lt $lines.Count; $i++) {
            $newLines += $lines[$i]
            
            if ($lines[$i] -match '^class Factory' -and $i+1 -lt $lines.Count -and $lines[$i+1] -match '^\{') {
                $newLines += $lines[$i+1]
                $newLines += "    protected `$request;"
                $i++
                $fixed = $true
            }
        }
        
        if ($fixed) {
            $newLines | Set-Content $TARGET_FILE2 -Encoding UTF8
            Write-Host "Factory.php fixed successfully" -ForegroundColor Green
        }
    } catch {
        Write-Host "Error fixing Factory.php: $_" -ForegroundColor Red
    }
}

# Fix Manager.php
if ($file3Exists -and -not $fixed3) {
    try {
        $lines = Get-Content $TARGET_FILE3
        $newLines = @()
        $fixed = $false
        
        for ($i = 0; $i -lt $lines.Count; $i++) {
            $newLines += $lines[$i]
            
            # Insert after "protected $payload;"
            if ($lines[$i] -match 'protected \$payload;') {
                $newLines += ""
                $newLines += "    protected `$provider;"
                $fixed = $true
            }
        }
        
        if ($fixed) {
            $newLines | Set-Content $TARGET_FILE3 -Encoding UTF8
            Write-Host "Manager.php fixed successfully" -ForegroundColor Green
        }
    } catch {
        Write-Host "Error fixing Manager.php: $_" -ForegroundColor Red
    }
}
    Write-Host "Error: Fix failed: $_" -ForegroundColor Red
    Write-Host "Please manually edit: $TARGET_FILE" -ForegroundColor Yellow
    Write-Host "Add after 'class Lcobucci extends Provider {': protected `$signer;" -ForegroundColor Yellow
    exit 1
}

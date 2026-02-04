# JWT PHP8 Compatibility Fix Script (PowerShell)
# Root Cause: thans/tp-jwt-auth v1.3.1 uses $this->signer in constructor
# but the class does not declare protected $signer property
# causing PHP8 dynamic property warning
# Solution: Add protected $signer; property declaration in the class

$TARGET_FILE = "vendor\thans\tp-jwt-auth\src\provider\JWT\Lcobucci.php"
$ErrorActionPreference = "Stop"

# Check if target file exists
if (-not (Test-Path $TARGET_FILE)) {
    Write-Host "Warning: Target file does not exist: $TARGET_FILE" -ForegroundColor Yellow
    exit 0
}

# Check if already fixed
$content = Get-Content $TARGET_FILE -Raw
if ($content -match 'class Lcobucci extends Provider\s*\{\s*protected \$signer;') {
    Write-Host "JWT PHP8 compatibility already fixed" -ForegroundColor Green
    exit 0
}

# Apply fix
Write-Host "Fixing JWT PHP8 compatibility..." -ForegroundColor Cyan

try {
    $lines = Get-Content $TARGET_FILE
    $newLines = @()
    $fixed = $false
    
    for ($i = 0; $i -lt $lines.Count; $i++) {
        $newLines += $lines[$i]
        
        # Insert after "class Lcobucci extends Provider" and "{"
        if ($lines[$i] -match '^class Lcobucci extends Provider' -and $i+1 -lt $lines.Count -and $lines[$i+1] -match '^\{') {
            $newLines += $lines[$i+1]  # Add {
            $newLines += "    protected `$signer;"  # Insert new property
            $i++  # Skip the processed {
            $fixed = $true
        }
    }
    
    if ($fixed) {
        $newLines | Set-Content $TARGET_FILE -Encoding UTF8
        Write-Host "JWT PHP8 compatibility fixed successfully" -ForegroundColor Green
    } else {
        Write-Host "Warning: Could not find the location to fix" -ForegroundColor Yellow
        exit 1
    }
} catch {
    Write-Host "Error: Fix failed: $_" -ForegroundColor Red
    Write-Host "Please manually edit: $TARGET_FILE" -ForegroundColor Yellow
    Write-Host "Add after 'class Lcobucci extends Provider {': protected `$signer;" -ForegroundColor Yellow
    exit 1
}

# Bengali Fonts

This directory should contain Bengali fonts for PDF generation.

## Required Fonts

### 1. Nikosh.ttf
- Download from: https://www.omicronlab.com/bangla-fonts.html
- Or from: https://github.com/potasiyam/Bangla-Fonts/raw/master/Nikosh.ttf
- Place the file as: `fonts/Nikosh.ttf`

### 2. Noto Sans Bengali
- Download from Google Fonts: https://fonts.google.com/noto/specimen/Noto+Sans+Bengali
- Or direct download: https://github.com/notofonts/bengali/raw/main/fonts/NotoSansBengali/hinted/ttf/NotoSansBengali-Regular.ttf
- Place the file as: `fonts/NotoSansBengali-Regular.ttf`

## Installation Instructions

### Option 1: Manual Download
1. Visit the links above
2. Download the font files
3. Place them in this `fonts/` directory

### Option 2: Using wget (Linux/Mac)
```bash
cd fonts
wget https://github.com/potasiyam/Bangla-Fonts/raw/master/Nikosh.ttf
wget https://github.com/notofonts/bengali/raw/main/fonts/NotoSansBengali/hinted/ttf/NotoSansBengali-Regular.ttf
```

### Option 3: Using curl (Linux/Mac)
```bash
cd fonts
curl -L -o Nikosh.ttf https://github.com/potasiyam/Bangla-Fonts/raw/master/Nikosh.ttf
curl -L -o NotoSansBengali-Regular.ttf https://github.com/notofonts/bengali/raw/main/fonts/NotoSansBengali/hinted/ttf/NotoSansBengali-Regular.ttf
```

### Option 4: Using PowerShell (Windows)
```powershell
cd fonts
Invoke-WebRequest -Uri "https://github.com/potasiyam/Bangla-Fonts/raw/master/Nikosh.ttf" -OutFile "Nikosh.ttf"
Invoke-WebRequest -Uri "https://github.com/notofonts/bengali/raw/main/fonts/NotoSansBengali/hinted/ttf/NotoSansBengali-Regular.ttf" -OutFile "NotoSansBengali-Regular.ttf"
```

## Verification

After downloading, verify the fonts are present:
- `fonts/Nikosh.ttf` (should be ~200-300 KB)
- `fonts/NotoSansBengali-Regular.ttf` (should be ~400-500 KB)

## License Information

- **Nikosh**: Free for personal and commercial use
- **Noto Sans Bengali**: Open Font License (OFL)

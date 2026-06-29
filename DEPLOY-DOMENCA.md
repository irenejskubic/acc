Deploy to Domenca.si

Build profile
- This package is performance-optimized (image-first portfolio).
- Heavy video files were removed to keep upload and page delivery fast.
- Core page sections, language switch (EN/SI/DE), and portfolio previews remain functional.

What to upload
- Upload everything inside this folder to your domain document root (usually public_html):
  - index.html
  - logos/
  - videos/
  - portfolio-images/
  - .htaccess

Recommended steps (cPanel File Manager)
1. Open Domenca cPanel.
2. Go to File Manager.
3. Open your domain root folder (usually public_html).
4. Delete or back up old website files.
5. Upload all files from this folder.
6. Confirm that index.html is directly in public_html.

Recommended steps (FTP)
1. Connect to your hosting via FTP (FileZilla).
2. Open remote folder public_html.
3. Upload all files from this folder.
4. Ensure transfer mode is Binary/Auto (important for video files).

Post-deploy checks
- Open your domain and verify:
  - Hero section loads
  - Logos load
  - Portfolio thumbnails load
  - Videos play
  - Language switch EN/SI/DE works

Notes
- Asset paths are relative (./...), so it works in root and subfolder hosting.
- No Node.js server is required. This is a static website.

import os
import shutil
import subprocess
from PIL import Image

BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
PUBLIC_DIR = os.path.join(BASE_DIR, 'public')
PUBLIC_IMG_DIR = os.path.join(PUBLIC_DIR, 'img')
PUBLIC_PROJECTS_DIR = os.path.join(PUBLIC_IMG_DIR, 'project-preview')
STORAGE_PUBLIC_DIR = os.path.join(BASE_DIR, 'storage', 'app', 'public')
ORIGINALS_DIR = os.path.join(BASE_DIR, 'resources', 'images', 'originals')
ORIGINALS_PROJECTS_DIR = os.path.join(ORIGINALS_DIR, 'project-preview')

os.makedirs(ORIGINALS_DIR, exist_ok=True)
os.makedirs(ORIGINALS_PROJECTS_DIR, exist_ok=True)
os.makedirs(STORAGE_PUBLIC_DIR, exist_ok=True)

def optimize_image(src_path, dest_dir, base_name, widths=[400, 800, 1200], default_width=800, is_hero=False):
    os.makedirs(dest_dir, exist_ok=True)
    orig_im = Image.open(src_path)
    orig_w, orig_h = orig_im.size
    aspect = orig_h / orig_w
    
    # Determine valid widths
    valid_widths = [w for w in widths if w <= orig_w]
    if not valid_widths:
        valid_widths = [orig_w]
    if orig_w not in valid_widths and (is_hero or orig_w <= 1200):
        valid_widths.append(orig_w)
    valid_widths = sorted(list(set(valid_widths)))

    print(f"Processing {base_name}: orig {orig_w}x{orig_h}, generating widths {valid_widths}")

    for w in valid_widths:
        h = max(1, int(w * aspect))
        resized = orig_im.resize((w, h), Image.Resampling.LANCZOS)
        
        # Save WebP
        webp_name = f"{base_name}-{w}.webp"
        webp_path = os.path.join(dest_dir, webp_name)
        resized.save(webp_path, 'WEBP', quality=80, method=6)
        
        # Save AVIF using ImageMagick
        tmp_png = f"/tmp/{base_name}_{w}_tmp.png"
        resized.save(tmp_png, 'PNG')
        avif_name = f"{base_name}-{w}.avif"
        avif_path = os.path.join(dest_dir, avif_name)
        subprocess.run(['magick', tmp_png, '-quality', '70', avif_path], check=True, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
        
        # If this is default width, create base .webp and .avif
        closest_w = min(valid_widths, key=lambda x: abs(x - default_width))
        if w == closest_w:
            shutil.copy2(webp_path, os.path.join(dest_dir, f"{base_name}.webp"))
            shutil.copy2(avif_path, os.path.join(dest_dir, f"{base_name}.avif"))

        if os.path.exists(tmp_png):
            os.remove(tmp_png)

    # Fallback PNG / JPG
    max_fallback_w = min(orig_w, 800 if not is_hero else 700)
    fb_h = max(1, int(max_fallback_w * aspect))
    fallback_im = orig_im.resize((max_fallback_w, fb_h), Image.Resampling.LANCZOS)
    ext = os.path.splitext(src_path)[1].lower()
    fallback_path = os.path.join(dest_dir, f"{base_name}{ext}")
    
    if ext in ['.jpg', '.jpeg']:
        fallback_im.convert('RGB').save(fallback_path, 'JPEG', quality=82, optimize=True)
    else:
        # Quantize or compress PNG to keep it small
        if not is_hero and max_fallback_w >= 800:
            try:
                # If RGB screenshot, 256 color adaptive palette saves massive bandwidth
                if fallback_im.mode == 'RGBA':
                    alpha = fallback_im.split()[3]
                    if alpha.getextrema() == (255, 255):
                        q_im = fallback_im.convert('RGB').quantize(colors=256)
                    else:
                        q_im = fallback_im
                else:
                    q_im = fallback_im.convert('RGB').quantize(colors=256)
                q_im.save(fallback_path, optimize=True)
            except Exception:
                fallback_im.save(fallback_path, optimize=True)
        else:
            fallback_im.save(fallback_path, optimize=True)

def main():
    print("=== Step 1: Backup originals of project previews ===")
    project_files = [f for f in os.listdir(PUBLIC_PROJECTS_DIR) if f.endswith('.png') and not f.endswith(('-400.png', '-800.png', '-1200.png'))]
    for pf in project_files:
        src = os.path.join(PUBLIC_PROJECTS_DIR, pf)
        dest = os.path.join(ORIGINALS_PROJECTS_DIR, pf)
        if not os.path.exists(dest):
            shutil.copy2(src, dest)
            print(f"Copied master {pf} to originals")

    print("\n=== Step 2: Backup general images ===")
    general_images = ['me-icon.png', 'me-icon-smiling.png', 'R.png', 'OIP.jpg']
    for gi in general_images:
        src = os.path.join(PUBLIC_IMG_DIR, gi)
        dest = os.path.join(ORIGINALS_DIR, gi)
        if os.path.exists(src) and not os.path.exists(dest):
            shutil.copy2(src, dest)
            print(f"Copied master {gi} to originals")

    print("\n=== Step 3: Optimize project previews ===")
    for pf in sorted(os.listdir(ORIGINALS_PROJECTS_DIR)):
        if pf.endswith('.png'):
            src = os.path.join(ORIGINALS_PROJECTS_DIR, pf)
            base_name = os.path.splitext(pf)[0]
            optimize_image(src, PUBLIC_PROJECTS_DIR, base_name, widths=[400, 800, 1200], default_width=800, is_hero=False)

    print("\n=== Step 4: Optimize me-icon.png ===")
    me_icon_src = os.path.join(ORIGINALS_DIR, 'me-icon.png')
    if os.path.exists(me_icon_src):
        optimize_image(me_icon_src, PUBLIC_IMG_DIR, 'me-icon', widths=[400, 700], default_width=700, is_hero=True)

    print("\n=== Step 5: Optimize me-icon-smiling.png ===")
    me_smiling_src = os.path.join(ORIGINALS_DIR, 'me-icon-smiling.png')
    if os.path.exists(me_smiling_src):
        optimize_image(me_smiling_src, PUBLIC_IMG_DIR, 'me-icon-smiling', widths=[400, 700], default_width=700, is_hero=True)

    print("\n=== Step 6: Optimize R.png ===")
    r_src = os.path.join(ORIGINALS_DIR, 'R.png')
    if os.path.exists(r_src):
        optimize_image(r_src, PUBLIC_IMG_DIR, 'R', widths=[400, 800], default_width=800, is_hero=False)

    print("\n=== Step 7: Populate storage/app/public with optimized variants ===")
    # 1. Hero image: 01JQ42KG7VZEPZQA1N8F2FZZRW
    optimize_image(me_icon_src, STORAGE_PUBLIC_DIR, '01JQ42KG7VZEPZQA1N8F2FZZRW', widths=[400, 700], default_width=700, is_hero=True)

    # 2. Project 1: Berchuma Furniture (01JQ7JFJGEEYB56MT1F8FR0SXP)
    berchuma_src = os.path.join(ORIGINALS_PROJECTS_DIR, 'comfy-coffee-preview.png')
    optimize_image(berchuma_src, STORAGE_PUBLIC_DIR, '01JQ7JFJGEEYB56MT1F8FR0SXP', widths=[400, 800, 1200], default_width=800, is_hero=False)

    # 3. Project 2: Mini school management (01JQCKR9E2QYV1YSA92X2NT2FP)
    school_src = os.path.join(ORIGINALS_PROJECTS_DIR, 'nunflix-preview.png')
    optimize_image(school_src, STORAGE_PUBLIC_DIR, '01JQCKR9E2QYV1YSA92X2NT2FP', widths=[400, 800, 1200], default_width=800, is_hero=False)

    # 4. Certificates
    cert_srcs = ['rnn-twitter-bot-preview.png', 'subuwu-preview.png', 'amazon2-preview.png']
    cert_hashes = ['01JQEAVB7AWYJAYPK0PT7MF9ZW', '01JQEAW0D4RCRCHYADN1NFMAJ0', '01JQEAWXQAG3PAV3WZMRR7CR5B']
    for c_src, c_hash in zip(cert_srcs, cert_hashes):
        c_full = os.path.join(ORIGINALS_PROJECTS_DIR, c_src)
        if os.path.exists(c_full):
            optimize_image(c_full, STORAGE_PUBLIC_DIR, c_hash, widths=[400, 800, 1200], default_width=800, is_hero=False)

    print("\n=== Step 8: Generate favicons ===")
    me_im = Image.open(me_icon_src)
    # apple-touch-icon: 180x180
    me_im.resize((180, 180), Image.Resampling.LANCZOS).save(os.path.join(PUBLIC_DIR, 'apple-touch-icon.png'), 'PNG', optimize=True)
    # favicon-32x32: 32x32
    me_im.resize((32, 32), Image.Resampling.LANCZOS).save(os.path.join(PUBLIC_DIR, 'favicon-32x32.png'), 'PNG', optimize=True)
    # favicon-16x16: 16x16
    me_im.resize((16, 16), Image.Resampling.LANCZOS).save(os.path.join(PUBLIC_DIR, 'favicon-16x16.png'), 'PNG', optimize=True)
    # favicon.ico: 32x32
    me_im.resize((32, 32), Image.Resampling.LANCZOS).save(os.path.join(PUBLIC_DIR, 'favicon.ico'), format='ICO')
    print("Favicons generated successfully")

    print("\n=== Optimization Complete ===")

if __name__ == '__main__':
    main()

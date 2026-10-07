/**
 * Generate WebP companions for public PNG/JPEG assets (keeps originals as fallback).
 * Run: npm run images:optimize
 */
import { readdir, stat } from 'node:fs/promises';
import path from 'node:path';
import sharp from 'sharp';

import { fileURLToPath } from 'node:url';

const ROOT = path.join(path.dirname(fileURLToPath(import.meta.url)), '..', 'public', 'images');
const MAX_WIDTH = 1920;
const WEBP_QUALITY = 82;

async function walk(dir) {
    const entries = await readdir(dir, { withFileTypes: true });
    const files = [];

    for (const entry of entries) {
        const full = path.join(dir, entry.name);
        if (entry.isDirectory()) {
            files.push(...(await walk(full)));
        } else if (/\.(png|jpe?g)$/i.test(entry.name) && !entry.name.endsWith('.webp')) {
            files.push(full);
        }
    }

    return files;
}

async function optimize(file) {
    const image = sharp(file);
    const meta = await image.metadata();
    const resize =
        meta.width && meta.width > MAX_WIDTH
            ? { width: MAX_WIDTH, withoutEnlargement: true }
            : null;

    const webpPath = file.replace(/\.(png|jpe?g)$/i, '.webp');
    let pipeline = sharp(file);
    if (resize) {
        pipeline = pipeline.resize(resize);
    }

    await pipeline.webp({ quality: WEBP_QUALITY, effort: 4 }).toFile(webpPath);

    const before = (await stat(file)).size;
    const after = (await stat(webpPath)).size;

    return { file: path.relative(ROOT, file), webpPath: path.relative(ROOT, webpPath), before, after };
}

const files = await walk(ROOT);
let totalBefore = 0;
let totalAfter = 0;

for (const file of files) {
    const result = await optimize(file);
    totalBefore += result.before;
    totalAfter += result.after;
    console.log(
        `${result.file} → ${result.webpPath} (${(result.before / 1024).toFixed(0)}KB → ${(result.after / 1024).toFixed(0)}KB WebP)`,
    );
}

console.log(
    `\n${files.length} files. PNG/JPEG total ${(totalBefore / 1024 / 1024).toFixed(1)}MB; WebP output ${(totalAfter / 1024 / 1024).toFixed(1)}MB.`,
);

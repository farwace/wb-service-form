import jsQR from 'jsqr';

// Утилита декодирования QR-кода с файла-фото.
// jsQR хорошо читает «чистые» QR, но фото с камеры часто крупные/размытые,
// поэтому пробуем несколько масштабов — от большего к меньшему.

// Максимальные стороны изображений, на которых пробуем декодирование (px).
const SCAN_SIZES = [1200, 800, 500];

function loadImage(url: string): Promise<HTMLImageElement> {
  return new Promise((resolve, reject) => {
    const img = new Image();
    img.onload = () => resolve(img);
    img.onerror = () => reject(new Error('Не удалось загрузить изображение'));
    img.src = url;
  });
}

function tryDecode(img: HTMLImageElement, maxSize: number): string | null {
  const scale = Math.min(1, maxSize / Math.max(img.naturalWidth, img.naturalHeight));
  const w = Math.max(1, Math.round(img.naturalWidth * scale));
  const h = Math.max(1, Math.round(img.naturalHeight * scale));

  const canvas = document.createElement('canvas');
  canvas.width = w;
  canvas.height = h;
  const ctx = canvas.getContext('2d', {willReadFrequently: true});
  if (!ctx) return null;
  ctx.drawImage(img, 0, 0, w, h);

  const imageData = ctx.getImageData(0, 0, w, h);
  const result = jsQR(imageData.data, imageData.width, imageData.height, {inversionAttempts: 'attemptBoth'});
  return result?.data ? result.data : null;
}

/**
 * Пытается прочитать QR с фото. Возвращает текст из QR или null, если не удалось.
 */
export async function decodeQrFromFile(file: File): Promise<string | null> {
  const url = URL.createObjectURL(file);
  try {
    const img = await loadImage(url);
    for (const size of SCAN_SIZES) {
      const text = tryDecode(img, size);
      if (text) return text.trim();
    }
    return null;
  } catch {
    return null;
  } finally {
    URL.revokeObjectURL(url);
  }
}

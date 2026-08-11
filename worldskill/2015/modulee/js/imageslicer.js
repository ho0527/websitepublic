/**
 * ImageSlicer
 * 負責圖片的讀取、驗證、置中裁切成正方形，以及切成 n x n 的碎片。
 * 全部在瀏覽器端以 Canvas 完成，圖片不會上傳到伺服器。
 */
class ImageSlicer {

	/** 允許的檔案格式（題目指定僅接受 JPG） */
	static get ACCEPTED_MIME() {
		return ['image/jpeg', 'image/jpg', 'image/pjpeg'];
	}

	/**
	 * 驗證檔案是否為 JPG。
	 * @param {File} file
	 * @returns {boolean}
	 */
	static isJpeg(file) {
		if (!file) {
			return false;
		}
		const mimeOk = ImageSlicer.ACCEPTED_MIME.indexOf((file.type || '').toLowerCase()) !== -1;
		const extOk = /\.jpe?g$/i.test(file.name || '');
		// 部分系統回報的 MIME 可能為空字串，因此附帶副檔名判斷
		return mimeOk || (file.type === '' && extOk);
	}

	/**
	 * 讀取檔案為 Data URL。
	 * @param {File} file
	 * @returns {Promise<string>}
	 */
	static readFile(file) {
		return new Promise((resolve, reject) => {
			const reader = new FileReader();
			reader.onload = () => resolve(String(reader.result));
			reader.onerror = () => reject(new Error('Could not read the image file.'));
			reader.readAsDataURL(file);
		});
	}

	/**
	 * 由 Data URL 建立 Image 物件。
	 * @param {string} dataUrl
	 * @returns {Promise<HTMLImageElement>}
	 */
	static loadImage(dataUrl) {
		return new Promise((resolve, reject) => {
			const img = new Image();
			img.onload = () => resolve(img);
			img.onerror = () => reject(new Error('The file is not a valid image.'));
			img.src = dataUrl;
		});
	}

	/**
	 * 以「置中」的方式把圖片裁成正方形，並縮放到指定邊長。
	 * @param {string} dataUrl 原始圖片
	 * @param {number} size    輸出的正方形邊長（px）
	 * @returns {Promise<string>} 裁切後的 JPEG Data URL
	 */
	static async cropCenteredSquare(dataUrl, size) {
		const img = await ImageSlicer.loadImage(dataUrl);
		const side = Math.min(img.naturalWidth, img.naturalHeight);
		// 置中裁切：左右（或上下）各裁掉一半的差值
		const sx = (img.naturalWidth - side) / 2;
		const sy = (img.naturalHeight - side) / 2;

		const canvas = document.createElement('canvas');
		canvas.width = size;
		canvas.height = size;
		const ctx = canvas.getContext('2d');
		ctx.imageSmoothingQuality = 'high';
		ctx.drawImage(img, sx, sy, side, side, 0, 0, size, size);

		return canvas.toDataURL('image/jpeg', 0.86);
	}

	/**
	 * 把正方形圖片切成 n x n 塊。
	 * @param {string} squareDataUrl 已裁成正方形的圖片
	 * @param {number} n             每邊的塊數（2 / 3 / 4）
	 * @returns {Promise<string[]>}  由左上到右下排序的碎片 Data URL 陣列
	 */
	static async slice(squareDataUrl, n) {
		const img = await ImageSlicer.loadImage(squareDataUrl);
		const src = img.naturalWidth;          // 來源正方形邊長
		const step = src / n;                  // 每塊在來源圖上的邊長
		const out = Math.round(step);          // 每塊輸出的像素尺寸

		const canvas = document.createElement('canvas');
		canvas.width = out;
		canvas.height = out;
		const ctx = canvas.getContext('2d');

		const pieces = [];
		for (let row = 0; row < n; row += 1) {
			for (let col = 0; col < n; col += 1) {
				ctx.clearRect(0, 0, out, out);
				ctx.drawImage(img, col * step, row * step, step, step, 0, 0, out, out);
				pieces.push(canvas.toDataURL('image/jpeg', 0.9));
			}
		}
		return pieces;
	}
}

window.ImageSlicer = ImageSlicer;

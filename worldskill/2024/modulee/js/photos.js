/*
	照片資料來源：檔案輸入、拖放載入、範例照片，以及照片排序
*/

/** 題目提供的範例照片檔名 */
const SAMPLE_PHOTO_FILE_NAMES = [
	"example n1.jpg",
	"example n2.jpg",
	"example n3.jpg",
	"example n4.jpg",
	"example-n5.jpg",
	"example-n6.jpg",
	"example-n7.jpg"
];

const SAMPLE_PHOTO_DIRECTORY = "material/image/";

const PhotoStore = {
	/** 照片清單，每筆為 { id, caption, url, isObjectUrl } */
	photos: [],

	/** 資料變更時要通知的函式清單 */
	changeCallbacks: [],

	/** 註冊資料變更的監聽函式 */
	onChange: function(callback){
		this.changeCallbacks.push(callback);
	},

	/** 通知所有監聽者 */
	notifyChange: function(){
		const photos = this.photos;

		this.changeCallbacks.forEach(function(callback){
			callback(photos);
		});
	},

	/** 釋放先前用 createObjectURL 產生的網址，避免記憶體洩漏 */
	releaseObjectUrls: function(){
		this.photos.forEach(function(photo){
			if(photo.isObjectUrl){
				URL.revokeObjectURL(photo.url);
			}
		});
	},

	/** 以使用者選取或拖入的檔案建立照片清單（只接受圖片檔） */
	setFiles: function(fileList){
		const imageFiles = Array.prototype.slice.call(fileList).filter(function(file){
			return file.type.indexOf("image/") === 0;
		});

		if(imageFiles.length === 0){
			return false;
		}

		this.releaseObjectUrls();

		this.photos = imageFiles.map(function(file, fileIndex){
			return {
				id: "file-" + Date.now() + "-" + fileIndex,
				caption: captionFromFileName(file.name),
				url: URL.createObjectURL(file),
				isObjectUrl: true
			};
		});

		this.notifyChange();

		return true;
	},

	/** 載入題目提供的範例照片 */
	loadSamplePhotos: function(){
		this.releaseObjectUrls();

		this.photos = SAMPLE_PHOTO_FILE_NAMES.map(function(fileName, fileIndex){
			return {
				id: "sample-" + fileIndex,
				caption: captionFromFileName(fileName),
				url: SAMPLE_PHOTO_DIRECTORY + encodeURIComponent(fileName),
				isObjectUrl: false
			};
		});

		this.notifyChange();
	},

	/** 把第 fromIndex 張照片移動到 toIndex 的位置（拖放排序使用） */
	movePhoto: function(fromIndex, toIndex){
		if(
			fromIndex === toIndex ||
			fromIndex < 0 || fromIndex >= this.photos.length ||
			toIndex < 0 || toIndex >= this.photos.length
		){
			return;
		}

		const movingPhoto = this.photos.splice(fromIndex, 1)[0];

		this.photos.splice(toIndex, 0, movingPhoto);
		this.notifyChange();
	}
};

/* ==========================================================================
   刊登 / 編輯房屋
   對應 API 5（讀取原有資料）、API 7（刊登房屋）、API 8（編輯房屋）
   可調整圖片順序並指定封面圖片
   ========================================================================== */

renderHeader("publish");

/** 編輯模式時的房屋編號，新增時為 null */
const editingHouseId = queryParam("id");

/**
 * 圖片清單
 * 每個項目為 { kind: "existing" | "new", url: string, file?: File }
 */
let imageItems = [];

/** 封面圖片在 imageItems 中的索引 */
let coverIndex = 0;

/** 初始化頁面 */
async function init() {
	const messageElement = document.getElementById("message");

	if (editingHouseId) {
		document.getElementById("page-title").textContent = "Edit House";
		document.title = "編輯房屋 - Best platform to deal the house";

		try {
			const house = await api("GET", "/house/" + encodeURIComponent(editingHouseId));

			document.getElementById("title").value = house.title;
			document.getElementById("description").value = house.description || "";
			document.getElementById("price").value = house.price;
			document.getElementById("square").value = house.square;
			document.getElementById("room").value = house.room;
			document.getElementById("floor").value = house.floor;
			document.getElementById("total_floor").value = house.total_floor;
			document.getElementById("age").value = house.age;
			document.getElementById("address").value = house.address || "";

			imageItems = (house.images || []).map((url) => ({ kind: "existing", url: url }));
			coverIndex = 0;
			renderImageEditor();
		} catch (error) {
			showMessage(messageElement, error.message);
		}
	}

	document.getElementById("images").addEventListener("change", (event) => {
		Array.from(event.target.files).forEach((file) => {
			imageItems.push({ kind: "new", file: file, url: URL.createObjectURL(file) });
		});
		event.target.value = "";
		renderImageEditor();
	});

	document.getElementById("house-form").addEventListener("submit", submitForm);
}

/** 繪製圖片編輯器 */
function renderImageEditor() {
	const editor = document.getElementById("image-editor");

	if (imageItems.length === 0) {
		editor.innerHTML = '<div class="meta">尚未選擇圖片</div>';
		return;
	}

	if (coverIndex >= imageItems.length) {
		coverIndex = 0;
	}

	editor.innerHTML = imageItems.map((item, index) => `
		<div class="image-item ${index === coverIndex ? "cover" : ""}">
			<img src="${escapeHtml(item.url)}" alt="圖片 ${index + 1}">
			<div class="cover-tag">${index === coverIndex ? "封面" : "&nbsp;"}</div>
			<div class="tools">
				<button type="button" data-image-action="left" data-index="${index}" title="往前">←</button>
				<button type="button" data-image-action="cover" data-index="${index}" title="設為封面">★</button>
				<button type="button" data-image-action="right" data-index="${index}" title="往後">→</button>
				<button type="button" data-image-action="remove" data-index="${index}" title="移除">✕</button>
			</div>
		</div>
	`).join("");

	editor.querySelectorAll("[data-image-action]").forEach((button) => {
		button.addEventListener("click", () => {
			const index = Number(button.dataset.index);
			const action = button.dataset.imageAction;

			if (action === "cover") {
				coverIndex = index;
			} else if (action === "remove") {
				imageItems.splice(index, 1);
				if (coverIndex >= imageItems.length) {
					coverIndex = Math.max(0, imageItems.length - 1);
				} else if (coverIndex === index) {
					coverIndex = 0;
				}
			} else if (action === "left" && index > 0) {
				swapImages(index, index - 1);
			} else if (action === "right" && index < imageItems.length - 1) {
				swapImages(index, index + 1);
			}

			renderImageEditor();
		});
	});
}

/** 交換兩張圖片的位置，並維持封面標記 */
function swapImages(from, to) {
	const temp = imageItems[from];
	imageItems[from] = imageItems[to];
	imageItems[to] = temp;

	if (coverIndex === from) {
		coverIndex = to;
	} else if (coverIndex === to) {
		coverIndex = from;
	}
}

/** 送出表單 */
async function submitForm(event) {
	event.preventDefault();

	const messageElement = document.getElementById("message");
	const submitButton = document.getElementById("submit");

	hideMessage(messageElement);
	submitButton.disabled = true;

	const form = new FormData();
	["title", "description", "price", "square", "room", "floor", "total_floor", "age", "address"].forEach((field) => {
		form.append(field, document.getElementById(field).value);
	});

	// 圖片順序：keep 表示既有圖片、new 表示本次上傳的圖片
	let newFileIndex = 0;
	imageItems.forEach((item) => {
		if (item.kind === "existing") {
			form.append("keep_paths[]", item.url);
			form.append("order[]", "keep:" + item.url);
		} else {
			form.append("images[]", item.file);
			form.append("order[]", "new:" + newFileIndex);
			newFileIndex++;
		}
	});

	if (imageItems.length > 0) {
		form.append("cover_index", String(coverIndex));
	}

	try {
		if (editingHouseId) {
			// 編輯使用 PUT + multipart/form-data
			await api("PUT", "/house/" + encodeURIComponent(editingHouseId), { form: form });
		} else {
			await api("POST", "/house", { form: form });
		}

		location.href = "publish.html";
	} catch (error) {
		showMessage(messageElement, error.message);
	} finally {
		submitButton.disabled = false;
	}
}

// 需要登入才能刊登或編輯房屋
if (requireLogin()) {
	init();
}

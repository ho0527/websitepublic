/* ==========================================================================
   查看房屋：房屋詳細資訊與圖片輪播器
   對應 API 5（查看房屋）
   ========================================================================== */

renderHeader("index");

/** 網址上的房屋編號 */
const houseId = queryParam("id");

/** 輪播器目前顯示的圖片索引 */
let carouselIndex = 0;

/** 房屋的圖片清單 */
let houseImages = [];

/** 載入房屋詳細資料 */
async function loadHouse() {
	const detailElement = document.getElementById("house-detail");
	const messageElement = document.getElementById("message");

	if (!houseId) {
		location.href = "index.html";
		return;
	}

	try {
		const house = await api("GET", "/house/" + encodeURIComponent(houseId), { auth: false });

		document.title = house.title + " - Best platform to deal the house";
		document.getElementById("breadcrumb-house").textContent = `House (no.${house.id})`;

		houseImages = Array.isArray(house.images) ? house.images : [];

		detailElement.innerHTML = `
			<div>
				<!-- 圖片輪播器 -->
				<div class="carousel" id="carousel">
					<div class="stage" id="carousel-stage"></div>
					<button type="button" class="arrow prev" id="carousel-prev" aria-label="上一張">‹</button>
					<button type="button" class="arrow next" id="carousel-next" aria-label="下一張">›</button>
				</div>
				<div class="thumb-strip" id="carousel-thumbs"></div>
			</div>
			<div>
				<h1>${escapeHtml(house.title)}</h1>
				<div class="meta">${escapeHtml(house.published_at)}</div>
				<div class="detail-price">${escapeHtml(formatMoney(house.price))}</div>
				<div class="meta">${escapeHtml(house.square)} square(s) | ${escapeHtml(formatMoney(unitPrice(house.price, house.square)))} per square</div>
				<div class="detail-specs">
					<div>
						<div class="value">${escapeHtml(house.room)}</div>
						<div class="label">Rooms</div>
					</div>
					<div>
						<div class="value">${escapeHtml(house.floor)}F/${escapeHtml(house.total_floor)}F</div>
						<div class="label">Floor</div>
					</div>
					<div>
						<div class="value">${escapeHtml(house.age)}</div>
						<div class="label">Years</div>
					</div>
				</div>
				<div>${escapeHtml(house.address)}</div>
				<div class="publisher">
					<div class="name">${escapeHtml(house.publisher.nickname)}</div>
					<div><a href="mailto:${escapeHtml(house.publisher.email)}">${escapeHtml(house.publisher.email)}</a></div>
				</div>
			</div>
			<div class="description">${escapeHtml(house.description)}</div>
		`;

		renderCarousel();

		document.getElementById("carousel-prev").addEventListener("click", () => moveCarousel(-1));
		document.getElementById("carousel-next").addEventListener("click", () => moveCarousel(1));
	} catch (error) {
		detailElement.innerHTML = "";
		showMessage(messageElement, error.message);
	}
}

/** 繪製輪播器目前的圖片與縮圖列 */
function renderCarousel() {
	const stage = document.getElementById("carousel-stage");
	const thumbs = document.getElementById("carousel-thumbs");

	if (houseImages.length === 0) {
		stage.innerHTML = '<span class="meta">沒有圖片</span>';
		thumbs.innerHTML = "";
		return;
	}

	stage.innerHTML = `<img src="${escapeHtml(houseImages[carouselIndex])}" alt="房屋圖片 ${carouselIndex + 1}">`;
	thumbs.innerHTML = houseImages
		.map((image, index) => `<img src="${escapeHtml(image)}" data-index="${index}" class="${index === carouselIndex ? "active" : ""}" alt="縮圖 ${index + 1}">`)
		.join("");

	thumbs.querySelectorAll("img").forEach((thumb) => {
		thumb.addEventListener("click", () => {
			carouselIndex = Number(thumb.dataset.index);
			renderCarousel();
		});
	});
}

/** 切換輪播器圖片 */
function moveCarousel(step) {
	if (houseImages.length === 0) {
		return;
	}
	carouselIndex = (carouselIndex + step + houseImages.length) % houseImages.length;
	renderCarousel();
}

loadHouse();

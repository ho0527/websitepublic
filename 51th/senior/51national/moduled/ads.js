/* ==========================================================================
   精選房屋列表（管理員）
   對應 API 14（取得精選房屋列表）、API 15（取消精選房屋）
   ========================================================================== */

renderHeader("ads");

/** 目前頁碼 */
let currentPage = 1;

/** 取得目前排序方向 */
let getOrder = () => "desc";

/** 初始化頁面事件 */
function init() {
	getOrder = bindOrderToggle(() => {
		currentPage = 1;
		loadAds();
	});

	bindSearchEvents(() => {
		currentPage = 1;
		loadAds();
	});

	loadAds();
}

/** 載入精選房屋列表 */
async function loadAds() {
	const listElement = document.getElementById("house-list");
	const messageElement = document.getElementById("message");

	hideMessage(messageElement);
	listElement.innerHTML = '<div class="empty">載入中…</div>';

	const query = readSearchConditions();
	query.order = getOrder();
	query.page = currentPage;

	try {
		const data = await api("GET", "/ads", { query });

		document.getElementById("result-title").textContent = `Result（共 ${data.total_count} 筆）`;

		if (data.ads.length === 0) {
			listElement.innerHTML = '<div class="empty">目前沒有精選房屋</div>';
		} else {
			listElement.innerHTML = data.ads.map((ad) => {
				const house = Object.assign({}, ad.house, { is_ad: true });
				const extra = `<div class="expired-at">到期時間：${escapeHtml(ad.expired_at)}</div>`;
				const actions = `<button type="button" class="button danger small" data-action="cancel" data-id="${escapeHtml(ad.id)}">Cancel</button>`;

				return houseCardHtml(house, { extra: extra, actions: actions });
			}).join("");
		}

		bindCancelActions();

		renderPagination(document.getElementById("pagination"), currentPage, data.total_count, (page) => {
			currentPage = page;
			loadAds();
			window.scrollTo({ top: 0, behavior: "smooth" });
		});
	} catch (error) {
		listElement.innerHTML = "";
		showMessage(messageElement, error.message);
	}
}

/** 綁定下架精選房屋按鈕 */
function bindCancelActions() {
	const messageElement = document.getElementById("message");

	document.querySelectorAll('[data-action="cancel"]').forEach((button) => {
		button.addEventListener("click", async () => {
			if (!confirm("確定要下架這個精選房屋嗎？")) {
				return;
			}

			hideMessage(messageElement);

			try {
				await api("DELETE", "/ads/" + encodeURIComponent(button.dataset.id));
				showMessage(messageElement, "已下架精選房屋", "success");
				loadAds();
			} catch (error) {
				showMessage(messageElement, error.message);
			}
		});
	});
}

// 只有管理員可以瀏覽精選房屋列表
if (requireAdmin()) {
	init();
}

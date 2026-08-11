/* ==========================================================================
   首頁：瀏覽所有房屋
   對應 API 4（取得房屋列表）
   ========================================================================== */

renderHeader("index");

/** 目前頁碼 */
let currentPage = 1;

/** 取得目前排序方向 */
const getOrder = bindOrderToggle(() => {
	currentPage = 1;
	loadHouses();
});

/** 載入房屋列表 */
async function loadHouses() {
	const listElement = document.getElementById("house-list");
	const messageElement = document.getElementById("message");

	hideMessage(messageElement);
	listElement.innerHTML = '<div class="empty">載入中…</div>';

	const query = readSearchConditions();
	query.order = getOrder();
	query.page = currentPage;

	try {
		const data = await api("GET", "/house", { query });

		document.getElementById("result-title").textContent = `Result（共 ${data.total_count} 筆）`;

		if (data.houses.length === 0) {
			listElement.innerHTML = '<div class="empty">查無符合條件的房屋</div>';
		} else {
			listElement.innerHTML = data.houses.map((house) => houseCardHtml(house)).join("");
		}

		renderPagination(document.getElementById("pagination"), currentPage, data.total_count, (page) => {
			currentPage = page;
			loadHouses();
			window.scrollTo({ top: 0, behavior: "smooth" });
		});
	} catch (error) {
		listElement.innerHTML = "";
		showMessage(messageElement, error.message);
	}
}

bindSearchEvents(() => {
	currentPage = 1;
	loadHouses();
});

loadHouses();

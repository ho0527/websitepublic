/* ==========================================================================
   刊登列表：管理自己刊登的房屋
   對應 API 6（自己的刊登列表）、API 9（刪除房屋）、
   API 10（申請精選房屋）、API 11（取消申請）
   ========================================================================== */

renderHeader("publish");

/** 目前頁碼 */
let currentPage = 1;

/** 取得目前排序方向 */
let getOrder = () => "desc";

/** 初始化頁面事件 */
function init() {
	getOrder = bindOrderToggle(() => {
		currentPage = 1;
		loadHouses();
	});

	bindSearchEvents(() => {
		currentPage = 1;
		loadHouses();
	});

	loadHouses();
}

/** 載入自己刊登的房屋 */
async function loadHouses() {
	const listElement = document.getElementById("house-list");
	const messageElement = document.getElementById("message");

	hideMessage(messageElement);
	listElement.innerHTML = '<div class="empty">載入中…</div>';

	const query = readSearchConditions();
	query.order = getOrder();
	query.page = currentPage;

	try {
		const data = await api("GET", "/user/house", { query });

		document.getElementById("result-title").textContent = `Result（共 ${data.total_count} 筆）`;

		if (data.houses.length === 0) {
			listElement.innerHTML = '<div class="empty">尚未刊登任何房屋</div>';
		} else {
			listElement.innerHTML = data.houses.map((house) => houseCardHtml(house, {
				extra: buildStatusHtml(house),
				actions: buildActionsHtml(house),
			})).join("");
		}

		bindCardActions();

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

/** 精選房屋申請狀態標示 */
function buildStatusHtml(house) {
	if (house.is_ad) {
		return '<div><span class="badge-status">精選房屋展示中</span></div>';
	}
	if (house.application_id) {
		return '<div><span class="badge-status">精選房屋申請審核中</span></div>';
	}
	return "";
}

/** 依照房屋狀態產生操作按鈕 */
function buildActionsHtml(house) {
	const buttons = [];

	if (house.application_id) {
		buttons.push(`<button type="button" class="button ghost small" data-action="cancel-application" data-application="${escapeHtml(house.application_id)}">Cancel the Application</button>`);
	} else if (!house.is_ad) {
		buttons.push(`<button type="button" class="button ghost small" data-action="apply" data-id="${escapeHtml(house.id)}">Apply to Ad</button>`);
	}

	buttons.push(`<button type="button" class="button danger small" data-action="delete" data-id="${escapeHtml(house.id)}">Delete</button>`);
	buttons.push(`<a class="button small" href="newhouse.html?id=${encodeURIComponent(house.id)}">Edit</a>`);

	return buttons.join("");
}

// 需要登入才能使用刊登列表
if (requireLogin()) {
	init();
}

/** 綁定卡片上的操作按鈕 */
function bindCardActions() {
	const messageElement = document.getElementById("message");

	document.querySelectorAll("[data-action]").forEach((button) => {
		button.addEventListener("click", async () => {
			const action = button.dataset.action;
			hideMessage(messageElement);

			try {
				if (action === "delete") {
					if (!confirm("確定要刪除這間房屋嗎？")) {
						return;
					}
					await api("DELETE", "/house/" + encodeURIComponent(button.dataset.id));
					showMessage(messageElement, "已刪除房屋", "success");
				} else if (action === "apply") {
					await api("POST", "/application", { json: { house_id: Number(button.dataset.id) } });
					showMessage(messageElement, "已送出精選房屋申請", "success");
				} else if (action === "cancel-application") {
					await api("DELETE", "/application/" + encodeURIComponent(button.dataset.application));
					showMessage(messageElement, "已取消精選房屋申請", "success");
				}

				loadHouses();
			} catch (error) {
				showMessage(messageElement, error.message);
			}
		});
	});
}

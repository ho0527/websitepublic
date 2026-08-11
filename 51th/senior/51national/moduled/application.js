/* ==========================================================================
   申請列表（管理員）
   對應 API 12（取得申請列表）、API 13（審核申請）
   ========================================================================== */

renderHeader("application");

/** 目前頁碼 */
let currentPage = 1;

/** 取得目前排序方向 */
let getOrder = () => "desc";

/** 審核狀態對應的顯示文字 */
const STATUS_TEXT = {
	APPROVE: "已同意",
	REJECT: "已拒絕",
};

/** 初始化頁面事件 */
function init() {
	getOrder = bindOrderToggle(() => {
		currentPage = 1;
		loadApplications();
	});

	bindSearchEvents(() => {
		currentPage = 1;
		loadApplications();
	});

	loadApplications();
}

/** 載入申請列表 */
async function loadApplications() {
	const listElement = document.getElementById("house-list");
	const messageElement = document.getElementById("message");

	hideMessage(messageElement);
	listElement.innerHTML = '<div class="empty">載入中…</div>';

	const query = readSearchConditions({ sort: false, status: true });
	query.order = getOrder();
	query.page = currentPage;

	try {
		const data = await api("GET", "/application", { query });

		document.getElementById("result-title").textContent = `Result（共 ${data.total_count} 筆）`;

		if (data.applications.length === 0) {
			listElement.innerHTML = '<div class="empty">目前沒有符合條件的申請</div>';
		} else {
			listElement.innerHTML = data.applications.map((application) => {
				const statusText = application.status === null ? "審核中" : (STATUS_TEXT[application.status] || application.status);
				const extra = `<div><span class="badge-status">${escapeHtml(statusText)}</span> <span class="meta">申請時間：${escapeHtml(application.applied_at)}</span></div>`;

				// 只有審核中的申請可以進行審核
				const actions = application.status === null
					? `<button type="button" class="button ghost small" data-action="decline" data-id="${escapeHtml(application.id)}">Decline</button>
					   <button type="button" class="button small" data-action="approve" data-id="${escapeHtml(application.id)}">Approve</button>`
					: "";

				return houseCardHtml(application.house, { extra: extra, actions: actions });
			}).join("");
		}

		bindReviewActions();

		renderPagination(document.getElementById("pagination"), currentPage, data.total_count, (page) => {
			currentPage = page;
			loadApplications();
			window.scrollTo({ top: 0, behavior: "smooth" });
		});
	} catch (error) {
		listElement.innerHTML = "";
		showMessage(messageElement, error.message);
	}
}

/** 綁定審核按鈕 */
function bindReviewActions() {
	const messageElement = document.getElementById("message");

	document.querySelectorAll("[data-action]").forEach((button) => {
		button.addEventListener("click", async () => {
			const approve = button.dataset.action === "approve";
			hideMessage(messageElement);

			try {
				await api("PUT", "/application/" + encodeURIComponent(button.dataset.id), {
					json: { approve: approve },
				});
				showMessage(messageElement, approve ? "已同意申請，房屋將展示為精選房屋 7 天" : "已拒絕申請", "success");
				loadApplications();
			} catch (error) {
				showMessage(messageElement, error.message);
			}
		});
	});
}

// 只有管理員可以瀏覽申請列表
if (requireAdmin()) {
	init();
}

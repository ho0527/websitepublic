/**
 * RankingService
 * 與伺服器端（PHP + MySQL）溝通，負責寫入成績與取得排行榜。
 */
class RankingService {

	/**
	 * @param {string} [endpoint] API 位址
	 */
	constructor(endpoint = 'api/ranking.php') {
		this.endpoint = endpoint;
	}

	/**
	 * 儲存一筆成績並取回該難度的排行資料。
	 * @param {{name:string, difficultId:number, seconds:number}} result
	 * @returns {Promise<Object>}
	 */
	async save(result) {
		const response = await fetch(this.endpoint, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({
				name: result.name,
				difficult_id: result.difficultId,
				seconds: result.seconds
			})
		});
		const data = await response.json();
		if (!response.ok || !data.ok) {
			throw new Error(data.error || 'Could not save the result.');
		}
		return data;
	}

	/**
	 * 只查詢排行資料。
	 * @param {number} difficultId
	 * @returns {Promise<Object>}
	 */
	async fetchRanking(difficultId) {
		const response = await fetch(this.endpoint + '?difficult_id=' + encodeURIComponent(difficultId));
		const data = await response.json();
		if (!response.ok || !data.ok) {
			throw new Error(data.error || 'Could not load the ranking.');
		}
		return data;
	}

	/**
	 * 把排行資料渲染成表格列。
	 * @param {HTMLElement} tbody
	 * @param {Object} data API 回傳的資料
	 */
	static render(tbody, data) {
		tbody.innerHTML = '';
		(data.rows || []).forEach((row) => {
			const tr = document.createElement('tr');
			if (row.me) {
				tr.className = 'you';
			}
			[row.position, row.level, row.name, row.time].forEach((value) => {
				const td = document.createElement('td');
				td.textContent = String(value);
				tr.appendChild(td);
			});
			tbody.appendChild(tr);
		});
	}
}

window.RankingService = RankingService;

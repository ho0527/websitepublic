/* ==========================================================================
   Module G - 汽車經銷商展場模擬器
   使用題目提供的 jQuery 1.8.3 + jQuery UI 1.9.2（本機檔案，不連外）
   規則摘要：
     1. 客戶依序在佇列排隊，最多同時 10 位，只有第一位可被拖曳
     2. 客戶只能被拖到「他指定品牌」且「未售出、未被佔用」的車位
     3. 指定品牌全部售出時，該客戶可改看任何仍有空車的品牌
     4. 在車位上的客戶可移到收銀台（詢問 YES / NO）或出口
     5. 放到錯誤位置一律回到原處
   ========================================================================== */

/* ---------- 基本設定：品牌價格與展場車輛圖片 ---------- */
var IMG = "material/picture/images/";

var BRANDS = {
	"Porsche":    { price: 72500, zone: "#porsche",    cars: ["Porsche_1.jpg", "Porsche_3.jpg", "Porsche_4.jpg", "Porsche_5.jpg"] },
	"Volkswagen": { price: 23930, zone: "#volkswagen", cars: ["Volkswagen_1.jpg", "volkswagen_2.png", "volkswagen_3.jpg", "volkswagen_4.jpg", "volkswagen_5.jpg", "volkswagen_6.jpg"] },
	"Audi":       { price: 31260, zone: "#audi",       cars: ["Audi_1.jpg", "Audi_2.jpg", "Audi_3.jpg", "Audi_4.jpg", "Audi_5.jpg"] },
	"BMW":        { price: 43990, zone: "#bmw",        cars: ["BMW_1.jpg", "bmw_2.jpg", "bmw_3.jpg"] }
};

var BRAND_LIST = ["Porsche", "Volkswagen", "Audi", "BMW"];

var QUEUE_MAX = 10;      /* 佇列同時最多容納的客戶數 */
var clientSeq = 0;       /* 客戶流水號 */

/* 統計數值（Statistic Display） */
var stats = { served: 0, sold: 0, amount: 0 };

/* ==========================================================================
   工具函式
   ========================================================================== */

/* 以歐洲格式輸出金額，例如 120000 -> € 120.000,00 */
function money(value) {
	var fixed = value.toFixed(2);            // 先取到小數兩位
	var parts = fixed.split(".");
	var int = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
	return "€ " + int + "," + parts[1];
}

/* 右下角操作回饋訊息，讓使用者知道剛剛發生什麼事 */
function toast(text, type) {
	var $t = $('<div class="toast"></div>').addClass(type || "").text(text);
	$("#toaster").append($t);
	setTimeout(function () {
		$t.fadeOut(300, function () { $(this).remove(); });
	}, 2600);
}

/* 更新統計面板，並讓變動的數字閃一下 */
function updateStats(changedIds) {
	$("#clients_served b").text(stats.served);
	$("#cars_sold b").text(stats.sold);
	$("#amount b").text(money(stats.amount));
	$.each(changedIds || [], function (i, id) {
		var $li = $(id).removeClass("bump");
		setTimeout(function () { $li.addClass("bump"); }, 10);
	});
}

/* 更新各品牌剩餘可看車輛數（未售出者） */
function updateStock() {
	$.each(BRANDS, function (brand, cfg) {
		var $zone = $(cfg.zone);
		var left = $zone.find(".car").not(".sold").length;
		$zone.find(".place_stock b").text(left);
	});
}

/* 判斷某品牌是否已全部售完（售完時客戶可改看其他品牌） */
function brandSoldOut(brand) {
	var $zone = $(BRANDS[brand].zone);
	return $zone.find(".car").not(".sold").length === 0;
}

/* ==========================================================================
   展場建置：依設定產生 4 台 Porsche / 6 台 Volkswagen / 5 台 Audi / 3 台 BMW
   ========================================================================== */
function buildShowroom() {
	$.each(BRANDS, function (brand, cfg) {
		var $slots = $(cfg.zone).find(".slots");
		$.each(cfg.cars, function (i, file) {
			var $car = $('<div class="car"></div>')
				.attr("data-brand", brand)
				.attr("id", brand.toLowerCase() + "_car_" + (i + 1))
				.css("background-image", "url(" + IMG + file + ")")
				.append('<span class="car_label">' + brand + " #" + (i + 1) + "</span>");
			$slots.append($car);
		});
	});
	updateStock();
}

/* ==========================================================================
   客戶佇列
   ========================================================================== */

/* 產生一位新客戶並排入佇列，佇列已滿（10 人）時不再放人 */
function spawnClient() {
	if ($("#clients_queue .client").length >= QUEUE_MAX) { return; }
	var preference = BRAND_LIST[Math.floor(Math.random() * 4)];
	var face = Math.floor(Math.random() * 10) + 1;
	clientSeq++;
	var $client = $('<div class="client enter"></div>')
		.attr("data-preference", preference)
		.attr("id", "client_" + clientSeq)
		.append('<span class="face" style="background-image:url(' + IMG + "client_" + face + '.jpg)"></span>')
		.append('<span class="preference">Wants a<b>' + preference + "</b></span>");
	$("#clients_queue").append($client);
	refreshQueue();
}

/* 沿用題目提供的隨機抵達邏輯：每 1 ~ 4 秒嘗試放入一位新客戶 */
function newClient() {
	spawnClient();
	setTimeout(newClient, Math.floor(Math.random() * 3000) + 1000);
}

/* 重新標記佇列：只有第一位可拖曳，其餘只能等待 */
function refreshQueue() {
	var $all = $("#clients_queue .client");
	$all.removeClass("first").each(function () {
		if ($(this).data("uiDraggable")) { $(this).draggable("destroy"); }
	});
	$all.first().addClass("first");
	makeDraggable($all.first());
	$("#queue_count").text($all.length + "/" + QUEUE_MAX);
}

/* ==========================================================================
   拖曳與放置
   ========================================================================== */

/* 讓指定客戶可以被拖曳；使用分身（clone）避免被車位的 overflow 裁切 */
function makeDraggable($client) {
	if (!$client.length) { return; }
	$client.draggable({
		helper: "clone",
		appendTo: "body",
		revert: "invalid",          /* 放到不合法的位置時自動飛回原處 */
		revertDuration: 300,
		zIndex: 9999,
		cursorAt: { left: 40, top: 20 },
		start: function () {
			$(this).css("visibility", "hidden");
			highlightTargets($(this), true);
		},
		stop: function () {
			$(this).css("visibility", "");
			highlightTargets($(this), false);
		}
	});
}

/* 拖曳開始時，把目前可合法放置的區域標亮，提升可用性 */
function highlightTargets($client, on) {
	if (!on) {
		$(".car, .dropzone").removeClass("ready hover");
		return;
	}
	$(".car").each(function () {
		if (carAccepts($(this), $client)) { $(this).addClass("ready"); }
	});
	$("#exit").addClass("ready");
	if ($client.closest(".car").length) { $("#cashier").addClass("ready"); }
}

/* 車位是否接受這位客戶：未售出、未被佔用、且品牌相符（或指定品牌已售完） */
function carAccepts($car, $client) {
	if (!$client || !$client.hasClass("client")) { return false; }
	if ($car.hasClass("sold")) { return false; }
	if ($car.find(".client").not($client).length) { return false; }
	if ($car.find(".client")[0] === $client[0]) { return false; }   /* 原地放回不算移動 */
	var want = $client.attr("data-preference");
	return $car.attr("data-brand") === want || brandSoldOut(want);
}

/* 把客戶移入車位（清掉 jQuery UI 於拖曳時留下的定位樣式） */
function placeInCar($client, $car) {
	$client.detach()
		.removeClass("first enter")
		.css({ position: "", top: "", left: "", width: "", height: "", visibility: "" })
		.appendTo($car);
	if ($client.data("uiDraggable")) { $client.draggable("destroy"); }
	makeDraggable($client);       /* 車上的客戶仍可再被移動到收銀台 / 出口 / 同品牌其他空車 */
	refreshQueue();
}

/* 客戶離場動畫，結束後從畫面移除 */
function removeClient($client, done) {
	$client.css("visibility", "").addClass("leaving");
	setTimeout(function () {
		$client.remove();
		refreshQueue();
		updateStock();
		if (done) { done(); }
	}, 600);
}

/* 建立所有放置區（車位、收銀台、出口） */
function initDroppables() {

	/* --- 車位：只接受品牌相符或指定品牌已售完的客戶 --- */
	$(".car").droppable({
		tolerance: "pointer",
		accept: function (draggable) { return carAccepts($(this), $(draggable)); },
		over: function () { $(this).addClass("hover"); },
		out: function () { $(this).removeClass("hover"); },
		drop: function (event, ui) {
			var $car = $(this).removeClass("hover");
			var $client = ui.draggable;
			/* 延後一個事件迴圈再搬動 DOM，避免在 draggable 尚未結束時銷毀元件 */
			setTimeout(function () {
				placeInCar($client, $car);
				toast("Customer is now visiting " + $car.attr("data-brand") + ".", "ok");
			}, 0);
		}
	});

	/* --- 收銀台：只接受已經在車上的客戶 --- */
	$("#cashier").droppable({
		tolerance: "pointer",
		accept: function (draggable) {
			var $c = $(draggable);
			return $c.hasClass("client") && $c.closest(".car").length > 0;
		},
		over: function () { $(this).addClass("hover"); },
		out: function () { $(this).removeClass("hover"); },
		drop: function (event, ui) {
			$(this).removeClass("hover");
			var $client = ui.draggable;
			setTimeout(function () { askPurchase($client); }, 0);
		}
	});

	/* --- 出口：佇列第一位或車上的客戶都可以直接離開，且不計入統計 --- */
	$("#exit").droppable({
		tolerance: "pointer",
		accept: function (draggable) {
			var $c = $(draggable);
			return $c.hasClass("client") && ($c.hasClass("first") || $c.closest(".car").length > 0);
		},
		over: function () { $(this).addClass("hover"); },
		out: function () { $(this).removeClass("hover"); },
		drop: function (event, ui) {
			$(this).removeClass("hover");
			var $client = ui.draggable;
			setTimeout(function () {
				removeClient($client);
				toast("Customer left the store. Statistics unchanged.", "warn");
			}, 0);
		}
	});
}

/* ==========================================================================
   收銀台流程：詢問是否購買，YES 才更新售出台數與金額
   ========================================================================== */
var pendingClient = null;

function askPurchase($client) {
	pendingClient = $client;
	var $car = $client.closest(".car");
	var brand = $car.attr("data-brand");
	$("#modal_car").text(brand + " " + $car.find(".car_label").text().replace(brand, "").trim() + "  —  " + money(BRANDS[brand].price));
	$("#modal_mask").addClass("show");
	$("#btn_yes").focus();
}

/* 使用者回答之後的處理：兩種情形都會讓服務人數 +1 */
function answerPurchase(bought) {
	$("#modal_mask").removeClass("show");
	var $client = pendingClient;
	pendingClient = null;
	if (!$client) { return; }

	var $car = $client.closest(".car");
	var brand = $car.attr("data-brand");

	if (bought) {
		/* 買了：車輛標記 SOLD 之後不可再被拜訪，統計三個數字全部更新 */
		$car.addClass("sold");
		stats.served++;
		stats.sold++;
		stats.amount += BRANDS[brand].price;
		removeClient($client, function () {
			updateStats(["#clients_served", "#cars_sold", "#amount"]);
			toast("Deal closed! " + brand + " sold for " + money(BRANDS[brand].price) + ".", "ok");
		});
	} else {
		/* 沒買：客戶離開，只增加服務人數 */
		stats.served++;
		removeClient($client, function () {
			updateStats(["#clients_served"]);
			toast("Customer did not buy the " + brand + ". Served count updated.", "warn");
		});
	}
}

/* ==========================================================================
   啟動
   ========================================================================== */
$(document).ready(function () {
	buildShowroom();
	initDroppables();
	updateStats([]);

	$("#btn_yes").on("click", function () { answerPurchase(true); });
	$("#btn_no").on("click", function () { answerPurchase(false); });

	/* 先放 3 位客戶讓畫面不空，之後由 newClient() 依隨機時間持續補人 */
	for (var i = 0; i < 3; i++) { spawnClient(); }
	newClient();

	toast("Drag the first customer in the queue to a car of the brand they want.", "");
});

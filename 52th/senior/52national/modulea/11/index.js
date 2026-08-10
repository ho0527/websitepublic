/**
 * Task A 05: 資料視覺化
 * 讀取 CSV（縣市、鄉鎮市區、男生人數、女生人數），以長條圖呈現人口數。
 * 預設依縣市呈現（排序依出現順序），點選長條後切換為該縣市各鄉鎮市區的人口數。
 */

const fileInput = document.getElementById("inputfile")
const table = document.getElementById("table")
const submitButton = document.getElementById("submit")
const reloadButton = document.getElementById("reflashbutton")

let countyList = []       // [{ name, population, districts: [{ name, population }] }]
let currentCounty = null  // 目前展開的縣市，null 代表顯示全部縣市

/**
 * 解析 CSV 文字，彙整成縣市 / 鄉鎮市區兩層資料
 * 縣市與鄉鎮市區皆維持在檔案中出現的順序
 * @param {string} text CSV 內容
 * @returns {object[]} 縣市資料陣列
 */
function parseCsv(text) {
    const lines = text.split(/\r?\n/).filter(function (line) {
        return line.trim() !== ""
    })
    const counties = []
    const countyIndex = {}

    // 第一行為欄位名稱，從第二行開始讀資料
    for (let i = 1; i < lines.length; i = i + 1) {
        const columns = lines[i].split(",")
        if (columns.length < 4) {
            continue
        }
        const countyName = columns[0].trim()
        const districtName = columns[1].trim()
        const male = parseInt(columns[2], 10) || 0
        const female = parseInt(columns[3], 10) || 0
        const population = male + female
        if (countyName === "") {
            continue
        }

        if (countyIndex[countyName] === undefined) {
            countyIndex[countyName] = counties.length
            counties.push({ name: countyName, population: 0, districts: [] })
        }
        const county = counties[countyIndex[countyName]]
        county.population = county.population + population
        county.districts.push({ name: districtName, population: population })
    }
    return counties
}

/**
 * 繪製長條圖
 * @param {{name:string,population:number}[]} rows 要呈現的資料
 * @param {boolean} clickable 長條是否可點選（僅縣市層可點）
 */
function renderChart(rows, clickable) {
    // 以最大值進位到千位作為長條的比例基準
    const maxPopulation = rows.reduce(function (max, row) {
        return row.population > max ? row.population : max
    }, 0)
    const scale = Math.max(Math.ceil(maxPopulation / 1000) * 1000, 1)

    table.innerHTML = ""
    rows.forEach(function (row, index) {
        const tr = document.createElement("tr")
        tr.className = "tr"

        const nameCell = document.createElement("td")
        nameCell.className = "td tdtitle"
        nameCell.textContent = row.name

        const barCell = document.createElement("td")
        barCell.className = "td tdshow"
        const bar = document.createElement("div")
        bar.className = "line"
        barCell.appendChild(bar)

        const numberCell = document.createElement("td")
        numberCell.className = "td tdnumber"
        numberCell.textContent = row.population.toLocaleString()

        tr.appendChild(nameCell)
        tr.appendChild(barCell)
        tr.appendChild(numberCell)
        table.appendChild(tr)

        // 先掛上元素再設定寬度，讓長條有展開的動畫
        setTimeout(function () {
            bar.style.width = (row.population / scale) * 100 + "%"
        }, 50)

        if (clickable) {
            tr.classList.add("clickable")
            tr.onclick = function () {
                showDistricts(index)
            }
        }
    })
}

/** 顯示所有縣市的人口數 */
function showCounties() {
    currentCounty = null
    document.getElementById("chartTitle").textContent = "各縣市人口數"
    document.getElementById("backButton").style.display = "none"
    renderChart(countyList, true)
}

/**
 * 顯示指定縣市底下各鄉鎮市區的人口數
 * @param {number} index 縣市在 countyList 中的索引
 */
function showDistricts(index) {
    currentCounty = countyList[index]
    document.getElementById("chartTitle").textContent = currentCounty.name + " 各鄉鎮市區人口數"
    document.getElementById("backButton").style.display = "inline-block"
    renderChart(currentCounty.districts, false)
}

/** 讀取選取的 CSV 並產生圖表 */
function loadCsvFile() {
    const file = fileInput.files[0]
    if (file === undefined) {
        alert("請先選擇 CSV 檔案")
        return
    }
    const reader = new FileReader()
    reader.onload = function (event) {
        countyList = parseCsv(event.target.result)
        if (countyList.length === 0) {
            alert("這個檔案沒有可用的資料")
            return
        }
        showCounties()
    }
    reader.readAsText(file, "UTF-8")
}

submitButton.onclick = loadCsvFile
reloadButton.onclick = function () {
    location.reload()
}
document.getElementById("backButton").onclick = showCounties

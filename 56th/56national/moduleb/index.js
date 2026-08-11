const samplePhotos = [
  ["material/Sample Photos/beautiful-view-in-lyon.jpg", "Beautiful View In Lyon"],
  ["material/Sample Photos/basilique-notre-dame-de-fourviere-lyon.jpg", "Basilique Notre Dame De Fourviere Lyon"],
  ["material/Sample Photos/place-bellecour-lyon.jpg", "Place Bellecour Lyon"],
  ["material/Sample Photos/tour-metalique-lyon.jpg", "Tour Metalique Lyon"]
];

const defaultSettings = {
  mode: "manual",
  theme: "a",
  interval: 4,
  speed: 1,
  dark: false,
  playing: true
};

let photos = [];
let currentIndex = 0;
let settings = loadSettings();
let timer = null;
let commandIndex = 0;
// 等待播放離場動畫的相片（切換相片時由 goToIndex 設定，renderSlide 取用）
let pendingExitPhoto = null;
let exitTimer = null;

const $ = (selector) => document.querySelector(selector);
const slideshow = $("#slideshow");
const slide = $("#slide");
const slideImage = $("#slideImage");
const slideCaption = $("#slideCaption");
const slideOutgoing = $("#slideOutgoing");
const slideOutgoingImage = $("#slideOutgoingImage");
const slideOutgoingCaption = $("#slideOutgoingCaption");
const emptyState = $("#emptyState");
const counter = $("#counter");
const photoList = $("#photoList");
const fileInput = $("#fileInput");
const settingsPanel = $("#settingsPanel");
const commandPalette = $("#commandPalette");
const commandBackdrop = $("#paletteBackdrop");
const commandInput = $("#commandInput");
const commandList = $("#commandList");
const loadStatus = $("#loadStatus");
const playIcon = $("#playIcon");
const panelBackdrop = $("#panelBackdrop");

/**
 * 開啟或關閉設定視窗（含背景遮罩）。
 */
function toggleSettings(open) {
  settingsPanel.classList.toggle("open", open);
  panelBackdrop.hidden = !open;
}

const commands = [
  ["Switch to manual mode", () => setMode("manual")],
  ["Switch to autoplay mode", () => setMode("auto")],
  ["Switch to random mode", () => setMode("random")],
  ["Switch to theme A", () => setTheme("a")],
  ["Switch to theme B", () => setTheme("b")],
  ["Switch to theme C", () => setTheme("c")],
  ["Switch to theme D", () => setTheme("d")],
  ["Toggle dark and light mode", toggleDarkMode],
  ["Shuffle photos", shufflePhotos],
  ["Pause playback", () => setPlaying(false)],
  ["Start playback", () => setPlaying(true)]
];

function loadSettings() {
  try {
    return { ...defaultSettings, ...JSON.parse(localStorage.getItem("moduleBSettings") || "{}") };
  } catch {
    return { ...defaultSettings };
  }
}

function saveSettings() {
  localStorage.setItem("moduleBSettings", JSON.stringify(settings));
}

function captionFromFilename(name) {
  return name
    .replace(/\.[^.]+$/, "")
    .replace(/[-_]+/g, " ")
    .trim()
    .split(/\s+/)
    .filter(Boolean)
    .map((word) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
    .join(" ");
}

function afterPaint() {
  return new Promise((resolve) => requestAnimationFrame(() => requestAnimationFrame(resolve)));
}

function setProcessing(text = "") {
  loadStatus.textContent = text;
  document.body.classList.toggle("processing", Boolean(text));
}

function canvasUrlFromBitmap(bitmap, file, maxEdge, quality) {
  const scale = Math.min(1, maxEdge / Math.max(bitmap.width, bitmap.height));
  const width = Math.max(1, Math.round(bitmap.width * scale));
  const height = Math.max(1, Math.round(bitmap.height * scale));
  const canvas = document.createElement("canvas");
  canvas.width = width;
  canvas.height = height;
  const context = canvas.getContext("2d", { alpha: false });
  context.imageSmoothingEnabled = true;
  context.imageSmoothingQuality = "high";
  context.drawImage(bitmap, 0, 0, width, height);
  return new Promise((resolve) => {
    canvas.toBlob((blob) => resolve(URL.createObjectURL(blob || file)), "image/jpeg", quality);
  });
}

async function optimizedImageUrls(file) {
  if (!("createImageBitmap" in window)) {
    const src = URL.createObjectURL(file);
    return { displaySrc: src, thumbSrc: src };
  }
  try {
    const bitmap = await createImageBitmap(file);
    const displaySrc = await canvasUrlFromBitmap(bitmap, file, 2200, 0.9);
    const thumbSrc = await canvasUrlFromBitmap(bitmap, file, 360, 0.76);
    bitmap.close();
    return { displaySrc, thumbSrc };
  } catch {
    const src = URL.createObjectURL(file);
    return { displaySrc: src, thumbSrc: src };
  }
}

async function addFiles(files) {
  const fileList = [...files].filter((file) => file.type.startsWith("image/"));
  if (fileList.length === 0) return;
  setProcessing(`0 / ${fileList.length}`);
  const existing = new Set(photos.flatMap((photo) => [photo.key, photo.name.toLowerCase()]));
  let loaded = 0;
  for (const file of fileList) {
    const nameKey = file.name.toLowerCase();
    const fileKey = `${nameKey}:${file.size}`;
    if (existing.has(fileKey) || existing.has(nameKey)) continue;
    existing.add(fileKey);
    existing.add(nameKey);
    await afterPaint();
    const { displaySrc, thumbSrc } = await optimizedImageUrls(file);
    photos.push({
      id: crypto.randomUUID(),
      src: displaySrc,
      thumbSrc,
      caption: captionFromFilename(file.name),
      name: file.name,
      key: fileKey,
      local: true
    });
    loaded++;
    setProcessing(`${loaded} / ${fileList.length}`);
    renderAll();
  }
  if (photos.length > 0 && currentIndex >= photos.length) currentIndex = photos.length - 1;
  setProcessing("");
  renderAll();
}

function loadSamples() {
  const existing = new Set(photos.map((photo) => photo.key));
  samplePhotos.forEach(([src, caption]) => {
    if (existing.has(src)) return;
    photos.push({
      id: crypto.randomUUID(),
      src,
      thumbSrc: src,
      caption,
      name: src.split("/").pop(),
      key: src,
      local: false
    });
  });
  renderAll();
}

function setMode(mode) {
  settings.mode = mode;
  settings.playing = mode !== "manual";
  saveSettings();
  renderControls();
  schedule();
}

function setTheme(theme) {
  settings.theme = theme;
  saveSettings();
  renderSlide();
  renderControls();
}

function setPlaying(playing) {
  settings.playing = playing;
  saveSettings();
  renderControls();
  schedule();
}

function toggleDarkMode() {
  settings.dark = !settings.dark;
  saveSettings();
  renderControls();
}

function clampNumbers() {
  settings.interval = Math.min(30, Math.max(1, Number(settings.interval) || defaultSettings.interval));
  settings.speed = Math.min(5, Math.max(0.2, Number(settings.speed) || defaultSettings.speed));
}

/**
 * 切換到指定索引，並記錄上一張相片以便播放離場動畫。
 */
function goToIndex(nextIndex) {
  if (photos.length === 0 || nextIndex === currentIndex) return;
  pendingExitPhoto = photos[currentIndex] || null;
  currentIndex = nextIndex;
  renderAll(false);
  schedule();
}

function nextPhoto() {
  if (photos.length === 0) return;
  if (settings.mode === "random" && photos.length > 1) {
    let next = currentIndex;
    while (next === currentIndex) next = Math.floor(Math.random() * photos.length);
    goToIndex(next);
    return;
  }
  goToIndex((currentIndex + 1) % photos.length);
}

function previousPhoto() {
  if (photos.length === 0) return;
  goToIndex((currentIndex - 1 + photos.length) % photos.length);
}

/**
 * 將上一張相片放到離場層並播放離場動畫，動畫結束後隱藏該層。
 */
function playExitAnimation(photo) {
  if (!photo) return;
  slideOutgoingImage.src = photo.src;
  slideOutgoingImage.alt = "";
  slideOutgoingCaption.textContent = photo.caption;
  slideOutgoing.classList.remove("leaving");
  void slideOutgoing.offsetWidth;
  slideOutgoing.classList.add("leaving");

  clearTimeout(exitTimer);
  exitTimer = setTimeout(() => {
    slideOutgoing.classList.remove("leaving");
    slideOutgoingImage.removeAttribute("src");
  }, settings.speed * 1000 + 400);
}

function schedule() {
  clearTimeout(timer);
  if (photos.length === 0 || settings.mode === "manual" || !settings.playing) return;
  timer = setTimeout(nextPhoto, settings.interval * 1000);
}

function renderSlide() {
  clampNumbers();
  slideshow.style.setProperty("--speed", `${settings.speed}s`);
  slideshow.className = `slideshow theme-${settings.theme}`;
  slide.classList.remove("active");
  void slide.offsetWidth;

  if (photos.length === 0) {
    emptyState.hidden = false;
    slide.classList.remove("active");
    slideImage.removeAttribute("src");
    slideImage.alt = "";
    slideCaption.textContent = "";
    counter.textContent = "0 / 0";
    return;
  }

  // 先播放上一張相片的離場動畫，再顯示新的相片
  if (pendingExitPhoto) {
    playExitAnimation(pendingExitPhoto);
    pendingExitPhoto = null;
  }

  const photo = photos[currentIndex];
  emptyState.hidden = true;
  slideImage.src = photo.src;
  slideImage.alt = photo.caption;
  renderCaption(photo.caption);
  slide.classList.add("active");
  counter.textContent = `${currentIndex + 1} / ${photos.length}`;
  preloadNearby();
}

function preloadNearby() {
  if (photos.length < 2) return;
  [1, -1].forEach((offset) => {
    const photo = photos[(currentIndex + offset + photos.length) % photos.length];
    const image = new Image();
    image.decoding = "async";
    image.src = photo.src;
  });
}

function renderCaption(text) {
  if (settings.theme !== "c") {
    slideCaption.textContent = text;
    return;
  }
  slideCaption.innerHTML = "";
  text.split(/\s+/).forEach((word, index) => {
    const span = document.createElement("span");
    span.className = "caption-word";
    span.textContent = word;
    span.style.animationDelay = `${index * 0.3}s`;
    slideCaption.append(span, " ");
  });
}

function renderControls() {
  document.body.classList.toggle("dark", settings.dark);
  $("#darkModeToggle").checked = settings.dark;
  $("#intervalInput").value = settings.interval;
  $("#intervalRange").value = settings.interval;
  $("#speedInput").value = settings.speed;
  $("#speedRange").value = settings.speed;
  playIcon.src = settings.playing ? "./material/icons/pause.svg" : "./material/icons/play.svg";
  document.querySelectorAll("[data-mode]").forEach((button) => {
    button.classList.toggle("active", button.dataset.mode === settings.mode);
  });
  document.querySelectorAll("[data-theme]").forEach((button) => {
    button.classList.toggle("active", button.dataset.theme === settings.theme);
  });
}

function renderPhotoList() {
  photoList.innerHTML = "";
  photos.forEach((photo, index) => {
    const item = document.createElement("div");
    item.className = `photo-item${index === currentIndex ? " active" : ""}`;
    item.draggable = true;
    item.dataset.index = index;

    const thumb = document.createElement("img");
    thumb.src = photo.thumbSrc || photo.src;
    thumb.alt = "";

    const input = document.createElement("input");
    input.type = "text";
    input.value = photo.caption;
    input.ariaLabel = "Photo caption";
    input.addEventListener("input", () => {
      photo.caption = input.value;
      if (index === currentIndex) renderSlide();
    });

    const remove = document.createElement("button");
    remove.className = "remove-button";
    remove.type = "button";
    remove.textContent = "x";
    remove.ariaLabel = "Remove photo";
    remove.addEventListener("click", () => {
      if (photo.local) {
        URL.revokeObjectURL(photo.src);
        if (photo.thumbSrc && photo.thumbSrc !== photo.src) URL.revokeObjectURL(photo.thumbSrc);
      }
      photos.splice(index, 1);
      currentIndex = Math.min(currentIndex, Math.max(photos.length - 1, 0));
      renderAll();
    });

    item.addEventListener("click", (event) => {
      if (event.target === input || event.target === remove) return;
      goToIndex(index);
    });
    item.addEventListener("dragstart", (event) => {
      item.classList.add("dragging");
      event.dataTransfer.setData("text/plain", String(index));
    });
    item.addEventListener("dragend", () => item.classList.remove("dragging"));
    item.addEventListener("dragover", (event) => event.preventDefault());
    item.addEventListener("drop", (event) => {
      event.preventDefault();
      const from = Number(event.dataTransfer.getData("text/plain"));
      movePhoto(from, index);
    });

    item.append(thumb, input, remove);
    photoList.append(item);
  });
}

function movePhoto(from, to) {
  if (Number.isNaN(from) || from === to) return;
  const [photo] = photos.splice(from, 1);
  photos.splice(to, 0, photo);
  if (currentIndex === from) currentIndex = to;
  else if (from < currentIndex && to >= currentIndex) currentIndex--;
  else if (from > currentIndex && to <= currentIndex) currentIndex++;
  renderAll();
}

function shufflePhotos() {
  for (let i = photos.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [photos[i], photos[j]] = [photos[j], photos[i]];
  }
  currentIndex = 0;
  renderAll();
}

function renderAll(shouldSchedule = true) {
  renderSlide();
  renderControls();
  renderPhotoList();
  if (shouldSchedule) schedule();
}

function openPalette() {
  commandPalette.hidden = false;
  commandBackdrop.hidden = false;
  commandInput.value = "";
  commandIndex = 0;
  renderCommands();
  commandInput.focus();
}

function closePalette() {
  commandPalette.hidden = true;
  commandBackdrop.hidden = true;
  slideshow.focus();
}

function visibleCommands() {
  const query = commandInput.value.trim().toLowerCase();
  return commands.filter(([label]) => label.toLowerCase().includes(query));
}

function renderCommands() {
  const list = visibleCommands();
  commandIndex = Math.min(commandIndex, Math.max(list.length - 1, 0));
  commandList.innerHTML = "";
  list.forEach(([label], index) => {
    const option = document.createElement("div");
    option.className = `command-option${index === commandIndex ? " selected" : ""}`;
    option.role = "option";
    option.dataset.index = index;
    option.textContent = label;
    option.addEventListener("mouseenter", () => {
      commandIndex = index;
      updateCommandSelection();
    });
    option.addEventListener("mousedown", (event) => {
      event.preventDefault();
      runCommand(index);
    });
    commandList.append(option);
  });
}

function updateCommandSelection() {
  commandList.querySelectorAll(".command-option").forEach((option) => {
    option.classList.toggle("selected", Number(option.dataset.index) === commandIndex);
  });
}

function runCommand(index = commandIndex) {
  const command = visibleCommands()[index];
  if (!command) return;
  command[1]();
  closePalette();
}

function bindEvents() {
  $("#uploadButton").addEventListener("click", () => fileInput.click());
  $("#sampleButton").addEventListener("click", loadSamples);
  $("#settingsButton").addEventListener("click", () => toggleSettings(!settingsPanel.classList.contains("open")));
  $("#closeSettingsButton").addEventListener("click", () => toggleSettings(false));
  panelBackdrop.addEventListener("click", () => toggleSettings(false));
  // 全螢幕瀏覽：對根元素要求全螢幕，瀏覽器工具列與系統工作列都會隱藏
  $("#fullscreenButton").addEventListener("click", () => {
    if (document.fullscreenElement) {
      document.exitFullscreen?.();
    } else {
      document.documentElement.requestFullscreen?.();
    }
  });
  $("#previousButton").addEventListener("click", previousPhoto);
  $("#nextButton").addEventListener("click", nextPhoto);
  $("#playButton").addEventListener("click", () => setPlaying(!settings.playing));
  $("#shuffleButton").addEventListener("click", shufflePhotos);
  $("#darkModeToggle").addEventListener("change", toggleDarkMode);
  fileInput.addEventListener("change", () => addFiles(fileInput.files));

  document.querySelectorAll("[data-mode]").forEach((button) => {
    button.addEventListener("click", () => setMode(button.dataset.mode));
  });
  document.querySelectorAll("[data-theme]").forEach((button) => {
    button.addEventListener("click", () => setTheme(button.dataset.theme));
  });

  ["intervalInput", "intervalRange"].forEach((id) => {
    $(`#${id}`).addEventListener("input", (event) => {
      settings.interval = Number(event.target.value);
      saveSettings();
      renderControls();
      schedule();
    });
  });
  ["speedInput", "speedRange"].forEach((id) => {
    $(`#${id}`).addEventListener("input", (event) => {
      settings.speed = Number(event.target.value);
      saveSettings();
      renderSlide();
      renderControls();
    });
  });

  ["dragenter", "dragover"].forEach((name) => {
    slideshow.addEventListener(name, (event) => {
      event.preventDefault();
      slideshow.classList.add("dragover");
    });
  });
  ["dragleave", "drop"].forEach((name) => {
    slideshow.addEventListener(name, () => slideshow.classList.remove("dragover"));
  });
  slideshow.addEventListener("drop", (event) => {
    event.preventDefault();
    addFiles(event.dataTransfer.files);
  });

  document.addEventListener("keydown", (event) => {
    if (!commandPalette.hidden) {
      if (event.key === "Escape") closePalette();
      if (event.key === "ArrowDown") {
        event.preventDefault();
        commandIndex = (commandIndex + 1) % Math.max(visibleCommands().length, 1);
        renderCommands();
      }
      if (event.key === "ArrowUp") {
        event.preventDefault();
        commandIndex = (commandIndex - 1 + Math.max(visibleCommands().length, 1)) % Math.max(visibleCommands().length, 1);
        renderCommands();
      }
      if (event.key === "Enter") runCommand();
      return;
    }

    const typing = ["INPUT", "TEXTAREA"].includes(document.activeElement.tagName);
    if ((event.ctrlKey && event.key.toLowerCase() === "k") || (!typing && event.key === "/")) {
      event.preventDefault();
      openPalette();
      return;
    }
    if (event.key === "Escape" && settingsPanel.classList.contains("open")) {
      toggleSettings(false);
      return;
    }
    if (typing) return;
    if (event.key === "ArrowRight") nextPhoto();
    if (event.key === "ArrowLeft") previousPhoto();
  });

  commandInput.addEventListener("input", () => {
    commandIndex = 0;
    renderCommands();
  });
  commandBackdrop.addEventListener("click", closePalette);
}

bindEvents();
renderAll();

if ("serviceWorker" in navigator) {
  navigator.serviceWorker.getRegistrations().then((items) => {
    items.forEach((item) => item.unregister());
  });
}

if ("caches" in window) {
  caches.keys().then((keys) => {
    keys.forEach((key) => caches.delete(key));
  });
}

const cards = document.querySelectorAll('.container .card')
cards.forEach((card, i) => {
  card.style.top = `${i * 0.1 + 4}rem`
  card.style.left = `${i * 2 + 4}rem`
})
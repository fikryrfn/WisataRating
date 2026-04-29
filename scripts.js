const destinations = [
  { id: 1, name: "Pantai Kuta", location: "Bali", category: "Pantai", rating: 4.5, reviews: 2847,
    image: "https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=600&q=80",
    description: "Pantai ikonik dengan ombak indah, sunset memukau, dan suasana yang ramai." },
  { id: 2, name: "Gunung Bromo", location: "Jawa Timur", category: "Gunung", rating: 4.8, reviews: 3120,
    image: "https://images.unsplash.com/photo-1589308078059-be1415eab4c3?w=600&q=80",
    description: "Kawah vulkanik aktif dengan lautan pasir yang menakjubkan dan pemandangan matahari terbit." },
  { id: 3, name: "Candi Borobudur", location: "Jawa Tengah", category: "Budaya", rating: 4.9, reviews: 4210,
    image: "https://images.unsplash.com/photo-1596402184320-417e7178b2cd?w=600&q=80",
    description: "Candi Buddha terbesar di dunia, warisan budaya UNESCO yang menakjubkan." },
  { id: 4, name: "Air Terjun Tumpak Sewu", location: "Jawa Timur", category: "Air Terjun", rating: 4.7, reviews: 1985,
    image: "https://images.unsplash.com/photo-1695323803671-b4ebee2c7484?w=600&q=80",
    description: "Air terjun spektakuler berbentuk tirai raksasa di lereng Gunung Semeru." },
  { id: 5, name: "Pantai Raja Ampat", location: "Papua Barat", category: "Pantai", rating: 4.9, reviews: 1560,
    image: "https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?w=600&q=80",
    description: "Surga bahari dengan keanekaragaman hayati laut terkaya di dunia." },
  { id: 6, name: "Gunung Rinjani", location: "Lombok, NTB", category: "Gunung", rating: 4.7, reviews: 2340,
    image: "https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=600&q=80",
    description: "Gunung berapi aktif kedua tertinggi di Indonesia dengan danau kawah Segara Anak." },
  { id: 7, name: "Candi Prambanan", location: "Yogyakarta", category: "Budaya", rating: 4.7, reviews: 3540,
    image: "https://images.unsplash.com/photo-1555400038-63f0ba517a47?w=600&q=80",
    description: "Kompleks candi Hindu terbesar di Indonesia, dibangun pada abad ke-9." },
  { id: 8, name: "Air Terjun Gitgit", location: "Bali", category: "Air Terjun", rating: 4.4, reviews: 1230,
    image: "https://images.unsplash.com/photo-1433086966358-54859d0ed716?w=600&q=80",
    description: "Air terjun setinggi 35 meter dikelilingi hutan tropis yang asri dan segar." },
  { id: 9, name: "Pantai Pink Lombok", location: "Lombok, NTB", category: "Pantai", rating: 4.6, reviews: 987,
    image: "https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=80",
    description: "Pantai unik dengan pasir berwarna merah muda yang langka, salah satu dari tujuh di dunia." }
];

let activeFilter = 'Semua';

function renderStars(rating) {
  const full = Math.floor(rating);
  const half = rating % 1 >= 0.5;
  let stars = '';
  for (let i = 0; i < full; i++) stars += '★';
  if (half) stars += '½';
  const empty = 5 - full - (half ? 1 : 0);
  for (let i = 0; i < empty; i++) stars += '☆';
  return stars;
}

function renderCards(data) {
  const grid = document.getElementById('cards-grid');
  const noResult = document.getElementById('no-result');
  if (data.length === 0) {
    grid.innerHTML = '';
    noResult.classList.remove('hidden');
    return;
  }
  noResult.classList.add('hidden');
  grid.innerHTML = data.map(dest => `
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200 cursor-pointer group"
         onclick="window.location.href='detail.php?id=${dest.id}'">
      <div class="relative overflow-hidden h-48">
        <img src="${dest.image}" alt="${dest.name}"
          class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
          onerror="this.src='https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=600&q=80'" />
        <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-green-700 text-xs font-semibold px-2.5 py-1 rounded-full">
          ${dest.category}
        </span>
      </div>
      <div class="p-4">
        <h3 class="font-bold text-gray-800 text-base mb-0.5">${dest.name}</h3>
        <p class="text-xs text-gray-500 mb-2">📍 ${dest.location}</p>
        <p class="text-xs text-gray-600 mb-3 line-clamp-2">${dest.description}</p>
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-1">
            <span class="text-yellow-400 text-sm">${renderStars(dest.rating)}</span>
            <span class="text-sm font-semibold text-gray-700">${dest.rating}</span>
          </div>
          <span class="text-xs text-gray-400">${dest.reviews.toLocaleString('id-ID')} ulasan</span>
        </div>
      </div>
    </div>
  `).join('');
}

function setFilter(btn, category) {
  activeFilter = category;
  document.querySelectorAll('.filter-btn').forEach(b => {
    b.classList.remove('bg-green-700', 'text-white', 'border-green-700');
    b.classList.add('border-gray-300', 'text-gray-600');
  });
  btn.classList.add('bg-green-700', 'text-white', 'border-green-700');
  btn.classList.remove('border-gray-300', 'text-gray-600');
  applyFilters();
}

function filterCards() { applyFilters(); }

function applyFilters() {
  const query = (document.getElementById('searchInput')?.value || '').toLowerCase();
  const filtered = destinations.filter(dest => {
    const matchCategory = activeFilter === 'Semua' || dest.category === activeFilter;
    const matchSearch = dest.name.toLowerCase().includes(query) ||
                        dest.location.toLowerCase().includes(query) ||
                        dest.description.toLowerCase().includes(query);
    return matchCategory && matchSearch;
  });
  renderCards(filtered);
}

document.addEventListener('DOMContentLoaded', () => { renderCards(destinations); });

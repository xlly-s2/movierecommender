document.addEventListener('DOMContentLoaded', async () => {
  // Load genres
  const genres = await fetchGenres();
  const genreSelect = document.getElementById('genre');
  genres.forEach(genre => {
    const option = document.createElement('option');
    option.value = genre.id;
    option.textContent = genre.name;
    genreSelect.appendChild(option);
  });

  // Filter buttons
  document.getElementById('apply-filters').addEventListener('click', loadMovies);
  document.getElementById('surprise-me').addEventListener('click', getRandomMovie);

  // Initial load
  loadMovies();
});

async function fetchGenres() {
  try {
    const response = await fetch(`https://api.themoviedb.org/3/genre/movie/list?api_key=${TMDB_API_KEY}`);
    const data = await response.json();
    return data.genres || [];
  } catch (error) {
    console.error('Error fetching genres:', error);
    return [];
  }
}

async function loadMovies() {
  const genre = document.getElementById('genre').value;
  const sort = document.getElementById('sort').value;

  try {
    const response = await fetch(`https://api.themoviedb.org/3/discover/movie?api_key=${TMDB_API_KEY}&sort_by=${sort}&with_genres=${genre}`);
    const data = await response.json();
    displayMovies(data.results);
  } catch (error) {
    console.error('Error fetching movies:', error);
  }
}

function displayMovies(movies) {
  const container = document.getElementById('movies-container');
  container.innerHTML = '';

  if (!movies || movies.length === 0) {
    container.innerHTML = `
      <div class="no-poster">
        <i class="fas fa-film"></i>
        <p>No movies found with these filters</p>
      </div>
    `;
    return;
  }

  movies.forEach(movie => {
    const posterPath = movie.poster_path 
      ? `https://image.tmdb.org/t/p/w500${movie.poster_path}`
      : null;

    const card = document.createElement('div');
    card.className = 'movie-card';
    card.innerHTML = `
      <div class="movie-poster">
        ${posterPath 
          ? `<img src="${posterPath}" alt="${movie.title}">`
          : `<i class="fas fa-film"></i><span>No poster</span>`}
      </div>
      <div class="movie-info">
        <h3>${movie.title}</h3>
        <p>${movie.overview || 'No description available'}</p>
      </div>
    `;
    container.appendChild(card);
  });
}

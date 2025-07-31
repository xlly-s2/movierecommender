<?php
require_once 'config.php';


function getRandomMovie() {
    // First try popular movies (higher chance of good results)
    $popular = fetchFromTMDB('movie/popular', ['page' => rand(1, 500)]);
    
    if (!empty($popular['results'])) {
        return $popular['results'][array_rand($popular['results'])];
    }
    
    // Fallback to discover if popular fails
    $discover = fetchFromTMDB('discover/movie', [
        'page' => rand(1, 500),
        'include_adult' => false
    ]);
    
    return $discover['results'][array_rand($discover['results'])] ?? null;
}

function getGenres() {
    $data = fetchFromTMDB('genre/movie/list');
    if (!$data || !isset($data['genres'])) {
        file_put_contents('debug.log', "Genre fetch failed!\n", FILE_APPEND);
        return [];
    }
    return $data['genres'];
}

// Function to fetch data from TMDB API
function fetchFromTMDB($endpoint, $params = []) {
    $params['api_key'] = TMDB_API_KEY;
    $url = TMDB_BASE_URL . $endpoint . '?' . http_build_query($params);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

// Get genres list
function getGenres() {
    $data = fetchFromTMDB('genre/movie/list');
    return $data['genres'] ?? [];
}

// Get movies by genre
function getMoviesByGenre($genreId, $page = 1, $sortBy = 'popularity.desc', $year = null, $rating = null) {
    $params = [
        'with_genres' => $genreId,
        'page' => $page,
        'sort_by' => $sortBy,
        'include_adult' => false
    ];
    
    if ($year) $params['primary_release_year'] = $year;
    if ($rating) $params['vote_average.gte'] = $rating;
    
    return fetchFromTMDB('discover/movie', $params);
}

// Get random movie
function getRandomMovie() {
    $totalPages = 500; // TMDB allows up to page 500
    $randomPage = rand(1, $totalPages);
    
    $data = fetchFromTMDB('discover/movie', [
        'page' => $randomPage,
        'include_adult' => false
    ]);
    
    if (!empty($data['results'])) {
        return $data['results'][array_rand($data['results'])];
    }
    
    return null;
}

// Get movie details
function getMovieDetails($movieId) {
    $movie = fetchFromTMDB("movie/$movieId", [
        'append_to_response' => 'videos,credits'
    ]);
    
    return $movie;
}

// Get trailer URL
function getTrailerUrl($movie) {
    if (!empty($movie['videos']['results'])) {
        foreach ($movie['videos']['results'] as $video) {
            if ($video['type'] == 'Trailer' && $video['site'] == 'YouTube') {
                return 'https://www.youtube.com/embed/' . $video['key'];
            }
        }
    }
    return null;
}
?>

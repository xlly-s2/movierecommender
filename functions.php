<?php
require_once 'config.php';

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

function getGenres() {
    $data = fetchFromTMDB('genre/movie/list');
    return $data['genres'] ?? [];
}

function getMovies($filters) {
    return fetchFromTMDB('discover/movie', [
        'with_genres' => $filters['genre'] ?? '',
        'sort_by' => $filters['sort'] ?? 'popularity.desc',
        'primary_release_year' => $filters['year'] ?? '',
        'vote_average.gte' => $filters['rating'] ?? '',
        'include_adult' => false
    ]);
}

function getRandomMovie() {
    $randomPage = rand(1, 500);
    $data = fetchFromTMDB('discover/movie', [
        'page' => $randomPage,
        'include_adult' => false
    ]);
    return $data['results'][array_rand($data['results'])] ?? null;
}
?>
